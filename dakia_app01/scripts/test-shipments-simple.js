#!/usr/bin/env node

/**
 * Simple Shipment Test - Outputs to file for reliability
 */

const fs = require('fs');
const path = require('path');

// Use Node's built-in fetch (Node 18+) or axios as fallback
let httpRequest;
try {
    // Try Node's built-in fetch first
    if (globalThis.fetch) {
        httpRequest = {
            post: async (url, data, config) => {
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
        throw new Error('fetch not available');
    }
} catch (e) {
    // Fallback to axios
    try {
        const axios = require('axios');
        httpRequest = axios;
    } catch (e2) {
        console.error('❌ Neither fetch nor axios available. Please install: npm install axios');
        process.exit(1);
    }
}

const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
const TEST_EMAIL = process.env.TEST_EMAIL || 'admin@example.com';
const TEST_PASSWORD = process.env.TEST_PASSWORD || 'password';
const OUTPUT_FILE = path.join(__dirname, 'test-results.txt');

function log(message) {
    const timestamp = new Date().toISOString();
    const logMessage = `[${timestamp}] ${message}\n`;
    try {
        fs.appendFileSync(OUTPUT_FILE, logMessage);
    } catch (e) {
        // If file write fails, still log to console
        console.error('Failed to write to log file:', e.message);
    }
    console.log(message);
}

// Clear previous results and ensure directory exists
try {
    if (fs.existsSync(OUTPUT_FILE)) {
        fs.unlinkSync(OUTPUT_FILE);
    }
    // Ensure directory exists
    const dir = path.dirname(OUTPUT_FILE);
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
    }
} catch (e) {
    console.error('Failed to setup output file:', e.message);
}

let authToken = null;
let createdShipmentId = null;

const testShipment = {
    consignment: {
        customer_id: 148,
        service_type: 'EXPRESS',
        warehouse_id: 1,
        reference: `TEST-${Date.now()}`,
        notes: 'Test shipment created by automated test',
        company: 'Test Company Ltd',
        contact: 'John Doe',
        email: 'receiver@test.com',
        telephone: '+1234567890',
        address_line_1: '123 Test Street',
        address_line_2: 'Suite 100',
        city: 'London',
        state: 'Greater London',
        postcode: 'SW1A 1AA',
        country_id: 1,
        sender_company: 'Sender Company',
        sender_contact: 'Jane Smith',
        sender_email: 'sender@test.com',
        sender_telephone: '+0987654321',
        sender_address_line_1: '456 Sender Ave',
        sender_city: 'Manchester',
        sender_postcode: 'M1 1AA',
        sender_country_id: 1
    },
    parcels: [
        {
            weight: 2.5,
            length: 30,
            width: 20,
            height: 15,
            notes: 'Test parcel 1',
            items: [
                {
                    description: 'Test Item 1',
                    quantity: 2,
                    weight: 1.2,
                    value: 50.00
                },
                {
                    description: 'Test Item 2',
                    quantity: 1,
                    weight: 1.3,
                    value: 30.00
                }
            ]
        }
    ]
};

async function login() {
    try {
        log('\n🔐 Step 1: Authenticating...');
        const response = await httpRequest.post(`${API_URL}/login`, {
            email: TEST_EMAIL,
            password: TEST_PASSWORD
        }, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = response.data;

        if (data && data.token) {
            authToken = data.token;
            log('✅ Authentication successful');
            return true;
        } else if (data && data.accessToken) {
            authToken = data.accessToken;
            log('✅ Authentication successful');
            return true;
        } else {
            log('❌ No token in response');
            log(`Response: ${JSON.stringify(data, null, 2)}`);
            return false;
        }
    } catch (error) {
        log('❌ Authentication failed');
        if (error.response) {
            log(`Status: ${error.response.status}`);
            log(`Error: ${JSON.stringify(error.response.data, null, 2)}`);
        } else {
            log(`Error: ${error.message}`);
        }
        return false;
    }
}

async function testCreateShipment() {
    try {
        log('\n📦 Step 2: Testing Shipment Creation...');

        const response = await httpRequest.post(`${API_URL}/shipments`, testShipment, {
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status === 201 || response.status === 200) {
            const shipment = response.data.data || response.data;
            createdShipmentId = shipment.id || shipment.uuid;
            
            log('✅ Shipment created successfully');
            log(`Shipment ID: ${createdShipmentId}`);
            log(`Reference: ${shipment.reference || testShipment.consignment.reference}`);
            
            const requiredFields = ['id', 'reference', 'status', 'customer_id'];
            const missingFields = requiredFields.filter(field => !shipment[field] && !shipment.data?.[field]);
            
            if (missingFields.length > 0) {
                log(`⚠️  Missing fields in response: ${missingFields.join(', ')}`);
            } else {
                log('✅ Response structure valid');
            }
            
            return true;
        } else {
            log(`❌ Unexpected status code: ${response.status}`);
            return false;
        }
    } catch (error) {
        log('❌ Shipment creation failed');
        if (error.response) {
            log(`Status: ${error.response.status}`);
            log(`Error Data: ${JSON.stringify(error.response.data, null, 2)}`);
            
            if (error.response.data.errors) {
                log('\nValidation Errors:');
                Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                    log(`  ${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`);
                });
            }
        } else {
            log(`Error: ${error.message}`);
        }
        return false;
    }
}

async function testListShipments() {
    try {
        log('\n📋 Step 3: Testing Shipment Listing...');

        const response = await httpRequest.get(`${API_URL}/shipments`, {
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            }
        });

        if (response.status === 200) {
            const responseData = response.data;
            const data = responseData.data || responseData;
            const shipments = Array.isArray(data) ? data : (data.data || []);
            
            log(`✅ Retrieved ${shipments.length} shipments`);
            
            if (createdShipmentId) {
                const found = shipments.find(s => 
                    (s.id && s.id.toString() === createdShipmentId.toString()) ||
                    (s.uuid && s.uuid === createdShipmentId)
                );
                
                if (found) {
                    log('✅ Created shipment found in list');
                } else {
                    log('⚠️  Created shipment not found in list (might be paginated)');
                }
            }
            
            if (shipments.length > 0) {
                const firstShipment = shipments[0];
                const requiredFields = ['id', 'reference'];
                const missingFields = requiredFields.filter(field => !firstShipment[field]);
                
                if (missingFields.length > 0) {
                    log(`⚠️  Missing fields in shipment: ${missingFields.join(', ')}`);
                } else {
                    log('✅ Shipment list structure valid');
                }
            }
            
            return true;
        } else {
            log(`❌ Unexpected status code: ${response.status}`);
            return false;
        }
    } catch (error) {
        log('❌ Shipment listing failed');
        if (error.response) {
            log(`Status: ${error.response.status}`);
            log(`Error: ${JSON.stringify(error.response.data, null, 2)}`);
        } else {
            log(`Error: ${error.message}`);
        }
        return false;
    }
}

