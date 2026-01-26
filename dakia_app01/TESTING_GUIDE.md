# Duplicate Key Testing Guide

## Automated Test Script

A comprehensive test script has been created to automatically detect duplicate key issues in React components.

### Running the Tests

```bash
npm run test:duplicate-keys
```

Or directly:
```bash
node scripts/check-duplicate-keys.js
```

### What the Test Checks

1. **Duplicate Key Detection**: Scans all `.map()` calls for keys that might conflict
2. **Deduplication Logic**: Verifies that data is deduplicated before rendering
3. **Key Uniqueness**: Ensures keys use unique prefixes and index fallbacks
4. **Backend Validation**: Checks that backend controllers use `unique()` or `distinct()`

### Test Results

- ✅ **PASSED**: Component has proper deduplication and unique keys
- ⚠️ **WARNING**: Component has keys but no deduplication detected
- ❌ **ERROR**: Component has duplicate key issues

## Manual Testing Checklist

Before declaring a fix complete, verify:

1. ✅ All `.map()` calls use unique key prefixes (e.g., `user-${id}`, `account-${id}`)
2. ✅ Keys include index fallback when possible (e.g., `${id}-${index}`)
3. ✅ Data is deduplicated using Map before setting state
4. ✅ Backend queries use `unique()` or `distinct()` when appropriate
5. ✅ No console errors about duplicate keys

## Fixed Components

All components have been updated with:
- Map-based deduplication logic
- Unique key prefixes
- Index fallbacks for extra safety

### Components Fixed:
- ✅ Users Page
- ✅ Accounts Page  
- ✅ Account Users Page
- ✅ User Services Page
- ✅ Services Routing Page
- ✅ Account Services Page
- ✅ Services Page
- ✅ User Permissions Page
- ✅ Consignments Pages

## Prevention

To prevent future duplicate key issues:

1. **Always use unique prefixes** for keys (e.g., `component-type-${id}`)
2. **Add index fallback** when rendering lists (e.g., `${id}-${index}`)
3. **Deduplicate data** using Map before setting state
4. **Run tests** before committing: `npm run test:duplicate-keys`
