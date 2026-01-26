# Business Logic Reference - Legacy System Deep Dive

## Document Purpose
This document extracts the **actual business logic** from the legacy PHP codebase to serve as a blueprint for the new Laravel/Next.js implementation. Every algorithm, workflow, and business rule is documented here.

---

## 1. Consignment Creation Workflow

### File: `consignment_add.php` (3561 lines)

### Core Business Logic

#### 1.1 Consignment Save Process
**Function**: `Consignment::saveShipment($form_vars, $userId, $isApi, $userParams, $source, $shipmentUserId)`

**Key Steps**:
1. **Permission Check**: Verify if user can create shipments for other users
2. **Data Validation**: Validate all form fields (addresses, weights, dimensions, service)
3. **Item Details**: Save customs declaration items (for international shipments)
4. **Session Management**: Use `session_id()` to temporarily store data before consignment creation
5. **Service Routing**: Determine which carrier service to use based on origin/destination
6. **Pricing Calculation**: Calculate shipping cost based on weight, zone, and service
7. **Status Assignment**: Set initial status as `STATUS_READY_TO_PRINT`
8. **Return Output**: JSON response with success/error and consignment ID

**Critical Fields**:
- Sender: company, contact, address (line1-3), city, postcode, country, phone, email
- Receiver: Same as sender
- Parcel: weight, length, width, height, pieces, description
- Service: service_id, delivery_mode (D=Domestic, I=International, E=Europe Road, R=Return)
- Special: insurance, paperless_invoice, drop_off_location, COD amount

#### 1.2 Item Detail Management (Customs Declaration)
**Function**: `save_itemdetail` (AJAX)

**Purpose**: For international shipments, customs requires item-level details

**Fields Per Item**:
- `item_description`: Product description
- `item_sku`: Stock Keeping Unit
- `item_url`: Product URL (for verification)
- `item_qty`: Quantity
- `item_value`: Unit value in currency
- `item_weight`: Item weight
- `item_hscode`: Harmonized System Code (tariff classification)
- `item_country`: Country of manufacture (ISO code)

**Storage**: JSON-encoded array stored in `item_details` table linked to `parcel_count`

**Validation Rules**:
- Quantity, value, weight must be numeric
- URL must be valid format
- HS Code and country required for customs

#### 1.3 Service Selection Logic
**Function**: `get_country_services` (AJAX)

**Algorithm**:
```
1. Get user's account ID
2. Query `ServiceFilter::getUserAccountServices(accountId, fromCountry, toCountry, serviceType)`
3. Filter services by:
   - User's allowed services (from customized routing)
   - Origin country
   - Destination country  
   - Service type (D/I/E/R)
4. Return dropdown HTML with carrier logos
```

**Service Types**:
- `D` = Domestic (same country)
- `I` = International (worldwide)
- `E` = Europe Road (land transport within Europe)
- `R` = Return (reverse logistics)

#### 1.4 Drop-Off Location Integration
**Function**: `map_load` and `DROPOFF_SELECTED_SERVICE` (AJAX)

**Purpose**: For services requiring drop-off (e.g., UPS Access Point), find nearest locations

**Process**:
1. Get service's `label_class_name` from database
2. Load carrier-specific class (e.g., `ups.class.php`)
3. Call `$classObject->getDropOffLocation($postcode, $countryIso, $city)`
4. Display locations on map with opening hours
5. Save selected location to `dropoff_user_location` table
6. Include location details on label

