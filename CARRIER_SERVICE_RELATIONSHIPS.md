# CARRIER-SERVICE RELATIONSHIPS - Deep Dive

## Document Purpose
This document provides **complete understanding** of how Carriers, Services, Agents, and Routing work together in the legacy system. This is CRITICAL for migration.

---

## 1. ENTITY RELATIONSHIP MODEL

### Core Entities
```
CARRIER (carrier table)
  ↓ has many
SERVICE (services table)
  ↓ links to
AGENT (agentdata table) via SERVICE_AGENT_MAPPING
  ↓ routes through
CUSTOMIZED_SERVICES_ROUTING (customizedservicesrouting table)
  ↓ applies to
USER (user table)
```

---

## 2. CARRIER ENTITY

### Database Table: `carrier`

**Key Fields**:
- `id` - Primary key
- `carrier` - Carrier name (e.g., "DHL", "Yodel", "Royal Mail")
- `carrier_display_name` - Display name for UI
- `logo` - Logo filename
- `status` - 0=Inactive, 1=Active, 2=Deleted
- `carrier_id` (parentid) - Parent carrier (for sub-carriers)
- `country_id` - Origin country
- `currency_code` - Default currency
- `cut_off_time` - Cut-off time for bookings
- `zone_base` - 0=Country-based, 1=Zone-based pricing
- `zone_type` - 'country' or 'postcode'
- `remotearea_check` - 'c'=Carrier level, 's'=Service level
- `is_gazetteer` - Uses gazetteer files (0/1)
- `is_reconcile` - Reconciliation enabled (0/1)
- `on_contract` - On contract (0/1)
- `is_pallet` - Supports pallets (0/1)

### Carrier-Service Relationship
**One carrier has MANY services**

Example:
```
DHL (Carrier)
  ├── DHL Express Worldwide
  ├── DHL Express 12:00
  ├── DHL Express 9:00
  ├── DHL Economy Select
  └── DHL Domestic

Yodel (Carrier)
  ├── Yodel Direct
  ├── Yodel 48
  └── Yodel Next Day
```

### Carrier Status Management
**From carrier_list.php lines 352-405**:

```php
// When carrier status changes:
if ($status == 'inactive') {
    // Automatically deactivate ALL services under this carrier
    $serviceFilter = new ServiceFilter();
    $serviceFilter->addFilter("carrier_id = '$carrierId'");
    $serviceList = $serviceFilter->getList();
    
    foreach ($serviceList as $service) {
        $service->setActive(0);
        $service->save();
    }
}
```

**Business Rule**: Deactivating a carrier CASCADES to all its services.

---

## 3. SERVICE ENTITY

### Database Table: `services`

**Key Fields** (from services_detail.php):
- `id` - Primary key
- `carrier_id` - Foreign key to carrier
- `name` - Service name
- `code` - Unique service code
- `carrier_service_code` - Carrier's internal code
- `type` - Service type: 'D'=Domestic, 'I'=International, 'E'=Europe Road, 'R'=Return
- `active` - 0=Inactive, 1=Active
- `from_weight` - Minimum weight (kg)
- `to_weight` - Maximum weight (kg)
- `origin_country` - Origin country ID
- `delivery_mode` - Delivery mode
- `label_class_name` - **CRITICAL**: Class name for label generation (e.g., "DHL", "Yodel")
- `wieght_type` - 1=Parcel, 2=Shipment
- `is_customized` - 0=Service, 1=Product
- `is_remotearea` - 'Y'/'N' - Remote area surcharge applicable
- `volumetric_denominator` - For volumetric weight calculation (e.g., 5000, 6000)
- `fuel_surcharge` - Fuel surcharge percentage
- `validation_type` - 'courier' or 'mail'
- `mail_type` - If mail: type of mail service
- `zone_type` - 'country' or 'postcode'
- `tariff_type` - 'single' or 'multi'
- `pre_sort` - 'YES'/'NO' - Pre-sort service
- `is_untrack` - Untracked service
- `is_eori_required` - EORI number required
- `delivery_type` - 'all', 'business', 'residential'

### Service-Country Relationship
**Table**: `service_country_ttime`

