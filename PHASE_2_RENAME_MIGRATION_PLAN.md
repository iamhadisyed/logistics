# Phase 2: Table Renaming & Data Migration Strategy

**Status:** 🔄 IN PROGRESS  
**Strategy:** Rename to Plural + Run Legacy Inserts + Drop Old Tables  
**Date:** 2025-12-18

---

## ✅ Confirmed Strategy

Based on user input, we will:

1. ✅ **Use `users` table** (Laravel standard, plural)
2. ✅ **Run INSERT queries** from legacy seeder (targets old `user` table)
3. ✅ **Rename ALL tables to plural** (Laravel convention)
4. ✅ **Remove old singular tables** after successful migration

---

## 🎯 Execution Plan

### Step 1: Handle User Table Migration 🔴 CRITICAL

**Problem:** Legacy seeder has INSERT into `user` (singular), but we want to use `users` (plural)

**Solution:**
1. Modify `LegacyDataSeeder.php` to INSERT into `users` instead of `user`
2. Map legacy `user` columns to `users` table structure
3. Run the seeder
4. Drop the empty `user` table

**Column Mapping Required:**

| Legacy `user` Column | `users` Column | Action |
|----------------------|----------------|--------|
| `id` | `id` | Direct map |
| `user_name` | `name` or custom | Map/Transform |
| `user_pass` | `password` | Direct map |
| `email` | `email` | Direct map |
| `first_name` + `last_name` | `name` | Concatenate |
| `user_type` | Add column | Extend `users` table |
| `active_flag` | Add column | Extend `users` table |
| ... (35 more columns) | ... | Extend `users` table |

**Decision Point:** 
- **Option A:** Extend `users` table with all legacy columns
- **Option B:** Create separate `user_profiles` table for extra data
- **Recommended:** Option A (simpler migration)

---

### Step 2: Create Table Rename Migration

**Approach:** Create a single migration file that renames all 193 singular tables to plural

**Priority Order:**
1. 🔴 **HIGH** - Core entities (6 tables)
2. 🟡 **MEDIUM** - Related tables (187 tables)

---

## 📋 Detailed Rename Checklist

### 🔴 Phase 2A: Core Entities (Priority 1)

| # | Old Name | New Name | Foreign Keys | Status |
|---|----------|----------|--------------|--------|
| 1 | `address` | `addresses` | Referenced by `consignment` | ⏳ PENDING |
| 2 | `carrier` | `carriers` | Referenced by multiple tables | ⏳ PENDING |
| 3 | `consignment` | `consignments` | References multiple tables | ⏳ PENDING |
| 4 | `country` | `countries` | Referenced by multiple tables | ⏳ PENDING |
| 5 | `parcel` | `parcels` | Referenced by multiple tables | ⏳ PENDING |
| 6 | `service` | `services` | Already plural! | ✅ SKIP |

---

### 🟡 Phase 2B: Supporting Tables (Chunk 1 - Agent & Log Tables)

| # | Old Name | New Name | Dependencies | Status |
|---|----------|----------|--------------|--------|
| 7 | `agent_document` | `agent_documents` | None | ⏳ PENDING |
| 8 | `agent_log` | `agent_logs` | None | ⏳ PENDING |
| 9 | `agent_restricted_postcode` | `agent_restricted_postcodes` | None | ⏳ PENDING |
| 10 | `bag_scan_log` | `bag_scan_logs` | None | ⏳ PENDING |
| 11 | `carrier_data_file_log` | `carrier_data_file_logs` | FK: `carrier` | ⏳ PENDING |
| 12 | `carrier_document` | `carrier_documents` | FK: `carrier` | ⏳ PENDING |
| 13 | `carrier_log` | `carrier_logs` | FK: `carrier` | ⏳ PENDING |

---

### 🟡 Phase 2C: Bagging & Mapping Tables (Chunk 2)

| # | Old Name | New Name | Dependencies | Status |
|---|----------|----------|--------------|--------|
| 14 | `bagging` | `baggings` | None | ⏳ PENDING |
| 15 | `bagging_manifest_mapping` | `bagging_manifest_mappings` | Pivot table | ⏳ PENDING |
| 16 | `bagging_services_mapping` | `bagging_services_mappings` | Pivot table | ⏳ PENDING |
| 17 | `consignment_bagging_mapping` | `consignment_bagging_mappings` | FK: `consignment` | ⏳ PENDING |
| 18 | `parcel_bagging_mapping` | `parcel_bagging_mappings` | FK: `parcel` | ⏳ PENDING |

---

### 🟡 Phase 2D: Consignment Related Tables (Chunk 3)

| # | Old Name | New Name | Dependencies | Status |
|---|----------|----------|--------------|--------|
| 19 | `consignment_billing_hold` | `consignment_billing_holds` | FK: `consignment` | ⏳ PENDING |
| 20 | `consignment_billing_hold_log` | `consignment_billing_hold_logs` | FK: `consignment` | ⏳ PENDING |
| 21 | `consignment_charges_log` | `consignment_charges_logs` | FK: `consignment` | ⏳ PENDING |
| 22 | `consignment_collection` | `consignment_collections` | FK: `consignment` | ⏳ PENDING |
| 23 | `consignment_dropoff_mapping` | `consignment_dropoff_mappings` | FK: `consignment` | ⏳ PENDING |
| 24 | `consignment_hold` | `consignment_holds` | FK: `consignment` | ⏳ PENDING |
| 25 | `consignment_hold_log` | `consignment_hold_logs` | FK: `consignment` | ⏳ PENDING |
| 26 | `consignment_hscode` | `consignment_hscodes` | FK: `consignment` | ⏳ PENDING |
| 27 | `consignment_log` | `consignment_logs` | FK: `consignment` | ⏳ PENDING |
| 28 | `consignment_pod` | `consignment_pods` | FK: `consignment` | ⏳ PENDING |
| 29 | `consignment_relabel` | `consignment_relabels` | FK: `consignment` | ⏳ PENDING |
| 30 | `consignment_status_log` | `consignment_status_logs` | FK: `consignment` | ⏳ PENDING |

