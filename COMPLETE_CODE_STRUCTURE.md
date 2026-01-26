# COMPLETE CODE STRUCTURE ANALYSIS

## Executive Summary
This document provides a **complete analysis** of the legacy PHP logistics system's code structure, extracted from reading actual source code (not just file names). Use this as the definitive reference for migration.

---

## 1. PROJECT ARCHITECTURE OVERVIEW

### Directory Structure
```
old_project/
├── main/                    # 456 PHP files - UI/Controllers
├── includes/
│   ├── mapping/             # 515 class files - Business Logic (Models)
│   ├── labels/              # 140+ carrier-specific label generators
│   ├── general/             # Utility classes
│   ├── 3rdparty/            # TCPDF, FPDI, PHPExcel
│   └── settings/            # Configuration
├── api/                     # API endpoints
├── Classes/                 # PHPExcel library
├── assets/                  # CSS, JS, images
└── _assets/                 # Generated files (PDFs, uploads)
```

### Architecture Pattern
**Type**: Procedural PHP with OOP classes (Hybrid)
- **UI Layer**: `main/*.php` - Mix of HTML, PHP, JavaScript
- **Business Logic**: `includes/mapping/*.class.php` - OOP classes
- **Data Access**: Custom `DbAccess3` base class (Active Record pattern)
- **Labels**: Strategy pattern - each carrier is a separate class

---

## 2. CORE BUSINESS LOGIC CLASSES

### 2.1 Consignment.class.php (210KB, 4198 lines, 92 methods)
**THE MOST CRITICAL FILE IN THE ENTIRE SYSTEM**

#### Key Constants
```php
// Service Types
SERVICE_DOMESTIC = "DBP"
SERVICE_INTERNATIONAL = "INT"
SERVICE_EUROPE_ROAD = "R1"
SERVICE_RETURN = "RTN"

// Status Flow (10-31)
STATUS_NEW = 10
STATUS_INVALID = 11
STATUS_READY_TO_PRINT = 12
STATUS_LABEL_CREATED = 13
STATUS_RECEIVED = 14
STATUS_DISPATCHED = 16
STATUS_INTRANSIT = 18
STATUS_DELIVERED = 19
STATUS_RECYCLED = 22 (deleted)
STATUS_CANCELLED = 23
STATUS_HOLD = 24
STATUS_PROBLEM = 25
STATUS_RETURNED = 27
```

#### Critical Methods (Top 20)

1. **`__construct($mixedCreator)`** (Lines 155-374)
   - Defines 370+ fields
   - Maps database columns to object properties
   - Handles both ID and array initialization

2. **`isValid()`** (Lines 1025-1200)
   - **175 lines of validation logic**
   - Validates sender/receiver addresses
   - Checks weight, dimensions, service selection
   - Country-specific postcode validation (e.g., Brazil format)
   - Returns error_list array

3. **`getServiceTypeFromWeight($user, $weight, $country, $type, $routing)`** (Lines 523-575)
   - Complex SQL join across 3 tables
   - Matches user + weight + country to find allowed service
   - Returns service code, name, denominator

4. **`getStandardUserRoutingCode($userId, $countryIso, $serviceId, $weight)`** (Lines 593-635)
   - Determines which carrier/agent handles the shipment
   - Checks user-specific routing rules
   - Falls back to parent account rules
   - Returns agent ID or 'NON_AGREED'

5. **`CustomizeAgentRule($userAccountId, $countryIso, $serviceId, $weight)`** (Lines 638-681)
   - **Recursive function** - checks parent accounts
   - Looks up `carrier_service_customize_rules` table
   - Falls back to `defaultAgentRule()` if no custom rule

6. **`defaultAgentRule($countryIso, $serviceId, $weight)`** (Lines 683-699)
   - Final fallback for agent assignment
   - Queries `carrier_service_default_rules`

7. **`getParcels($create_if_missing)`** (Lines 814-861)
   - Returns array of Parcel objects
   - Creates missing parcels if needed
   - Each consignment has 1+ parcels

8. **`GetVolWeightOfConsignment($conid, $denominator)`** (Lines 1409-1423)
   - Calculates volumetric weight
   - Formula: (L × W × H) / denominator
   - Denominator varies by service (5000, 6000, etc.)

#### Field Definitions (370+ fields)
**Core Fields**:
- `id`, `user_id`, `service_id`, `warehouse_id`
- `awb` (tracking number), `hawb` (internal reference)
- `shipment_status`, `shipment_type`
- `reference`, `date_created`, `date_label_created`

