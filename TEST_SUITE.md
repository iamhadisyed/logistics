# Test Suite Documentation

## Overview
Comprehensive test suite for the Daakia logistics backend application.

## Test Files Created

### Unit Tests (3 files)

#### 1. ConsignmentTest.php
**Location**: `tests/Unit/Models/ConsignmentTest.php`

**Tests**:
- ✅ Can create a consignment
- ✅ Validates required fields (address, city, contact, weight, description)
- ✅ Validates contact length (max 35 characters)
- ✅ Validates number of pieces (max 99)
- ✅ Calculates volumetric weight correctly
- ✅ Returns chargeable weight as max(actual, volumetric)
- ✅ Has correct status constants
- ✅ Can scope by status
- ✅ Can scope ready to print

**Total**: 9 test cases

#### 2. CarrierTest.php
**Location**: `tests/Unit/Models/CarrierTest.php`

**Tests**:
- ✅ Can create a carrier
- ✅ Can deactivate carrier and cascade to services
- ✅ Can activate carrier
- ✅ Can scope active carriers
- ✅ Can scope not deleted carriers
- ✅ Has many services relationship
- ✅ Can have parent carrier
- ✅ Can have sub-carriers

**Total**: 8 test cases

#### 3. PricingEngineTest.php
**Location**: `tests/Unit/Services/PricingEngineTest.php`

**Tests**:
- ✅ Calculates base price
- ✅ Applies fuel surcharge (percentage-based)
- ✅ Applies remote area surcharge
- ✅ Applies insurance fee (2% of value)
- ✅ Saves charges to database
- ✅ Deletes existing charges before saving new ones

**Total**: 6 test cases

### Feature Tests (3 files)

#### 4. ConsignmentControllerTest.php
**Location**: `tests/Feature/Api/ConsignmentControllerTest.php`

**Tests**:
- ✅ Can list consignments
- ✅ Can filter consignments by status
- ✅ Can create a consignment
- ✅ Validates required fields when creating
- ✅ Can show a consignment
- ✅ Can update a consignment
- ✅ Can delete a consignment (sets to RECYCLED status)
- ✅ Can bulk update consignments

**Total**: 8 test cases

#### 5. ServiceControllerTest.php
**Location**: `tests/Feature/Api/ServiceControllerTest.php`

**Tests**:
- ✅ Can list services
- ✅ Can filter services by carrier
- ✅ Can get available services for destination and weight
- ✅ Can show a service
- ✅ Can create a service
- ✅ Validates unique service code
- ✅ Can update a service

**Total**: 7 test cases

#### 6. CarrierControllerTest.php
**Location**: `tests/Feature/Api/CarrierControllerTest.php`

**Tests**:
- ✅ Can list carriers
- ✅ Can filter active carriers only
- ✅ Can show a carrier with services
- ✅ Can create a carrier
- ✅ Can update a carrier
- ✅ Can activate a carrier
- ✅ Can deactivate a carrier and cascade to services
- ✅ Can delete a carrier

**Total**: 8 test cases

## Model Factories Created

### 1. ConsignmentFactory.php
Generates realistic consignment data with:
- Receiver and sender addresses
- Parcel details (weight, dimensions, value)
- Status and tracking info

### 2. ServiceFactory.php
Generates service data with:
- Carrier relationship
- Weight ranges
- Label class name
- Pricing settings

### 3. CarrierFactory.php
Generates carrier data with:
- Company name
- Logo
- Status
- Configuration settings

## Running Tests

### Run All Tests
```bash
cd c:/daakia/dakia_backend01
php artisan test
```

### Run Specific Test File
```bash
php artisan test tests/Unit/Models/ConsignmentTest.php
php artisan test tests/Feature/Api/ConsignmentControllerTest.php
```

### Run Specific Test Method
```bash
php artisan test --filter it_validates_required_fields
```

### Run with Coverage
```bash
php artisan test --coverage
```

## Test Statistics

**Total Test Files**: 6
**Total Test Cases**: 46
**Unit Tests**: 23
**Feature Tests**: 23

## Coverage Areas

### Models
- ✅ Consignment (validation, calculations, relationships)
- ✅ Carrier (status management, cascade logic)
- ✅ Service (relationships, scopes)

### Controllers
- ✅ ConsignmentController (CRUD + bulk operations)
- ✅ ServiceController (service selection logic)
- ✅ CarrierController (status management with cascade)

### Services
- ✅ PricingEngine (price calculation with surcharges)

## Next Steps

### Additional Tests Needed
1. **RoutingService** - Test 4-step routing algorithm
2. **Label Generation** - Test label generator interface
3. **Validation** - Test country-specific validation rules
4. **Relationships** - Test all model relationships
5. **Scopes** - Test all query scopes

### Integration Tests
1. Complete flow: Create consignment → Calculate price → Generate label
2. Service selection based on routing rules
3. Agent assignment logic

## Notes

- All tests use `RefreshDatabase` trait for clean database state
- Factories generate realistic test data
- Feature tests include authentication via Sanctum
- Tests cover both happy paths and validation errors
