# Debugging 500 Error on Shipment Creation

## Enhanced Error Handling

The code now includes comprehensive error handling that will:

1. **Log detailed errors** to `storage/logs/laravel.log` with:
   - Error message
   - File and line number
   - Full stack trace
   - Request data

2. **Return error details in response** (if `APP_DEBUG=true`):
   - Error message
   - File and line number
   - Stack trace

## How to Debug

### Step 1: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

Look for entries like:
```
Shipment creation error: [error message]
File: [file path]
Line: [line number]
```

### Step 2: Check Browser Network Tab
- Open browser DevTools → Network tab
- Try creating a shipment
- Look at the response for the 500 error
- If `APP_DEBUG=true`, you'll see detailed error info

### Step 3: Common Issues to Check

1. **Database Schema Mismatch**
   - Check if all columns exist in `shipments` table
   - Verify column types match model expectations
   - Run: `php artisan migrate:status`

2. **Missing Required Fields**
   - Check validation rules in `StoreShipmentRequest`
   - Ensure all required fields are sent from frontend

3. **Relationship Issues**
   - Verify `shipment_parcels` table has `shipment_id` column
   - Verify `shipment_items` table has `parcel_id` column
   - Check foreign key constraints

4. **Resource Serialization**
   - Check if all resource fields exist on model
   - Verify relationships are properly loaded

## Quick Test

Try creating a shipment and check:
1. Laravel logs for the exact error
2. Browser console for error details
3. Network tab response for error message

## Next Steps

Once you have the error message from logs:
1. Share the error message
2. Check the file and line number mentioned
3. Verify the database schema matches expectations
