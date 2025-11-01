const axios = require('axios');

/**
 * SOAP Client for Solution X401 Fingerprint Device
 * Based on official SDK (tarik-data.php)
 */
class SoapClient {
    /**
     * Get attendance log from fingerprint device via SOAP Web Service
     * 
     * @param {string} ip - Fingerprint device IP address
     * @param {number} port - HTTP port (default: 80)
     * @param {number} commKey - Communication key (default: 0)
     * @returns {Promise<string>} - Raw XML response
     */
    static async getAttendanceLog(ip, port = 80, commKey = 0) {
        try {
            // Format SOAP request sesuai SDK resmi Solution X401
            const soapRequest = `<GetAttLog><ArgComKey xsi:type="xsd:integer">${commKey}</ArgComKey><Arg><PIN xsi:type="xsd:integer">All</PIN></Arg></GetAttLog>`;

            // HTTP POST request ke fingerprint device
            const url = `http://${ip}:${port}/iWsService`;
            
            const response = await axios.post(url, soapRequest, {
                headers: {
                    'Content-Type': 'text/xml',
                    'Content-Length': Buffer.byteLength(soapRequest, 'utf8').toString()
                },
                timeout: 10000, // 10 seconds timeout
                responseType: 'text'
            });

            return response.data;

        } catch (error) {
            if (error.code === 'ECONNREFUSED' || error.code === 'ETIMEDOUT') {
                throw new Error(`Cannot connect to fingerprint device at ${ip}:${port}. Please check network connection and IP address.`);
            }
            if (error.response) {
                throw new Error(`Fingerprint device returned error: ${error.response.status} - ${error.response.statusText}`);
            }
            throw new Error(`SOAP request failed: ${error.message}`);
        }
    }

    /**
     * Test connection to fingerprint device
     * 
     * @param {string} ip - Fingerprint device IP address
     * @param {number} port - HTTP port (default: 80)
     * @returns {Promise<boolean>} - true if connection successful
     */
    static async testConnection(ip, port = 80) {
        try {
            const url = `http://${ip}:${port}/iWsService`;
            await axios.post(url, '<GetAttLog><ArgComKey xsi:type="xsd:integer">0</ArgComKey><Arg><PIN xsi:type="xsd:integer">All</PIN></Arg></GetAttLog>', {
                headers: { 'Content-Type': 'text/xml' },
                timeout: 5000,
                responseType: 'text'
            });
            return true;
        } catch (error) {
            return false;
        }
    }
}

module.exports = SoapClient;

