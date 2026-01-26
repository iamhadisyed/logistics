# Shipment API Test Instructions

## Quick Test

The test script has been created at `scripts/test-shipments.js`.

### To Run Tests:

```bash
npm run test:shipments
```

Or directly:
```bash
node scripts/test-shipments.js
```

### Environment Variables (Optional):

Set these if your test credentials differ:
- `TEST_EMAIL` - Default: admin@example.com
- `TEST_PASSWORD` - Default: password
- `NEXT_PUBLIC_API_URL` - Default: http://localhost:8000/api

## What Gets Tested:

1. ✅ **Authentication** - Logs in and gets auth token
2. ✅ **Create Shipment** - Creates a test shipment with parcels and items
3. ✅ **List Shipments** - Retrieves list of shipments
4. ✅ **Get Shipment** - Retrieves the created shipment by ID

## Expected Results:

- All tests should pass if the API is working correctly
- Any failures will show detailed error messages
- Validation errors will be displayed field by field

## Troubleshooting:

If tests fail:
1. Check that Laravel backend is running on port 8000
2. Verify test credentials are correct
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure database tables exist: `shipments`, `shipment_parcels`, `shipment_items`, `shipment_history`
