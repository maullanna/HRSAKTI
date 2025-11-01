const { parseString } = require('xml2js');

/**
 * XML Parser for Solution X401 Fingerprint Device Response
 * Based on parse.php from official SDK
 */
class XmlParser {
    /**
     * Parse SOAP XML response from fingerprint device
     * 
     * @param {string} xmlString - Raw XML response from fingerprint device
     * @returns {Promise<Array>} - Array of attendance records
     */
    static async parseAttendanceResponse(xmlString) {
        try {
            // Parse XML string to JSON
            const result = await parseString(xmlString, {
                explicitArray: false,
                mergeAttrs: true,
                trim: true
            });

            // Extract GetAttLogResponse
            const response = result?.GetAttLogResponse || result?.soapenv?.Body?.GetAttLogResponse;
            
            if (!response) {
                throw new Error('Invalid XML response format: GetAttLogResponse not found');
            }

            // Extract rows - handle both single and multiple rows
            let rows = [];
            
            if (response.Row) {
                // If single row, convert to array
                if (Array.isArray(response.Row)) {
                    rows = response.Row;
                } else {
                    rows = [response.Row];
                }
            }

            // Parse each row to attendance object
            const attendances = rows.map(row => {
                return {
                    pin: parseInt(row.PIN) || null,
                    datetime: row.DateTime || null,
                    verified: parseInt(row.Verified) || 15,
                    status: parseInt(row.Status) || 0
                };
            }).filter(att => att.pin && att.datetime); // Filter invalid records

            return attendances;

        } catch (error) {
            if (error.message.includes('Invalid XML')) {
                throw error;
            }
            throw new Error(`Failed to parse XML response: ${error.message}`);
        }
    }

    /**
     * Helper function to extract data between tags (similar to parse.php)
     * 
     * @param {string} data - XML string
     * @param {string} startTag - Start tag
     * @param {string} endTag - End tag
     * @returns {string} - Extracted content
     */
    static extractBetweenTags(data, startTag, endTag) {
        const startIndex = data.indexOf(startTag);
        if (startIndex === -1) return '';
        
        const endIndex = data.indexOf(endTag, startIndex + startTag.length);
        if (endIndex === -1) return '';
        
        return data.substring(startIndex + startTag.length, endIndex);
    }
}

module.exports = XmlParser;