**Data Stored**:
- Company name, address, postcode, coordinates (lat/lng)
- Opening hours (Mon-Sun)
- Branch ID (carrier's internal reference)

#### 1.5 Address Book Integration
**Function**: `handle_address_ajax` (AJAX)

**Purpose**: Reuse previously saved addresses

**Features**:
- Search by company, contact, phone, address, postcode
- Pagination and sorting
- Click to auto-fill sender/receiver fields
- Stored per user in `address` table

---

## 2. Label Generation System

### Files: `label_generate_new.php`, `ajaxgeneratelabels.php`, `bulkAjaxLabel.php`

### 2.1 Label Generation Workflow

**High-Level Process**:
```
1. User selects consignments (status = STATUS_READY_TO_PRINT)
2. Frontend calls label_generate_new.php
3. JavaScript makes AJAX calls to bulkAjaxLabel.php
4. For each service type:
   a. Group consignments by service
   b. Call Consignment::getInstantLabel() for each
   c. Generate individual PDF labels
   d. Merge PDFs into one file per service
5. Final merge of all service PDFs
6. Update consignment status to STATUS_LABEL_CREATED
7. Return merged PDF URL to user
```

### 2.2 Core Label Generation Function
**Function**: `Consignment::getInstantLabel($consignment)`

**Location**: Likely in `includes/mapping/consignment.class.php`

**Algorithm** (inferred from code):
```php
1. Get consignment service ID
2. Load service object: $service = new Services($consignment->getServiceId())
3. Get carrier's label class name: $className = $service->getLabelClassName()
4. Load carrier-specific label class:
   - File: includes/labels/{lowercase($className)}.class.php
   - Example: includes/labels/dhl.class.php, includes/labels/yodel.class.php
5. Instantiate class: $labelObj = new $className()
6. Call label generation method: $labelObj->generateLabel($consignment)
7. Save PDF to: _assets/pdf/YYYY_MM_DD/{unique_id}.pdf
8. Update consignment:
   - label_file = path to PDF
   - shipment_status = STATUS_LABEL_CREATED
   - awb = tracking number (if generated by carrier API)
9. Return: ['STATUS' => 'SUCCESS', 'LABEL_FILE' => $path]
```

### 2.3 Carrier-Specific Label Classes

**Pattern**: Each carrier has its own class in `includes/labels/`

**Found Classes** (from file search):
- `whitelabel.class.php` - Generic white-label format
- `baglabel.class.php` - Bag labels for manifesting
- `cn22.class.php` - CN22 customs form
- Plus carrier-specific: DHL, Yodel, UPS, Royal Mail, DPD, etc.

**Standard Methods** (expected in each class):
```php
class CarrierName {
    public function generateLabel($consignment) {
        // 1. Prepare data
        $sender = $consignment->getSenderAddress();
        $receiver = $consignment->getReceiverAddress();
        $service = $consignment->getService();
        
        // 2. Call carrier API (if real-time)
        $trackingNumber = $this->callCarrierAPI($consignment);
        
        // 3. Generate PDF using TCPDF
        $pdf = new TCPDF();
        // Add barcode, addresses, logo, etc.
        
        // 4. Save PDF
        $filename = $this->savePDF($pdf);
        
        // 5. Return path
        return $filename;
    }
    
    public function getDropOffLocation($postcode, $country, $city) {
        // Call carrier's location API
        // Return array of locations
    }
}
```

### 2.4 PDF Merging Logic
**Class**: `PDFMerger` (from `includes/labels/pdfmerger.class.php`)

**Usage**:
```php
$pdf = new PDFMerger();
foreach ($labelFiles as $file) {
    $pdf->addPDF($file, 'all'); // Add all pages
}
$pdf->merge('file', $outputPath); // Save merged PDF
```

**Purpose**: Combine multiple individual labels into one printable document

### 2.5 Label File Naming Convention
**Pattern**: `YYYYMMDD_{unique_id}.pdf`

**Storage Path**: `_assets/pdf/YYYY_MM_DD/`

**Example**: `_assets/pdf/2024_01_15/20240115_65a4b2c3d1e5f.pdf`

---

## 3. Service Routing & Pricing

### 3.1 Service Routing Logic

**Tables Involved**:
- `services` - Master list of all carrier services
- `customizedservicesrouting` - User-specific service assignments
- `userservicesrouting` - Custom routing rules per user
- `serviceagentmapping` - Which agent/carrier handles which service

**Routing Algorithm**:
```
1. User selects origin and destination countries
2. System queries customizedservicesrouting WHERE:
   - user_id = current user
   - status = 'active'
   - service supports origin->destination route
3. Filter by service_type (D/I/E/R)
4. Return available services with carrier logos
5. User selects service
6. System assigns agent/carrier based on serviceagentmapping
```

### 3.2 Pricing Calculation

**Function**: `get_pricing.php` (252,178 bytes - massive file!)

**Key Factors**:
1. **Weight Bands**: Different rates for weight ranges (0-1kg, 1-5kg, etc.)
2. **Zones**: Countries grouped into zones (Zone 1, Zone 2, etc.)
3. **Service Type**: Express vs. Standard vs. Economy
4. **Surcharges**:
   - Remote area surcharge
   - Fuel surcharge
   - Oversize surcharge
   - COD fee
   - Insurance fee
5. **User-Specific Rates**: Custom pricing per account

**Tables**:
- `tariffs` - Base pricing matrix
- `tariffs_pricing_rules` - Complex pricing rules
- `remotearea_charges_carrier` - Remote area fees
- `user_services_charges` - Custom user rates

---

## 4. Bagging & Manifesting

### Files: `add_bag.php`, `bagscan.php`, `country_bag.php`

### 4.1 Bagging Concept

**Purpose**: Group multiple consignments into physical bags for transport

**Workflow**:
1. Scan consignment barcodes
2. Assign to bag (bag has unique ID)
3. Close bag when full
4. Generate bag label
5. Bag goes to manifest

### 4.2 Manifest Creation

**Purpose**: Create shipping manifest for carrier pickup

**Process**:
1. Select bags or individual consignments
2. Group by carrier and destination
3. Generate manifest document (PDF)
4. Send manifest to carrier (email or API)
5. Update consignment status to STATUS_DISPATCHED

**Manifest Contains**:
- List of all HAWBs (consignment IDs)
- Total pieces, total weight
- Destination country
- Carrier reference number

---

## 5. Tracking System

### Files: `tracking.php`, `tracking_data.php`, `ops_bulk_tracking.php`

### 5.1 Tracking Number Generation

**Table**: `auto_tracking`

**Logic**:
- System pre-generates ranges of tracking numbers
- Format varies by carrier (e.g., OWE1234567890GB for domestic)
- Assigned sequentially when consignment is created
- Stored in `consignment.hawb` field

### 5.2 Tracking Events

**Table**: `tracking_data`

**Event Types**:
- Received
- In Transit
- Out for Delivery
- Delivered
- Exception (failed delivery)
- Returned

**Data Captured**:
- Event code
- Event description
- Location
- Timestamp
- Signature (for delivery)

### 5.3 Bulk Tracking Updates

**File**: `ops_bulk_tracking.php` (124,852 bytes)

**Purpose**: Import tracking updates from carrier files

**Process**:
1. Upload CSV/Excel from carrier
2. Parse file
3. Match tracking numbers to consignments
4. Insert tracking events
5. Send email notifications to customers

---

## 6. Invoicing & Billing

### Files: `invoice_generate_new.php`, `performainvoicepdf_multi.php`

### 6.1 Invoice Generation

**Trigger**: Manual or automatic (end of month)

**Process**:
1. Query all consignments for user in date range
2. Calculate charges per consignment:
   - Base shipping cost
   - Surcharges (remote area, fuel, etc.)
   - VAT/Tax
3. Group by service type
4. Generate PDF invoice
5. Send to customer email
6. Store in `invoices` table

### 6.2 Credit Notes

**File**: `credit_note.php`

**Purpose**: Issue refunds for failed deliveries or overcharges

**Process**:
1. Select consignments to credit
2. Calculate refund amount
3. Generate credit note PDF
4. Update account balance
5. Link to original invoice

---

## 7. Key Business Rules Extracted

### 7.1 Consignment Status Flow
```
1. STATUS_READY_TO_PRINT (initial)
2. STATUS_LABEL_CREATED (after label generation)
3. STATUS_RECEIVED (scanned at warehouse)
4. STATUS_DISPATCHED (sent to carrier)
5. STATUS_INTRANSIT (in carrier network)
6. STATUS_OUT_FOR_DELIVERY
7. STATUS_DELIVERED (final)
8. STATUS_RECYCLED (cancelled/deleted)
9. STATUS_INVALID (failed validation)
```

### 7.2 Weight & Dimension Rules
- Weight in KG (convert from LBS if needed)
- Dimensions in CM
- Volumetric weight = (L × W × H) / 5000
- Chargeable weight = MAX(actual weight, volumetric weight)

### 7.3 Remote Area Detection
**Function**: `FIND_REMOTE_AREA_POSTCODE`

**Logic**:
1. Extract numeric part of postcode
2. Query `remoteareas` table
3. If match found, apply surcharge
4. Flag consignment with `remote_charges = 1`

### 7.4 Insurance Calculation
- Optional per consignment
- Rate: typically 2-3% of declared value
- Minimum charge: £5
- Maximum coverage varies by carrier

---

## 8. Critical Implementation Notes

### 8.1 Label Generation is Carrier-Specific
**DO NOT** try to create a generic label generator. Each carrier has:
- Different label formats (sizes, layouts)
- Different required fields
- Different barcode formats
- Different API integrations

**Solution**: Keep the class-based approach where each carrier has its own `{Carrier}Label.class.php`

### 8.2 Session-Based Temporary Storage
The legacy system uses PHP sessions to store:
- Incomplete consignments
- Item details before save
- User preferences

**Migration**: Use Redis or database temp tables in Laravel

### 8.3 PDF Generation Library
Legacy uses **TCPDF** (included in `includes/3rdparty/tcpdf/`)

**Migration Options**:
- Laravel: Use `barryvdh/laravel-dompdf` or `spatie/laravel-pdf`
- Keep TCPDF if label templates are complex

### 8.4 File Storage Structure
```
_assets/
  pdf/
    YYYY_MM_DD/
      {consignment_labels}.pdf
  paperless_invoice/
    {invoice_pdfs}.pdf
  relabel/
    {relabeled_shipments}.pdf
```

**Migration**: Use Laravel Storage with S3 or local disk

---

## 9. Migration Priority Matrix

### Phase 1: Core Consignment Flow (CRITICAL)
1. ✅ Consignment creation form
2. ✅ Service selection based on routes
3. ✅ Address book integration
4. ✅ Item details for customs
5. ✅ Basic label generation (one carrier to start)

### Phase 2: Label System (HIGH)
1. Carrier-specific label classes
2. PDF merging
3. Bulk label generation
4. Drop-off location finder

### Phase 3: Operations (HIGH)
1. Bagging system
2. Manifest generation
3. Tracking updates
4. Bulk tracking import

### Phase 4: Finance (MEDIUM)
1. Pricing engine
2. Invoice generation
3. Credit notes
4. Payment integration

---

## 10. Code Patterns to Replicate

### 10.1 Dynamic Class Loading
**Legacy Pattern**:
```php
$className = $service->getLabelClassName(); // e.g., "DHL"
$file = "../includes/labels/" . strtolower($className) . ".class.php";
require_once($file);
$labelObj = new $className();
```

**Laravel Equivalent**:
```php
$className = "App\\Services\\Labels\\" . $service->label_class_name;
$labelObj = app($className);
$labelObj->generateLabel($consignment);
```

### 10.2 AJAX Response Format
**Legacy Pattern**:
```php
$output = ['STATUS' => 'SUCCESS', 'MESSAGE' => '...', 'DATA' => [...]];
echo json_encode($output);
die;
```

**Laravel Equivalent**:
```php
return response()->json([
    'status' => 'success',
    'message' => '...',
    'data' => [...]
]);
```

### 10.3 Filter Classes
**Legacy Pattern**: `ConsignmentFilter`, `ServiceFilter`, etc.

**Laravel Equivalent**: Use Query Scopes
```php
// In Consignment model
public function scopeReadyToPrint($query) {
    return $query->where('shipment_status', 'ready_to_print');
}

// Usage
Consignment::readyToPrint()->get();
```

---

## 11. Next Steps for Implementation

1. **Create Laravel Models** matching database schema
2. **Port Consignment::saveShipment()** to Laravel Controller
3. **Create one label class** (e.g., DHL) as proof of concept
4. **Build frontend form** in Next.js matching legacy functionality
5. **Test end-to-end**: Create consignment → Generate label → Download PDF
6. **Iterate**: Add more carriers, features, etc.

---

**This document should be treated as the SOURCE OF TRUTH for business logic during migration.**
