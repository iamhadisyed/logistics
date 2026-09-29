# Daakia Logistics — Module Completion Tracker

This is the authoritative, evidence-based list of every module in the legacy
Core PHP system (`dakia_backend01/logistic/main/`, 452 files) and its real
status in the Laravel + Next.js revamp. No module is marked **Done** until:

1. **Built** — backend (real legacy-aligned table, registered route, controller
   logic that matches legacy business rules) AND frontend (real page, calling
   the real API, no mock/fake data).
2. **Tested** — automated test(s) for the module in isolation.
3. **Integration-tested** — verified working together with the modules it
   actually depends on (e.g. Consignment can't be "done" independent of
   Tariffs/Pricing, because pricing is calculated at booking time).

Status values: `Not Started` / `In Progress` / `Built (untested)` /
`Tested (isolated)` / `Done (integration-tested)`.

Last updated: 2026-09-29. Updated after every module milestone — not before.

## ✅ Decided (2026-09-29): booking schema is `Shipment`
User decision: **`Shipment` (the new `shipments`/`shipment_parcels`/
`shipment_items` tables) is the real foundation going forward — NOT the
legacy `Consignment`/`consignments` table.** This unblocks modules 8, 11, 12,
13 below (no longer 🔒). The legacy `Consignment` model/routes and legacy
`consignment_*` tables are considered deprecated for new development; the old
Core PHP files remain as read-only reference for business-rule logic only.

---

