# Database Standardization Progress Tracker

**Project:** Daakia Laravel Backend  
**Objective:** 100% Laravel-Compatible Table Naming  
**Started:** 2025-12-17  
**Status:** 🔄 In Progress

---

## Phase Overview

| Phase | Title | Status | Progress |
|-------|-------|--------|----------|
| 1 | Full Database Audit | ✅ COMPLETED | 100% |
| 2 | Naming Standardization Plan | ⏳ PENDING | 0% |
| 3 | Missing Tables Verification | ⏳ PENDING | 0% |
| 4 | Query & Insert Fix Plan | ⏳ PENDING | 0% |
| 5 | Chunk-Based Execution | ⏳ PENDING | 0% |

---

## Phase 1: Full Database Audit ✅

**Status:** ✅ COMPLETED  
**Completion Date:** 2025-12-17

### Deliverables:
- ✅ Complete table inventory (267 tables)
- ✅ Naming compliance analysis
- ✅ Missing tables verification
- ✅ Critical issues identified

### Key Findings:
- **Total Tables:** 267
- **Already Plural:** 67 tables (25%)
- **Need Pluralization:** 160 tables (60%)
- **Special Cases:** 40 tables (15%)
- **Missing Tables:** 0
- **Critical Issue:** `user` vs `users` table conflict (both empty)

**Document:** `PHASE_1_DATABASE_AUDIT.md`

---

## Phase 2: Naming Standardization Plan ✅

**Status:** ✅ COMPLETE  
**Completion Date:** 2025-12-18  
**Tables Renamed:** 190+  
**Users Migrated:** 112

### Objectives:
- [x] Create comprehensive rename mapping
- [x] Identify model `$table` overrides needed
- [x] Plan and execute migration files
- [x] Handle legacy User table data migration

### Deliverables:
- ✅ Complete rename mapping document (`PHASE_2_RENAME_MIGRATION_PLAN.md`)
- ✅ User table migration strategy (Data in `users` table)
- ✅ Chunk-based execution plan (Finished in 2 chunks)
- ✅ Migration files created and executed:
  - `2025_12_17_190857_extend_users_table_for_legacy_data.php`
  - `2025_12_17_201042_rename_core_tables_to_plural.php`
  - `2025_12_17_201604_rename_remaining_tables_to_plural.php`

---

## Phase 3: Missing Tables Verification ⏳

**Status:** ⏳ PENDING

### Objectives:
1. Cross-reference with legacy Core PHP project
2. Verify against old SQL dumps
3. Check code references for missing tables
4. Confirm no data loss

### Deliverables (Planned):
- [ ] Missing tables report
- [ ] Data import verification
- [ ] Legacy code cross-reference

---

## Phase 4: Query & Insert Fix Plan ⏳

**Status:** ⏳ PENDING

### Objectives:
1. Scan all queries for singular table names
2. Identify affected files
3. Create query fix mapping
4. Test query compatibility

### Deliverables (Planned):
- [ ] Query audit report
- [ ] File-by-file fix checklist
- [ ] Test coverage plan

---

## Phase 5: Chunk-Based Execution ⏳

**Status:** ⏳ PENDING

### Execution Strategy:
Work in small, trackable chunks to minimize risk and maintain visibility.

---

## Critical Issues Tracker

### 🚨 CRITICAL: User Table Conflict

**Issue:** Both `user` (singular) and `users` (plural) tables exist  
**Status:** 🔍 INVESTIGATING  
**Priority:** 🔴 CRITICAL

**Analysis:**
- `user` table: Legacy table with 38 columns (user_name, user_pass, user_type, etc.)
- `users` table: Laravel standard table with 7 columns (name, email, password, etc.)
- Both tables: Currently EMPTY (0 rows)

**Proposed Resolution:**
1. ✅ **Recommended:** Drop `user` table, use `users` as primary
2. **Alternative:** Rename `user` to `legacy_users` for reference
3. **Migration:** Create migration to add legacy columns to `users` table if needed

**Decision Required:** ⏳ AWAITING USER CONFIRMATION

---

## Table Renaming Checklist

### 🔴 Priority 1: Core Entities (Must Fix First)

| Current Name | New Name | Model Exists | Status | Notes |
|--------------|----------|--------------|--------|-------|
| `address` | `addresses` | ✅ Yes | ⏳ PENDING | Core entity |
| `carrier` | `carriers` | ✅ Yes | ⏳ PENDING | Core entity |
| `consignment` | `consignments` | ✅ Yes | ⏳ PENDING | Core entity |
| `country` | `countries` | ✅ Yes | ⏳ PENDING | Core entity |
| `parcel` | `parcels` | ✅ Yes | ⏳ PENDING | Core entity |
| `service` | `services` | ✅ Yes | ⏳ PENDING | Core entity |
| `user` | DROP/MERGE | ✅ Yes | 🔴 CRITICAL | Conflict with `users` |

### 🟡 Priority 2: Related Tables (40 tables)

<details>
<summary>Click to expand full list</summary>

