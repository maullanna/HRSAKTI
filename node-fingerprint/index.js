const axios = require('axios');
const winston = require('winston');
require('dotenv').config();

// Import custom modules
const SoapClient = require('./lib/soapClient');
const XmlParser = require('./lib/xmlParser');
const ApiSender = require('./lib/apiSender');

// Configure logger
const logger = winston.createLogger({
    level: process.env.LOG_LEVEL || 'info',
    format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.errors({ stack: true }),
        winston.format.printf(({ timestamp, level, message, ...meta }) => {
            return `${timestamp} [${level.toUpperCase()}]: ${message} ${Object.keys(meta).length ? JSON.stringify(meta, null, 2) : ''}`;
        })
    ),
    transports: [
        new winston.transports.File({ 
            filename: process.env.LOG_FILE || 'logs/fingerprint.log',
            maxsize: 5242880, // 5MB
            maxFiles: 5
        }),
        new winston.transports.Console({
            format: winston.format.combine(
                winston.format.colorize(),
                winston.format.simple()
            )
        })
    ]
});

// Configuration from environment variables
const config = {
    fingerprint: {
        ip: process.env.FINGERPRINT_DEVICE_IP || '192.168.0.201',
        port: parseInt(process.env.FINGERPRINT_DEVICE_PORT) || 80,
        commKey: parseInt(process.env.FINGERPRINT_COMM_KEY) || 0
    },
    laravel: {
        apiUrl: process.env.LARAVEL_API_URL || 'http://127.0.0.1:8000/api',
        apiToken: process.env.LARAVEL_API_TOKEN || ''
    },
    sync: {
        interval: parseInt(process.env.SYNC_INTERVAL) || 5000 // 5 seconds default
    }
};

// Track last sync time to avoid duplicates
let lastSyncTimestamp = null;

/**
 * Pull attendance data from fingerprint device via SOAP
 * 
 * @returns {Promise<Array>} - Array of attendance records
 */
const pullFingerprintData = async () => {
    try {
        logger.info(`📡 Pulling data from fingerprint device: ${config.fingerprint.ip}:${config.fingerprint.port}`);

        // Get attendance log via SOAP
        const xmlResponse = await SoapClient.getAttendanceLog(
            config.fingerprint.ip,
            config.fingerprint.port,
            config.fingerprint.commKey
        );

        // Parse XML response
        const attendances = await XmlParser.parseAttendanceResponse(xmlResponse);

        logger.info(`✅ Retrieved ${attendances.length} attendance record(s) from fingerprint device`);

        return attendances;

    } catch (error) {
        logger.error(`❌ Failed to pull data from fingerprint device: ${error.message}`);
        throw error;
    }
};

/**
 * Send attendance data to Laravel API
 * 
 * @param {Array} attendances - Array of attendance records
 */
const sendToLaravel = async (attendances) => {
    if (!attendances || attendances.length === 0) {
        logger.info('ℹ️ No attendance data to send');
        return;
    }

    // Filter new records (only send if datetime is newer than last sync)
    let newAttendances = attendances;
    if (lastSyncTimestamp) {
        newAttendances = attendances.filter(att => {
            const attTime = new Date(att.datetime).getTime();
            return attTime > lastSyncTimestamp;
        });
    }

    if (newAttendances.length === 0) {
        logger.info('ℹ️ No new attendance records to sync');
        return;
    }

    logger.info(`📤 Sending ${newAttendances.length} attendance record(s) to Laravel API...`);

    // Send each attendance record to Laravel API
    let successCount = 0;
    let errorCount = 0;
    let duplicateCount = 0;

    for (const attendance of newAttendances) {
        const result = await ApiSender.sendSingle(
            config.laravel.apiUrl,
            config.laravel.apiToken,
            attendance,
            logger
        );

        if (result.success) {
            successCount++;
            // Update last sync timestamp
            const attTime = new Date(attendance.datetime).getTime();
            if (!lastSyncTimestamp || attTime > lastSyncTimestamp) {
                lastSyncTimestamp = attTime;
            }
        } else if (result.duplicate) {
            duplicateCount++;
            // Still update timestamp for duplicates
            const attTime = new Date(attendance.datetime).getTime();
            if (!lastSyncTimestamp || attTime > lastSyncTimestamp) {
                lastSyncTimestamp = attTime;
            }
        } else {
            errorCount++;
        }

        // Small delay between requests to avoid overwhelming API
        await new Promise(resolve => setTimeout(resolve, 100));
    }

    logger.info(`✅ Sync completed: ${successCount} success, ${duplicateCount} duplicates, ${errorCount} errors`);
};

