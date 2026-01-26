#!/usr/bin/env node

console.log('=== Starting Shipment Test ===');
console.log('Node version:', process.version);

// Test fetch availability
if (globalThis.fetch) {
    console.log('✅ Fetch is available');
} else {
    console.log('❌ Fetch not available, trying axios...');
    try {
        require('axios');
        console.log('✅ Axios is available');
    } catch (e) {
        console.log('❌ Neither fetch nor axios available');
        process.exit(1);
    }
}

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
console.log('API URL:', API_URL);

// Use Node's built-in fetch
let httpRequest;
if (globalThis.fetch) {
    httpRequest = {
        post: async (url, data, config) => {
            console.log('POST', url);
            const response = await fetch(url, {
                method: 'POST',
                headers: config?.headers || {},
                body: JSON.stringify(data)
            });
            const responseData = await response.json();
            return {
                status: response.status,
                data: responseData
            };
        },
        get: async (url, config) => {
            console.log('GET', url);
            const response = await fetch(url, {
                method: 'GET',
                headers: config?.headers || {}
            });
            const responseData = await response.json();
            return {
                status: response.status,
                data: responseData
            };
        }
    };
} else {
    const axios = require('axios');
    httpRequest = axios;
}

const TEST_EMAIL = process.env.TEST_EMAIL || 'admin@example.com';
const TEST_PASSWORD = process.env.TEST_PASSWORD || 'password';

async function testLogin() {
    try {
        console.log('\n🔐 Step 1: Testing Authentication...');
        const response = await httpRequest.post(`${API_URL}/login`, {
            email: TEST_EMAIL,
            password: TEST_PASSWORD
        }, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        console.log('Response status:', response.status);
        console.log('Response data:', JSON.stringify(response.data, null, 2));

        if (response.status === 200 && (response.data.token || response.data.accessToken)) {
            const token = response.data.token || response.data.accessToken;
            console.log('✅ Authentication successful');
            return token;
        } else {
            console.log('❌ Authentication failed - no token');
            return null;
        }
    } catch (error) {
        console.log('❌ Authentication error:', error.message);
        if (error.response) {
            console.log('Status:', error.response.status);
            console.log('Data:', JSON.stringify(error.response.data, null, 2));
        }
        return null;
    }
}

async function testListShipments(token) {
    try {
        console.log('\n📋 Step 2: Testing Shipment Listing...');
        const response = await httpRequest.get(`${API_URL}/shipments`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        console.log('Response status:', response.status);
        
        if (response.status === 200) {
            const data = response.data.data || response.data;
            const shipments = Array.isArray(data) ? data : (data.data || []);
            console.log(`✅ Retrieved ${shipments.length} shipments`);
            
            if (shipments.length > 0) {
                console.log('First shipment:', JSON.stringify(shipments[0], null, 2));
            }
            return true;
        } else {
            console.log('❌ Unexpected status:', response.status);
            return false;
        }
    } catch (error) {
        console.log('❌ Listing error:', error.message);
        if (error.response) {
            console.log('Status:', error.response.status);
            console.log('Data:', JSON.stringify(error.response.data, null, 2));
        }
        return false;
    }
}

async function runTests() {
    console.log('\n' + '='.repeat(60));
    console.log('🧪 Shipment API Test Suite');
    console.log('='.repeat(60));

    const token = await testLogin();
    if (!token) {
        console.log('\n❌ Cannot proceed without authentication');
        process.exit(1);
    }

    const listResult = await testListShipments(token);

    console.log('\n' + '='.repeat(60));
    console.log('📊 Test Results Summary');
    console.log('='.repeat(60));
    console.log(`Authentication: ${token ? '✅ PASS' : '❌ FAIL'}`);
    console.log(`List Shipments: ${listResult ? '✅ PASS' : '❌ FAIL'}`);
    console.log('='.repeat(60));

    if (token && listResult) {
        console.log('\n✅ All tests PASSED');
        process.exit(0);
    } else {
        console.log('\n❌ Some tests FAILED');
        process.exit(1);
    }
}

runTests().catch(error => {
    console.error('\n❌ Test suite error:', error);
    console.error('Stack:', error.stack);
    process.exit(1);
});