---

### 🟡 Phase 2E: Remaining Tables (Chunks 4-20)

*Full list of remaining 163 tables to be renamed in subsequent chunks...*

---

## 🔧 Implementation Steps

### Step 1: Extend `users` Table Schema

```php
// Migration: extend_users_table_for_legacy_data.php
Schema::table('users', function (Blueprint $table) {
    // Legacy user table columns
    $table->enum('user_type', ['corporate', 'client', 'admin', 'driver'])->default('client')->after('password');
    $table->boolean('active_flag')->default(false)->after('user_type');
    $table->string('first_name', 100)->nullable()->after('name');
    $table->string('last_name', 255)->nullable()->after('first_name');
    $table->string('phone', 20)->nullable();
    $table->unsignedInteger('country_id')->nullable();
    $table->string('api_key', 100)->nullable();
    $table->string('api_secret', 100)->nullable();
    $table->datetime('api_date')->nullable();
    $table->string('profile_image')->nullable();
    $table->boolean('is_employee')->default(false);
    $table->unsignedInteger('warehouse_id')->nullable();
    $table->enum('dashboard', ['corporate', 'operation', 'customer_service', 'account', 'driver'])->default('corporate');
    $table->unsignedInteger('invalid_login_count')->nullable();
    $table->unsignedInteger('user_account_id')->nullable();
    $table->datetime('last_login_date')->nullable();
    $table->unsignedInteger('added_by')->nullable();
    $table->datetime('added_date')->nullable();
    $table->unsignedInteger('updated_by')->nullable();
    $table->datetime('updated_date')->nullable();
    $table->boolean('is_deleted')->default(false);
    $table->boolean('archive_server')->default(false);
    $table->boolean('carrier_setup_agreement')->default(false);
    $table->enum('receive_email', ['y', 'n'])->default('n');
    $table->date('tc_agreed_date')->nullable();
    $table->enum('is_tc_agreed', ['y', 'n', 'i'])->default('n');
    $table->string('address', 255)->nullable();
    $table->string('address_2', 50)->nullable();
    $table->string('address_3', 50)->nullable();
    $table->string('city', 50)->nullable();
    $table->string('postcode', 15)->nullable();
    $table->string('state', 50)->nullable();
    $table->string('commission_break_event_amount', 50)->default('');
    $table->boolean('is_sale_pot_eligible')->default(false);
});
```

### Step 2: Update Legacy Data Seeder

```php
// Modify LegacyDataSeeder.php line 44019
// Change: INSERT INTO `user` 
// To: INSERT INTO `users`

// Also update column names:
// user_name → name (or keep as user_name if we added it)
// user_pass → password
// api_secert → api_secret (fix typo)
```

### Step 3: Create Rename Migration

```php
// Migration: rename_tables_to_plural.php
public function up(): void
{
    // Core entities first
    Schema::rename('address', 'addresses');
    Schema::rename('carrier', 'carriers');
    Schema::rename('consignment', 'consignments');
    Schema::rename('country', 'countries');
    Schema::rename('parcel', 'parcels');
    
    // Then all other tables...
    Schema::rename('agent_document', 'agent_documents');
    // ... (continue for all 193 tables)
}

public function down(): void
{
    // Reverse all renames
    Schema::rename('addresses', 'address');
    // ... etc
}
```

### Step 4: Update Existing Models

Update all 16 existing models to remove `$table` property (since tables will now match Laravel conventions):

```php
// Before:
class Address extends Model
{
    protected $table = 'address'; // Remove this
}

// After:
class Address extends Model
{
    // Laravel will auto-detect 'addresses' table
}
```

### Step 5: Drop Legacy `user` Table

```php
// Migration: drop_legacy_user_table.php
public function up(): void
{
    Schema::dropIfExists('user');
}
```

---

## ⚠️ Risk Mitigation

### 1. Backup Strategy
```bash
# Before ANY changes
mysqldump -u root -p daakia > daakia_backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Foreign Key Handling
- Disable foreign key checks during rename
- Re-enable after completion
- Verify all FK constraints still work

### 3. Testing Checklist
- [ ] All models can query their tables
- [ ] All relationships still work
- [ ] All foreign keys intact
- [ ] Legacy data seeder runs successfully
- [ ] No broken queries in codebase

---

## 📊 Progress Tracking

| Phase | Tables | Status | Completion |
|-------|--------|--------|------------|
| 2A - Core Entities | 6 | ⏳ PENDING | 0% |
| 2B - Agent & Logs | 7 | ⏳ PENDING | 0% |
| 2C - Bagging | 5 | ⏳ PENDING | 0% |
| 2D - Consignment | 12 | ⏳ PENDING | 0% |
| 2E - Remaining | 163 | ⏳ PENDING | 0% |
| **TOTAL** | **193** | **⏳ PENDING** | **0%** |

---

## 🚀 Next Actions

1. ⏳ **Awaiting Confirmation:**
   - Approve extending `users` table with legacy columns
   - Approve chunk-based execution plan
   - Confirm backup strategy

2. **After Approval:**
   - Create migration to extend `users` table
   - Update `LegacyDataSeeder.php` to target `users` table
   - Run seeder to import legacy user data
   - Create table rename migration (Phase 2A first)
   - Update models
   - Test thoroughly
   - Proceed with remaining chunks

---

**Last Updated:** 2025-12-18  
**Awaiting:** User confirmation to proceed