async function testGetShipment() {
    if (!createdShipmentId) {
        log('\n⚠️  Skipping Get Shipment test (no shipment created)');
        return true;
    }

    try {
        log('\n🔍 Step 4: Testing Get Single Shipment...');
        log(`Shipment ID: ${createdShipmentId}`);

        const response = await httpRequest.get(`${API_URL}/shipments/${createdShipmentId}`, {
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            }
        });

        if (response.status === 200) {
            const data = response.data;
            const shipment = data.data || data;
            
            log('✅ Shipment retrieved successfully');
            log(`Reference: ${shipment.reference}`);
            log(`Status: ${shipment.status}`);
            
            if (shipment.parcels && Array.isArray(shipment.parcels)) {
                log(`Parcels: ${shipment.parcels.length}`);
                shipment.parcels.forEach((parcel, index) => {
                    log(`  Parcel ${index + 1}: ${parcel.weight}kg, Items: ${parcel.items?.length || 0}`);
                });
            }
            
            return true;
        } else {
            log(`❌ Unexpected status code: ${response.status}`);
            return false;
        }
    } catch (error) {
        log('❌ Get shipment failed');
        if (error.response) {
            log(`Status: ${error.response.status}`);
            log(`Error: ${JSON.stringify(error.response.data, null, 2)}`);
        } else {
            log(`Error: ${error.message}`);
        }
        return false;
    }
}

async function runTests() {
    log('\n' + '='.repeat(60));
    log('🧪 Shipment API Test Suite');
    log('='.repeat(60) + '\n');

    const results = {
        login: false,
        create: false,
        list: false,
        get: false
    };

    results.login = await login();
    if (!results.login) {
        log('\n❌ Cannot proceed without authentication');
        process.exit(1);
    }

    results.create = await testCreateShipment();
    results.list = await testListShipments();
    results.get = await testGetShipment();

    log('\n' + '='.repeat(60));
    log('📊 Test Results Summary');
    log('='.repeat(60));
    log(`Authentication: ${results.login ? '✅ PASS' : '❌ FAIL'}`);
    log(`Create Shipment: ${results.create ? '✅ PASS' : '❌ FAIL'}`);
    log(`List Shipments: ${results.list ? '✅ PASS' : '❌ FAIL'}`);
    log(`Get Shipment: ${results.get ? '✅ PASS' : '❌ FAIL'}`);
    log('='.repeat(60));
    
    const allPassed = Object.values(results).every(r => r === true);
    
    if (allPassed) {
        log('\n✅ All tests PASSED');
        process.exit(0);
    } else {
        log('\n❌ Some tests FAILED');
        process.exit(1);
    }
}

// Wrap in try-catch to ensure errors are logged
(async () => {
    try {
        await runTests();
    } catch (error) {
        console.error('❌ Test suite error:', error);
        try {
            log(`\n❌ Test suite error: ${error.message}`);
            if (error.stack) {
                log(`Stack: ${error.stack}`);
            }
        } catch (logError) {
            console.error('Failed to log error:', logError);
            console.error('Original error:', error);
        }
        process.exit(1);
    }
})();