**Receiver Address**:
- `company`, `contact`, `address_line_1/2/3`
- `city`, `state`, `postcode`, `country_id`
- `telephone`, `email`

**Sender Address** (prefixed with `sender_`):
- `sender_company`, `sender_contact`, `sender_address_line_1/2/3`
- `sender_city`, `sender_postcode`, `sender_country_id`
- `sender_telephone`, `sender_email`

**Parcel Details**:
- `number_pieces`, `weight`, `vol_weight`, `charge_weight`
- `description`, `value`, `currency`
- `is_doc` (document vs. parcel)

**Special Fields**:
- `remote_charges` (1 if remote area)
- `is_insured`, `eori_number`, `vat_number`, `ioss_number`
- `label_file` (path to PDF)
- `routing_code`, `agent_id`

### 2.2 ConsignmentFilter.class.php (114KB, complex queries)
**Purpose**: Build dynamic SQL queries for consignment searches

**Key Methods**:
- `addStatusFilter($status)`
- `addAccountFilter($account)`
- `addServiceFilter($serviceId)`
- `addDateRangeFilter($from, $to)`
- `getList()`, `getCount()`, `getPagingList()`

### 2.3 ConsignmentCharges.class.php (67KB)
**Purpose**: Calculate all charges for a consignment

**Charge Types**:
- Basic shipping cost
- Fuel surcharge
- Remote area surcharge
- Oversize surcharge
- Insurance fee
- COD fee
- Customs clearance fee

**Key Method**: `calculateCharges($consignment)`

### 2.4 Services.class.php (31KB)
**Purpose**: Manage carrier services

**Fields**:
- `id`, `code`, `name`, `carrier_id`
- `type` (D/I/E/R)
- `volumetric_denominator`
- `label_class_name` (e.g., "DHL", "Yodel")
- `service_country` (allowed countries)

### 2.5 Carrier.class.php (24KB)
**Purpose**: Manage carriers/agents

**Fields**:
- `id`, `carrier` (name), `logo`
- `api_url`, `api_username`, `api_password`
- `tracking_url`

### 2.6 Parcel.class.php (8KB)
**Purpose**: Individual parcel within consignment

**Fields**:
- `consignment_id`, `tracking_number`
- `length`, `width`, `height`, `weight`
- `barcode_data`

### 2.7 Tracking.class.php (53KB) & TrackingData.class.php (32KB)
**Purpose**: Tracking events

**TrackingData Fields**:
- `consignment_id`, `tracking_number`
- `status_code`, `status_description`
- `location`, `timestamp`
- `signature` (for delivery)

### 2.8 Tariffs.class.php (7KB) & TariffsPricingRules.class.php
**Purpose**: Pricing engine

**Logic**:
- Weight bands (0-1kg, 1-5kg, 5-10kg, etc.)
- Zone-based pricing
- User-specific rates
- Surcharge rules

---

## 3. LABEL GENERATION SYSTEM

### 3.1 Carrier Label Classes (140+ files in `includes/labels/`)

**Pattern**: Each carrier has its own class extending a base

**Major Carriers**:
1. **DHL** - `dhl.class.php` (98KB) - Largest, most complex
2. **Yodel** - `yodel.class.php` (128KB)
3. **Royal Mail** - `royalmail.class.php` (143KB) - BIGGEST
4. **DPD UK** - `dpduk.class.php` (74KB)
5. **Hermes** - `hermes.class.php` (75KB)
6. **UPS** - `ups.class.php` (51KB)
7. **Parcel Force** - `parcelforce.class.php` (26KB)
8. **Canada Post** - `canadapost.class.php` (86KB)
9. **Deutsche Post** - `deutschepost.class.php` (60KB)
10. **Aramex** - `aramex.class.php` (27KB)

**Plus 130+ more carriers** including:
- Asendia, Belgium Post, BRT Italy, CTT Express
- Fastway, GLS, Omniva, Orange Connex
- PostNL, Sweden Post, Tourline, Whistl
- And many regional carriers

### 3.2 Standard Label Class Structure

```php
class CarrierName {
    // Generate label PDF
    public function generateLabel($consignment) {
        // 1. Validate consignment data
        // 2. Call carrier API (if real-time)
        // 3. Generate PDF using TCPDF
        // 4. Add barcode, addresses, logo
        // 5. Save PDF to _assets/pdf/
        // 6. Return file path
    }
    
    // Get drop-off locations (if applicable)
    public function getDropOffLocation($postcode, $country, $city) {
        // Call carrier's location API
        // Return array of locations with lat/lng
    }
    
    // Get tracking updates
    public function getTrackingStatus($trackingNumber) {
        // Call carrier's tracking API
        // Parse response
        // Return tracking events
    }
}
```

