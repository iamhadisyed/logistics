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

## 🎯 Priority order (set 2026-09-29, executing top-down without re-asking)

Rationale: get the core revenue path (book → price → label → hand off) fully
real first, since every other module either feeds it or depends on it. Then
operational necessities, then money/admin, then integrations, then
low-stakes content. Modules already Tested/Done are listed for sequencing
context even though no more work is queued on them right now.

**Tier 1 — Core booking & revenue path (build this first, in this order)**
1. Shipments/Bookings + Parcels/Items — Tested (isolated). ✅ baseline done.
2. Label Generation — real PDF, tested. ✅ baseline done (barcode/carrier-API
   depth deferred, see module 13 notes).
3. **Tariffs & Pricing — IN PROGRESS, current focus.** Nothing downstream
   (accurate invoices, sales reporting, agent commissions) is trustworthy
   until real prices are calculated instead of `PricingEngine`'s hardcoded
   `10.00`/`5.00` stubs.
4. Carriers, Services & Routing — Built (untested). The `services.max_weight`
   schema question is resolved now (see below) — next real blocker is a
   403 authorization bug affecting every `ServiceControllerTest` test.

**Tier 2 — Operational necessities (can't run the business day-to-day without these)**
5. Manifest (handoff to carriers)
6. Tracking (customer-facing status)
7. Bagging
8. Warehouses, Racks & Inventory
9. Pre-Alerts
10. Flights & MAWB (only if air freight is actually part of current ops —
    confirm scope with user before building, it's a large module)

**Tier 3 — Money & accounts**
11. Invoicing & Billing (currently fake — hardcoded JSON, unregistered route)
12. Payments & Finance
13. Customer/Business Accounts (hierarchy, `user_accounts`)
14. Remote Area Charges, Ratebands (sub-parts of pricing, can ride along with Tier 1.3)

**Tier 4 — Admin & support**
15. Admin Users
16. Groups, Permissions & Roles + dynamic sidebar (backend real, frontend
    wiring unconfirmed — see section 4)
17. Agents & Sales
18. Reporting (~20 report types)

**Tier 5 — Integrations (defer until Tiers 1-4 are solid)**
19. Marketplace Integrations (Amazon, eBay, WooCommerce, carrier APIs)
20. Scanning
21. Pallets

**Tier 6 — Low priority / content**
22. Countries/Postcodes/Geography (Countries done; postcodes/zones not built)
23. CMS/Static Pages/Legal/Help/Language management

**RESOLVED 2026-10-06**: frontend template noise (Ecommerce, Invoice demo,
Academy, Chat, Email, Kanban, Calendar, the fake Mapbox "Logistics"
fleet/dashboard, 5 fake dashboards, charts/forms/react-table showcases,
widget/dialog/wizard examples, FAQ/pricing demo, fake user-profile,
front-pages marketing site, duplicate auth v1/v2 variants) — deleted, along
with 4 unregistered fake backend controllers (Academy/Ecommerce/Invoice/
Pages) and every dead nav link. `package.json` renamed to
`daakia-logistics-frontend`. See commit `983a2acb`.

**Held pending sign-off** (Users/Permissions/Roles — real features
currently on template mock data, not demo junk to delete): all three use
the same static `getUserData()`/`getPermissionsData()` mock. Plan agreed:
keep the shells, strip the fake sub-tabs (Users' overview/billing-plans/
connections), wire real calls to `UserController`/`AccountController`/
`SidebarController` — endpoint shapes to be confirmed before executing.

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
| 8 | Tariffs & Pricing (incl. cost/sales tariffs, calculator) | 38 | **Tested (isolated) + integration-tested at booking time** | `PricingEngine` rewritten against `Shipment`. `getBaseRate()` is now a REAL lookup: `carrier_zones_countries` resolves country→zone, `tariffs`+`tariffs_details` resolve the weight-banded rate — no hardcoded fallback, throws `TariffNotConfiguredException` when nothing's configured (never fakes a price). Fuel surcharge (real, from `service.fuel_surcharge`) applies on top. Wired into `ShipmentController::store()` as best-effort — booking succeeds even with no tariff configured (logs a warning, `total_price` stays null), doesn't block the customer. New `shipment_charges` table/model records the breakdown (the old `ConsignmentCharge` model's columns didn't even match the real `consignment_charges` legacy table — a third schema-mismatch bug found this session, not fixed since Consignment is deprecated anyway). 11/11 `ShipmentControllerTest` + 6/6 `PricingEngineTest` passing, including a real end-to-end "seed a tariff → book → get a real calculated price" test. **Deferred, documented not faked**: remote-area surcharge and insurance (Shipment has no `is_insured`/`value` fields yet — different gap from pricing lookup itself), postcode-level zone overrides (country-level only for now). |
| 9 | Remote Area Charges | 6 | Not Started | — |
| 10 | Ratebands / Extra Charges | 3 | Not Started | — |
| 11 | Consignments / Bookings | 8 | **Deprecated** | Legacy `Consignment` model/routes are NOT the path forward (user decision 2026-09-29). `Shipment` is now the authoritative booking entity — see module below. |
| 11b | Shipments / Bookings (`Shipment` model — authoritative) | — | **Tested (isolated)** | 9/9 real backend tests passing: create, validate, list, search, view, generate-label, refuse-duplicate-label, download, and a full create→list→generate→view integration test. Two-button "Save Booking / Generate Label" implemented in `ShipmentCreateForm.tsx`. Not yet "Done" — no integration test against Carriers/Services/Tariffs pricing at booking time (Tariffs module isn't built yet). |
| 12 | Parcels & Items | 7 | **Tested (isolated)** | Covered by the Shipment tests above (parcel + item creation verified in DB). |
| 13 | Label Generation | 19 | **Real implementation (isolated tests passing)** | `DefaultLabelGenerator` now implements `LabelGeneratorInterface` for real: renders an actual PDF (dompdf) from shipment/parcel/address data, stores it, serves it via a download endpoint. Verified in tests by asserting the stored file starts with `%PDF`, not just checking a boolean flag. **Honest limits, not hidden:** the barcode area is a bordered text block, not a real scannable barcode (no barcode library added yet); `getDropOffLocations()`/`getTrackingStatus()` throw "not implemented" (need real carrier API credentials this project doesn't have — not fakeable). No real carrier (DHL/UPS/etc.) integration — this is a carrier-agnostic fallback label. |
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

## Current real completion: **~12%** (Carriers/Services/Countries built-but-untested; Shipments+Parcels/Items+Label Generation now built AND isolated-tested with a real PDF output; 0 modules fully integration-tested across the whole system yet)

## ✅ Environment blocker resolved (2026-09-29): backend now runs and tests in this session
Migrations ran successfully against a local SQLite DB once retried (the earlier
block was a one-time false positive). `APP_KEY` was also missing (an earlier
command that should have generated it never ran) — fixed. Real baseline test
run: **44 failed / 12 passed** out of 56 existing tests. `phpunit.xml` is still
hardcoded to a MySQL DB that doesn't exist here — tests are run with an inline
`DB_CONNECTION=sqlite` override instead, matching your app's actual default.

### ⚠️ CORRECTION (2026-09-30): the `is_pallet` fix below was WRONG
`db_full_schema.json` (an actual column-level dump of the real legacy
database, already sitting in the repo — should have checked this FIRST
instead of inferring from PHP form-field usage) shows the real `carriers`
table has 15 columns and **no `is_pallet`**. The real "pallet carrier"
concept lives in a separate `pallet_carriers` lookup table instead. Reverted
via a new corrective migration
(`2026_09_30_031227_remove_fabricated_is_pallet_from_carriers_table.php` —
never rewrote the already-pushed migration, added a follow-up instead) and
removed from `Carrier` model/`CarrierController`/`CarrierFactory`. Lesson
applied immediately below: checked `db_full_schema.json` before touching
`services.max_weight`/`tracking_flag` this time, instead of guessing again.

### Real bugs found and fixed by actually running tests
- ~~`carriers` table was missing `is_pallet`~~ — **this "fix" was itself
  wrong, see correction above.**
- **`services.max_weight` / `tracking_flag` — CONFIRMED fabricated, not
  ambiguous anymore.** Checked `db_full_schema.json`: the real `services`
  table has 79 columns and neither of these exists. Removed from `Service`
  model `$fillable`/`$casts` and `ServiceFactory`; added the real NOT-NULL
  columns the factory was missing instead (`fuel_surcharge_type`,
  `max_length`, `max_width`, `max_height`). **Result: 31 failed/36 passed →
  28 failed/39 passed**, zero regressions.
- **Stale test assertions use singular table names** (`carrier`, `country`)
  that don't match the real plural tables (`carriers`, `countries`). This is
  a bug in the *test files themselves*, not the schema — lower priority,
  doesn't affect production code.
- Consignment-module test failures (`ConsignmentControllerTest`,
  `ConsignmentTest`, part of `PricingEngineTest`) are expected/low-priority —
  they test the now-deprecated `Consignment` path.
- `AuthenticationTest` — 2 failures (session auth assertion not behaving as
  expected in the test environment) — not yet root-caused.
- ~~Zero test coverage exists for `Shipment`/`ShipmentController`~~ **RESOLVED
  2026-09-29**: `tests/Feature/Api/ShipmentControllerTest.php` added, 9/9
  passing, including a real full-flow integration test and a real PDF-content
  assertion.
- **All 7 `ServiceControllerTest` tests now fail with 403** (not just one) —
  now that the schema noise is gone, this is clearly a single real
  authorization bug affecting every Service route in tests, not a one-off.
  Not yet root-caused — next thing to look at for Tier 1.4.
- Stale test assertions using singular table names (`carrier`, `country`)
  and Consignment-module test failures remain as noted above — unaffected
  by this pass, still low-priority/expected.

### Running total after this pass
44 failed/12 passed → 37 failed/28 passed → 31 failed/36 passed (Tariffs
pass) → **28 failed/39 passed** (this correction), zero regressions at each
step — full suite re-run every time, not just the changed tests.

### Continued (same day): real carrier/service linkage + a second real bug
Started implementing real `getBaseRate()` tariff lookup for `PricingEngine`
and hit a structural gap: `shipments` had no `carrier_id`/`service_id`, only
a free-text `service_type` string — couldn't join to `tariffs` without one.
**User decision: add real FKs** (not fuzzy string matching). Implemented:
- Migration: `shipments.carrier_id`, `shipments.service_id` (nullable FKs).
- `Shipment` model: `carrier()`/`service()` relations, fillable updated.
- `StoreShipmentRequest`: now requires + validates both against real tables.
- `ShipmentCreateForm.tsx`: service `<Select>` now keyed by real `service_id`
  (was keyed by name string, fragile if two services share a name), sends
  `carrier_id`/`service_id` in the payload. TypeScript-clean.
- `ShipmentControllerTest.php`: updated to create a real `Carrier`+`Service`
  and assert the link — all 9 tests still pass.

**Second real bug found while wiring this up**: `Service` model had
`public $incrementing = false;` even though `services.id` IS a genuine
auto-increment primary key in the migration. This silently broke every
`Service::create()` that didn't manually pass an `id` — Eloquent would
insert the row correctly but never read back the real generated id, leaving
the in-memory model's `id` null. Fixed to `true` (kept `id` in `$fillable`
so legacy-data seeders can still assign explicit ids). Verified via tinker
before and after.

**RESOLVED same day**: real `Tariff`/`TariffDetail`/`CarrierZone`/
`CarrierZoneCountry` models added. Zone resolution confirmed via
`carrier_zones` + `carrier_zones_countries` (found by searching legacy
`carrier_zones.php`/`countries_zones.php`) rather than guessed at. See
module 8 above for the full picture, including a third schema-mismatch bug
found and deliberately NOT fixed (ConsignmentCharge vs. real
consignment_charges columns — moot, Consignment path is deprecated).

### Running total after this pass
37 failed / 28 passed → **31 failed / 36 passed** (net: 8 more tests
passing — 6 PricingEngineTest + 2 ShipmentControllerTest integration tests
— zero regressions, full suite re-run to confirm).

## 🔒 Blocking decision needed before modules 8, 11, 12, 13 can start

Everything above marked 🔒 depends on one call: **is `Consignment` (legacy
`consignments` table) or `Shipment` (new `shipments` table) the real path
forward?** Tariffs/Pricing, Parcels/Items, and Label Generation are all
sub-features of *booking a consignment* — building them on the wrong table
means redoing them later. I'm asking this before writing more code so I
don't repeat the earlier mistake of building on an unverified assumption.
