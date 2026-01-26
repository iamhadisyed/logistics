#!/usr/bin/env node

/**
 * Automated Test: Shipment Save and List
 * 
 * This script tests:
 * 1. Creating a new shipment (POST /api/shipments)
 * 2. Listing shipments (GET /api/shipments)
 * 3. Validates data structure and required fields
 * 
 * Run: node scripts/test-shipments.js
 */

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
                return {
                    status: response.status,
                    data: await response.json()
                };
            },
            get: async (url, config) => {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: config?.headers || {}
                });
                return {
                    status: response.status,
                    data: await response.json()
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

const colors = {
    reset: '\x1b[0m',
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
};

function log(message, color = 'reset') {
    console.log(`${colors[color]}${message}${colors.reset}`);
}

let authToken = null;
let createdShipmentId = null;

// Test data
const testShipment = {
    consignment: {
        customer_id: 148,
        service_type: 'EXPRESS',
        warehouse_id: 1,
        reference: `TEST-${Date.now()}`,
        notes: 'Test shipment created by automated test',
        // Receiver
        company: 'Test Company Ltd',
        contact: 'John Doe',
        email: 'receiver@test.com',
        telephone: '+1234567890',
        address_line_1: '123 Test Street',
        address_line_2: 'Suite 100',
        address_line_3: '',
        city: 'London',
        state: 'Greater London',
        postcode: 'SW1A 1AA',
        country_id: 1,
        // Sender (optional)
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
        log('\n🔐 Step 1: Authenticating...', 'blue');
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
            log('✅ Authentication successful', 'green');
            return true;
        } else if (data && data.accessToken) {
            authToken = data.accessToken;
            log('✅ Authentication successful', 'green');
            return true;
        } else {
            log('❌ No token in response', 'red');
            log(`Response: ${JSON.stringify(data, null, 2)}`, 'yellow');
            return false;
        }
    } catch (error) {
        log('❌ Authentication failed', 'red');
        if (error.response) {
            log(`Status: ${error.response.status}`, 'red');
            log(`Error: ${JSON.stringify(error.response.data, null, 2)}`, 'yellow');
        } else {
            log(`Error: ${error.message}`, 'red');
        }
        return false;
    }
}

async function testCreateShipment() {
    try {
        log('\n📦 Step 2: Testing Shipment Creation...', 'blue');
        log(`Payload: ${JSON.stringify(testShipment, null, 2)}`, 'blue');

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
            
            log('✅ Shipment created successfully', 'green');
            log(`Shipment ID: ${createdShipmentId}`, 'green');
            log(`Reference: ${shipment.reference || testShipment.consignment.reference}`, 'green');
            
            // Validate response structure
            const requiredFields = ['id', 'reference', 'status', 'customer_id'];
            const missingFields = requiredFields.filter(field => !shipment[field] && !shipment.data?.[field]);
            
            if (missingFields.length > 0) {
                log(`⚠️  Missing fields in response: ${missingFields.join(', ')}`, 'yellow');
            } else {
                log('✅ Response structure valid', 'green');
            }
            
            return true;
        } else {
            log(`❌ Unexpected status code: ${response.status}`, 'red');
            return false;
        }
    } catch (error) {
        log('❌ Shipment creation failed', 'red');
        // Handle both axios-style and fetch-style errors
        const errorResponse = error.response || (error.status ? { status: error.status, data: error } : null);
        
        if (errorResponse) {
            log(`Status: ${errorResponse.status}`, 'red');
            const errorData = errorResponse.data || errorResponse;
            log(`Error Data: ${JSON.stringify(errorData, null, 2)}`, 'yellow');
            
            if (errorData.errors) {
                log('\nValidation Errors:', 'red');
                Object.entries(errorData.errors).forEach(([field, messages]) => {
                    log(`  ${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`, 'red');
                });
            }
        } else {
            log(`Error: ${error.message}`, 'red');
            if (error.stack) {
                log(`Stack: ${error.stack}`, 'red');
            }
        }
        return false;
    }
}

