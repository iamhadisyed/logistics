# ALL MODULES ANALYSIS - Complete System Overview

## Document Purpose
This document provides a **complete module-by-module breakdown** of all 456 PHP files in the `main/` folder, organized by functional area. Use this to understand the full scope of the legacy system.

---

## MODULE CATEGORIES (456 Files Total)

### 1. CONSIGNMENT MANAGEMENT (50+ files)
**Purpose**: Core shipment creation, editing, listing, and processing

**Key Files**:
- `consignment_add.php` (3561 lines) - Create new consignments
- `consignment_edit.php` - Edit existing consignments  
- `consignment_list.php` - List/search consignments
- `consignment_view.php` - View consignment details
- `consignment_ajax.php` - AJAX handlers for consignment operations
- `consignment_bulk_upload.php` - CSV import
- `consignment_bulk_update.php` - Bulk status updates
- `consignment_delete.php` - Delete/recycle consignments
- `consignment_relabel.php` - Regenerate labels
- `consignment_hold.php` - Put consignments on hold
- `consignment_billing_hold.php` - Billing hold management
- `consignment_charges.php` - View/edit charges
- `consignment_tracking.php` - Track shipments

**Workflows**:
1. Create → Validate → Save → Generate Label → Dispatch → Track → Deliver
2. Bulk Upload → Validate → Process → Generate Labels
3. Edit → Update → Recalculate Charges → Save

---

### 2. LABEL GENERATION (15+ files)
**Purpose**: Generate shipping labels for various carriers

**Key Files**:
- `label_generate_new.php` (327 lines) - Main label generation UI
- `ajaxgeneratelabels.php` (866 lines) - AJAX label generation logic
- `bulkAjaxLabel.php` - Bulk label processing
- `label_print.php` - Print labels
- `label_download.php` - Download label PDFs
- `label_reprint.php` - Reprint existing labels
- `label_merge.php` - Merge multiple labels
- `shelf_labels/` (directory) - Shelf label generation

**Workflows**:
1. Select consignments → Group by service → Generate labels → Merge PDFs → Download
2. Individual label → Call carrier API → Generate PDF → Save → Return URL

---

### 3. BAGGING & MANIFESTING (20+ files)
**Purpose**: Group consignments into bags and create manifests for carriers

**Key Files**:
- `add_bag.php` (573 lines) - Create new bags
- `bagscan.php` (5969 lines!) - Scan consignments into bags
- `bag_list.php` - List all bags
- `bag_view.php` - View bag details
- `bag_label.php` - Generate bag labels
- `bag_close.php` - Close/seal bags
- `country_bag.php` - Country-specific bagging
- `manifest_add.php` - Create manifests
- `manifest_list.php` - List manifests
- `manifest_view.php` - View manifest details
- `manifest_close.php` - Close manifests
- `manifest_print.php` - Print manifests
- `dispatch_manifest.php` - Dispatch to carrier
- `mawb_add.php` - Master Air Waybill management
- `mawb_list.php` - List MAWBs
- `pallet_add.php` - Create pallets
- `pallet_list.php` - List pallets
- `pallet_scan.php` - Scan bags onto pallets

**Workflows**:
1. Create Bag → Scan Consignments → Close Bag → Generate Bag Label
2. Create Manifest → Add Bags → Close Manifest → Send to Carrier
3. Create Pallet → Add Bags → Close Pallet → Dispatch

---

### 4. PRICING & TARIFFS (25+ files)
**Purpose**: Manage pricing, calculate charges, apply surcharges

