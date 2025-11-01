const axios = require('axios');
const winston = require('winston');

/**
 * API Sender - Send attendance data to Laravel API
 */
class ApiSender {
    /**
     * Send single attendance record to Laravel API
     * 
     * @param {string} apiUrl - Laravel API endpoint URL
     * @param {string} apiToken - API authentication token
     * @param {Object} attendanceData - Attendance data object
     * @param {winston.Logger} logger - Winston logger instance
     * @returns {Promise<Object>} - API response
     */
    static async sendSingle(apiUrl, apiToken, attendanceData, logger = null) {
        try {
            const response = await axios.post(`${apiUrl}/fingerprint/sync`, {
                pin: attendanceData.pin,
                datetime: attendanceData.datetime,
                verified: attendanceData.verified || 15,
                status: attendanceData.status || 0
            }, {
                headers: {
                    'Authorization': `Bearer ${apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                timeout: 10000 // 10 seconds timeout
            });

            if (logger) {
                logger.info(`✅ Attendance synced: PIN ${attendanceData.pin}, ${attendanceData.datetime}`);
            }

            return {
                success: true,
                data: response.data
            };

        } catch (error) {
            let errorMessage = 'Unknown error';
            
            if (error.response) {
                // Laravel API returned error
                const status = error.response.status;
                const data = error.response.data;
                
                if (status === 409) {
                    // Conflict - attendance already exists (not an error, just duplicate)
                    if (logger) {
                        logger.warn(`⚠️ Attendance already exists: PIN ${attendanceData.pin}, ${attendanceData.datetime}`);
                    }
                    return {
                        success: false,
                        duplicate: true,
                        message: data.message || 'Attendance already exists'
                    };
                }
                
                errorMessage = data.message || `API error: ${status}`;
            } else if (error.request) {
                // Request made but no response
                errorMessage = `No response from Laravel API: ${error.message}`;
            } else {
                // Request setup error
                errorMessage = `Request failed: ${error.message}`;
            }

            if (logger) {
                logger.error(`❌ Failed to sync attendance (PIN ${attendanceData.pin}): ${errorMessage}`);
            }

            return {
                success: false,
                error: errorMessage
            };
        }
    }

    /**
     * Send multiple attendance records to Laravel API (bulk sync)
     * 
     * @param {string} apiUrl - Laravel API endpoint URL
     * @param {string} apiToken - API authentication token
     * @param {Array} attendances - Array of attendance data objects
     * @param {winston.Logger} logger - Winston logger instance
     * @returns {Promise<Object>} - Bulk sync result
     */
    static async sendBulk(apiUrl, apiToken, attendances, logger = null) {
        try {
            const response = await axios.post(`${apiUrl}/fingerprint/sync-bulk`, {
                attendances: attendances.map(att => ({
                    pin: att.pin,
                    datetime: att.datetime,
                    verified: att.verified || 15,
                    status: att.status || 0
                }))
            }, {
                headers: {
                    'Authorization': `Bearer ${apiToken}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                timeout: 30000 // 30 seconds for bulk
            });

            if (logger) {
                const result = response.data.data || {};
                logger.info(`✅ Bulk sync: ${result.success_count || 0} success, ${result.error_count || 0} errors`);
            }

            return {
                success: true,
                data: response.data
            };

        } catch (error) {
            let errorMessage = 'Unknown error';
            
            if (error.response) {
                errorMessage = error.response.data?.message || `API error: ${error.response.status}`;
            } else if (error.request) {
                errorMessage = `No response from Laravel API: ${error.message}`;
            } else {
                errorMessage = `Request failed: ${error.message}`;
            }

            if (logger) {
                logger.error(`❌ Bulk sync failed: ${errorMessage}`);
            }

            return {
                success: false,
                error: errorMessage
            };
        }
    }

    /**
     * Test connection to Laravel API
     * 
     * @param {string} apiUrl - Laravel API base URL
     * @param {string} apiToken - API authentication token
     * @returns {Promise<boolean>} - true if API is accessible
     */
    static async testConnection(apiUrl, apiToken) {
        try {
            // Try to sync empty data to test connection (will fail validation but confirms API is reachable)
            await axios.post(`${apiUrl}/fingerprint/sync`, {}, {
                headers: {
                    'Authorization': `Bearer ${apiToken}`,
                    'Content-Type': 'application/json'
                },
                timeout: 5000,
                validateStatus: () => true // Accept any status code
            });
            return true;
        } catch (error) {
            return false;
        }
    }
}

module.exports = ApiSender;