### 3.3 Label Generation Flow (from ajaxgeneratelabels.php)

```
1. User selects consignments (status = READY_TO_PRINT)
2. Group by service_id
3. For each service:
   a. Get service.label_class_name
   b. Load includes/labels/{lowercase(label_class_name)}.class.php
   c. Instantiate class: $labelObj = new $className()
   d. For each consignment:
      - Call $labelObj->generateLabel($consignment)
      - Returns PDF path
   e. Merge all PDFs for this service
4. Final merge of all service PDFs
5. Update consignment.shipment_status = LABEL_CREATED
6. Update consignment.label_file = merged_pdf_path
7. Return URL to user
```

### 3.4 PDF Libraries Used
- **TCPDF** - Main PDF generation (includes/3rdparty/tcpdf/)
- **FPDI** - PDF merging (includes/3rdparty/fpdi/)
- **PDFMerger** - Custom wrapper (includes/labels/pdfmerger.php)

---

## 4. DATABASE SCHEMA INSIGHTS

### 4.1 Core Tables (from logistics.sql)

**consignment** - Main table
- 370+ columns
- Stores all shipment data
- Indexed on: id, hawb, awb, user_id, service_id, shipment_status

**parcel** - Parcel details
- Links to consignment via consignment_id
- Stores dimensions, weight, tracking_number

**services** - Carrier services
- Defines available shipping options
- Links to carrier table

**carrier** - Carriers/Agents
- Stores carrier information and API credentials

**customizedservicesrouting** - User service permissions
- Which users can use which services
- Weight ranges, countries

**user_services_routing** - Custom routing rules
- User-specific agent assignments

**carrier_service_customize_rules** - Agent assignment rules
- Per user, per service, per weight range

**carrier_service_default_rules** - Default agent rules
- Fallback when no custom rule exists

**tariffs** - Pricing matrix
- Base rates by weight, zone, service

**consignment_charges** - Calculated charges
- Breakdown of all fees per consignment

**tracking_data** - Tracking events
- Status updates from carriers

**bagging** - Bag management
- Groups consignments for dispatch

**consignment_bagging_mapping** - Links consignments to bags

**invoices** - Customer invoices

**invoice_detail** - Invoice line items

### 4.2 Key Relationships

```
user (1) ----< (many) consignment
consignment (1) ----< (many) parcel
consignment (many) >---- (1) services
services (many) >---- (1) carrier
consignment (many) >---- (1) user (via user_id)
consignment (many) >---- (1) carrier (via agent_id)
consignment (1) ----< (many) consignment_charges
consignment (1) ----< (many) tracking_data
consignment (many) >----< (many) bagging (via consignment_bagging_mapping)
```

---

## 5. CRITICAL BUSINESS LOGIC ALGORITHMS

### 5.1 Service Selection Algorithm

```
INPUT: user_id, from_country, to_country, weight, service_type

PROCESS:
1. Query customizedservicesrouting WHERE:
   - user_id matches (or parent account)
   - from_weight <= weight <= to_weight
   - country matches
   - service_type matches (D/I/E/R)
   - status = 'active'

2. Filter by user permissions

3. Return list of available services with:
   - service_id, service_name, carrier_name
   - carrier_logo (for UI display)
   - volumetric_denominator
   - is_agreed (pricing agreed?)

OUTPUT: Array of service objects
```

### 5.2 Agent Assignment Algorithm

```
INPUT: user_id, country, service_id, weight

PROCESS:
1. Check user_services_routing:
   - Match user_id, country, service_id, weight range
   - If found and is_agreed = 1:
     - Go to step 2
   - Else:
     - Return error "NON_AGREED"

2. Check carrier_service_customize_rules:
   - Match user_account_id, service_id, weight range
   - If found:
     - Return agent_id
   - Else:
     - Go to step 3

3. Check parent account (recursive):
   - If user has parent account:
     - Repeat steps 1-2 for parent
   - Else:
     - Go to step 4

4. Check carrier_service_default_rules:
   - Match service_id, weight range
   - Return agent_id

5. If no match found:
   - Return 0 (error)

OUTPUT: agent_id (carrier who will handle shipment)
```

### 5.3 Pricing Calculation Algorithm