/**
 * Main sync function - Pull from fingerprint and send to Laravel
 */
const syncAttendance = async () => {
    try {
        logger.info('🔄 Starting attendance sync...');

        // Test connection to fingerprint device
        const fingerprintConnected = await SoapClient.testConnection(
            config.fingerprint.ip,
            config.fingerprint.port
        );

        if (!fingerprintConnected) {
            logger.error(`❌ Cannot connect to fingerprint device at ${config.fingerprint.ip}:${config.fingerprint.port}`);
            logger.error('⚠️ Please check:');
            logger.error('   1. Fingerprint device is powered on and connected to network');
            logger.error('   2. IP address is correct: ' + config.fingerprint.ip);
            logger.error('   3. Network connection is available');
            return;
        }

        // Test connection to Laravel API
        const apiConnected = await ApiSender.testConnection(
            config.laravel.apiUrl,
            config.laravel.apiToken
        );

        if (!apiConnected) {
            logger.error(`❌ Cannot connect to Laravel API at ${config.laravel.apiUrl}`);
            logger.error('⚠️ Please check:');
            logger.error('   1. Laravel application is running and accessible');
            logger.error('   2. API URL is correct: ' + config.laravel.apiUrl);
            logger.error('   3. API token is correct');
            logger.error('   4. Internet connection is available (for production)');
            return;
        }

        // Pull data from fingerprint device
        const attendances = await pullFingerprintData();

        // Send data to Laravel API
        await sendToLaravel(attendances);

        logger.info('✅ Attendance sync completed successfully');

    } catch (error) {
        logger.error(`❌ Sync failed: ${error.message}`);
        if (error.stack) {
            logger.error(`Stack trace: ${error.stack}`);
        }
    }
};

/**
 * Main function - Start the service
 */
const main = async () => {
    logger.info('🚀 Starting Node.js Fingerprint Sync Service...');
    logger.info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    logger.info(`📋 Configuration:`);
    logger.info(`   Fingerprint Device: ${config.fingerprint.ip}:${config.fingerprint.port}`);
    logger.info(`   Laravel API URL: ${config.laravel.apiUrl}`);
    logger.info(`   Sync Interval: ${config.sync.interval}ms (${config.sync.interval / 1000}s)`);
    logger.info(`   API Token: ${config.laravel.apiToken ? '✅ Set' : '❌ Not set (recommended for security)'}`);
    logger.info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

    // Initial sync
    logger.info('🔄 Running initial sync...');
    await syncAttendance();

    // Setup interval sync
    const intervalMs = config.sync.interval;
    logger.info(`⏰ Setting up auto-sync every ${intervalMs / 1000} seconds...`);

    setInterval(async () => {
        await syncAttendance();
    }, intervalMs);

    logger.info('✅ Service is running. Monitoring fingerprint device...');
    logger.info('📝 Press Ctrl+C to stop the service');
};

// Handle graceful shutdown
process.on('SIGINT', async () => {
    logger.info('🛑 Received SIGINT. Shutting down gracefully...');
    logger.info('👋 Service stopped');
    process.exit(0);
});

process.on('SIGTERM', async () => {
    logger.info('🛑 Received SIGTERM. Shutting down gracefully...');
    logger.info('👋 Service stopped');
    process.exit(0);
});

// Handle unhandled errors
process.on('unhandledRejection', (reason, promise) => {
    logger.error('❌ Unhandled Rejection at:', promise, 'reason:', reason);
});

process.on('uncaughtException', (error) => {
    logger.error('❌ Uncaught Exception:', error);
    process.exit(1);
});

// Start the service
main().catch(error => {
    logger.error('❌ Fatal error starting service:', error);
    process.exit(1);
});