async function testListShipments() {
    try {
        log('\n📋 Step 3: Testing Shipment Listing...', 'blue');

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
            
            log(`✅ Retrieved ${shipments.length} shipments`, 'green');
            
            // Check if our created shipment is in the list
            if (createdShipmentId) {
                const found = shipments.find(s => 
                    (s.id && s.id.toString() === createdShipmentId.toString()) ||
                    (s.uuid && s.uuid === createdShipmentId)
                );
                
                if (found) {
                    log('✅ Created shipment found in list', 'green');
                } else {
                    log('⚠️  Created shipment not found in list (might be paginated)', 'yellow');
                }
            }
            
            // Validate structure of first shipment if available
            if (shipments.length > 0) {
                const firstShipment = shipments[0];
                const requiredFields = ['id', 'reference'];
                const missingFields = requiredFields.filter(field => !firstShipment[field]);
                
                if (missingFields.length > 0) {
                    log(`⚠️  Missing fields in shipment: ${missingFields.join(', ')}`, 'yellow');
                } else {
                    log('✅ Shipment list structure valid', 'green');
                }
            }
            
            return true;
        } else {
            log(`❌ Unexpected status code: ${response.status}`, 'red');
            return false;
        }
    } catch (error) {
        log('❌ Shipment listing failed', 'red');
        if (error.response) {
            log(`Status: ${error.response.status}`, 'red');
            log(`Error: ${JSON.stringify(error.response.data, null, 2)}`, 'yellow');
        } else {
            log(`Error: ${error.message}`, 'red');
        }
        return false;
    }
}

async function testGetShipment() {
    if (!createdShipmentId) {
        log('\n⚠️  Skipping Get Shipment test (no shipment created)', 'yellow');
        return true;
    }

    try {
        log('\n🔍 Step 4: Testing Get Single Shipment...', 'blue');
        log(`Shipment ID: ${createdShipmentId}`, 'blue');

        const response = await httpRequest.get(`${API_URL}/shipments/${createdShipmentId}`, {
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
            }
        });

        if (response.status === 200) {
            const data = response.data;
            const shipment = data.data || data;
            
            log('✅ Shipment retrieved successfully', 'green');
            log(`Reference: ${shipment.reference}`, 'green');
            log(`Status: ${shipment.status}`, 'green');
            
            // Check for parcels
            if (shipment.parcels && Array.isArray(shipment.parcels)) {
                log(`Parcels: ${shipment.parcels.length}`, 'green');
                shipment.parcels.forEach((parcel, index) => {
                    log(`  Parcel ${index + 1}: ${parcel.weight}kg, Items: ${parcel.items?.length || 0}`, 'blue');
                });
            }
            
            return true;
        } else {
            log(`❌ Unexpected status code: ${response.status}`, 'red');
            return false;
        }
    } catch (error) {
        log('❌ Get shipment failed', 'red');
        if (error.response) {
            log(`Status: ${error.response.status}`, 'red');
            log(`Error: ${JSON.stringify(error.response.data, null, 2)}`, 'yellow');
        } else {
            log(`Error: ${error.message}`, 'red');
        }
        return false;
    }
}

async function runTests() {
    log('\n' + '='.repeat(60), 'blue');
    log('🧪 Shipment API Test Suite', 'blue');
    log('='.repeat(60) + '\n', 'blue');

    const results = {
        login: false,
        create: false,
        list: false,
        get: false
    };

    // Step 1: Login
    results.login = await login();
    if (!results.login) {
        log('\n❌ Cannot proceed without authentication', 'red');
        process.exit(1);
    }

    // Step 2: Create Shipment
    results.create = await testCreateShipment();

    // Step 3: List Shipments
    results.list = await testListShipments();

    // Step 4: Get Single Shipment
    results.get = await testGetShipment();

    // Summary
    log('\n' + '='.repeat(60), 'blue');
    log('📊 Test Results Summary', 'blue');
    log('='.repeat(60), 'blue');
    log(`Authentication: ${results.login ? '✅ PASS' : '❌ FAIL'}`, results.login ? 'green' : 'red');
    log(`Create Shipment: ${results.create ? '✅ PASS' : '❌ FAIL'}`, results.create ? 'green' : 'red');
    log(`List Shipments: ${results.list ? '✅ PASS' : '❌ FAIL'}`, results.list ? 'green' : 'red');
    log(`Get Shipment: ${results.get ? '✅ PASS' : '❌ FAIL'}`, results.get ? 'green' : 'red');
    
    const allPassed = Object.values(results).every(r => r === true);
    log('='.repeat(60), 'blue');
    
    if (allPassed) {
        log('\n✅ All tests PASSED', 'green');
        process.exit(0);
    } else {
        log('\n❌ Some tests FAILED', 'red');
        process.exit(1);
    }
}

// Run tests
runTests().catch(error => {
    log(`\n❌ Test suite error: ${error.message}`, 'red');
    process.exit(1);
});