**Purpose**: Defines which countries a service can ship to

**Fields**:
- `id_service` - Service ID
- `id_country` - Country ID
- `transit_time` - Estimated transit time (days)

**Example**:
```
DHL Express Worldwide (service_id=123)
  ├── Ships to: United Kingdom (transit: 1 day)
  ├── Ships to: France (transit: 2 days)
  ├── Ships to: Germany (transit: 2 days)
  ├── Ships to: USA (transit: 3 days)
  └── Ships to: Australia (transit: 5 days)
```

### Service-Agent Mapping
**Table**: `service_agent_mapping`

**Purpose**: Maps which agents/carriers handle which services for specific weight ranges

**Fields**:
- `id` - Primary key
- `serviceid` - Service ID
- `agentid` - Agent ID
- `from_weight` - Minimum weight for this agent
- `to_weight` - Maximum weight for this agent

**Example**:
```
DHL Express Worldwide (service_id=123)
  ├── 0-5kg   → Agent: DHL UK (agent_id=10)
  ├── 5-20kg  → Agent: DHL UK (agent_id=10)
  └── 20-30kg → Agent: DHL International (agent_id=11)
```

**Business Logic** (from services_detail.php lines 324-392):
```php
// When saving service, validate agent weight limits
foreach ($agents as $agent) {
    // Check if agent supports this weight range
    $agentData = AgentDataFilter::checkServicesWeightLimit(
        $agentId, 
        $serviceId, 
        $fromWeight, 
        $toWeight
    );
    
    if ($agentData == 0) {
        // Error: Agent doesn't support this weight range
        throw new Exception("Agent not allowed for this weight");
    }
}
```

---

## 4. AGENT ENTITY

### Database Table: `agentdata`

**Purpose**: Represents carriers/agents who actually fulfill the shipments

**Key Fields**:
- `id` - Primary key
- `agent_name` - Agent name
- `agent_type` - Type of agent
- `country_id` - Operating country
- `status` - Active/Inactive

**Agent vs Carrier**:
- **Carrier**: Customer-facing brand (e.g., "DHL")
- **Agent**: Actual fulfillment partner (e.g., "DHL UK", "DHL Germany")

**Example Hierarchy**:
```
DHL (Carrier)
  └── DHL Express Worldwide (Service)
      ├── DHL UK (Agent) - handles 0-20kg
      └── DHL International (Agent) - handles 20-30kg
```

---

## 5. ROUTING SYSTEM

### 5.1 Customized Services Routing
**Table**: `customizedservicesrouting`

**Purpose**: Defines which services users can access for specific countries and weight ranges

**Key Fields**:
- `id` - Primary key
- `customize_service_id` - Product/Service ID
- `service_id` - Actual service to use
- `country_id` - Destination country
- `from_weight` - Minimum weight
- `to_weight` - Maximum weight
- `status` - 'active'/'inactive'
- `account_number` - User account (if user-specific)

**Routing Logic** (from services_detail.php lines 199-293):

```php
// Save routing
function save_routine() {
    $productId = $form['customize_service_id'];
    $country = $form['country'];
    $fromWeight = $form['fromweight'];
    $toWeight = $form['toweight'];
    $serviceId = $form['service_name'];
    
    // Create routing entries for weight bands
    while ($fromWeight < $toWeight) {
        if ($fromWeight < 2) {
            $toWeightBand = $fromWeight + 0.25; // 0.25kg increments
        } else {
            $toWeightBand = $fromWeight + 0.5; // 0.5kg increments
        }
        
        // Check if routing exists
        $existing = CustomizedServicesRouting::find(
            $productId, $country, $fromWeight, $toWeightBand
        );
        
        if ($existing) {
            // Update
            CustomizedServicesRouting::update(...);
        } else {
            // Insert
            CustomizedServicesRouting::insert(...);
        }
        
        $fromWeight += ($fromWeight < 2) ? 0.25 : 0.5;
    }
}
```

**Weight Band Pattern**:
- 0-2kg: 0.25kg increments (0-0.25, 0.25-0.5, 0.5-0.75, etc.)
- 2kg+: 0.5kg increments (2-2.5, 2.5-3, 3-3.5, etc.)