| # | Module | Legacy files (approx.) | Status | Notes |
|---|---|---|---|---|
| 1 | Auth & Login (incl. carrier-specific logins) | 12 | **Built (untested)** | Sanctum login/logout work. No automated tests found. |
| 2 | Admin Users | 13 | Not Started | `UserController` exists but not confirmed wired to a real admin-user CRUD screen. |
| 3 | Customer/Business Accounts (`user_accounts`, hierarchy) | 14 | Not Started | `AccountController` (RBAC) exists, but hierarchical account view/signup/summary reports not built. |
| 4 | Groups, Permissions & Roles | 4 | Not Started | Backend RBAC routes exist; sidebar permission tree logic real but frontend wiring unconfirmed. Not tested. |
| 5 | Agents & Sales | 11 | Not Started | `Agent` model exists, no controller/routes. |
| 6 | Carriers | 7 | **Built (untested)** | Real CRUD, real `carrier` table, real frontend page. No automated tests. |
| 7 | Services & Routing | 18 | **Built (untested)** | Real CRUD + availability + routing endpoints exist. Frontend coverage partial. No tests. |
| 8 | Tariffs & Pricing (incl. cost/sales tariffs, calculator) | 38 | **In Progress (correction)** | `app/Services/PricingEngine.php` DOES exist (I was wrong earlier — I hadn't listed `app/Services/` before writing the first tracker version). Surcharge/insurance math is real; `getBaseRate()` and `getRemoteAreaCharge()` are still TODO stubs returning hardcoded values. Built against deprecated `Consignment`, not yet ported to `Shipment`. |
| 9 | Remote Area Charges | 6 | Not Started | — |
| 10 | Ratebands / Extra Charges | 3 | Not Started | — |
| 11 | Consignments / Bookings | 8 | **Deprecated** | Legacy `Consignment` model/routes are NOT the path forward (user decision 2026-09-29). `Shipment` is now the authoritative booking entity — see module below. |
| 11b | Shipments / Bookings (`Shipment` model — authoritative) | — | **Built (untested)** | Create/list/detail work end-to-end. Two-button "Save Booking / Generate Label" spec now implemented in `ShipmentCreateForm.tsx` (2026-09-29) — TypeScript-clean, not yet runtime-tested (see blocker below). |
| 12 | Parcels & Items | 7 | Partial (wrong schema label — now correct per decision) | Exist nested under `Shipment`. Needs test coverage. |
| 13 | Label Generation | 19 | **Fake, but has a real skeleton** | `app/Services/Labels/LabelGeneratorInterface.php` DOES exist (correction — same miss as PricingEngine). It's an interface only — zero classes implement it yet. `generateLabel()` actually called by `ShipmentController` bypasses this interface entirely and just flips a boolean. Next real build target: implement a concrete label generator against `Shipment`. |
| 14 | Bagging | 8 | Not Started | — |
| 15 | Flights & MAWB (air freight) | 12 | Not Started | — |
| 16 | Pallets | 8 | Not Started | — |
| 17 | Scanning | 9 | Not Started | — |
| 18 | Manifest | 7 | Not Started | — |
| 19 | Tracking | 9 | Not Started | `TrackingData` model exists, no controller/route. |
| 20 | Vehicles & Drivers (fleet) | 4 | **Fake** | `views/apps/logistics/fleet` looks like this module but is 100% MUI template demo data (Mapbox showcase), not connected to any real table. |
| 21 | Warehouses, Racks & Inventory | 16 | Not Started | — |
| 22 | Pre-Alerts | 10 | Not Started | — |
| 23 | Invoicing & Billing | 18 | **Fake** | `InvoiceController` returns hardcoded "John Doe" test JSON. Not registered in any route. Dead code. |
| 24 | Payments & Finance (incl. PayPal, quotations, currency, GP reports) | 24 | Not Started | — |
| 25 | Marketplace Integrations (Amazon, eBay, WooCommerce, Vinculum, dpd, Yodel, Asendia, etc.) | ~45 | Not Started | — |
| 26 | Products / Catalog | 9 | **Fake** | `EcommerceController` returns hardcoded fake customer/product JSON. Not registered. Dead code — likely irrelevant to a logistics platform anyway (confirm with user whether this module is even in scope). |
| 27 | Reporting (general/misc, ~20 report types) | 20 | Not Started | — |
| 28 | Countries / Postcodes / Geography | 10 | **Built (untested)** | Real CRUD for Countries. Postcodes/zones not built. |
| 29 | CMS / Static Pages / Legal / Help / Language management | 35 | Not Started | Lowest priority — content pages, not core business logic. |
| — | System/dev/test cruft in legacy (401/403/404, test_*.php, tempDelFile, etc.) | ~35 | N/A | Not real modules — skip, don't port. |

### Frontend template noise (not modules — flag for deletion, not development)
- `dakia_app01/src/views/apps/ecommerce/*` (67 files), `invoice`, `academy`,
  `chat`, `email`, `kanban`, `calendar` — unmodified MUI admin template demo
  screens, unrelated to logistics. Recommend deleting once confirmed with you,
  so they stop inflating the apparent completion %.

---

## Current real completion: **~10%** (3 of 29 modules built-but-untested; 0 tested; 0 integration-tested)

## ✅ Environment blocker resolved (2026-09-29): backend now runs and tests in this session
Migrations ran successfully against a local SQLite DB once retried (the earlier
block was a one-time false positive). `APP_KEY` was also missing (an earlier
command that should have generated it never ran) — fixed. Real baseline test
run: **44 failed / 12 passed** out of 56 existing tests. `phpunit.xml` is still
hardcoded to a MySQL DB that doesn't exist here — tests are run with an inline
`DB_CONNECTION=sqlite` override instead, matching your app's actual default.

### Real bugs found and fixed by actually running tests
- **`carriers` table was missing `is_pallet`** — a genuine legacy column
  (confirmed present in `logistic/main/carrier.php` legacy UI) that the
  original schema-restore migration simply left out, even though the
  `Carrier` model, `CarrierController` validation, and `CarrierFactory` all
  already expected it. Added via a new migration
  (`2026_09_29_200118_add_is_pallet_to_carriers_table.php`), additive only —
  did not touch any existing legacy column. **Result: 44 failed → 37 failed,
  12 passed → 19 passed.**

### Real issues found, NOT yet fixed (need more care before touching schema)
- **`services` table may be missing `max_weight` / `tracking_flag`** —
  `ServiceFactory`/`Service` model expect them, migration doesn't have them.
  Unlike `is_pallet`, these are ambiguous: `max_weight` appears in legacy code
  only as a computed getter (`getMaxWeight()`), not confirmed as a literal
  legacy DB column, and the real legacy table already has
  `max_length`/`max_width`/`max_height`/`max_volumetric_weight`. Adding a
  column here risks inventing a field that never existed in your real
  database — did not do it without checking the real legacy data first.
- **Stale test assertions use singular table names** (`carrier`, `country`)
  that don't match the real plural tables (`carriers`, `countries`). This is
  a bug in the *test files themselves*, not the schema — lower priority,
  doesn't affect production code.
- Consignment-module test failures (`ConsignmentControllerTest`,
  `ConsignmentTest`, part of `PricingEngineTest`) are expected/low-priority —
  they test the now-deprecated `Consignment` path.
- `AuthenticationTest` — 2 failures (session auth assertion not behaving as
  expected in the test environment) — not yet root-caused.
- **Zero test coverage exists for `Shipment`/`ShipmentController`** — the
  actually-wired booking module has no automated tests at all yet.

## 🔒 Blocking decision needed before modules 8, 11, 12, 13 can start

Everything above marked 🔒 depends on one call: **is `Consignment` (legacy
`consignments` table) or `Shipment` (new `shipments` table) the real path
forward?** Tariffs/Pricing, Parcels/Items, and Label Generation are all
sub-features of *booking a consignment* — building them on the wrong table
means redoing them later. I'm asking this before writing more code so I
don't repeat the earlier mistake of building on an unverified assumption.
