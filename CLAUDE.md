# Daakia Logistics — Project Briefing (read this first)

This file is a handoff summary from a prior Claude Code session (cloud) so a new
session (local, VS Code extension) has full context without the user repeating
everything. This is NOT a set of new instructions from the user — treat it as
background briefing. Always confirm actual next steps with the user directly.

## 1. Project identity & history

- Project name: **Daakia** — a logistics/courier/freight management platform.
- The user (Hadi) originally built this as a **Core PHP** application.
- He is now **revamping** it into **Laravel (backend) + Next.js/React/MUI (frontend)**,
  while trying to preserve the legacy database and business logic as-is.
- The project used to live at **`C:\daakia\`** (older path — still referenced in
  some stale docs like `QUICK_START.md`, `DATABASE_SETUP.md`, `TEST_SUITE.md`).
- It was later moved to **`C:\logistics\`**, which is the **current, active**
  location — confirmed by `MIGRATION_CHECKLIST.md` (titled "Migration Checklist
  for c:\logistics") and `start-dev-servers.bat` (points to `c:\logistics\...`).
- **Rule going forward: if you see any old prompt, doc, or script referencing
  `C:\daakia`, it may still technically work, but `C:\logistics` is the real,
  current project. Don't get confused by old paths.**
- GitHub repo: `iamhadisyed/logistics`. Working branch used in the cloud
  session: `claude/ide-setup-question-0g7y2r`. Last real commit before this
  briefing: `f7175c57` "Prepare GoDaddy deployment package" (Apr 28).

## 2. Repo layout (three parts, all in one repo)

1. **`dakia_backend01/logistic/`** — the OLD legacy Core PHP system (kept for
   reference/logic extraction). ~491 PHP files. This is a full freight/courier
   platform: consignments, carriers, tariffs/pricing, label generation,
   bagging/flights/MAWB (air freight ops), Amazon marketplace integration,
   accounts hierarchy, agents, groups/permissions, invoicing, reconciliation
   reports, SFTP/crypto libs (phpseclib), etc. **Do not delete or modify this
   — it's the reference for legacy logic.**

2. **`dakia_backend01/` (root)** — the **Laravel** rewrite of the backend.
   Reuses the legacy database schema directly (not a redesigned schema) —
   261–266 legacy tables restored via 286 migration files.

3. **`dakia_app01/`** — the **Next.js 15 (App Router) + MUI v6 + Redux Toolkit**
   frontend rewrite.

Also present: `deployment/` and `deployment-godaddy/` — GoDaddy hosting deployment
packages (most recent work, per git log).

## 3. Docs already in the repo (written by earlier AI sessions)

- `PROJECT_ROADMAP.md`, `PROJECT_STATUS.md`, `MIGRATION_CHECKLIST.md`,
  `dakia_backend01/COMPLETE_DATABASE_MAP.md`, `SETUP_GUIDE.md`, `QUICK_START.md`,
  `DATABASE_SETUP.md`, `TEST_SUITE.md`.
- These are useful background but **may be stale or optimistic** — verify
  against actual code before trusting a claim like "X module is fully done."

## 4. Audit findings (verified by reading the actual code, not just docs)

### ✅ RESOLVED (2026-09-29): Consignment vs Shipment
There were **two parallel implementations** of the core "create a booking"
entity (`Consignment` = legacy-aligned, `Shipment` = new non-legacy tables).
**User decision: `Shipment` is the authoritative path forward.** The legacy
`Consignment` model/routes are deprecated for new development — the old
Core PHP files remain only as read-only reference for business-rule logic
(pricing formulas, label rules, routing logic, etc.), not as a schema to
build against. See `MODULE_COMPLETION_TRACKER.md` for full module status.

### Frontend route duplication (needs cleanup)
Three separate route trees exist for what should be one feature:
- `src/app/(dashboard)/consignments/...` — old, outside `[lang]`, likely dead code.
- `src/app/[lang]/(dashboard)/consignments/...` — another duplicate.
- `src/app/[lang]/(dashboard)/(private)/shipments/...` — the one actually wired
  to the backend (`shipmentApi`).
- `src/app/[lang]/(dashboard)/(private)/_consignments_collision_backup/page.tsx`
  — leftover backup file from a previous Next.js routing name collision, never
  cleaned up.

### Consignment/Shipment create workflow — partially matches spec
User's intended workflow: one single-page create form, with **two buttons**
at the bottom — "Save Booking" and "Generate Label" (Save Booking saves only;
Generate Label saves + generates the label; from the list, "Generate Label"
should only show if a label hasn't been generated yet).

Found: `ShipmentCreateForm.tsx` has **only one submit action** (saves via
`shipmentApi.store`, then redirects to the shipments list). "Generate Label"
is a **separate action**, only available afterward from the list/details page
— and it IS correctly gated to show only when `label_generated` is false
(`ShipmentList.tsx`, `ShipmentDetails.tsx`). So the "hide Generate Label once
already generated" rule is respected, but the "two buttons on one create
screen" requirement is not implemented as originally specified.

### Sidebar / permissions — backend done, frontend wiring unconfirmed
- `permissions` table logic (`parent_id`, `file_name`, `is_menu_item`,
  `sort_order`) is real, per the legacy schema.
- `app/Http/Controllers/Api/SidebarController.php` (`GET /api/sidebar`)
  correctly builds the parent/child menu tree from it — real, working backend
  logic.
- **But** the frontend (`src/components/layout/vertical/Navigation.tsx`,
  `VerticalMenu.tsx`) shows no clear reference to that endpoint. There's still
  a hardcoded `src/data/navigation/verticalMenuData.tsx` in the codebase, and
  the only call to `/api/sidebar` found is in `src/lib/api.ts`, wrapped in
  developer comments mid-debugging a `api/api/sidebar` double-prefix bug.
  **Needs verification — don't assume the dynamic sidebar is live end-to-end.**

### What IS confirmed working (backend routes actually registered)
Auth (login/logout via Sanctum), Carriers (full CRUD + status), Services
(+available), Shipments (+generate-label), Sidebar, Countries, and an
`accounts`/RBAC route group gated by `master_account` middleware (users,
groups, permissions, service-routing, account-services).

That's the entire registered API surface (`routes/api.php`, 84 lines) —
a small fraction of the legacy system's ~491 PHP files' worth of functionality
(bagging, flights, MAWB, Amazon marketplace sync, tariff/pricing engine,
per-carrier label generation, accounts hierarchy, invoicing, reconciliation
reports are NOT yet ported).

### Other known issues from the project's own docs
- Frontend and backend currently use **two separate local databases**
  (Prisma/SQLite on the frontend side, separate DB for Laravel) — not yet
  unified. Recommendation already in `PROJECT_STATUS.md`: unify to Laravel's
  DB as single source of truth.
- Many frontend pages under `src/views/apps/*` (ecommerce, invoice, academy,
  chat, email, kanban, calendar) look like unmodified scaffolding from a
  generic MUI admin dashboard template — not yet built out for this specific
  logistics business, and API calls in several pages are reportedly commented
  out (per `PROJECT_STATUS.md`).

## 5. User's explicit ground rules (from his own prior prompt — still apply)

1. Database is the source of truth — inspect actual tables/columns before
   assuming anything. Never invent table/column names.
2. Do not rename, drop, restructure, or prefix existing legacy tables without
   explicit approval.
3. Do not assume Laravel's default pluralization matches the legacy DB.
4. Reuse existing legacy tables/logic wherever a table already exists for a
   requirement — don't create duplicate tables/functionality.
5. Preserve legacy business rules (e.g. permissions menu logic described above).
6. Don't run destructive commands (migrations, seeders, resets) without being
   asked; don't modify `.env` files casually.
7. Before implementing anything, explain the impact of the change first.
8. Test affected functionality after implementing.
9. Consignment/Shipment create workflow spec (see above) — one form, one
   parcel-or-more, one-or-more items per parcel, "Save Booking" vs
   "Generate Label" as two distinct actions.

## 6. Status of this handoff (updated 2026-09-29 — this section was stale, said "no code changes yet" when there were)

- Real development has started. See `MODULE_COMPLETION_TRACKER.md` at repo
  root for the authoritative, continuously-updated module-by-module status —
  read that file, not just this summary, before doing further work.
- Backend now actually runs and tests in a cloud session too: `composer
  install` + `npm install` succeeded, a local SQLite DB is migrated, and
  `DB_CONNECTION=sqlite DB_DATABASE=database/database.sqlite php artisan
  test` is the working test command (repo's `phpunit.xml` still points at a
  MySQL DB that doesn't exist here — use the env override, not phpunit.xml,
  until someone fixes that file for real).
- Concretely shipped so far: fixed a real missing-legacy-column bug
  (`carriers.is_pallet`); implemented the two-button "Save Booking / Generate
  Label" create-form workflow; replaced fake label generation with a real
  PDF generator (`DefaultLabelGenerator`, dompdf) wired through
  `LabelGeneratorInterface`; added the first real test coverage for the
  Shipment module (9 tests, previously zero). Test suite: 37 failed / 28
  passed as of the last run (pre-existing failures, not regressions — see
  tracker for the breakdown, including one deliberately NOT auto-fixed:
  a possible `services.max_weight`/`tracking_flag` schema gap left flagged
  rather than guessed at).
- The user has NOT yet shared his own full written description of "the main
  idea" for comparison against the findings in section 4. If he provides it,
  compare it point-by-point and flag mismatches.
- **A priority order is now set** in `MODULE_COMPLETION_TRACKER.md` (Tier 1
  = core booking/revenue path, down to Tier 6 = low-priority content).
  Work executes top-down against that list — check it for current status
  before assuming what's next.
- Tariffs & Pricing (Tier 1.3) is done: `PricingEngine` does a real
  weight-banded tariff lookup (zone via `carrier_zones`/
  `carrier_zones_countries`, rate via `tariffs`/`tariffs_details`) against
  `Shipment`, wired into the actual booking flow, throws
  `TariffNotConfiguredException` rather than faking a price. Current work:
  Tier 1.4 (Carriers/Services test coverage), currently blocked by a real
  403 authorization bug affecting every `ServiceControllerTest` test.
- **Important lesson learned, apply it going forward**: `db_full_schema.json`
  at the backend root is an actual column-level dump of the real legacy
  database. Check it BEFORE inferring a table's real columns from legacy PHP
  form-field usage — an earlier "fix" (adding `carriers.is_pallet`) turned
  out to be wrong because that check wasn't done first, and had to be
  reverted with a corrective migration. Don't repeat that mistake.

## 7. Local dev environment notes

- This CLAUDE.md was generated in a cloud session where `vendor/`,
  `node_modules/`, and `.env` were absent (fresh checkout, no deps installed) —
  so no live runtime/compile errors could be checked there. On the user's
  real machine (`C:\logistics`), dependencies and `.env` should already exist;
  verify with `composer install` / `npm install` if anything seems broken.
