# Shipment API Test Results

## Test Script Created ✅

**Location:** `scripts/test-shipments.js`

**Command to run:**
```bash
npm run test:shipments
```

Or directly:
```bash
node scripts/test-shipments.js
```

## Test Coverage

The automated test covers:

1. **Authentication** ✅
   - Logs in with test credentials
   - Retrieves auth token
   - Validates token format

2. **Create Shipment** ✅
   - Creates a test shipment with:
     - Consignment data (customer, service type, reference, addresses)
     - Parcels (weight, dimensions)
     - Items (description, quantity, weight, value)
   - Validates response structure
   - Checks for required fields

3. **List Shipments** ✅
   - Retrieves list of all shipments
   - Validates response structure
   - Checks if created shipment appears in list
   - Validates pagination handling

4. **Get Single Shipment** ✅
   - Retrieves the created shipment by ID
   - Validates full shipment structure
   - Checks parcels and items data

## Test Data

The test uses realistic sample data:
- Customer ID: 148 (master account)
- Service Type: EXPRESS
- Reference: TEST-{timestamp}
- Complete receiver and sender addresses
- Parcels with items

## Environment Variables

Set these if needed:
- `TEST_EMAIL` - Default: admin@example.com
- `TEST_PASSWORD` - Default: password
- `NEXT_PUBLIC_API_URL` - Default: http://localhost:8000/api

## Expected Output

When tests pass:
```
============================================================
🧪 Shipment API Test Suite
============================================================

🔐 Step 1: Authenticating...
✅ Authentication successful

📦 Step 2: Testing Shipment Creation...
✅ Shipment created successfully
Shipment ID: 123
Reference: TEST-1234567890
✅ Response structure valid

📋 Step 3: Testing Shipment Listing...
✅ Retrieved 10 shipments
✅ Created shipment found in list
✅ Shipment list structure valid

🔍 Step 4: Testing Get Single Shipment...
✅ Shipment retrieved successfully
Reference: TEST-1234567890
Status: booked
Parcels: 1
  Parcel 1: 2.5kg, Items: 2

============================================================
📊 Test Results Summary
============================================================
Authentication: ✅ PASS
Create Shipment: ✅ PASS
List Shipments: ✅ PASS
Get Shipment: ✅ PASS
============================================================

✅ All tests PASSED
```

## Troubleshooting

If tests fail:

1. **Authentication fails:**
   - Check Laravel backend is running on port 8000
   - Verify test credentials in database
   - Check API route: `POST /api/login`

2. **Shipment creation fails:**
   - Check validation errors in output
   - Verify database tables exist: `shipments`, `shipment_parcels`, `shipment_items`, `shipment_history`
   - Check Laravel logs: `storage/logs/laravel.log`
   - Ensure customer_id 148 exists

3. **Listing fails:**
   - Check API route: `GET /api/shipments`
   - Verify authentication token is valid
   - Check database connection

## Notes

- The test script uses Node's built-in `fetch` (Node 18+) or falls back to `axios`
- All HTTP requests include proper authentication headers
- Error messages are detailed and include validation errors
- Test results are logged with timestamps

## Integration

This test can be integrated into CI/CD pipelines:
```bash
npm run test:shipments
```

Exit code 0 = all tests passed
Exit code 1 = one or more tests failed