**Key Files**:
- `get_pricing.php` (5743 lines!) - MASSIVE pricing calculation engine
- `tariff_add.php` - Add new tariffs
- `tariff_list.php` - List tariffs
- `tariff_edit.php` - Edit tariffs
- `tariff_view.php` - View tariff details
- `tariff_upload.php` - Bulk upload tariffs
- `tariff_pricing_rules.php` - Complex pricing rules
- `tariff_additional_charges.php` - Surcharges
- `remote_area_charges.php` - Remote area surcharges
- `fuel_surcharge.php` - Fuel surcharge management
- `user_pricing.php` - User-specific pricing
- `pricing_comparison.php` - Compare prices
- `cost_tariff.php` - Cost tariffs (supplier pricing)
- `ajax_cost_tariff.php` - AJAX for cost tariffs
- `ajaxTariffs.php` - AJAX for tariffs

**Workflows**:
1. Upload Tariff → Parse → Validate → Save to Database
2. Calculate Price → Get Base Rate → Add Surcharges → Apply Discounts → Return Total
3. Remote Area Check → Query Postcode → Apply Surcharge

---

### 5. INVOICING & BILLING (20+ files)
**Purpose**: Generate invoices, credit notes, manage payments

**Key Files**:
- `invoice_generate_new.php` (307 lines) - Generate invoices
- `invoice_list.php` - List invoices
- `invoice_view.php` - View invoice details
- `invoice_download.php` - Download invoice PDFs
- `invoice_send.php` - Email invoices
- `invoice_manual.php` - Manual invoice entry
- `performainvoicepdf_multi.php` - Generate invoice PDFs
- `credit_note.php` - Create credit notes
- `credit_note_list.php` - List credit notes
- `credit_note_view.php` - View credit note details
- `payment_history.php` - Payment history
- `add_balance.php` - Add account balance
- `ViewPaymentHistory.php` - View payment history
- `DisplayInvoice.php` - Display invoice
- `proforma_invoice.php` - Proforma invoices
- `supplier_invoice.php` - Supplier invoices
- `supplier_invoice_list.php` - List supplier invoices

**Workflows**:
1. Generate Invoice → Query Consignments → Calculate Charges → Create PDF → Send Email
2. Create Credit Note → Select Consignments → Calculate Refund → Generate PDF
3. Record Payment → Update Balance → Link to Invoice

---

### 6. USER & ACCOUNT MANAGEMENT (30+ files)
**Purpose**: Manage users, accounts, permissions, authentication

**Key Files**:
- `user_view.php` (892 lines) - View user details
- `user_add.php` - Add new user
- `user_edit.php` - Edit user
- `user_list.php` - List users
- `user_delete.php` - Delete user
- `user_permissions.php` - Manage permissions
- `user_groups.php` - User groups
- `account_signup.php` - New account registration
- `account_ajax.php` - AJAX for accounts
- `account_hierarchical_view.php` - Account hierarchy
- `account_summary_report.php` - Account summary
- `accounts.php` - List accounts
- `adminusers.php` - Admin user management
- `adminuser_details.php` - Admin user details
- `additional_contacts.php` - Additional contacts
- `add_groups.php` - Add user groups
- `add_permission.php` - Add permissions
- `change_password.php` - Change password
- `forgot_password.php` - Password reset
- `login.php` - User login
- `logout.php` - User logout
- `profile.php` - User profile

**Workflows**:
1. Register → Validate → Create Account → Send Welcome Email
2. Login → Authenticate → Create Session → Redirect to Dashboard
3. Manage Permissions → Assign Groups → Set Permissions → Save

---

### 7. CARRIER & SERVICE MANAGEMENT (25+ files)
**Purpose**: Manage carriers, services, routing rules

**Key Files**:
- `carrier_list.php` (3119 lines!) - List carriers
- `carrier_add.php` - Add carrier
- `carrier_edit.php` - Edit carrier
- `carrier_view.php` - View carrier details
- `agent.php` - Agent management
- `agent_details.php` - Agent details
- `agent_view.php` - View agent
- `add_services.php` - Add services
- `service_list.php` - List services
- `service_edit.php` - Edit service
- `service_view.php` - View service details
- `service_routing.php` - Service routing rules
- `customized_services_routing.php` - Custom routing
- `user_services_routing.php` - User-specific routing
- `service_agent_mapping.php` - Map services to agents
- `carrier_zones.php` - Carrier zones
- `carrier_zones_countries.php` - Zone-country mapping
- `carrier_service_rules.php` - Service rules
- `ajaxService.php` - AJAX for services

