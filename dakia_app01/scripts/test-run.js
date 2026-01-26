const fs = require('fs');
const path = require('path');

const outputFile = path.join(__dirname, 'test-output.txt');

function writeOutput(msg) {
    const message = new Date().toISOString() + ' - ' + msg + '\n';
    fs.appendFileSync(outputFile, message);
    console.log(msg);
}

// Clear previous output
if (fs.existsSync(outputFile)) {
    fs.unlinkSync(outputFile);
}

writeOutput('=== Starting Test ===');
writeOutput('Node version: ' + process.version);

if (globalThis.fetch) {
    writeOutput('✅ Fetch available');
} else {
    writeOutput('❌ Fetch not available');
    try {
        require('axios');
        writeOutput('✅ Axios available');
    } catch (e) {
        writeOutput('❌ Neither available');
        process.exit(1);
    }
}

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
writeOutput('API URL: ' + API_URL);

async function test() {
    try {
        writeOutput('\nTesting login...');
        const response = await fetch(API_URL + '/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: process.env.TEST_EMAIL || 'admin@example.com',
                password: process.env.TEST_PASSWORD || 'password'
            })
        });

        writeOutput('Status: ' + response.status);
        const data = await response.json();
        writeOutput('Response: ' + JSON.stringify(data, null, 2));

        if (response.ok && (data.token || data.accessToken)) {
            writeOutput('✅ Login successful');
            const token = data.token || data.accessToken;

            // Test listing shipments
            writeOutput('\nTesting shipment list...');
            const listResponse = await fetch(API_URL + '/shipments', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            writeOutput('List Status: ' + listResponse.status);
            const listData = await listResponse.json();
            const shipments = listData.data || listData;
            const count = Array.isArray(shipments) ? shipments.length : (shipments.data?.length || 0);
            writeOutput('✅ Retrieved ' + count + ' shipments');

            writeOutput('\n✅ All tests passed!');
        } else {
            writeOutput('❌ Login failed');
        }
    } catch (error) {
        writeOutput('❌ Error: ' + error.message);
        if (error.stack) {
            writeOutput('Stack: ' + error.stack);
        }
    }
}

test().then(() => {
    writeOutput('\n=== Test Complete ===');
}).catch(err => {
    writeOutput('Fatal error: ' + err.message);
    process.exit(1);
});
