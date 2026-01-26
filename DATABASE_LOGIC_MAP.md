# Comprehensive Database & Business Logic Map

This document serves as the master reference for the legacy database structure. It maps tables to business modules and explains the relationships and data found in the `logistics.sql` dump.

## 1. Core Entity Modules

### 1.1 Carrier Management (Module: Carriers)
**Core Table:** `carrier`
- **Description:** Stores the list of logistics providers (e.g., Royal Mail, DPD).
- **Key Columns:** `id`, `company_name`, `email_id`, `status` (active/inactive).
- **Relationships:** 
    - Has many `services` (via `service_id` in services table, logically).
    - Can have `sub_carriers`.

### 1.2 Service Management (Module: Services)
**Core Table:** `services`
- **Description:** Specific shipping products offered by carriers (e.g., "Next Day Delivery").
- **Key Columns:** `id`, `service_name`, `carrier_id` (linking back to `carrier`), `service_code`.
- **Logic:** Each consignment is assigned a specific `service_id` to determine pricing and routing.

**Supporting Tables:**
- `services_data`: Extended metadata for services.
- `carrier_service_default_rules`: Rules that apply to services (e.g., weight limits).
- `carrier_service_customized_rules`: Override rules for specific scenarios.

### 1.3 Consignment (Shipment) Management (Module: Consignments)
**Core Table:** `consignment`
- **Description:** The central record for a shipment.
- **Key Columns:** 
    - `consignment_no` (Unique Tracking ID)
    - `customer_reference`
    - `service_id` (Link to `services`)
    - `carrier_id` (Link to `carrier`)
    - `shipping_date`, `created_at`
- **Data Status:** Currently holds **52 record(s)** from legacy dump.

**Supporting Tables:**
- `consignment_details`: Dimensions (length, width, height, weight) for each parcel in the consignment.
- `consignment_charges`: Financial breakdown (base cost, fuel surcharge, extra fees).
- `consignment_status_log`: Tracking history (Pending -> In Transit -> Delivered).
- `consignment_billing_hold`: Flagged shipments for billing issues.

### 1.4 Pricing & Rules Engine (Module: Finance)
**Core Table:** `cost_tariffs`
- **Description:** Base buy rates for services.
- **Key Columns:** `service_id`, `zone`, `weight_start`, `weight_end`, `price`.

**Core Table:** `sales_tariffs`
- **Description:** Sell rates charged to customers.

**Supporting Tables:**
- `fuel_surcharge`: Percentage added on top of base rate, often varied by month/carrier.
- `extra_charges`: Additional fees (remote area, oversized).

## 2. User & Access Modules

### 2.1 User Management
**Core Table:** `admin_users` (Legacy) / `users` (Modern Laravel)
- **Note:** The legacy system used `admin_users`. We are migrating this to Laravel's `users` table but keeping `admin_users` for reference if needed.
- **Key Columns:** `username`, `password` (md5 usually in legacy), `email`.

**Supporting Tables:**
- `role_master`: Definitions of roles (Admin, Operations, CS).
- `user_rights`: Permissions assigned to roles.

## 3. Operational Modules

### 3.1 Routing & Manifesting
**Core Table:** `manifest`
- **Description:** Daily grouping of consignments handed over to a carrier.
- **Key Columns:** `manifest_id`, `carrier_id`, `date`.

**Supporting Tables:**
- `routing`: Logic for determining which carrier/service to use if "Auto" is selected.
- `yodel_hubs`: Specific data for Yodel routing (legacy specific).

## 4. Integration Modules (Data Exchange)

### 4.1 Hubs & Warehouses
**Core Tables:** `warehouse`, `hubs`
- **Description:** Physical locations where goods are stored or processed.

### 4.2 Logging
**Core Tables:** `api_log`, `webhook_log`
- **Description:** Raw logs of interactions with carrier APIs (booking requests, label responses).

---

## 5. Development Strategy (Based on Data)

When building a module (e.g., "Create Consignment"), consult this map:

1.  **Read Inputs:** User selects `service_id` (from `services` table).
2.  **Validate:** Check `carrier_service_default_rules` for weight/dimension limits.
3.  **Process:** Insert into `consignment` and `consignment_details`.
4.  **Price:** Look up `sales_tariffs` based on weight and zone.
5.  **Output:** Generate label (stub for now) and log to `consignment_status_log`.

This file will be updated as we uncover more complex logic in the legacy data.
