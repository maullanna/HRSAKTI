const mysql = require('mysql2/promise');
const cron = require('node-cron');
const moment = require('moment');
const winston = require('winston');
require('dotenv').config();

// Configure logger
const logger = winston.createLogger({
    level: process.env.LOG_LEVEL || 'info',
    format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.errors({ stack: true }),
        winston.format.json()
    ),
    transports: [
        new winston.transports.File({ filename: process.env.LOG_FILE || 'logs/fingerprint.log' }),
        new winston.transports.Console({
            format: winston.format.simple()
        })
    ]
});

// Database connection
let dbConnection;

const connectDatabase = async () => {
    try {
        dbConnection = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            port: process.env.DB_PORT || 3306,
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_NAME || 'attendance_management_system',
            charset: 'utf8mb4'
        });
        
        logger.info('✅ Database connected successfully');
        return true;
    } catch (error) {
        logger.error('❌ Database connection failed:', error.message);
        return false;
    }
};

// Simulate fingerprint device data collection
const collectFingerprintData = async () => {
    try {
        logger.info('🔍 Collecting fingerprint data...');
        
        // Simulate data from fingerprint device
        const mockFingerprintData = [
            {
                employee_id: 'EMP001',
                fingerprint_id: 1,
                timestamp: moment().format('YYYY-MM-DD HH:mm:ss'),
                device_id: 1,
                action: 'check_in'
            },
            {
                employee_id: 'EMP002',
                fingerprint_id: 2,
                timestamp: moment().subtract(30, 'minutes').format('YYYY-MM-DD HH:mm:ss'),
                device_id: 1,
                action: 'check_in'
            },
            {
                employee_id: 'EMP001',
                fingerprint_id: 1,
                timestamp: moment().subtract(15, 'minutes').format('YYYY-MM-DD HH:mm:ss'),
                device_id: 1,
                action: 'check_out'
            }
        ];

        // Process each fingerprint log
        for (const logData of mockFingerprintData) {
            await processFingerprintLog(logData);
        }

        logger.info(`✅ Processed ${mockFingerprintData.length} fingerprint logs`);
        
    } catch (error) {
        logger.error('❌ Error collecting fingerprint data:', error.message);
    }
};

// Process individual fingerprint log
const processFingerprintLog = async (logData) => {
    try {
        // Check if employee exists
        const [employees] = await dbConnection.execute(
            'SELECT id, name FROM employees WHERE employee_id = ?',
            [logData.employee_id]
        );

        if (employees.length === 0) {
            logger.warn(`⚠️ Employee not found: ${logData.employee_id}`);
            return;
        }

        const employee = employees[0];
        const logDate = moment(logData.timestamp).format('YYYY-MM-DD');
        const logTime = moment(logData.timestamp).format('HH:mm:ss');

        // Check if attendance record exists for today
        const [existingAttendance] = await dbConnection.execute(
            'SELECT id, time_in, time_out FROM attendances WHERE employee_id = ? AND DATE(attendance_date) = ?',
            [employee.id, logDate]
        );

        if (existingAttendance.length === 0) {
            // Create new attendance record
            if (logData.action === 'check_in') {
                await dbConnection.execute(
                    'INSERT INTO attendances (employee_id, attendance_date, time_in, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())',
                    [employee.id, logDate, logTime]
                );
                logger.info(`✅ Created attendance record for ${employee.name} - Check In: ${logTime}`);
            }
        } else {
            // Update existing attendance record
            const attendance = existingAttendance[0];
            
            if (logData.action === 'check_in' && !attendance.time_in) {
                await dbConnection.execute(
                    'UPDATE attendances SET time_in = ?, updated_at = NOW() WHERE id = ?',
                    [logTime, attendance.id]
                );
                logger.info(`✅ Updated attendance record for ${employee.name} - Check In: ${logTime}`);
            } else if (logData.action === 'check_out' && attendance.time_in && !attendance.time_out) {
                await dbConnection.execute(
                    'UPDATE attendances SET time_out = ?, updated_at = NOW() WHERE id = ?',
                    [logTime, attendance.id]
                );
                logger.info(`✅ Updated attendance record for ${employee.name} - Check Out: ${logTime}`);
            }
        }

        // Insert fingerprint log for tracking
        await dbConnection.execute(
            'INSERT INTO fingerprint_logs (employee_id, device_id, action, timestamp, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())',
            [employee.id, logData.device_id, logData.action, logData.timestamp]
        );

    } catch (error) {
        logger.error('❌ Error processing fingerprint log:', error.message);
    }
};

// Get fingerprint devices from database
const getFingerprintDevices = async () => {
    try {
        const [devices] = await dbConnection.execute(
            'SELECT id, name, ip_address, port, status FROM finger_devices WHERE status = "active"'
        );
        return devices;
    } catch (error) {
        logger.error('❌ Error getting fingerprint devices:', error.message);
        return [];
    }
};

// Sync data with fingerprint devices
const syncWithFingerprintDevices = async () => {
    try {
        const devices = await getFingerprintDevices();
        
        if (devices.length === 0) {
            logger.warn('⚠️ No active fingerprint devices found');
            return;
        }

        logger.info(`🔄 Syncing with ${devices.length} fingerprint device(s)...`);

        for (const device of devices) {
            await syncWithDevice(device);
        }

    } catch (error) {
        logger.error('❌ Error syncing with fingerprint devices:', error.message);
    }
};

// Sync with individual device
const syncWithDevice = async (device) => {
    try {
        logger.info(`📡 Syncing with device: ${device.name} (${device.ip_address}:${device.port})`);
        
        // Here you would implement the actual communication with the fingerprint device
        // This is a placeholder for the sync logic
        
        // Update device last sync time
        await dbConnection.execute(
            'UPDATE finger_devices SET last_sync = NOW() WHERE id = ?',
            [device.id]
        );

        logger.info(`✅ Sync completed for device: ${device.name}`);
        
    } catch (error) {
        logger.error(`❌ Error syncing with device ${device.name}:`, error.message);
    }
};

// Main function
const main = async () => {
    logger.info('🚀 Starting Node.js Fingerprint Service...');
    
    // Connect to database
    const dbConnected = await connectDatabase();
    if (!dbConnected) {
        logger.error('❌ Failed to connect to database. Exiting...');
        process.exit(1);
    }

    // Initial sync
    await syncWithFingerprintDevices();
    await collectFingerprintData();

    // Schedule periodic sync
    const syncInterval = parseInt(process.env.SYNC_INTERVAL) || 300000; // 5 minutes default
    
    cron.schedule('*/5 * * * *', async () => {
        logger.info('⏰ Running scheduled sync...');
        await syncWithFingerprintDevices();
        await collectFingerprintData();
    });

    logger.info(`✅ Service started. Sync interval: ${syncInterval}ms`);
    logger.info('📊 Monitoring fingerprint devices...');
};

// Handle graceful shutdown
process.on('SIGINT', async () => {
    logger.info('🛑 Shutting down gracefully...');
    if (dbConnection) {
        await dbConnection.end();
    }
    process.exit(0);
});

process.on('SIGTERM', async () => {
    logger.info('🛑 Shutting down gracefully...');
    if (dbConnection) {
        await dbConnection.end();
    }
    process.exit(0);
});

// Start the service
main().catch(error => {
    logger.error('❌ Fatal error:', error);
    process.exit(1);
});