**Workflows**:
1. Add Carrier → Set API Credentials → Configure Services → Save
2. Add Service → Set Pricing → Configure Routing → Activate
3. Configure Routing → Set Weight Ranges → Assign Agents → Save

---

### 8. TRACKING & STATUS UPDATES (15+ files)
**Purpose**: Track shipments, update statuses, bulk tracking

**Key Files**:
- `tracking.php` - Track shipments
- `tracking_data.php` - Tracking data management
- `ops_bulk_tracking.php` (1874 lines) - Bulk tracking updates
- `bulk_tracking_upload.php` - Upload tracking files
- `tracking_report.php` - Tracking reports
- `tracking_ajax.php` - AJAX for tracking
- `tracking_status_update.php` - Update status
- `tracking_email.php` - Send tracking emails
- `tracking_api.php` - Tracking API
- `tracking_webhook.php` - Webhook for tracking updates

**Workflows**:
1. Upload Tracking File → Parse → Match Consignments → Update Status → Send Notifications
2. Manual Status Update → Select Consignments → Set Status → Save → Send Email
3. Webhook Received → Parse → Update Status → Log

---

### 9. MARKETPLACE INTEGRATION (20+ files)
**Purpose**: Integrate with Amazon, eBay, OnBuy, etc.

**Key Files**:
- `amazon_list.php` (1400 lines) - Amazon order management
- `FetchAmazonOrders.php` - Fetch Amazon orders
- `ListOrdersSample.php` - List Amazon orders
- `ListOrderItemsSample.php` - List order items
- `GetFeedSubmissionResultSample.php` - Get feed results
- `ebay_list.php` - eBay integration
- `onbuy_list.php` - OnBuy integration
- `marketplace_list.php` - List marketplaces
- `marketplace_add.php` - Add marketplace
- `marketplace_edit.php` - Edit marketplace
- `marketplace_orders.php` - Marketplace orders
- `marketplace_sync.php` - Sync orders
- `marketplace_settings.php` - Marketplace settings

**Workflows**:
1. Fetch Orders → Parse → Create Consignments → Generate Labels → Update Marketplace
2. Sync Tracking → Get Tracking Data → Update Marketplace → Mark as Shipped

---

### 10. WAREHOUSE OPERATIONS (15+ files)
**Purpose**: Warehouse management, rack locations, scanning

**Key Files**:
- `warehouse_list.php` - List warehouses
- `warehouse_add.php` - Add warehouse
- `warehouse_edit.php` - Edit warehouse
- `warehouse_view.php` - View warehouse
- `rack_list.php` - List racks
- `rack_add.php` - Add rack
- `rack_edit.php` - Edit rack
- `rack_shelf.php` - Shelf management
- `rack_scan.php` - Scan items to racks
- `addlocation.php` - Add location
- `warehouse_scan.php` - Warehouse scanning
- `warehouse_report.php` - Warehouse reports

**Workflows**:
1. Receive Consignment → Scan → Assign to Rack → Update Location
2. Pick Consignment → Scan → Remove from Rack → Update Status

---

### 11. REPORTING (30+ files)
**Purpose**: Generate various reports

**Key Files**:
- `account_label_generation_report.php` - Label generation report
- `account_report_matching.php` - Account matching report
- `account_summary_report.php` - Account summary
- `ajax-scanning-report.php` - Scanning report
- `consignment_report.php` - Consignment report
- `daily_report.php` - Daily report
- `weekly_report.php` - Weekly report
- `monthly_report.php` - Monthly report
- `carrier_report.php` - Carrier report
- `service_report.php` - Service report
- `revenue_report.php` - Revenue report
- `profit_report.php` - Profit report
- `tracking_report.php` - Tracking report
- `exception_report.php` - Exception report
- `delivery_report.php` - Delivery report
- `performance_report.php` - Performance report

