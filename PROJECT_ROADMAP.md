---
description: Update Project Roadmap based on restored legacy database
---
The roadmap is being updated to reflect the reality that we are building on top of the **legacy schema** (261 tables) rather than a simplified new schema.

## Phase 1: Foundation & Recovery (Current)
- [x] **Database Restoration**: Restore full 261-table schema and data from `logistics.sql`.
- [x] **Project Cleanup**: Remove inconsistent migration files and unused scripts.
- [ ] **Model Mapping**: Create Laravel Models for key legacy tables (`carrier`, `services`, `consignment`, `cost_tariffs`) to replace the "new" models that were using wrong table names (e.g., `Carrier` model should point to `carrier` table, not `carriers`).

## Phase 2: Core Modules (Backend)
### Carrier & Service Module
- **Goal**: Read/Write from legacy `carrier` and `services` tables via API.
- **Tasks**:
    - Update `Carrier` Model to use `protected $table = 'carrier';`.
    - Update `Service` Model to use `protected $table = 'services';`.
    - Verify API endpoints (`GET /carriers`, `GET /services`) return legacy data.

### Consignment Module
- **Goal**: Create shipments using the complex legacy logic.
- **Tasks**:
    - Update `Consignment` Model to use `protected $table = 'consignment';`.
    - Implement validation rules based on `carrier_service_default_rules`.
    - Implement pricing lookup from `sales_tariffs` and `cost_tariffs`.

## Phase 3: Frontend Integration
- **Goal**: Connect Next.js UI to the backend using the legacy data structure.
- **Tasks**:
    - Update "Carriers" page to display data from `carrier` table.
    - Update "Create Consignment" form to use `services` dropdown.

## Phase 4: Migration & Modernization (Long Term)
- **Goal**: Slowly refactor legacy tables to modern standards *after* functionalities are working.
- **Tasks**:
    - Identify unused columns in `consignment`.
    - Normalize naming conventions (snake_case vs camelCase) in API resources.

---
**Next Immediate Step:** Update Laravel Models (`Carrier.php`, `Service.php`, etc.) to point to the correct legacy table names.