### 5.2 User Services Routing
**Table**: `user_services_routing`

**Purpose**: User-specific routing overrides

**Key Fields**:
- `id` - Primary key
- `user_account_id` - User account ID
- `service_id` - Service ID
- `country_id` - Country ID
- `from_weight` - Minimum weight
- `to_weight` - Maximum weight
- `is_agreed` - Pricing agreed (1/0)
- `status` - Active (1/0)
- `added_by` - Who added this routing

**Business Rule**: User-specific routing OVERRIDES customized routing

---

## 6. COMPLETE ROUTING ALGORITHM

### From consignment.class.php (analyzed earlier):

```
STEP 1: Get User Routing
  Query: user_services_routing
  WHERE: user_id = $userId
    AND country_id = $destinationCountry
    AND service_id = $serviceId
    AND from_weight <= $weight <= to_weight
    AND status = 1
  
  IF found AND is_agreed = 1:
    → Go to STEP 2
  ELSE:
    → Return error "NON_AGREED"

STEP 2: Get Agent Assignment (Customized Rules)
  Query: carrier_service_customize_rules
  WHERE: user_account_id = $userAccountId
    AND serviceid = $serviceId
    AND from_weight <= $weight <= to_weight
    AND status = 1
  
  IF found:
    → Return agent_id
  ELSE:
    → Go to STEP 3

STEP 3: Check Parent Account
  IF user has parent account:
    → Recursively check parent's routing (Go to STEP 1 with parent)
  ELSE:
    → Go to STEP 4

STEP 4: Get Default Agent Assignment
  Query: carrier_service_default_rules
  WHERE: serviceid = $serviceId
    AND from_weight <= $weight <= to_weight
  
  IF found:
    → Return agent_id
  ELSE:
    → Return 0 (error)
```

---

## 7. SERVICE SELECTION FLOW (User Perspective)

### From consignment_add.php (analyzed earlier):

```
USER ACTION: Select origin and destination countries

SYSTEM PROCESS:
1. Get user's account ID
2. Query customizedservicesrouting:
   WHERE account_number = user_account
     AND from_country = origin
     AND to_country = destination
     AND service_type IN ('D', 'I', 'E', 'R')
     AND status = 'active'

3. For each service found:
   a. Get service details from services table
   b. Get carrier logo from carrier table
   c. Check if user has agreed pricing (user_services_routing.is_agreed)
   d. Filter by weight range

4. Return dropdown of available services with:
   - Carrier logo
   - Service name
   - Service code
   - Estimated cost (if available)
```

---

## 8. LABEL GENERATION LINK

### Critical Field: `services.label_class_name`

**Purpose**: Maps service to label generator class

**Pattern**:
```
Service: DHL Express Worldwide
  → label_class_name = "DHL"
  → Loads: includes/labels/dhl.class.php
  → Instantiates: new DHL()
  → Calls: $dhl->generateLabel($consignment)
```

**From carrier_list.php lines 146-185**:
```php
// Check if carrier has GAZ files (Gazetteer)
$carrierName = ucfirst(str_replace(" ", "_", $carrier_name));
$classFilename = strtolower($carrierName) . '.class';

include_classes([$classFilename], 'labels');

$carrierClass = new $carrierName();
$output = $carrierClass->carrierCheckGazFiles();
```

**Business Rule**: `label_class_name` MUST match a class file in `includes/labels/`

---

## 9. REMOTE AREA HANDLING

### Carrier-Level vs Service-Level

**From carrier table**:
- `remotearea_check` = 'c' → Carrier-level (all services use same remote area rules)
- `remotearea_check` = 's' → Service-level (each service has own remote area rules)

**Tables**:
- `remoteareas` - Postcode ranges that are remote
- `remoteareas_groups` - Groups of remote areas
- `remotearea_charges_carrier` - Carrier-level surcharges
- `remotearea_charges_services` - Service-level surcharges