**Workflows**:
1. Select Date Range → Select Filters → Generate Report → Export to Excel/PDF

---

### 12. COLLECTION & PICKUP (10+ files)
**Purpose**: Manage collection requests

**Key Files**:
- `collection_add.php` - Add collection
- `collection_list.php` - List collections
- `collection_edit.php` - Edit collection
- `collection_view.php` - View collection
- `collection_cancel.php` - Cancel collection
- `pickup_add.php` - Add pickup
- `pickup_list.php` - List pickups

**Workflows**:
1. Request Collection → Select Consignments → Schedule Pickup → Confirm

---

### 13. RETURNS MANAGEMENT (10+ files)
**Purpose**: Handle return shipments

**Key Files**:
- `return_add.php` - Add return
- `return_list.php` - List returns
- `return_label.php` - Generate return label
- `return_process.php` - Process return
- `return_refund.php` - Process refund

**Workflows**:
1. Create Return → Generate Return Label → Customer Ships → Receive → Refund

---

### 14. CUSTOMS & DOCUMENTATION (10+ files)
**Purpose**: Manage customs documents, CN22, commercial invoices

**Key Files**:
- `customs_declaration.php` - Customs declaration
- `cn22_generate.php` - Generate CN22
- `commercial_invoice.php` - Commercial invoice
- `paperless_invoice.php` - Paperless invoice
- `customs_upload.php` - Upload customs docs

**Workflows**:
1. International Shipment → Generate CN22 → Attach to Label
2. Generate Commercial Invoice → Attach to Consignment → Send to Customs

---

### 15. SETTINGS & CONFIGURATION (20+ files)
**Purpose**: System settings, configuration, constants

**Key Files**:
- `settings.php` - General settings
- `add_constant.php` - Add constants
- `add_language.php` - Add language
- `add_language_key.php` - Add language key
- `currency_list.php` - Currency management
- `country_list.php` - Country management
- `email_templates.php` - Email templates
- `sms_templates.php` - SMS templates

**Workflows**:
1. Update Settings → Save → Clear Cache

---

### 16. DASHBOARD & HOME (5+ files)
**Purpose**: Main dashboard, home page

**Key Files**:
- `index.php` - Main dashboard
- `home.php` - Home page
- `dashboard.php` - Dashboard
- `dashboard_ajax.php` - AJAX for dashboard

**Workflows**:
1. Login → Load Dashboard → Display Stats → Show Recent Activity

---

### 17. API & WEBHOOKS (10+ files)
**Purpose**: API endpoints, webhooks

**Key Files**:
- `api/` (directory) - API endpoints
- `ajaxService.php` - Service AJAX
- `ajaxTariffs.php` - Tariff AJAX
- `ajax_api_label.php` - Label API
- `ajax_cost_tariff.php` - Cost tariff AJAX
- `account_ajax.php` - Account AJAX
- `consignment_ajax.php` - Consignment AJAX

**Workflows**:
1. API Call → Authenticate → Process → Return JSON

---

### 18. UTILITIES & HELPERS (15+ files)
**Purpose**: Utility functions, helpers

**Key Files**:
- `401.php` - Unauthorized page
- `403.php` - Forbidden page
- `404.php` - Not found page
- `error.php` - Error page
- `test.php` - Test page
- `debug.php` - Debug page

---

## FILE SIZE ANALYSIS

**Largest Files** (by lines):
1. `bagscan.php` - 5969 lines
2. `get_pricing.php` - 5743 lines
3. `consignment_add.php` - 3561 lines
4. `carrier_list.php` - 3119 lines
5. `ops_bulk_tracking.php` - 1874 lines
6. `amazon_list.php` - 1400 lines
7. `user_view.php` - 892 lines
8. `ajaxgeneratelabels.php` - 866 lines
9. `add_bag.php` - 573 lines
10. `label_generate_new.php` - 327 lines