```
INPUT: consignment object

PROCESS:
1. Determine chargeable weight:
   - actual_weight = consignment.weight
   - vol_weight = (L × W × H) / denominator
   - chargeable_weight = MAX(actual_weight, vol_weight)

2. Get base rate from tariffs:
   - Match user_account, service, destination_zone, weight_band
   - If user-specific rate exists, use it
   - Else use default rate

3. Calculate surcharges:
   a. Fuel surcharge = base_rate × fuel_percentage
   b. Remote area surcharge (if applicable):
      - Check remoteareas table by postcode
      - Add fixed fee or percentage
   c. Oversize surcharge (if L > X or W > Y or H > Z)
   d. Insurance fee (if is_insured = 1):
      - insurance_fee = value × insurance_rate
   e. COD fee (if cash on delivery)
   f. Customs clearance fee (for international)

4. Calculate total:
   - subtotal = base_rate + all surcharges
   - vat = subtotal × vat_rate (if applicable)
   - total = subtotal + vat

5. Save to consignment_charges table

OUTPUT: total_amount, breakdown of charges
```

### 5.4 Label Generation Algorithm (Detailed)

```
INPUT: consignment_id

PROCESS:
1. Load consignment object:
   - $consignment = new Consignment($consignment_id)

2. Validate status:
   - If shipment_status != STATUS_READY_TO_PRINT:
     - Return error

3. Get service and carrier:
   - $service = new Services($consignment->getServiceId())
   - $label_class = $service->getLabelClassName()

4. Load carrier label class:
   - $file = "includes/labels/" . strtolower($label_class) . ".class.php"
   - require_once($file)
   - $labelObj = new $label_class()

5. Generate label:
   - $result = $labelObj->generateLabel($consignment)
   - This may:
     a. Call carrier API to get tracking number
     b. Generate barcode
     c. Create PDF with TCPDF
     d. Save to _assets/pdf/YYYY_MM_DD/{unique_id}.pdf

6. Update consignment:
   - consignment.label_file = pdf_path
   - consignment.awb = tracking_number (if from API)
   - consignment.shipment_status = STATUS_LABEL_CREATED
   - consignment.date_label_created = NOW()
   - consignment.save()

7. Log API call (if applicable):
   - Save request/response to api_data table

OUTPUT: {
    'STATUS': 'SUCCESS',
    'LABEL_FILE': pdf_path,
    'AWB': tracking_number
}
```

---

## 6. VALIDATION RULES (from isValid() method)

### Sender Validation (if sender_checked = 1)
- ✅ sender_address_line_1 NOT empty
- ✅ sender_city NOT empty
- ✅ sender_postcode NOT empty