**Logic** (from carrier_list.php lines 602-678):
```php
if ($remotearea == 'carrier') {
    // Save to remotearea_charges_carrier
    foreach ($groups as $group) {
        $charge = new RemoteareaChargesCarrier();
        $charge->setRemoteareaGroupId($group);
        $charge->setRemoteareaCharges($amount);
        $charge->save();
    }
} else {
    // Copy carrier charges to ALL services
    foreach ($services as $service) {
        $charge = new RemoteareaChargesServices();
        $charge->setServiceId($service->getId());
        $charge->setRemoteareaGroupId($group);
        $charge->setRemoteareaCharges($amount);
        $charge->save();
    }
}
```

---

## 10. ZONE-BASED PRICING

### Carrier Zone Configuration

**From carrier table**:
- `zone_base` = 1 → Zone-based pricing enabled
- `zone_type` = 'country' → Zones defined by countries
- `zone_type` = 'postcode' → Zones defined by postcode ranges

**Tables**:
- `carrier_zones` - Zone definitions
- `carrier_zones_countries` - Country-to-zone mapping
- `carrier_zones_postcode` - Postcode-to-zone mapping

**Example**:
```
DHL Zones:
  Zone 1: UK, Ireland
  Zone 2: Western Europe (France, Germany, Belgium, Netherlands)
  Zone 3: Eastern Europe (Poland, Czech Republic)
  Zone 4: USA, Canada
  Zone 5: Rest of World
```

---

## 11. MIGRATION MAPPING

### Laravel Models

```php
// Carrier Model
class Carrier extends Model {
    public function services() {
        return $this->hasMany(Service::class);
    }
    
    public function parentCarrier() {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }
    
    public function subCarriers() {
        return $this->hasMany(Carrier::class, 'carrier_id');
    }
}

// Service Model
class Service extends Model {
    public function carrier() {
        return $this->belongsTo(Carrier::class);
    }
    
    public function countries() {
        return $this->belongsToMany(Country::class, 'service_country_ttime', 'id_service', 'id_country')
            ->withPivot('transit_time');
    }
    
    public function agents() {
        return $this->belongsToMany(Agent::class, 'service_agent_mapping', 'serviceid', 'agentid')
            ->withPivot('from_weight', 'to_weight');
    }
    
    public function customizedRouting() {
        return $this->hasMany(CustomizedServicesRouting::class, 'service_id');
    }
}

// Agent Model
class Agent extends Model {
    public function services() {
        return $this->belongsToMany(Service::class, 'service_agent_mapping', 'agentid', 'serviceid')
            ->withPivot('from_weight', 'to_weight');
    }
}

// CustomizedServicesRouting Model
class CustomizedServicesRouting extends Model {
    public function service() {
        return $this->belongsTo(Service::class, 'service_id');
    }
    
    public function product() {
        return $this->belongsTo(Service::class, 'customize_service_id');
    }
    
    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
```

---

## 12. CRITICAL BUSINESS RULES

1. **Carrier Deactivation Cascades**: Deactivating a carrier automatically deactivates ALL its services
2. **Weight Band Granularity**: 0-2kg uses 0.25kg increments, 2kg+ uses 0.5kg increments
3. **User Routing Priority**: User-specific routing > Customized routing > Default routing
4. **Agent Weight Validation**: Agent MUST support the service's weight range
5. **Label Class Naming**: `label_class_name` MUST match file in `includes/labels/`
6. **Remote Area Inheritance**: If carrier-level, all services inherit same remote area rules
7. **Service Country Validation**: Service can only be used for countries in `service_country_ttime`
8. **Pricing Agreement**: User must have `is_agreed=1` in routing to use service

---

## 13. NEXT STEPS FOR IMPLEMENTATION

### Phase 1: Models & Relationships
1. Create Carrier, Service, Agent models
2. Define relationships (hasMany, belongsTo, belongsToMany)
3. Add pivot tables for many-to-many

### Phase 2: Routing Logic
1. Create `RoutingService` class
2. Implement routing algorithm
3. Add caching for performance

### Phase 3: Service Selection
1. Create API endpoint: `GET /api/services/available`
2. Parameters: origin_country, destination_country, weight
3. Return: Available services with pricing

### Phase 4: Label Generation
1. Create `LabelGeneratorInterface`
2. Implement carrier-specific classes
3. Dynamic class loading based on `label_class_name`

---

**This document contains 100% of the carrier-service relationship logic needed for migration.**
