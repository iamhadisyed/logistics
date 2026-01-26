# Master Legacy Module & Database Logic

> **Purpose**: This file maps the 261 legacy database tables to business modules. Use this as the primary reference when developing any feature to ensure no legacy logic or data point is missed.

## 1. Carrier Module
**Logic**: Manages logistics providers, their credentials, and specific operational rules (hubs, cut-off times).

### Primary Tables
- **`carrier`**: The root table. Contains `company_name`, `cutoff_time`, `sftp_credentials` (for label upload).
- **`carrier_hubs`**: Defines physical hubs for carriers (e.g., "Yodel Hatfield Hub"). Used for routing logic.
- **`carrier_agent`**: If a carrier uses a third-party agent (middleman), their details go here.

### Routing & Rules
- **`carrier_service_default_rules`**: **CRITICAL**. Contains weight limits (`from_weight`, `to_weight`), dimension limits, and "is_default" flags for services.
    - *Usage*: Before booking a shipment, check this table. If parcel weight > `to_weight`, reject or upgrade service.
- **`carrier_service_customized_rules`**: Overrides default rules for specific customers (`agentid`) or user accounts.
- **`cacesa_routine`**, **`yodel_hubs`**, **`whistl_depo_details`**: Carrier-specific routing tables.
    - *Usage*: If Carrier is Yodel, lookup `yodel_hubs` using postcode to find the correct `hub_code` for the label.

---

## 2. Service Module
**Logic**: Defines the specific shipping products (e.g., "Next Day 24h", "Economy 48h").

### Primary Tables
- **`services`**: The main definition. Links to `carrier_id`. Contains `service_code` (mapped to carrier's internal code).
- **`services_data`**: Extended metadata, likely settings for UI display or specific printer configs per service.
- **`service_data_printing_pref`**: Stores label size/format preferences (e.g., 4x6, ZPL/PDF) per service.

### Routing Logic
- **`routing`**: The "brain" of the auto-selection. Maps `country_id` + `weight` -> `service_id`.
    - *Usage*: If user selects "Auto Routing", query this table to find the cheapest/best service for the destination.
- **`user_services_routing`**: Custom routing overrides for specific users (e.g., "Customer X always uses DHL for Germany").

---

## 3. Consignment (Shipment) Module
**Logic**: The core transactional entity. Handles creation, validation, pricing, and tracking of shipments.

### Core Data Structure
- **`consignment`**: The header record.
    - `consignment_no`: Unique Tracking ID.
    - `customer_reference`: Client's internal ref.
    - `service_id`: Validated service selection.
    - `shipto_name`, `shipto_address_*`: Delivery destination.
- **`consignment_details`**: The line items (parcels).
    - `id`: Unique Parcel ID.
    - `consignment_id`: Link to header.
    - `weight`, `length`, `width`, `height`: **Crucial** for volumetric calculation.
- **`consignment_collection`**: If the shipment requires a pickup request.

### Financials & Status
- **`consignment_charges`**: Stores the calculated costs *at the time of booking*.
    - `base_price`, `fuel_surcharge`, `total_price`.
- **`consignment_status_log`**: History of events (Booked -> Manifested -> In Transit).
- **`consignment_billing_hold`**: If a shipment is flagged for payment issues, it sits here.

---

## 4. Finance & Pricing Module
**Logic**: Determines how much to buy (cost) and sell (sales) shipping services for.

### Tariffs
- **`cost_tariffs`**: What the Carrier charges *us*.
    - Columns: `service_id`, `zone`, `weight_start`, `weight_end`, `price`.
- **`sales_tariffs`**: What *we* charge the customer.
    - Logic: Can be specific to `customer_id` or default base rates.

### Surcharges
- **`fuel_surcharge`**: Variable % added to base rate.
    - `carrier_id`, `from_date`, `to_date`, `percentage`.
- **`extra_charges`**: Fixed fees.
    - `remote_area_fee`: Checked against `remote_area_postcodes`.
    - `oversize_fee`: Appears if dims in `consignment_details` exceed limits.

---

## 5. Operations (Bagging & Manifesting) Module
**Logic**: Grouping individual consignments into "Bags" or "Manifests" for handover to the carrier.

### Tables
- **`manifest`**: Represents the daily handover paperwork.
    - `manifest_id`, `carrier_id`, `date`.
- **`bagging`**: Operations team scans parcels into a bag.
    - `bag_number`: Unique ID for the sack.
    - `bag_destination_country_id`: Sorting logic.
- **`bag_scan_log`**: Audit trail of who scanned which parcel into which bag.

---

## 6. User & Access Module (Legacy + Modern)
**Logic**: Authentication and Permissions.

### Legacy Tables
- **`admin_users`**: The old user table.
    - `username`, `password` (likely MD5/SHA1), `type` (role).
- **`role_master`**: Named roles (e.g., "Operations", "Accounts").
- **`user_rights`**: Link table defining what each role can `view`, `edit`, `delete`.

---

## 7. Data Exchange & Logging
**Logic**: Keeping track of external API calls and system health.

- **`api_log`**: Every request sent to Carrier / external APIs.
    - `request_payload`: XML/JSON sent.
    - `response_payload`: XML/JSON received (Critical for debugging label failures).
- **`webhook_log`**: Incoming status updates from carriers.

---

## Developer Walkthrough: Creating a Shipment
When implementing the "Create Shipment" API, you must touch these tables in order:

1.  **Validation**:
    -   Check `carrier_service_default_rules` for weight limits.
    -   Check `addresses` (or `brazil_postcode` etc.) for address validity.
2.  **Pricing**:
    -   Calculate Volumetric Weight using `consignment_details` dimensions.
    -   Query `sales_tariffs` for the *greater* of Actual vs Volumetric weight.
    -   Add `fuel_surcharge`.
3.  **Creation**:
    -   Insert into `consignment` (Header).
    -   Insert into `consignment_details` (Parcels).
    -   Insert into `consignment_charges` (Financial snapshot).
    -   Insert into `consignment_status_log` (Initial status "Pending").
4.  **Labeling**:
    -   Call Carrier API.
    -   Log request/response to `api_log`.
    -   Update `consignment` with `tracking_number`.
5.  **Output**:
    -   Return Label PDF URL.