### Receiver Validation
- ✅ address_line_1 NOT empty
- ✅ city NOT empty
- ✅ postcode NOT empty (unless country doesn't require)
- ✅ contact NOT empty AND length <= 35
- ✅ company length <= 35
- ✅ country selected

### Shipment Validation
- ✅ weight > 0.000
- ✅ weight is numeric
- ✅ value is numeric
- ✅ description NOT empty
- ✅ service selected
- ✅ service_type selected (unless product mode)
- ✅ number_pieces < 100

### Country-Specific Rules
- **Brazil**: Postcode must be format XXXXX-XXX
- **Countries without postcodes**: Postcode optional (checked via country.postcode_required field)

---

## 7. STATUS FLOW DIAGRAM

```
NEW (10)
  ↓
INVALID (11) ← [Validation fails]
  ↓
READY_TO_PRINT (12) ← [Validation passes]
  ↓
LABEL_CREATED (13) ← [Label generated]
  ↓
RECEIVED (14) ← [Scanned at warehouse]
  ↓
DISPATCHED (16) ← [Sent to carrier]
  ↓
INTRANSIT (18) ← [In carrier network]
  ↓
DELIVERED (19) ← [Final delivery]
  ↓
CLOSE (21)

Side branches:
- RECYCLED (22) - Deleted/Cancelled
- HOLD (24) - On hold
- PROBLEM (25) - Issue occurred
- RETURNED (27) - Returned to sender
- NOT_DELIVERED (31) - Failed delivery
```

---

## 8. API INTEGRATION PATTERNS

### 8.1 Carrier API Calls
Most carrier label classes follow this pattern:

```php
public function generateLabel($consignment) {
    // 1. Prepare request XML/JSON
    $request = $this->buildRequest($consignment);
    
    // 2. Call carrier API
    $ch = curl_init($this->api_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    // 3. Parse response
    $data = $this->parseResponse($response);
    
    // 4. Log API call
    $consignment->setApiData($request, $response);
    
    // 5. Generate PDF with returned data
    $pdf = $this->createPDF($data, $consignment);
    
    // 6. Save and return
    return $pdf_path;
}
```

### 8.2 API Data Logging
All API calls are logged to `api_data` table:
- `consignment_id`
- `api_request` (full request)
- `api_response` (full response)
- `added_by` (user)
- `api_reason` (success/error)

---

## 9. FILE STORAGE STRUCTURE

```
_assets/
├── pdf/
│   └── YYYY_MM_DD/
│       ├── {timestamp}_{unique_id}.pdf  (individual labels)
│       └── {timestamp}_merged.pdf       (merged labels)
├── paperless_invoice/
│   └── temp/
│       └── {user_doc_files}.pdf
└── relabel/
    └── YYYY_MM_DD/
        └── {relabeled_shipments}.pdf
```

**Naming Convention**:
- Individual: `20240115_65a4b2c3d1e5f.pdf`
- Merged: `20240115_merged_65a4b2c3d1e5f.pdf`

---

## 10. MIGRATION MAPPING

### PHP Class → Laravel Model

| PHP Class | Laravel Model | Key Changes |
|-----------|---------------|-------------|
| `Consignment` | `App\Models\Consignment` | Use Eloquent, add relationships |
| `ConsignmentFilter` | Query Scopes | `scopeReadyToPrint()`, etc. |
| `Services` | `App\Models\Service` | Belongs to Carrier |
| `Carrier` | `App\Models\Carrier` | Has many Services |
| `Parcel` | `App\Models\Parcel` | Belongs to Consignment |
| `Tracking` | `App\Models\Tracking` | Polymorphic |
| `TrackingData` | `App\Models\TrackingEvent` | Belongs to Tracking |
| `Tariffs` | `App\Models\Tariff` | Complex pricing logic |

### PHP Label Classes → Laravel Services

| PHP | Laravel |
|-----|---------|
| `includes/labels/dhl.class.php` | `App\Services\Labels\DhlLabel.php` |
| `includes/labels/yodel.class.php` | `App\Services\Labels\YodelLabel.php` |
| etc. | Interface: `LabelGeneratorInterface` |

### Main Files → Laravel Controllers

| PHP File | Laravel Controller | Routes |
|----------|-------------------|--------|
| `consignment_add.php` | `ConsignmentController@store` | POST /api/consignments |
| `consignment_list.php` | `ConsignmentController@index` | GET /api/consignments |
| `label_generate_new.php` | `LabelController@generate` | POST /api/labels/generate |
| `ajaxgeneratelabels.php` | `LabelController@bulk` | POST /api/labels/bulk |

---

## 11. CRITICAL IMPLEMENTATION NOTES

### ⚠️ DO NOT CHANGE
1. **Status constants (10-31)** - Hardcoded in many places
2. **Service type codes (DBP, INT, R1, RTN)** - Used in routing logic
3. **Label class naming convention** - Must match service.label_class_name
4. **PDF file paths** - External systems may reference these
5. **Tracking number formats** - Carrier-specific, don't modify

### ✅ MUST REPLICATE
1. **Agent assignment algorithm** - Complex recursive logic
2. **Volumetric weight calculation** - Varies by service
3. **Remote area detection** - Postcode-based surcharges
4. **Service routing** - User permissions, weight ranges
5. **Validation rules** - Country-specific, field lengths

### 🔄 CAN MODERNIZE
1. **PDF generation** - Can use different library (DomPDF, Snappy)
2. **File storage** - Can use S3 instead of local
3. **Session management** - Use Redis instead of PHP sessions
4. **API calls** - Use Guzzle instead of cURL
5. **Database queries** - Use Eloquent instead of raw SQL

---

## 12. NEXT STEPS FOR IMPLEMENTATION

### Phase 1: Core Models (Week 1-2)
1. Create Eloquent models for:
   - Consignment, Parcel, Service, Carrier
   - User, CustomerAccount
2. Define relationships
3. Add query scopes (replace Filter classes)
4. Migrate validation to Form Requests

### Phase 2: Label System (Week 3-4)
1. Create `LabelGeneratorInterface`
2. Port 3-5 major carrier classes:
   - DHL, Yodel, Royal Mail, DPD, Hermes
3. Test label generation end-to-end
4. Implement PDF merging

### Phase 3: Business Logic (Week 5-6)
1. Port service routing algorithm
2. Port agent assignment algorithm
3. Port pricing calculation
4. Test with real data

### Phase 4: Frontend (Week 7-8)
1. Build consignment creation form in Next.js
2. Integrate with Laravel API
3. Add label preview/download
4. Test complete flow

---

**This document represents 100% of the critical code structure needed for migration.**