| Current Name | New Name | Status |
|--------------|----------|--------|
| `agent_document` | `agent_documents` | ⏳ PENDING |
| `agent_log` | `agent_logs` | ⏳ PENDING |
| `agent_restricted_postcode` | `agent_restricted_postcodes` | ⏳ PENDING |
| `bag_scan_log` | `bag_scan_logs` | ⏳ PENDING |
| `bagging` | `baggings` | ⏳ PENDING |
| `bagging_manifest_mapping` | `bagging_manifest_mappings` | ⏳ PENDING |
| `bagging_services_mapping` | `bagging_services_mappings` | ⏳ PENDING |
| `box_info` | `box_infos` | ⏳ PENDING |
| `brazil_postcode` | `brazil_postcodes` | ⏳ PENDING |
| `brazil_state` | `brazil_states` | ⏳ PENDING |
| `cacesa_routine` | `cacesa_routines` | ⏳ PENDING |
| `carrier_agent` | `carrier_agents` | ⏳ PENDING |
| `carrier_data_file_log` | `carrier_data_file_logs` | ⏳ PENDING |
| `carrier_document` | `carrier_documents` | ⏳ PENDING |
| `carrier_log` | `carrier_logs` | ⏳ PENDING |
| `carrier_zones_postcode` | `carrier_zones_postcodes` | ⏳ PENDING |
| `carton_pallet_number` | `carton_pallet_numbers` | ⏳ PENDING |
| `consignment_bagging_mapping` | `consignment_bagging_mappings` | ⏳ PENDING |
| `consignment_billing_hold` | `consignment_billing_holds` | ⏳ PENDING |
| `consignment_billing_hold_log` | `consignment_billing_hold_logs` | ⏳ PENDING |
| `consignment_charges_log` | `consignment_charges_logs` | ⏳ PENDING |
| `consignment_collection` | `consignment_collections` | ⏳ PENDING |
| `consignment_dropoff_mapping` | `consignment_dropoff_mappings` | ⏳ PENDING |
| `consignment_hold` | `consignment_holds` | ⏳ PENDING |
| `consignment_hold_log` | `consignment_hold_logs` | ⏳ PENDING |
| `consignment_hscode` | `consignment_hscodes` | ⏳ PENDING |
| `consignment_log` | `consignment_logs` | ⏳ PENDING |
| `consignment_pod` | `consignment_pods` | ⏳ PENDING |
| `consignment_relabel` | `consignment_relabels` | ⏳ PENDING |
| `consignment_status_log` | `consignment_status_logs` | ⏳ PENDING |
| `correos_brazil_datafile` | `correos_brazil_datafiles` | ⏳ PENDING |
| `cpost_manifest` | `cpost_manifests` | ⏳ PENDING |
| `credit_note` | `credit_notes` | ⏳ PENDING |
| `cs_log` | `cs_logs` | ⏳ PENDING |
| `csv_import_template` | `csv_import_templates` | ⏳ PENDING |
| `csv_tracking_template` | `csv_tracking_templates` | ⏳ PENDING |
| ... (120+ more tables) | ... | ⏳ PENDING |

</details>

### ⚠️ Priority 3: Special Cases (Require Review)

| Table Name | Issue | Recommendation | Status |
|------------|-------|----------------|--------|
| `cache` | Uncountable | Keep as-is | ⏳ REVIEW |
| `tracking_data` | Mass noun | Keep or rename to `tracking_records` | ⏳ REVIEW |
| `api_data` | Mass noun | Consider `api_requests` | ⏳ REVIEW |
| `agent_data` | Mass noun | Keep as-is | ⏳ REVIEW |
| `pricing_bulk_data_*` | Timestamped | Review for deletion | ⏳ REVIEW |

---

## Model Creation Tracker

**Current Models:** 16 / 267 tables (6%)  
**Missing Models:** 251

### Existing Models:
- ✅ Address.php
- ✅ Agent.php
- ✅ Carrier.php
- ✅ CarrierServiceCustomizeRules.php
- ✅ CarrierServiceDefaultRules.php
- ✅ Consignment.php
- ✅ ConsignmentCharge.php
- ✅ Country.php
- ✅ CustomizedServicesRouting.php
- ✅ Parcel.php
- ✅ Permission.php
- ✅ Role.php
- ✅ Service.php
- ✅ TrackingData.php
- ✅ User.php
- ✅ UserServicesRouting.php

### Models Requiring `$table` Override (After Rename):
These models will need `protected $table = 'new_name';` if we don't rename tables:
- [ ] Address → `protected $table = 'addresses';`
- [ ] Carrier → `protected $table = 'carriers';`
- [ ] Consignment → `protected $table = 'consignments';`
- [ ] Country → `protected $table = 'countries';`
- [ ] Parcel → `protected $table = 'parcels';`
- [ ] Service → `protected $table = 'services';`

---

## Risk Assessment

| Risk | Severity | Mitigation |
|------|----------|------------|
| Data loss during rename | 🔴 HIGH | Full backup before any changes |
| Foreign key constraint failures | 🟡 MEDIUM | Map all FK dependencies first |
| Query breakage | 🟡 MEDIUM | Comprehensive query audit |
| Downtime during migration | 🟢 LOW | Chunk-based execution |
| Model-table mismatch | 🟡 MEDIUM | Update all models with renames |

---

## Next Actions

### Immediate (Awaiting Confirmation):
1. ⏳ **User Decision:** Approve dropping `user` table in favor of `users`
2. ⏳ **Strategy Decision:** Rename tables OR use model `$table` overrides?
3. ⏳ **Priority Decision:** Which tables to fix first?

### After Approval:
1. Create Phase 2 detailed plan
2. Generate migration files for renames
3. Update existing models
4. Begin chunk-based execution

---

## Success Criteria

- [ ] All tables follow Laravel plural naming convention
- [ ] All models correctly reference their tables
- [ ] All queries use correct table names
- [ ] Zero data loss
- [ ] All foreign keys intact
- [ ] All tests passing
- [ ] Complete documentation

---

**Last Updated:** 2025-12-17  
**Next Review:** After user confirmation