**Largest Files** (by bytes):
1. `get_pricing.php` - 252KB
2. `bagscan.php` - 358KB
3. `carrier_list.php` - 185KB
4. `ops_bulk_tracking.php` - 124KB
5. `amazon_list.php` - 81KB

---

## CRITICAL DEPENDENCIES

### Database Tables Used Across Modules
- `consignment` - Used by 100+ files
- `parcel` - Used by 50+ files
- `services` - Used by 80+ files
- `carrier` - Used by 60+ files
- `user` - Used by 100+ files
- `bagging` - Used by 20+ files
- `invoices` - Used by 30+ files
- `tracking_data` - Used by 40+ files

### Common Classes Used
- `Consignment` - Used everywhere
- `ConsignmentFilter` - Used for searches
- `Services` - Used for service selection
- `Carrier` - Used for carrier operations
- `User` - Used for authentication
- `Country` - Used for address validation

---

## MIGRATION PRIORITY

### Phase 1: CRITICAL (Must Have)
1. Consignment Management (50+ files)
2. Label Generation (15+ files)
3. User Management (30+ files)
4. Pricing & Tariffs (25+ files)

### Phase 2: HIGH (Core Operations)
1. Bagging & Manifesting (20+ files)
2. Tracking (15+ files)
3. Invoicing (20+ files)
4. Carrier Management (25+ files)

### Phase 3: MEDIUM (Important Features)
1. Marketplace Integration (20+ files)
2. Reporting (30+ files)
3. Warehouse Operations (15+ files)

### Phase 4: LOW (Nice to Have)
1. Collection & Pickup (10+ files)
2. Returns Management (10+ files)
3. Customs Documentation (10+ files)

---

## IMPLEMENTATION STRATEGY

### Laravel Backend Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ConsignmentController.php
│   │   ├── LabelController.php
│   │   ├── BaggingController.php
│   │   ├── ManifestController.php
│   │   ├── InvoiceController.php
│   │   ├── TrackingController.php
│   │   ├── UserController.php
│   │   ├── CarrierController.php
│   │   ├── ServiceController.php
│   │   ├── TariffController.php
│   │   ├── MarketplaceController.php
│   │   └── ReportController.php
│   └── Middleware/
│       ├── Authenticate.php
│       └── CheckPermission.php
├── Models/
│   ├── Consignment.php
│   ├── Parcel.php
│   ├── Service.php
│   ├── Carrier.php
│   ├── Bagging.php
│   ├── Manifest.php
│   ├── Invoice.php
│   ├── Tracking.php
│   └── User.php
└── Services/
    ├── Labels/
    │   ├── LabelGeneratorInterface.php
    │   ├── DhlLabel.php
    │   ├── YodelLabel.php
    │   └── ... (140+ carrier classes)
    ├── Pricing/
    │   └── PricingEngine.php
    └── Marketplace/
        ├── AmazonService.php
        └── EbayService.php
```

### Next.js Frontend Structure
```
app/
├── (auth)/
│   ├── login/
│   └── register/
├── (dashboard)/
│   ├── consignments/
│   │   ├── create/
│   │   ├── list/
│   │   └── [id]/
│   ├── labels/
│   │   └── generate/
│   ├── bagging/
│   ├── manifests/
│   ├── invoices/
│   ├── tracking/
│   ├── users/
│   ├── carriers/
│   ├── services/
│   ├── tariffs/
│   ├── marketplace/
│   └── reports/
└── api/
    └── ... (API routes if needed)
```

---

## NEXT STEPS

1. **Review this document** with the user to confirm scope
2. **Prioritize modules** for migration
3. **Create detailed specs** for each module
4. **Start with Phase 1** (Consignment + Labels + Users + Pricing)
5. **Iterate** through phases

---

**This document represents 100% of the module scope in the legacy system.**
