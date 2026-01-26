# Full Legacy Database Walkthrough & Logic Analysis

This document provides a detailed analysis of all 938 tables in the legacy database, explaining their structure, business logic, and relationships.

## Table: `daakia.address`
- **Record Count:** 612
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| phone_number | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| email | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country | varchar | - | - |  |
| postcode | varchar | - | - |  |
| user_id | int | - | - |  |
| state | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 329,
    "phone_number": "7919115608",
    "company": "Dominic Jackson",
    "contact": "REGINA BERGELT",
    "email": null
}
... (more columns)
```

---

## Table: `daakia.agent_data`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_code | varchar | - | - |  |
| agent_name | varchar | - | - |  |
| active | tinyint | - | - |  |
| contact_name | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| country_id | int | - | - |  |
| county | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| telephone | varchar | - | - |  |
| mobile | varchar | - | - |  |
| fax | varchar | - | - |  |
| email | varchar | - | - |  |
| alternative_contact_1 | varchar | - | - |  |
| alternative1_telephone | varchar | - | - |  |
| alternative1_mobile | varchar | - | - |  |
| alternative1_fax | varchar | - | - |  |
| alternative1_email | varchar | - | - |  |
| alternative_contact_2 | varchar | - | - |  |
| alternative2_telephone | varchar | - | - |  |
| alternative2_mobile | varchar | - | - |  |
| alternative2_fax | varchar | - | - |  |
| alternative2_email | varchar | - | - |  |
| remarks | text | - | - |  |
| date_created | datetime | - | - |  |
| user_id | int | - | - |  |
| is_deleted | bit | - | - |  |
| logo | varchar | - | - |  |
| agent_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.agent_document`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "agent_id": 1,
    "document_id": 1,
    "document_name": "150780039822153082.pdf",
    "added_by": 148
}
... (more columns)
```

---

## Table: `daakia.agent_log`
- **Record Count:** 62
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-10-23 16:04:35",
    "ipaddress": "0",
    "log_id": 59
}
... (more columns)
```

---

## Table: `daakia.agent_restricted_postcode`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| agent_id | int | - | - |  |
| service_id | int | - | - |  |
| postcode_city | varchar | - | - |  |
| is_city | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11,
    "agent_id": 1,
    "service_id": 226,
    "postcode_city": "UB77RB",
    "is_city": 0
}
... (more columns)
```

---

## Table: `daakia.api_data`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| api_request | text | - | - |  |
| api_response | text | - | - |  |
| added_by | varchar | - | - |  |
| date_created | datetime | - | - |  |
| api_reason | varchar | - | - |  |
| type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.auto_tracking`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| next_number | int | - | - |  |
| range_end | int | - | - |  |
| increment_date | datetime | - | - |  |
| service_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.bag_scan_log`
- **Record Count:** 450
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2182,
    "logdate": "2018-12-05 11:11:57",
    "ipaddress": "3065225534",
    "log_id": 10082
}
... (more columns)
```

---

## Table: `daakia.bagging`
- **Record Count:** 592
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| bagnumber | varchar | - | - |  |
| date_created | datetime | - | - |  |
| csv | varchar | - | - |  |
| pdf | varchar | - | - |  |
| manifestid | int | - | - |  |
| account | varchar | - | - |  |
| user_id | int | - | - |  |
| manifest_pdf | varchar | - | - |  |
| bag_status | tinyint | - | - |  |
| date_updated | datetime | - | - |  |
| isdeleted | tinyint | - | - |  |
| service | varchar | - | - |  |
| serviceid | int | - | - |  |
| country | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| bag_type | varchar | - | - |  |
| actual_weight | decimal | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| pieces | int | - | - |  |
| weight | decimal | - | - |  |
| bag_label | varchar | - | - |  |
| bag_source_country_id | int | - | - |  |
| bag_source_warehouse_id | int | - | - |  |
| bag_destination_country_id | int | - | - |  |
| bag_destination_warehouse_id | int | - | - |  |
| is_closed | bit | - | - |  |
| closed_by | bigint | - | - |  |
| closed_date | datetime | - | - |  |
| reopen_by | bigint | - | - |  |
| reopen_date | datetime | - | - |  |
| bag_manifest | varchar | - | - |  |
| bag_value | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 10075,
    "bagnumber": "BHX10075248",
    "date_created": "2018-06-11 11:39:20",
    "csv": "",
    "pdf": ""
}
... (more columns)
```

---

## Table: `daakia.bagging_manifest_mapping`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| manifest_id | int | - | - |  |
| manifest_entity_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.bagging_services_mapping`
- **Record Count:** 8492
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| bag_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "bag_id": 10075,
    "service_id": 248
}
... (more columns)
```

---

## Table: `daakia.bagnumbers`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| consignment_id | varchar | - | - |  |
| parcel_id | int | - | - |  |
| tag_number | varchar | - | - |  |
| bag_number | int | - | - |  |
| service | varchar | - | - |  |
| value | varchar | - | - |  |
| weight | varchar | - | - |  |
| number_pieces | varchar | - | - |  |
| label_file | varchar | - | - |  |
| manifest_file | varchar | - | - |  |
| status | varchar | - | - |  |
| date_created | varchar | - | - |  |
| date_printed | varchar | - | - |  |
| flightnumber | varchar | - | - |  |
| flight_id | int | - | - |  |
| mawb | int | - | - |  |
| accountnumber | varchar | - | - |  |
| destination_addr | varchar | - | - |  |
| country | varchar | - | - |  |
| dispatchdate | varchar | - | - |  |
| mail_number | varchar | - | - |  |
| last_bag | varchar | - | - |  |
| flight_datetime | varchar | - | - |  |
| bag_type | varchar | - | - |  |
| is_track | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.box_info`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| box_number | varchar | - | - |  |
| box_size | varchar | - | - |  |
| box_weight | decimal | - | - |  |
| tracking_numbers | text | - | - |  |
| manifest_number | varchar | - | - |  |
| mawb_number | varchar | - | - |  |
| date_submitted | datetime | - | - |  |
| date_scanned | datetime | - | - |  |
| api_data | text | - | - |  |
| status | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.brazil_postcode`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state | varchar | - | - |  |
| locality | varchar | - | - |  |
| postcode | varchar | - | - |  |
| zone | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.brazil_state`
- **Record Count:** 8126
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state_code | varchar | - | - |  |
| state_name | varchar | - | - |  |
| city_code | varchar | - | - |  |
| city_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "state_code": "421",
    "state_name": "Acre",
    "city_code": "2495",
    "city_name": "Brasileia"
}
... (more columns)
```

---

## Table: `daakia.bulletins`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| heading | varchar | - | - |  |
| description | text | - | - |  |
| date_created | datetime | - | - |  |
| date_submitted | datetime | - | - |  |
| created_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "heading": "Test Bulletin",
    "description": "This is **_test_** description\ns\nd\ndd\n",
    "date_created": "2018-11-18 15:55:13",
    "date_submitted": "2018-11-12 00:00:00"
}
... (more columns)
```

---

## Table: `daakia.cacesa_routine`
- **Record Count:** 27978
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| postcode | varchar | - | - |  |
| agency | varchar | - | - |  |
| route_description | varchar | - | - |  |
| route_id | varchar | - | - |  |
| courier | varchar | - | - |  |
| routing | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "H",
    "postcode": "01216",
    "agency": "000909",
    "route_description": "09-MIRANDA DE EBRO"
}
... (more columns)
```

---

## Table: `daakia.cache`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| key | varchar | - | - |  |
| value | mediumtext | - | - |  |
| expiration | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.cache_locks`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| key | varchar | - | - |  |
| owner | varchar | - | - |  |
| expiration | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.carrier`
- **Record Count:** 2
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier | varchar | - | - |  |
| logo | varchar | - | - |  |
| cut_off_time | varchar | - | - |  |
| carrier_display_name | varchar | - | - |  |
| status | int | - | - |  |
| country_id | int | - | - |  |
| carrier_id | int | - | - |  |
| currency_code | varchar | - | - |  |
| remotearea_check | enum | - | - |  |
| zone_base | bit | - | - |  |
| zone_type | enum | - | - |  |
| on_contract | bit | - | - |  |
| is_gazetteer | tinyint | - | - |  |
| is_reconcile | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 16,
    "carrier": "Amazon",
    "logo": "amazon.png",
    "cut_off_time": "18:00",
    "carrier_display_name": "Amazon"
}
... (more columns)
```

---

## Table: `daakia.carrier_agent`
- **Record Count:** 0
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_number | varchar | - | - |  |
| name | varchar | - | - |  |
| company | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| api_username | varchar | - | - |  |
| api_password | varchar | - | - |  |
| ftp_host | varchar | - | - |  |
| ftp_username | varchar | - | - |  |
| ftp_password | varchar | - | - |  |
| integration_type | varchar | - | - |  |
| date_created | datetime | - | - |  |
| created_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.carrier_data_file_log`
- **Record Count:** 316
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| carrier_id | bigint | - | - |  |
| agent_id | bigint | - | - |  |
| file_name | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| run_number | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "agent_id": 1,
    "file_name": "UKD10161.001",
    "date_created": "2018-11-16 22:25:40"
}
... (more columns)
```

---

## Table: `daakia.carrier_document`
- **Record Count:** 18
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "carrier_id": 19,
    "document_id": 7,
    "document_name": "1508238498aramex.png",
    "added_by": 148
}
... (more columns)
```

---

## Table: `daakia.carrier_hubs`
- **Record Count:** 120
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| hub | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "hub": "52_INVERNESS",
    "routing_code": "52",
    "company": null
}
... (more columns)
```

---

## Table: `daakia.carrier_log`
- **Record Count:** 182
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-09-09 11:33:10",
    "ipaddress": "2006650821",
    "log_id": 16
}
... (more columns)
```

---

## Table: `daakia.carrier_service_customize_rules`
- **Record Count:** 44
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| user_account_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3973,
    "serviceid": 248,
    "agentid": 73,
    "user_account_id": 2294,
    "from_weight": "0.000"
}
... (more columns)
```

---

## Table: `daakia.carrier_service_default_rules`
- **Record Count:** 302
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| is_default | bit | - | - |  |
| agent_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 98,
    "serviceid": 1,
    "agentid": 4,
    "from_weight": "1.000",
    "to_weight": "2.000"
}
... (more columns)
```

---

## Table: `daakia.carrier_zones`
- **Record Count:** 4100
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| service_id | int | - | - |  |
| name | varchar | - | - |  |
| sort_order | int | - | - |  |
| status | tinyint | - | - |  |
| deleted | tinyint | - | - |  |
| date_added | datetime | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 194,
    "service_id": 0,
    "name": "UKMELL LOCAL ZONE",
    "sort_order": 7
}
... (more columns)
```

---

## Table: `daakia.carrier_zones_countries`
- **Record Count:** 5456
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| carrier_zone_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 13,
    "country_id": 225,
    "carrier_zone_id": 1
}
... (more columns)
```

---

## Table: `daakia.carrier_zones_postcode`
- **Record Count:** 10
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_zone_id | int | - | - |  |
| postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 0,
    "carrier_zone_id": 2812,
    "postcode": "Ub3 3nb"
}
... (more columns)
```

---

## Table: `daakia.carton_pallet_number`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| quantity | int | - | - |  |
| start_number | varchar | - | - |  |
| end_number | varchar | - | - |  |
| date_created | datetime | - | - |  |
| userid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.ch_shipments`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| reference | varchar | - | - |  |
| awb | varchar | - | - |  |
| account | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| postcode | varchar | - | - |  |
| product | varchar | - | - |  |
| service | varchar | - | - |  |
| bagnumber | varchar | - | - |  |
| mawb | varchar | - | - |  |
| charge_able_weight | decimal | - | - |  |
| total_charge | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment`
- **Record Count:** 52
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_id | int | - | - |  |
| user_id | int | - | - |  |
| service_id | int | - | - |  |
| customized_service_id | int | - | - |  |
| warehouse_user_id | int | - | - |  |
| warehouse_id | int | - | - |  |
| sales_pot_id | bigint | - | - |  |
| invoice_id | int | - | - |  |
| credit_id | int | - | - |  |
| is_invoiced | int | - | - |  |
| invoice_type | enum | - | - |  |
| shipment_status | int | - | - |  |
| shipment_type | enum | - | - |  |
| awb | varchar | - | - |  |
| consignment_status | varchar | - | - |  |
| return_awb | varchar | - | - |  |
| hawb | varchar | - | - |  |
| mawb | varchar | - | - |  |
| service_name | varchar | - | - |  |
| reference | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| date_label_created | int | - | - |  |
| date_booked | int | - | - |  |
| date_delivered | int | - | - |  |
| is_customer_manifested | int | - | - |  |
| booked_file_id | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| state | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_id | int | - | - |  |
| telephone | varchar | - | - |  |
| number_pieces | int | - | - |  |
| weight_type | varchar | - | - |  |
| weight | decimal | - | - |  |
| update_weight | decimal | - | - |  |
| fake_weight | decimal | - | - |  |
| charge_weight | decimal | - | - |  |
| vol_weight | decimal | - | - |  |
| vol_demonimator | int | - | - |  |
| hv_lv | enum | - | - |  |
| description | varchar | - | - |  |
| notes | varchar | - | - |  |
| value | decimal | - | - |  |
| currency | varchar | - | - |  |
| sender_name | varchar | - | - |  |
| username | varchar | - | - |  |
| sender_checked | int | - | - |  |
| message | varchar | - | - |  |
| sorter_image | varchar | - | - |  |
| label_file | varchar | - | - |  |
| is_doc | int | - | - |  |
| email | varchar | - | - |  |
| itemtype | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| routing_code_eur | varchar | - | - |  |
| other_routing_code | varchar | - | - |  |
| billing_hold | int | - | - |  |
| send_courier_data | int | - | - |  |
| remote_charges | int | - | - |  |
| reinvoices | int | - | - |  |
| optimus_sorter | int | - | - |  |
| full_pallet | int | - | - |  |
| half_pallet | int | - | - |  |
| quarter_pallet | int | - | - |  |
| date_scanned | datetime | - | - |  |
| consignment_type | enum | - | - |  |
| api_uuid | varchar | - | - |  |
| sender_company | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| sender_telephone | varchar | - | - |  |
| sender_address_line_1 | varchar | - | - |  |
| sender_address_line_2 | varchar | - | - |  |
| sender_address_line_3 | varchar | - | - |  |
| sender_city | varchar | - | - |  |
| sender_postcode | varchar | - | - |  |
| sender_country_id | int | - | - |  |
| sender_state | varchar | - | - |  |
| collection_date | date | - | - |  |
| collection_start_time | varchar | - | - |  |
| collection_end_time | varchar | - | - |  |
| collection_confirmation_no | varchar | - | - |  |
| created_from | enum | - | - |  |
| is_white_label | tinyint | - | - |  |
| is_dead_weight_chargable | tinyint | - | - |  |
| is_customer_billable | int | - | - |  |
| ioss_number | varchar | - | - |  |
| eori_number | varchar | - | - |  |
| vat_number | varchar | - | - |  |
| is_over_size_chargable | int | - | - |  |
| is_insured | int | - | - |  |
| destination_warehouse_id | int | - | - |  |
| consignment_seller | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 170442,
    "agent_id": 0,
    "user_id": 2341,
    "service_id": 226,
    "customized_service_id": 0
}
... (more columns)
```

---

## Table: `daakia.consignment_bagging_mapping`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| consignmentid | int | - | - |  |
| bagid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_billing_hold`
- **Record Count:** 12
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| reason_for_hold | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 19,
    "consignment_id": 106855,
    "user_account_id_from": 148,
    "user_account_id_to": 2294,
    "reason_for_hold": "test"
}
... (more columns)
```

---

## Table: `daakia.consignment_billing_hold_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| status | enum | - | - |  |
| reason | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_charges`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | int | - | - |  |
| account_id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_id | int | - | - |  |
| charge_type_id | int | - | - |  |
| agent_id | int | - | - |  |
| cost_type | enum | - | - |  |
| cost | decimal | - | - |  |
| cost_currency | varchar | - | - |  |
| cost_supplier_currency | decimal | - | - |  |
| supplier_currency | varchar | - | - |  |
| cost_company_currency | decimal | - | - |  |
| company_currency | varchar | - | - |  |
| description | varchar | - | - |  |
| changes_reference | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_charges_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_charges_types`
- **Record Count:** 60
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| charges_key | varchar | - | - |  |
| charge_type | enum | - | - |  |
| apply_per_kg | bit | - | - |  |
| is_extra_charge | bit | - | - |  |
| is_vat | bit | - | - |  |
| has_account_default_value | bit | - | - |  |
| is_replace_charges | bit | - | - |  |
| status | tinyint | - | - |  |
| is_delete | bit | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Basic Charges",
    "charges_key": "BASIC_CHARGES",
    "charge_type": "both",
    "apply_per_kg": 0
}
... (more columns)
```

---

## Table: `daakia.consignment_collection`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| sender_company | varchar | - | - |  |
| sender_contact | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| sender_address_line_1 | varchar | - | - |  |
| sender_address_line_2 | varchar | - | - |  |
| sender_address_line_3 | varchar | - | - |  |
| sender_city | varchar | - | - |  |
| sender_country_iso_code | char | - | - |  |
| sender_postcode | varchar | - | - |  |
| sender_telephone | varchar | - | - |  |
| date_collection | int | - | - |  |
| earliest_latest_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_details`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| custom_export_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_dropoff_mapping`
- **Record Count:** 16
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| dropoff_consignment_id | bigint | - | - |  |
| dispatch_consignment_id | bigint | - | - |  |
| dropoff_consignment_tracking | text | - | - |  |
| dispatch_consignment_tracking | text | - | - |  |
| parcel_tracking | text | - | - |  |
| added_by | bigint | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "dropoff_consignment_id": 160151,
    "dispatch_consignment_id": 160152,
    "dropoff_consignment_tracking": "1Z3985RW6806068797",
    "dispatch_consignment_tracking": "JD0002210161165769"
}
... (more columns)
```

---

## Table: `daakia.consignment_hold`
- **Record Count:** 2
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| comments | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| date_created | datetime | - | - |  |
| action | varchar | - | - |  |
| reason_tag | varchar | - | - |  |
| weight | varchar | - | - |  |
| width | varchar | - | - |  |
| height | varchar | - | - |  |
| length | varchar | - | - |  |
| volume | varchar | - | - |  |
| image | varchar | - | - |  |
| account | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "userid": null,
    "comments": null,
    "tracking_number": null,
    "date_created": null
}
... (more columns)
```

---

## Table: `daakia.consignment_hold_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| status | enum | - | - |  |
| reason | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_hscode`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| hscode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_pod`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignmentid | int | - | - |  |
| signature | varchar | - | - |  |
| pod_date | varchar | - | - |  |
| pod_image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.consignment_relabel`
- **Record Count:** 216
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| old_tracking_no | varchar | - | - |  |
| new_tracking_no | varchar | - | - |  |
| date_created | datetime | - | - |  |
| userid | int | - | - |  |
| old_consignment_data | text | - | - |  |
| old_parcel_tracking_no | text | - | - |  |
| old_new_tracking_mapping | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "consignment_id": 95962,
    "old_tracking_no": "JD0002210161134700",
    "new_tracking_no": "JD0002210161134700",
    "date_created": "2019-01-25 13:14:47"
}
... (more columns)
```

---

## Table: `daakia.consignment_status_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| parcel_id | bigint | - | - |  |
| old_status | varchar | - | - |  |
| new_status | varchar | - | - |  |
| message | text | - | - |  |
| added_by | bigint | - | - |  |
| date_added | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.correos_brazil_datafile`
- **Record Count:** 18
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 9,
    "file_name": "temp",
    "sent_date": null
}
... (more columns)
```

---

## Table: `daakia.cost_tariffs`
- **Record Count:** 20
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| courier_service_id | int | - | - |  |
| collection_rateband_id | int | - | - |  |
| destination_rateband_id | int | - | - |  |
| collection_postcode_group_id | int | - | - |  |
| destination_postcode_group_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| tariff_cost | decimal | - | - |  |
| unit_cost | decimal | - | - |  |
| unit_size | decimal | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| tariff_name | varchar | - | - |  |
| formula | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "courier_service_id": 79,
    "collection_rateband_id": 916,
    "destination_rateband_id": 916,
    "collection_postcode_group_id": 0
}
... (more columns)
```

---

## Table: `daakia.countries_link_ratebands`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| rateband_id | int | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.country`
- **Record Count:** 538
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| iso | char | - | - |  |
| name | varchar | - | - |  |
| region | varchar | - | - |  |
| postcode_required | enum | - | - |  |
| type | varchar | - | - |  |
| region_collection | varchar | - | - |  |
| numcode | int | - | - |  |
| allow_express | char | - | - |  |
| allow_classic | char | - | - |  |
| eu_country | char | - | - |  |
| shipping_advice | varchar | - | - |  |
| is_vatable | enum | - | - |  |
| vat_rate | decimal | - | - |  |
| printable_name | varchar | - | - |  |
| iso3 | char | - | - |  |
| export_flag | int | - | - |  |
| timezone_difference | int | - | - |  |
| has_postcodeq | char | - | - |  |
| has_subzonesq | char | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| vat_charged_flag | int | - | - |  |
| customs_flag | int | - | - |  |
| description | text | - | - |  |
| country_image | varchar | - | - |  |
| metakeywords | varchar | - | - |  |
| metadescription | varchar | - | - |  |
| pagetitle | varchar | - | - |  |
| countrybanner | varchar | - | - |  |
| opcode | varchar | - | - |  |
| iso_three | varchar | - | - |  |
| german_name | varchar | - | - |  |
| manifest_template | varchar | - | - |  |
| bag_template | varchar | - | - |  |
| bag_weight_limit | int | - | - |  |
| bag_low_value | int | - | - |  |
| currency_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "iso": "AF",
    "name": "Afghanistan",
    "region": "INT",
    "postcode_required": "NO"
}
... (more columns)
```

---

## Table: `daakia.cpost_manifest`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| manifest_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.credit_note`
- **Record Count:** 42
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| credit_note_number | varchar | - | - |  |
| user_account_id | int | - | - |  |
| invoice_type | enum | - | - |  |
| invoice_number | varchar | - | - |  |
| credit_note_type | enum | - | - |  |
| hawb | text | - | - |  |
| credit_note_heading | text | - | - |  |
| credit_date | datetime | - | - |  |
| net_amount | decimal | - | - |  |
| vat_amount | decimal | - | - |  |
| credit_total | decimal | - | - |  |
| credit_note_by | int | - | - |  |
| pdf | varchar | - | - |  |
| is_email | bit | - | - |  |
| is_read | bit | - | - |  |
| added_by | int | - | - |  |
| date_created | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| currency_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "credit_note_number": "",
    "user_account_id": 2286,
    "invoice_type": "MNI",
    "invoice_number": "MNI1019"
}
... (more columns)
```

---

## Table: `daakia.credit_note_details`
- **Record Count:** 50
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| credit_note_id | int | - | - |  |
| hawb | varchar | - | - |  |
| date_booked | datetime | - | - |  |
| reference | varchar | - | - |  |
| invoice_amount | decimal | - | - |  |
| chargeable_amount | decimal | - | - |  |
| credit_amount | decimal | - | - |  |
| description | varchar | - | - |  |
| is_vatable | enum | - | - |  |
| date_created | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| vat_amount | decimal | - | - |  |
| added_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "credit_note_id": 2,
    "hawb": "Test",
    "date_booked": "2018-11-25 00:00:00",
    "reference": "Test ref"
}
... (more columns)
```

---

## Table: `daakia.cs_log`
- **Record Count:** 16
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| internal_message | text | - | - |  |
| customer_message | text | - | - |  |
| cust_mail | varchar | - | - |  |
| agent_mail | varchar | - | - |  |
| date_created | datetime | - | - |  |
| reminder | varchar | - | - |  |
| reminder_expiry | datetime | - | - |  |
| userid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "consignment_id": 93154,
    "internal_message": "",
    "customer_message": "asdfasdf",
    "cust_mail": "No"
}
... (more columns)
```

---

## Table: `daakia.cs_notes`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| notes | text | - | - |  |
| created_by | bigint | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "notes": "Test Notes",
    "created_by": 2234,
    "date_created": "2018-11-12 17:12:49"
}
... (more columns)
```

---

## Table: `daakia.csv_import_template`
- **Record Count:** 34
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| template_name | varchar | - | - |  |
| template | text | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| update_by | bigint | - | - |  |
| update_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "user_id": 2191,
    "user_account_id": 2297,
    "template_name": "Tahir Finance Account Template",
    "template": "{\"date_added\":\"date_added\",\"receiver_country_iso\":\"receiver_country_iso\",\"service_code\":\"service_code\",\"order_reference\":\"order_reference\",\"receiver_contact\":\"receiver_contact\",\"receiver_address_line_1\":\"receiver_address_line_1\",\"receiver_city\":\"receiver_city\",\"receiver_postcode\":\"receiver_postcode\",\"description\":\"description\",\"parcels\":\"parcels\"}"
}
... (more columns)
```

---

## Table: `daakia.csv_tracking_template`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| template_name | varchar | - | - |  |
| template | text | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| update_by | bigint | - | - |  |
| update_date | datetime | - | - |  |
| service_id | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "user_id": 2324,
    "user_account_id": 2357,
    "template_name": "testtest",
    "template": "{\"Data Received\":\"Data Received\",\"Arrived at Sort Facility Hayes - GBR\":\"Arrived at Sort Facility Hayes - GBR\",\"Arrived at Sort Facility Hamburg - GBR\":\"Arrived at Sort Facility Hamburg - GBR\",\"Departed Facility in Hamburg - GBR\":\"Departed Facility in Hamburg - GBR\"}"
}
... (more columns)
```

---

## Table: `daakia.ctt_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.currency`
- **Record Count:** 180
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| currencyname | varchar | - | - |  |
| leftsymbol | varchar | - | - |  |
| rightsymbol | varchar | - | - |  |
| isdefault | tinyint | - | - |  |
| currencyexchangerate | decimal | - | - |  |
| isactive | tinyint | - | - |  |
| clientdisplay | tinyint | - | - |  |
| currencyid | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "currencyname": "US Dollar",
    "leftsymbol": "$",
    "rightsymbol": "USD",
    "isdefault": 0
}
... (more columns)
```

---

## Table: `daakia.currency_temp`
- **Record Count:** 192
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| country | varchar | - | - |  |
| currency | varchar | - | - |  |
| code | varchar | - | - |  |
| symbol | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "country": "Albania",
    "currency": "Leke",
    "code": "ALL",
    "symbol": "Lek"
}
... (more columns)
```

---

## Table: `daakia.customer_account`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| active_flag | bit | - | - |  |
| company | varchar | - | - |  |
| full_name | varchar | - | - |  |
| return_address | varchar | - | - |  |
| sms_dpd | bit | - | - |  |
| user_service_type | enum | - | - |  |
| parentid | int | - | - |  |
| phone | varchar | - | - |  |
| logo | varchar | - | - |  |
| instant_label | bit | - | - |  |
| country | varchar | - | - |  |
| country_id | int | - | - |  |
| tracking_api_access | bit | - | - |  |
| import_data_csv | bit | - | - |  |
| proforma | bit | - | - |  |
| add_tracking | bit | - | - |  |
| collection | bit | - | - |  |
| default_description | varchar | - | - |  |
| default_notes | varchar | - | - |  |
| default_weight | decimal | - | - |  |
| payment_term | text | - | - |  |
| query_term | text | - | - |  |
| vat_number | varchar | - | - |  |
| billing_currency | varchar | - | - |  |
| vat_chargable | bit | - | - |  |
| vat_value | decimal | - | - |  |
| allow_remote_area | bit | - | - |  |
| telephone | varchar | - | - |  |
| billing_address | varchar | - | - |  |
| date_dispatch | bit | - | - |  |
| is_product | varchar | - | - |  |
| profile_image | varchar | - | - |  |
| send_courier_data | bit | - | - |  |
| archive_server | bit | - | - |  |
| credit_check | bit | - | - |  |
| tariff_agreed | bit | - | - |  |
| sales_person | varchar | - | - |  |
| scan_document | text | - | - |  |
| data_entry | bit | - | - |  |
| bank_account_title | varchar | - | - |  |
| bank_sortcode | varchar | - | - |  |
| bank_account_number | varchar | - | - |  |
| bank_branch_address | varchar | - | - |  |
| trade_name_i | varchar | - | - |  |
| trade_address_i | varchar | - | - |  |
| trade_email_i | varchar | - | - |  |
| trade_phone_i | varchar | - | - |  |
| trade_name_ii | varchar | - | - |  |
| trade_address_ii | varchar | - | - |  |
| trade_email_ii | varchar | - | - |  |
| trade_phone_ii | varchar | - | - |  |
| reg_number | varchar | - | - |  |
| reg_address | varchar | - | - |  |
| reg_postcode | varchar | - | - |  |
| reg_country | varchar | - | - |  |
| sale_agent | varchar | - | - |  |
| sale_date | datetime | - | - |  |
| fuel_charges | decimal | - | - |  |
| warehouse_id | int | - | - |  |
| user_signature | text | - | - |  |
| is_fuelcharges_include | bit | - | - |  |
| is_prepaid | bit | - | - |  |
| return_label | bit | - | - |  |
| finalmile_over_label | bit | - | - |  |
| request_manifest_collection | bit | - | - |  |
| create_pre_alert | bit | - | - |  |
| is_employee | bit | - | - |  |
| invoice_bank_details_id | int | - | - |  |
| check_list_account_form | bit | - | - |  |
| check_list_credit_check | bit | - | - |  |
| check_list_t_cs | bit | - | - |  |
| check_list_tariff_agreed | bit | - | - |  |
| check_list_sales_pot | bit | - | - |  |
| sales_pot_time_period | int | - | - |  |
| sales_pot_percentage | decimal | - | - |  |
| last_login_date | timestamp | - | - |  |
| invalid_login_count | int | - | - |  |
| token | varchar | - | - |  |
| token_updated | timestamp | - | - |  |
| lock_time | timestamp | - | - |  |
| opearation_manifest | bit | - | - |  |
| own_tariff | bit | - | - |  |
| user_warehouse | enum | - | - |  |
| api_key | varchar | - | - |  |
| api_secert | varchar | - | - |  |
| api_date | datetime | - | - |  |
| bagging | bit | - | - |  |
| retail_customer | bit | - | - |  |
| show_price | bit | - | - |  |
| sales_rate | decimal | - | - |  |
| collection_add_line_1 | varchar | - | - |  |
| collection_add_line_2 | varchar | - | - |  |
| collection_add_line_3 | varchar | - | - |  |
| collection_city | varchar | - | - |  |
| collection_postcode | varchar | - | - |  |
| collection_country | varchar | - | - |  |
| theme_id | int | - | - |  |
| user_code | int | - | - |  |
| website_link | varchar | - | - |  |
| allow_return_email | bit | - | - |  |
| default_lang | varchar | - | - |  |
| credit_limit | decimal | - | - |  |
| invoice_period | enum | - | - |  |
| label_price | decimal | - | - |  |
| discount | decimal | - | - |  |
| account_code | varchar | - | - |  |
| paypal_email | varchar | - | - |  |
| paypal_currency | varchar | - | - |  |
| email | varchar | - | - |  |
| paypal_client_secret | varchar | - | - |  |
| alternative_email | varchar | - | - |  |
| billing_email | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| paypal_client_id | varchar | - | - |  |
| invoice_template_id | bigint | - | - |  |
| send_tracking_data | bit | - | - |  |
| billing_contact | varchar | - | - |  |
| ftp_shipment_upload | int | - | - |  |
| balance_alert_percentage | int | - | - |  |
| commission_break_event_account_amount | int | - | - |  |
| tracking_order_prefix | varchar | - | - |  |
| return_shipment_allow | int | - | - |  |
| account_balance | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.customized_services_routing`
- **Record Count:** 76
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | int | - | - |  |
| customize_service_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 15658,
    "country_id": 150,
    "from_weight": "20.50",
    "to_weight": "20.75",
    "status": 1
}
... (more columns)
```

---

## Table: `daakia.customized_services_routing_log`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| message | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.customized_user_services_routing`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| user_account_id | int | - | - |  |
| routing_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.cz_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.czint_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.department`
- **Record Count:** 14
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| department_code | varchar | - | - |  |
| description | varchar | - | - |  |
| department_head | bigint | - | - |  |
| isactive | tinyint | - | - |  |
| isdeleted | tinyint | - | - |  |
| addedby | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | bigint | - | - |  |
| updated_on | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Accounts",
    "department_code": "ACC",
    "description": "Accounts & Finance",
    "department_head": 58
}
... (more columns)
```

---

## Table: `daakia.deutschepost_dhl_streetcode`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| street | varchar | - | - |  |
| zipcode | varchar | - | - |  |
| street_code | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.deutschepostdhl_cargo_code`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| start_postcode | int | - | - |  |
| end_postcode | int | - | - |  |
| cargo_code | int | - | - |  |
| municipality_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "start_postcode": 1000,
    "end_postcode": 1999,
    "cargo_code": 1,
    "municipality_name": "Ottendorf-Okrilla"
}
... (more columns)
```

---

## Table: `daakia.document_type`
- **Record Count:** 34
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| document_name | varchar | - | - |  |
| description | varchar | - | - |  |
| document_type | enum | - | - |  |
| is_active | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |
| is_delete | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "document_name": "T & Cs",
    "description": "T & Cs",
    "document_type": "company_contract",
    "is_active": "1"
}
... (more columns)
```

---

## Table: `daakia.domestic`
- **Record Count:** 22
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode_sector | varchar | - | - |  |
| dpd_depot | varchar | - | - |  |
| dpd_services_group | varchar | - | - |  |
| dpd_offshore_zone | varchar | - | - |  |
| timeslots_code | varchar | - | - |  |
| cluster | varchar | - | - |  |
| ilk_depot | varchar | - | - |  |
| ilk_services_group | varchar | - | - |  |
| ilk_offshore_zone | varchar | - | - |  |
| ilk_alternate_service | varchar | - | - |  |
| new_postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11990,
    "postcode_sector": "ZE1 9",
    "dpd_depot": "0082",
    "dpd_services_group": "7",
    "dpd_offshore_zone": "3221"
}
... (more columns)
```

---

## Table: `daakia.domestic_day_file`
- **Record Count:** 1410
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": "13147",
    "file_name": "UKD57356.147",
    "sent_date": "2016-09-04 19:00:09"
}
... (more columns)
```

---

## Table: `daakia.dpd_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.dpdgroups`
- **Record Count:** 158
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| lookup_code | varchar | - | - |  |
| list_of_available_services | varchar | - | - |  |
| Business | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "lookup_code": "1",
    "list_of_available_services": "000000000-010000000-000000000-010000000-000000000-010000000-000000000-010000000-010000000-000000000-",
    "Business": "D"
}
... (more columns)
```

---

## Table: `daakia.dropoff_user_location`
- **Record Count:** 58
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| service_id | int | - | - |  |
| user_id | bigint | - | - |  |
| companyname | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country | varchar | - | - |  |
| telephone | varchar | - | - |  |
| mon | varchar | - | - |  |
| tue | varchar | - | - |  |
| wed | varchar | - | - |  |
| thu | varchar | - | - |  |
| fri | varchar | - | - |  |
| sat | varchar | - | - |  |
| sun | varchar | - | - |  |
| lat | varchar | - | - |  |
| lng | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 29,
    "service_id": 12,
    "user_id": 58,
    "companyname": "COLNBROOK PHARMACY",
    "address_line_1": "36 HIGH STREET"
}
... (more columns)
```

---

## Table: `daakia.dx_routing`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| district | varchar | - | - |  |
| sector | varchar | - | - |  |
| depot | varchar | - | - |  |
| depotid | varchar | - | - |  |
| region_id | varchar | - | - |  |
| delivery_method | varchar | - | - |  |
| delivery_method_id | varchar | - | - |  |
| delivery_method_description | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.emailtemplate`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| emailtemplateid | int | - | - |  |
| title | varchar | - | - |  |
| shortkey | varchar | - | - |  |
| content | text | - | - |  |
| isactive | tinyint | - | - |  |
| createdon | timestamp | - | - |  |
| isdeleted | tinyint | - | - |  |
| type | varchar | - | - |  |
| pagetitle | varchar | - | - |  |
| metatitle | varchar | - | - |  |
| metadescription | varchar | - | - |  |
| metakeywords | varchar | - | - |  |
| sorder | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.estimate_delivery_timing`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| from_rateband | int | - | - |  |
| to_rateband | int | - | - |  |
| date_created | datetime | - | - |  |
| created_by | varchar | - | - |  |
| status | enum | - | - |  |
| delivery_timing | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.euro_day_file`
- **Record Count:** 200
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_name": "COSSW46E_PreAdvice3_000000001",
    "sent_date": "2018-11-22 17:21:15"
}
... (more columns)
```

---

## Table: `daakia.failed_jobs`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| uuid | varchar | - | - |  |
| connection | text | - | - |  |
| queue | text | - | - |  |
| payload | longtext | - | - |  |
| exception | longtext | - | - |  |
| failed_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.fftin_file`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| label_link | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.flight_info`
- **Record Count:** 64
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| flight_number | varchar | - | - |  |
| country_id | int | - | - |  |
| destination_country_id | int | - | - |  |
| date_created | datetime | - | - |  |
| current_status | enum | - | - |  |
| status | enum | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| signature | varchar | - | - |  |
| carrier | varchar | - | - |  |
| carriage_value | decimal | - | - |  |
| custom_value | decimal | - | - |  |
| insurance_amount | decimal | - | - |  |
| currency | varchar | - | - |  |
| connecting_flight_number | varchar | - | - |  |
| weight_type | varchar | - | - |  |
| rate_charge | varchar | - | - |  |
| iata_code | varchar | - | - |  |
| departure_airport | varchar | - | - |  |
| phone_number | varchar | - | - |  |
| shipper_co | varchar | - | - |  |
| consignee_co | varchar | - | - |  |
| arrival_airport | varchar | - | - |  |
| account_id | int | - | - |  |
| shippers_name | varchar | - | - |  |
| shippers_addressline1 | varchar | - | - |  |
| shippers_addressline2 | varchar | - | - |  |
| accounting_reference | varchar | - | - |  |
| reference | varchar | - | - |  |
| rate_change | varchar | - | - |  |
| low_value_manifest | varchar | - | - |  |
| high_value_manifest | varchar | - | - |  |
| invoice | varchar | - | - |  |
| files_hv | varchar | - | - |  |
| hscodes | varchar | - | - |  |
| etd | varchar | - | - |  |
| eta | varchar | - | - |  |
| shed | varchar | - | - |  |
| files_lv | varchar | - | - |  |
| cleared | varchar | - | - |  |
| comments | varchar | - | - |  |
| weight | varchar | - | - |  |
| pieces | varchar | - | - |  |
| created_by | bigint | - | - |  |
| is_delete | tinyint | - | - |  |
| is_closed | tinyint | - | - |  |
| account_number | varchar | - | - |  |
| airway_bill | varchar | - | - |  |
| company | varchar | - | - |  |
| currancy | varchar | - | - |  |
| files | varchar | - | - |  |
| destination_company | varchar | - | - |  |
| destination_phone_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "flight_number": "PK 0909",
    "country_id": 225,
    "destination_country_id": 225,
    "date_created": null
}
... (more columns)
```

---

## Table: `daakia.flight_mapping`
- **Record Count:** 82
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| flight_info_id | int | - | - |  |
| flight_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| mawb_id | int | - | - |  |
| is_delete | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "flight_info_id": 1,
    "flight_number": "",
    "mawb": "",
    "mawb_id": 7
}
... (more columns)
```

---

## Table: `daakia.forget_password_request`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_name | varchar | - | - |  |
| ip_address | varchar | - | - |  |
| user_agent | varchar | - | - |  |
| token | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| date_expire | datetime | - | - |  |
| is_expire | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.grouphaspermissions`
- **Record Count:** 3844
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| group_id | int | - | - |  |
| perm_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 25,
    "group_id": 19,
    "perm_id": 1
}
... (more columns)
```

---

## Table: `daakia.groups`
- **Record Count:** 102
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| group_id | int | - | - |  |
| group_name | varchar | - | - |  |
| group_slug | varchar | - | - |  |
| group_desc | varchar | - | - |  |
| group_type | enum | - | - |  |
| is_active | tinyint | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| is_deleted | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "group_id": 17,
    "group_name": "Admin",
    "group_slug": "admin",
    "group_desc": "Admin",
    "group_type": "client"
}
... (more columns)
```

---

## Table: `daakia.groups_log`
- **Record Count:** 442
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-08-07 16:21:43",
    "ipaddress": "656769484",
    "log_id": 25
}
... (more columns)
```

---

## Table: `daakia.hawb_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| status | varchar | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.helpdesk_ticket`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| ticket_code | varchar | - | - |  |
| department_id | int | - | - |  |
| priority | enum | - | - |  |
| subject | varchar | - | - |  |
| status | enum | - | - |  |
| addedby | int | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.helpdesk_ticket_message`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| ticketid | bigint | - | - |  |
| message | varchar | - | - |  |
| attachment | varchar | - | - |  |
| addedby | int | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.hermes_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.hermes_postcode_record`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| fullpostcode | varchar | - | - |  |
| pos_pcd_postcode_excluded_indicator | char | - | - |  |
| sort_level_key | varchar | - | - |  |
| next_day_service | char | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.imcp`
- **Record Count:** 3706
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| opcode | varchar | - | - |  |
| imcpcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "opcode": "AEA",
    "imcpcode": "AEAUHA"
}
... (more columns)
```

---

## Table: `daakia.import_csv_consignment_temp`
- **Record Count:** 42
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| date_added | date | - | - |  |
| shipper_country_iso | varchar | - | - |  |
| receiver_country_iso | varchar | - | - |  |
| service_code | varchar | - | - |  |
| order_reference | varchar | - | - |  |
| shipper_company | varchar | - | - |  |
| shipper_contact | varchar | - | - |  |
| shipper_email | varchar | - | - |  |
| shipper_telephone | varchar | - | - |  |
| shipper_address_line_1 | varchar | - | - |  |
| shipper_address_line_2 | varchar | - | - |  |
| shipper_address_line_3 | varchar | - | - |  |
| shipper_city | varchar | - | - |  |
| shipper_state | varchar | - | - |  |
| shipper_postcode | varchar | - | - |  |
| receiver_company | varchar | - | - |  |
| receiver_contact | varchar | - | - |  |
| receiver_email | varchar | - | - |  |
| receiver_telephone | varchar | - | - |  |
| receiver_address_line_1 | varchar | - | - |  |
| receiver_address_line_2 | varchar | - | - |  |
| receiver_address_line_3 | varchar | - | - |  |
| receiver_city | varchar | - | - |  |
| receiver_state | varchar | - | - |  |
| receiver_postcode | varchar | - | - |  |
| reference | varchar | - | - |  |
| items_value | decimal | - | - |  |
| items_currency | varchar | - | - |  |
| item_type | varchar | - | - |  |
| note | text | - | - |  |
| description | text | - | - |  |
| bag_number | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| mawb_number | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| status | enum | - | - |  |
| is_complete | enum | - | - |  |
| batch_number | varchar | - | - |  |
| user_id | bigint | - | - |  |
| message | text | - | - |  |
| weight | text | - | - |  |
| length | text | - | - |  |
| height | text | - | - |  |
| width | text | - | - |  |
| itemvalue | text | - | - |  |
| parcel_item_desc | varchar | - | - |  |
| parcel_item_sku | varchar | - | - |  |
| parcel_item_url | varchar | - | - |  |
| parcel_item_quantity | int | - | - |  |
| parcel_item_value | decimal | - | - |  |
| parcel_item_weight | decimal | - | - |  |
| parcel_item_hs_code | varchar | - | - |  |
| parcel_item_manufacture_country | varchar | - | - |  |
| eori_number | varchar | - | - |  |
| vat_number | varchar | - | - |  |
| ioss_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 227,
    "date_added": "2024-03-03",
    "shipper_country_iso": "GB",
    "receiver_country_iso": "GB",
    "service_code": "AMZ002"
}
... (more columns)
```

---

## Table: `daakia.import_csv_tmp`
- **Record Count:** 250
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| account | varchar | - | - |  |
| hawb | varchar | - | - |  |
| service | varchar | - | - |  |
| service_code | varchar | - | - |  |
| reference | varchar | - | - |  |
| date_submitted | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line1 | varchar | - | - |  |
| address_line2 | varchar | - | - |  |
| address_line3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country | varchar | - | - |  |
| post_code | varchar | - | - |  |
| telephone | varchar | - | - |  |
| number_of_pieces | varchar | - | - |  |
| weight | varchar | - | - |  |
| description | varchar | - | - |  |
| value | varchar | - | - |  |
| currency | varchar | - | - |  |
| notes | varchar | - | - |  |
| routing_non_routing | varchar | - | - |  |
| full_pallet | varchar | - | - |  |
| half_pallet | varchar | - | - |  |
| quarter_pallet | varchar | - | - |  |
| all_weight | varchar | - | - |  |
| width | varchar | - | - |  |
| heigh | varchar | - | - |  |
| length | varchar | - | - |  |
| item_type | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| email | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| bag_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| is_complete | tinyint | - | - |  |
| status | tinyint | - | - |  |
| message | text | - | - |  |
| batch_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 113479,
    "account": "cltst",
    "hawb": "",
    "service": "",
    "service_code": "3HPA"
}
... (more columns)
```

---

## Table: `daakia.international`
- **Record Count:** 108
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| iata_country_code | char | - | - |  |
| zipcode_from | varchar | - | - |  |
| zipcode_to | varchar | - | - |  |
| air_express_depot | varchar | - | - |  |
| air_express_osort | varchar | - | - |  |
| air_express_dsort | varchar | - | - |  |
| dpd_classic_deport | varchar | - | - |  |
| dpd_classic_osort | varchar | - | - |  |
| dpd_classic_dsort | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1075616,
    "iata_country_code": "SK",
    "zipcode_from": "99080",
    "zipcode_to": "99080",
    "air_express_depot": "1503-NSK"
}
... (more columns)
```

---

## Table: `daakia.invoice_bank_details`
- **Record Count:** 16
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| account_title | varchar | - | - |  |
| account_sortcode | varchar | - | - |  |
| account_number | int | - | - |  |
| account_iban | varchar | - | - |  |
| bank_name | varchar | - | - |  |
| bank_branch | varchar | - | - |  |
| bank_address | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| status | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "user_account_id": 148,
    "account_title": "testing here",
    "account_sortcode": "2",
    "account_number": 3
}
... (more columns)
```

---

## Table: `daakia.invoice_detail`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| charges_detail | text | - | - |  |
| total | int | - | - |  |
| vat | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.invoice_detail_backup`
- **Record Count:** 276
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| hawb | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| remote_area_charge | decimal | - | - |  |
| on_farword_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| extra | decimal | - | - |  |
| hv | decimal | - | - |  |
| amount | decimal | - | - |  |
| agent_basic_charges | decimal | - | - |  |
| agent_fuel_charges | decimal | - | - |  |
| agent_additional_charges | decimal | - | - |  |
| agent_remote_area_charge | decimal | - | - |  |
| agent_on_farword_charges | decimal | - | - |  |
| agent_ndx | decimal | - | - |  |
| agent_ddp | decimal | - | - |  |
| agent_extra | decimal | - | - |  |
| agent_amount | decimal | - | - |  |
| agent_linehaul_cost | decimal | - | - |  |
| agent_handling_charges | decimal | - | - |  |
| reference | varchar | - | - |  |
| quotation_id | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1939667,
    "consignment_id": 10971723,
    "invoice_no": "",
    "hawb": "IT14694510012369",
    "basic_charges": "6.14"
}
... (more columns)
```

---

## Table: `daakia.invoice_detail_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.invoice_extra_charges`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| invoice_detail_id | int | - | - |  |
| charge_type_id | int | - | - |  |
| agent_id | int | - | - |  |
| cost_type | enum | - | - |  |
| cost | decimal | - | - |  |
| description | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.invoice_extra_charges_types`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| isactive | tinyint | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.invoice_templates`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| image | varchar | - | - |  |
| invoice_function | varchar | - | - |  |
| summary_invoice_function | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Default Template",
    "image": "default_template.png",
    "invoice_function": "SavePDFFile",
    "summary_invoice_function": "SaveSummaryInvoicePDFFile"
}
... (more columns)
```

---

## Table: `daakia.invoices_number_range`
- **Record Count:** 114
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| is_default | tinyint | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| user_account_id | int | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| range_type | enum | - | - |  |
| date_created | timestamp | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 171,
    "is_default": 1,
    "range_start": 1000,
    "range_end": 10000000,
    "next_number": 1235
}
... (more columns)
```

---

## Table: `daakia.item_details`
- **Record Count:** 552
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| session_id | varchar | - | - |  |
| parcel_count | int | - | - |  |
| item_detail | text | - | - |  |
| user_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 12,
    "consignment_id": 170278,
    "session_id": "",
    "parcel_count": 0,
    "item_detail": "[{\"item_description\":\"test\",\"item_url\":\"\",\"item_sku\":\"3434\",\"no_of_items\":\"1\",\"item_value\":\"1\",\"weight\":\"1\",\"tariff_no\":\"\",\"hscode\":\"13232\",\"manufacture_country_iso\":\"AU\"}]"
}
... (more columns)
```

---

## Table: `daakia.job_batches`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | varchar | - | - |  |
| name | varchar | - | - |  |
| total_jobs | int | - | - |  |
| pending_jobs | int | - | - |  |
| failed_jobs | int | - | - |  |
| failed_job_ids | longtext | - | - |  |
| options | mediumtext | - | - |  |
| cancelled_at | int | - | - |  |
| created_at | int | - | - |  |
| finished_at | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.jobs`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| queue | varchar | - | - |  |
| payload | longtext | - | - |  |
| attempts | tinyint | - | - |  |
| reserved_at | int | - | - |  |
| available_at | int | - | - |  |
| created_at | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.label_file`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| account_number | varchar | - | - |  |
| hawb_list | text | - | - |  |
| created_date | datetime | - | - |  |
| error_list | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.language`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| language | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| created_by | int | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "language": "en-GB",
    "date_created": "2016-07-12 00:00:00",
    "created_by": null,
    "is_active": "Y"
}
... (more columns)
```

---

## Table: `daakia.language_keys`
- **Record Count:** 28
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| keyword | varchar | - | - |  |
| language | varchar | - | - |  |
| caption | text | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| date_updated | timestamp | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3962,
    "keyword": "NAV_REPORT_SETTINGS",
    "language": "en-US",
    "caption": "Report Settings",
    "date_created": "2018-11-14 20:10:00"
}
... (more columns)
```

---

## Table: `daakia.licence_plate`
- **Record Count:** 166
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| range_name | varchar | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| delivery_network | varchar | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| date_created | datetime | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |
| country_range | bit | - | - |  |
| country_list | text | - | - |  |
| range_reminder_limit | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 126,
    "range_name": "Yodel_1519406498",
    "range_start": 1,
    "range_end": 100000000000076,
    "next_number": 16
}
... (more columns)
```

---

## Table: `daakia.licence_plate_country`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| licence_plate_id | int | - | - |  |
| country_id | int | - | - |  |
| range_name | varchar | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| date_created | datetime | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "licence_plate_id": 112,
    "country_id": 13,
    "range_name": "Sweden Post Australia",
    "range_start": 91713500
}
... (more columns)
```

---

## Table: `daakia.location`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| date_updated | varchar | - | - |  |
| updatedby | timestamp | - | - |  |
| active | varchar | - | - |  |
| type | varchar | - | - |  |
| warehouseid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.log_rack_shelf`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| rack_shelf_id | bigint | - | - |  |
| rack_shelf_item_id | bigint | - | - |  |
| customer_id | int | - | - |  |
| remarks | text | - | - |  |
| in_date | datetime | - | - |  |
| in_by | int | - | - |  |
| out_date | datetime | - | - |  |
| out_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.login_request`
- **Record Count:** 74
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | int | - | - |  |
| user_name | varchar | - | - |  |
| ip_address | varchar | - | - |  |
| user_agent | text | - | - |  |
| login_status | tinyint | - | - |  |
| login_time | timestamp | - | - |  |
| logout_time | timestamp | - | - |  |
| session_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 15364,
    "user_id": 0,
    "user_name": "nutoncarrilu1981@gmail.com",
    "ip_address": "196.3.97.69",
    "user_agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/120.0.0.0 Safari\/537.36"
}
... (more columns)
```

---

## Table: `daakia.manifest`
- **Record Count:** 18
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_id | int | - | - |  |
| file_name | varchar | - | - |  |
| label_link | varchar | - | - |  |
| date_created | datetime | - | - |  |
| pieces | varchar | - | - |  |
| agent_id | bigint | - | - |  |
| weight | decimal | - | - |  |
| service_id | int | - | - |  |
| handling | varchar | - | - |  |
| pdf_file | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| type | varchar | - | - |  |
| collection_comment | text | - | - |  |
| collection_date | datetime | - | - |  |
| collection_date_to | datetime | - | - |  |
| pickup_date | datetime | - | - |  |
| delivery_note | text | - | - |  |
| signature | varchar | - | - |  |
| pickup_id | int | - | - |  |
| route_warehouse_id | int | - | - |  |
| routing_email_date | datetime | - | - |  |
| date_received | datetime | - | - |  |
| received_by | varchar | - | - |  |
| name_of_driver | varchar | - | - |  |
| licence_number | varchar | - | - |  |
| account_owner | varchar | - | - |  |
| number_bag | varchar | - | - |  |
| product | varchar | - | - |  |
| carrier_note | varchar | - | - |  |
| carrier_pdf | varchar | - | - |  |
| carrier_id | int | - | - |  |
| is_deleted | enum | - | - |  |
| is_dispatched | enum | - | - |  |
| is_send_email | enum | - | - |  |
| manifest_by | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 809,
    "user_id": 58,
    "file_name": "2020_04_21\/148\/1587486963.csv",
    "label_link": "",
    "date_created": "2020-04-21 18:36:02"
}
... (more columns)
```

---

## Table: `daakia.manifest_consignment_mapping`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| manifestid | int | - | - |  |
| consignmentid | int | - | - |  |
| export_mawb | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.manifest_entity_mapping`
- **Record Count:** 690
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| manifest_id | int | - | - |  |
| manifest_entity_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "entity_id": 81539,
    "manifest_id": 809,
    "manifest_entity_type": "p"
}
... (more columns)
```

---

## Table: `daakia.manifest_service_mapping`
- **Record Count:** 40
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| manifest_id | bigint | - | - |  |
| service_id | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 42,
    "manifest_id": 809,
    "service_id": 2
}
... (more columns)
```

---

## Table: `daakia.market_place_documentation_mapping`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| marketplace_id | int | - | - |  |
| step_title | varchar | - | - |  |
| step_description | text | - | - |  |
| step_image | varchar | - | - |  |
| step_order | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | timestamp | - | - |  |
| added_by | int | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.market_places`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| title | varchar | - | - |  |
| description | varchar | - | - |  |
| translation_key | varchar | - | - |  |
| is_active | tinyint | - | - |  |
| page_link | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |
| is_delete | tinyint | - | - |  |
| integration_logo | varchar | - | - |  |
| manual_link | varchar | - | - |  |
| plugin_key | varchar | - | - |  |
| parent_id | int | - | - |  |
| integration_type | int | - | - |  |
| channel_type | int | - | - |  |
| country_id | int | - | - |  |
| last_sync | datetime | - | - |  |
| is_featured | bit | - | - |  |
| is_api2cart | bit | - | - |  |
| help_doc | varchar | - | - |  |
| class_name | varchar | - | - |  |
| documentation_title | varchar | - | - |  |
| documentation_cover_image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.market_places_authenticate_field`
- **Record Count:** 374
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| field_name | varchar | - | - |  |
| field_value | varchar | - | - |  |
| market_places_id | bigint | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| is_delete | tinyint | - | - |  |
| auto_generate_value | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 43,
    "field_name": "Amazon Access Key ",
    "field_value": "AWS_ACCESS_KEY_ID",
    "market_places_id": 1,
    "added_by": 188
}
... (more columns)
```

---

## Table: `daakia.marketplace_order`
- **Record Count:** 22
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| marketplace_id | bigint | - | - |  |
| marketplace_order_number | varchar | - | - |  |
| create_time | datetime | - | - |  |
| order_status | varchar | - | - |  |
| receiver_name | varchar | - | - |  |
| receiver_phone | varchar | - | - |  |
| receiver_state | varchar | - | - |  |
| receiver_city | varchar | - | - |  |
| receiver_country_id | bigint | - | - |  |
| receiver_addressline1 | varchar | - | - |  |
| receiver_addressline2 | varchar | - | - |  |
| receiver_postcode | varchar | - | - |  |
| payment_method | varchar | - | - |  |
| order_total | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| receiver_email | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| Ack | varchar | - | - |  |
| error_code | varchar | - | - |  |
| error_message | varchar | - | - |  |
| consignment_id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| shipped_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "marketplace_id": 88,
    "marketplace_order_number": "304",
    "create_time": "2020-03-26 18:33:20",
    "order_status": "Unshipped"
}
... (more columns)
```

---

## Table: `daakia.marketplace_order_details`
- **Record Count:** 48
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| marketplace_order_id | bigint | - | - |  |
| marketplace_item_id | varchar | - | - |  |
| sku | varchar | - | - |  |
| title | varchar | - | - |  |
| quantity_purchased | varchar | - | - |  |
| asin | varchar | - | - |  |
| item_price | varchar | - | - |  |
| currency | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "marketplace_order_id": 1,
    "marketplace_item_id": "1",
    "sku": "",
    "title": "Ship Your Idea - Blue"
}
... (more columns)
```

---

## Table: `daakia.mawb`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| mawb_number | varchar | - | - |  |
| mawb_source_country_id | int | - | - |  |
| mawb_source_warehouse_id | int | - | - |  |
| mawb_destination_country_id | int | - | - |  |
| mawb_destination_warehouse_id | int | - | - |  |
| is_active | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |
| mawb_status | enum | - | - |  |
| manifest_label | varchar | - | - |  |
| mawb_lv_manifest | varchar | - | - |  |
| mawb_hv_manifest | varchar | - | - |  |
| bagging_type | enum | - | - |  |
| bag_is_hv_lv | enum | - | - |  |
| mawb_class | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.mawb_flight_document`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| document_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "document_name": "ExportSAManifest"
}
... (more columns)
```

---

## Table: `daakia.mawb_flight_document_mapping`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| country_id | bigint | - | - |  |
| document_id | bigint | - | - |  |
| template_id | bigint | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 197,
    "document_id": 1,
    "template_id": 1,
    "added_by": 148
}
... (more columns)
```

---

## Table: `daakia.mawb_parcel_mapping`
- **Record Count:** 30
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb_id | int | - | - |  |
| parcel_id | int | - | - |  |
| wharehouse_id | int | - | - |  |
| bag_id | int | - | - |  |
| date_added | datetime | - | - |  |
| added_by | bigint | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3670,
    "mawb_id": 85,
    "parcel_id": 228079,
    "wharehouse_id": 9,
    "bag_id": 10356
}
... (more columns)
```

---

## Table: `daakia.migrations`
- **Record Count:** 5
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| migration | varchar | - | - |  |
| batch | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "migration": "0001_01_01_000000_create_users_table",
    "batch": 1
}
... (more columns)
```

---

## Table: `daakia.not_found_record`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb | varchar | - | - |  |
| bag_number | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| scanned_by | bigint | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| weight | decimal | - | - |  |
| date_created | datetime | - | - |  |
| reason | varchar | - | - |  |
| image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.oauth_access_tokens`
- **Record Count:** 24
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| access_token | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "access_token": "fe73060e430030efe5ea3d8496b1b3c4d6c46c52",
    "client_id": "developer",
    "user_id": 58,
    "expires": "2019-11-18 20:08:16",
    "scope": null
}
... (more columns)
```

---

## Table: `daakia.oauth_authorization_codes`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| authorization_code | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| redirect_uri | varchar | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.oauth_clients`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| client_secret | varchar | - | - |  |
| redirect_uri | varchar | - | - |  |
| grant_types | varchar | - | - |  |
| scope | varchar | - | - |  |
| user_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.oauth_jwt`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| subject | varchar | - | - |  |
| public_key | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.oauth_public_keys`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| public_key | varchar | - | - |  |
| private_key | varchar | - | - |  |
| encryption_algorithm | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.oauth_refresh_tokens`
- **Record Count:** 20
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| refresh_token | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "refresh_token": "fe9944ab1578dd1529d1eef7af4a90728bddf291",
    "client_id": "developer",
    "user_id": 58,
    "expires": "2020-04-06 23:16:13",
    "scope": null
}
... (more columns)
```

---

## Table: `daakia.oauth_scopes`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| scope | varchar | - | - |  |
| is_default | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.ops_summary`
- **Record Count:** 22
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account | varchar | - | - |  |
| services | varchar | - | - |  |
| country | varchar | - | - |  |
| quantity | varchar | - | - |  |
| weight | varchar | - | - |  |
| carrier | varchar | - | - |  |
| date_submitted | datetime | - | - |  |
| reference | varchar | - | - |  |
| mawb | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2504,
    "account": "",
    "services": "",
    "country": "Austria",
    "quantity": "9"
}
... (more columns)
```

---

## Table: `daakia.optimus_file_name`
- **Record Count:** 28
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 40732,
    "file_name": "ESPN00040695",
    "sent_date": "2016-09-11 13:30:04",
    "file_id": "40695"
}
... (more columns)
```

---

## Table: `daakia.owe_southafrica_postcode`
- **Record Count:** 26
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| zone | varchar | - | - |  |
| postcode | varchar | - | - |  |
| main_outlying | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5852,
    "zone": "ELS",
    "postcode": "9996",
    "main_outlying": "O"
}
... (more columns)
```

---

## Table: `daakia.owe_southafrica_routine`
- **Record Count:** 22
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state | varchar | - | - |  |
| zone | varchar | - | - |  |
| route | varchar | - | - |  |
| delivery_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "state": "Aberdeen",
    "zone": "PLZ2",
    "route": "RD4",
    "delivery_time": 3
}
... (more columns)
```

---

## Table: `daakia.pallet`
- **Record Count:** 38
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| palletno | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| close | tinyint | - | - |  |
| userid | int | - | - |  |
| pallet_carrier_id | int | - | - |  |
| date_dispatch | timestamp | - | - |  |
| dispatch_userid | int | - | - |  |
| type | varchar | - | - |  |
| manifestid | int | - | - |  |
| hub | varchar | - | - |  |
| is_active | tinyint | - | - |  |
| comments | varchar | - | - |  |
| label | varchar | - | - |  |
| pallet_source_country_id | int | - | - |  |
| pallet_source_warehouse_id | int | - | - |  |
| pallet_destination_country_id | int | - | - |  |
| pallet_destination_warehouse_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3922,
    "palletno": "",
    "date_created": "2018-11-13 22:31:45",
    "close": 0,
    "userid": 2128
}
... (more columns)
```

---

## Table: `daakia.pallet_bag_mapping`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| palletid | int | - | - |  |
| bagid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.pallet_bag_remove_reason`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pallet_id | int | - | - |  |
| bag_id | int | - | - |  |
| reason | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pallet_id": 3934,
    "bag_id": 10263,
    "reason": "Testing"
}
... (more columns)
```

---

## Table: `daakia.pallet_carier_group`
- **Record Count:** 12
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| group_name | varchar | - | - |  |
| carrier_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "group_name": "Quality Assurance",
    "carrier_id": 194
}
... (more columns)
```

---

## Table: `daakia.pallet_carrier`
- **Record Count:** 6
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| service_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "ROYAL MAIL TRACKED 48",
    "service_id": "12"
}
... (more columns)
```

---

## Table: `daakia.pallet_carrier_service`
- **Record Count:** 22
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_group_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "carrier_group_id": 2,
    "service_id": 261
}
... (more columns)
```

---

## Table: `daakia.pallet_entity_mapping`
- **Record Count:** 66
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pallet_id | int | - | - |  |
| entity_id | int | - | - |  |
| pallet_entity_type | enum | - | - |  |
| pre_sort | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pallet_id": 3928,
    "entity_id": 10091,
    "pallet_entity_type": "b",
    "pre_sort": "n"
}
... (more columns)
```

---

## Table: `daakia.pallet_location`
- **Record Count:** 22
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| locationid | int | - | - |  |
| palletid | int | - | - |  |
| consignmentid | int | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| comments | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 110892,
    "locationid": 3,
    "palletid": 0,
    "consignmentid": 11888192,
    "date_created": "2016-09-11 18:07:18"
}
... (more columns)
```

---

## Table: `daakia.pallet_name`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| serviceid | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.parcel`
- **Record Count:** 72
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| do_tracking_number | varchar | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| weight | decimal | - | - |  |
| description | text | - | - |  |
| parcel_message | text | - | - |  |
| qty | varchar | - | - |  |
| commoditycode | varchar | - | - |  |
| hscode | varchar | - | - |  |
| grossweight | decimal | - | - |  |
| pweight | varchar | - | - |  |
| itemvalue | varchar | - | - |  |
| number_item | int | - | - |  |
| tarrif_no | varchar | - | - |  |
| update_weight | decimal | - | - |  |
| owe_status_code | varchar | - | - |  |
| chute_sorted | int | - | - |  |
| parcel_status_code | int | - | - |  |
| routing_code | varchar | - | - |  |
| last_tracking_update | datetime | - | - |  |
| parcel_item_desc | text | - | - |  |
| parcel_label | varchar | - | - |  |
| itemsku | varchar | - | - |  |
| itemurl | varchar | - | - |  |
| sort_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 232016,
    "consignment_id": 170432,
    "tracking_number": "A12290298493",
    "do_tracking_number": "",
    "length": "2.00"
}
... (more columns)
```

---

## Table: `daakia.parcel_bagging_mapping`
- **Record Count:** 74
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| parcel_id | int | - | - |  |
| bag_id | int | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4031,
    "parcel_id": 228641,
    "bag_id": 10331,
    "added_by": 2328,
    "added_date": "2020-03-19 16:53:06"
}
... (more columns)
```

---

## Table: `daakia.parcel_iteam`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| parcel_id | int | - | - |  |
| iteam_name | varchar | - | - |  |
| iteam_weight | decimal | - | - |  |
| iteam_weight_unit | enum | - | - |  |
| iteam_value | int | - | - |  |
| iteam_quantity | int | - | - |  |
| iteam_country_id | int | - | - |  |
| iteam_description | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.parcel_log`
- **Record Count:** 2
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 0,
    "userid": 58,
    "logdate": "2019-07-12 19:45:27",
    "ipaddress": "3937179205",
    "log_id": 139002
}
... (more columns)
```

---

## Table: `daakia.parcelforce_datafile_id`
- **Record Count:** 52
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1070,
    "file_name": "PGO",
    "sent_date": "2016-04-22 10:33:44",
    "file_name_id": 1
}
... (more columns)
```

---

## Table: `daakia.parcelforce_depo_detail`
- **Record Count:** 34
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_name | varchar | - | - |  |
| depo_short_name | varchar | - | - |  |
| postcode | varchar | - | - |  |
| route_number | varchar | - | - |  |
| pfw_ect | varchar | - | - |  |
| pfw_lat | varchar | - | - |  |
| pfw_lct | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11170,
    "depo_name": "Worcester (Satellite) Depot",
    "depo_short_name": "WORC",
    "postcode": "WR11",
    "route_number": "R000"
}
... (more columns)
```

---

## Table: `daakia.parcelforce_hub_details`
- **Record Count:** 16
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_name | varchar | - | - |  |
| depo_number | varchar | - | - |  |
| mon_hub_24 | varchar | - | - |  |
| mon_chute_24 | varchar | - | - |  |
| mon_hub_48 | varchar | - | - |  |
| mon_chute_48 | varchar | - | - |  |
| tue_hub_24 | varchar | - | - |  |
| tue_chute_24 | varchar | - | - |  |
| tue_hub_48 | varchar | - | - |  |
| tue_chute_48 | varchar | - | - |  |
| wed_hub_24 | varchar | - | - |  |
| wed_chute_24 | varchar | - | - |  |
| wed_hub_48 | varchar | - | - |  |
| wed_chute_48 | varchar | - | - |  |
| thu_hub_24 | varchar | - | - |  |
| thu_chute_24 | varchar | - | - |  |
| thu_hub_48 | varchar | - | - |  |
| thu_chute_48 | varchar | - | - |  |
| fri_hub_24 | varchar | - | - |  |
| fri_chute_24 | varchar | - | - |  |
| fri_hub_48 | varchar | - | - |  |
| fri_chute_48 | varchar | - | - |  |
| sat_hub_24 | varchar | - | - |  |
| sat_chute_24 | varchar | - | - |  |
| sat_hub_48 | varchar | - | - |  |
| sat_chute_48 | varchar | - | - |  |
| sun_hub_24 | varchar | - | - |  |
| sun_chute_24 | varchar | - | - |  |
| sun_hub_48 | varchar | - | - |  |
| sun_chute_48 | varchar | - | - |  |
| sat_delivery_hub | varchar | - | - |  |
| sat_delivery_chute | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 234,
    "depo_name": "Newcastle Depot",
    "depo_number": "NC20",
    "mon_hub_24": "NW",
    "mon_chute_24": ""
}
... (more columns)
```

---

## Table: `daakia.parcelforu_pickup_point`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| company | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| statuscode | varchar | - | - |  |
| status_description | varchar | - | - |  |
| latitude | varchar | - | - |  |
| longitude | varchar | - | - |  |
| mon | varchar | - | - |  |
| tue | varchar | - | - |  |
| wed | varchar | - | - |  |
| thu | varchar | - | - |  |
| fri | varchar | - | - |  |
| sat | varchar | - | - |  |
| sun | varchar | - | - |  |
| label_routing | varchar | - | - |  |
| branch_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.partnerservicesrouting`
- **Record Count:** 256
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | int | - | - |  |
| product_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 225,
    "from_weight": "0.00",
    "to_weight": "0.25",
    "status": 1
}
... (more columns)
```

---

## Table: `daakia.password_reset_tokens`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| email | varchar | - | - |  |
| token | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.payment_gateways`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_id | bigint | - | - |  |
| gateway_type | enum | - | - |  |
| email | varchar | - | - |  |
| currency_id | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.payments_history`
- **Record Count:** 92
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | bigint | - | - |  |
| paypal_payment_id | varchar | - | - |  |
| txn_id | varchar | - | - |  |
| billing_id | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| amount | double | - | - |  |
| amount_currency_id | int | - | - |  |
| payment_method | enum | - | - |  |
| payment_detail | varchar | - | - |  |
| user_currency_id | int | - | - |  |
| debit | decimal | - | - |  |
| credit | decimal | - | - |  |
| module_name | varchar | - | - |  |
| module_id | varchar | - | - |  |
| invoice_id | int | - | - |  |
| payment_status | varchar | - | - |  |
| is_completed | enum | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 19439,
    "account_id": 2327,
    "paypal_payment_id": null,
    "txn_id": null,
    "billing_id": null
}
... (more columns)
```

---

## Table: `daakia.pbt_datafile_id`
- **Record Count:** 26
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_name": "AVOWE00001",
    "sent_date": "2016-05-09 09:36:10"
}
... (more columns)
```

---

## Table: `daakia.pbt_routine`
- **Record Count:** 74
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_code | varchar | - | - |  |
| courier_file_label_code | varchar | - | - |  |
| transport_label_code | varchar | - | - |  |
| courier_charges_code | varchar | - | - |  |
| area_desc | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2151,
    "file_code": "7299",
    "courier_file_label_code": "DUD",
    "transport_label_code": "DUD",
    "courier_charges_code": "C"
}
... (more columns)
```

---

## Table: `daakia.permissions`
- **Record Count:** 812
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| lang_key | varchar | - | - |  |
| parent_id | int | - | - |  |
| file_name | varchar | - | - |  |
| description | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| query_string | varchar | - | - |  |
| icon | varchar | - | - |  |
| sort_order | int | - | - |  |
| is_menu_item | tinyint | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "lang_key": "LEFT_MENU_PERMISSIONS_&_ACTIONS",
    "parent_id": 0,
    "file_name": "#",
    "description": "Manage Permissions"
}
... (more columns)
```

---

## Table: `daakia.permissions_log`
- **Record Count:** 432
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-09-05 15:38:29",
    "ipaddress": "657980247",
    "log_id": 350
}
... (more columns)
```

---

## Table: `daakia.personal_access_tokens`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tokenable_type | varchar | - | - |  |
| tokenable_id | bigint | - | - |  |
| name | text | - | - |  |
| token | varchar | - | - |  |
| abilities | text | - | - |  |
| last_used_at | timestamp | - | - |  |
| expires_at | timestamp | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.pickup`
- **Record Count:** 20
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pickup_number | varchar | - | - |  |
| pickup_date | timestamp | - | - |  |
| delivery_note | varchar | - | - |  |
| pick_up_pdf | varchar | - | - |  |
| collection_pdf | varchar | - | - |  |
| collection_address | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pickup_number": "",
    "pickup_date": "2016-08-05 19:55:00",
    "delivery_note": "Picked up by kazim",
    "pick_up_pdf": null
}
... (more columns)
```

---

## Table: `daakia.pmp_routine`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| storeid | int | - | - |  |
| store_name | varchar | - | - |  |
| is_active | bit | - | - |  |
| country | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| telephone | varchar | - | - |  |
| depot_no | int | - | - |  |
| depot_description | varchar | - | - |  |
| round1 | int | - | - |  |
| drop1 | int | - | - |  |
| round2 | int | - | - |  |
| drop2 | int | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.post_italia_routing`
- **Record Count:** 56
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| zip_code | varchar | - | - |  |
| routing_file | varchar | - | - |  |
| province | varchar | - | - |  |
| province_iso_code | varchar | - | - |  |
| sortation_name | varchar | - | - |  |
| sortation_id | varchar | - | - |  |
| sortation_name_on_bag | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 8823,
    "zip_code": "89024",
    "routing_file": "93-D52-POLISTEN-x21",
    "province": "Reggio Calabria",
    "province_iso_code": "RC"
}
... (more columns)
```

---

## Table: `daakia.postcode_user_service_charges`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| from_postcode | varchar | - | - |  |
| to_postcode | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| city_name | varchar | - | - |  |
| country_iso | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.postitalia_untracked`
- **Record Count:** 26
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| postcode | int | - | - |  |
| region | varchar | - | - |  |
| provenience | varchar | - | - |  |
| sortation | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4410,
    "postcode": 31024,
    "region": "VENETO",
    "provenience": "TV",
    "sortation": "10 - Padova"
}
... (more columns)
```

---

## Table: `daakia.postnl_datafile_id`
- **Record Count:** 74
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| service_country | varchar | - | - |  |
| file_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4,
    "file_name": "VM000001",
    "sent_date": "2016-09-22 10:45:47",
    "service_country": "BE",
    "file_id": 1
}
... (more columns)
```

---

## Table: `daakia.pre_alert`
- **Record Count:** 58
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb_id | int | - | - |  |
| flight_number | varchar | - | - |  |
| pieces | varchar | - | - |  |
| weight | varchar | - | - |  |
| etd | varchar | - | - |  |
| eta | varchar | - | - |  |
| current_status | varchar | - | - |  |
| date_time | varchar | - | - |  |
| cleared | varchar | - | - |  |
| status | varchar | - | - |  |
| comments | varchar | - | - |  |
| account | varchar | - | - |  |
| files | text | - | - |  |
| uploadby | varchar | - | - |  |
| shed | varchar | - | - |  |
| date_entry | varchar | - | - |  |
| created_by | int | - | - |  |
| date_updated | varchar | - | - |  |
| updated_by | int | - | - |  |
| type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3683,
    "mawb_id": 7,
    "flight_number": "3",
    "pieces": "3",
    "weight": "3"
}
... (more columns)
```

---

## Table: `daakia.pricing_bulk_data_1579539860`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| hawb | varchar | - | - |  |
| charges_reference | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| remote_area_charges | decimal | - | - |  |
| handling_charges | decimal | - | - |  |
| reference | decimal | - | - |  |
| linehaul | decimal | - | - |  |
| address_correction | decimal | - | - |  |
| airline_handling | decimal | - | - |  |
| clearance | decimal | - | - |  |
| collections | decimal | - | - |  |
| ddp_admin_fee | decimal | - | - |  |
| ddp_charge | decimal | - | - |  |
| delivery | decimal | - | - |  |
| dispatch | decimal | - | - |  |
| labour | decimal | - | - |  |
| other | decimal | - | - |  |
| out_of_gauge | decimal | - | - |  |
| over_weight | decimal | - | - |  |
| ras | decimal | - | - |  |
| redelivery | decimal | - | - |  |
| label_charges | decimal | - | - |  |
| vat | decimal | - | - |  |
| discount | decimal | - | - |  |
| mobile_tracking | decimal | - | - |  |
| user_account | varchar | - | - |  |
| status | tinyint | - | - |  |
| is_complete | tinyint | - | - |  |
| message | varchar | - | - |  |
| batch_number | varchar | - | - |  |
| currency | varchar | - | - |  |
| data_result | text | - | - |  |
| data_result_count | tinyint | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.pricing_bulk_data_1579539864`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| hawb | varchar | - | - |  |
| charges_reference | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| remote_area_charges | decimal | - | - |  |
| handling_charges | decimal | - | - |  |
| reference | decimal | - | - |  |
| linehaul | decimal | - | - |  |
| address_correction | decimal | - | - |  |
| airline_handling | decimal | - | - |  |
| clearance | decimal | - | - |  |
| collections | decimal | - | - |  |
| ddp_admin_fee | decimal | - | - |  |
| ddp_charge | decimal | - | - |  |
| delivery | decimal | - | - |  |
| dispatch | decimal | - | - |  |
| labour | decimal | - | - |  |
| other | decimal | - | - |  |
| out_of_gauge | decimal | - | - |  |
| over_weight | decimal | - | - |  |
| ras | decimal | - | - |  |
| redelivery | decimal | - | - |  |
| label_charges | decimal | - | - |  |
| vat | decimal | - | - |  |
| discount | decimal | - | - |  |
| mobile_tracking | decimal | - | - |  |
| user_account | varchar | - | - |  |
| status | tinyint | - | - |  |
| is_complete | tinyint | - | - |  |
| message | varchar | - | - |  |
| batch_number | varchar | - | - |  |
| currency | varchar | - | - |  |
| data_result | text | - | - |  |
| data_result_count | tinyint | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.product_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.product_routine_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| message | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.products`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| product_name | varchar | - | - |  |
| insurance | decimal | - | - |  |
| description | varchar | - | - |  |
| status | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| logo | varchar | - | - |  |
| length | int | - | - |  |
| width | int | - | - |  |
| height | int | - | - |  |
| vol_weight | decimal | - | - |  |
| vol_denominator | int | - | - |  |
| is_untrack | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| added_date | timestamp | - | - |  |
| added_by | int | - | - |  |
| transit_time | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 225,
    "product_name": "YODEL_TEST_PRODUCT",
    "insurance": "10.00",
    "description": ""
}
... (more columns)
```

---

## Table: `daakia.proforma_invoice_biiling`
- **Record Count:** 78
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| billing_company | varchar | - | - |  |
| billing_contact | varchar | - | - |  |
| billing_address_line_1 | varchar | - | - |  |
| billing_address_line_2 | varchar | - | - |  |
| billing_address_line_3 | varchar | - | - |  |
| billing_city | varchar | - | - |  |
| billing_country | varchar | - | - |  |
| billing_postcode | varchar | - | - |  |
| billing_telephone | varchar | - | - |  |
| payment_terms | varchar | - | - |  |
| export_type | varchar | - | - |  |
| comments | varchar | - | - |  |
| delivery_terms | varchar | - | - |  |
| link_file | varchar | - | - |  |
| payer_vat | varchar | - | - |  |
| harm_comm_code | varchar | - | - |  |
| export | varchar | - | - |  |
| invoice_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4717,
    "consignment_id": 58893,
    "billing_company": "",
    "billing_contact": "",
    "billing_address_line_1": ""
}
... (more columns)
```

---

## Table: `daakia.quotation_details`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| shipping_from | bigint | - | - |  |
| shipping_to | bigint | - | - |  |
| carrier_id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| account_id | bigint | - | - |  |
| price_type | enum | - | - |  |
| pieces | int | - | - |  |
| currency_id | int | - | - |  |
| weight | decimal | - | - |  |
| dimensions | longtext | - | - |  |
| volumn_weight | decimal | - | - |  |
| basic_charge | decimal | - | - |  |
| vat_charge | decimal | - | - |  |
| extra_charge | decimal | - | - |  |
| sub_total | decimal | - | - |  |
| discount | decimal | - | - |  |
| user_email | varchar | - | - |  |
| discount_type | enum | - | - |  |
| total_charge | decimal | - | - |  |
| remark | text | - | - |  |
| status | enum | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| conversionrate | float | - | - |  |
| pdf | varchar | - | - |  |
| date_created | datetime | - | - |  |
| added_by | bigint | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.rack`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| warehouse_id | int | - | - |  |
| title | varchar | - | - |  |
| short_title | varchar | - | - |  |
| rack_rows | int | - | - |  |
| rack_cols | int | - | - |  |
| shelf_dimension | varchar | - | - |  |
| shelf_max_weight | decimal | - | - |  |
| is_york | tinyint | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.rack_shelf`
- **Record Count:** 30
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| rack_id | int | - | - |  |
| shelf_no | int | - | - |  |
| is_filled | tinyint | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4946,
    "rack_id": 24,
    "shelf_no": 1243,
    "is_filled": 0,
    "updated_date": "2016-06-15 18:40:19"
}
... (more columns)
```

---

## Table: `daakia.rack_shelf_item`
- **Record Count:** 32
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| goods_name | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| description | text | - | - |  |
| weight | decimal | - | - |  |
| dimension | varchar | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1599,
    "goods_name": "BABY TOYS",
    "tracking_number": "RE796582043SE",
    "description": "BABY TOYS",
    "weight": "0.76"
}
... (more columns)
```

---

## Table: `daakia.ratebands`
- **Record Count:** 1658
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| courier_service_id | int | - | - |  |
| name | varchar | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 270,
    "courier_service_id": 2,
    "name": "United Kingdom",
    "orderq": 0,
    "active": 1
}
... (more columns)
```

---

## Table: `daakia.reamus_destination_station`
- **Record Count:** 62
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_code | varchar | - | - |  |
| postcode_from | varchar | - | - |  |
| postcode_to | varchar | - | - |  |
| product_code | varchar | - | - |  |
| station_id | varchar | - | - |  |
| hub_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 202072,
    "country_code": "GB",
    "postcode_from": "YO307WX",
    "postcode_to": "YO309ZZ",
    "product_code": "01"
}
... (more columns)
```

---

## Table: `daakia.reamus_exception`
- **Record Count:** 26
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_code | varchar | - | - |  |
| postcode_from | varchar | - | - |  |
| postcode_to | varchar | - | - |  |
| product_code | varchar | - | - |  |
| feature_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6635,
    "country_code": "GB",
    "postcode_from": "TN80AA",
    "postcode_to": "TN89ZZ",
    "product_code": "01"
}
... (more columns)
```

---

## Table: `daakia.reamus_product_service`
- **Record Count:** 26
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reamus_id | varchar | - | - |  |
| product_code | varchar | - | - |  |
| feature_code | varchar | - | - |  |
| exception | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3057,
    "reamus_id": "078008",
    "product_code": "01",
    "feature_code": "94",
    "exception": "Y"
}
... (more columns)
```

---

## Table: `daakia.reamus_service`
- **Record Count:** 154
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| service_description | varchar | - | - |  |
| product_line1 | varchar | - | - |  |
| product_line2 | varchar | - | - |  |
| product_code | varchar | - | - |  |
| date_code | varchar | - | - |  |
| day_text | varchar | - | - |  |
| time_code | varchar | - | - |  |
| time_text | varchar | - | - |  |
| handling | varchar | - | - |  |
| feature_id | varchar | - | - |  |
| feature_code | varchar | - | - |  |
| file_type | varchar | - | - |  |
| consignment_flag | varchar | - | - |  |
| ds_flag | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "service_id": 10,
    "service_description": "PRIORITY 12:00",
    "product_line1": "VAN MON TO FRI",
    "product_line2": "PRE 12 POD"
}
... (more columns)
```

---

## Table: `daakia.reamus_site`
- **Record Count:** 28
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reamus_id | varchar | - | - |  |
| site | varchar | - | - |  |
| reamus_id2 | varchar | - | - |  |
| country_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2191,
    "reamus_id": "002448",
    "site": "94D   (00)",
    "reamus_id2": "002448",
    "country_code": "GB"
}
... (more columns)
```

---

## Table: `daakia.remotearea_charges_carrier`
- **Record Count:** 2
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "remotearea_group_id": null,
    "remotearea_charges": null,
    "is_deleted": "N",
    "added_by": 58
}
... (more columns)
```

---

## Table: `daakia.remotearea_charges_carrier_user`
- **Record Count:** 4
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| user_account_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "remotearea_group_id": 2,
    "user_account_id": 2234,
    "remotearea_charges": "12.00",
    "is_deleted": "N"
}
... (more columns)
```

---

## Table: `daakia.remotearea_charges_services`
- **Record Count:** 2
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| service_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "remotearea_group_id": 16,
    "service_id": 252,
    "remotearea_charges": "5.00",
    "from_weight": "0.00"
}
... (more columns)
```

---

## Table: `daakia.remotearea_charges_services_user`
- **Record Count:** 40
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| service_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| user_account_id | int | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "remotearea_group_id": 16,
    "remotearea_charges": "5.00",
    "service_id": 252,
    "from_weight": "0.00"
}
... (more columns)
```

---

## Table: `daakia.remotearea_charges_tariffs`
- **Record Count:** 908
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| tariff_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "remotearea_group_id": 324,
    "remotearea_charges": "3.00",
    "tariff_id": 240542,
    "from_weight": "1.00"
}
... (more columns)
```

---

## Table: `daakia.remotearea_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| service_code | varchar | - | - |  |
| charges | varchar | - | - |  |
| remotearea_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.remotearea_weight_charge`
- **Record Count:** 480
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| service_code | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| formulla | varchar | - | - |  |
| charges | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "weight_from": "0.00",
    "weight_to": "0.50",
    "service_code": "TINA",
    "country_iso": "ZM"
}
... (more columns)
```

---

## Table: `daakia.remoteareas`
- **Record Count:** 30
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remoteareas_groups_id | int | - | - |  |
| country_id | int | - | - |  |
| from_postcode | varchar | - | - |  |
| to_postcode | varchar | - | - |  |
| city | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 14206,
    "remoteareas_groups_id": 325,
    "country_id": 197,
    "from_postcode": "9774",
    "to_postcode": "9774"
}
... (more columns)
```

---

## Table: `daakia.remoteareas_groups`
- **Record Count:** 96
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| group_name | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "group_name": "TEST123",
    "is_deleted": "Y",
    "added_by": 148
}
... (more columns)
```

---

## Table: `daakia.remoteareas_groups_log`
- **Record Count:** 42
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2170,
    "logdate": "2018-11-29 18:21:40",
    "ipaddress": "656773136",
    "log_id": 31
}
... (more columns)
```

---

## Table: `daakia.remoteareas_log`
- **Record Count:** 46
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2170,
    "logdate": "2018-11-29 18:22:31",
    "ipaddress": "656773136",
    "log_id": 4481
}
... (more columns)
```

---

## Table: `daakia.report_customize_settings`
- **Record Count:** 20
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | int | - | - |  |
| report_title | varchar | - | - |  |
| report_key | varchar | - | - |  |
| fields_data | longtext | - | - |  |
| date_added | datetime | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "account_id": 148,
    "report_title": "Royal mail Template",
    "report_key": "tracking_status_report",
    "fields_data": "a:21:{i:0;a:2:{s:3:\"key\";s:4:\"date\";s:5:\"title\";s:4:\"Date\";}i:1;a:2:{s:3:\"key\";s:15:\"tracking_number\";s:5:\"title\";s:15:\"Tracking Number\";}i:2;a:2:{s:3:\"key\";s:4:\"hawb\";s:5:\"title\";s:4:\"HAWB\";}i:3;a:2:{s:3:\"key\";s:7:\"service\";s:5:\"title\";s:7:\"Service\";}i:4;a:2:{s:3:\"key\";s:4:\"city\";s:5:\"title\";s:4:\"City\";}i:5;a:2:{s:3:\"key\";s:7:\"country\";s:5:\"title\";s:7:\"Country\";}i:6;a:2:{s:3:\"key\";s:6:\"weight\";s:5:\"title\";s:10:\"Weight(Kg)\";}i:7;a:2:{s:3:\"key\";s:17:\"volumetric_weight\";s:5:\"title\";s:17:\"Volumetric Weight\";}i:8;a:2:{s:3:\"key\";s:16:\"volumetric_liter\";s:5:\"title\";s:16:\"Volumetric Liter\";}i:9;a:2:{s:3:\"key\";s:5:\"lxwxh\";s:5:\"title\";s:5:\"LXWXH\";}i:10;a:2:{s:3:\"key\";s:24:\"last_event_tracking_date\";s:5:\"title\";s:24:\"Last Event Tracking Date\";}i:11;a:2:{s:3:\"key\";s:6:\"status\";s:5:\"title\";s:6:\"Status\";}i:12;a:2:{s:3:\"key\";s:15:\"tracking_detail\";s:5:\"title\";s:15:\"Tracking Detail\";}i:13;a:2:{s:3:\"key\";s:16:\"delivery_on_time\";s:5:\"title\";s:16:\"Delivery On Time\";}i:14;a:2:{s:3:\"key\";s:25:\"delivery_aim_working_days\";s:5:\"title\";s:27:\"Delivery Aim (Working Days)\";}i:15;a:2:{s:3:\"key\";s:40:\"total_no_of_days_booking_to_hub_received\";s:5:\"title\";s:42:\"Total No of Days (Booking To Hub Received)\";}i:16;a:2:{s:3:\"key\";s:49:\"total_no_of_days_hub_received_to_carrier_received\";s:5:\"title\";s:52:\"Total No of Days  (Hub Received to carrier received)\";}i:17;a:2:{s:3:\"key\";s:52:\"total_no_of_days_from_carrier_received_calendar_days\";s:5:\"title\";s:54:\"Total No of Days From Carrier Received (Calendar Days)\";}i:18;a:2:{s:3:\"key\";s:46:\"total_no_of_working_days_from_carrier_received\";s:5:\"title\";s:46:\"Total No of Working Days From Carrier Received\";}i:19;a:2:{s:3:\"key\";s:32:\"total_transit_time_calendar_days\";s:5:\"title\";s:34:\"Total Transit Time (Calendar Days)\";}i:20;a:2:{s:3:\"key\";s:31:\"total_transit_time_working_days\";s:5:\"title\";s:33:\"Total Transit Time (Working Days)\";}}"
}
... (more columns)
```

---

## Table: `daakia.routing_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| product_id | int | - | - |  |
| user_id | int | - | - |  |
| routing_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.royalmail_docket_number`
- **Record Count:** 22
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| docket_number | varchar | - | - |  |
| file_name | varchar | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 69381,
    "tracking_number": "GV028030824GB",
    "docket_number": "7029158451",
    "file_name": "WCTB200117DF",
    "date_created": "2020-01-28 17:40:57"
}
... (more columns)
```

---

## Table: `daakia.royalmail_sortcode`
- **Record Count:** 852
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode_area | varchar | - | - |  |
| sortcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1819,
    "postcode_area": "PA",
    "sortcode": "A87"
}
... (more columns)
```

---

## Table: `daakia.sales_call_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| date_call | date | - | - |  |
| meeting_date | timestamp | - | - |  |
| customer_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address | varchar | - | - |  |
| telephone | varchar | - | - |  |
| email | varchar | - | - |  |
| detail_discussed | text | - | - |  |
| document_link | varchar | - | - |  |
| follow_meeting_date | timestamp | - | - |  |
| userid | int | - | - |  |
| email_send | char | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.sales_pot_comission`
- **Record Count:** 30
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| date_added | datetime | - | - |  |
| no_of_shipments | int | - | - |  |
| comission | decimal | - | - |  |
| is_paid | tinyint | - | - |  |
| paid_by | int | - | - |  |
| paid_date | datetime | - | - |  |
| company_comission | decimal | - | - |  |
| sales_comission | decimal | - | - |  |
| salepot_table_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "date_added": "2016-08-18 15:38:46",
    "no_of_shipments": 5,
    "comission": "50.00",
    "is_paid": 1
}
... (more columns)
```

---

## Table: `daakia.service_agent_mapping`
- **Record Count:** 42
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| linehaul_agent | int | - | - |  |
| account_number | varchar | - | - |  |
| api_url | varchar | - | - |  |
| api_username | varchar | - | - |  |
| api_password | varchar | - | - |  |
| ftp_host | varchar | - | - |  |
| ftp_username | varchar | - | - |  |
| ftp_password | varchar | - | - |  |
| integration_type | varchar | - | - |  |
| class_file_name | varchar | - | - |  |
| insurance_charges | decimal | - | - |  |
| insurance_cover | decimal | - | - |  |
| reroute_charges | decimal | - | - |  |
| oversize_charges | decimal | - | - |  |
| address_change_charges | decimal | - | - |  |
| other_surcharges | decimal | - | - |  |
| return_charges | decimal | - | - |  |
| relabel_charges | decimal | - | - |  |
| wrong_address_charges | decimal | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| email | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 45,
    "serviceid": 63,
    "agentid": 4,
    "linehaul_agent": 0,
    "account_number": ""
}
... (more columns)
```

---

## Table: `daakia.service_collection_county`
- **Record Count:** 12
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| country_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 7,
    "service_id": 300,
    "country_id": 80
}
... (more columns)
```

---

## Table: `daakia.service_constant`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| constant | varchar | - | - |  |
| carrier_id | int | - | - |  |
| caption | varchar | - | - |  |
| description | varchar | - | - |  |
| design_control | varchar | - | - |  |
| mandatory | bit | - | - |  |
| sort_order | int | - | - |  |
| integration_type | enum | - | - |  |
| default_values | text | - | - |  |
| field_size | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.service_constant_value`
- **Record Count:** 30
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| constant_value | varchar | - | - |  |
| service_id | int | - | - |  |
| agent_id | int | - | - |  |
| constant_id | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | int | - | - |  |
| date_update | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "constant_value": "ONE WORLD EXPRESS",
    "service_id": 226,
    "agent_id": 1,
    "constant_id": 1
}
... (more columns)
```

---

## Table: `daakia.service_country_ttime`
- **Record Count:** 56
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| id_country | int | - | - |  |
| id_service | int | - | - |  |
| transit_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11327,
    "id_country": 65,
    "id_service": 322,
    "transit_time": 5
}
... (more columns)
```

---

## Table: `daakia.service_document`
- **Record Count:** 8
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| document_id | int | - | - |  |
| agent_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6,
    "service_id": 1,
    "document_id": 7,
    "agent_id": 3,
    "document_name": "1508154586aramex.png"
}
... (more columns)
```

---

## Table: `daakia.service_log`
- **Record Count:** 28
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 512,
    "userid": 58,
    "logdate": "2020-03-10 13:44:55",
    "ipaddress": "393717774",
    "log_id": 299
}
... (more columns)
```

---

## Table: `daakia.service_range_mapping`
- **Record Count:** 274
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| agent_id | int | - | - |  |
| licence_plate_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 10,
    "service_id": 226,
    "agent_id": 1,
    "licence_plate_id": 92
}
... (more columns)
```

---

## Table: `daakia.services`
- **Record Count:** 4
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| code | varchar | - | - |  |
| carrier_id | int | - | - |  |
| account_number | varchar | - | - |  |
| type | varchar | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| wieght_type | int | - | - |  |
| supplier | varchar | - | - |  |
| service_type | enum | - | - |  |
| drop_off_service_id | bigint | - | - |  |
| description | text | - | - |  |
| fuel_surcharge_cost | decimal | - | - |  |
| fuel_surcharge | decimal | - | - |  |
| fuel_surcharge_type | char | - | - |  |
| max_length | decimal | - | - |  |
| max_width | decimal | - | - |  |
| max_height | decimal | - | - |  |
| max_volumetric_weight | decimal | - | - |  |
| volumetric_denominator | int | - | - |  |
| send_data_courier | tinyint | - | - |  |
| is_document | tinyint | - | - |  |
| friday_only_flag | tinyint | - | - |  |
| saturday_only_flag | tinyint | - | - |  |
| sunday_only_flag | tinyint | - | - |  |
| product_owner | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | tinyint | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| uploaded_currency | varchar | - | - |  |
| uploaded_currency_value | decimal | - | - |  |
| registration_fee | decimal | - | - |  |
| weight_after | decimal | - | - |  |
| aditional_charge | decimal | - | - |  |
| origin_country | int | - | - |  |
| is_untrack | bit | - | - |  |
| account_owner | int | - | - |  |
| remotearea | enum | - | - |  |
| carrier_address_limit | int | - | - |  |
| label_class_name | varchar | - | - |  |
| transit_time | int | - | - |  |
| required_email | tinyint | - | - |  |
| required_telephone | tinyint | - | - |  |
| shipment_type | enum | - | - |  |
| pre_sort | enum | - | - |  |
| proforma_invoice | tinyint | - | - |  |
| agent_dispatch | enum | - | - |  |
| brief_manifest | enum | - | - |  |
| delivery_mode | tinyint | - | - |  |
| insurance_available | tinyint | - | - |  |
| vol_wgt_formula | varchar | - | - |  |
| is_remotearea | enum | - | - |  |
| is_customized | bit | - | - |  |
| pre_advise | enum | - | - |  |
| pre_alert | enum | - | - |  |
| pre_alert_email | text | - | - |  |
| cut_off_time | varchar | - | - |  |
| label_charges | decimal | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| maximum_allowed_dimension | int | - | - |  |
| maximum_dim_formula | varchar | - | - |  |
| validation_type | enum | - | - |  |
| zone_type | enum | - | - |  |
| tariff_type | enum | - | - |  |
| girth | decimal | - | - |  |
| girth_formula | varchar | - | - |  |
| mail_type | enum | - | - |  |
| mail_option | enum | - | - |  |
| is_reschedulable | int | - | - |  |
| carrier_service_code | varchar | - | - |  |
| is_eori_required | int | - | - |  |
| delivery_type | enum | - | - |  |
| is_commercials | enum | - | - |  |
| is_cn | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 226,
    "name": "Next Day Delivery",
    "code": "AMZ001",
    "carrier_id": 16,
    "account_number": null
}
... (more columns)
```

---

## Table: `daakia.services_dpd`
- **Record Count:** 166
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| 2_digit_service_code | varchar | - | - |  |
| 3_digit_service_code | varchar | - | - |  |
| dpd_product_desc | varchar | - | - |  |
| dpd_label_service | varchar | - | - |  |
| ilk_product_desc | varchar | - | - |  |
| ilk_alternative_service_desc | varchar | - | - |  |
| premium | varchar | - | - |  |
| sec_dpd | varchar | - | - |  |
| sec_ilk | varchar | - | - |  |
| ilk_max_parcels_per_con | int | - | - |  |
| ilk_max_weight_per_parcel | int | - | - |  |
| dpd_max_parcels_per_con | int | - | - |  |
| dpd_max_weight_per_parcel | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "2_digit_service_code": "01",
    "3_digit_service_code": "801",
    "dpd_product_desc": "",
    "dpd_label_service": ""
}
... (more columns)
```

---

## Table: `daakia.sessions`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | varchar | - | - |  |
| user_id | bigint | - | - |  |
| ip_address | varchar | - | - |  |
| user_agent | text | - | - |  |
| payload | longtext | - | - |  |
| last_activity | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.shopping_platform`
- **Record Count:** 32
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| title | varchar | - | - |  |
| page_key | varchar | - | - |  |
| description | longtext | - | - |  |
| translation_key | varchar | - | - |  |
| plugin_key | varchar | - | - |  |
| integration_logo | varchar | - | - |  |
| display_option | tinyint | - | - |  |
| connect_url | varchar | - | - |  |
| active | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Magento 1.0",
    "page_key": "magento",
    "description": "<center><img src=\"..\/images\/magento.jpg\" \/><\/center>\n <div class=\"row\">\n <div class=\"col-md-9 col-sm-9 col-xs-9\">\n <div class=\"tab-content\">\n <div class=\"tab-pane active\" id=\"tab_7_1\">\n <h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.<\/h3>\n <p>Oneworld's multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.<\/p>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_2\">\n <p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, \n our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development \n teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier \n integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,\n OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld's innovative multi-carrier shipping software provides seamless \n integration with a wide range of UK and international parcel, packet and mail service providers, including:<\/p>\n <ul>\n <li>YODEL<\/li>\n <li>DHL Express<\/li>\n <li>UK Mail<\/li>\n <li>TNT Express<\/li>\n <li>DX Freight<\/li>\n <li>Whistl<\/li>\n <li>Asendia<\/li>\n <li>OneWorldorce<\/li>\n <li>Hermes \/ MyHermes<\/li>\n <li>Collect Plus<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_3\">\n <ul>\n <li>Integrates multiple carriers, reducing time and paperwork.<\/li>\n <li>Access OneWorld's 'pooled volume' discounted rates and save up to 80% on your existing rates.<\/li>\n <li>Multi-carrier web based tracking.<\/li>\n <li>Free Magento integration provided by our team of in-house software developers.<\/li>\n <li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).<\/li>\n <li>Dedicated in-house customer services team.<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_4\">\n <ul>\n <li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager<\/li>\n <li>2. Log in using your Magento admin username and password<\/li>\n <li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;<\/li>\n <li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_5\">\n <h3>System Requirements<\/h3>\n <ul>\n <li>Magento 1.9 or higher<\/li>\n <li>OneWorld shipping account<\/li>\n <li>At least one OneWorld Service Preference List configured<\/li>\n <li>OneWorld tracking API Key (optional)<\/li>\n <\/ul>\n <h3>Setup<\/h3>\n After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.<\/div>\n <\/div>\n <\/div>\n <div class=\"col-md-3 col-sm-3 col-xs-3\">\n <ul class=\"nav nav-tabs tabs-right\">\n <li class=\"active\"><a href=\"#tab_7_1\" data-toggle=\"tab\"> Integration <\/a><\/li>\n <li><a href=\"#tab_7_2\" data-toggle=\"tab\"> Save time and money <\/a><\/li>\n <li><a href=\"#tab_7_3\" data-toggle=\"tab\"> Features and Benefits<\/a><\/li>\n <li><a href=\"#tab_7_4\" data-toggle=\"tab\"> Instructions for installing <\/a><\/li>\n <li><a href=\"#tab_7_5\" data-toggle=\"tab\"> Setup Guide <\/a><\/li>\n <\/ul>\n <\/div>\n <\/div>",
    "translation_key": "MAGENTO 1.0"
}
... (more columns)
```

---

## Table: `daakia.sort_key_record`
- **Record Count:** 46
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pos_sld_sort_level_key | varchar | - | - |  |
| pos_sld_level_1_type | varchar | - | - |  |
| pos_sld_level_1_name | varchar | - | - |  |
| pos_sld_level_1_code | varchar | - | - |  |
| pos_sld_level_2_type | varchar | - | - |  |
| pos_sld_level_2_name | varchar | - | - |  |
| pos_sld_level_2_code | varchar | - | - |  |
| pos_sld_level_3_type | varchar | - | - |  |
| pos_sld_level_3_name | varchar | - | - |  |
| pos_sld_level_3_code | varchar | - | - |  |
| pos_sld_level_4_type | varchar | - | - |  |
| pos_sld_level_4_name | varchar | - | - |  |
| pos_sld_level_4_code | varchar | - | - |  |
| pos_sld_level_5_type | varchar | - | - |  |
| pos_sld_level_5_name | varchar | - | - |  |
| pos_sld_level_5_code | varchar | - | - |  |
| pos_sld_hermes_barcode_1_to_7 | varchar | - | - |  |
| pos_sld_hermes_barcode_seq_key | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 70857,
    "pos_sld_sort_level_key": "604",
    "pos_sld_level_1_type": "DEPOT",
    "pos_sld_level_1_name": "PER",
    "pos_sld_level_1_code": "33"
}
... (more columns)
```

---

## Table: `daakia.sorter_postcode_zone`
- **Record Count:** 238
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| area | varchar | - | - |  |
| postcode | varchar | - | - |  |
| zone | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "area": "Aberdeen",
    "postcode": "AB",
    "zone": "1"
}
... (more columns)
```

---

## Table: `daakia.sp_tariff_log`
- **Record Count:** 42
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | int | - | - |  |
| charges_type | varchar | - | - |  |
| charges | decimal | - | - |  |
| formulla | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| consignment_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2763,
    "account_id": 2288,
    "charges_type": "label charges",
    "charges": "0.00",
    "formulla": ",CURRENT_TIMESTAMP,@consignment_id_p),\n                                            (NULL, @f_user_ac"
}
... (more columns)
```

---

## Table: `daakia.status_reason`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| status_id | int | - | - |  |
| reason | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.tagnumber_range`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| service | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.tariff_additional_charges`
- **Record Count:** 78
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | bigint | - | - |  |
| consignment_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "tariff_id": 240542,
    "consignment_charges_types_id": 1,
    "charge": "2.00",
    "charge_type": "percentage"
}
... (more columns)
```

---

## Table: `daakia.tariff_details`
- **Record Count:** 16
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_name | varchar | - | - |  |
| status | bit | - | - |  |
| tariff_type | varchar | - | - |  |
| start_date | datetime | - | - |  |
| end_date | datetime | - | - |  |
| date_created | timestamp | - | - |  |
| added_by | int | - | - |  |
| currency | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 8,
    "tariff_name": "jaimin test",
    "status": 0,
    "tariff_type": "CHARGE",
    "start_date": "2017-02-01 00:00:00"
}
... (more columns)
```

---

## Table: `daakia.tariff_service_charges`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | bigint | - | - |  |
| tariff_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.tariff_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_name | int | - | - |  |
| user_id | int | - | - |  |
| tariff_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.tariffs`
- **Record Count:** 28
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| carrier_id | int | - | - |  |
| service_id | int | - | - |  |
| name | varchar | - | - |  |
| status | tinyint | - | - |  |
| currency_id | int | - | - |  |
| tariff_type | enum | - | - |  |
| start_date | date | - | - |  |
| end_date | date | - | - |  |
| description | text | - | - |  |
| tariffs_pricing_rule_id | int | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 240577,
    "user_account_id": 2331,
    "carrier_id": 16,
    "service_id": 38,
    "name": "EURACC Tariff 1 Sup"
}
... (more columns)
```

---

## Table: `daakia.tariffs_account_mapping`
- **Record Count:** 288
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| user_account_id | int | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_id": 240403,
    "user_account_id": 2290,
    "added_date": "2018-12-07 10:05:03",
    "added_by": 1544177103
}
... (more columns)
```

---

## Table: `daakia.tariffs_details`
- **Record Count:** 28
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariffs_id | int | - | - |  |
| from_zone_id | int | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| weight_cost | decimal | - | - |  |
| piece_cost | decimal | - | - |  |
| formula | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 61470,
    "tariffs_id": 240676,
    "from_zone_id": 2819,
    "to_zone_id": 0,
    "weight_from": "0.50"
}
... (more columns)
```

---

## Table: `daakia.tariffs_log`
- **Record Count:** 0
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| old_id | int | - | - |  |
| courier_service_id | int | - | - |  |
| collection_rateband_id | int | - | - |  |
| destination_rateband_id | int | - | - |  |
| collection_postcode_group_id | int | - | - |  |
| destination_postcode_group_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| tariff | decimal | - | - |  |
| add_unit_cost | decimal | - | - |  |
| unit_size | decimal | - | - |  |
| extra_tariff | decimal | - | - |  |
| extra_add_unit_cost | decimal | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| customer_id | varchar | - | - |  |
| formula | varchar | - | - |  |
| log_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.tariffs_pricing`
- **Record Count:** 0
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| tarif_pricing_type | enum | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| margin_type | enum | - | - |  |
| margin | decimal | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.tariffs_pricing_rules`
- **Record Count:** 76
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| name | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_id": 240393,
    "name": "UKMELL TARIFF CUSTOMER",
    "date_added": "2018-11-12 16:11:40",
    "added_by": 58
}
... (more columns)
```

---

## Table: `daakia.tariffs_pricing_rules_details`
- **Record Count:** 0
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_pricing_rule_id | int | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| margin | decimal | - | - |  |
| margin_type | enum | - | - |  |
| margin_weight_cost | varchar | - | - |  |
| margin_piece_cost | varchar | - | - |  |
| linehaul | decimal | - | - |  |
| linehaul_type | enum | - | - |  |
| tariff_pricing_type | enum | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.themes`
- **Record Count:** 12
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| slug | varchar | - | - |  |
| style_sheet | varchar | - | - |  |
| dashboard_template | varchar | - | - |  |
| is_active | bit | - | - |  |
| created_by | bigint | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Handlerbund",
    "slug": "hnd",
    "style_sheet": "",
    "dashboard_template": ""
}
... (more columns)
```

---

## Table: `daakia.tourline_routine`
- **Record Count:** 40
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| agency_name | varchar | - | - |  |
| postal_code | varchar | - | - |  |
| agency_code | varchar | - | - |  |
| zone | varchar | - | - |  |
| province | varchar | - | - |  |
| route_code | varchar | - | - |  |
| km | varchar | - | - |  |
| town_name | varchar | - | - |  |
| kilometer | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 130497,
    "agency_name": "VISEU P03                                           ",
    "postal_code": "3511 ",
    "agency_code": "004034",
    "zone": "PTI"
}
... (more columns)
```

---

## Table: `daakia.tracking_data`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| entity_type | enum | - | - |  |
| tracking_number | varchar | - | - |  |
| user_id | int | - | - |  |
| track_point | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| ip_address | varchar | - | - |  |
| status_code_id | int | - | - |  |
| warehouse_id | int | - | - |  |
| pod_image | varchar | - | - |  |
| carrier_code | varchar | - | - |  |
| carrier_desc | varchar | - | - |  |
| signatory | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| parcel_image | varchar | - | - |  |
| latitude | varchar | - | - |  |
| longitude | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.tracking_estimated_time`
- **Record Count:** 648
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| handeling_code | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| estimated_time | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "handeling_code": "REGPOSTEURUTR",
    "country_iso": "BR",
    "estimated_time": "will be delivered in the upcoming days"
}
... (more columns)
```

---

## Table: `daakia.tracking_status_codes`
- **Record Count:** 16
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| status_code | varchar | - | - |  |
| status_code_map | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "status_code": "Order Created \/ Label Created",
    "status_code_map": null
}
... (more columns)
```

---

## Table: `daakia.ukmail_authentication`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| authentication_token | varchar | - | - |  |
| date_created | datetime | - | - |  |
| user_account | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6,
    "authentication_token": "A188F266-CFE1-407D-B16A-F2F46DD8B064",
    "date_created": "2019-03-11 16:23:20",
    "user_account": "2297"
}
... (more columns)
```

---

## Table: `daakia.ukpostcodelatlng`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode | varchar | - | - |  |
| latitude | decimal | - | - |  |
| longitude | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.user`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_type | enum | - | - |  |
| user_name | varchar | - | - |  |
| user_pass | varchar | - | - |  |
| active_flag | bit | - | - |  |
| first_name | varchar | - | - |  |
| last_name | varchar | - | - |  |
| address | varchar | - | - |  |
| email | varchar | - | - |  |
| phone | varchar | - | - |  |
| country_id | int | - | - |  |
| api_key | varchar | - | - |  |
| api_secert | varchar | - | - |  |
| api_date | datetime | - | - |  |
| profile_image | varchar | - | - |  |
| is_employee | bit | - | - |  |
| warehouse_id | int | - | - |  |
| dashboard | enum | - | - |  |
| invalid_login_count | int | - | - |  |
| user_account_id | int | - | - |  |
| last_login_date | datetime | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |
| is_deleted | bit | - | - |  |
| archive_server | bit | - | - |  |
| carrier_setup_agreement | bit | - | - |  |
| receive_email | enum | - | - |  |
| tc_agreed_date | date | - | - |  |
| is_tc_agreed | enum | - | - |  |
| address_2 | varchar | - | - |  |
| address_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| state | varchar | - | - |  |
| commission_break_event_amount | varchar | - | - |  |
| is_sale_pot_eligible | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.user_account_log`
- **Record Count:** 26
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 773,
    "userid": 58,
    "logdate": "2020-03-10 14:58:25",
    "ipaddress": "859091698",
    "log_id": 2297
}
... (more columns)
```

---

## Table: `daakia.user_account_old`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| active_flag | bit | - | - |  |
| company | varchar | - | - |  |
| full_name | varchar | - | - |  |
| return_address | varchar | - | - |  |
| sms_dpd | bit | - | - |  |
| user_service_type | enum | - | - |  |
| parentid | int | - | - |  |
| phone | varchar | - | - |  |
| logo | varchar | - | - |  |
| instant_label | bit | - | - |  |
| country | varchar | - | - |  |
| country_id | int | - | - |  |
| tracking_api_access | bit | - | - |  |
| import_data_csv | bit | - | - |  |
| proforma | bit | - | - |  |
| add_tracking | bit | - | - |  |
| collection | bit | - | - |  |
| default_description | varchar | - | - |  |
| default_notes | varchar | - | - |  |
| default_weight | decimal | - | - |  |
| payment_term | text | - | - |  |
| query_term | text | - | - |  |
| vat_number | varchar | - | - |  |
| billing_currency | varchar | - | - |  |
| vat_chargable | bit | - | - |  |
| vat_value | decimal | - | - |  |
| allow_remote_area | bit | - | - |  |
| telephone | varchar | - | - |  |
| billing_address | varchar | - | - |  |
| date_dispatch | bit | - | - |  |
| is_product | varchar | - | - |  |
| profile_image | varchar | - | - |  |
| send_courier_data | bit | - | - |  |
| archive_server | bit | - | - |  |
| credit_check | bit | - | - |  |
| tariff_agreed | bit | - | - |  |
| sales_person | varchar | - | - |  |
| scan_document | text | - | - |  |
| data_entry | bit | - | - |  |
| bank_account_title | varchar | - | - |  |
| bank_sortcode | varchar | - | - |  |
| bank_account_number | varchar | - | - |  |
| bank_branch_address | varchar | - | - |  |
| trade_name_i | varchar | - | - |  |
| trade_address_i | varchar | - | - |  |
| trade_email_i | varchar | - | - |  |
| trade_phone_i | varchar | - | - |  |
| trade_name_ii | varchar | - | - |  |
| trade_address_ii | varchar | - | - |  |
| trade_email_ii | varchar | - | - |  |
| trade_phone_ii | varchar | - | - |  |
| reg_number | varchar | - | - |  |
| reg_address | varchar | - | - |  |
| reg_postcode | varchar | - | - |  |
| reg_country | varchar | - | - |  |
| sale_agent | varchar | - | - |  |
| sale_date | datetime | - | - |  |
| fuel_charges | decimal | - | - |  |
| warehouse_id | int | - | - |  |
| user_signature | text | - | - |  |
| is_fuelcharges_include | bit | - | - |  |
| is_prepaid | bit | - | - |  |
| return_label | bit | - | - |  |
| finalmile_over_label | bit | - | - |  |
| request_manifest_collection | bit | - | - |  |
| create_pre_alert | bit | - | - |  |
| is_employee | bit | - | - |  |
| invoice_bank_details_id | int | - | - |  |
| check_list_account_form | bit | - | - |  |
| check_list_credit_check | bit | - | - |  |
| check_list_t_cs | bit | - | - |  |
| check_list_tariff_agreed | bit | - | - |  |
| check_list_sales_pot | bit | - | - |  |
| sales_pot_time_period | int | - | - |  |
| sales_pot_percentage | decimal | - | - |  |
| last_login_date | timestamp | - | - |  |
| invalid_login_count | int | - | - |  |
| token | varchar | - | - |  |
| token_updated | timestamp | - | - |  |
| lock_time | timestamp | - | - |  |
| opearation_manifest | bit | - | - |  |
| own_tariff | bit | - | - |  |
| user_warehouse | enum | - | - |  |
| api_key | varchar | - | - |  |
| api_secert | varchar | - | - |  |
| api_date | datetime | - | - |  |
| bagging | bit | - | - |  |
| retail_customer | bit | - | - |  |
| show_price | bit | - | - |  |
| sales_rate | decimal | - | - |  |
| collection_add_line_1 | varchar | - | - |  |
| collection_add_line_2 | varchar | - | - |  |
| collection_add_line_3 | varchar | - | - |  |
| collection_city | varchar | - | - |  |
| collection_postcode | varchar | - | - |  |
| collection_country | varchar | - | - |  |
| theme_id | int | - | - |  |
| user_code | int | - | - |  |
| website_link | varchar | - | - |  |
| allow_return_email | bit | - | - |  |
| default_lang | varchar | - | - |  |
| credit_limit | decimal | - | - |  |
| invoice_period | enum | - | - |  |
| label_price | decimal | - | - |  |
| discount | decimal | - | - |  |
| account_code | varchar | - | - |  |
| paypal_email | varchar | - | - |  |
| paypal_currency | varchar | - | - |  |
| email | varchar | - | - |  |
| paypal_client_secret | varchar | - | - |  |
| alternative_email | varchar | - | - |  |
| billing_email | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| paypal_client_id | varchar | - | - |  |
| invoice_template_id | bigint | - | - |  |
| send_tracking_data | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.user_account_service_charges`
- **Record Count:** 50
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| consignment_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "user_account_id": 2285,
    "service_id": 244,
    "consignment_charges_types_id": 2,
    "charge": "15.00"
}
... (more columns)
```

---

## Table: `daakia.user_audit`
- **Record Count:** 96
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| table_name | varchar | - | - |  |
| table_key | bigint | - | - |  |
| message | text | - | - |  |
| old_data | longtext | - | - |  |
| new_data | longtext | - | - |  |
| ip_address | varchar | - | - |  |
| added_by | bigint | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2047,
    "table_name": "parcel",
    "table_key": 232080,
    "message": "usman usman has generated new Parcel",
    "old_data": "null"
}
... (more columns)
```

---

## Table: `daakia.user_department`
- **Record Count:** 8
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| department_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "user_id": 2141,
    "department_id": 1
}
... (more columns)
```

---

## Table: `daakia.user_document`
- **Record Count:** 10
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 20,
    "user_account_id": null,
    "document_id": 4,
    "document_name": "1507639322images.jpg",
    "added_by": 148
}
... (more columns)
```

---

## Table: `daakia.user_log`
- **Record Count:** 1222
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-08-07 12:09:58",
    "ipaddress": "1506093954",
    "log_id": 2258
}
... (more columns)
```

---

## Table: `daakia.user_market_places_mapping`
- **Record Count:** 38
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| market_places_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| auth_data | text | - | - |  |
| store_key | varchar | - | - |  |
| active | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "market_places_id": 1,
    "user_account_id": 2191,
    "auth_data": "{\"AWS_ACCESS_KEY_ID\":\"AKIAJBUWT3ZBRDV3QITA\",\"AWS_SECRET_ACCESS_KEY\":\"6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe\",\"MERCHANT_ID\":\"A3LX344APRTG2Z\",\"MARKETPLACE_ID\":\"A1F83G8C2ARO7P\"}",
    "store_key": null
}
... (more columns)
```

---

## Table: `daakia.user_services_charges`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| service_id | int | - | - |  |
| sur_charge | decimal | - | - |  |
| sur_charge_type | enum | - | - |  |
| extra_charge | decimal | - | - |  |
| extra_charge_type | enum | - | - |  |
| discount | decimal | - | - |  |
| discount_type | enum | - | - |  |
| additional_charges_type | enum | - | - |  |
| additional_charges | decimal | - | - |  |
| additional_charges_details | text | - | - |  |
| last_updated | datetime | - | - |  |
| over_weight | decimal | - | - |  |
| over_size | decimal | - | - |  |
| over_weight_type | enum | - | - |  |
| over_size_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.user_services_charges_log`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.user_services_routing`
- **Record Count:** 36
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | bit | - | - |  |
| service_id | int | - | - |  |
| is_remotearea | bit | - | - |  |
| is_over_label | bit | - | - |  |
| added_by | int | - | - |  |
| is_agreed | bit | - | - |  |
| label_charges | decimal | - | - |  |
| is_dead_weight | bit | - | - |  |
| is_over_size | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 148474,
    "user_account_id": 148,
    "country_id": 97,
    "from_weight": "0.00",
    "to_weight": "300.00"
}
... (more columns)
```

---

## Table: `daakia.user_shopping_platforms`
- **Record Count:** 24
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| shopping_platform_id | int | - | - |  |
| reference | varchar | - | - |  |
| site_url | varchar | - | - |  |
| user_id | int | - | - |  |
| status | tinyint | - | - |  |
| date_created | timestamp | - | - |  |
| api_key | varchar | - | - |  |
| api_secrete | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "shopping_platform_id": 1,
    "reference": "asdasd",
    "site_url": "https:\/\/one.com",
    "user_id": 148
}
... (more columns)
```

---

## Table: `daakia.userhasgroups`
- **Record Count:** 1308
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| admin_id | int | - | - |  |
| group_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 44,
    "admin_id": 4,
    "group_id": 17
}
... (more columns)
```

---

## Table: `daakia.users`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| email | varchar | - | - |  |
| email_verified_at | timestamp | - | - |  |
| password | varchar | - | - |  |
| remember_token | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `daakia.vehicle`
- **Record Count:** 24
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| vehicle_type | varchar | - | - |  |
| vehicle_make | varchar | - | - |  |
| model_year | int | - | - |  |
| vehicle_model | varchar | - | - |  |
| registration_number | varchar | - | - |  |
| vehicle_color | varchar | - | - |  |
| vehicle_capacity | varchar | - | - |  |
| is_deleted | tinyint | - | - |  |
| added_date | timestamp | - | - |  |
| added_by | int | - | - |  |
| vehicle_number | varchar | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "vehicle_type": "Vehicle Type",
    "vehicle_make": "Honda",
    "model_year": 2018,
    "vehicle_model": "Vehicle Model"
}
... (more columns)
```

---

## Table: `daakia.vehicle_parcel_mapping`
- **Record Count:** 632
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| parcel_id | bigint | - | - |  |
| vehicle_id | bigint | - | - |  |
| driver_id | bigint | - | - |  |
| pickup_date | date | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | bigint | - | - |  |
| is_active | bit | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "parcel_id": 11595,
    "vehicle_id": 3,
    "driver_id": 2225,
    "pickup_date": "2019-05-22"
}
... (more columns)
```

---

## Table: `daakia.warehouse`
- **Record Count:** 22
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| warehouse_name | varchar | - | - |  |
| addressline1 | varchar | - | - |  |
| addressline2 | varchar | - | - |  |
| stateregion | varchar | - | - |  |
| citytown | varchar | - | - |  |
| postzipcode | varchar | - | - |  |
| countryid | int | - | - |  |
| phone | varchar | - | - |  |
| description | text | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| hub | varchar | - | - |  |
| email | text | - | - |  |
| warehouse_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 9,
    "warehouse_name": "Birmingham3",
    "addressline1": "One World House",
    "addressline2": "",
    "stateregion": "Bartley Green"
}
... (more columns)
```

---

## Table: `daakia.warehouse_processing_time`
- **Record Count:** 96
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| warehouse_id | int | - | - |  |
| parcel_processing_time | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "warehouse_id": 13,
    "parcel_processing_time": 39,
    "service_id": 162
}
... (more columns)
```

---

## Table: `daakia.warehouse_warehouse_ttime`
- **Record Count:** 10
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| from_warehouse_id | int | - | - |  |
| to_warehouse_id | int | - | - |  |
| transit_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "from_warehouse_id": 28,
    "to_warehouse_id": 27,
    "transit_time": 2
}
... (more columns)
```

---

## Table: `daakia.whistl_depo_details`
- **Record Count:** 18
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_id | varchar | - | - |  |
| depo_detail | varchar | - | - |  |
| depo_address | varchar | - | - |  |
| from_postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 966,
    "depo_id": "36360",
    "depo_detail": "ROMFORD MC",
    "depo_address": "SOUTH OCKENDON",
    "from_postcode": "RM15"
}
... (more columns)
```

---

## Table: `daakia.yodel_hubs`
- **Record Count:** 16
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pre_sort_carrier_id | int | - | - |  |
| hub | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pre_sort_carrier_id": 38,
    "hub": "52_INVERNESS",
    "routing_code": "52",
    "company": "YODEL"
}
... (more columns)
```

---

## Table: `logistics.address`
- **Record Count:** 306
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| phone_number | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| email | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country | varchar | - | - |  |
| postcode | varchar | - | - |  |
| user_id | int | - | - |  |
| state | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 329,
    "phone_number": "7919115608",
    "company": "Dominic Jackson",
    "contact": "REGINA BERGELT",
    "email": null
}
... (more columns)
```

---

## Table: `logistics.agent_data`
- **Record Count:** 55
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_code | varchar | - | - |  |
| agent_name | varchar | - | - |  |
| active | tinyint | - | - |  |
| contact_name | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| country_id | int | - | - |  |
| county | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| telephone | varchar | - | - |  |
| mobile | varchar | - | - |  |
| fax | varchar | - | - |  |
| email | varchar | - | - |  |
| alternative_contact_1 | varchar | - | - |  |
| alternative1_telephone | varchar | - | - |  |
| alternative1_mobile | varchar | - | - |  |
| alternative1_fax | varchar | - | - |  |
| alternative1_email | varchar | - | - |  |
| alternative_contact_2 | varchar | - | - |  |
| alternative2_telephone | varchar | - | - |  |
| alternative2_mobile | varchar | - | - |  |
| alternative2_fax | varchar | - | - |  |
| alternative2_email | varchar | - | - |  |
| remarks | text | - | - |  |
| date_created | datetime | - | - |  |
| user_id | int | - | - |  |
| is_deleted | bit | - | - |  |
| logo | varchar | - | - |  |
| agent_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "agent_code": "OWE",
    "agent_name": "One World Express",
    "active": 1,
    "contact_name": "Gordon Shuttleworth"
}
... (more columns)
```

---

## Table: `logistics.agent_document`
- **Record Count:** 3
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "agent_id": 1,
    "document_id": 1,
    "document_name": "150780039822153082.pdf",
    "added_by": 148
}
... (more columns)
```

---

## Table: `logistics.agent_log`
- **Record Count:** 31
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-10-23 16:04:35",
    "ipaddress": "0",
    "log_id": 59
}
... (more columns)
```

---

## Table: `logistics.agent_restricted_postcode`
- **Record Count:** 3
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| agent_id | int | - | - |  |
| service_id | int | - | - |  |
| postcode_city | varchar | - | - |  |
| is_city | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11,
    "agent_id": 1,
    "service_id": 226,
    "postcode_city": "UB77RB",
    "is_city": 0
}
... (more columns)
```

---

## Table: `logistics.api_data`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| api_request | text | - | - |  |
| api_response | text | - | - |  |
| added_by | varchar | - | - |  |
| date_created | datetime | - | - |  |
| api_reason | varchar | - | - |  |
| type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.auto_tracking`
- **Record Count:** 16
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| next_number | int | - | - |  |
| range_end | int | - | - |  |
| increment_date | datetime | - | - |  |
| service_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "next_number": 11703384,
    "range_end": 11917468,
    "increment_date": "0000-00-00 00:00:00",
    "service_name": "Yodel"
}
... (more columns)
```

---

## Table: `logistics.bag_scan_log`
- **Record Count:** 225
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2182,
    "logdate": "2018-12-05 11:11:57",
    "ipaddress": "3065225534",
    "log_id": 10082
}
... (more columns)
```

---

## Table: `logistics.bagging`
- **Record Count:** 296
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| bagnumber | varchar | - | - |  |
| date_created | datetime | - | - |  |
| csv | varchar | - | - |  |
| pdf | varchar | - | - |  |
| manifestid | int | - | - |  |
| account | varchar | - | - |  |
| user_id | int | - | - |  |
| manifest_pdf | varchar | - | - |  |
| bag_status | tinyint | - | - |  |
| date_updated | datetime | - | - |  |
| isdeleted | tinyint | - | - |  |
| service | varchar | - | - |  |
| serviceid | int | - | - |  |
| country | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| bag_type | varchar | - | - |  |
| actual_weight | decimal | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| pieces | int | - | - |  |
| weight | decimal | - | - |  |
| bag_label | varchar | - | - |  |
| bag_source_country_id | int | - | - |  |
| bag_source_warehouse_id | int | - | - |  |
| bag_destination_country_id | int | - | - |  |
| bag_destination_warehouse_id | int | - | - |  |
| is_closed | bit | - | - |  |
| closed_by | bigint | - | - |  |
| closed_date | datetime | - | - |  |
| reopen_by | bigint | - | - |  |
| reopen_date | datetime | - | - |  |
| bag_manifest | varchar | - | - |  |
| bag_value | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 10075,
    "bagnumber": "BHX10075248",
    "date_created": "2018-06-11 11:39:20",
    "csv": "",
    "pdf": ""
}
... (more columns)
```

---

## Table: `logistics.bagging_manifest_mapping`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| manifest_id | int | - | - |  |
| manifest_entity_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.bagging_services_mapping`
- **Record Count:** 4246
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| bag_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "bag_id": 10075,
    "service_id": 248
}
... (more columns)
```

---

## Table: `logistics.bagnumbers`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| consignment_id | varchar | - | - |  |
| parcel_id | int | - | - |  |
| tag_number | varchar | - | - |  |
| bag_number | int | - | - |  |
| service | varchar | - | - |  |
| value | varchar | - | - |  |
| weight | varchar | - | - |  |
| number_pieces | varchar | - | - |  |
| label_file | varchar | - | - |  |
| manifest_file | varchar | - | - |  |
| status | varchar | - | - |  |
| date_created | varchar | - | - |  |
| date_printed | varchar | - | - |  |
| flightnumber | varchar | - | - |  |
| flight_id | int | - | - |  |
| mawb | int | - | - |  |
| accountnumber | varchar | - | - |  |
| destination_addr | varchar | - | - |  |
| country | varchar | - | - |  |
| dispatchdate | varchar | - | - |  |
| mail_number | varchar | - | - |  |
| last_bag | varchar | - | - |  |
| flight_datetime | varchar | - | - |  |
| bag_type | varchar | - | - |  |
| is_track | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.box_info`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| box_number | varchar | - | - |  |
| box_size | varchar | - | - |  |
| box_weight | decimal | - | - |  |
| tracking_numbers | text | - | - |  |
| manifest_number | varchar | - | - |  |
| mawb_number | varchar | - | - |  |
| date_submitted | datetime | - | - |  |
| date_scanned | datetime | - | - |  |
| api_data | text | - | - |  |
| status | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.brazil_postcode`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state | varchar | - | - |  |
| locality | varchar | - | - |  |
| postcode | varchar | - | - |  |
| zone | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.brazil_state`
- **Record Count:** 4063
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state_code | varchar | - | - |  |
| state_name | varchar | - | - |  |
| city_code | varchar | - | - |  |
| city_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "state_code": "421",
    "state_name": "Acre",
    "city_code": "2495",
    "city_name": "Brasileia"
}
... (more columns)
```

---

## Table: `logistics.bulletins`
- **Record Count:** 3
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| heading | varchar | - | - |  |
| description | text | - | - |  |
| date_created | datetime | - | - |  |
| date_submitted | datetime | - | - |  |
| created_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "heading": "Test Bulletin",
    "description": "This is **_test_** description\ns\nd\ndd\n",
    "date_created": "2018-11-18 15:55:13",
    "date_submitted": "2018-11-12 00:00:00"
}
... (more columns)
```

---

## Table: `logistics.cacesa_routine`
- **Record Count:** 13989
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| postcode | varchar | - | - |  |
| agency | varchar | - | - |  |
| route_description | varchar | - | - |  |
| route_id | varchar | - | - |  |
| courier | varchar | - | - |  |
| routing | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "H",
    "postcode": "01216",
    "agency": "000909",
    "route_description": "09-MIRANDA DE EBRO"
}
... (more columns)
```

---

## Table: `logistics.carrier`
- **Record Count:** 1
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier | varchar | - | - |  |
| logo | varchar | - | - |  |
| cut_off_time | varchar | - | - |  |
| carrier_display_name | varchar | - | - |  |
| status | int | - | - |  |
| country_id | int | - | - |  |
| carrier_id | int | - | - |  |
| currency_code | varchar | - | - |  |
| remotearea_check | enum | - | - |  |
| zone_base | bit | - | - |  |
| zone_type | enum | - | - |  |
| on_contract | bit | - | - |  |
| is_gazetteer | tinyint | - | - |  |
| is_reconcile | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 16,
    "carrier": "Amazon",
    "logo": "amazon.png",
    "cut_off_time": "18:00",
    "carrier_display_name": "Amazon"
}
... (more columns)
```

---

## Table: `logistics.carrier_agent`
- **Record Count:** 0
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_number | varchar | - | - |  |
| name | varchar | - | - |  |
| company | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| api_username | varchar | - | - |  |
| api_password | varchar | - | - |  |
| ftp_host | varchar | - | - |  |
| ftp_username | varchar | - | - |  |
| ftp_password | varchar | - | - |  |
| integration_type | varchar | - | - |  |
| date_created | datetime | - | - |  |
| created_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.carrier_data_file_log`
- **Record Count:** 158
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| carrier_id | bigint | - | - |  |
| agent_id | bigint | - | - |  |
| file_name | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| run_number | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "agent_id": 1,
    "file_name": "UKD10161.001",
    "date_created": "2018-11-16 22:25:40"
}
... (more columns)
```

---

## Table: `logistics.carrier_document`
- **Record Count:** 9
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "carrier_id": 19,
    "document_id": 7,
    "document_name": "1508238498aramex.png",
    "added_by": 148
}
... (more columns)
```

---

## Table: `logistics.carrier_hubs`
- **Record Count:** 60
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| hub | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "hub": "52_INVERNESS",
    "routing_code": "52",
    "company": null
}
... (more columns)
```

---

## Table: `logistics.carrier_log`
- **Record Count:** 91
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-09-09 11:33:10",
    "ipaddress": "2006650821",
    "log_id": 16
}
... (more columns)
```

---

## Table: `logistics.carrier_service_customize_rules`
- **Record Count:** 22
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| user_account_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3973,
    "serviceid": 248,
    "agentid": 73,
    "user_account_id": 2294,
    "from_weight": "0.000"
}
... (more columns)
```

---

## Table: `logistics.carrier_service_default_rules`
- **Record Count:** 151
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| is_default | bit | - | - |  |
| agent_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 98,
    "serviceid": 1,
    "agentid": 4,
    "from_weight": "1.000",
    "to_weight": "2.000"
}
... (more columns)
```

---

## Table: `logistics.carrier_zones`
- **Record Count:** 2050
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| service_id | int | - | - |  |
| name | varchar | - | - |  |
| sort_order | int | - | - |  |
| status | tinyint | - | - |  |
| deleted | tinyint | - | - |  |
| date_added | datetime | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 194,
    "service_id": 0,
    "name": "UKMELL LOCAL ZONE",
    "sort_order": 7
}
... (more columns)
```

---

## Table: `logistics.carrier_zones_countries`
- **Record Count:** 2728
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| carrier_zone_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 13,
    "country_id": 225,
    "carrier_zone_id": 1
}
... (more columns)
```

---

## Table: `logistics.carrier_zones_postcode`
- **Record Count:** 5
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_zone_id | int | - | - |  |
| postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 0,
    "carrier_zone_id": 2812,
    "postcode": "Ub3 3nb"
}
... (more columns)
```

---

## Table: `logistics.carton_pallet_number`
- **Record Count:** 492
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| quantity | int | - | - |  |
| start_number | varchar | - | - |  |
| end_number | varchar | - | - |  |
| date_created | datetime | - | - |  |
| userid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "B",
    "quantity": 3,
    "start_number": "0",
    "end_number": "0"
}
... (more columns)
```

---

## Table: `logistics.ch_shipments`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| reference | varchar | - | - |  |
| awb | varchar | - | - |  |
| account | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| postcode | varchar | - | - |  |
| product | varchar | - | - |  |
| service | varchar | - | - |  |
| bagnumber | varchar | - | - |  |
| mawb | varchar | - | - |  |
| charge_able_weight | decimal | - | - |  |
| total_charge | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment`
- **Record Count:** 286
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_id | int | - | - |  |
| user_id | int | - | - |  |
| service_id | int | - | - |  |
| customized_service_id | int | - | - |  |
| warehouse_user_id | int | - | - |  |
| warehouse_id | int | - | - |  |
| sales_pot_id | bigint | - | - |  |
| invoice_id | int | - | - |  |
| credit_id | int | - | - |  |
| is_invoiced | int | - | - |  |
| invoice_type | enum | - | - |  |
| shipment_status | int | - | - |  |
| shipment_type | enum | - | - |  |
| awb | varchar | - | - |  |
| consignment_status | varchar | - | - |  |
| return_awb | varchar | - | - |  |
| hawb | varchar | - | - |  |
| mawb | varchar | - | - |  |
| service_name | varchar | - | - |  |
| reference | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| date_label_created | int | - | - |  |
| date_booked | int | - | - |  |
| date_delivered | int | - | - |  |
| is_customer_manifested | int | - | - |  |
| booked_file_id | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| state | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_id | int | - | - |  |
| telephone | varchar | - | - |  |
| number_pieces | int | - | - |  |
| weight_type | varchar | - | - |  |
| weight | decimal | - | - |  |
| update_weight | decimal | - | - |  |
| fake_weight | decimal | - | - |  |
| charge_weight | decimal | - | - |  |
| vol_weight | decimal | - | - |  |
| vol_demonimator | int | - | - |  |
| hv_lv | enum | - | - |  |
| description | varchar | - | - |  |
| notes | varchar | - | - |  |
| value | decimal | - | - |  |
| currency | varchar | - | - |  |
| sender_name | varchar | - | - |  |
| username | varchar | - | - |  |
| sender_checked | int | - | - |  |
| message | varchar | - | - |  |
| sorter_image | varchar | - | - |  |
| label_file | varchar | - | - |  |
| is_doc | int | - | - |  |
| email | varchar | - | - |  |
| itemtype | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| routing_code_eur | varchar | - | - |  |
| other_routing_code | varchar | - | - |  |
| billing_hold | int | - | - |  |
| send_courier_data | int | - | - |  |
| remote_charges | int | - | - |  |
| reinvoices | int | - | - |  |
| optimus_sorter | int | - | - |  |
| full_pallet | int | - | - |  |
| half_pallet | int | - | - |  |
| quarter_pallet | int | - | - |  |
| date_scanned | datetime | - | - |  |
| consignment_type | enum | - | - |  |
| api_uuid | varchar | - | - |  |
| sender_company | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| sender_telephone | varchar | - | - |  |
| sender_address_line_1 | varchar | - | - |  |
| sender_address_line_2 | varchar | - | - |  |
| sender_address_line_3 | varchar | - | - |  |
| sender_city | varchar | - | - |  |
| sender_postcode | varchar | - | - |  |
| sender_country_id | int | - | - |  |
| sender_state | varchar | - | - |  |
| collection_date | date | - | - |  |
| collection_start_time | varchar | - | - |  |
| collection_end_time | varchar | - | - |  |
| collection_confirmation_no | varchar | - | - |  |
| created_from | enum | - | - |  |
| is_white_label | tinyint | - | - |  |
| is_dead_weight_chargable | tinyint | - | - |  |
| is_customer_billable | int | - | - |  |
| ioss_number | varchar | - | - |  |
| eori_number | varchar | - | - |  |
| vat_number | varchar | - | - |  |
| is_over_size_chargable | int | - | - |  |
| is_insured | int | - | - |  |
| destination_warehouse_id | int | - | - |  |
| consignment_seller | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 170182,
    "agent_id": 0,
    "user_id": 58,
    "service_id": 226,
    "customized_service_id": 0
}
... (more columns)
```

---

## Table: `logistics.consignment_bagging_mapping`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| consignmentid | int | - | - |  |
| bagid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_billing_hold`
- **Record Count:** 6
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| reason_for_hold | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 19,
    "consignment_id": 106855,
    "user_account_id_from": 148,
    "user_account_id_to": 2294,
    "reason_for_hold": "test"
}
... (more columns)
```

---

## Table: `logistics.consignment_billing_hold_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| status | enum | - | - |  |
| reason | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_charges`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | int | - | - |  |
| account_id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_id | int | - | - |  |
| charge_type_id | int | - | - |  |
| agent_id | int | - | - |  |
| cost_type | enum | - | - |  |
| cost | decimal | - | - |  |
| cost_currency | varchar | - | - |  |
| cost_supplier_currency | decimal | - | - |  |
| supplier_currency | varchar | - | - |  |
| cost_company_currency | decimal | - | - |  |
| company_currency | varchar | - | - |  |
| description | varchar | - | - |  |
| changes_reference | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_charges_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_charges_types`
- **Record Count:** 30
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| charges_key | varchar | - | - |  |
| charge_type | enum | - | - |  |
| apply_per_kg | bit | - | - |  |
| is_extra_charge | bit | - | - |  |
| is_vat | bit | - | - |  |
| has_account_default_value | bit | - | - |  |
| is_replace_charges | bit | - | - |  |
| status | tinyint | - | - |  |
| is_delete | bit | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Basic Charges",
    "charges_key": "BASIC_CHARGES",
    "charge_type": "both",
    "apply_per_kg": 0
}
... (more columns)
```

---

## Table: `logistics.consignment_collection`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| sender_company | varchar | - | - |  |
| sender_contact | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| sender_address_line_1 | varchar | - | - |  |
| sender_address_line_2 | varchar | - | - |  |
| sender_address_line_3 | varchar | - | - |  |
| sender_city | varchar | - | - |  |
| sender_country_iso_code | char | - | - |  |
| sender_postcode | varchar | - | - |  |
| sender_telephone | varchar | - | - |  |
| date_collection | int | - | - |  |
| earliest_latest_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_details`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| custom_export_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_dropoff_mapping`
- **Record Count:** 8
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| dropoff_consignment_id | bigint | - | - |  |
| dispatch_consignment_id | bigint | - | - |  |
| dropoff_consignment_tracking | text | - | - |  |
| dispatch_consignment_tracking | text | - | - |  |
| parcel_tracking | text | - | - |  |
| added_by | bigint | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "dropoff_consignment_id": 160151,
    "dispatch_consignment_id": 160152,
    "dropoff_consignment_tracking": "1Z3985RW6806068797",
    "dispatch_consignment_tracking": "JD0002210161165769"
}
... (more columns)
```

---

## Table: `logistics.consignment_hold`
- **Record Count:** 1
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| comments | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| date_created | datetime | - | - |  |
| action | varchar | - | - |  |
| reason_tag | varchar | - | - |  |
| weight | varchar | - | - |  |
| width | varchar | - | - |  |
| height | varchar | - | - |  |
| length | varchar | - | - |  |
| volume | varchar | - | - |  |
| image | varchar | - | - |  |
| account | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "userid": null,
    "comments": null,
    "tracking_number": null,
    "date_created": null
}
... (more columns)
```

---

## Table: `logistics.consignment_hold_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| status | enum | - | - |  |
| reason | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_hscode`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| hscode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_pod`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignmentid | int | - | - |  |
| signature | varchar | - | - |  |
| pod_date | varchar | - | - |  |
| pod_image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.consignment_relabel`
- **Record Count:** 108
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| old_tracking_no | varchar | - | - |  |
| new_tracking_no | varchar | - | - |  |
| date_created | datetime | - | - |  |
| userid | int | - | - |  |
| old_consignment_data | text | - | - |  |
| old_parcel_tracking_no | text | - | - |  |
| old_new_tracking_mapping | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "consignment_id": 95962,
    "old_tracking_no": "JD0002210161134700",
    "new_tracking_no": "JD0002210161134700",
    "date_created": "2019-01-25 13:14:47"
}
... (more columns)
```

---

## Table: `logistics.consignment_status_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| parcel_id | bigint | - | - |  |
| old_status | varchar | - | - |  |
| new_status | varchar | - | - |  |
| message | text | - | - |  |
| added_by | bigint | - | - |  |
| date_added | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.correos_brazil_datafile`
- **Record Count:** 9
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 9,
    "file_name": "temp",
    "sent_date": null
}
... (more columns)
```

---

## Table: `logistics.cost_tariffs`
- **Record Count:** 10
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| courier_service_id | int | - | - |  |
| collection_rateband_id | int | - | - |  |
| destination_rateband_id | int | - | - |  |
| collection_postcode_group_id | int | - | - |  |
| destination_postcode_group_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| tariff_cost | decimal | - | - |  |
| unit_cost | decimal | - | - |  |
| unit_size | decimal | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| tariff_name | varchar | - | - |  |
| formula | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "courier_service_id": 79,
    "collection_rateband_id": 916,
    "destination_rateband_id": 916,
    "collection_postcode_group_id": 0
}
... (more columns)
```

---

## Table: `logistics.countries_link_ratebands`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| rateband_id | int | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.country`
- **Record Count:** 269
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| iso | char | - | - |  |
| name | varchar | - | - |  |
| region | varchar | - | - |  |
| postcode_required | enum | - | - |  |
| type | varchar | - | - |  |
| region_collection | varchar | - | - |  |
| numcode | int | - | - |  |
| allow_express | char | - | - |  |
| allow_classic | char | - | - |  |
| eu_country | char | - | - |  |
| shipping_advice | varchar | - | - |  |
| is_vatable | enum | - | - |  |
| vat_rate | decimal | - | - |  |
| printable_name | varchar | - | - |  |
| iso3 | char | - | - |  |
| export_flag | int | - | - |  |
| timezone_difference | int | - | - |  |
| has_postcodeq | char | - | - |  |
| has_subzonesq | char | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| vat_charged_flag | int | - | - |  |
| customs_flag | int | - | - |  |
| description | text | - | - |  |
| country_image | varchar | - | - |  |
| metakeywords | varchar | - | - |  |
| metadescription | varchar | - | - |  |
| pagetitle | varchar | - | - |  |
| countrybanner | varchar | - | - |  |
| opcode | varchar | - | - |  |
| iso_three | varchar | - | - |  |
| german_name | varchar | - | - |  |
| manifest_template | varchar | - | - |  |
| bag_template | varchar | - | - |  |
| bag_weight_limit | int | - | - |  |
| bag_low_value | int | - | - |  |
| currency_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "iso": "AF",
    "name": "Afghanistan",
    "region": "INT",
    "postcode_required": "NO"
}
... (more columns)
```

---

## Table: `logistics.cpost_manifest`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| manifest_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.credit_note`
- **Record Count:** 21
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| credit_note_number | varchar | - | - |  |
| user_account_id | int | - | - |  |
| invoice_type | enum | - | - |  |
| invoice_number | varchar | - | - |  |
| credit_note_type | enum | - | - |  |
| hawb | text | - | - |  |
| credit_note_heading | text | - | - |  |
| credit_date | datetime | - | - |  |
| net_amount | decimal | - | - |  |
| vat_amount | decimal | - | - |  |
| credit_total | decimal | - | - |  |
| credit_note_by | int | - | - |  |
| pdf | varchar | - | - |  |
| is_email | bit | - | - |  |
| is_read | bit | - | - |  |
| added_by | int | - | - |  |
| date_created | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| currency_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "credit_note_number": "",
    "user_account_id": 2286,
    "invoice_type": "MNI",
    "invoice_number": "MNI1019"
}
... (more columns)
```

---

## Table: `logistics.credit_note_details`
- **Record Count:** 25
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| credit_note_id | int | - | - |  |
| hawb | varchar | - | - |  |
| date_booked | datetime | - | - |  |
| reference | varchar | - | - |  |
| invoice_amount | decimal | - | - |  |
| chargeable_amount | decimal | - | - |  |
| credit_amount | decimal | - | - |  |
| description | varchar | - | - |  |
| is_vatable | enum | - | - |  |
| date_created | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| vat_amount | decimal | - | - |  |
| added_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "credit_note_id": 2,
    "hawb": "Test",
    "date_booked": "2018-11-25 00:00:00",
    "reference": "Test ref"
}
... (more columns)
```

---

## Table: `logistics.cs_log`
- **Record Count:** 8
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| internal_message | text | - | - |  |
| customer_message | text | - | - |  |
| cust_mail | varchar | - | - |  |
| agent_mail | varchar | - | - |  |
| date_created | datetime | - | - |  |
| reminder | varchar | - | - |  |
| reminder_expiry | datetime | - | - |  |
| userid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "consignment_id": 93154,
    "internal_message": "",
    "customer_message": "asdfasdf",
    "cust_mail": "No"
}
... (more columns)
```

---

## Table: `logistics.cs_notes`
- **Record Count:** 4
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| notes | text | - | - |  |
| created_by | bigint | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "notes": "Test Notes",
    "created_by": 2234,
    "date_created": "2018-11-12 17:12:49"
}
... (more columns)
```

---

## Table: `logistics.csv_import_template`
- **Record Count:** 17
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| template_name | varchar | - | - |  |
| template | text | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| update_by | bigint | - | - |  |
| update_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "user_id": 2191,
    "user_account_id": 2297,
    "template_name": "Tahir Finance Account Template",
    "template": "{\"date_added\":\"date_added\",\"receiver_country_iso\":\"receiver_country_iso\",\"service_code\":\"service_code\",\"order_reference\":\"order_reference\",\"receiver_contact\":\"receiver_contact\",\"receiver_address_line_1\":\"receiver_address_line_1\",\"receiver_city\":\"receiver_city\",\"receiver_postcode\":\"receiver_postcode\",\"description\":\"description\",\"parcels\":\"parcels\"}"
}
... (more columns)
```

---

## Table: `logistics.csv_tracking_template`
- **Record Count:** 2
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| template_name | varchar | - | - |  |
| template | text | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| update_by | bigint | - | - |  |
| update_date | datetime | - | - |  |
| service_id | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "user_id": 2324,
    "user_account_id": 2357,
    "template_name": "testtest",
    "template": "{\"Data Received\":\"Data Received\",\"Arrived at Sort Facility Hayes - GBR\":\"Arrived at Sort Facility Hayes - GBR\",\"Arrived at Sort Facility Hamburg - GBR\":\"Arrived at Sort Facility Hamburg - GBR\",\"Departed Facility in Hamburg - GBR\":\"Departed Facility in Hamburg - GBR\"}"
}
... (more columns)
```

---

## Table: `logistics.ctt_datafile_id`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.currency`
- **Record Count:** 90
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| currencyname | varchar | - | - |  |
| leftsymbol | varchar | - | - |  |
| rightsymbol | varchar | - | - |  |
| isdefault | tinyint | - | - |  |
| currencyexchangerate | decimal | - | - |  |
| isactive | tinyint | - | - |  |
| clientdisplay | tinyint | - | - |  |
| currencyid | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "currencyname": "US Dollar",
    "leftsymbol": "$",
    "rightsymbol": "USD",
    "isdefault": 0
}
... (more columns)
```

---

## Table: `logistics.currency_temp`
- **Record Count:** 96
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| country | varchar | - | - |  |
| currency | varchar | - | - |  |
| code | varchar | - | - |  |
| symbol | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "country": "Albania",
    "currency": "Leke",
    "code": "ALL",
    "symbol": "Lek"
}
... (more columns)
```

---

## Table: `logistics.customer_account`
- **Record Count:** 4
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| active_flag | bit | - | - |  |
| company | varchar | - | - |  |
| full_name | varchar | - | - |  |
| return_address | varchar | - | - |  |
| sms_dpd | bit | - | - |  |
| user_service_type | enum | - | - |  |
| parentid | int | - | - |  |
| phone | varchar | - | - |  |
| logo | varchar | - | - |  |
| instant_label | bit | - | - |  |
| country | varchar | - | - |  |
| country_id | int | - | - |  |
| tracking_api_access | bit | - | - |  |
| import_data_csv | bit | - | - |  |
| proforma | bit | - | - |  |
| add_tracking | bit | - | - |  |
| collection | bit | - | - |  |
| default_description | varchar | - | - |  |
| default_notes | varchar | - | - |  |
| default_weight | decimal | - | - |  |
| payment_term | text | - | - |  |
| query_term | text | - | - |  |
| vat_number | varchar | - | - |  |
| billing_currency | varchar | - | - |  |
| vat_chargable | bit | - | - |  |
| vat_value | decimal | - | - |  |
| allow_remote_area | bit | - | - |  |
| telephone | varchar | - | - |  |
| billing_address | varchar | - | - |  |
| date_dispatch | bit | - | - |  |
| is_product | varchar | - | - |  |
| profile_image | varchar | - | - |  |
| send_courier_data | bit | - | - |  |
| archive_server | bit | - | - |  |
| credit_check | bit | - | - |  |
| tariff_agreed | bit | - | - |  |
| sales_person | varchar | - | - |  |
| scan_document | text | - | - |  |
| data_entry | bit | - | - |  |
| bank_account_title | varchar | - | - |  |
| bank_sortcode | varchar | - | - |  |
| bank_account_number | varchar | - | - |  |
| bank_branch_address | varchar | - | - |  |
| trade_name_i | varchar | - | - |  |
| trade_address_i | varchar | - | - |  |
| trade_email_i | varchar | - | - |  |
| trade_phone_i | varchar | - | - |  |
| trade_name_ii | varchar | - | - |  |
| trade_address_ii | varchar | - | - |  |
| trade_email_ii | varchar | - | - |  |
| trade_phone_ii | varchar | - | - |  |
| reg_number | varchar | - | - |  |
| reg_address | varchar | - | - |  |
| reg_postcode | varchar | - | - |  |
| reg_country | varchar | - | - |  |
| sale_agent | varchar | - | - |  |
| sale_date | datetime | - | - |  |
| fuel_charges | decimal | - | - |  |
| warehouse_id | int | - | - |  |
| user_signature | text | - | - |  |
| is_fuelcharges_include | bit | - | - |  |
| is_prepaid | bit | - | - |  |
| return_label | bit | - | - |  |
| finalmile_over_label | bit | - | - |  |
| request_manifest_collection | bit | - | - |  |
| create_pre_alert | bit | - | - |  |
| is_employee | bit | - | - |  |
| invoice_bank_details_id | int | - | - |  |
| check_list_account_form | bit | - | - |  |
| check_list_credit_check | bit | - | - |  |
| check_list_t_cs | bit | - | - |  |
| check_list_tariff_agreed | bit | - | - |  |
| check_list_sales_pot | bit | - | - |  |
| sales_pot_time_period | int | - | - |  |
| sales_pot_percentage | decimal | - | - |  |
| last_login_date | timestamp | - | - |  |
| invalid_login_count | int | - | - |  |
| token | varchar | - | - |  |
| token_updated | timestamp | - | - |  |
| lock_time | timestamp | - | - |  |
| opearation_manifest | bit | - | - |  |
| own_tariff | bit | - | - |  |
| user_warehouse | enum | - | - |  |
| api_key | varchar | - | - |  |
| api_secert | varchar | - | - |  |
| api_date | datetime | - | - |  |
| bagging | bit | - | - |  |
| retail_customer | bit | - | - |  |
| show_price | bit | - | - |  |
| sales_rate | decimal | - | - |  |
| collection_add_line_1 | varchar | - | - |  |
| collection_add_line_2 | varchar | - | - |  |
| collection_add_line_3 | varchar | - | - |  |
| collection_city | varchar | - | - |  |
| collection_postcode | varchar | - | - |  |
| collection_country | varchar | - | - |  |
| theme_id | int | - | - |  |
| user_code | int | - | - |  |
| website_link | varchar | - | - |  |
| allow_return_email | bit | - | - |  |
| default_lang | varchar | - | - |  |
| credit_limit | decimal | - | - |  |
| invoice_period | enum | - | - |  |
| label_price | decimal | - | - |  |
| discount | decimal | - | - |  |
| account_code | varchar | - | - |  |
| paypal_email | varchar | - | - |  |
| paypal_currency | varchar | - | - |  |
| email | varchar | - | - |  |
| paypal_client_secret | varchar | - | - |  |
| alternative_email | varchar | - | - |  |
| billing_email | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| paypal_client_id | varchar | - | - |  |
| invoice_template_id | bigint | - | - |  |
| send_tracking_data | bit | - | - |  |
| billing_contact | varchar | - | - |  |
| ftp_shipment_upload | int | - | - |  |
| balance_alert_percentage | int | - | - |  |
| commission_break_event_account_amount | int | - | - |  |
| tracking_order_prefix | varchar | - | - |  |
| return_shipment_allow | int | - | - |  |
| account_balance | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 148,
    "user_account": "PDS",
    "active_flag": 1,
    "company": "Parcel Delivey Solution",
    "full_name": "PDS"
}
... (more columns)
```

---

## Table: `logistics.customized_services_routing`
- **Record Count:** 13953
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | int | - | - |  |
| customize_service_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 225,
    "from_weight": "0.00",
    "to_weight": "0.25",
    "status": 1
}
... (more columns)
```

---

## Table: `logistics.customized_services_routing_log`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| message | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.customized_user_services_routing`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| user_account_id | int | - | - |  |
| routing_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.cz_datafile_id`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.czint_datafile_id`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.department`
- **Record Count:** 7
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| department_code | varchar | - | - |  |
| description | varchar | - | - |  |
| department_head | bigint | - | - |  |
| isactive | tinyint | - | - |  |
| isdeleted | tinyint | - | - |  |
| addedby | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | bigint | - | - |  |
| updated_on | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Accounts",
    "department_code": "ACC",
    "description": "Accounts & Finance",
    "department_head": 58
}
... (more columns)
```

---

## Table: `logistics.deutschepost_dhl_streetcode`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| street | varchar | - | - |  |
| zipcode | varchar | - | - |  |
| street_code | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.deutschepostdhl_cargo_code`
- **Record Count:** 345
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| start_postcode | int | - | - |  |
| end_postcode | int | - | - |  |
| cargo_code | int | - | - |  |
| municipality_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "start_postcode": 1000,
    "end_postcode": 1999,
    "cargo_code": 1,
    "municipality_name": "Ottendorf-Okrilla"
}
... (more columns)
```

---

## Table: `logistics.document_type`
- **Record Count:** 17
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| document_name | varchar | - | - |  |
| description | varchar | - | - |  |
| document_type | enum | - | - |  |
| is_active | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |
| is_delete | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "document_name": "T & Cs",
    "description": "T & Cs",
    "document_type": "company_contract",
    "is_active": "1"
}
... (more columns)
```

---

## Table: `logistics.domestic`
- **Record Count:** 12000
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode_sector | varchar | - | - |  |
| dpd_depot | varchar | - | - |  |
| dpd_services_group | varchar | - | - |  |
| dpd_offshore_zone | varchar | - | - |  |
| timeslots_code | varchar | - | - |  |
| cluster | varchar | - | - |  |
| ilk_depot | varchar | - | - |  |
| ilk_services_group | varchar | - | - |  |
| ilk_offshore_zone | varchar | - | - |  |
| ilk_alternate_service | varchar | - | - |  |
| new_postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "postcode_sector": "AB10 1",
    "dpd_depot": "0052",
    "dpd_services_group": "18",
    "dpd_offshore_zone": ""
}
... (more columns)
```

---

## Table: `logistics.domestic_day_file`
- **Record Count:** 9339
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": "00090",
    "file_name": "318961_20121107171437.txt",
    "sent_date": "2012-11-07 17:14:37"
}
... (more columns)
```

---

## Table: `logistics.dpd_datafile_id`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.dpdgroups`
- **Record Count:** 79
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| lookup_code | varchar | - | - |  |
| list_of_available_services | varchar | - | - |  |
| Business | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "lookup_code": "1",
    "list_of_available_services": "000000000-010000000-000000000-010000000-000000000-010000000-000000000-010000000-010000000-000000000-",
    "Business": "D"
}
... (more columns)
```

---

## Table: `logistics.dropoff_user_location`
- **Record Count:** 29
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| service_id | int | - | - |  |
| user_id | bigint | - | - |  |
| companyname | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country | varchar | - | - |  |
| telephone | varchar | - | - |  |
| mon | varchar | - | - |  |
| tue | varchar | - | - |  |
| wed | varchar | - | - |  |
| thu | varchar | - | - |  |
| fri | varchar | - | - |  |
| sat | varchar | - | - |  |
| sun | varchar | - | - |  |
| lat | varchar | - | - |  |
| lng | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 29,
    "service_id": 12,
    "user_id": 58,
    "companyname": "COLNBROOK PHARMACY",
    "address_line_1": "36 HIGH STREET"
}
... (more columns)
```

---

## Table: `logistics.dx_routing`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| district | varchar | - | - |  |
| sector | varchar | - | - |  |
| depot | varchar | - | - |  |
| depotid | varchar | - | - |  |
| region_id | varchar | - | - |  |
| delivery_method | varchar | - | - |  |
| delivery_method_id | varchar | - | - |  |
| delivery_method_description | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.emailtemplate`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| emailtemplateid | int | - | - |  |
| title | varchar | - | - |  |
| shortkey | varchar | - | - |  |
| content | text | - | - |  |
| isactive | tinyint | - | - |  |
| createdon | timestamp | - | - |  |
| isdeleted | tinyint | - | - |  |
| type | varchar | - | - |  |
| pagetitle | varchar | - | - |  |
| metatitle | varchar | - | - |  |
| metadescription | varchar | - | - |  |
| metakeywords | varchar | - | - |  |
| sorder | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.estimate_delivery_timing`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| from_rateband | int | - | - |  |
| to_rateband | int | - | - |  |
| date_created | datetime | - | - |  |
| created_by | varchar | - | - |  |
| status | enum | - | - |  |
| delivery_timing | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.euro_day_file`
- **Record Count:** 100
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_name": "COSSW46E_PreAdvice3_000000001",
    "sent_date": "2018-11-22 17:21:15"
}
... (more columns)
```

---

## Table: `logistics.fftin_file`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| label_link | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.flight_info`
- **Record Count:** 32
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| flight_number | varchar | - | - |  |
| country_id | int | - | - |  |
| destination_country_id | int | - | - |  |
| date_created | datetime | - | - |  |
| current_status | enum | - | - |  |
| status | enum | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| signature | varchar | - | - |  |
| carrier | varchar | - | - |  |
| carriage_value | decimal | - | - |  |
| custom_value | decimal | - | - |  |
| insurance_amount | decimal | - | - |  |
| currency | varchar | - | - |  |
| connecting_flight_number | varchar | - | - |  |
| weight_type | varchar | - | - |  |
| rate_charge | varchar | - | - |  |
| iata_code | varchar | - | - |  |
| departure_airport | varchar | - | - |  |
| phone_number | varchar | - | - |  |
| shipper_co | varchar | - | - |  |
| consignee_co | varchar | - | - |  |
| arrival_airport | varchar | - | - |  |
| account_id | int | - | - |  |
| shippers_name | varchar | - | - |  |
| shippers_addressline1 | varchar | - | - |  |
| shippers_addressline2 | varchar | - | - |  |
| accounting_reference | varchar | - | - |  |
| reference | varchar | - | - |  |
| rate_change | varchar | - | - |  |
| low_value_manifest | varchar | - | - |  |
| high_value_manifest | varchar | - | - |  |
| invoice | varchar | - | - |  |
| files_hv | varchar | - | - |  |
| hscodes | varchar | - | - |  |
| etd | varchar | - | - |  |
| eta | varchar | - | - |  |
| shed | varchar | - | - |  |
| files_lv | varchar | - | - |  |
| cleared | varchar | - | - |  |
| comments | varchar | - | - |  |
| weight | varchar | - | - |  |
| pieces | varchar | - | - |  |
| created_by | bigint | - | - |  |
| is_delete | tinyint | - | - |  |
| is_closed | tinyint | - | - |  |
| account_number | varchar | - | - |  |
| airway_bill | varchar | - | - |  |
| company | varchar | - | - |  |
| currancy | varchar | - | - |  |
| files | varchar | - | - |  |
| destination_company | varchar | - | - |  |
| destination_phone_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "flight_number": "PK 0909",
    "country_id": 225,
    "destination_country_id": 225,
    "date_created": null
}
... (more columns)
```

---

## Table: `logistics.flight_mapping`
- **Record Count:** 41
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| flight_info_id | int | - | - |  |
| flight_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| mawb_id | int | - | - |  |
| is_delete | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "flight_info_id": 1,
    "flight_number": "",
    "mawb": "",
    "mawb_id": 7
}
... (more columns)
```

---

## Table: `logistics.forget_password_request`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_name | varchar | - | - |  |
| ip_address | varchar | - | - |  |
| user_agent | varchar | - | - |  |
| token | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| date_expire | datetime | - | - |  |
| is_expire | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.grouphaspermissions`
- **Record Count:** 1922
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| group_id | int | - | - |  |
| perm_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 25,
    "group_id": 19,
    "perm_id": 1
}
... (more columns)
```

---

## Table: `logistics.groups`
- **Record Count:** 51
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| group_id | int | - | - |  |
| group_name | varchar | - | - |  |
| group_slug | varchar | - | - |  |
| group_desc | varchar | - | - |  |
| group_type | enum | - | - |  |
| is_active | tinyint | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| is_deleted | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "group_id": 17,
    "group_name": "Admin",
    "group_slug": "admin",
    "group_desc": "Admin",
    "group_type": "client"
}
... (more columns)
```

---

## Table: `logistics.groups_log`
- **Record Count:** 221
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-08-07 16:21:43",
    "ipaddress": "656769484",
    "log_id": 25
}
... (more columns)
```

---

## Table: `logistics.hawb_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| status | varchar | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.helpdesk_ticket`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| ticket_code | varchar | - | - |  |
| department_id | int | - | - |  |
| priority | enum | - | - |  |
| subject | varchar | - | - |  |
| status | enum | - | - |  |
| addedby | int | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.helpdesk_ticket_message`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| ticketid | bigint | - | - |  |
| message | varchar | - | - |  |
| attachment | varchar | - | - |  |
| addedby | int | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.hermes_datafile_id`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.hermes_postcode_record`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| fullpostcode | varchar | - | - |  |
| pos_pcd_postcode_excluded_indicator | char | - | - |  |
| sort_level_key | varchar | - | - |  |
| next_day_service | char | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.imcp`
- **Record Count:** 1853
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| opcode | varchar | - | - |  |
| imcpcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "opcode": "AEA",
    "imcpcode": "AEAUHA"
}
... (more columns)
```

---

## Table: `logistics.import_csv_consignment_temp`
- **Record Count:** 247
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| date_added | date | - | - |  |
| shipper_country_iso | varchar | - | - |  |
| receiver_country_iso | varchar | - | - |  |
| service_code | varchar | - | - |  |
| order_reference | varchar | - | - |  |
| shipper_company | varchar | - | - |  |
| shipper_contact | varchar | - | - |  |
| shipper_email | varchar | - | - |  |
| shipper_telephone | varchar | - | - |  |
| shipper_address_line_1 | varchar | - | - |  |
| shipper_address_line_2 | varchar | - | - |  |
| shipper_address_line_3 | varchar | - | - |  |
| shipper_city | varchar | - | - |  |
| shipper_state | varchar | - | - |  |
| shipper_postcode | varchar | - | - |  |
| receiver_company | varchar | - | - |  |
| receiver_contact | varchar | - | - |  |
| receiver_email | varchar | - | - |  |
| receiver_telephone | varchar | - | - |  |
| receiver_address_line_1 | varchar | - | - |  |
| receiver_address_line_2 | varchar | - | - |  |
| receiver_address_line_3 | varchar | - | - |  |
| receiver_city | varchar | - | - |  |
| receiver_state | varchar | - | - |  |
| receiver_postcode | varchar | - | - |  |
| reference | varchar | - | - |  |
| items_value | decimal | - | - |  |
| items_currency | varchar | - | - |  |
| item_type | varchar | - | - |  |
| note | text | - | - |  |
| description | text | - | - |  |
| bag_number | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| mawb_number | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| status | enum | - | - |  |
| is_complete | enum | - | - |  |
| batch_number | varchar | - | - |  |
| user_id | bigint | - | - |  |
| message | text | - | - |  |
| weight | text | - | - |  |
| length | text | - | - |  |
| height | text | - | - |  |
| width | text | - | - |  |
| itemvalue | text | - | - |  |
| parcel_item_desc | varchar | - | - |  |
| parcel_item_sku | varchar | - | - |  |
| parcel_item_url | varchar | - | - |  |
| parcel_item_quantity | int | - | - |  |
| parcel_item_value | decimal | - | - |  |
| parcel_item_weight | decimal | - | - |  |
| parcel_item_hs_code | varchar | - | - |  |
| parcel_item_manufacture_country | varchar | - | - |  |
| eori_number | varchar | - | - |  |
| vat_number | varchar | - | - |  |
| ioss_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "date_added": "2018-11-17",
    "shipper_country_iso": "GB",
    "receiver_country_iso": "GB",
    "service_code": "3HPA"
}
... (more columns)
```

---

## Table: `logistics.import_csv_tmp`
- **Record Count:** 5891
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| account | varchar | - | - |  |
| hawb | varchar | - | - |  |
| service | varchar | - | - |  |
| service_code | varchar | - | - |  |
| reference | varchar | - | - |  |
| date_submitted | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line1 | varchar | - | - |  |
| address_line2 | varchar | - | - |  |
| address_line3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country | varchar | - | - |  |
| post_code | varchar | - | - |  |
| telephone | varchar | - | - |  |
| number_of_pieces | varchar | - | - |  |
| weight | varchar | - | - |  |
| description | varchar | - | - |  |
| value | varchar | - | - |  |
| currency | varchar | - | - |  |
| notes | varchar | - | - |  |
| routing_non_routing | varchar | - | - |  |
| full_pallet | varchar | - | - |  |
| half_pallet | varchar | - | - |  |
| quarter_pallet | varchar | - | - |  |
| all_weight | varchar | - | - |  |
| width | varchar | - | - |  |
| heigh | varchar | - | - |  |
| length | varchar | - | - |  |
| item_type | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| email | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| bag_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| is_complete | tinyint | - | - |  |
| status | tinyint | - | - |  |
| message | text | - | - |  |
| batch_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "account": "sharwoodm@gmail.com",
    "hawb": "411121404",
    "service": "",
    "service_code": "Michael Sharwood"
}
... (more columns)
```

---

## Table: `logistics.international`
- **Record Count:** 128254
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| iata_country_code | char | - | - |  |
| zipcode_from | varchar | - | - |  |
| zipcode_to | varchar | - | - |  |
| air_express_depot | varchar | - | - |  |
| air_express_osort | varchar | - | - |  |
| air_express_dsort | varchar | - | - |  |
| dpd_classic_deport | varchar | - | - |  |
| dpd_classic_osort | varchar | - | - |  |
| dpd_classic_dsort | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 947416,
    "iata_country_code": "AD",
    "zipcode_from": "0",
    "zipcode_to": "Z",
    "air_express_depot": "0918-ARE"
}
... (more columns)
```

---

## Table: `logistics.invoice_bank_details`
- **Record Count:** 8
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| account_title | varchar | - | - |  |
| account_sortcode | varchar | - | - |  |
| account_number | int | - | - |  |
| account_iban | varchar | - | - |  |
| bank_name | varchar | - | - |  |
| bank_branch | varchar | - | - |  |
| bank_address | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| status | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "user_account_id": 148,
    "account_title": "testing here",
    "account_sortcode": "2",
    "account_number": 3
}
... (more columns)
```

---

## Table: `logistics.invoice_detail`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| charges_detail | text | - | - |  |
| total | int | - | - |  |
| vat | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.invoice_detail_backup`
- **Record Count:** 9624
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| hawb | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| remote_area_charge | decimal | - | - |  |
| on_farword_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| extra | decimal | - | - |  |
| hv | decimal | - | - |  |
| amount | decimal | - | - |  |
| agent_basic_charges | decimal | - | - |  |
| agent_fuel_charges | decimal | - | - |  |
| agent_additional_charges | decimal | - | - |  |
| agent_remote_area_charge | decimal | - | - |  |
| agent_on_farword_charges | decimal | - | - |  |
| agent_ndx | decimal | - | - |  |
| agent_ddp | decimal | - | - |  |
| agent_extra | decimal | - | - |  |
| agent_amount | decimal | - | - |  |
| agent_linehaul_cost | decimal | - | - |  |
| agent_handling_charges | decimal | - | - |  |
| reference | varchar | - | - |  |
| quotation_id | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 225227,
    "consignment_id": 8730008,
    "invoice_no": "",
    "hawb": "FR14599272477267",
    "basic_charges": "8.43"
}
... (more columns)
```

---

## Table: `logistics.invoice_detail_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.invoice_extra_charges`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| invoice_detail_id | int | - | - |  |
| charge_type_id | int | - | - |  |
| agent_id | int | - | - |  |
| cost_type | enum | - | - |  |
| cost | decimal | - | - |  |
| description | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.invoice_extra_charges_types`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| isactive | tinyint | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.invoice_templates`
- **Record Count:** 2
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| image | varchar | - | - |  |
| invoice_function | varchar | - | - |  |
| summary_invoice_function | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Default Template",
    "image": "default_template.png",
    "invoice_function": "SavePDFFile",
    "summary_invoice_function": "SaveSummaryInvoicePDFFile"
}
... (more columns)
```

---

## Table: `logistics.invoices`
- **Record Count:** 1762
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| user_account_id | int | - | - |  |
| net_amount | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| vatable_amount | decimal | - | - |  |
| vat | decimal | - | - |  |
| total_amount | decimal | - | - |  |
| weight | decimal | - | - |  |
| currency_id | int | - | - |  |
| exchange_rate | decimal | - | - |  |
| credit_type | enum | - | - |  |
| credit_amount | decimal | - | - |  |
| invoice_date | datetime | - | - |  |
| summary_pdf | varchar | - | - |  |
| pdf | varchar | - | - |  |
| csv | varchar | - | - |  |
| invoice_by | int | - | - |  |
| invoice_type | enum | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| is_email | bit | - | - |  |
| date_deleted | timestamp | - | - |  |
| is_paid | bit | - | - |  |
| is_cancel | bit | - | - |  |
| is_read | bit | - | - |  |
| paid_date | datetime | - | - |  |
| salepot_id | int | - | - |  |
| added_by | int | - | - |  |
| date_created | timestamp | - | - |  |
| date_updated | timestamp | - | - |  |
| invoice_heading | text | - | - |  |
| attached_files | text | - | - |  |
| invoice_reference | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 30166,
    "invoice_no": "30166",
    "user_account_id": null,
    "net_amount": null,
    "fuel_charges": null
}
... (more columns)
```

---

## Table: `logistics.invoices_manual`
- **Record Count:** 580
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| account_number | varchar | - | - |  |
| invoice_heading | varchar | - | - |  |
| invoice_amount | varchar | - | - |  |
| invoice_weight | varchar | - | - |  |
| vat_amount | varchar | - | - |  |
| invoice_total_amount | varchar | - | - |  |
| currency | varchar | - | - |  |
| exchange_rate | varchar | - | - |  |
| invoice_status | varchar | - | - |  |
| added_by | varchar | - | - |  |
| invoice_file | varchar | - | - |  |
| is_active | enum | - | - |  |
| is_deleted | enum | - | - |  |
| invoice_date | timestamp | - | - |  |
| date_created | timestamp | - | - |  |
| date_updated | timestamp | - | - |  |
| date_deleted | timestamp | - | - |  |
| is_email | enum | - | - |  |
| is_paid | enum | - | - |  |
| paid_date | timestamp | - | - |  |
| salepot_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 56759,
    "invoice_no": "56759",
    "account_number": "BUYLOGIC",
    "invoice_heading": "Clearance & Bag Charges; Apr'16",
    "invoice_amount": "1947.52"
}
... (more columns)
```

---

## Table: `logistics.invoices_manual_details`
- **Record Count:** 964
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| invoice_id | varchar | - | - |  |
| hawb | varchar | - | - |  |
| service_id | int | - | - |  |
| date_booked | datetime | - | - |  |
| reference | varchar | - | - |  |
| weight | varchar | - | - |  |
| amount | varchar | - | - |  |
| description | varchar | - | - |  |
| destination | varchar | - | - |  |
| vat_amount | decimal | - | - |  |
| is_vat | enum | - | - |  |
| created_by | int | - | - |  |
| date_created | datetime | - | - |  |
| updated_by | int | - | - |  |
| date_updated | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 42,
    "invoice_id": "56314",
    "hawb": "",
    "service_id": null,
    "date_booked": "0000-00-00 00:00:00"
}
... (more columns)
```

---

## Table: `logistics.invoices_number_range`
- **Record Count:** 57
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| is_default | tinyint | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| user_account_id | int | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| range_type | enum | - | - |  |
| date_created | timestamp | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 171,
    "is_default": 1,
    "range_start": 1000,
    "range_end": 10000000,
    "next_number": 1235
}
... (more columns)
```

---

## Table: `logistics.item_details`
- **Record Count:** 276
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| session_id | varchar | - | - |  |
| parcel_count | int | - | - |  |
| item_detail | text | - | - |  |
| user_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 12,
    "consignment_id": 170278,
    "session_id": "",
    "parcel_count": 0,
    "item_detail": "[{\"item_description\":\"test\",\"item_url\":\"\",\"item_sku\":\"3434\",\"no_of_items\":\"1\",\"item_value\":\"1\",\"weight\":\"1\",\"tariff_no\":\"\",\"hscode\":\"13232\",\"manufacture_country_iso\":\"AU\"}]"
}
... (more columns)
```

---

## Table: `logistics.label_file`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| account_number | varchar | - | - |  |
| hawb_list | text | - | - |  |
| created_date | datetime | - | - |  |
| error_list | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.language`
- **Record Count:** 4
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| language | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| created_by | int | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "language": "en-GB",
    "date_created": "2016-07-12 00:00:00",
    "created_by": null,
    "is_active": "Y"
}
... (more columns)
```

---

## Table: `logistics.language_keys`
- **Record Count:** 3364
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| keyword | varchar | - | - |  |
| language | varchar | - | - |  |
| caption | text | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| date_updated | timestamp | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "keyword": "admin",
    "language": "en-GB",
    "caption": "Administrator",
    "date_created": "2016-07-10 02:24:59"
}
... (more columns)
```

---

## Table: `logistics.licence_plate`
- **Record Count:** 83
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| range_name | varchar | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| delivery_network | varchar | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| date_created | datetime | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |
| country_range | bit | - | - |  |
| country_list | text | - | - |  |
| range_reminder_limit | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 126,
    "range_name": "Yodel_1519406498",
    "range_start": 1,
    "range_end": 100000000000076,
    "next_number": 16
}
... (more columns)
```

---

## Table: `logistics.licence_plate_country`
- **Record Count:** 4
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| licence_plate_id | int | - | - |  |
| country_id | int | - | - |  |
| range_name | varchar | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| date_created | datetime | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "licence_plate_id": 112,
    "country_id": 13,
    "range_name": "Sweden Post Australia",
    "range_start": 91713500
}
... (more columns)
```

---

## Table: `logistics.location`
- **Record Count:** 15
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| date_updated | varchar | - | - |  |
| updatedby | timestamp | - | - |  |
| active | varchar | - | - |  |
| type | varchar | - | - |  |
| warehouseid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Stock System Racking",
    "date_created": "0000-00-00 00:00:00",
    "createdby": 189,
    "date_updated": "2016-06-13 9:21:01"
}
... (more columns)
```

---

## Table: `logistics.log_rack_shelf`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| rack_shelf_id | bigint | - | - |  |
| rack_shelf_item_id | bigint | - | - |  |
| customer_id | int | - | - |  |
| remarks | text | - | - |  |
| in_date | datetime | - | - |  |
| in_by | int | - | - |  |
| out_date | datetime | - | - |  |
| out_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.login_request`
- **Record Count:** 15397
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | int | - | - |  |
| user_name | varchar | - | - |  |
| ip_address | varchar | - | - |  |
| user_agent | text | - | - |  |
| login_status | tinyint | - | - |  |
| login_time | timestamp | - | - |  |
| logout_time | timestamp | - | - |  |
| session_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 15122,
    "user_id": 2332,
    "user_name": "testAccount",
    "ip_address": "139.135.43.147",
    "user_agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/121.0.0.0 Safari\/537.36"
}
... (more columns)
```

---

## Table: `logistics.manifest`
- **Record Count:** 9
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_id | int | - | - |  |
| file_name | varchar | - | - |  |
| label_link | varchar | - | - |  |
| date_created | datetime | - | - |  |
| pieces | varchar | - | - |  |
| agent_id | bigint | - | - |  |
| weight | decimal | - | - |  |
| service_id | int | - | - |  |
| handling | varchar | - | - |  |
| pdf_file | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| type | varchar | - | - |  |
| collection_comment | text | - | - |  |
| collection_date | datetime | - | - |  |
| collection_date_to | datetime | - | - |  |
| pickup_date | datetime | - | - |  |
| delivery_note | text | - | - |  |
| signature | varchar | - | - |  |
| pickup_id | int | - | - |  |
| route_warehouse_id | int | - | - |  |
| routing_email_date | datetime | - | - |  |
| date_received | datetime | - | - |  |
| received_by | varchar | - | - |  |
| name_of_driver | varchar | - | - |  |
| licence_number | varchar | - | - |  |
| account_owner | varchar | - | - |  |
| number_bag | varchar | - | - |  |
| product | varchar | - | - |  |
| carrier_note | varchar | - | - |  |
| carrier_pdf | varchar | - | - |  |
| carrier_id | int | - | - |  |
| is_deleted | enum | - | - |  |
| is_dispatched | enum | - | - |  |
| is_send_email | enum | - | - |  |
| manifest_by | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 809,
    "user_id": 58,
    "file_name": "2020_04_21\/148\/1587486963.csv",
    "label_link": "",
    "date_created": "2020-04-21 18:36:02"
}
... (more columns)
```

---

## Table: `logistics.manifest_consignment_mapping`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| manifestid | int | - | - |  |
| consignmentid | int | - | - |  |
| export_mawb | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.manifest_entity_mapping`
- **Record Count:** 345
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| manifest_id | int | - | - |  |
| manifest_entity_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "entity_id": 81539,
    "manifest_id": 809,
    "manifest_entity_type": "p"
}
... (more columns)
```

---

## Table: `logistics.manifest_service_mapping`
- **Record Count:** 20
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| manifest_id | bigint | - | - |  |
| service_id | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 42,
    "manifest_id": 809,
    "service_id": 2
}
... (more columns)
```

---

## Table: `logistics.market_place_documentation_mapping`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| marketplace_id | int | - | - |  |
| step_title | varchar | - | - |  |
| step_description | text | - | - |  |
| step_image | varchar | - | - |  |
| step_order | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | timestamp | - | - |  |
| added_by | int | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.market_places`
- **Record Count:** 81
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| title | varchar | - | - |  |
| description | varchar | - | - |  |
| translation_key | varchar | - | - |  |
| is_active | tinyint | - | - |  |
| page_link | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |
| is_delete | tinyint | - | - |  |
| integration_logo | varchar | - | - |  |
| manual_link | varchar | - | - |  |
| plugin_key | varchar | - | - |  |
| parent_id | int | - | - |  |
| integration_type | int | - | - |  |
| channel_type | int | - | - |  |
| country_id | int | - | - |  |
| last_sync | datetime | - | - |  |
| is_featured | bit | - | - |  |
| is_api2cart | bit | - | - |  |
| help_doc | varchar | - | - |  |
| class_name | varchar | - | - |  |
| documentation_title | varchar | - | - |  |
| documentation_cover_image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Amazon",
    "description": "Amazon",
    "translation_key": "AMAZON",
    "is_active": 1
}
... (more columns)
```

---

## Table: `logistics.market_places_authenticate_field`
- **Record Count:** 187
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| field_name | varchar | - | - |  |
| field_value | varchar | - | - |  |
| market_places_id | bigint | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| is_delete | tinyint | - | - |  |
| auto_generate_value | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 43,
    "field_name": "Amazon Access Key ",
    "field_value": "AWS_ACCESS_KEY_ID",
    "market_places_id": 1,
    "added_by": 188
}
... (more columns)
```

---

## Table: `logistics.marketplace_order`
- **Record Count:** 11
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| marketplace_id | bigint | - | - |  |
| marketplace_order_number | varchar | - | - |  |
| create_time | datetime | - | - |  |
| order_status | varchar | - | - |  |
| receiver_name | varchar | - | - |  |
| receiver_phone | varchar | - | - |  |
| receiver_state | varchar | - | - |  |
| receiver_city | varchar | - | - |  |
| receiver_country_id | bigint | - | - |  |
| receiver_addressline1 | varchar | - | - |  |
| receiver_addressline2 | varchar | - | - |  |
| receiver_postcode | varchar | - | - |  |
| payment_method | varchar | - | - |  |
| order_total | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| receiver_email | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| Ack | varchar | - | - |  |
| error_code | varchar | - | - |  |
| error_message | varchar | - | - |  |
| consignment_id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| shipped_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "marketplace_id": 88,
    "marketplace_order_number": "304",
    "create_time": "2020-03-26 18:33:20",
    "order_status": "Unshipped"
}
... (more columns)
```

---

## Table: `logistics.marketplace_order_details`
- **Record Count:** 24
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| marketplace_order_id | bigint | - | - |  |
| marketplace_item_id | varchar | - | - |  |
| sku | varchar | - | - |  |
| title | varchar | - | - |  |
| quantity_purchased | varchar | - | - |  |
| asin | varchar | - | - |  |
| item_price | varchar | - | - |  |
| currency | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "marketplace_order_id": 1,
    "marketplace_item_id": "1",
    "sku": "",
    "title": "Ship Your Idea - Blue"
}
... (more columns)
```

---

## Table: `logistics.mawb`
- **Record Count:** 106
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| mawb_number | varchar | - | - |  |
| mawb_source_country_id | int | - | - |  |
| mawb_source_warehouse_id | int | - | - |  |
| mawb_destination_country_id | int | - | - |  |
| mawb_destination_warehouse_id | int | - | - |  |
| is_active | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |
| mawb_status | enum | - | - |  |
| manifest_label | varchar | - | - |  |
| mawb_lv_manifest | varchar | - | - |  |
| mawb_hv_manifest | varchar | - | - |  |
| bagging_type | enum | - | - |  |
| bag_is_hv_lv | enum | - | - |  |
| mawb_class | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "mawb_number": "154-21271503",
    "mawb_source_country_id": 225,
    "mawb_source_warehouse_id": 36,
    "mawb_destination_country_id": 226
}
... (more columns)
```

---

## Table: `logistics.mawb_flight_document`
- **Record Count:** 1
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| document_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "document_name": "ExportSAManifest"
}
... (more columns)
```

---

## Table: `logistics.mawb_flight_document_mapping`
- **Record Count:** 1
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| country_id | bigint | - | - |  |
| document_id | bigint | - | - |  |
| template_id | bigint | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 197,
    "document_id": 1,
    "template_id": 1,
    "added_by": 148
}
... (more columns)
```

---

## Table: `logistics.mawb_parcel_mapping`
- **Record Count:** 4266
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb_id | int | - | - |  |
| parcel_id | int | - | - |  |
| wharehouse_id | int | - | - |  |
| bag_id | int | - | - |  |
| date_added | datetime | - | - |  |
| added_by | bigint | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "mawb_id": 10,
    "parcel_id": 83681,
    "wharehouse_id": 36,
    "bag_id": 0
}
... (more columns)
```

---

## Table: `logistics.not_found_record`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb | varchar | - | - |  |
| bag_number | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| scanned_by | bigint | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| weight | decimal | - | - |  |
| date_created | datetime | - | - |  |
| reason | varchar | - | - |  |
| image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.oauth_access_tokens`
- **Record Count:** 84924
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| access_token | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "access_token": "000051dd71d5b305935f85701a4fa1e511850167",
    "client_id": "developer",
    "user_id": 58,
    "expires": "2020-03-23 22:47:15",
    "scope": null
}
... (more columns)
```

---

## Table: `logistics.oauth_authorization_codes`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| authorization_code | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| redirect_uri | varchar | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.oauth_clients`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| client_secret | varchar | - | - |  |
| redirect_uri | varchar | - | - |  |
| grant_types | varchar | - | - |  |
| scope | varchar | - | - |  |
| user_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.oauth_jwt`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| subject | varchar | - | - |  |
| public_key | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.oauth_public_keys`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| public_key | varchar | - | - |  |
| private_key | varchar | - | - |  |
| encryption_algorithm | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.oauth_refresh_tokens`
- **Record Count:** 84483
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| refresh_token | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "refresh_token": "00024af79d1f39f7a000c646911b1e6eae2dcb8c",
    "client_id": "developer",
    "user_id": 58,
    "expires": "2020-04-06 22:52:02",
    "scope": null
}
... (more columns)
```

---

## Table: `logistics.oauth_scopes`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| scope | varchar | - | - |  |
| is_default | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.ops_summary`
- **Record Count:** 2583
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account | varchar | - | - |  |
| services | varchar | - | - |  |
| country | varchar | - | - |  |
| quantity | varchar | - | - |  |
| weight | varchar | - | - |  |
| carrier | varchar | - | - |  |
| date_submitted | datetime | - | - |  |
| reference | varchar | - | - |  |
| mawb | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "account": "BFELOGCH",
    "services": "DBP",
    "country": "",
    "quantity": "55"
}
... (more columns)
```

---

## Table: `logistics.optimus_file_name`
- **Record Count:** 40745
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_name": "00000001",
    "sent_date": null,
    "file_id": "1"
}
... (more columns)
```

---

## Table: `logistics.owe_southafrica_postcode`
- **Record Count:** 5864
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| zone | varchar | - | - |  |
| postcode | varchar | - | - |  |
| main_outlying | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "zone": "PRY",
    "postcode": "0001",
    "main_outlying": "O"
}
... (more columns)
```

---

## Table: `logistics.owe_southafrica_routine`
- **Record Count:** 571
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state | varchar | - | - |  |
| zone | varchar | - | - |  |
| route | varchar | - | - |  |
| delivery_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "state": "Aberdeen",
    "zone": "PLZ2",
    "route": "RD4",
    "delivery_time": 3
}
... (more columns)
```

---

## Table: `logistics.pallet`
- **Record Count:** 3369
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| palletno | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| close | tinyint | - | - |  |
| userid | int | - | - |  |
| pallet_carrier_id | int | - | - |  |
| date_dispatch | timestamp | - | - |  |
| dispatch_userid | int | - | - |  |
| type | varchar | - | - |  |
| manifestid | int | - | - |  |
| hub | varchar | - | - |  |
| is_active | tinyint | - | - |  |
| comments | varchar | - | - |  |
| label | varchar | - | - |  |
| pallet_source_country_id | int | - | - |  |
| pallet_source_warehouse_id | int | - | - |  |
| pallet_destination_country_id | int | - | - |  |
| pallet_destination_warehouse_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 142,
    "palletno": null,
    "date_created": "2015-12-30 22:53:16",
    "close": 0,
    "userid": 0
}
... (more columns)
```

---

## Table: `logistics.pallet_bag_mapping`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| palletid | int | - | - |  |
| bagid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.pallet_bag_remove_reason`
- **Record Count:** 1
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pallet_id | int | - | - |  |
| bag_id | int | - | - |  |
| reason | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pallet_id": 3934,
    "bag_id": 10263,
    "reason": "Testing"
}
... (more columns)
```

---

## Table: `logistics.pallet_carier_group`
- **Record Count:** 6
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| group_name | varchar | - | - |  |
| carrier_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "group_name": "Quality Assurance",
    "carrier_id": 194
}
... (more columns)
```

---

## Table: `logistics.pallet_carrier`
- **Record Count:** 3
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| service_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "ROYAL MAIL TRACKED 48",
    "service_id": "12"
}
... (more columns)
```

---

## Table: `logistics.pallet_carrier_service`
- **Record Count:** 11
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_group_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "carrier_group_id": 2,
    "service_id": 261
}
... (more columns)
```

---

## Table: `logistics.pallet_entity_mapping`
- **Record Count:** 33
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pallet_id | int | - | - |  |
| entity_id | int | - | - |  |
| pallet_entity_type | enum | - | - |  |
| pre_sort | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pallet_id": 3928,
    "entity_id": 10091,
    "pallet_entity_type": "b",
    "pre_sort": "n"
}
... (more columns)
```

---

## Table: `logistics.pallet_location`
- **Record Count:** 106533
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| locationid | int | - | - |  |
| palletid | int | - | - |  |
| consignmentid | int | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| comments | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "locationid": 1,
    "palletid": 2171,
    "consignmentid": null,
    "date_created": "2016-06-13 17:07:27"
}
... (more columns)
```

---

## Table: `logistics.pallet_name`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| serviceid | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.parcel`
- **Record Count:** 149163
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| do_tracking_number | varchar | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| weight | decimal | - | - |  |
| description | text | - | - |  |
| parcel_message | text | - | - |  |
| qty | varchar | - | - |  |
| commoditycode | varchar | - | - |  |
| hscode | varchar | - | - |  |
| grossweight | decimal | - | - |  |
| pweight | varchar | - | - |  |
| itemvalue | varchar | - | - |  |
| number_item | int | - | - |  |
| tarrif_no | varchar | - | - |  |
| update_weight | decimal | - | - |  |
| owe_status_code | varchar | - | - |  |
| chute_sorted | int | - | - |  |
| parcel_status_code | int | - | - |  |
| routing_code | varchar | - | - |  |
| last_tracking_update | datetime | - | - |  |
| parcel_item_desc | text | - | - |  |
| parcel_label | varchar | - | - |  |
| itemsku | varchar | - | - |  |
| itemurl | varchar | - | - |  |
| sort_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 81476,
    "consignment_id": 58422,
    "tracking_number": "RE413520650SE",
    "do_tracking_number": null,
    "length": "0.00"
}
... (more columns)
```

---

## Table: `logistics.parcel_bagging_mapping`
- **Record Count:** 4066
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| parcel_id | int | - | - |  |
| bag_id | int | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "parcel_id": 219721,
    "bag_id": 10075,
    "added_by": 0,
    "added_date": "0000-00-00 00:00:00"
}
... (more columns)
```

---

## Table: `logistics.parcel_iteam`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| parcel_id | int | - | - |  |
| iteam_name | varchar | - | - |  |
| iteam_weight | decimal | - | - |  |
| iteam_weight_unit | enum | - | - |  |
| iteam_value | int | - | - |  |
| iteam_quantity | int | - | - |  |
| iteam_country_id | int | - | - |  |
| iteam_description | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.parcel_log`
- **Record Count:** 1
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 0,
    "userid": 58,
    "logdate": "2019-07-12 19:45:27",
    "ipaddress": "3937179205",
    "log_id": 139002
}
... (more columns)
```

---

## Table: `logistics.parcelforce_datafile_id`
- **Record Count:** 155
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1070,
    "file_name": "PGO",
    "sent_date": "2016-04-22 10:33:44",
    "file_name_id": 1
}
... (more columns)
```

---

## Table: `logistics.parcelforce_depo_detail`
- **Record Count:** 11312
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_name | varchar | - | - |  |
| depo_short_name | varchar | - | - |  |
| postcode | varchar | - | - |  |
| route_number | varchar | - | - |  |
| pfw_ect | varchar | - | - |  |
| pfw_lat | varchar | - | - |  |
| pfw_lct | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "depo_name": "Aberdeen Depot",
    "depo_short_name": "ABER",
    "postcode": "AB21",
    "route_number": "R000"
}
... (more columns)
```

---

## Table: `logistics.parcelforce_hub_details`
- **Record Count:** 382
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_name | varchar | - | - |  |
| depo_number | varchar | - | - |  |
| mon_hub_24 | varchar | - | - |  |
| mon_chute_24 | varchar | - | - |  |
| mon_hub_48 | varchar | - | - |  |
| mon_chute_48 | varchar | - | - |  |
| tue_hub_24 | varchar | - | - |  |
| tue_chute_24 | varchar | - | - |  |
| tue_hub_48 | varchar | - | - |  |
| tue_chute_48 | varchar | - | - |  |
| wed_hub_24 | varchar | - | - |  |
| wed_chute_24 | varchar | - | - |  |
| wed_hub_48 | varchar | - | - |  |
| wed_chute_48 | varchar | - | - |  |
| thu_hub_24 | varchar | - | - |  |
| thu_chute_24 | varchar | - | - |  |
| thu_hub_48 | varchar | - | - |  |
| thu_chute_48 | varchar | - | - |  |
| fri_hub_24 | varchar | - | - |  |
| fri_chute_24 | varchar | - | - |  |
| fri_hub_48 | varchar | - | - |  |
| fri_chute_48 | varchar | - | - |  |
| sat_hub_24 | varchar | - | - |  |
| sat_chute_24 | varchar | - | - |  |
| sat_hub_48 | varchar | - | - |  |
| sat_chute_48 | varchar | - | - |  |
| sun_hub_24 | varchar | - | - |  |
| sun_chute_24 | varchar | - | - |  |
| sun_hub_48 | varchar | - | - |  |
| sun_chute_48 | varchar | - | - |  |
| sat_delivery_hub | varchar | - | - |  |
| sat_delivery_chute | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "depo_name": "Aberdeen Depot",
    "depo_number": "D901",
    "mon_hub_24": "NWSC",
    "mon_chute_24": ""
}
... (more columns)
```

---

## Table: `logistics.parcelforu_pickup_point`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| company | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| statuscode | varchar | - | - |  |
| status_description | varchar | - | - |  |
| latitude | varchar | - | - |  |
| longitude | varchar | - | - |  |
| mon | varchar | - | - |  |
| tue | varchar | - | - |  |
| wed | varchar | - | - |  |
| thu | varchar | - | - |  |
| fri | varchar | - | - |  |
| sat | varchar | - | - |  |
| sun | varchar | - | - |  |
| label_routing | varchar | - | - |  |
| branch_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.partnerservicesrouting`
- **Record Count:** 128
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | int | - | - |  |
| product_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 225,
    "from_weight": "0.00",
    "to_weight": "0.25",
    "status": 1
}
... (more columns)
```

---

## Table: `logistics.payment_gateways`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_id | bigint | - | - |  |
| gateway_type | enum | - | - |  |
| email | varchar | - | - |  |
| currency_id | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.payments_history`
- **Record Count:** 19404
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | bigint | - | - |  |
| paypal_payment_id | varchar | - | - |  |
| txn_id | varchar | - | - |  |
| billing_id | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| amount | double | - | - |  |
| amount_currency_id | int | - | - |  |
| payment_method | enum | - | - |  |
| payment_detail | varchar | - | - |  |
| user_currency_id | int | - | - |  |
| debit | decimal | - | - |  |
| credit | decimal | - | - |  |
| module_name | varchar | - | - |  |
| module_id | varchar | - | - |  |
| invoice_id | int | - | - |  |
| payment_status | varchar | - | - |  |
| is_completed | enum | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "account_id": 148,
    "paypal_payment_id": null,
    "txn_id": "",
    "billing_id": "0316846313165"
}
... (more columns)
```

---

## Table: `logistics.pbt_datafile_id`
- **Record Count:** 13
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_name": "AVOWE00001",
    "sent_date": "2016-05-09 09:36:10"
}
... (more columns)
```

---

## Table: `logistics.pbt_routine`
- **Record Count:** 2322
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_code | varchar | - | - |  |
| courier_file_label_code | varchar | - | - |  |
| transport_label_code | varchar | - | - |  |
| courier_charges_code | varchar | - | - |  |
| area_desc | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_code": "100",
    "courier_file_label_code": "WRE",
    "transport_label_code": "WRE",
    "courier_charges_code": "A"
}
... (more columns)
```

---

## Table: `logistics.permissions`
- **Record Count:** 406
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| lang_key | varchar | - | - |  |
| parent_id | int | - | - |  |
| file_name | varchar | - | - |  |
| description | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| query_string | varchar | - | - |  |
| icon | varchar | - | - |  |
| sort_order | int | - | - |  |
| is_menu_item | tinyint | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "lang_key": "LEFT_MENU_PERMISSIONS_&_ACTIONS",
    "parent_id": 0,
    "file_name": "#",
    "description": "Manage Permissions"
}
... (more columns)
```

---

## Table: `logistics.permissions_log`
- **Record Count:** 216
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-09-05 15:38:29",
    "ipaddress": "657980247",
    "log_id": 350
}
... (more columns)
```

---

## Table: `logistics.pickup`
- **Record Count:** 434
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pickup_number | varchar | - | - |  |
| pickup_date | timestamp | - | - |  |
| delivery_note | varchar | - | - |  |
| pick_up_pdf | varchar | - | - |  |
| collection_pdf | varchar | - | - |  |
| collection_address | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pickup_number": "",
    "pickup_date": "2016-08-05 19:55:00",
    "delivery_note": "Picked up by kazim",
    "pick_up_pdf": null
}
... (more columns)
```

---

## Table: `logistics.pmp_routine`
- **Record Count:** 3055
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| storeid | int | - | - |  |
| store_name | varchar | - | - |  |
| is_active | bit | - | - |  |
| country | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| telephone | varchar | - | - |  |
| depot_no | int | - | - |  |
| depot_description | varchar | - | - |  |
| round1 | int | - | - |  |
| drop1 | int | - | - |  |
| round2 | int | - | - |  |
| drop2 | int | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "storeid": 212502,
    "store_name": "Pass My Parcel",
    "is_active": 1,
    "country": "GB"
}
... (more columns)
```

---

## Table: `logistics.post_italia_routing`
- **Record Count:** 4616
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| zip_code | varchar | - | - |  |
| routing_file | varchar | - | - |  |
| province | varchar | - | - |  |
| province_iso_code | varchar | - | - |  |
| sortation_name | varchar | - | - |  |
| sortation_id | varchar | - | - |  |
| sortation_name_on_bag | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4620,
    "zip_code": "00010",
    "routing_file": "00010-MIX-RM-x28",
    "province": "Roma",
    "province_iso_code": "RM"
}
... (more columns)
```

---

## Table: `logistics.postcode_user_service_charges`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| from_postcode | varchar | - | - |  |
| to_postcode | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| city_name | varchar | - | - |  |
| country_iso | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.postitalia_untracked`
- **Record Count:** 4643
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| postcode | int | - | - |  |
| region | varchar | - | - |  |
| provenience | varchar | - | - |  |
| sortation | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "postcode": 67010,
    "region": "ABRUZZO",
    "provenience": "AQ",
    "sortation": "1 - Roma"
}
... (more columns)
```

---

## Table: `logistics.postnl_datafile_id`
- **Record Count:** 37
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| service_country | varchar | - | - |  |
| file_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4,
    "file_name": "VM000001",
    "sent_date": "2016-09-22 10:45:47",
    "service_country": "BE",
    "file_id": 1
}
... (more columns)
```

---

## Table: `logistics.pre_alert`
- **Record Count:** 3460
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb_id | int | - | - |  |
| flight_number | varchar | - | - |  |
| pieces | varchar | - | - |  |
| weight | varchar | - | - |  |
| etd | varchar | - | - |  |
| eta | varchar | - | - |  |
| current_status | varchar | - | - |  |
| date_time | varchar | - | - |  |
| cleared | varchar | - | - |  |
| status | varchar | - | - |  |
| comments | varchar | - | - |  |
| account | varchar | - | - |  |
| files | text | - | - |  |
| uploadby | varchar | - | - |  |
| shed | varchar | - | - |  |
| date_entry | varchar | - | - |  |
| created_by | int | - | - |  |
| date_updated | varchar | - | - |  |
| updated_by | int | - | - |  |
| type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 245,
    "mawb_id": 7,
    "flight_number": "548",
    "pieces": "5",
    "weight": "8"
}
... (more columns)
```

---

## Table: `logistics.pricing_bulk_data_1579539860`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| hawb | varchar | - | - |  |
| charges_reference | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| remote_area_charges | decimal | - | - |  |
| handling_charges | decimal | - | - |  |
| reference | decimal | - | - |  |
| linehaul | decimal | - | - |  |
| address_correction | decimal | - | - |  |
| airline_handling | decimal | - | - |  |
| clearance | decimal | - | - |  |
| collections | decimal | - | - |  |
| ddp_admin_fee | decimal | - | - |  |
| ddp_charge | decimal | - | - |  |
| delivery | decimal | - | - |  |
| dispatch | decimal | - | - |  |
| labour | decimal | - | - |  |
| other | decimal | - | - |  |
| out_of_gauge | decimal | - | - |  |
| over_weight | decimal | - | - |  |
| ras | decimal | - | - |  |
| redelivery | decimal | - | - |  |
| label_charges | decimal | - | - |  |
| vat | decimal | - | - |  |
| discount | decimal | - | - |  |
| mobile_tracking | decimal | - | - |  |
| user_account | varchar | - | - |  |
| status | tinyint | - | - |  |
| is_complete | tinyint | - | - |  |
| message | varchar | - | - |  |
| batch_number | varchar | - | - |  |
| currency | varchar | - | - |  |
| data_result | text | - | - |  |
| data_result_count | tinyint | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.pricing_bulk_data_1579539864`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| hawb | varchar | - | - |  |
| charges_reference | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| remote_area_charges | decimal | - | - |  |
| handling_charges | decimal | - | - |  |
| reference | decimal | - | - |  |
| linehaul | decimal | - | - |  |
| address_correction | decimal | - | - |  |
| airline_handling | decimal | - | - |  |
| clearance | decimal | - | - |  |
| collections | decimal | - | - |  |
| ddp_admin_fee | decimal | - | - |  |
| ddp_charge | decimal | - | - |  |
| delivery | decimal | - | - |  |
| dispatch | decimal | - | - |  |
| labour | decimal | - | - |  |
| other | decimal | - | - |  |
| out_of_gauge | decimal | - | - |  |
| over_weight | decimal | - | - |  |
| ras | decimal | - | - |  |
| redelivery | decimal | - | - |  |
| label_charges | decimal | - | - |  |
| vat | decimal | - | - |  |
| discount | decimal | - | - |  |
| mobile_tracking | decimal | - | - |  |
| user_account | varchar | - | - |  |
| status | tinyint | - | - |  |
| is_complete | tinyint | - | - |  |
| message | varchar | - | - |  |
| batch_number | varchar | - | - |  |
| currency | varchar | - | - |  |
| data_result | text | - | - |  |
| data_result_count | tinyint | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.product_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.product_routine_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| message | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.products`
- **Record Count:** 2
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| product_name | varchar | - | - |  |
| insurance | decimal | - | - |  |
| description | varchar | - | - |  |
| status | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| logo | varchar | - | - |  |
| length | int | - | - |  |
| width | int | - | - |  |
| height | int | - | - |  |
| vol_weight | decimal | - | - |  |
| vol_denominator | int | - | - |  |
| is_untrack | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| added_date | timestamp | - | - |  |
| added_by | int | - | - |  |
| transit_time | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 225,
    "product_name": "YODEL_TEST_PRODUCT",
    "insurance": "10.00",
    "description": ""
}
... (more columns)
```

---

## Table: `logistics.proforma_invoice_biiling`
- **Record Count:** 5135
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| billing_company | varchar | - | - |  |
| billing_contact | varchar | - | - |  |
| billing_address_line_1 | varchar | - | - |  |
| billing_address_line_2 | varchar | - | - |  |
| billing_address_line_3 | varchar | - | - |  |
| billing_city | varchar | - | - |  |
| billing_country | varchar | - | - |  |
| billing_postcode | varchar | - | - |  |
| billing_telephone | varchar | - | - |  |
| payment_terms | varchar | - | - |  |
| export_type | varchar | - | - |  |
| comments | varchar | - | - |  |
| delivery_terms | varchar | - | - |  |
| link_file | varchar | - | - |  |
| payer_vat | varchar | - | - |  |
| harm_comm_code | varchar | - | - |  |
| export | varchar | - | - |  |
| invoice_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "consignment_id": 3549039,
    "billing_company": "test",
    "billing_contact": "t",
    "billing_address_line_1": "t"
}
... (more columns)
```

---

## Table: `logistics.quotation_details`
- **Record Count:** 58
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| shipping_from | bigint | - | - |  |
| shipping_to | bigint | - | - |  |
| carrier_id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| account_id | bigint | - | - |  |
| price_type | enum | - | - |  |
| pieces | int | - | - |  |
| currency_id | int | - | - |  |
| weight | decimal | - | - |  |
| dimensions | longtext | - | - |  |
| volumn_weight | decimal | - | - |  |
| basic_charge | decimal | - | - |  |
| vat_charge | decimal | - | - |  |
| extra_charge | decimal | - | - |  |
| sub_total | decimal | - | - |  |
| discount | decimal | - | - |  |
| user_email | varchar | - | - |  |
| discount_type | enum | - | - |  |
| total_charge | decimal | - | - |  |
| remark | text | - | - |  |
| status | enum | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| conversionrate | float | - | - |  |
| pdf | varchar | - | - |  |
| date_created | datetime | - | - |  |
| added_by | bigint | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 9,
    "shipping_from": 0,
    "shipping_to": 0,
    "carrier_id": null,
    "service_id": null
}
... (more columns)
```

---

## Table: `logistics.rack`
- **Record Count:** 17
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| warehouse_id | int | - | - |  |
| title | varchar | - | - |  |
| short_title | varchar | - | - |  |
| rack_rows | int | - | - |  |
| rack_cols | int | - | - |  |
| shelf_dimension | varchar | - | - |  |
| shelf_max_weight | decimal | - | - |  |
| is_york | tinyint | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11,
    "warehouse_id": 9,
    "title": "Rack 1",
    "short_title": "R1",
    "rack_rows": 5
}
... (more columns)
```

---

## Table: `logistics.rack_shelf`
- **Record Count:** 5044
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| rack_id | int | - | - |  |
| shelf_no | int | - | - |  |
| is_filled | tinyint | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 430,
    "rack_id": 11,
    "shelf_no": 0,
    "is_filled": 1,
    "updated_date": "2016-01-18 16:12:29"
}
... (more columns)
```

---

## Table: `logistics.rack_shelf_item`
- **Record Count:** 1730
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| goods_name | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| description | text | - | - |  |
| weight | decimal | - | - |  |
| dimension | varchar | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "goods_name": "test",
    "tracking_number": null,
    "description": "test desc",
    "weight": "20.00"
}
... (more columns)
```

---

## Table: `logistics.ratebands`
- **Record Count:** 829
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| courier_service_id | int | - | - |  |
| name | varchar | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 270,
    "courier_service_id": 2,
    "name": "United Kingdom",
    "orderq": 0,
    "active": 1
}
... (more columns)
```

---

## Table: `logistics.reamus_destination_station`
- **Record Count:** 202813
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_code | varchar | - | - |  |
| postcode_from | varchar | - | - |  |
| postcode_to | varchar | - | - |  |
| product_code | varchar | - | - |  |
| station_id | varchar | - | - |  |
| hub_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_code": "GB",
    "postcode_from": "AB100AA",
    "postcode_to": "AB169ZZ",
    "product_code": "01"
}
... (more columns)
```

---

## Table: `logistics.reamus_exception`
- **Record Count:** 7698
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_code | varchar | - | - |  |
| postcode_from | varchar | - | - |  |
| postcode_to | varchar | - | - |  |
| product_code | varchar | - | - |  |
| feature_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_code": "GB",
    "postcode_from": "AB100AA",
    "postcode_to": "AB109ZZ",
    "product_code": "01"
}
... (more columns)
```

---

## Table: `logistics.reamus_product_service`
- **Record Count:** 3397
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reamus_id | varchar | - | - |  |
| product_code | varchar | - | - |  |
| feature_code | varchar | - | - |  |
| exception | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "reamus_id": "077009",
    "product_code": "01",
    "feature_code": "02",
    "exception": "E"
}
... (more columns)
```

---

## Table: `logistics.reamus_service`
- **Record Count:** 77
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| service_description | varchar | - | - |  |
| product_line1 | varchar | - | - |  |
| product_line2 | varchar | - | - |  |
| product_code | varchar | - | - |  |
| date_code | varchar | - | - |  |
| day_text | varchar | - | - |  |
| time_code | varchar | - | - |  |
| time_text | varchar | - | - |  |
| handling | varchar | - | - |  |
| feature_id | varchar | - | - |  |
| feature_code | varchar | - | - |  |
| file_type | varchar | - | - |  |
| consignment_flag | varchar | - | - |  |
| ds_flag | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "service_id": 10,
    "service_description": "PRIORITY 12:00",
    "product_line1": "VAN MON TO FRI",
    "product_line2": "PRE 12 POD"
}
... (more columns)
```

---

## Table: `logistics.reamus_site`
- **Record Count:** 2316
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reamus_id | varchar | - | - |  |
| site | varchar | - | - |  |
| reamus_id2 | varchar | - | - |  |
| country_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "reamus_id": "077009",
    "site": "52_HIGH-G161",
    "reamus_id2": "077009",
    "country_code": "GB"
}
... (more columns)
```

---

## Table: `logistics.remotearea_charges_carrier`
- **Record Count:** 1
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "remotearea_group_id": null,
    "remotearea_charges": null,
    "is_deleted": "N",
    "added_by": 58
}
... (more columns)
```

---

## Table: `logistics.remotearea_charges_carrier_user`
- **Record Count:** 2
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| user_account_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "remotearea_group_id": 2,
    "user_account_id": 2234,
    "remotearea_charges": "12.00",
    "is_deleted": "N"
}
... (more columns)
```

---

## Table: `logistics.remotearea_charges_services`
- **Record Count:** 1
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| service_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "remotearea_group_id": 16,
    "service_id": 252,
    "remotearea_charges": "5.00",
    "from_weight": "0.00"
}
... (more columns)
```

---

## Table: `logistics.remotearea_charges_services_user`
- **Record Count:** 20
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| service_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| user_account_id | int | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "remotearea_group_id": 16,
    "remotearea_charges": "5.00",
    "service_id": 252,
    "from_weight": "0.00"
}
... (more columns)
```

---

## Table: `logistics.remotearea_charges_tariffs`
- **Record Count:** 454
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| tariff_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "remotearea_group_id": 324,
    "remotearea_charges": "3.00",
    "tariff_id": 240542,
    "from_weight": "1.00"
}
... (more columns)
```

---

## Table: `logistics.remotearea_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| service_code | varchar | - | - |  |
| charges | varchar | - | - |  |
| remotearea_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.remotearea_weight_charge`
- **Record Count:** 240
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| service_code | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| formulla | varchar | - | - |  |
| charges | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "weight_from": "0.00",
    "weight_to": "0.50",
    "service_code": "TINA",
    "country_iso": "ZM"
}
... (more columns)
```

---

## Table: `logistics.remoteareas`
- **Record Count:** 11492
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remoteareas_groups_id | int | - | - |  |
| country_id | int | - | - |  |
| from_postcode | varchar | - | - |  |
| to_postcode | varchar | - | - |  |
| city | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6,
    "remoteareas_groups_id": 7,
    "country_id": 225,
    "from_postcode": "ub3 4jj",
    "to_postcode": "UB2 5QJ"
}
... (more columns)
```

---

## Table: `logistics.remoteareas_groups`
- **Record Count:** 322
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| group_name | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "group_name": "TEST123",
    "is_deleted": "Y",
    "added_by": 148
}
... (more columns)
```

---

## Table: `logistics.remoteareas_groups_log`
- **Record Count:** 21
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2170,
    "logdate": "2018-11-29 18:21:40",
    "ipaddress": "656773136",
    "log_id": 31
}
... (more columns)
```

---

## Table: `logistics.remoteareas_log`
- **Record Count:** 56
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2170,
    "logdate": "2018-11-29 18:22:31",
    "ipaddress": "656773136",
    "log_id": 4481
}
... (more columns)
```

---

## Table: `logistics.report_customize_settings`
- **Record Count:** 10
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | int | - | - |  |
| report_title | varchar | - | - |  |
| report_key | varchar | - | - |  |
| fields_data | longtext | - | - |  |
| date_added | datetime | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "account_id": 148,
    "report_title": "Royal mail Template",
    "report_key": "tracking_status_report",
    "fields_data": "a:21:{i:0;a:2:{s:3:\"key\";s:4:\"date\";s:5:\"title\";s:4:\"Date\";}i:1;a:2:{s:3:\"key\";s:15:\"tracking_number\";s:5:\"title\";s:15:\"Tracking Number\";}i:2;a:2:{s:3:\"key\";s:4:\"hawb\";s:5:\"title\";s:4:\"HAWB\";}i:3;a:2:{s:3:\"key\";s:7:\"service\";s:5:\"title\";s:7:\"Service\";}i:4;a:2:{s:3:\"key\";s:4:\"city\";s:5:\"title\";s:4:\"City\";}i:5;a:2:{s:3:\"key\";s:7:\"country\";s:5:\"title\";s:7:\"Country\";}i:6;a:2:{s:3:\"key\";s:6:\"weight\";s:5:\"title\";s:10:\"Weight(Kg)\";}i:7;a:2:{s:3:\"key\";s:17:\"volumetric_weight\";s:5:\"title\";s:17:\"Volumetric Weight\";}i:8;a:2:{s:3:\"key\";s:16:\"volumetric_liter\";s:5:\"title\";s:16:\"Volumetric Liter\";}i:9;a:2:{s:3:\"key\";s:5:\"lxwxh\";s:5:\"title\";s:5:\"LXWXH\";}i:10;a:2:{s:3:\"key\";s:24:\"last_event_tracking_date\";s:5:\"title\";s:24:\"Last Event Tracking Date\";}i:11;a:2:{s:3:\"key\";s:6:\"status\";s:5:\"title\";s:6:\"Status\";}i:12;a:2:{s:3:\"key\";s:15:\"tracking_detail\";s:5:\"title\";s:15:\"Tracking Detail\";}i:13;a:2:{s:3:\"key\";s:16:\"delivery_on_time\";s:5:\"title\";s:16:\"Delivery On Time\";}i:14;a:2:{s:3:\"key\";s:25:\"delivery_aim_working_days\";s:5:\"title\";s:27:\"Delivery Aim (Working Days)\";}i:15;a:2:{s:3:\"key\";s:40:\"total_no_of_days_booking_to_hub_received\";s:5:\"title\";s:42:\"Total No of Days (Booking To Hub Received)\";}i:16;a:2:{s:3:\"key\";s:49:\"total_no_of_days_hub_received_to_carrier_received\";s:5:\"title\";s:52:\"Total No of Days  (Hub Received to carrier received)\";}i:17;a:2:{s:3:\"key\";s:52:\"total_no_of_days_from_carrier_received_calendar_days\";s:5:\"title\";s:54:\"Total No of Days From Carrier Received (Calendar Days)\";}i:18;a:2:{s:3:\"key\";s:46:\"total_no_of_working_days_from_carrier_received\";s:5:\"title\";s:46:\"Total No of Working Days From Carrier Received\";}i:19;a:2:{s:3:\"key\";s:32:\"total_transit_time_calendar_days\";s:5:\"title\";s:34:\"Total Transit Time (Calendar Days)\";}i:20;a:2:{s:3:\"key\";s:31:\"total_transit_time_working_days\";s:5:\"title\";s:33:\"Total Transit Time (Working Days)\";}}"
}
... (more columns)
```

---

## Table: `logistics.routing_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| product_id | int | - | - |  |
| user_id | int | - | - |  |
| routing_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.royalmail_docket_number`
- **Record Count:** 69890
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| docket_number | varchar | - | - |  |
| file_name | varchar | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tracking_number": "FG227557376GB",
    "docket_number": "7023653256",
    "file_name": "W46E190319DF",
    "date_created": "2020-01-28 17:26:50"
}
... (more columns)
```

---

## Table: `logistics.royalmail_sortcode`
- **Record Count:** 426
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode_area | varchar | - | - |  |
| sortcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1819,
    "postcode_area": "PA",
    "sortcode": "A87"
}
... (more columns)
```

---

## Table: `logistics.sales_call_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| date_call | date | - | - |  |
| meeting_date | timestamp | - | - |  |
| customer_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address | varchar | - | - |  |
| telephone | varchar | - | - |  |
| email | varchar | - | - |  |
| detail_discussed | text | - | - |  |
| document_link | varchar | - | - |  |
| follow_meeting_date | timestamp | - | - |  |
| userid | int | - | - |  |
| email_send | char | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.sales_pot_comission`
- **Record Count:** 15
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| date_added | datetime | - | - |  |
| no_of_shipments | int | - | - |  |
| comission | decimal | - | - |  |
| is_paid | tinyint | - | - |  |
| paid_by | int | - | - |  |
| paid_date | datetime | - | - |  |
| company_comission | decimal | - | - |  |
| sales_comission | decimal | - | - |  |
| salepot_table_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "date_added": "2016-08-18 15:38:46",
    "no_of_shipments": 5,
    "comission": "50.00",
    "is_paid": 1
}
... (more columns)
```

---

## Table: `logistics.service_agent_mapping`
- **Record Count:** 157
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| linehaul_agent | int | - | - |  |
| account_number | varchar | - | - |  |
| api_url | varchar | - | - |  |
| api_username | varchar | - | - |  |
| api_password | varchar | - | - |  |
| ftp_host | varchar | - | - |  |
| ftp_username | varchar | - | - |  |
| ftp_password | varchar | - | - |  |
| integration_type | varchar | - | - |  |
| class_file_name | varchar | - | - |  |
| insurance_charges | decimal | - | - |  |
| insurance_cover | decimal | - | - |  |
| reroute_charges | decimal | - | - |  |
| oversize_charges | decimal | - | - |  |
| address_change_charges | decimal | - | - |  |
| other_surcharges | decimal | - | - |  |
| return_charges | decimal | - | - |  |
| relabel_charges | decimal | - | - |  |
| wrong_address_charges | decimal | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| email | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 45,
    "serviceid": 63,
    "agentid": 4,
    "linehaul_agent": 0,
    "account_number": ""
}
... (more columns)
```

---

## Table: `logistics.service_collection_county`
- **Record Count:** 6
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| country_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 7,
    "service_id": 300,
    "country_id": 80
}
... (more columns)
```

---

## Table: `logistics.service_constant`
- **Record Count:** 434
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| constant | varchar | - | - |  |
| carrier_id | int | - | - |  |
| caption | varchar | - | - |  |
| description | varchar | - | - |  |
| design_control | varchar | - | - |  |
| mandatory | bit | - | - |  |
| sort_order | int | - | - |  |
| integration_type | enum | - | - |  |
| default_values | text | - | - |  |
| field_size | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "constant": "YODEL_SHIPPER_CONTACT",
    "carrier_id": 16,
    "caption": "Shipper Contact",
    "description": null
}
... (more columns)
```

---

## Table: `logistics.service_constant_value`
- **Record Count:** 1791
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| constant_value | varchar | - | - |  |
| service_id | int | - | - |  |
| agent_id | int | - | - |  |
| constant_id | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | int | - | - |  |
| date_update | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "constant_value": "ONE WORLD EXPRESS",
    "service_id": 226,
    "agent_id": 1,
    "constant_id": 1
}
... (more columns)
```

---

## Table: `logistics.service_country_ttime`
- **Record Count:** 9588
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| id_country | int | - | - |  |
| id_service | int | - | - |  |
| transit_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 46,
    "id_country": 116,
    "id_service": 143,
    "transit_time": 88
}
... (more columns)
```

---

## Table: `logistics.service_document`
- **Record Count:** 4
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| document_id | int | - | - |  |
| agent_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6,
    "service_id": 1,
    "document_id": 7,
    "agent_id": 3,
    "document_name": "1508154586aramex.png"
}
... (more columns)
```

---

## Table: `logistics.service_log`
- **Record Count:** 525
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-11-14 17:22:12",
    "ipaddress": "8919743130",
    "log_id": -1
}
... (more columns)
```

---

## Table: `logistics.service_range_mapping`
- **Record Count:** 137
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| agent_id | int | - | - |  |
| licence_plate_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 10,
    "service_id": 226,
    "agent_id": 1,
    "licence_plate_id": 92
}
... (more columns)
```

---

## Table: `logistics.services`
- **Record Count:** 2
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| code | varchar | - | - |  |
| carrier_id | int | - | - |  |
| account_number | varchar | - | - |  |
| type | varchar | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| wieght_type | int | - | - |  |
| supplier | varchar | - | - |  |
| service_type | enum | - | - |  |
| drop_off_service_id | bigint | - | - |  |
| description | text | - | - |  |
| fuel_surcharge_cost | decimal | - | - |  |
| fuel_surcharge | decimal | - | - |  |
| fuel_surcharge_type | char | - | - |  |
| max_length | decimal | - | - |  |
| max_width | decimal | - | - |  |
| max_height | decimal | - | - |  |
| max_volumetric_weight | decimal | - | - |  |
| volumetric_denominator | int | - | - |  |
| send_data_courier | tinyint | - | - |  |
| is_document | tinyint | - | - |  |
| friday_only_flag | tinyint | - | - |  |
| saturday_only_flag | tinyint | - | - |  |
| sunday_only_flag | tinyint | - | - |  |
| product_owner | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | tinyint | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| uploaded_currency | varchar | - | - |  |
| uploaded_currency_value | decimal | - | - |  |
| registration_fee | decimal | - | - |  |
| weight_after | decimal | - | - |  |
| aditional_charge | decimal | - | - |  |
| origin_country | int | - | - |  |
| is_untrack | bit | - | - |  |
| account_owner | int | - | - |  |
| remotearea | enum | - | - |  |
| carrier_address_limit | int | - | - |  |
| label_class_name | varchar | - | - |  |
| transit_time | int | - | - |  |
| required_email | tinyint | - | - |  |
| required_telephone | tinyint | - | - |  |
| shipment_type | enum | - | - |  |
| pre_sort | enum | - | - |  |
| proforma_invoice | tinyint | - | - |  |
| agent_dispatch | enum | - | - |  |
| brief_manifest | enum | - | - |  |
| delivery_mode | tinyint | - | - |  |
| insurance_available | tinyint | - | - |  |
| vol_wgt_formula | varchar | - | - |  |
| is_remotearea | enum | - | - |  |
| is_customized | bit | - | - |  |
| pre_advise | enum | - | - |  |
| pre_alert | enum | - | - |  |
| pre_alert_email | text | - | - |  |
| cut_off_time | varchar | - | - |  |
| label_charges | decimal | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| maximum_allowed_dimension | int | - | - |  |
| maximum_dim_formula | varchar | - | - |  |
| validation_type | enum | - | - |  |
| zone_type | enum | - | - |  |
| tariff_type | enum | - | - |  |
| girth | decimal | - | - |  |
| girth_formula | varchar | - | - |  |
| mail_type | enum | - | - |  |
| mail_option | enum | - | - |  |
| is_reschedulable | int | - | - |  |
| carrier_service_code | varchar | - | - |  |
| is_eori_required | int | - | - |  |
| delivery_type | enum | - | - |  |
| is_commercials | enum | - | - |  |
| is_cn | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 226,
    "name": "Next Day Delivery",
    "code": "AMZ001",
    "carrier_id": 16,
    "account_number": null
}
... (more columns)
```

---

## Table: `logistics.services_dpd`
- **Record Count:** 83
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| 2_digit_service_code | varchar | - | - |  |
| 3_digit_service_code | varchar | - | - |  |
| dpd_product_desc | varchar | - | - |  |
| dpd_label_service | varchar | - | - |  |
| ilk_product_desc | varchar | - | - |  |
| ilk_alternative_service_desc | varchar | - | - |  |
| premium | varchar | - | - |  |
| sec_dpd | varchar | - | - |  |
| sec_ilk | varchar | - | - |  |
| ilk_max_parcels_per_con | int | - | - |  |
| ilk_max_weight_per_parcel | int | - | - |  |
| dpd_max_parcels_per_con | int | - | - |  |
| dpd_max_weight_per_parcel | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "2_digit_service_code": "01",
    "3_digit_service_code": "801",
    "dpd_product_desc": "",
    "dpd_label_service": ""
}
... (more columns)
```

---

## Table: `logistics.shopping_platform`
- **Record Count:** 16
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| title | varchar | - | - |  |
| page_key | varchar | - | - |  |
| description | longtext | - | - |  |
| translation_key | varchar | - | - |  |
| plugin_key | varchar | - | - |  |
| integration_logo | varchar | - | - |  |
| display_option | tinyint | - | - |  |
| connect_url | varchar | - | - |  |
| active | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Magento 1.0",
    "page_key": "magento",
    "description": "<center><img src=\"..\/images\/magento.jpg\" \/><\/center>\n <div class=\"row\">\n <div class=\"col-md-9 col-sm-9 col-xs-9\">\n <div class=\"tab-content\">\n <div class=\"tab-pane active\" id=\"tab_7_1\">\n <h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.<\/h3>\n <p>Oneworld's multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.<\/p>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_2\">\n <p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, \n our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development \n teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier \n integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,\n OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld's innovative multi-carrier shipping software provides seamless \n integration with a wide range of UK and international parcel, packet and mail service providers, including:<\/p>\n <ul>\n <li>YODEL<\/li>\n <li>DHL Express<\/li>\n <li>UK Mail<\/li>\n <li>TNT Express<\/li>\n <li>DX Freight<\/li>\n <li>Whistl<\/li>\n <li>Asendia<\/li>\n <li>OneWorldorce<\/li>\n <li>Hermes \/ MyHermes<\/li>\n <li>Collect Plus<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_3\">\n <ul>\n <li>Integrates multiple carriers, reducing time and paperwork.<\/li>\n <li>Access OneWorld's 'pooled volume' discounted rates and save up to 80% on your existing rates.<\/li>\n <li>Multi-carrier web based tracking.<\/li>\n <li>Free Magento integration provided by our team of in-house software developers.<\/li>\n <li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).<\/li>\n <li>Dedicated in-house customer services team.<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_4\">\n <ul>\n <li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager<\/li>\n <li>2. Log in using your Magento admin username and password<\/li>\n <li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;<\/li>\n <li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_5\">\n <h3>System Requirements<\/h3>\n <ul>\n <li>Magento 1.9 or higher<\/li>\n <li>OneWorld shipping account<\/li>\n <li>At least one OneWorld Service Preference List configured<\/li>\n <li>OneWorld tracking API Key (optional)<\/li>\n <\/ul>\n <h3>Setup<\/h3>\n After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.<\/div>\n <\/div>\n <\/div>\n <div class=\"col-md-3 col-sm-3 col-xs-3\">\n <ul class=\"nav nav-tabs tabs-right\">\n <li class=\"active\"><a href=\"#tab_7_1\" data-toggle=\"tab\"> Integration <\/a><\/li>\n <li><a href=\"#tab_7_2\" data-toggle=\"tab\"> Save time and money <\/a><\/li>\n <li><a href=\"#tab_7_3\" data-toggle=\"tab\"> Features and Benefits<\/a><\/li>\n <li><a href=\"#tab_7_4\" data-toggle=\"tab\"> Instructions for installing <\/a><\/li>\n <li><a href=\"#tab_7_5\" data-toggle=\"tab\"> Setup Guide <\/a><\/li>\n <\/ul>\n <\/div>\n <\/div>",
    "translation_key": "MAGENTO 1.0"
}
... (more columns)
```

---

## Table: `logistics.sort_key_record`
- **Record Count:** 24184
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pos_sld_sort_level_key | varchar | - | - |  |
| pos_sld_level_1_type | varchar | - | - |  |
| pos_sld_level_1_name | varchar | - | - |  |
| pos_sld_level_1_code | varchar | - | - |  |
| pos_sld_level_2_type | varchar | - | - |  |
| pos_sld_level_2_name | varchar | - | - |  |
| pos_sld_level_2_code | varchar | - | - |  |
| pos_sld_level_3_type | varchar | - | - |  |
| pos_sld_level_3_name | varchar | - | - |  |
| pos_sld_level_3_code | varchar | - | - |  |
| pos_sld_level_4_type | varchar | - | - |  |
| pos_sld_level_4_name | varchar | - | - |  |
| pos_sld_level_4_code | varchar | - | - |  |
| pos_sld_level_5_type | varchar | - | - |  |
| pos_sld_level_5_name | varchar | - | - |  |
| pos_sld_level_5_code | varchar | - | - |  |
| pos_sld_hermes_barcode_1_to_7 | varchar | - | - |  |
| pos_sld_hermes_barcode_seq_key | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 46772,
    "pos_sld_sort_level_key": "010014",
    "pos_sld_level_1_type": "DEPOT",
    "pos_sld_level_1_name": "TYN2",
    "pos_sld_level_1_code": "01"
}
... (more columns)
```

---

## Table: `logistics.sorter_postcode_zone`
- **Record Count:** 119
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| area | varchar | - | - |  |
| postcode | varchar | - | - |  |
| zone | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "area": "Aberdeen",
    "postcode": "AB",
    "zone": "1"
}
... (more columns)
```

---

## Table: `logistics.sp_tariff_log`
- **Record Count:** 2783
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | int | - | - |  |
| charges_type | varchar | - | - |  |
| charges | decimal | - | - |  |
| formulla | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| consignment_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "account_id": 2255,
    "charges_type": "basic charges",
    "charges": "0.00",
    "formulla": null
}
... (more columns)
```

---

## Table: `logistics.status_reason`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| status_id | int | - | - |  |
| reason | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.tagnumber_range`
- **Record Count:** 8
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| service | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "range_start": 1000001,
    "range_end": 9999999,
    "next_number": 1001359,
    "increment_date": "0000-00-00 00:00:00"
}
... (more columns)
```

---

## Table: `logistics.tariff_additional_charges`
- **Record Count:** 39
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | bigint | - | - |  |
| consignment_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "tariff_id": 240542,
    "consignment_charges_types_id": 1,
    "charge": "2.00",
    "charge_type": "percentage"
}
... (more columns)
```

---

## Table: `logistics.tariff_details`
- **Record Count:** 8
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_name | varchar | - | - |  |
| status | bit | - | - |  |
| tariff_type | varchar | - | - |  |
| start_date | datetime | - | - |  |
| end_date | datetime | - | - |  |
| date_created | timestamp | - | - |  |
| added_by | int | - | - |  |
| currency | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 8,
    "tariff_name": "jaimin test",
    "status": 0,
    "tariff_type": "CHARGE",
    "start_date": "2017-02-01 00:00:00"
}
... (more columns)
```

---

## Table: `logistics.tariff_service_charges`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | bigint | - | - |  |
| tariff_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.tariff_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_name | int | - | - |  |
| user_id | int | - | - |  |
| tariff_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.tariffs`
- **Record Count:** 19083
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| carrier_id | int | - | - |  |
| service_id | int | - | - |  |
| name | varchar | - | - |  |
| status | tinyint | - | - |  |
| currency_id | int | - | - |  |
| tariff_type | enum | - | - |  |
| start_date | date | - | - |  |
| end_date | date | - | - |  |
| description | text | - | - |  |
| tariffs_pricing_rule_id | int | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 204777,
    "user_account_id": 0,
    "carrier_id": 0,
    "service_id": 75,
    "name": null
}
... (more columns)
```

---

## Table: `logistics.tariffs_account_mapping`
- **Record Count:** 144
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| user_account_id | int | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_id": 240403,
    "user_account_id": 2290,
    "added_date": "2018-12-07 10:05:03",
    "added_by": 1544177103
}
... (more columns)
```

---

## Table: `logistics.tariffs_details`
- **Record Count:** 27391
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariffs_id | int | - | - |  |
| from_zone_id | int | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| weight_cost | decimal | - | - |  |
| piece_cost | decimal | - | - |  |
| formula | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 22,
    "tariffs_id": 240393,
    "from_zone_id": 1,
    "to_zone_id": 1,
    "weight_from": "0.00"
}
... (more columns)
```

---

## Table: `logistics.tariffs_log`
- **Record Count:** 0
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| old_id | int | - | - |  |
| courier_service_id | int | - | - |  |
| collection_rateband_id | int | - | - |  |
| destination_rateband_id | int | - | - |  |
| collection_postcode_group_id | int | - | - |  |
| destination_postcode_group_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| tariff | decimal | - | - |  |
| add_unit_cost | decimal | - | - |  |
| unit_size | decimal | - | - |  |
| extra_tariff | decimal | - | - |  |
| extra_add_unit_cost | decimal | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| customer_id | varchar | - | - |  |
| formula | varchar | - | - |  |
| log_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.tariffs_pricing`
- **Record Count:** 0
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| tarif_pricing_type | enum | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| margin_type | enum | - | - |  |
| margin | decimal | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `logistics.tariffs_pricing_rules`
- **Record Count:** 38
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| name | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_id": 240393,
    "name": "UKMELL TARIFF CUSTOMER",
    "date_added": "2018-11-12 16:11:40",
    "added_by": 58
}
... (more columns)
```

---

## Table: `logistics.tariffs_pricing_rules_details`
- **Record Count:** 23
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_pricing_rule_id | int | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| margin | decimal | - | - |  |
| margin_type | enum | - | - |  |
| margin_weight_cost | varchar | - | - |  |
| margin_piece_cost | varchar | - | - |  |
| linehaul | decimal | - | - |  |
| linehaul_type | enum | - | - |  |
| tariff_pricing_type | enum | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_pricing_rule_id": 1,
    "to_zone_id": 0,
    "weight_from": "0.00",
    "weight_to": "0.00"
}
... (more columns)
```

---

## Table: `logistics.themes`
- **Record Count:** 6
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| slug | varchar | - | - |  |
| style_sheet | varchar | - | - |  |
| dashboard_template | varchar | - | - |  |
| is_active | bit | - | - |  |
| created_by | bigint | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Handlerbund",
    "slug": "hnd",
    "style_sheet": "",
    "dashboard_template": ""
}
... (more columns)
```

---

## Table: `logistics.tourline_routine`
- **Record Count:** 30023
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| agency_name | varchar | - | - |  |
| postal_code | varchar | - | - |  |
| agency_code | varchar | - | - |  |
| zone | varchar | - | - |  |
| province | varchar | - | - |  |
| route_code | varchar | - | - |  |
| km | varchar | - | - |  |
| town_name | varchar | - | - |  |
| kilometer | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 87012,
    "agency_name": "C.O. VITORIA N00                                    ",
    "postal_code": "01013",
    "agency_code": "000908",
    "zone": "PEN"
}
... (more columns)
```

---

## Table: `phpmyadmin.pma__bookmark`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| dbase | varchar | - | - |  |
| user | varchar | - | - |  |
| label | varchar | - | - |  |
| query | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__central_columns`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| db_name | varchar | - | - |  |
| col_name | varchar | - | - |  |
| col_type | varchar | - | - |  |
| col_length | text | - | - |  |
| col_collation | varchar | - | - |  |
| col_isNull | tinyint | - | - |  |
| col_extra | varchar | - | - |  |
| col_default | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__column_info`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| db_name | varchar | - | - |  |
| table_name | varchar | - | - |  |
| column_name | varchar | - | - |  |
| comment | varchar | - | - |  |
| mimetype | varchar | - | - |  |
| transformation | varchar | - | - |  |
| transformation_options | varchar | - | - |  |
| input_transformation | varchar | - | - |  |
| input_transformation_options | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__designer_settings`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| username | varchar | - | - |  |
| settings_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__export_templates`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| username | varchar | - | - |  |
| export_type | varchar | - | - |  |
| template_name | varchar | - | - |  |
| template_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__favorite`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| username | varchar | - | - |  |
| tables | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__history`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| username | varchar | - | - |  |
| db | varchar | - | - |  |
| table | varchar | - | - |  |
| timevalue | timestamp | - | - |  |
| sqlquery | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__navigationhiding`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| username | varchar | - | - |  |
| item_name | varchar | - | - |  |
| item_type | varchar | - | - |  |
| db_name | varchar | - | - |  |
| table_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__pdf_pages`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| db_name | varchar | - | - |  |
| page_nr | int | - | - |  |
| page_descr | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__recent`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| username | varchar | - | - |  |
| tables | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "username": "root",
    "tables": "[{\"db\":\"daakia\",\"table\":\"users\"},{\"db\":\"daakia\",\"table\":\"user_account_old\"},{\"db\":\"daakia\",\"table\":\"user_account_log\"},{\"db\":\"daakia\",\"table\":\"userhasgroups\"},{\"db\":\"daakia\",\"table\":\"user\"},{\"db\":\"daakia\",\"table\":\"vehicle\"},{\"db\":\"daakia\",\"table\":\"tourline_routine\"},{\"db\":\"smarttrack_staging\",\"table\":\"user\"},{\"db\":\"logistics\",\"table\":\"login_request\"},{\"db\":\"smart_school\",\"table\":\"class_sections\"}]"
}
... (more columns)
```

---

## Table: `phpmyadmin.pma__relation`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| master_db | varchar | - | - |  |
| master_table | varchar | - | - |  |
| master_field | varchar | - | - |  |
| foreign_db | varchar | - | - |  |
| foreign_table | varchar | - | - |  |
| foreign_field | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__savedsearches`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| username | varchar | - | - |  |
| db_name | varchar | - | - |  |
| search_name | varchar | - | - |  |
| search_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__table_coords`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| db_name | varchar | - | - |  |
| table_name | varchar | - | - |  |
| pdf_page_number | int | - | - |  |
| x | float | - | - |  |
| y | float | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__table_info`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| db_name | varchar | - | - |  |
| table_name | varchar | - | - |  |
| display_field | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__table_uiprefs`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| username | varchar | - | - |  |
| db_name | varchar | - | - |  |
| table_name | varchar | - | - |  |
| prefs | text | - | - |  |
| last_update | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "username": "root",
    "db_name": "smart_school",
    "table_name": "class_sections",
    "prefs": "{\"sorted_col\":\"`class_sections`.`id` ASC\"}",
    "last_update": "2023-05-07 18:57:23"
}
... (more columns)
```

---

## Table: `phpmyadmin.pma__tracking`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| db_name | varchar | - | - |  |
| table_name | varchar | - | - |  |
| version | int | - | - |  |
| date_created | datetime | - | - |  |
| date_updated | datetime | - | - |  |
| schema_snapshot | text | - | - |  |
| schema_sql | text | - | - |  |
| data_sql | longtext | - | - |  |
| tracking | set | - | - |  |
| tracking_active | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__userconfig`
- **Record Count:** 1
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| username | varchar | - | - |  |
| timevalue | timestamp | - | - |  |
| config_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "username": "root",
    "timevalue": "2025-12-09 00:13:07",
    "config_data": "{\"Console\\\/Mode\":\"collapse\"}"
}
... (more columns)
```

---

## Table: `phpmyadmin.pma__usergroups`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| usergroup | varchar | - | - |  |
| tab | varchar | - | - |  |
| allowed | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `phpmyadmin.pma__users`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| username | varchar | - | - |  |
| usergroup | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.alumni_events`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | text | - | - |  |
| event_for | varchar | - | - |  |
| session_id | int | - | - |  |
| class_id | varchar | - | - |  |
| section | varchar | - | - |  |
| from_date | date | - | - |  |
| to_date | date | - | - |  |
| note | text | - | - |  |
| photo | varchar | - | - |  |
| is_active | int | - | - |  |
| event_notification_message | text | - | - |  |
| show_onwebsite | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.alumni_students`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| current_email | varchar | - | - |  |
| current_phone | varchar | - | - |  |
| occupation | text | - | - |  |
| address | text | - | - |  |
| student_id | int | - | - |  |
| photo | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.attendence_type`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| key_value | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "Present",
    "key_value": "<b class=\"text text-success\">P<\/b>",
    "is_active": "yes",
    "created_at": "2016-06-23 18:11:37"
}
... (more columns)
```

---

## Table: `smart_school.book_issues`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| book_id | int | - | - |  |
| duereturn_date | date | - | - |  |
| return_date | date | - | - |  |
| issue_date | date | - | - |  |
| is_returned | int | - | - |  |
| member_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.books`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| book_title | varchar | - | - |  |
| book_no | varchar | - | - |  |
| isbn_no | varchar | - | - |  |
| subject | varchar | - | - |  |
| rack_no | varchar | - | - |  |
| publish | varchar | - | - |  |
| author | varchar | - | - |  |
| qty | int | - | - |  |
| perunitcost | float | - | - |  |
| postdate | date | - | - |  |
| description | text | - | - |  |
| available | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.captcha`
- **Record Count:** 5
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| status | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "userlogin",
    "status": 0,
    "created_at": "2021-01-19 08:10:29"
}
... (more columns)
```

---

## Table: `smart_school.categories`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| category | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.certificates`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| certificate_name | varchar | - | - |  |
| certificate_text | text | - | - |  |
| left_header | varchar | - | - |  |
| center_header | varchar | - | - |  |
| right_header | varchar | - | - |  |
| left_footer | varchar | - | - |  |
| right_footer | varchar | - | - |  |
| center_footer | varchar | - | - |  |
| background_image | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| created_for | tinyint | - | - |  |
| status | tinyint | - | - |  |
| header_height | int | - | - |  |
| content_height | int | - | - |  |
| footer_height | int | - | - |  |
| content_width | int | - | - |  |
| enable_student_image | tinyint | - | - |  |
| enable_image_height | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "certificate_name": "Sample Transfer Certificate",
    "certificate_text": "This is certify that <b>[name]<\/b> has born on [dob]  <br> and have following details [present_address] [guardian] [created_at] [admission_no] [roll_no] [class] [section] [gender] [admission_date] [category] [cast] [father_name] [mother_name] [religion] [email] [phone] .<br>We wish best of luck for future endeavors.",
    "left_header": "Reff. No.....1111111.........",
    "center_header": "To Whomever It May Concern"
}
... (more columns)
```

---

## Table: `smart_school.chat_connections`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| chat_user_one | int | - | - |  |
| chat_user_two | int | - | - |  |
| ip | varchar | - | - |  |
| time | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.chat_messages`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| message | text | - | - |  |
| chat_user_id | int | - | - |  |
| ip | varchar | - | - |  |
| time | int | - | - |  |
| is_first | int | - | - |  |
| is_read | int | - | - |  |
| chat_connection_id | int | - | - |  |
| created_at | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.chat_users`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_type | varchar | - | - |  |
| staff_id | int | - | - |  |
| student_id | int | - | - |  |
| create_staff_id | int | - | - |  |
| create_student_id | int | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.class_sections`
- **Record Count:** 13
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| class_id | int | - | - |  |
| section_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "class_id": 5,
    "section_id": 3,
    "is_active": "no",
    "created_at": "2023-02-10 13:39:25"
}
... (more columns)
```

---

## Table: `smart_school.class_teacher`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| class_id | int | - | - |  |
| staff_id | int | - | - |  |
| section_id | int | - | - |  |
| session_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "class_id": 5,
    "staff_id": 3,
    "section_id": 3,
    "session_id": 16
}
... (more columns)
```

---

## Table: `smart_school.classes`
- **Record Count:** 7
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| class | varchar | - | - |  |
| is_active | varchar | - | - |  |
| category | enum | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "class": "one",
    "is_active": "no",
    "category": "islamic",
    "created_at": "2023-02-10 13:39:25"
}
... (more columns)
```

---

## Table: `smart_school.complaint`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| complaint_type | varchar | - | - |  |
| source | varchar | - | - |  |
| name | varchar | - | - |  |
| contact | varchar | - | - |  |
| email | varchar | - | - |  |
| date | date | - | - |  |
| description | text | - | - |  |
| action_taken | varchar | - | - |  |
| assigned | varchar | - | - |  |
| note | text | - | - |  |
| image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.complaint_type`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| complaint_type | varchar | - | - |  |
| description | text | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.content_for`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| role | varchar | - | - |  |
| content_id | int | - | - |  |
| user_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.contents`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| type | varchar | - | - |  |
| is_public | varchar | - | - |  |
| class_id | int | - | - |  |
| cls_sec_id | int | - | - |  |
| file | varchar | - | - |  |
| created_by | int | - | - |  |
| note | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.custom_field_values`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| belong_table_id | int | - | - |  |
| custom_field_id | int | - | - |  |
| field_value | longtext | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.custom_fields`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| belong_to | varchar | - | - |  |
| type | varchar | - | - |  |
| bs_column | int | - | - |  |
| validation | int | - | - |  |
| field_values | text | - | - |  |
| show_table | varchar | - | - |  |
| visible_on_table | int | - | - |  |
| weight | int | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.department`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| department_name | varchar | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "department_name": "IT",
    "is_active": "yes"
}
... (more columns)
```

---

## Table: `smart_school.disable_reason`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reason | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.dispatch_receive`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reference_no | varchar | - | - |  |
| to_title | varchar | - | - |  |
| address | varchar | - | - |  |
| note | varchar | - | - |  |
| from_title | varchar | - | - |  |
| date | varchar | - | - |  |
| image | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.email_config`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| email_type | varchar | - | - |  |
| smtp_server | varchar | - | - |  |
| smtp_port | varchar | - | - |  |
| smtp_username | varchar | - | - |  |
| smtp_password | varchar | - | - |  |
| ssl_tls | varchar | - | - |  |
| smtp_auth | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "email_type": "sendmail",
    "smtp_server": null,
    "smtp_port": null,
    "smtp_username": null
}
... (more columns)
```

---

## Table: `smart_school.enquiry`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| contact | varchar | - | - |  |
| address | text | - | - |  |
| reference | varchar | - | - |  |
| date | date | - | - |  |
| description | varchar | - | - |  |
| follow_up_date | date | - | - |  |
| note | text | - | - |  |
| source | varchar | - | - |  |
| email | varchar | - | - |  |
| assigned | varchar | - | - |  |
| class | int | - | - |  |
| no_of_child | varchar | - | - |  |
| status | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.enquiry_type`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| enquiry_type | varchar | - | - |  |
| description | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.events`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| event_title | varchar | - | - |  |
| event_description | varchar | - | - |  |
| start_date | datetime | - | - |  |
| end_date | datetime | - | - |  |
| event_type | varchar | - | - |  |
| event_color | varchar | - | - |  |
| event_for | varchar | - | - |  |
| role_id | int | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_group_class_batch_exam_students`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam_group_class_batch_exam_id | int | - | - |  |
| student_id | int | - | - |  |
| student_session_id | int | - | - |  |
| roll_no | int | - | - |  |
| teacher_remark | text | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_group_class_batch_exam_subjects`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam_group_class_batch_exams_id | int | - | - |  |
| subject_id | int | - | - |  |
| date_from | date | - | - |  |
| time_from | time | - | - |  |
| duration | varchar | - | - |  |
| room_no | varchar | - | - |  |
| max_marks | float | - | - |  |
| min_marks | float | - | - |  |
| credit_hours | float | - | - |  |
| date_to | datetime | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_group_class_batch_exams`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam | varchar | - | - |  |
| session_id | int | - | - |  |
| date_from | date | - | - |  |
| date_to | date | - | - |  |
| description | text | - | - |  |
| exam_group_id | int | - | - |  |
| use_exam_roll_no | int | - | - |  |
| is_publish | int | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_group_exam_connections`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam_group_id | int | - | - |  |
| exam_group_class_batch_exams_id | int | - | - |  |
| exam_weightage | float | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_group_exam_results`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam_group_class_batch_exam_student_id | int | - | - |  |
| exam_group_class_batch_exam_subject_id | int | - | - |  |
| attendence | varchar | - | - |  |
| get_marks | float | - | - |  |
| note | text | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| exam_group_student_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_group_students`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam_group_id | int | - | - |  |
| student_id | int | - | - |  |
| student_session_id | int | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_groups`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| exam_type | varchar | - | - |  |
| description | text | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_results`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| attendence | varchar | - | - |  |
| exam_schedule_id | int | - | - |  |
| student_id | int | - | - |  |
| get_marks | float | - | - |  |
| note | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exam_schedules`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session_id | int | - | - |  |
| exam_id | int | - | - |  |
| teacher_subject_id | int | - | - |  |
| date_of_exam | date | - | - |  |
| start_to | varchar | - | - |  |
| end_from | varchar | - | - |  |
| room_no | varchar | - | - |  |
| full_marks | int | - | - |  |
| passing_marks | int | - | - |  |
| note | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.exams`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| sesion_id | int | - | - |  |
| note | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.expense_head`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exp_category | varchar | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| is_deleted | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.expenses`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exp_head_id | int | - | - |  |
| name | varchar | - | - |  |
| invoice_no | varchar | - | - |  |
| date | date | - | - |  |
| amount | float | - | - |  |
| documents | varchar | - | - |  |
| note | text | - | - |  |
| is_active | varchar | - | - |  |
| is_deleted | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.fee_groups`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| is_system | int | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "name": "Balance Master",
    "is_system": 1,
    "description": null,
    "is_active": "no"
}
... (more columns)
```

---

## Table: `smart_school.fee_groups_feetype`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| fee_session_group_id | int | - | - |  |
| fee_groups_id | int | - | - |  |
| feetype_id | int | - | - |  |
| session_id | int | - | - |  |
| amount | decimal | - | - |  |
| fine_type | varchar | - | - |  |
| due_date | date | - | - |  |
| fine_percentage | float | - | - |  |
| fine_amount | float | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| feegroup | varchar | - | - |  |
| feecode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4,
    "fee_session_group_id": 4,
    "fee_groups_id": null,
    "feetype_id": null,
    "session_id": 18
}
... (more columns)
```

---

## Table: `smart_school.fee_receipt_no`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| payment | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.fee_session_groups`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| fee_groups_id | int | - | - |  |
| session_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4,
    "fee_groups_id": null,
    "session_id": 18,
    "is_active": "no",
    "created_at": "2023-02-19 08:10:39"
}
... (more columns)
```

---

## Table: `smart_school.feecategory`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| category | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.feemasters`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session_id | int | - | - |  |
| feetype_id | int | - | - |  |
| class_id | int | - | - |  |
| amount | float | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.fees_discounts`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session_id | int | - | - |  |
| name | varchar | - | - |  |
| code | varchar | - | - |  |
| amount | decimal | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.fees_reminder`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reminder_type | varchar | - | - |  |
| day | int | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "reminder_type": "before",
    "day": 2,
    "is_active": 0,
    "created_at": "2020-02-28 13:38:32"
}
... (more columns)
```

---

## Table: `smart_school.feetype`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| is_system | int | - | - |  |
| feecategory_id | int | - | - |  |
| type | varchar | - | - |  |
| code | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| description | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "is_system": 1,
    "feecategory_id": null,
    "type": "Previous Session Balance",
    "code": "Previous Session Balance"
}
... (more columns)
```

---

## Table: `smart_school.filetypes`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_extension | text | - | - |  |
| file_mime | text | - | - |  |
| file_size | int | - | - |  |
| image_extension | text | - | - |  |
| image_mime | text | - | - |  |
| image_size | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_extension": "pdf, zip, jpg, jpeg, png, txt, 7z, gif, csv, docx, mp3, mp4, accdb, odt, ods, ppt, pptx, xlsx, wmv, jfif, apk, ppt, bmp, jpe, mdb, rar, xls, svg",
    "file_mime": "application\/pdf, image\/zip, image\/jpg, image\/png, image\/jpeg, text\/plain, application\/x-zip-compressed, application\/zip, image\/gif, text\/csv, application\/vnd.openxmlformats-officedocument.wordprocessingml.document, audio\/mpeg, application\/msaccess, application\/vnd.oasis.opendocument.text, application\/vnd.oasis.opendocument.spreadsheet, application\/vnd.ms-powerpoint, application\/vnd.openxmlformats-officedocument.presentationml.presentation, application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet, video\/x-ms-wmv, video\/mp4, image\/jpeg, application\/vnd.android.package-archive, application\/x-msdownload, application\/vnd.ms-powerpoint, image\/bmp, image\/jpeg, application\/msaccess, application\/vnd.ms-excel, image\/svg+xml",
    "file_size": 100048576,
    "image_extension": "jfif, png, jpe, jpeg, jpg, bmp, gif, svg"
}
... (more columns)
```

---

## Table: `smart_school.follow_up`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| enquiry_id | int | - | - |  |
| date | date | - | - |  |
| next_date | date | - | - |  |
| response | text | - | - |  |
| note | text | - | - |  |
| followup_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.front_cms_media_gallery`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| image | varchar | - | - |  |
| thumb_path | varchar | - | - |  |
| dir_path | varchar | - | - |  |
| img_name | varchar | - | - |  |
| thumb_name | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| file_type | varchar | - | - |  |
| file_size | varchar | - | - |  |
| vid_url | text | - | - |  |
| vid_title | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.front_cms_menu_items`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| menu_id | int | - | - |  |
| menu | varchar | - | - |  |
| page_id | int | - | - |  |
| parent_id | int | - | - |  |
| ext_url | text | - | - |  |
| open_new_tab | int | - | - |  |
| ext_url_link | text | - | - |  |
| slug | varchar | - | - |  |
| weight | int | - | - |  |
| publish | int | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "menu_id": 1,
    "menu": "Home",
    "page_id": 1,
    "parent_id": 0
}
... (more columns)
```

---

## Table: `smart_school.front_cms_menus`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| menu | varchar | - | - |  |
| slug | varchar | - | - |  |
| description | text | - | - |  |
| open_new_tab | int | - | - |  |
| ext_url | text | - | - |  |
| ext_url_link | text | - | - |  |
| publish | int | - | - |  |
| content_type | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "menu": "Main Menu",
    "slug": "main-menu",
    "description": "Main menu",
    "open_new_tab": 0
}
... (more columns)
```

---

## Table: `smart_school.front_cms_page_contents`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| page_id | int | - | - |  |
| content_type | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.front_cms_pages`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| page_type | varchar | - | - |  |
| is_homepage | int | - | - |  |
| title | varchar | - | - |  |
| url | varchar | - | - |  |
| type | varchar | - | - |  |
| slug | varchar | - | - |  |
| meta_title | text | - | - |  |
| meta_description | text | - | - |  |
| meta_keyword | text | - | - |  |
| feature_image | varchar | - | - |  |
| description | longtext | - | - |  |
| publish_date | date | - | - |  |
| publish | int | - | - |  |
| sidebar | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "page_type": "default",
    "is_homepage": 1,
    "title": "Home",
    "url": "page\/home"
}
... (more columns)
```

---

## Table: `smart_school.front_cms_program_photos`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| program_id | int | - | - |  |
| media_gallery_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.front_cms_programs`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| slug | varchar | - | - |  |
| url | text | - | - |  |
| title | varchar | - | - |  |
| date | date | - | - |  |
| event_start | date | - | - |  |
| event_end | date | - | - |  |
| event_venue | text | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| meta_title | text | - | - |  |
| meta_description | text | - | - |  |
| meta_keyword | text | - | - |  |
| feature_image | text | - | - |  |
| publish_date | date | - | - |  |
| publish | varchar | - | - |  |
| sidebar | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.front_cms_settings`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| theme | varchar | - | - |  |
| is_active_rtl | int | - | - |  |
| is_active_front_cms | int | - | - |  |
| is_active_sidebar | int | - | - |  |
| logo | varchar | - | - |  |
| contact_us_email | varchar | - | - |  |
| complain_form_email | varchar | - | - |  |
| sidebar_options | text | - | - |  |
| whatsapp_url | varchar | - | - |  |
| fb_url | varchar | - | - |  |
| twitter_url | varchar | - | - |  |
| youtube_url | varchar | - | - |  |
| google_plus | varchar | - | - |  |
| instagram_url | varchar | - | - |  |
| pinterest_url | varchar | - | - |  |
| linkedin_url | varchar | - | - |  |
| google_analytics | text | - | - |  |
| footer_text | varchar | - | - |  |
| cookie_consent | varchar | - | - |  |
| fav_icon | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "theme": "default",
    "is_active_rtl": null,
    "is_active_front_cms": null,
    "is_active_sidebar": null
}
... (more columns)
```

---

## Table: `smart_school.general_calls`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| contact | varchar | - | - |  |
| date | date | - | - |  |
| description | varchar | - | - |  |
| follow_up_date | date | - | - |  |
| call_dureation | varchar | - | - |  |
| note | text | - | - |  |
| call_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.grades`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam_type | varchar | - | - |  |
| name | varchar | - | - |  |
| point | float | - | - |  |
| mark_from | float | - | - |  |
| mark_upto | float | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.homework`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| class_id | int | - | - |  |
| section_id | int | - | - |  |
| session_id | int | - | - |  |
| homework_date | date | - | - |  |
| submit_date | date | - | - |  |
| staff_id | int | - | - |  |
| subject_group_subject_id | int | - | - |  |
| subject_id | int | - | - |  |
| description | text | - | - |  |
| create_date | date | - | - |  |
| evaluation_date | date | - | - |  |
| document | varchar | - | - |  |
| created_by | int | - | - |  |
| evaluated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.homework_evaluation`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| homework_id | int | - | - |  |
| student_id | int | - | - |  |
| student_session_id | int | - | - |  |
| date | date | - | - |  |
| status | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.hostel`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hostel_name | varchar | - | - |  |
| type | varchar | - | - |  |
| address | text | - | - |  |
| intake | int | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.hostel_rooms`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hostel_id | int | - | - |  |
| room_type_id | int | - | - |  |
| room_no | varchar | - | - |  |
| no_of_bed | int | - | - |  |
| cost_per_bed | float | - | - |  |
| title | varchar | - | - |  |
| description | text | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.id_card`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| school_name | varchar | - | - |  |
| school_address | varchar | - | - |  |
| background | varchar | - | - |  |
| logo | varchar | - | - |  |
| sign_image | varchar | - | - |  |
| enable_vertical_card | int | - | - |  |
| header_color | varchar | - | - |  |
| enable_admission_no | tinyint | - | - |  |
| enable_student_name | tinyint | - | - |  |
| enable_class | tinyint | - | - |  |
| enable_fathers_name | tinyint | - | - |  |
| enable_mothers_name | tinyint | - | - |  |
| enable_address | tinyint | - | - |  |
| enable_phone | tinyint | - | - |  |
| enable_dob | tinyint | - | - |  |
| enable_blood_group | tinyint | - | - |  |
| status | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Sample Student Identity Card Horizontal",
    "school_name": "Mount Carmel School",
    "school_address": "110 Kings Street, CA  Phone: 456542 Email: mount@gmail.com",
    "background": "samplebackground12.png"
}
... (more columns)
```

---

## Table: `smart_school.income`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| inc_head_id | varchar | - | - |  |
| name | varchar | - | - |  |
| invoice_no | varchar | - | - |  |
| date | date | - | - |  |
| amount | float | - | - |  |
| note | text | - | - |  |
| is_active | varchar | - | - |  |
| is_deleted | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| documents | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.income_head`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| income_category | varchar | - | - |  |
| description | varchar | - | - |  |
| is_active | varchar | - | - |  |
| is_deleted | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.item`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| item_category_id | int | - | - |  |
| name | varchar | - | - |  |
| unit | varchar | - | - |  |
| item_photo | varchar | - | - |  |
| description | text | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| item_store_id | int | - | - |  |
| item_supplier_id | int | - | - |  |
| quantity | int | - | - |  |
| date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.item_category`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| item_category | varchar | - | - |  |
| is_active | varchar | - | - |  |
| description | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.item_issue`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| issue_type | varchar | - | - |  |
| issue_to | varchar | - | - |  |
| issue_by | varchar | - | - |  |
| issue_date | date | - | - |  |
| return_date | date | - | - |  |
| item_category_id | int | - | - |  |
| item_id | int | - | - |  |
| quantity | int | - | - |  |
| note | text | - | - |  |
| is_returned | int | - | - |  |
| created_at | timestamp | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.item_stock`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| item_id | int | - | - |  |
| supplier_id | int | - | - |  |
| symbol | varchar | - | - |  |
| store_id | int | - | - |  |
| quantity | int | - | - |  |
| purchase_price | varchar | - | - |  |
| date | date | - | - |  |
| attachment | varchar | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.item_store`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| item_store | varchar | - | - |  |
| code | varchar | - | - |  |
| description | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.item_supplier`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| item_supplier | varchar | - | - |  |
| phone | varchar | - | - |  |
| email | varchar | - | - |  |
| address | varchar | - | - |  |
| contact_person_name | varchar | - | - |  |
| contact_person_phone | varchar | - | - |  |
| contact_person_email | varchar | - | - |  |
| description | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.languages`
- **Record Count:** 76
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| language | varchar | - | - |  |
| short_code | varchar | - | - |  |
| country_code | varchar | - | - |  |
| is_deleted | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "language": "Azerbaijan",
    "short_code": "az",
    "country_code": "az",
    "is_deleted": "no"
}
... (more columns)
```

---

## Table: `smart_school.leave_types`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.lesson`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session_id | int | - | - |  |
| subject_group_subject_id | int | - | - |  |
| subject_group_class_sections_id | int | - | - |  |
| name | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "session_id": 18,
    "subject_group_subject_id": 1,
    "subject_group_class_sections_id": 3,
    "name": "Lession 1"
}
... (more columns)
```

---

## Table: `smart_school.libarary_members`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| library_card_no | varchar | - | - |  |
| member_type | varchar | - | - |  |
| member_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.logs`
- **Record Count:** 160
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| message | text | - | - |  |
| record_id | text | - | - |  |
| user_id | int | - | - |  |
| action | varchar | - | - |  |
| ip_address | varchar | - | - |  |
| platform | varchar | - | - |  |
| agent | varchar | - | - |  |
| time | timestamp | - | - |  |
| created_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "message": "New Record inserted On sections id 0",
    "record_id": "1",
    "user_id": 1,
    "action": "Insert"
}
... (more columns)
```

---

## Table: `smart_school.messages`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| template_id | varchar | - | - |  |
| message | text | - | - |  |
| send_mail | varchar | - | - |  |
| send_sms | varchar | - | - |  |
| is_group | varchar | - | - |  |
| is_individual | varchar | - | - |  |
| is_class | int | - | - |  |
| group_list | text | - | - |  |
| user_list | text | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.migrations`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| version | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.multi_class_students`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_id | int | - | - |  |
| student_session_id | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.notification_roles`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| send_notification_id | int | - | - |  |
| role_id | int | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.notification_setting`
- **Record Count:** 12
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| is_mail | varchar | - | - |  |
| is_sms | varchar | - | - |  |
| is_notification | int | - | - |  |
| display_notification | int | - | - |  |
| display_sms | int | - | - |  |
| subject | varchar | - | - |  |
| template_id | varchar | - | - |  |
| template | longtext | - | - |  |
| variables | text | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "student_admission",
    "is_mail": "1",
    "is_sms": "0",
    "is_notification": 0
}
... (more columns)
```

---

## Table: `smart_school.online_admission_fields`
- **Record Count:** 40
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| status | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "middlename",
    "status": 0,
    "created_at": "2021-05-28 10:29:23"
}
... (more columns)
```

---

## Table: `smart_school.online_admission_payment`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| admission_id | int | - | - |  |
| paid_amount | float | - | - |  |
| payment_mode | varchar | - | - |  |
| payment_type | varchar | - | - |  |
| transaction_id | varchar | - | - |  |
| note | varchar | - | - |  |
| date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.online_admissions`
- **Record Count:** 13
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| admission_no | varchar | - | - |  |
| roll_no | varchar | - | - |  |
| reference_no | varchar | - | - |  |
| admission_date | date | - | - |  |
| firstname | varchar | - | - |  |
| middlename | varchar | - | - |  |
| lastname | varchar | - | - |  |
| rte | varchar | - | - |  |
| image | varchar | - | - |  |
| mobileno | varchar | - | - |  |
| email | varchar | - | - |  |
| state | varchar | - | - |  |
| city | varchar | - | - |  |
| pincode | varchar | - | - |  |
| religion | varchar | - | - |  |
| cast | varchar | - | - |  |
| dob | date | - | - |  |
| gender | varchar | - | - |  |
| current_address | text | - | - |  |
| permanent_address | text | - | - |  |
| category_id | int | - | - |  |
| class_section_id | int | - | - |  |
| route_id | int | - | - |  |
| school_house_id | int | - | - |  |
| blood_group | varchar | - | - |  |
| vehroute_id | int | - | - |  |
| hostel_room_id | int | - | - |  |
| adhar_no | varchar | - | - |  |
| samagra_id | varchar | - | - |  |
| bank_account_no | varchar | - | - |  |
| bank_name | varchar | - | - |  |
| ifsc_code | varchar | - | - |  |
| guardian_is | varchar | - | - |  |
| father_name | varchar | - | - |  |
| father_phone | varchar | - | - |  |
| father_occupation | varchar | - | - |  |
| mother_name | varchar | - | - |  |
| mother_phone | varchar | - | - |  |
| mother_occupation | varchar | - | - |  |
| guardian_name | varchar | - | - |  |
| guardian_relation | varchar | - | - |  |
| guardian_phone | varchar | - | - |  |
| guardian_occupation | varchar | - | - |  |
| guardian_address | text | - | - |  |
| guardian_email | varchar | - | - |  |
| father_pic | varchar | - | - |  |
| mother_pic | varchar | - | - |  |
| guardian_pic | varchar | - | - |  |
| is_enroll | int | - | - |  |
| previous_school | text | - | - |  |
| height | varchar | - | - |  |
| weight | varchar | - | - |  |
| note | varchar | - | - |  |
| form_status | int | - | - |  |
| paid_status | int | - | - |  |
| measurement_date | date | - | - |  |
| app_key | text | - | - |  |
| document | text | - | - |  |
| disable_at | date | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |
| age | varchar | - | - |  |
| previous_madrasa | varchar | - | - |  |
| date_of_leaving | varchar | - | - |  |
| reason_for_leaving | varchar | - | - |  |
| current_reading_surah | varchar | - | - |  |
| have_you_memorised_surah | varchar | - | - |  |
| siblings | text | - | - |  |
| home_tel | varchar | - | - |  |
| emergency_name | varchar | - | - |  |
| emergency_address | varchar | - | - |  |
| emergency_tel | varchar | - | - |  |
| emergency_mob | varchar | - | - |  |
| medical_problem | varchar | - | - |  |
| prob_details | varchar | - | - |  |
| gp | varchar | - | - |  |
| contact_number | varchar | - | - |  |
| student_name | varchar | - | - |  |
| signature | varchar | - | - |  |
| mother_email | varchar | - | - |  |
| guardian_relationship | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "admission_no": null,
    "roll_no": null,
    "reference_no": "",
    "admission_date": "2023-02-03"
}
... (more columns)
```

---

## Table: `smart_school.onlineexam`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| exam | text | - | - |  |
| attempt | int | - | - |  |
| exam_from | datetime | - | - |  |
| exam_to | datetime | - | - |  |
| is_quiz | int | - | - |  |
| auto_publish_date | datetime | - | - |  |
| time_from | time | - | - |  |
| time_to | time | - | - |  |
| duration | time | - | - |  |
| passing_percentage | float | - | - |  |
| description | text | - | - |  |
| session_id | int | - | - |  |
| publish_result | int | - | - |  |
| is_active | varchar | - | - |  |
| is_marks_display | int | - | - |  |
| is_neg_marking | int | - | - |  |
| is_random_question | int | - | - |  |
| is_rank_generated | int | - | - |  |
| publish_exam_notification | int | - | - |  |
| publish_result_notification | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.onlineexam_attempts`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| onlineexam_student_id | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.onlineexam_questions`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| question_id | int | - | - |  |
| onlineexam_id | int | - | - |  |
| session_id | int | - | - |  |
| marks | float | - | - |  |
| neg_marks | float | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.onlineexam_student_results`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| onlineexam_student_id | int | - | - |  |
| onlineexam_question_id | int | - | - |  |
| select_option | longtext | - | - |  |
| marks | float | - | - |  |
| remark | text | - | - |  |
| attachment_name | text | - | - |  |
| attachment_upload_name | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.onlineexam_students`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| onlineexam_id | int | - | - |  |
| student_session_id | int | - | - |  |
| is_attempted | int | - | - |  |
| rank | int | - | - |  |
| quiz_attempted | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.payment_settings`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| payment_type | varchar | - | - |  |
| api_username | varchar | - | - |  |
| api_secret_key | varchar | - | - |  |
| salt | varchar | - | - |  |
| api_publishable_key | varchar | - | - |  |
| api_password | varchar | - | - |  |
| api_signature | varchar | - | - |  |
| api_email | varchar | - | - |  |
| paypal_demo | varchar | - | - |  |
| account_no | varchar | - | - |  |
| is_active | varchar | - | - |  |
| gateway_mode | int | - | - |  |
| paytm_website | varchar | - | - |  |
| paytm_industrytype | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "payment_type": "jazzcash",
    "api_username": null,
    "api_secret_key": "tedttdt",
    "salt": ""
}
... (more columns)
```

---

## Table: `smart_school.payslip_allowance`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| payslip_id | int | - | - |  |
| allowance_type | varchar | - | - |  |
| amount | float | - | - |  |
| staff_id | int | - | - |  |
| cal_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.permission_category`
- **Record Count:** 201
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| perm_group_id | int | - | - |  |
| name | varchar | - | - |  |
| short_code | varchar | - | - |  |
| enable_view | int | - | - |  |
| enable_add | int | - | - |  |
| enable_edit | int | - | - |  |
| enable_delete | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "perm_group_id": 1,
    "name": "Student",
    "short_code": "student",
    "enable_view": 1
}
... (more columns)
```

---

## Table: `smart_school.permission_group`
- **Record Count:** 28
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| short_code | varchar | - | - |  |
| is_active | int | - | - |  |
| system | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Student Information",
    "short_code": "student_information",
    "is_active": 1,
    "system": 1
}
... (more columns)
```

---

## Table: `smart_school.permission_student`
- **Record Count:** 18
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| short_code | varchar | - | - |  |
| system | int | - | - |  |
| student | int | - | - |  |
| parent | int | - | - |  |
| group_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Fees",
    "short_code": "fees",
    "system": 0,
    "student": 1
}
... (more columns)
```

---

## Table: `smart_school.print_headerfooter`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| print_type | varchar | - | - |  |
| header_image | varchar | - | - |  |
| footer_content | text | - | - |  |
| created_by | int | - | - |  |
| entry_date | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "print_type": "staff_payslip",
    "header_image": "header_image.jpg",
    "footer_content": "This payslip is computer generated hence no signature is required.",
    "created_by": 1
}
... (more columns)
```

---

## Table: `smart_school.question_answers`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| question_id | int | - | - |  |
| option_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.question_options`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| question_id | int | - | - |  |
| option | text | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.questions`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| staff_id | int | - | - |  |
| subject_id | int | - | - |  |
| question_type | varchar | - | - |  |
| level | varchar | - | - |  |
| class_id | int | - | - |  |
| section_id | int | - | - |  |
| class_section_id | int | - | - |  |
| question | text | - | - |  |
| opt_a | text | - | - |  |
| opt_b | text | - | - |  |
| opt_c | text | - | - |  |
| opt_d | text | - | - |  |
| opt_e | text | - | - |  |
| correct | text | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.read_notification`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_id | int | - | - |  |
| parent_id | int | - | - |  |
| staff_id | int | - | - |  |
| notification_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.reference`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reference | varchar | - | - |  |
| description | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.roles`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| slug | varchar | - | - |  |
| is_active | int | - | - |  |
| is_system | int | - | - |  |
| is_superadmin | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Admin",
    "slug": null,
    "is_active": 0,
    "is_system": 1
}
... (more columns)
```

---

## Table: `smart_school.roles_permissions`
- **Record Count:** 579
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| role_id | int | - | - |  |
| perm_cat_id | int | - | - |  |
| can_view | int | - | - |  |
| can_add | int | - | - |  |
| can_edit | int | - | - |  |
| can_delete | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 10,
    "role_id": 1,
    "perm_cat_id": 17,
    "can_view": 1,
    "can_add": 1
}
... (more columns)
```

---

## Table: `smart_school.room_types`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| room_type | varchar | - | - |  |
| description | text | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.sch_settings`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| biometric | int | - | - |  |
| biometric_device | text | - | - |  |
| email | varchar | - | - |  |
| phone | varchar | - | - |  |
| address | text | - | - |  |
| lang_id | int | - | - |  |
| languages | varchar | - | - |  |
| dise_code | varchar | - | - |  |
| date_format | varchar | - | - |  |
| time_format | varchar | - | - |  |
| currency | varchar | - | - |  |
| currency_symbol | varchar | - | - |  |
| is_rtl | varchar | - | - |  |
| is_duplicate_fees_invoice | int | - | - |  |
| timezone | varchar | - | - |  |
| session_id | int | - | - |  |
| cron_secret_key | varchar | - | - |  |
| currency_place | varchar | - | - |  |
| class_teacher | varchar | - | - |  |
| start_month | varchar | - | - |  |
| attendence_type | int | - | - |  |
| image | varchar | - | - |  |
| admin_logo | varchar | - | - |  |
| admin_small_logo | varchar | - | - |  |
| theme | varchar | - | - |  |
| fee_due_days | int | - | - |  |
| adm_auto_insert | int | - | - |  |
| adm_prefix | varchar | - | - |  |
| adm_start_from | varchar | - | - |  |
| adm_no_digit | int | - | - |  |
| adm_update_status | int | - | - |  |
| staffid_auto_insert | int | - | - |  |
| staffid_prefix | varchar | - | - |  |
| staffid_start_from | varchar | - | - |  |
| staffid_no_digit | int | - | - |  |
| staffid_update_status | int | - | - |  |
| is_active | varchar | - | - |  |
| online_admission | int | - | - |  |
| online_admission_payment | varchar | - | - |  |
| online_admission_amount | float | - | - |  |
| online_admission_instruction | text | - | - |  |
| online_admission_conditions | text | - | - |  |
| is_blood_group | int | - | - |  |
| is_student_house | int | - | - |  |
| roll_no | int | - | - |  |
| category | int | - | - |  |
| religion | int | - | - |  |
| cast | int | - | - |  |
| mobile_no | int | - | - |  |
| student_email | int | - | - |  |
| admission_date | int | - | - |  |
| lastname | int | - | - |  |
| middlename | int | - | - |  |
| student_photo | int | - | - |  |
| student_height | int | - | - |  |
| student_weight | int | - | - |  |
| measurement_date | int | - | - |  |
| father_name | int | - | - |  |
| father_phone | int | - | - |  |
| father_occupation | int | - | - |  |
| father_pic | int | - | - |  |
| mother_name | int | - | - |  |
| mother_phone | int | - | - |  |
| mother_occupation | int | - | - |  |
| mother_pic | int | - | - |  |
| guardian_name | int | - | - |  |
| guardian_relation | int | - | - |  |
| guardian_phone | int | - | - |  |
| guardian_email | int | - | - |  |
| guardian_pic | int | - | - |  |
| guardian_occupation | int | - | - |  |
| guardian_address | int | - | - |  |
| current_address | int | - | - |  |
| permanent_address | int | - | - |  |
| route_list | int | - | - |  |
| hostel_id | int | - | - |  |
| bank_account_no | int | - | - |  |
| ifsc_code | int | - | - |  |
| bank_name | int | - | - |  |
| national_identification_no | int | - | - |  |
| local_identification_no | int | - | - |  |
| rte | int | - | - |  |
| previous_school_details | int | - | - |  |
| student_note | int | - | - |  |
| upload_documents | int | - | - |  |
| staff_designation | int | - | - |  |
| staff_department | int | - | - |  |
| staff_last_name | int | - | - |  |
| staff_father_name | int | - | - |  |
| staff_mother_name | int | - | - |  |
| staff_date_of_joining | int | - | - |  |
| staff_phone | int | - | - |  |
| staff_emergency_contact | int | - | - |  |
| staff_marital_status | int | - | - |  |
| staff_photo | int | - | - |  |
| staff_current_address | int | - | - |  |
| staff_permanent_address | int | - | - |  |
| staff_qualification | int | - | - |  |
| staff_work_experience | int | - | - |  |
| staff_note | int | - | - |  |
| staff_epf_no | int | - | - |  |
| staff_basic_salary | int | - | - |  |
| staff_contract_type | int | - | - |  |
| staff_work_shift | int | - | - |  |
| staff_work_location | int | - | - |  |
| staff_leaves | int | - | - |  |
| staff_account_details | int | - | - |  |
| staff_social_media | int | - | - |  |
| staff_upload_documents | int | - | - |  |
| mobile_api_url | tinytext | - | - |  |
| app_primary_color_code | varchar | - | - |  |
| app_secondary_color_code | varchar | - | - |  |
| app_logo | varchar | - | - |  |
| student_profile_edit | int | - | - |  |
| start_week | varchar | - | - |  |
| my_question | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Your School Name",
    "biometric": 0,
    "biometric_device": "",
    "email": "yourschoolemail@domain.com"
}
... (more columns)
```

---

## Table: `smart_school.school_houses`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| house_name | varchar | - | - |  |
| description | varchar | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.sections`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| section | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4,
    "section": "one",
    "is_active": "no",
    "created_at": "2023-05-07 14:34:52",
    "updated_at": null
}
... (more columns)
```

---

## Table: `smart_school.send_notification`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| publish_date | date | - | - |  |
| date | date | - | - |  |
| message | text | - | - |  |
| visible_student | varchar | - | - |  |
| visible_staff | varchar | - | - |  |
| visible_parent | varchar | - | - |  |
| created_by | varchar | - | - |  |
| created_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.sessions`
- **Record Count:** 14
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 7,
    "session": "2016-17",
    "is_active": "no",
    "created_at": "2017-04-20 06:42:19",
    "updated_at": "0000-00-00"
}
... (more columns)
```

---

## Table: `smart_school.sms_config`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| name | varchar | - | - |  |
| api_id | varchar | - | - |  |
| authkey | varchar | - | - |  |
| senderid | varchar | - | - |  |
| contact | text | - | - |  |
| username | varchar | - | - |  |
| url | varchar | - | - |  |
| password | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.source`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| source | varchar | - | - |  |
| description | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.staff`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| employee_id | varchar | - | - |  |
| lang_id | int | - | - |  |
| department | int | - | - |  |
| designation | int | - | - |  |
| qualification | varchar | - | - |  |
| work_exp | varchar | - | - |  |
| name | varchar | - | - |  |
| surname | varchar | - | - |  |
| father_name | varchar | - | - |  |
| mother_name | varchar | - | - |  |
| contact_no | varchar | - | - |  |
| emergency_contact_no | varchar | - | - |  |
| email | varchar | - | - |  |
| dob | date | - | - |  |
| marital_status | varchar | - | - |  |
| date_of_joining | date | - | - |  |
| date_of_leaving | date | - | - |  |
| local_address | varchar | - | - |  |
| permanent_address | varchar | - | - |  |
| note | varchar | - | - |  |
| image | varchar | - | - |  |
| password | varchar | - | - |  |
| gender | varchar | - | - |  |
| account_title | varchar | - | - |  |
| bank_account_no | varchar | - | - |  |
| bank_name | varchar | - | - |  |
| ifsc_code | varchar | - | - |  |
| bank_branch | varchar | - | - |  |
| payscale | varchar | - | - |  |
| basic_salary | varchar | - | - |  |
| epf_no | varchar | - | - |  |
| contract_type | varchar | - | - |  |
| shift | varchar | - | - |  |
| location | varchar | - | - |  |
| facebook | varchar | - | - |  |
| twitter | varchar | - | - |  |
| linkedin | varchar | - | - |  |
| instagram | varchar | - | - |  |
| resume | varchar | - | - |  |
| joining_letter | varchar | - | - |  |
| resignation_letter | varchar | - | - |  |
| other_document_name | varchar | - | - |  |
| other_document_file | varchar | - | - |  |
| user_id | int | - | - |  |
| is_active | int | - | - |  |
| verification_code | varchar | - | - |  |
| disable_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "employee_id": "9000",
    "lang_id": 0,
    "department": 0,
    "designation": 0
}
... (more columns)
```

---

## Table: `smart_school.staff_attendance`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| date | date | - | - |  |
| staff_id | int | - | - |  |
| staff_attendance_type_id | int | - | - |  |
| remark | varchar | - | - |  |
| is_active | int | - | - |  |
| created_at | datetime | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.staff_attendance_type`
- **Record Count:** 5
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| key_value | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "Present",
    "key_value": "<b class=\"text text-success\">P<\/b>",
    "is_active": "yes",
    "created_at": "0000-00-00 00:00:00"
}
... (more columns)
```

---

## Table: `smart_school.staff_designation`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| designation | varchar | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "designation": "Teacher",
    "is_active": "yes"
}
... (more columns)
```

---

## Table: `smart_school.staff_id_card`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| school_name | varchar | - | - |  |
| school_address | varchar | - | - |  |
| background | varchar | - | - |  |
| logo | varchar | - | - |  |
| sign_image | varchar | - | - |  |
| header_color | varchar | - | - |  |
| enable_vertical_card | int | - | - |  |
| enable_staff_role | tinyint | - | - |  |
| enable_staff_id | tinyint | - | - |  |
| enable_staff_department | tinyint | - | - |  |
| enable_designation | tinyint | - | - |  |
| enable_name | tinyint | - | - |  |
| enable_fathers_name | tinyint | - | - |  |
| enable_mothers_name | tinyint | - | - |  |
| enable_date_of_joining | tinyint | - | - |  |
| enable_permanent_address | tinyint | - | - |  |
| enable_staff_dob | tinyint | - | - |  |
| enable_staff_phone | tinyint | - | - |  |
| status | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Sample Staff ID Card Horizontal",
    "school_name": "Mount Carmel School",
    "school_address": "110 Kings Street, CA",
    "background": "background1.png"
}
... (more columns)
```

---

## Table: `smart_school.staff_leave_details`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| staff_id | int | - | - |  |
| leave_type_id | int | - | - |  |
| alloted_leave | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.staff_leave_request`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| staff_id | int | - | - |  |
| leave_type_id | int | - | - |  |
| leave_from | date | - | - |  |
| leave_to | date | - | - |  |
| leave_days | int | - | - |  |
| employee_remark | varchar | - | - |  |
| admin_remark | varchar | - | - |  |
| status | varchar | - | - |  |
| applied_by | varchar | - | - |  |
| document_file | varchar | - | - |  |
| date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.staff_payroll`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| basic_salary | int | - | - |  |
| pay_scale | varchar | - | - |  |
| grade | varchar | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.staff_payslip`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| staff_id | int | - | - |  |
| basic | float | - | - |  |
| total_allowance | float | - | - |  |
| total_deduction | float | - | - |  |
| leave_deduction | int | - | - |  |
| tax | varchar | - | - |  |
| net_salary | float | - | - |  |
| status | varchar | - | - |  |
| month | varchar | - | - |  |
| year | varchar | - | - |  |
| payment_mode | varchar | - | - |  |
| payment_date | date | - | - |  |
| remark | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.staff_rating`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| staff_id | int | - | - |  |
| comment | text | - | - |  |
| rate | int | - | - |  |
| user_id | int | - | - |  |
| role | varchar | - | - |  |
| status | int | - | - |  |
| entrydt | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.staff_roles`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| role_id | int | - | - |  |
| staff_id | int | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "role_id": 7,
    "staff_id": 1,
    "is_active": 1,
    "created_at": "2023-02-08 13:58:26"
}
... (more columns)
```

---

## Table: `smart_school.staff_timeline`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| staff_id | int | - | - |  |
| title | varchar | - | - |  |
| timeline_date | date | - | - |  |
| description | varchar | - | - |  |
| document | varchar | - | - |  |
| status | varchar | - | - |  |
| date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_applyleave`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_session_id | int | - | - |  |
| from_date | date | - | - |  |
| to_date | date | - | - |  |
| apply_date | date | - | - |  |
| status | int | - | - |  |
| created_at | timestamp | - | - |  |
| docs | text | - | - |  |
| reason | text | - | - |  |
| approve_by | int | - | - |  |
| request_type | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_attendences`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_session_id | int | - | - |  |
| biometric_attendence | int | - | - |  |
| date | date | - | - |  |
| attendence_type_id | int | - | - |  |
| remark | varchar | - | - |  |
| biometric_device_data | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_doc`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_id | int | - | - |  |
| title | varchar | - | - |  |
| doc | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_edit_fields`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| status | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_fees`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_session_id | int | - | - |  |
| feemaster_id | int | - | - |  |
| amount | float | - | - |  |
| amount_discount | float | - | - |  |
| amount_fine | float | - | - |  |
| description | text | - | - |  |
| date | date | - | - |  |
| payment_mode | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_fees_deposite`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_fees_master_id | int | - | - |  |
| fee_groups_feetype_id | int | - | - |  |
| amount_detail | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "student_fees_master_id": 2,
    "fee_groups_feetype_id": 2,
    "amount_detail": "{\"1\":{\"amount\":\"10\",\"date\":\"2023-02-19\",\"amount_discount\":\"0\",\"amount_fine\":\"0\",\"description\":\"\",\"collected_by\":\"super admin(9000)\",\"payment_mode\":\"Cash\",\"received_by\":\"1\",\"inv_no\":1}}",
    "is_active": "no"
}
... (more columns)
```

---

## Table: `smart_school.student_fees_discounts`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_session_id | int | - | - |  |
| fees_discount_id | int | - | - |  |
| status | varchar | - | - |  |
| payment_id | varchar | - | - |  |
| description | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_fees_master`
- **Record Count:** 12
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| is_system | int | - | - |  |
| student_session_id | int | - | - |  |
| fee_session_group_id | int | - | - |  |
| amount | float | - | - |  |
| is_active | varchar | - | - |  |
| fee_groups_feetype_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "is_system": 0,
    "student_session_id": 6,
    "fee_session_group_id": 4,
    "amount": 0
}
... (more columns)
```

---

## Table: `smart_school.student_session`
- **Record Count:** 30
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session_id | int | - | - |  |
| student_id | int | - | - |  |
| class_id | int | - | - |  |
| section_id | int | - | - |  |
| route_id | int | - | - |  |
| hostel_room_id | int | - | - |  |
| vehroute_id | int | - | - |  |
| transport_fees | float | - | - |  |
| fees_discount | float | - | - |  |
| is_active | varchar | - | - |  |
| is_alumni | int | - | - |  |
| default_login | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "session_id": 18,
    "student_id": 1,
    "class_id": 5,
    "section_id": 3
}
... (more columns)
```

---

## Table: `smart_school.student_sibling`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_id | int | - | - |  |
| sibling_student_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_subject_attendances`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_session_id | int | - | - |  |
| subject_timetable_id | int | - | - |  |
| attendence_type_id | int | - | - |  |
| date | date | - | - |  |
| remark | text | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.student_timeline`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| student_id | int | - | - |  |
| title | varchar | - | - |  |
| timeline_date | date | - | - |  |
| description | varchar | - | - |  |
| document | varchar | - | - |  |
| status | varchar | - | - |  |
| date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.students`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| parent_id | int | - | - |  |
| admission_no | varchar | - | - |  |
| roll_no | varchar | - | - |  |
| admission_date | date | - | - |  |
| firstname | varchar | - | - |  |
| middlename | varchar | - | - |  |
| lastname | varchar | - | - |  |
| rte | varchar | - | - |  |
| image | varchar | - | - |  |
| mobileno | varchar | - | - |  |
| email | varchar | - | - |  |
| state | varchar | - | - |  |
| city | varchar | - | - |  |
| pincode | varchar | - | - |  |
| religion | varchar | - | - |  |
| cast | varchar | - | - |  |
| dob | date | - | - |  |
| gender | varchar | - | - |  |
| current_address | text | - | - |  |
| permanent_address | text | - | - |  |
| category_id | varchar | - | - |  |
| route_id | int | - | - |  |
| school_house_id | int | - | - |  |
| blood_group | varchar | - | - |  |
| vehroute_id | int | - | - |  |
| hostel_room_id | int | - | - |  |
| adhar_no | varchar | - | - |  |
| samagra_id | varchar | - | - |  |
| bank_account_no | varchar | - | - |  |
| bank_name | varchar | - | - |  |
| ifsc_code | varchar | - | - |  |
| guardian_is | varchar | - | - |  |
| father_name | varchar | - | - |  |
| father_phone | varchar | - | - |  |
| father_occupation | varchar | - | - |  |
| mother_name | varchar | - | - |  |
| mother_phone | varchar | - | - |  |
| mother_occupation | varchar | - | - |  |
| guardian_name | varchar | - | - |  |
| guardian_relation | varchar | - | - |  |
| guardian_phone | varchar | - | - |  |
| guardian_occupation | varchar | - | - |  |
| guardian_address | text | - | - |  |
| guardian_email | varchar | - | - |  |
| father_pic | varchar | - | - |  |
| mother_pic | varchar | - | - |  |
| guardian_pic | varchar | - | - |  |
| is_active | varchar | - | - |  |
| previous_school | text | - | - |  |
| height | varchar | - | - |  |
| weight | varchar | - | - |  |
| measurement_date | date | - | - |  |
| dis_reason | int | - | - |  |
| note | varchar | - | - |  |
| dis_note | text | - | - |  |
| app_key | text | - | - |  |
| parent_app_key | text | - | - |  |
| disable_at | date | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "parent_id": 2,
    "admission_no": "687687",
    "roll_no": "1",
    "admission_date": "2023-02-16"
}
... (more columns)
```

---

## Table: `smart_school.subject_group_class_sections`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| subject_group_id | int | - | - |  |
| class_section_id | int | - | - |  |
| session_id | int | - | - |  |
| description | text | - | - |  |
| is_active | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "subject_group_id": 2,
    "class_section_id": 5,
    "session_id": 16,
    "description": null
}
... (more columns)
```

---

## Table: `smart_school.subject_group_subjects`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| subject_group_id | int | - | - |  |
| session_id | int | - | - |  |
| subject_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "subject_group_id": 3,
    "session_id": 18,
    "subject_id": 1,
    "created_at": "2023-02-10 13:54:16"
}
... (more columns)
```

---

## Table: `smart_school.subject_groups`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| description | text | - | - |  |
| session_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "name": "one",
    "description": "Description",
    "session_id": 16,
    "created_at": "2023-02-10 13:39:53"
}
... (more columns)
```

---

## Table: `smart_school.subject_syllabus`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| topic_id | int | - | - |  |
| session_id | int | - | - |  |
| created_by | int | - | - |  |
| created_for | int | - | - |  |
| date | date | - | - |  |
| time_from | varchar | - | - |  |
| time_to | varchar | - | - |  |
| presentation | text | - | - |  |
| attachment | text | - | - |  |
| lacture_youtube_url | varchar | - | - |  |
| lacture_video | varchar | - | - |  |
| sub_topic | text | - | - |  |
| teaching_method | text | - | - |  |
| general_objectives | text | - | - |  |
| previous_knowledge | text | - | - |  |
| comprehensive_questions | text | - | - |  |
| status | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.subject_timetable`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| day | varchar | - | - |  |
| class_id | int | - | - |  |
| section_id | int | - | - |  |
| subject_group_id | int | - | - |  |
| subject_group_subject_id | int | - | - |  |
| staff_id | int | - | - |  |
| time_from | varchar | - | - |  |
| time_to | varchar | - | - |  |
| start_time | time | - | - |  |
| end_time | time | - | - |  |
| room_no | varchar | - | - |  |
| session_id | int | - | - |  |
| lesson_id | int | - | - |  |
| date | date | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "day": "Monday",
    "class_id": 5,
    "section_id": 3,
    "subject_group_id": 3
}
... (more columns)
```

---

## Table: `smart_school.subjects`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| code | varchar | - | - |  |
| type | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Urdu level 1",
    "code": "level 1",
    "type": "theory",
    "is_active": "yes"
}
... (more columns)
```

---

## Table: `smart_school.submit_assignment`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| homework_id | int | - | - |  |
| student_id | int | - | - |  |
| message | text | - | - |  |
| docs | varchar | - | - |  |
| file_name | text | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.teacher_subjects`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session_id | int | - | - |  |
| class_section_id | int | - | - |  |
| subject_id | int | - | - |  |
| teacher_id | int | - | - |  |
| description | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.template_admitcards`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| template | varchar | - | - |  |
| heading | text | - | - |  |
| title | text | - | - |  |
| left_logo | varchar | - | - |  |
| right_logo | varchar | - | - |  |
| exam_name | varchar | - | - |  |
| school_name | varchar | - | - |  |
| exam_center | varchar | - | - |  |
| sign | varchar | - | - |  |
| background_img | varchar | - | - |  |
| is_name | int | - | - |  |
| is_father_name | int | - | - |  |
| is_mother_name | int | - | - |  |
| is_dob | int | - | - |  |
| is_admission_no | int | - | - |  |
| is_roll_no | int | - | - |  |
| is_address | int | - | - |  |
| is_gender | int | - | - |  |
| is_photo | int | - | - |  |
| is_class | int | - | - |  |
| is_section | int | - | - |  |
| content_footer | text | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "template": "Sample Admit Card",
    "heading": "BOARD OF SECONDARY EDUCATION, MADHYA PRADESH, BHOPAL",
    "title": "HIGHER SECONDARY SCHOOL CERTIFICATE EXAMINATION (10+2) 2014",
    "left_logo": "ab12c4b65f53ee621dcf84370a7c5be4.png"
}
... (more columns)
```

---

## Table: `smart_school.template_marksheets`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| template | varchar | - | - |  |
| heading | text | - | - |  |
| title | text | - | - |  |
| left_logo | varchar | - | - |  |
| right_logo | varchar | - | - |  |
| exam_name | varchar | - | - |  |
| school_name | varchar | - | - |  |
| exam_center | varchar | - | - |  |
| left_sign | varchar | - | - |  |
| middle_sign | varchar | - | - |  |
| right_sign | varchar | - | - |  |
| exam_session | int | - | - |  |
| is_name | int | - | - |  |
| is_father_name | int | - | - |  |
| is_mother_name | int | - | - |  |
| is_dob | int | - | - |  |
| is_admission_no | int | - | - |  |
| is_roll_no | int | - | - |  |
| is_photo | int | - | - |  |
| is_division | int | - | - |  |
| is_customfield | int | - | - |  |
| background_img | varchar | - | - |  |
| date | varchar | - | - |  |
| is_class | int | - | - |  |
| is_teacher_remark | int | - | - |  |
| is_section | int | - | - |  |
| content | text | - | - |  |
| content_footer | text | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "template": "Sample Marksheet",
    "heading": "BOARD OF SECONDARY EDUCATION, MADHYA PRADESH, BHOPAL",
    "title": "BOARD OF SECONDARY EDUCATION, MADHYA PRADESH, BHOPAL",
    "left_logo": "f314cec3f688771ccaeddbcee6e52f7c.png"
}
... (more columns)
```

---

## Table: `smart_school.timetables`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| teacher_subject_id | int | - | - |  |
| day_name | varchar | - | - |  |
| start_time | varchar | - | - |  |
| end_time | varchar | - | - |  |
| room_no | varchar | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.topic`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| session_id | int | - | - |  |
| lesson_id | int | - | - |  |
| name | varchar | - | - |  |
| status | int | - | - |  |
| complete_date | date | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 0,
    "session_id": 18,
    "lesson_id": 1,
    "name": "Topic1",
    "status": 1
}
... (more columns)
```

---

## Table: `smart_school.transport_route`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| route_title | varchar | - | - |  |
| no_of_vehicle | int | - | - |  |
| fare | float | - | - |  |
| note | text | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.userlog`
- **Record Count:** 34
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user | varchar | - | - |  |
| role | varchar | - | - |  |
| class_section_id | int | - | - |  |
| ipaddress | varchar | - | - |  |
| user_agent | varchar | - | - |  |
| login_datetime | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "user": "admin",
    "role": "Super Admin",
    "class_section_id": null,
    "ipaddress": "::1"
}
... (more columns)
```

---

## Table: `smart_school.users`
- **Record Count:** 22
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_id | int | - | - |  |
| username | varchar | - | - |  |
| password | varchar | - | - |  |
| childs | text | - | - |  |
| role | varchar | - | - |  |
| verification_code | varchar | - | - |  |
| lang_id | int | - | - |  |
| is_active | varchar | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "user_id": 1,
    "username": "std1",
    "password": "wpajpo",
    "childs": ""
}
... (more columns)
```

---

## Table: `smart_school.users_authentication`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| users_id | int | - | - |  |
| token | varchar | - | - |  |
| expired_at | timestamp | - | - |  |
| created_at | date | - | - |  |
| updated_at | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.vehicle_routes`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| route_id | int | - | - |  |
| vehicle_id | int | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.vehicles`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| vehicle_no | varchar | - | - |  |
| vehicle_model | varchar | - | - |  |
| manufacture_year | varchar | - | - |  |
| driver_name | varchar | - | - |  |
| driver_licence | varchar | - | - |  |
| driver_contact | varchar | - | - |  |
| note | text | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.visitors_book`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| source | varchar | - | - |  |
| purpose | varchar | - | - |  |
| name | varchar | - | - |  |
| email | varchar | - | - |  |
| contact | varchar | - | - |  |
| id_proof | varchar | - | - |  |
| no_of_pepple | int | - | - |  |
| date | date | - | - |  |
| in_time | varchar | - | - |  |
| out_time | varchar | - | - |  |
| note | text | - | - |  |
| image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smart_school.visitors_purpose`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| visitors_purpose | varchar | - | - |  |
| description | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.address`
- **Record Count:** 306
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| phone_number | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| email | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country | varchar | - | - |  |
| postcode | varchar | - | - |  |
| user_id | int | - | - |  |
| state | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 329,
    "phone_number": "7919115608",
    "company": "Dominic Jackson",
    "contact": "REGINA BERGELT",
    "email": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.agent_data`
- **Record Count:** 55
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_code | varchar | - | - |  |
| agent_name | varchar | - | - |  |
| active | tinyint | - | - |  |
| contact_name | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| country_id | int | - | - |  |
| county | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| telephone | varchar | - | - |  |
| mobile | varchar | - | - |  |
| fax | varchar | - | - |  |
| email | varchar | - | - |  |
| alternative_contact_1 | varchar | - | - |  |
| alternative1_telephone | varchar | - | - |  |
| alternative1_mobile | varchar | - | - |  |
| alternative1_fax | varchar | - | - |  |
| alternative1_email | varchar | - | - |  |
| alternative_contact_2 | varchar | - | - |  |
| alternative2_telephone | varchar | - | - |  |
| alternative2_mobile | varchar | - | - |  |
| alternative2_fax | varchar | - | - |  |
| alternative2_email | varchar | - | - |  |
| remarks | text | - | - |  |
| date_created | datetime | - | - |  |
| user_id | int | - | - |  |
| is_deleted | bit | - | - |  |
| logo | varchar | - | - |  |
| agent_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "agent_code": "OWE",
    "agent_name": "One World Express",
    "active": 1,
    "contact_name": "Gordon Shuttleworth"
}
... (more columns)
```

---

## Table: `smarttrack_staging.agent_document`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "agent_id": 1,
    "document_id": 1,
    "document_name": "150780039822153082.pdf",
    "added_by": 148
}
... (more columns)
```

---

## Table: `smarttrack_staging.agent_log`
- **Record Count:** 31
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-10-23 16:04:35",
    "ipaddress": "0",
    "log_id": 59
}
... (more columns)
```

---

## Table: `smarttrack_staging.agent_restricted_postcode`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| agent_id | int | - | - |  |
| service_id | int | - | - |  |
| postcode_city | varchar | - | - |  |
| is_city | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11,
    "agent_id": 1,
    "service_id": 226,
    "postcode_city": "UB77RB",
    "is_city": 0
}
... (more columns)
```

---

## Table: `smarttrack_staging.api_data`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| api_request | text | - | - |  |
| api_response | text | - | - |  |
| added_by | varchar | - | - |  |
| date_created | datetime | - | - |  |
| api_reason | varchar | - | - |  |
| type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.auto_tracking`
- **Record Count:** 16
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| next_number | int | - | - |  |
| range_end | int | - | - |  |
| increment_date | datetime | - | - |  |
| service_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "next_number": 11703384,
    "range_end": 11917468,
    "increment_date": "0000-00-00 00:00:00",
    "service_name": "Yodel"
}
... (more columns)
```

---

## Table: `smarttrack_staging.bag_scan_log`
- **Record Count:** 225
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2182,
    "logdate": "2018-12-05 11:11:57",
    "ipaddress": "3065225534",
    "log_id": 10082
}
... (more columns)
```

---

## Table: `smarttrack_staging.bagging`
- **Record Count:** 296
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| bagnumber | varchar | - | - |  |
| date_created | datetime | - | - |  |
| csv | varchar | - | - |  |
| pdf | varchar | - | - |  |
| manifestid | int | - | - |  |
| account | varchar | - | - |  |
| user_id | int | - | - |  |
| manifest_pdf | varchar | - | - |  |
| bag_status | tinyint | - | - |  |
| date_updated | datetime | - | - |  |
| isdeleted | tinyint | - | - |  |
| service | varchar | - | - |  |
| serviceid | int | - | - |  |
| country | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| bag_type | varchar | - | - |  |
| actual_weight | decimal | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| pieces | int | - | - |  |
| weight | decimal | - | - |  |
| bag_label | varchar | - | - |  |
| bag_source_country_id | int | - | - |  |
| bag_source_warehouse_id | int | - | - |  |
| bag_destination_country_id | int | - | - |  |
| bag_destination_warehouse_id | int | - | - |  |
| is_closed | bit | - | - |  |
| closed_by | bigint | - | - |  |
| closed_date | datetime | - | - |  |
| reopen_by | bigint | - | - |  |
| reopen_date | datetime | - | - |  |
| bag_manifest | varchar | - | - |  |
| bag_value | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 10075,
    "bagnumber": "BHX10075248",
    "date_created": "2018-06-11 11:39:20",
    "csv": "",
    "pdf": ""
}
... (more columns)
```

---

## Table: `smarttrack_staging.bagging_manifest_mapping`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| manifest_id | int | - | - |  |
| manifest_entity_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.bagging_services_mapping`
- **Record Count:** 4246
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| bag_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "bag_id": 10075,
    "service_id": 248
}
... (more columns)
```

---

## Table: `smarttrack_staging.bagnumbers`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| consignment_id | varchar | - | - |  |
| parcel_id | int | - | - |  |
| tag_number | varchar | - | - |  |
| bag_number | int | - | - |  |
| service | varchar | - | - |  |
| value | varchar | - | - |  |
| weight | varchar | - | - |  |
| number_pieces | varchar | - | - |  |
| label_file | varchar | - | - |  |
| manifest_file | varchar | - | - |  |
| status | varchar | - | - |  |
| date_created | varchar | - | - |  |
| date_printed | varchar | - | - |  |
| flightnumber | varchar | - | - |  |
| flight_id | int | - | - |  |
| mawb | int | - | - |  |
| accountnumber | varchar | - | - |  |
| destination_addr | varchar | - | - |  |
| country | varchar | - | - |  |
| dispatchdate | varchar | - | - |  |
| mail_number | varchar | - | - |  |
| last_bag | varchar | - | - |  |
| flight_datetime | varchar | - | - |  |
| bag_type | varchar | - | - |  |
| is_track | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.box_info`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| box_number | varchar | - | - |  |
| box_size | varchar | - | - |  |
| box_weight | decimal | - | - |  |
| tracking_numbers | text | - | - |  |
| manifest_number | varchar | - | - |  |
| mawb_number | varchar | - | - |  |
| date_submitted | datetime | - | - |  |
| date_scanned | datetime | - | - |  |
| api_data | text | - | - |  |
| status | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.brazil_postcode`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state | varchar | - | - |  |
| locality | varchar | - | - |  |
| postcode | varchar | - | - |  |
| zone | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.brazil_state`
- **Record Count:** 4063
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state_code | varchar | - | - |  |
| state_name | varchar | - | - |  |
| city_code | varchar | - | - |  |
| city_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "state_code": "421",
    "state_name": "Acre",
    "city_code": "2495",
    "city_name": "Brasileia"
}
... (more columns)
```

---

## Table: `smarttrack_staging.bulletins`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| heading | varchar | - | - |  |
| description | text | - | - |  |
| date_created | datetime | - | - |  |
| date_submitted | datetime | - | - |  |
| created_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "heading": "Test Bulletin",
    "description": "This is **_test_** description\ns\nd\ndd\n",
    "date_created": "2018-11-18 15:55:13",
    "date_submitted": "2018-11-12 00:00:00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.cacesa_routine`
- **Record Count:** 13989
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| postcode | varchar | - | - |  |
| agency | varchar | - | - |  |
| route_description | varchar | - | - |  |
| route_id | varchar | - | - |  |
| courier | varchar | - | - |  |
| routing | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "H",
    "postcode": "01216",
    "agency": "000909",
    "route_description": "09-MIRANDA DE EBRO"
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier`
- **Record Count:** 1
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier | varchar | - | - |  |
| logo | varchar | - | - |  |
| cut_off_time | varchar | - | - |  |
| carrier_display_name | varchar | - | - |  |
| status | int | - | - |  |
| country_id | int | - | - |  |
| carrier_id | int | - | - |  |
| currency_code | varchar | - | - |  |
| remotearea_check | enum | - | - |  |
| zone_base | bit | - | - |  |
| zone_type | enum | - | - |  |
| on_contract | bit | - | - |  |
| is_gazetteer | tinyint | - | - |  |
| is_reconcile | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 16,
    "carrier": "Amazon",
    "logo": "amazon.png",
    "cut_off_time": "18:00",
    "carrier_display_name": "Amazon"
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_agent`
- **Record Count:** 0
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_number | varchar | - | - |  |
| name | varchar | - | - |  |
| company | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| api_username | varchar | - | - |  |
| api_password | varchar | - | - |  |
| ftp_host | varchar | - | - |  |
| ftp_username | varchar | - | - |  |
| ftp_password | varchar | - | - |  |
| integration_type | varchar | - | - |  |
| date_created | datetime | - | - |  |
| created_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.carrier_data_file_log`
- **Record Count:** 158
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| carrier_id | bigint | - | - |  |
| agent_id | bigint | - | - |  |
| file_name | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| run_number | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "agent_id": 1,
    "file_name": "UKD10161.001",
    "date_created": "2018-11-16 22:25:40"
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_document`
- **Record Count:** 9
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "carrier_id": 19,
    "document_id": 7,
    "document_name": "1508238498aramex.png",
    "added_by": 148
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_hubs`
- **Record Count:** 60
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| hub | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "hub": "52_INVERNESS",
    "routing_code": "52",
    "company": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_log`
- **Record Count:** 91
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-09-09 11:33:10",
    "ipaddress": "2006650821",
    "log_id": 16
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_service_customize_rules`
- **Record Count:** 22
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| user_account_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3973,
    "serviceid": 248,
    "agentid": 73,
    "user_account_id": 2294,
    "from_weight": "0.000"
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_service_default_rules`
- **Record Count:** 151
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| is_default | bit | - | - |  |
| agent_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 98,
    "serviceid": 1,
    "agentid": 4,
    "from_weight": "1.000",
    "to_weight": "2.000"
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_zones`
- **Record Count:** 2050
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| service_id | int | - | - |  |
| name | varchar | - | - |  |
| sort_order | int | - | - |  |
| status | tinyint | - | - |  |
| deleted | tinyint | - | - |  |
| date_added | datetime | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 194,
    "service_id": 0,
    "name": "UKMELL LOCAL ZONE",
    "sort_order": 7
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_zones_countries`
- **Record Count:** 2728
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| carrier_zone_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 13,
    "country_id": 225,
    "carrier_zone_id": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.carrier_zones_postcode`
- **Record Count:** 5
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_zone_id | int | - | - |  |
| postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 0,
    "carrier_zone_id": 2812,
    "postcode": "Ub3 3nb"
}
... (more columns)
```

---

## Table: `smarttrack_staging.carton_pallet_number`
- **Record Count:** 492
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| type | varchar | - | - |  |
| quantity | int | - | - |  |
| start_number | varchar | - | - |  |
| end_number | varchar | - | - |  |
| date_created | datetime | - | - |  |
| userid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "type": "B",
    "quantity": 3,
    "start_number": "0",
    "end_number": "0"
}
... (more columns)
```

---

## Table: `smarttrack_staging.ch_shipments`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| reference | varchar | - | - |  |
| awb | varchar | - | - |  |
| account | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |
| postcode | varchar | - | - |  |
| product | varchar | - | - |  |
| service | varchar | - | - |  |
| bagnumber | varchar | - | - |  |
| mawb | varchar | - | - |  |
| charge_able_weight | decimal | - | - |  |
| total_charge | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment`
- **Record Count:** 26
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| agent_id | int | - | - |  |
| user_id | int | - | - |  |
| service_id | int | - | - |  |
| customized_service_id | int | - | - |  |
| warehouse_user_id | int | - | - |  |
| warehouse_id | int | - | - |  |
| sales_pot_id | bigint | - | - |  |
| invoice_id | int | - | - |  |
| credit_id | int | - | - |  |
| is_invoiced | int | - | - |  |
| invoice_type | enum | - | - |  |
| shipment_status | int | - | - |  |
| shipment_type | enum | - | - |  |
| awb | varchar | - | - |  |
| consignment_status | varchar | - | - |  |
| return_awb | varchar | - | - |  |
| hawb | varchar | - | - |  |
| mawb | varchar | - | - |  |
| service_name | varchar | - | - |  |
| reference | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| date_label_created | int | - | - |  |
| date_booked | int | - | - |  |
| date_delivered | int | - | - |  |
| is_customer_manifested | int | - | - |  |
| booked_file_id | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| state | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_id | int | - | - |  |
| telephone | varchar | - | - |  |
| number_pieces | int | - | - |  |
| weight_type | varchar | - | - |  |
| weight | decimal | - | - |  |
| update_weight | decimal | - | - |  |
| fake_weight | decimal | - | - |  |
| charge_weight | decimal | - | - |  |
| vol_weight | decimal | - | - |  |
| vol_demonimator | int | - | - |  |
| hv_lv | enum | - | - |  |
| description | varchar | - | - |  |
| notes | varchar | - | - |  |
| value | decimal | - | - |  |
| currency | varchar | - | - |  |
| sender_name | varchar | - | - |  |
| username | varchar | - | - |  |
| sender_checked | int | - | - |  |
| message | varchar | - | - |  |
| sorter_image | varchar | - | - |  |
| label_file | varchar | - | - |  |
| is_doc | int | - | - |  |
| email | varchar | - | - |  |
| itemtype | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| routing_code_eur | varchar | - | - |  |
| other_routing_code | varchar | - | - |  |
| billing_hold | int | - | - |  |
| send_courier_data | int | - | - |  |
| remote_charges | int | - | - |  |
| reinvoices | int | - | - |  |
| optimus_sorter | int | - | - |  |
| full_pallet | int | - | - |  |
| half_pallet | int | - | - |  |
| quarter_pallet | int | - | - |  |
| date_scanned | datetime | - | - |  |
| consignment_type | enum | - | - |  |
| api_uuid | varchar | - | - |  |
| sender_company | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| sender_telephone | varchar | - | - |  |
| sender_address_line_1 | varchar | - | - |  |
| sender_address_line_2 | varchar | - | - |  |
| sender_address_line_3 | varchar | - | - |  |
| sender_city | varchar | - | - |  |
| sender_postcode | varchar | - | - |  |
| sender_country_id | int | - | - |  |
| sender_state | varchar | - | - |  |
| collection_date | date | - | - |  |
| collection_start_time | varchar | - | - |  |
| collection_end_time | varchar | - | - |  |
| collection_confirmation_no | varchar | - | - |  |
| created_from | enum | - | - |  |
| is_white_label | tinyint | - | - |  |
| is_dead_weight_chargable | tinyint | - | - |  |
| is_customer_billable | int | - | - |  |
| ioss_number | varchar | - | - |  |
| eori_number | varchar | - | - |  |
| vat_number | varchar | - | - |  |
| is_over_size_chargable | int | - | - |  |
| is_insured | int | - | - |  |
| destination_warehouse_id | int | - | - |  |
| consignment_seller | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 170442,
    "agent_id": 0,
    "user_id": 2341,
    "service_id": 226,
    "customized_service_id": 0
}
... (more columns)
```

---

## Table: `smarttrack_staging.consignment_bagging_mapping`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| consignmentid | int | - | - |  |
| bagid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_billing_hold`
- **Record Count:** 6
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| reason_for_hold | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 19,
    "consignment_id": 106855,
    "user_account_id_from": 148,
    "user_account_id_to": 2294,
    "reason_for_hold": "test"
}
... (more columns)
```

---

## Table: `smarttrack_staging.consignment_billing_hold_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| status | enum | - | - |  |
| reason | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_charges`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | int | - | - |  |
| account_id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_id | int | - | - |  |
| charge_type_id | int | - | - |  |
| agent_id | int | - | - |  |
| cost_type | enum | - | - |  |
| cost | decimal | - | - |  |
| cost_currency | varchar | - | - |  |
| cost_supplier_currency | decimal | - | - |  |
| supplier_currency | varchar | - | - |  |
| cost_company_currency | decimal | - | - |  |
| company_currency | varchar | - | - |  |
| description | varchar | - | - |  |
| changes_reference | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_charges_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_charges_types`
- **Record Count:** 30
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| charges_key | varchar | - | - |  |
| charge_type | enum | - | - |  |
| apply_per_kg | bit | - | - |  |
| is_extra_charge | bit | - | - |  |
| is_vat | bit | - | - |  |
| has_account_default_value | bit | - | - |  |
| is_replace_charges | bit | - | - |  |
| status | tinyint | - | - |  |
| is_delete | bit | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Basic Charges",
    "charges_key": "BASIC_CHARGES",
    "charge_type": "both",
    "apply_per_kg": 0
}
... (more columns)
```

---

## Table: `smarttrack_staging.consignment_collection`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| sender_company | varchar | - | - |  |
| sender_contact | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| sender_address_line_1 | varchar | - | - |  |
| sender_address_line_2 | varchar | - | - |  |
| sender_address_line_3 | varchar | - | - |  |
| sender_city | varchar | - | - |  |
| sender_country_iso_code | char | - | - |  |
| sender_postcode | varchar | - | - |  |
| sender_telephone | varchar | - | - |  |
| date_collection | int | - | - |  |
| earliest_latest_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_details`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| custom_export_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_dropoff_mapping`
- **Record Count:** 8
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| dropoff_consignment_id | bigint | - | - |  |
| dispatch_consignment_id | bigint | - | - |  |
| dropoff_consignment_tracking | text | - | - |  |
| dispatch_consignment_tracking | text | - | - |  |
| parcel_tracking | text | - | - |  |
| added_by | bigint | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "dropoff_consignment_id": 160151,
    "dispatch_consignment_id": 160152,
    "dropoff_consignment_tracking": "1Z3985RW6806068797",
    "dispatch_consignment_tracking": "JD0002210161165769"
}
... (more columns)
```

---

## Table: `smarttrack_staging.consignment_hold`
- **Record Count:** 1
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| comments | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| date_created | datetime | - | - |  |
| action | varchar | - | - |  |
| reason_tag | varchar | - | - |  |
| weight | varchar | - | - |  |
| width | varchar | - | - |  |
| height | varchar | - | - |  |
| length | varchar | - | - |  |
| volume | varchar | - | - |  |
| image | varchar | - | - |  |
| account | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "userid": null,
    "comments": null,
    "tracking_number": null,
    "date_created": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.consignment_hold_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| user_account_id_from | int | - | - |  |
| user_account_id_to | int | - | - |  |
| status | enum | - | - |  |
| reason | varchar | - | - |  |
| added_by | int | - | - |  |
| date_added | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_hscode`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| hscode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_pod`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignmentid | int | - | - |  |
| signature | varchar | - | - |  |
| pod_date | varchar | - | - |  |
| pod_image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.consignment_relabel`
- **Record Count:** 108
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| old_tracking_no | varchar | - | - |  |
| new_tracking_no | varchar | - | - |  |
| date_created | datetime | - | - |  |
| userid | int | - | - |  |
| old_consignment_data | text | - | - |  |
| old_parcel_tracking_no | text | - | - |  |
| old_new_tracking_mapping | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "consignment_id": 95962,
    "old_tracking_no": "JD0002210161134700",
    "new_tracking_no": "JD0002210161134700",
    "date_created": "2019-01-25 13:14:47"
}
... (more columns)
```

---

## Table: `smarttrack_staging.consignment_status_log`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| parcel_id | bigint | - | - |  |
| old_status | varchar | - | - |  |
| new_status | varchar | - | - |  |
| message | text | - | - |  |
| added_by | bigint | - | - |  |
| date_added | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.correos_brazil_datafile`
- **Record Count:** 9
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 9,
    "file_name": "temp",
    "sent_date": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.cost_tariffs`
- **Record Count:** 10
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| courier_service_id | int | - | - |  |
| collection_rateband_id | int | - | - |  |
| destination_rateband_id | int | - | - |  |
| collection_postcode_group_id | int | - | - |  |
| destination_postcode_group_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| tariff_cost | decimal | - | - |  |
| unit_cost | decimal | - | - |  |
| unit_size | decimal | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| tariff_name | varchar | - | - |  |
| formula | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "courier_service_id": 79,
    "collection_rateband_id": 916,
    "destination_rateband_id": 916,
    "collection_postcode_group_id": 0
}
... (more columns)
```

---

## Table: `smarttrack_staging.countries_link_ratebands`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| rateband_id | int | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.country`
- **Record Count:** 269
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| iso | char | - | - |  |
| name | varchar | - | - |  |
| region | varchar | - | - |  |
| postcode_required | enum | - | - |  |
| type | varchar | - | - |  |
| region_collection | varchar | - | - |  |
| numcode | int | - | - |  |
| allow_express | char | - | - |  |
| allow_classic | char | - | - |  |
| eu_country | char | - | - |  |
| shipping_advice | varchar | - | - |  |
| is_vatable | enum | - | - |  |
| vat_rate | decimal | - | - |  |
| printable_name | varchar | - | - |  |
| iso3 | char | - | - |  |
| export_flag | int | - | - |  |
| timezone_difference | int | - | - |  |
| has_postcodeq | char | - | - |  |
| has_subzonesq | char | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| vat_charged_flag | int | - | - |  |
| customs_flag | int | - | - |  |
| description | text | - | - |  |
| country_image | varchar | - | - |  |
| metakeywords | varchar | - | - |  |
| metadescription | varchar | - | - |  |
| pagetitle | varchar | - | - |  |
| countrybanner | varchar | - | - |  |
| opcode | varchar | - | - |  |
| iso_three | varchar | - | - |  |
| german_name | varchar | - | - |  |
| manifest_template | varchar | - | - |  |
| bag_template | varchar | - | - |  |
| bag_weight_limit | int | - | - |  |
| bag_low_value | int | - | - |  |
| currency_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "iso": "AF",
    "name": "Afghanistan",
    "region": "INT",
    "postcode_required": "NO"
}
... (more columns)
```

---

## Table: `smarttrack_staging.cpost_manifest`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| manifest_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.credit_note`
- **Record Count:** 21
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| credit_note_number | varchar | - | - |  |
| user_account_id | int | - | - |  |
| invoice_type | enum | - | - |  |
| invoice_number | varchar | - | - |  |
| credit_note_type | enum | - | - |  |
| hawb | text | - | - |  |
| credit_note_heading | text | - | - |  |
| credit_date | datetime | - | - |  |
| net_amount | decimal | - | - |  |
| vat_amount | decimal | - | - |  |
| credit_total | decimal | - | - |  |
| credit_note_by | int | - | - |  |
| pdf | varchar | - | - |  |
| is_email | bit | - | - |  |
| is_read | bit | - | - |  |
| added_by | int | - | - |  |
| date_created | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| currency_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "credit_note_number": "",
    "user_account_id": 2286,
    "invoice_type": "MNI",
    "invoice_number": "MNI1019"
}
... (more columns)
```

---

## Table: `smarttrack_staging.credit_note_details`
- **Record Count:** 25
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| credit_note_id | int | - | - |  |
| hawb | varchar | - | - |  |
| date_booked | datetime | - | - |  |
| reference | varchar | - | - |  |
| invoice_amount | decimal | - | - |  |
| chargeable_amount | decimal | - | - |  |
| credit_amount | decimal | - | - |  |
| description | varchar | - | - |  |
| is_vatable | enum | - | - |  |
| date_created | timestamp | - | - |  |
| updated_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| vat_amount | decimal | - | - |  |
| added_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "credit_note_id": 2,
    "hawb": "Test",
    "date_booked": "2018-11-25 00:00:00",
    "reference": "Test ref"
}
... (more columns)
```

---

## Table: `smarttrack_staging.cs_log`
- **Record Count:** 8
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| internal_message | text | - | - |  |
| customer_message | text | - | - |  |
| cust_mail | varchar | - | - |  |
| agent_mail | varchar | - | - |  |
| date_created | datetime | - | - |  |
| reminder | varchar | - | - |  |
| reminder_expiry | datetime | - | - |  |
| userid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "consignment_id": 93154,
    "internal_message": "",
    "customer_message": "asdfasdf",
    "cust_mail": "No"
}
... (more columns)
```

---

## Table: `smarttrack_staging.cs_notes`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| notes | text | - | - |  |
| created_by | bigint | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "notes": "Test Notes",
    "created_by": 2234,
    "date_created": "2018-11-12 17:12:49"
}
... (more columns)
```

---

## Table: `smarttrack_staging.csv_import_template`
- **Record Count:** 17
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| template_name | varchar | - | - |  |
| template | text | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| update_by | bigint | - | - |  |
| update_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "user_id": 2191,
    "user_account_id": 2297,
    "template_name": "Tahir Finance Account Template",
    "template": "{\"date_added\":\"date_added\",\"receiver_country_iso\":\"receiver_country_iso\",\"service_code\":\"service_code\",\"order_reference\":\"order_reference\",\"receiver_contact\":\"receiver_contact\",\"receiver_address_line_1\":\"receiver_address_line_1\",\"receiver_city\":\"receiver_city\",\"receiver_postcode\":\"receiver_postcode\",\"description\":\"description\",\"parcels\":\"parcels\"}"
}
... (more columns)
```

---

## Table: `smarttrack_staging.csv_tracking_template`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| template_name | varchar | - | - |  |
| template | text | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| update_by | bigint | - | - |  |
| update_date | datetime | - | - |  |
| service_id | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "user_id": 2324,
    "user_account_id": 2357,
    "template_name": "testtest",
    "template": "{\"Data Received\":\"Data Received\",\"Arrived at Sort Facility Hayes - GBR\":\"Arrived at Sort Facility Hayes - GBR\",\"Arrived at Sort Facility Hamburg - GBR\":\"Arrived at Sort Facility Hamburg - GBR\",\"Departed Facility in Hamburg - GBR\":\"Departed Facility in Hamburg - GBR\"}"
}
... (more columns)
```

---

## Table: `smarttrack_staging.ctt_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.currency`
- **Record Count:** 90
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| currencyname | varchar | - | - |  |
| leftsymbol | varchar | - | - |  |
| rightsymbol | varchar | - | - |  |
| isdefault | tinyint | - | - |  |
| currencyexchangerate | decimal | - | - |  |
| isactive | tinyint | - | - |  |
| clientdisplay | tinyint | - | - |  |
| currencyid | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "currencyname": "US Dollar",
    "leftsymbol": "$",
    "rightsymbol": "USD",
    "isdefault": 0
}
... (more columns)
```

---

## Table: `smarttrack_staging.currency_temp`
- **Record Count:** 96
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| country | varchar | - | - |  |
| currency | varchar | - | - |  |
| code | varchar | - | - |  |
| symbol | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "country": "Albania",
    "currency": "Leke",
    "code": "ALL",
    "symbol": "Lek"
}
... (more columns)
```

---

## Table: `smarttrack_staging.customer_account`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| active_flag | bit | - | - |  |
| company | varchar | - | - |  |
| full_name | varchar | - | - |  |
| return_address | varchar | - | - |  |
| sms_dpd | bit | - | - |  |
| user_service_type | enum | - | - |  |
| parentid | int | - | - |  |
| phone | varchar | - | - |  |
| logo | varchar | - | - |  |
| instant_label | bit | - | - |  |
| country | varchar | - | - |  |
| country_id | int | - | - |  |
| tracking_api_access | bit | - | - |  |
| import_data_csv | bit | - | - |  |
| proforma | bit | - | - |  |
| add_tracking | bit | - | - |  |
| collection | bit | - | - |  |
| default_description | varchar | - | - |  |
| default_notes | varchar | - | - |  |
| default_weight | decimal | - | - |  |
| payment_term | text | - | - |  |
| query_term | text | - | - |  |
| vat_number | varchar | - | - |  |
| billing_currency | varchar | - | - |  |
| vat_chargable | bit | - | - |  |
| vat_value | decimal | - | - |  |
| allow_remote_area | bit | - | - |  |
| telephone | varchar | - | - |  |
| billing_address | varchar | - | - |  |
| date_dispatch | bit | - | - |  |
| is_product | varchar | - | - |  |
| profile_image | varchar | - | - |  |
| send_courier_data | bit | - | - |  |
| archive_server | bit | - | - |  |
| credit_check | bit | - | - |  |
| tariff_agreed | bit | - | - |  |
| sales_person | varchar | - | - |  |
| scan_document | text | - | - |  |
| data_entry | bit | - | - |  |
| bank_account_title | varchar | - | - |  |
| bank_sortcode | varchar | - | - |  |
| bank_account_number | varchar | - | - |  |
| bank_branch_address | varchar | - | - |  |
| trade_name_i | varchar | - | - |  |
| trade_address_i | varchar | - | - |  |
| trade_email_i | varchar | - | - |  |
| trade_phone_i | varchar | - | - |  |
| trade_name_ii | varchar | - | - |  |
| trade_address_ii | varchar | - | - |  |
| trade_email_ii | varchar | - | - |  |
| trade_phone_ii | varchar | - | - |  |
| reg_number | varchar | - | - |  |
| reg_address | varchar | - | - |  |
| reg_postcode | varchar | - | - |  |
| reg_country | varchar | - | - |  |
| sale_agent | varchar | - | - |  |
| sale_date | datetime | - | - |  |
| fuel_charges | decimal | - | - |  |
| warehouse_id | int | - | - |  |
| user_signature | text | - | - |  |
| is_fuelcharges_include | bit | - | - |  |
| is_prepaid | bit | - | - |  |
| return_label | bit | - | - |  |
| finalmile_over_label | bit | - | - |  |
| request_manifest_collection | bit | - | - |  |
| create_pre_alert | bit | - | - |  |
| is_employee | bit | - | - |  |
| invoice_bank_details_id | int | - | - |  |
| check_list_account_form | bit | - | - |  |
| check_list_credit_check | bit | - | - |  |
| check_list_t_cs | bit | - | - |  |
| check_list_tariff_agreed | bit | - | - |  |
| check_list_sales_pot | bit | - | - |  |
| sales_pot_time_period | int | - | - |  |
| sales_pot_percentage | decimal | - | - |  |
| last_login_date | timestamp | - | - |  |
| invalid_login_count | int | - | - |  |
| token | varchar | - | - |  |
| token_updated | timestamp | - | - |  |
| lock_time | timestamp | - | - |  |
| opearation_manifest | bit | - | - |  |
| own_tariff | bit | - | - |  |
| user_warehouse | enum | - | - |  |
| api_key | varchar | - | - |  |
| api_secert | varchar | - | - |  |
| api_date | datetime | - | - |  |
| bagging | bit | - | - |  |
| retail_customer | bit | - | - |  |
| show_price | bit | - | - |  |
| sales_rate | decimal | - | - |  |
| collection_add_line_1 | varchar | - | - |  |
| collection_add_line_2 | varchar | - | - |  |
| collection_add_line_3 | varchar | - | - |  |
| collection_city | varchar | - | - |  |
| collection_postcode | varchar | - | - |  |
| collection_country | varchar | - | - |  |
| theme_id | int | - | - |  |
| user_code | int | - | - |  |
| website_link | varchar | - | - |  |
| allow_return_email | bit | - | - |  |
| default_lang | varchar | - | - |  |
| credit_limit | decimal | - | - |  |
| invoice_period | enum | - | - |  |
| label_price | decimal | - | - |  |
| discount | decimal | - | - |  |
| account_code | varchar | - | - |  |
| paypal_email | varchar | - | - |  |
| paypal_currency | varchar | - | - |  |
| email | varchar | - | - |  |
| paypal_client_secret | varchar | - | - |  |
| alternative_email | varchar | - | - |  |
| billing_email | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| paypal_client_id | varchar | - | - |  |
| invoice_template_id | bigint | - | - |  |
| send_tracking_data | bit | - | - |  |
| billing_contact | varchar | - | - |  |
| ftp_shipment_upload | int | - | - |  |
| balance_alert_percentage | int | - | - |  |
| commission_break_event_account_amount | int | - | - |  |
| tracking_order_prefix | varchar | - | - |  |
| return_shipment_allow | int | - | - |  |
| account_balance | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 148,
    "user_account": "PDS",
    "active_flag": 1,
    "company": "Parcel Delivey Solution",
    "full_name": "PDS"
}
... (more columns)
```

---

## Table: `smarttrack_staging.customized_services_routing`
- **Record Count:** 38
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | int | - | - |  |
| customize_service_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 15658,
    "country_id": 150,
    "from_weight": "20.50",
    "to_weight": "20.75",
    "status": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.customized_services_routing_log`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| message | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.customized_user_services_routing`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| user_account_id | int | - | - |  |
| routing_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.cz_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.czint_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.department`
- **Record Count:** 7
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| department_code | varchar | - | - |  |
| description | varchar | - | - |  |
| department_head | bigint | - | - |  |
| isactive | tinyint | - | - |  |
| isdeleted | tinyint | - | - |  |
| addedby | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | bigint | - | - |  |
| updated_on | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Accounts",
    "department_code": "ACC",
    "description": "Accounts & Finance",
    "department_head": 58
}
... (more columns)
```

---

## Table: `smarttrack_staging.deutschepost_dhl_streetcode`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| street | varchar | - | - |  |
| zipcode | varchar | - | - |  |
| street_code | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.deutschepostdhl_cargo_code`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| start_postcode | int | - | - |  |
| end_postcode | int | - | - |  |
| cargo_code | int | - | - |  |
| municipality_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "start_postcode": 1000,
    "end_postcode": 1999,
    "cargo_code": 1,
    "municipality_name": "Ottendorf-Okrilla"
}
... (more columns)
```

---

## Table: `smarttrack_staging.document_type`
- **Record Count:** 17
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| document_name | varchar | - | - |  |
| description | varchar | - | - |  |
| document_type | enum | - | - |  |
| is_active | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |
| is_delete | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "document_name": "T & Cs",
    "description": "T & Cs",
    "document_type": "company_contract",
    "is_active": "1"
}
... (more columns)
```

---

## Table: `smarttrack_staging.domestic`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode_sector | varchar | - | - |  |
| dpd_depot | varchar | - | - |  |
| dpd_services_group | varchar | - | - |  |
| dpd_offshore_zone | varchar | - | - |  |
| timeslots_code | varchar | - | - |  |
| cluster | varchar | - | - |  |
| ilk_depot | varchar | - | - |  |
| ilk_services_group | varchar | - | - |  |
| ilk_offshore_zone | varchar | - | - |  |
| ilk_alternate_service | varchar | - | - |  |
| new_postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11990,
    "postcode_sector": "ZE1 9",
    "dpd_depot": "0082",
    "dpd_services_group": "7",
    "dpd_offshore_zone": "3221"
}
... (more columns)
```

---

## Table: `smarttrack_staging.domestic_day_file`
- **Record Count:** 705
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": "13147",
    "file_name": "UKD57356.147",
    "sent_date": "2016-09-04 19:00:09"
}
... (more columns)
```

---

## Table: `smarttrack_staging.dpd_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.dpdgroups`
- **Record Count:** 79
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| lookup_code | varchar | - | - |  |
| list_of_available_services | varchar | - | - |  |
| Business | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "lookup_code": "1",
    "list_of_available_services": "000000000-010000000-000000000-010000000-000000000-010000000-000000000-010000000-010000000-000000000-",
    "Business": "D"
}
... (more columns)
```

---

## Table: `smarttrack_staging.dropoff_user_location`
- **Record Count:** 29
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| service_id | int | - | - |  |
| user_id | bigint | - | - |  |
| companyname | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country | varchar | - | - |  |
| telephone | varchar | - | - |  |
| mon | varchar | - | - |  |
| tue | varchar | - | - |  |
| wed | varchar | - | - |  |
| thu | varchar | - | - |  |
| fri | varchar | - | - |  |
| sat | varchar | - | - |  |
| sun | varchar | - | - |  |
| lat | varchar | - | - |  |
| lng | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 29,
    "service_id": 12,
    "user_id": 58,
    "companyname": "COLNBROOK PHARMACY",
    "address_line_1": "36 HIGH STREET"
}
... (more columns)
```

---

## Table: `smarttrack_staging.dx_routing`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| district | varchar | - | - |  |
| sector | varchar | - | - |  |
| depot | varchar | - | - |  |
| depotid | varchar | - | - |  |
| region_id | varchar | - | - |  |
| delivery_method | varchar | - | - |  |
| delivery_method_id | varchar | - | - |  |
| delivery_method_description | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.emailtemplate`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| emailtemplateid | int | - | - |  |
| title | varchar | - | - |  |
| shortkey | varchar | - | - |  |
| content | text | - | - |  |
| isactive | tinyint | - | - |  |
| createdon | timestamp | - | - |  |
| isdeleted | tinyint | - | - |  |
| type | varchar | - | - |  |
| pagetitle | varchar | - | - |  |
| metatitle | varchar | - | - |  |
| metadescription | varchar | - | - |  |
| metakeywords | varchar | - | - |  |
| sorder | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.estimate_delivery_timing`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| from_rateband | int | - | - |  |
| to_rateband | int | - | - |  |
| date_created | datetime | - | - |  |
| created_by | varchar | - | - |  |
| status | enum | - | - |  |
| delivery_timing | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.euro_day_file`
- **Record Count:** 100
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_name": "COSSW46E_PreAdvice3_000000001",
    "sent_date": "2018-11-22 17:21:15"
}
... (more columns)
```

---

## Table: `smarttrack_staging.fftin_file`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| label_link | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.flight_info`
- **Record Count:** 32
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| flight_number | varchar | - | - |  |
| country_id | int | - | - |  |
| destination_country_id | int | - | - |  |
| date_created | datetime | - | - |  |
| current_status | enum | - | - |  |
| status | enum | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| signature | varchar | - | - |  |
| carrier | varchar | - | - |  |
| carriage_value | decimal | - | - |  |
| custom_value | decimal | - | - |  |
| insurance_amount | decimal | - | - |  |
| currency | varchar | - | - |  |
| connecting_flight_number | varchar | - | - |  |
| weight_type | varchar | - | - |  |
| rate_charge | varchar | - | - |  |
| iata_code | varchar | - | - |  |
| departure_airport | varchar | - | - |  |
| phone_number | varchar | - | - |  |
| shipper_co | varchar | - | - |  |
| consignee_co | varchar | - | - |  |
| arrival_airport | varchar | - | - |  |
| account_id | int | - | - |  |
| shippers_name | varchar | - | - |  |
| shippers_addressline1 | varchar | - | - |  |
| shippers_addressline2 | varchar | - | - |  |
| accounting_reference | varchar | - | - |  |
| reference | varchar | - | - |  |
| rate_change | varchar | - | - |  |
| low_value_manifest | varchar | - | - |  |
| high_value_manifest | varchar | - | - |  |
| invoice | varchar | - | - |  |
| files_hv | varchar | - | - |  |
| hscodes | varchar | - | - |  |
| etd | varchar | - | - |  |
| eta | varchar | - | - |  |
| shed | varchar | - | - |  |
| files_lv | varchar | - | - |  |
| cleared | varchar | - | - |  |
| comments | varchar | - | - |  |
| weight | varchar | - | - |  |
| pieces | varchar | - | - |  |
| created_by | bigint | - | - |  |
| is_delete | tinyint | - | - |  |
| is_closed | tinyint | - | - |  |
| account_number | varchar | - | - |  |
| airway_bill | varchar | - | - |  |
| company | varchar | - | - |  |
| currancy | varchar | - | - |  |
| files | varchar | - | - |  |
| destination_company | varchar | - | - |  |
| destination_phone_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "flight_number": "PK 0909",
    "country_id": 225,
    "destination_country_id": 225,
    "date_created": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.flight_mapping`
- **Record Count:** 41
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| flight_info_id | int | - | - |  |
| flight_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| mawb_id | int | - | - |  |
| is_delete | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "flight_info_id": 1,
    "flight_number": "",
    "mawb": "",
    "mawb_id": 7
}
... (more columns)
```

---

## Table: `smarttrack_staging.forget_password_request`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_name | varchar | - | - |  |
| ip_address | varchar | - | - |  |
| user_agent | varchar | - | - |  |
| token | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| date_expire | datetime | - | - |  |
| is_expire | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.grouphaspermissions`
- **Record Count:** 1922
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| group_id | int | - | - |  |
| perm_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 25,
    "group_id": 19,
    "perm_id": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.groups`
- **Record Count:** 51
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| group_id | int | - | - |  |
| group_name | varchar | - | - |  |
| group_slug | varchar | - | - |  |
| group_desc | varchar | - | - |  |
| group_type | enum | - | - |  |
| is_active | tinyint | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| is_deleted | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "group_id": 17,
    "group_name": "Admin",
    "group_slug": "admin",
    "group_desc": "Admin",
    "group_type": "client"
}
... (more columns)
```

---

## Table: `smarttrack_staging.groups_log`
- **Record Count:** 221
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-08-07 16:21:43",
    "ipaddress": "656769484",
    "log_id": 25
}
... (more columns)
```

---

## Table: `smarttrack_staging.hawb_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| hawb | varchar | - | - |  |
| status | varchar | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.helpdesk_ticket`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| ticket_code | varchar | - | - |  |
| department_id | int | - | - |  |
| priority | enum | - | - |  |
| subject | varchar | - | - |  |
| status | enum | - | - |  |
| addedby | int | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.helpdesk_ticket_message`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| ticketid | bigint | - | - |  |
| message | varchar | - | - |  |
| attachment | varchar | - | - |  |
| addedby | int | - | - |  |
| added_date | datetime | - | - |  |
| updatedby | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.hermes_datafile_id`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.hermes_postcode_record`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| fullpostcode | varchar | - | - |  |
| pos_pcd_postcode_excluded_indicator | char | - | - |  |
| sort_level_key | varchar | - | - |  |
| next_day_service | char | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.imcp`
- **Record Count:** 1853
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| opcode | varchar | - | - |  |
| imcpcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "opcode": "AEA",
    "imcpcode": "AEAUHA"
}
... (more columns)
```

---

## Table: `smarttrack_staging.import_csv_consignment_temp`
- **Record Count:** 21
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| date_added | date | - | - |  |
| shipper_country_iso | varchar | - | - |  |
| receiver_country_iso | varchar | - | - |  |
| service_code | varchar | - | - |  |
| order_reference | varchar | - | - |  |
| shipper_company | varchar | - | - |  |
| shipper_contact | varchar | - | - |  |
| shipper_email | varchar | - | - |  |
| shipper_telephone | varchar | - | - |  |
| shipper_address_line_1 | varchar | - | - |  |
| shipper_address_line_2 | varchar | - | - |  |
| shipper_address_line_3 | varchar | - | - |  |
| shipper_city | varchar | - | - |  |
| shipper_state | varchar | - | - |  |
| shipper_postcode | varchar | - | - |  |
| receiver_company | varchar | - | - |  |
| receiver_contact | varchar | - | - |  |
| receiver_email | varchar | - | - |  |
| receiver_telephone | varchar | - | - |  |
| receiver_address_line_1 | varchar | - | - |  |
| receiver_address_line_2 | varchar | - | - |  |
| receiver_address_line_3 | varchar | - | - |  |
| receiver_city | varchar | - | - |  |
| receiver_state | varchar | - | - |  |
| receiver_postcode | varchar | - | - |  |
| reference | varchar | - | - |  |
| items_value | decimal | - | - |  |
| items_currency | varchar | - | - |  |
| item_type | varchar | - | - |  |
| note | text | - | - |  |
| description | text | - | - |  |
| bag_number | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| mawb_number | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| status | enum | - | - |  |
| is_complete | enum | - | - |  |
| batch_number | varchar | - | - |  |
| user_id | bigint | - | - |  |
| message | text | - | - |  |
| weight | text | - | - |  |
| length | text | - | - |  |
| height | text | - | - |  |
| width | text | - | - |  |
| itemvalue | text | - | - |  |
| parcel_item_desc | varchar | - | - |  |
| parcel_item_sku | varchar | - | - |  |
| parcel_item_url | varchar | - | - |  |
| parcel_item_quantity | int | - | - |  |
| parcel_item_value | decimal | - | - |  |
| parcel_item_weight | decimal | - | - |  |
| parcel_item_hs_code | varchar | - | - |  |
| parcel_item_manufacture_country | varchar | - | - |  |
| eori_number | varchar | - | - |  |
| vat_number | varchar | - | - |  |
| ioss_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 227,
    "date_added": "2024-03-03",
    "shipper_country_iso": "GB",
    "receiver_country_iso": "GB",
    "service_code": "AMZ002"
}
... (more columns)
```

---

## Table: `smarttrack_staging.import_csv_tmp`
- **Record Count:** 125
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| account | varchar | - | - |  |
| hawb | varchar | - | - |  |
| service | varchar | - | - |  |
| service_code | varchar | - | - |  |
| reference | varchar | - | - |  |
| date_submitted | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line1 | varchar | - | - |  |
| address_line2 | varchar | - | - |  |
| address_line3 | varchar | - | - |  |
| city | varchar | - | - |  |
| country | varchar | - | - |  |
| post_code | varchar | - | - |  |
| telephone | varchar | - | - |  |
| number_of_pieces | varchar | - | - |  |
| weight | varchar | - | - |  |
| description | varchar | - | - |  |
| value | varchar | - | - |  |
| currency | varchar | - | - |  |
| notes | varchar | - | - |  |
| routing_non_routing | varchar | - | - |  |
| full_pallet | varchar | - | - |  |
| half_pallet | varchar | - | - |  |
| quarter_pallet | varchar | - | - |  |
| all_weight | varchar | - | - |  |
| width | varchar | - | - |  |
| heigh | varchar | - | - |  |
| length | varchar | - | - |  |
| item_type | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| email | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| bag_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| is_complete | tinyint | - | - |  |
| status | tinyint | - | - |  |
| message | text | - | - |  |
| batch_number | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 113479,
    "account": "cltst",
    "hawb": "",
    "service": "",
    "service_code": "3HPA"
}
... (more columns)
```

---

## Table: `smarttrack_staging.international`
- **Record Count:** 54
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| iata_country_code | char | - | - |  |
| zipcode_from | varchar | - | - |  |
| zipcode_to | varchar | - | - |  |
| air_express_depot | varchar | - | - |  |
| air_express_osort | varchar | - | - |  |
| air_express_dsort | varchar | - | - |  |
| dpd_classic_deport | varchar | - | - |  |
| dpd_classic_osort | varchar | - | - |  |
| dpd_classic_dsort | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1075616,
    "iata_country_code": "SK",
    "zipcode_from": "99080",
    "zipcode_to": "99080",
    "air_express_depot": "1503-NSK"
}
... (more columns)
```

---

## Table: `smarttrack_staging.invoice_bank_details`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| account_title | varchar | - | - |  |
| account_sortcode | varchar | - | - |  |
| account_number | int | - | - |  |
| account_iban | varchar | - | - |  |
| bank_name | varchar | - | - |  |
| bank_branch | varchar | - | - |  |
| bank_address | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| status | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "user_account_id": 148,
    "account_title": "testing here",
    "account_sortcode": "2",
    "account_number": 3
}
... (more columns)
```

---

## Table: `smarttrack_staging.invoice_detail`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| charges_detail | text | - | - |  |
| total | int | - | - |  |
| vat | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.invoice_detail_backup`
- **Record Count:** 138
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| hawb | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| remote_area_charge | decimal | - | - |  |
| on_farword_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| extra | decimal | - | - |  |
| hv | decimal | - | - |  |
| amount | decimal | - | - |  |
| agent_basic_charges | decimal | - | - |  |
| agent_fuel_charges | decimal | - | - |  |
| agent_additional_charges | decimal | - | - |  |
| agent_remote_area_charge | decimal | - | - |  |
| agent_on_farword_charges | decimal | - | - |  |
| agent_ndx | decimal | - | - |  |
| agent_ddp | decimal | - | - |  |
| agent_extra | decimal | - | - |  |
| agent_amount | decimal | - | - |  |
| agent_linehaul_cost | decimal | - | - |  |
| agent_handling_charges | decimal | - | - |  |
| reference | varchar | - | - |  |
| quotation_id | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1939667,
    "consignment_id": 10971723,
    "invoice_no": "",
    "hawb": "IT14694510012369",
    "basic_charges": "6.14"
}
... (more columns)
```

---

## Table: `smarttrack_staging.invoice_detail_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.invoice_extra_charges`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| invoice_detail_id | int | - | - |  |
| charge_type_id | int | - | - |  |
| agent_id | int | - | - |  |
| cost_type | enum | - | - |  |
| cost | decimal | - | - |  |
| description | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.invoice_extra_charges_types`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| title | varchar | - | - |  |
| isactive | tinyint | - | - |  |
| added_by | int | - | - |  |
| added_date | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.invoice_templates`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| image | varchar | - | - |  |
| invoice_function | varchar | - | - |  |
| summary_invoice_function | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Default Template",
    "image": "default_template.png",
    "invoice_function": "SavePDFFile",
    "summary_invoice_function": "SaveSummaryInvoicePDFFile"
}
... (more columns)
```

---

## Table: `smarttrack_staging.invoices`
- **Record Count:** 110
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| user_account_id | int | - | - |  |
| net_amount | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| vatable_amount | decimal | - | - |  |
| vat | decimal | - | - |  |
| total_amount | decimal | - | - |  |
| weight | decimal | - | - |  |
| currency_id | int | - | - |  |
| exchange_rate | decimal | - | - |  |
| credit_type | enum | - | - |  |
| credit_amount | decimal | - | - |  |
| invoice_date | datetime | - | - |  |
| summary_pdf | varchar | - | - |  |
| pdf | varchar | - | - |  |
| csv | varchar | - | - |  |
| invoice_by | int | - | - |  |
| invoice_type | enum | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| is_email | bit | - | - |  |
| date_deleted | timestamp | - | - |  |
| is_paid | bit | - | - |  |
| is_cancel | bit | - | - |  |
| is_read | bit | - | - |  |
| paid_date | datetime | - | - |  |
| salepot_id | int | - | - |  |
| added_by | int | - | - |  |
| date_created | timestamp | - | - |  |
| date_updated | timestamp | - | - |  |
| invoice_heading | text | - | - |  |
| attached_files | text | - | - |  |
| invoice_reference | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 31820,
    "invoice_no": "INV1028",
    "user_account_id": 2294,
    "net_amount": "0.30",
    "fuel_charges": "0.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.invoices_manual`
- **Record Count:** 193
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| invoice_no | varchar | - | - |  |
| account_number | varchar | - | - |  |
| invoice_heading | varchar | - | - |  |
| invoice_amount | varchar | - | - |  |
| invoice_weight | varchar | - | - |  |
| vat_amount | varchar | - | - |  |
| invoice_total_amount | varchar | - | - |  |
| currency | varchar | - | - |  |
| exchange_rate | varchar | - | - |  |
| invoice_status | varchar | - | - |  |
| added_by | varchar | - | - |  |
| invoice_file | varchar | - | - |  |
| is_active | enum | - | - |  |
| is_deleted | enum | - | - |  |
| invoice_date | timestamp | - | - |  |
| date_created | timestamp | - | - |  |
| date_updated | timestamp | - | - |  |
| date_deleted | timestamp | - | - |  |
| is_email | enum | - | - |  |
| is_paid | enum | - | - |  |
| paid_date | timestamp | - | - |  |
| salepot_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 57146,
    "invoice_no": "57146",
    "account_number": "YANWEN",
    "invoice_heading": "MAWB-125-31005660",
    "invoice_amount": "832.34"
}
... (more columns)
```

---

## Table: `smarttrack_staging.invoices_manual_details`
- **Record Count:** 13
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| invoice_id | varchar | - | - |  |
| hawb | varchar | - | - |  |
| service_id | int | - | - |  |
| date_booked | datetime | - | - |  |
| reference | varchar | - | - |  |
| weight | varchar | - | - |  |
| amount | varchar | - | - |  |
| description | varchar | - | - |  |
| destination | varchar | - | - |  |
| vat_amount | decimal | - | - |  |
| is_vat | enum | - | - |  |
| created_by | int | - | - |  |
| date_created | datetime | - | - |  |
| updated_by | int | - | - |  |
| date_updated | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1164,
    "invoice_id": "31845",
    "hawb": "",
    "service_id": null,
    "date_booked": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.invoices_number_range`
- **Record Count:** 57
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| is_default | tinyint | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| user_account_id | int | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| range_type | enum | - | - |  |
| date_created | timestamp | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 171,
    "is_default": 1,
    "range_start": 1000,
    "range_end": 10000000,
    "next_number": 1235
}
... (more columns)
```

---

## Table: `smarttrack_staging.item_details`
- **Record Count:** 276
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| session_id | varchar | - | - |  |
| parcel_count | int | - | - |  |
| item_detail | text | - | - |  |
| user_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 12,
    "consignment_id": 170278,
    "session_id": "",
    "parcel_count": 0,
    "item_detail": "[{\"item_description\":\"test\",\"item_url\":\"\",\"item_sku\":\"3434\",\"no_of_items\":\"1\",\"item_value\":\"1\",\"weight\":\"1\",\"tariff_no\":\"\",\"hscode\":\"13232\",\"manufacture_country_iso\":\"AU\"}]"
}
... (more columns)
```

---

## Table: `smarttrack_staging.label_file`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| account_number | varchar | - | - |  |
| hawb_list | text | - | - |  |
| created_date | datetime | - | - |  |
| error_list | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.language`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| language | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| created_by | int | - | - |  |
| is_active | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "language": "en-GB",
    "date_created": "2016-07-12 00:00:00",
    "created_by": null,
    "is_active": "Y"
}
... (more columns)
```

---

## Table: `smarttrack_staging.language_keys`
- **Record Count:** 14
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| keyword | varchar | - | - |  |
| language | varchar | - | - |  |
| caption | text | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| date_updated | timestamp | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3962,
    "keyword": "NAV_REPORT_SETTINGS",
    "language": "en-US",
    "caption": "Report Settings",
    "date_created": "2018-11-14 20:10:00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.licence_plate`
- **Record Count:** 83
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| range_name | varchar | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| delivery_network | varchar | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| date_created | datetime | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |
| country_range | bit | - | - |  |
| country_list | text | - | - |  |
| range_reminder_limit | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 126,
    "range_name": "Yodel_1519406498",
    "range_start": 1,
    "range_end": 100000000000076,
    "next_number": 16
}
... (more columns)
```

---

## Table: `smarttrack_staging.licence_plate_country`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| licence_plate_id | int | - | - |  |
| country_id | int | - | - |  |
| range_name | varchar | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| prefix | varchar | - | - |  |
| sufix | varchar | - | - |  |
| date_created | datetime | - | - |  |
| date_updated | datetime | - | - |  |
| addedby | int | - | - |  |
| updatedby | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "licence_plate_id": 112,
    "country_id": 13,
    "range_name": "Sweden Post Australia",
    "range_start": 91713500
}
... (more columns)
```

---

## Table: `smarttrack_staging.location`
- **Record Count:** 15
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| date_updated | varchar | - | - |  |
| updatedby | timestamp | - | - |  |
| active | varchar | - | - |  |
| type | varchar | - | - |  |
| warehouseid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Stock System Racking",
    "date_created": "0000-00-00 00:00:00",
    "createdby": 189,
    "date_updated": "2016-06-13 9:21:01"
}
... (more columns)
```

---

## Table: `smarttrack_staging.log_rack_shelf`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| rack_shelf_id | bigint | - | - |  |
| rack_shelf_item_id | bigint | - | - |  |
| customer_id | int | - | - |  |
| remarks | text | - | - |  |
| in_date | datetime | - | - |  |
| in_by | int | - | - |  |
| out_date | datetime | - | - |  |
| out_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.login_request`
- **Record Count:** 37
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | int | - | - |  |
| user_name | varchar | - | - |  |
| ip_address | varchar | - | - |  |
| user_agent | text | - | - |  |
| login_status | tinyint | - | - |  |
| login_time | timestamp | - | - |  |
| logout_time | timestamp | - | - |  |
| session_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 15364,
    "user_id": 0,
    "user_name": "nutoncarrilu1981@gmail.com",
    "ip_address": "196.3.97.69",
    "user_agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/120.0.0.0 Safari\/537.36"
}
... (more columns)
```

---

## Table: `smarttrack_staging.manifest`
- **Record Count:** 9
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_id | int | - | - |  |
| file_name | varchar | - | - |  |
| label_link | varchar | - | - |  |
| date_created | datetime | - | - |  |
| pieces | varchar | - | - |  |
| agent_id | bigint | - | - |  |
| weight | decimal | - | - |  |
| service_id | int | - | - |  |
| handling | varchar | - | - |  |
| pdf_file | varchar | - | - |  |
| flight_number | varchar | - | - |  |
| mawb | varchar | - | - |  |
| type | varchar | - | - |  |
| collection_comment | text | - | - |  |
| collection_date | datetime | - | - |  |
| collection_date_to | datetime | - | - |  |
| pickup_date | datetime | - | - |  |
| delivery_note | text | - | - |  |
| signature | varchar | - | - |  |
| pickup_id | int | - | - |  |
| route_warehouse_id | int | - | - |  |
| routing_email_date | datetime | - | - |  |
| date_received | datetime | - | - |  |
| received_by | varchar | - | - |  |
| name_of_driver | varchar | - | - |  |
| licence_number | varchar | - | - |  |
| account_owner | varchar | - | - |  |
| number_bag | varchar | - | - |  |
| product | varchar | - | - |  |
| carrier_note | varchar | - | - |  |
| carrier_pdf | varchar | - | - |  |
| carrier_id | int | - | - |  |
| is_deleted | enum | - | - |  |
| is_dispatched | enum | - | - |  |
| is_send_email | enum | - | - |  |
| manifest_by | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 809,
    "user_id": 58,
    "file_name": "2020_04_21\/148\/1587486963.csv",
    "label_link": "",
    "date_created": "2020-04-21 18:36:02"
}
... (more columns)
```

---

## Table: `smarttrack_staging.manifest_consignment_mapping`
- **Record Count:** 0
- **Inferred Module:** Consignments & Operations
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| manifestid | int | - | - |  |
| consignmentid | int | - | - |  |
| export_mawb | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.manifest_entity_mapping`
- **Record Count:** 345
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| manifest_id | int | - | - |  |
| manifest_entity_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "entity_id": 81539,
    "manifest_id": 809,
    "manifest_entity_type": "p"
}
... (more columns)
```

---

## Table: `smarttrack_staging.manifest_service_mapping`
- **Record Count:** 20
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| manifest_id | bigint | - | - |  |
| service_id | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 42,
    "manifest_id": 809,
    "service_id": 2
}
... (more columns)
```

---

## Table: `smarttrack_staging.market_place_documentation_mapping`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| marketplace_id | int | - | - |  |
| step_title | varchar | - | - |  |
| step_description | text | - | - |  |
| step_image | varchar | - | - |  |
| step_order | int | - | - |  |
| created_at | timestamp | - | - |  |
| updated_at | timestamp | - | - |  |
| added_by | int | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.market_places`
- **Record Count:** 81
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| title | varchar | - | - |  |
| description | varchar | - | - |  |
| translation_key | varchar | - | - |  |
| is_active | tinyint | - | - |  |
| page_link | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |
| is_delete | tinyint | - | - |  |
| integration_logo | varchar | - | - |  |
| manual_link | varchar | - | - |  |
| plugin_key | varchar | - | - |  |
| parent_id | int | - | - |  |
| integration_type | int | - | - |  |
| channel_type | int | - | - |  |
| country_id | int | - | - |  |
| last_sync | datetime | - | - |  |
| is_featured | bit | - | - |  |
| is_api2cart | bit | - | - |  |
| help_doc | varchar | - | - |  |
| class_name | varchar | - | - |  |
| documentation_title | varchar | - | - |  |
| documentation_cover_image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Amazon",
    "description": "Amazon",
    "translation_key": "AMAZON",
    "is_active": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.market_places_authenticate_field`
- **Record Count:** 187
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| field_name | varchar | - | - |  |
| field_value | varchar | - | - |  |
| market_places_id | bigint | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| is_delete | tinyint | - | - |  |
| auto_generate_value | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 43,
    "field_name": "Amazon Access Key ",
    "field_value": "AWS_ACCESS_KEY_ID",
    "market_places_id": 1,
    "added_by": 188
}
... (more columns)
```

---

## Table: `smarttrack_staging.marketplace_order`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| marketplace_id | bigint | - | - |  |
| marketplace_order_number | varchar | - | - |  |
| create_time | datetime | - | - |  |
| order_status | varchar | - | - |  |
| receiver_name | varchar | - | - |  |
| receiver_phone | varchar | - | - |  |
| receiver_state | varchar | - | - |  |
| receiver_city | varchar | - | - |  |
| receiver_country_id | bigint | - | - |  |
| receiver_addressline1 | varchar | - | - |  |
| receiver_addressline2 | varchar | - | - |  |
| receiver_postcode | varchar | - | - |  |
| payment_method | varchar | - | - |  |
| order_total | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| receiver_email | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| Ack | varchar | - | - |  |
| error_code | varchar | - | - |  |
| error_message | varchar | - | - |  |
| consignment_id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| shipped_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "marketplace_id": 88,
    "marketplace_order_number": "304",
    "create_time": "2020-03-26 18:33:20",
    "order_status": "Unshipped"
}
... (more columns)
```

---

## Table: `smarttrack_staging.marketplace_order_details`
- **Record Count:** 24
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| marketplace_order_id | bigint | - | - |  |
| marketplace_item_id | varchar | - | - |  |
| sku | varchar | - | - |  |
| title | varchar | - | - |  |
| quantity_purchased | varchar | - | - |  |
| asin | varchar | - | - |  |
| item_price | varchar | - | - |  |
| currency | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "marketplace_order_id": 1,
    "marketplace_item_id": "1",
    "sku": "",
    "title": "Ship Your Idea - Blue"
}
... (more columns)
```

---

## Table: `smarttrack_staging.mawb`
- **Record Count:** 106
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| mawb_number | varchar | - | - |  |
| mawb_source_country_id | int | - | - |  |
| mawb_source_warehouse_id | int | - | - |  |
| mawb_destination_country_id | int | - | - |  |
| mawb_destination_warehouse_id | int | - | - |  |
| is_active | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |
| mawb_status | enum | - | - |  |
| manifest_label | varchar | - | - |  |
| mawb_lv_manifest | varchar | - | - |  |
| mawb_hv_manifest | varchar | - | - |  |
| bagging_type | enum | - | - |  |
| bag_is_hv_lv | enum | - | - |  |
| mawb_class | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "mawb_number": "154-21271503",
    "mawb_source_country_id": 225,
    "mawb_source_warehouse_id": 36,
    "mawb_destination_country_id": 226
}
... (more columns)
```

---

## Table: `smarttrack_staging.mawb_flight_document`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| document_name | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "document_name": "ExportSAManifest"
}
... (more columns)
```

---

## Table: `smarttrack_staging.mawb_flight_document_mapping`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| country_id | bigint | - | - |  |
| document_id | bigint | - | - |  |
| template_id | bigint | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 197,
    "document_id": 1,
    "template_id": 1,
    "added_by": 148
}
... (more columns)
```

---

## Table: `smarttrack_staging.mawb_parcel_mapping`
- **Record Count:** 15
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb_id | int | - | - |  |
| parcel_id | int | - | - |  |
| wharehouse_id | int | - | - |  |
| bag_id | int | - | - |  |
| date_added | datetime | - | - |  |
| added_by | bigint | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3670,
    "mawb_id": 85,
    "parcel_id": 228079,
    "wharehouse_id": 9,
    "bag_id": 10356
}
... (more columns)
```

---

## Table: `smarttrack_staging.not_found_record`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb | varchar | - | - |  |
| bag_number | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| scanned_by | bigint | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| weight | decimal | - | - |  |
| date_created | datetime | - | - |  |
| reason | varchar | - | - |  |
| image | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.oauth_access_tokens`
- **Record Count:** 12
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| access_token | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "access_token": "fe73060e430030efe5ea3d8496b1b3c4d6c46c52",
    "client_id": "developer",
    "user_id": 58,
    "expires": "2019-11-18 20:08:16",
    "scope": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.oauth_authorization_codes`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| authorization_code | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| redirect_uri | varchar | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.oauth_clients`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| client_secret | varchar | - | - |  |
| redirect_uri | varchar | - | - |  |
| grant_types | varchar | - | - |  |
| scope | varchar | - | - |  |
| user_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.oauth_jwt`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| subject | varchar | - | - |  |
| public_key | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.oauth_public_keys`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| client_id | varchar | - | - |  |
| public_key | varchar | - | - |  |
| private_key | varchar | - | - |  |
| encryption_algorithm | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.oauth_refresh_tokens`
- **Record Count:** 10
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| refresh_token | varchar | - | - |  |
| client_id | varchar | - | - |  |
| user_id | int | - | - |  |
| expires | timestamp | - | - |  |
| scope | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "refresh_token": "fe9944ab1578dd1529d1eef7af4a90728bddf291",
    "client_id": "developer",
    "user_id": 58,
    "expires": "2020-04-06 23:16:13",
    "scope": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.oauth_scopes`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| scope | varchar | - | - |  |
| is_default | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.ops_summary`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account | varchar | - | - |  |
| services | varchar | - | - |  |
| country | varchar | - | - |  |
| quantity | varchar | - | - |  |
| weight | varchar | - | - |  |
| carrier | varchar | - | - |  |
| date_submitted | datetime | - | - |  |
| reference | varchar | - | - |  |
| mawb | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2504,
    "account": "",
    "services": "",
    "country": "Austria",
    "quantity": "9"
}
... (more columns)
```

---

## Table: `smarttrack_staging.optimus_file_name`
- **Record Count:** 14
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 40732,
    "file_name": "ESPN00040695",
    "sent_date": "2016-09-11 13:30:04",
    "file_id": "40695"
}
... (more columns)
```

---

## Table: `smarttrack_staging.owe_southafrica_postcode`
- **Record Count:** 13
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| zone | varchar | - | - |  |
| postcode | varchar | - | - |  |
| main_outlying | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5852,
    "zone": "ELS",
    "postcode": "9996",
    "main_outlying": "O"
}
... (more columns)
```

---

## Table: `smarttrack_staging.owe_southafrica_routine`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| state | varchar | - | - |  |
| zone | varchar | - | - |  |
| route | varchar | - | - |  |
| delivery_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "state": "Aberdeen",
    "zone": "PLZ2",
    "route": "RD4",
    "delivery_time": 3
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet`
- **Record Count:** 19
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| palletno | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| close | tinyint | - | - |  |
| userid | int | - | - |  |
| pallet_carrier_id | int | - | - |  |
| date_dispatch | timestamp | - | - |  |
| dispatch_userid | int | - | - |  |
| type | varchar | - | - |  |
| manifestid | int | - | - |  |
| hub | varchar | - | - |  |
| is_active | tinyint | - | - |  |
| comments | varchar | - | - |  |
| label | varchar | - | - |  |
| pallet_source_country_id | int | - | - |  |
| pallet_source_warehouse_id | int | - | - |  |
| pallet_destination_country_id | int | - | - |  |
| pallet_destination_warehouse_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3922,
    "palletno": "",
    "date_created": "2018-11-13 22:31:45",
    "close": 0,
    "userid": 2128
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet_bag_mapping`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| palletid | int | - | - |  |
| bagid | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.pallet_bag_remove_reason`
- **Record Count:** 1
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pallet_id | int | - | - |  |
| bag_id | int | - | - |  |
| reason | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pallet_id": 3934,
    "bag_id": 10263,
    "reason": "Testing"
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet_carier_group`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| group_name | varchar | - | - |  |
| carrier_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "group_name": "Quality Assurance",
    "carrier_id": 194
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet_carrier`
- **Record Count:** 3
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| service_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "ROYAL MAIL TRACKED 48",
    "service_id": "12"
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet_carrier_service`
- **Record Count:** 11
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_group_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "carrier_group_id": 2,
    "service_id": 261
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet_entity_mapping`
- **Record Count:** 33
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pallet_id | int | - | - |  |
| entity_id | int | - | - |  |
| pallet_entity_type | enum | - | - |  |
| pre_sort | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pallet_id": 3928,
    "entity_id": 10091,
    "pallet_entity_type": "b",
    "pre_sort": "n"
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet_location`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| locationid | int | - | - |  |
| palletid | int | - | - |  |
| consignmentid | int | - | - |  |
| date_created | timestamp | - | - |  |
| createdby | int | - | - |  |
| comments | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 110892,
    "locationid": 3,
    "palletid": 0,
    "consignmentid": 11888192,
    "date_created": "2016-09-11 18:07:18"
}
... (more columns)
```

---

## Table: `smarttrack_staging.pallet_name`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| serviceid | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.parcel`
- **Record Count:** 36
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| do_tracking_number | varchar | - | - |  |
| length | decimal | - | - |  |
| width | decimal | - | - |  |
| height | decimal | - | - |  |
| weight | decimal | - | - |  |
| description | text | - | - |  |
| parcel_message | text | - | - |  |
| qty | varchar | - | - |  |
| commoditycode | varchar | - | - |  |
| hscode | varchar | - | - |  |
| grossweight | decimal | - | - |  |
| pweight | varchar | - | - |  |
| itemvalue | varchar | - | - |  |
| number_item | int | - | - |  |
| tarrif_no | varchar | - | - |  |
| update_weight | decimal | - | - |  |
| owe_status_code | varchar | - | - |  |
| chute_sorted | int | - | - |  |
| parcel_status_code | int | - | - |  |
| routing_code | varchar | - | - |  |
| last_tracking_update | datetime | - | - |  |
| parcel_item_desc | text | - | - |  |
| parcel_label | varchar | - | - |  |
| itemsku | varchar | - | - |  |
| itemurl | varchar | - | - |  |
| sort_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 232016,
    "consignment_id": 170432,
    "tracking_number": "A12290298493",
    "do_tracking_number": "",
    "length": "2.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.parcel_bagging_mapping`
- **Record Count:** 37
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| parcel_id | int | - | - |  |
| bag_id | int | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4031,
    "parcel_id": 228641,
    "bag_id": 10331,
    "added_by": 2328,
    "added_date": "2020-03-19 16:53:06"
}
... (more columns)
```

---

## Table: `smarttrack_staging.parcel_iteam`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| parcel_id | int | - | - |  |
| iteam_name | varchar | - | - |  |
| iteam_weight | decimal | - | - |  |
| iteam_weight_unit | enum | - | - |  |
| iteam_value | int | - | - |  |
| iteam_quantity | int | - | - |  |
| iteam_country_id | int | - | - |  |
| iteam_description | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.parcel_log`
- **Record Count:** 1
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 0,
    "userid": 58,
    "logdate": "2019-07-12 19:45:27",
    "ipaddress": "3937179205",
    "log_id": 139002
}
... (more columns)
```

---

## Table: `smarttrack_staging.parcelforce_datafile_id`
- **Record Count:** 26
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| file_name_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1070,
    "file_name": "PGO",
    "sent_date": "2016-04-22 10:33:44",
    "file_name_id": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.parcelforce_depo_detail`
- **Record Count:** 17
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_name | varchar | - | - |  |
| depo_short_name | varchar | - | - |  |
| postcode | varchar | - | - |  |
| route_number | varchar | - | - |  |
| pfw_ect | varchar | - | - |  |
| pfw_lat | varchar | - | - |  |
| pfw_lct | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11170,
    "depo_name": "Worcester (Satellite) Depot",
    "depo_short_name": "WORC",
    "postcode": "WR11",
    "route_number": "R000"
}
... (more columns)
```

---

## Table: `smarttrack_staging.parcelforce_hub_details`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_name | varchar | - | - |  |
| depo_number | varchar | - | - |  |
| mon_hub_24 | varchar | - | - |  |
| mon_chute_24 | varchar | - | - |  |
| mon_hub_48 | varchar | - | - |  |
| mon_chute_48 | varchar | - | - |  |
| tue_hub_24 | varchar | - | - |  |
| tue_chute_24 | varchar | - | - |  |
| tue_hub_48 | varchar | - | - |  |
| tue_chute_48 | varchar | - | - |  |
| wed_hub_24 | varchar | - | - |  |
| wed_chute_24 | varchar | - | - |  |
| wed_hub_48 | varchar | - | - |  |
| wed_chute_48 | varchar | - | - |  |
| thu_hub_24 | varchar | - | - |  |
| thu_chute_24 | varchar | - | - |  |
| thu_hub_48 | varchar | - | - |  |
| thu_chute_48 | varchar | - | - |  |
| fri_hub_24 | varchar | - | - |  |
| fri_chute_24 | varchar | - | - |  |
| fri_hub_48 | varchar | - | - |  |
| fri_chute_48 | varchar | - | - |  |
| sat_hub_24 | varchar | - | - |  |
| sat_chute_24 | varchar | - | - |  |
| sat_hub_48 | varchar | - | - |  |
| sat_chute_48 | varchar | - | - |  |
| sun_hub_24 | varchar | - | - |  |
| sun_chute_24 | varchar | - | - |  |
| sun_hub_48 | varchar | - | - |  |
| sun_chute_48 | varchar | - | - |  |
| sat_delivery_hub | varchar | - | - |  |
| sat_delivery_chute | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 234,
    "depo_name": "Newcastle Depot",
    "depo_number": "NC20",
    "mon_hub_24": "NW",
    "mon_chute_24": ""
}
... (more columns)
```

---

## Table: `smarttrack_staging.parcelforu_pickup_point`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| company | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| statuscode | varchar | - | - |  |
| status_description | varchar | - | - |  |
| latitude | varchar | - | - |  |
| longitude | varchar | - | - |  |
| mon | varchar | - | - |  |
| tue | varchar | - | - |  |
| wed | varchar | - | - |  |
| thu | varchar | - | - |  |
| fri | varchar | - | - |  |
| sat | varchar | - | - |  |
| sun | varchar | - | - |  |
| label_routing | varchar | - | - |  |
| branch_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.partnerservicesrouting`
- **Record Count:** 128
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | int | - | - |  |
| product_id | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 225,
    "from_weight": "0.00",
    "to_weight": "0.25",
    "status": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.payment_gateways`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_id | bigint | - | - |  |
| gateway_type | enum | - | - |  |
| email | varchar | - | - |  |
| currency_id | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.payments_history`
- **Record Count:** 46
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | bigint | - | - |  |
| paypal_payment_id | varchar | - | - |  |
| txn_id | varchar | - | - |  |
| billing_id | varchar | - | - |  |
| sender_email | varchar | - | - |  |
| amount | double | - | - |  |
| amount_currency_id | int | - | - |  |
| payment_method | enum | - | - |  |
| payment_detail | varchar | - | - |  |
| user_currency_id | int | - | - |  |
| debit | decimal | - | - |  |
| credit | decimal | - | - |  |
| module_name | varchar | - | - |  |
| module_id | varchar | - | - |  |
| invoice_id | int | - | - |  |
| payment_status | varchar | - | - |  |
| is_completed | enum | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 19439,
    "account_id": 2327,
    "paypal_payment_id": null,
    "txn_id": null,
    "billing_id": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.pbt_datafile_id`
- **Record Count:** 13
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "file_name": "AVOWE00001",
    "sent_date": "2016-05-09 09:36:10"
}
... (more columns)
```

---

## Table: `smarttrack_staging.pbt_routine`
- **Record Count:** 37
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_code | varchar | - | - |  |
| courier_file_label_code | varchar | - | - |  |
| transport_label_code | varchar | - | - |  |
| courier_charges_code | varchar | - | - |  |
| area_desc | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2151,
    "file_code": "7299",
    "courier_file_label_code": "DUD",
    "transport_label_code": "DUD",
    "courier_charges_code": "C"
}
... (more columns)
```

---

## Table: `smarttrack_staging.permissions`
- **Record Count:** 406
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| lang_key | varchar | - | - |  |
| parent_id | int | - | - |  |
| file_name | varchar | - | - |  |
| description | varchar | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| query_string | varchar | - | - |  |
| icon | varchar | - | - |  |
| sort_order | int | - | - |  |
| is_menu_item | tinyint | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "lang_key": "LEFT_MENU_PERMISSIONS_&_ACTIONS",
    "parent_id": 0,
    "file_name": "#",
    "description": "Manage Permissions"
}
... (more columns)
```

---

## Table: `smarttrack_staging.permissions_log`
- **Record Count:** 216
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-09-05 15:38:29",
    "ipaddress": "657980247",
    "log_id": 350
}
... (more columns)
```

---

## Table: `smarttrack_staging.pickup`
- **Record Count:** 10
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pickup_number | varchar | - | - |  |
| pickup_date | timestamp | - | - |  |
| delivery_note | varchar | - | - |  |
| pick_up_pdf | varchar | - | - |  |
| collection_pdf | varchar | - | - |  |
| collection_address | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pickup_number": "",
    "pickup_date": "2016-08-05 19:55:00",
    "delivery_note": "Picked up by kazim",
    "pick_up_pdf": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.pmp_routine`
- **Record Count:** 3
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| storeid | int | - | - |  |
| store_name | varchar | - | - |  |
| is_active | bit | - | - |  |
| country | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| telephone | varchar | - | - |  |
| depot_no | int | - | - |  |
| depot_description | varchar | - | - |  |
| round1 | int | - | - |  |
| drop1 | int | - | - |  |
| round2 | int | - | - |  |
| drop2 | int | - | - |  |
| date_created | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3053,
    "storeid": 241921,
    "store_name": "Pass My Parcel",
    "is_active": 1,
    "country": "GB"
}
... (more columns)
```

---

## Table: `smarttrack_staging.post_italia_routing`
- **Record Count:** 28
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| zip_code | varchar | - | - |  |
| routing_file | varchar | - | - |  |
| province | varchar | - | - |  |
| province_iso_code | varchar | - | - |  |
| sortation_name | varchar | - | - |  |
| sortation_id | varchar | - | - |  |
| sortation_name_on_bag | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 8823,
    "zip_code": "89024",
    "routing_file": "93-D52-POLISTEN-x21",
    "province": "Reggio Calabria",
    "province_iso_code": "RC"
}
... (more columns)
```

---

## Table: `smarttrack_staging.postcode_user_service_charges`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| from_postcode | varchar | - | - |  |
| to_postcode | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| city_name | varchar | - | - |  |
| country_iso | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.postitalia_untracked`
- **Record Count:** 13
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| postcode | int | - | - |  |
| region | varchar | - | - |  |
| provenience | varchar | - | - |  |
| sortation | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4410,
    "postcode": 31024,
    "region": "VENETO",
    "provenience": "TV",
    "sortation": "10 - Padova"
}
... (more columns)
```

---

## Table: `smarttrack_staging.postnl_datafile_id`
- **Record Count:** 37
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| file_name | varchar | - | - |  |
| sent_date | datetime | - | - |  |
| service_country | varchar | - | - |  |
| file_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4,
    "file_name": "VM000001",
    "sent_date": "2016-09-22 10:45:47",
    "service_country": "BE",
    "file_id": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.pre_alert`
- **Record Count:** 29
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| mawb_id | int | - | - |  |
| flight_number | varchar | - | - |  |
| pieces | varchar | - | - |  |
| weight | varchar | - | - |  |
| etd | varchar | - | - |  |
| eta | varchar | - | - |  |
| current_status | varchar | - | - |  |
| date_time | varchar | - | - |  |
| cleared | varchar | - | - |  |
| status | varchar | - | - |  |
| comments | varchar | - | - |  |
| account | varchar | - | - |  |
| files | text | - | - |  |
| uploadby | varchar | - | - |  |
| shed | varchar | - | - |  |
| date_entry | varchar | - | - |  |
| created_by | int | - | - |  |
| date_updated | varchar | - | - |  |
| updated_by | int | - | - |  |
| type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3683,
    "mawb_id": 7,
    "flight_number": "3",
    "pieces": "3",
    "weight": "3"
}
... (more columns)
```

---

## Table: `smarttrack_staging.pricing_bulk_data_1579539860`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| hawb | varchar | - | - |  |
| charges_reference | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| remote_area_charges | decimal | - | - |  |
| handling_charges | decimal | - | - |  |
| reference | decimal | - | - |  |
| linehaul | decimal | - | - |  |
| address_correction | decimal | - | - |  |
| airline_handling | decimal | - | - |  |
| clearance | decimal | - | - |  |
| collections | decimal | - | - |  |
| ddp_admin_fee | decimal | - | - |  |
| ddp_charge | decimal | - | - |  |
| delivery | decimal | - | - |  |
| dispatch | decimal | - | - |  |
| labour | decimal | - | - |  |
| other | decimal | - | - |  |
| out_of_gauge | decimal | - | - |  |
| over_weight | decimal | - | - |  |
| ras | decimal | - | - |  |
| redelivery | decimal | - | - |  |
| label_charges | decimal | - | - |  |
| vat | decimal | - | - |  |
| discount | decimal | - | - |  |
| mobile_tracking | decimal | - | - |  |
| user_account | varchar | - | - |  |
| status | tinyint | - | - |  |
| is_complete | tinyint | - | - |  |
| message | varchar | - | - |  |
| batch_number | varchar | - | - |  |
| currency | varchar | - | - |  |
| data_result | text | - | - |  |
| data_result_count | tinyint | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.pricing_bulk_data_1579539864`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| hawb | varchar | - | - |  |
| charges_reference | varchar | - | - |  |
| basic_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| additional_charges | decimal | - | - |  |
| ndx | decimal | - | - |  |
| ddp | decimal | - | - |  |
| remote_area_charges | decimal | - | - |  |
| handling_charges | decimal | - | - |  |
| reference | decimal | - | - |  |
| linehaul | decimal | - | - |  |
| address_correction | decimal | - | - |  |
| airline_handling | decimal | - | - |  |
| clearance | decimal | - | - |  |
| collections | decimal | - | - |  |
| ddp_admin_fee | decimal | - | - |  |
| ddp_charge | decimal | - | - |  |
| delivery | decimal | - | - |  |
| dispatch | decimal | - | - |  |
| labour | decimal | - | - |  |
| other | decimal | - | - |  |
| out_of_gauge | decimal | - | - |  |
| over_weight | decimal | - | - |  |
| ras | decimal | - | - |  |
| redelivery | decimal | - | - |  |
| label_charges | decimal | - | - |  |
| vat | decimal | - | - |  |
| discount | decimal | - | - |  |
| mobile_tracking | decimal | - | - |  |
| user_account | varchar | - | - |  |
| status | tinyint | - | - |  |
| is_complete | tinyint | - | - |  |
| message | varchar | - | - |  |
| batch_number | varchar | - | - |  |
| currency | varchar | - | - |  |
| data_result | text | - | - |  |
| data_result_count | tinyint | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.product_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.product_routine_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| message | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.products`
- **Record Count:** 2
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_id | int | - | - |  |
| product_name | varchar | - | - |  |
| insurance | decimal | - | - |  |
| description | varchar | - | - |  |
| status | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| logo | varchar | - | - |  |
| length | int | - | - |  |
| width | int | - | - |  |
| height | int | - | - |  |
| vol_weight | decimal | - | - |  |
| vol_denominator | int | - | - |  |
| is_untrack | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| fuel_charges | decimal | - | - |  |
| added_date | timestamp | - | - |  |
| added_by | int | - | - |  |
| transit_time | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "country_id": 225,
    "product_name": "YODEL_TEST_PRODUCT",
    "insurance": "10.00",
    "description": ""
}
... (more columns)
```

---

## Table: `smarttrack_staging.proforma_invoice_biiling`
- **Record Count:** 39
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| consignment_id | int | - | - |  |
| billing_company | varchar | - | - |  |
| billing_contact | varchar | - | - |  |
| billing_address_line_1 | varchar | - | - |  |
| billing_address_line_2 | varchar | - | - |  |
| billing_address_line_3 | varchar | - | - |  |
| billing_city | varchar | - | - |  |
| billing_country | varchar | - | - |  |
| billing_postcode | varchar | - | - |  |
| billing_telephone | varchar | - | - |  |
| payment_terms | varchar | - | - |  |
| export_type | varchar | - | - |  |
| comments | varchar | - | - |  |
| delivery_terms | varchar | - | - |  |
| link_file | varchar | - | - |  |
| payer_vat | varchar | - | - |  |
| harm_comm_code | varchar | - | - |  |
| export | varchar | - | - |  |
| invoice_type | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4717,
    "consignment_id": 58893,
    "billing_company": "",
    "billing_contact": "",
    "billing_address_line_1": ""
}
... (more columns)
```

---

## Table: `smarttrack_staging.quotation_details`
- **Record Count:** 58
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| shipping_from | bigint | - | - |  |
| shipping_to | bigint | - | - |  |
| carrier_id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| account_id | bigint | - | - |  |
| price_type | enum | - | - |  |
| pieces | int | - | - |  |
| currency_id | int | - | - |  |
| weight | decimal | - | - |  |
| dimensions | longtext | - | - |  |
| volumn_weight | decimal | - | - |  |
| basic_charge | decimal | - | - |  |
| vat_charge | decimal | - | - |  |
| extra_charge | decimal | - | - |  |
| sub_total | decimal | - | - |  |
| discount | decimal | - | - |  |
| user_email | varchar | - | - |  |
| discount_type | enum | - | - |  |
| total_charge | decimal | - | - |  |
| remark | text | - | - |  |
| status | enum | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| conversionrate | float | - | - |  |
| pdf | varchar | - | - |  |
| date_created | datetime | - | - |  |
| added_by | bigint | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 9,
    "shipping_from": 0,
    "shipping_to": 0,
    "carrier_id": null,
    "service_id": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.rack`
- **Record Count:** 17
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| warehouse_id | int | - | - |  |
| title | varchar | - | - |  |
| short_title | varchar | - | - |  |
| rack_rows | int | - | - |  |
| rack_cols | int | - | - |  |
| shelf_dimension | varchar | - | - |  |
| shelf_max_weight | decimal | - | - |  |
| is_york | tinyint | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11,
    "warehouse_id": 9,
    "title": "Rack 1",
    "short_title": "R1",
    "rack_rows": 5
}
... (more columns)
```

---

## Table: `smarttrack_staging.rack_shelf`
- **Record Count:** 15
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| rack_id | int | - | - |  |
| shelf_no | int | - | - |  |
| is_filled | tinyint | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 4946,
    "rack_id": 24,
    "shelf_no": 1243,
    "is_filled": 0,
    "updated_date": "2016-06-15 18:40:19"
}
... (more columns)
```

---

## Table: `smarttrack_staging.rack_shelf_item`
- **Record Count:** 16
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| goods_name | varchar | - | - |  |
| tracking_number | varchar | - | - |  |
| description | text | - | - |  |
| weight | decimal | - | - |  |
| dimension | varchar | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1599,
    "goods_name": "BABY TOYS",
    "tracking_number": "RE796582043SE",
    "description": "BABY TOYS",
    "weight": "0.76"
}
... (more columns)
```

---

## Table: `smarttrack_staging.ratebands`
- **Record Count:** 829
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| courier_service_id | int | - | - |  |
| name | varchar | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 270,
    "courier_service_id": 2,
    "name": "United Kingdom",
    "orderq": 0,
    "active": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.reamus_destination_station`
- **Record Count:** 31
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_code | varchar | - | - |  |
| postcode_from | varchar | - | - |  |
| postcode_to | varchar | - | - |  |
| product_code | varchar | - | - |  |
| station_id | varchar | - | - |  |
| hub_id | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 202072,
    "country_code": "GB",
    "postcode_from": "YO307WX",
    "postcode_to": "YO309ZZ",
    "product_code": "01"
}
... (more columns)
```

---

## Table: `smarttrack_staging.reamus_exception`
- **Record Count:** 13
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| country_code | varchar | - | - |  |
| postcode_from | varchar | - | - |  |
| postcode_to | varchar | - | - |  |
| product_code | varchar | - | - |  |
| feature_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6635,
    "country_code": "GB",
    "postcode_from": "TN80AA",
    "postcode_to": "TN89ZZ",
    "product_code": "01"
}
... (more columns)
```

---

## Table: `smarttrack_staging.reamus_product_service`
- **Record Count:** 13
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reamus_id | varchar | - | - |  |
| product_code | varchar | - | - |  |
| feature_code | varchar | - | - |  |
| exception | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3057,
    "reamus_id": "078008",
    "product_code": "01",
    "feature_code": "94",
    "exception": "Y"
}
... (more columns)
```

---

## Table: `smarttrack_staging.reamus_service`
- **Record Count:** 77
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| service_description | varchar | - | - |  |
| product_line1 | varchar | - | - |  |
| product_line2 | varchar | - | - |  |
| product_code | varchar | - | - |  |
| date_code | varchar | - | - |  |
| day_text | varchar | - | - |  |
| time_code | varchar | - | - |  |
| time_text | varchar | - | - |  |
| handling | varchar | - | - |  |
| feature_id | varchar | - | - |  |
| feature_code | varchar | - | - |  |
| file_type | varchar | - | - |  |
| consignment_flag | varchar | - | - |  |
| ds_flag | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "service_id": 10,
    "service_description": "PRIORITY 12:00",
    "product_line1": "VAN MON TO FRI",
    "product_line2": "PRE 12 POD"
}
... (more columns)
```

---

## Table: `smarttrack_staging.reamus_site`
- **Record Count:** 14
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| reamus_id | varchar | - | - |  |
| site | varchar | - | - |  |
| reamus_id2 | varchar | - | - |  |
| country_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2191,
    "reamus_id": "002448",
    "site": "94D   (00)",
    "reamus_id2": "002448",
    "country_code": "GB"
}
... (more columns)
```

---

## Table: `smarttrack_staging.remotearea_charges_carrier`
- **Record Count:** 1
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "remotearea_group_id": null,
    "remotearea_charges": null,
    "is_deleted": "N",
    "added_by": 58
}
... (more columns)
```

---

## Table: `smarttrack_staging.remotearea_charges_carrier_user`
- **Record Count:** 2
- **Inferred Module:** Carrier Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| user_account_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "remotearea_group_id": 2,
    "user_account_id": 2234,
    "remotearea_charges": "12.00",
    "is_deleted": "N"
}
... (more columns)
```

---

## Table: `smarttrack_staging.remotearea_charges_services`
- **Record Count:** 1
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| service_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "remotearea_group_id": 16,
    "service_id": 252,
    "remotearea_charges": "5.00",
    "from_weight": "0.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.remotearea_charges_services_user`
- **Record Count:** 20
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| service_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| user_account_id | int | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 5,
    "remotearea_group_id": 16,
    "remotearea_charges": "5.00",
    "service_id": 252,
    "from_weight": "0.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.remotearea_charges_tariffs`
- **Record Count:** 454
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remotearea_group_id | int | - | - |  |
| remotearea_charges | decimal | - | - |  |
| tariff_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| formulla | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "remotearea_group_id": 324,
    "remotearea_charges": "3.00",
    "tariff_id": 240542,
    "from_weight": "1.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.remotearea_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| service_code | varchar | - | - |  |
| charges | varchar | - | - |  |
| remotearea_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.remotearea_weight_charge`
- **Record Count:** 240
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| service_code | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| postcode_name | varchar | - | - |  |
| formulla | varchar | - | - |  |
| charges | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "weight_from": "0.00",
    "weight_to": "0.50",
    "service_code": "TINA",
    "country_iso": "ZM"
}
... (more columns)
```

---

## Table: `smarttrack_staging.remoteareas`
- **Record Count:** 15
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| remoteareas_groups_id | int | - | - |  |
| country_id | int | - | - |  |
| from_postcode | varchar | - | - |  |
| to_postcode | varchar | - | - |  |
| city | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 14206,
    "remoteareas_groups_id": 325,
    "country_id": 197,
    "from_postcode": "9774",
    "to_postcode": "9774"
}
... (more columns)
```

---

## Table: `smarttrack_staging.remoteareas_groups`
- **Record Count:** 48
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| carrier_id | int | - | - |  |
| group_name | varchar | - | - |  |
| is_deleted | enum | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "carrier_id": 16,
    "group_name": "TEST123",
    "is_deleted": "Y",
    "added_by": 148
}
... (more columns)
```

---

## Table: `smarttrack_staging.remoteareas_groups_log`
- **Record Count:** 21
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2170,
    "logdate": "2018-11-29 18:21:40",
    "ipaddress": "656773136",
    "log_id": 31
}
... (more columns)
```

---

## Table: `smarttrack_staging.remoteareas_log`
- **Record Count:** 23
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 2170,
    "logdate": "2018-11-29 18:22:31",
    "ipaddress": "656773136",
    "log_id": 4481
}
... (more columns)
```

---

## Table: `smarttrack_staging.report_customize_settings`
- **Record Count:** 10
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | int | - | - |  |
| report_title | varchar | - | - |  |
| report_key | varchar | - | - |  |
| fields_data | longtext | - | - |  |
| date_added | datetime | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "account_id": 148,
    "report_title": "Royal mail Template",
    "report_key": "tracking_status_report",
    "fields_data": "a:21:{i:0;a:2:{s:3:\"key\";s:4:\"date\";s:5:\"title\";s:4:\"Date\";}i:1;a:2:{s:3:\"key\";s:15:\"tracking_number\";s:5:\"title\";s:15:\"Tracking Number\";}i:2;a:2:{s:3:\"key\";s:4:\"hawb\";s:5:\"title\";s:4:\"HAWB\";}i:3;a:2:{s:3:\"key\";s:7:\"service\";s:5:\"title\";s:7:\"Service\";}i:4;a:2:{s:3:\"key\";s:4:\"city\";s:5:\"title\";s:4:\"City\";}i:5;a:2:{s:3:\"key\";s:7:\"country\";s:5:\"title\";s:7:\"Country\";}i:6;a:2:{s:3:\"key\";s:6:\"weight\";s:5:\"title\";s:10:\"Weight(Kg)\";}i:7;a:2:{s:3:\"key\";s:17:\"volumetric_weight\";s:5:\"title\";s:17:\"Volumetric Weight\";}i:8;a:2:{s:3:\"key\";s:16:\"volumetric_liter\";s:5:\"title\";s:16:\"Volumetric Liter\";}i:9;a:2:{s:3:\"key\";s:5:\"lxwxh\";s:5:\"title\";s:5:\"LXWXH\";}i:10;a:2:{s:3:\"key\";s:24:\"last_event_tracking_date\";s:5:\"title\";s:24:\"Last Event Tracking Date\";}i:11;a:2:{s:3:\"key\";s:6:\"status\";s:5:\"title\";s:6:\"Status\";}i:12;a:2:{s:3:\"key\";s:15:\"tracking_detail\";s:5:\"title\";s:15:\"Tracking Detail\";}i:13;a:2:{s:3:\"key\";s:16:\"delivery_on_time\";s:5:\"title\";s:16:\"Delivery On Time\";}i:14;a:2:{s:3:\"key\";s:25:\"delivery_aim_working_days\";s:5:\"title\";s:27:\"Delivery Aim (Working Days)\";}i:15;a:2:{s:3:\"key\";s:40:\"total_no_of_days_booking_to_hub_received\";s:5:\"title\";s:42:\"Total No of Days (Booking To Hub Received)\";}i:16;a:2:{s:3:\"key\";s:49:\"total_no_of_days_hub_received_to_carrier_received\";s:5:\"title\";s:52:\"Total No of Days  (Hub Received to carrier received)\";}i:17;a:2:{s:3:\"key\";s:52:\"total_no_of_days_from_carrier_received_calendar_days\";s:5:\"title\";s:54:\"Total No of Days From Carrier Received (Calendar Days)\";}i:18;a:2:{s:3:\"key\";s:46:\"total_no_of_working_days_from_carrier_received\";s:5:\"title\";s:46:\"Total No of Working Days From Carrier Received\";}i:19;a:2:{s:3:\"key\";s:32:\"total_transit_time_calendar_days\";s:5:\"title\";s:34:\"Total Transit Time (Calendar Days)\";}i:20;a:2:{s:3:\"key\";s:31:\"total_transit_time_working_days\";s:5:\"title\";s:33:\"Total Transit Time (Working Days)\";}}"
}
... (more columns)
```

---

## Table: `smarttrack_staging.routing_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| product_id | int | - | - |  |
| user_id | int | - | - |  |
| routing_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.royalmail_docket_number`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tracking_number | varchar | - | - |  |
| docket_number | varchar | - | - |  |
| file_name | varchar | - | - |  |
| date_created | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 69381,
    "tracking_number": "GV028030824GB",
    "docket_number": "7029158451",
    "file_name": "WCTB200117DF",
    "date_created": "2020-01-28 17:40:57"
}
... (more columns)
```

---

## Table: `smarttrack_staging.royalmail_sortcode`
- **Record Count:** 426
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode_area | varchar | - | - |  |
| sortcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1819,
    "postcode_area": "PA",
    "sortcode": "A87"
}
... (more columns)
```

---

## Table: `smarttrack_staging.sales_call_log`
- **Record Count:** 0
- **Inferred Module:** System Logging
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| date_call | date | - | - |  |
| meeting_date | timestamp | - | - |  |
| customer_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address | varchar | - | - |  |
| telephone | varchar | - | - |  |
| email | varchar | - | - |  |
| detail_discussed | text | - | - |  |
| document_link | varchar | - | - |  |
| follow_meeting_date | timestamp | - | - |  |
| userid | int | - | - |  |
| email_send | char | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.sales_pot_comission`
- **Record Count:** 15
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| date_added | datetime | - | - |  |
| no_of_shipments | int | - | - |  |
| comission | decimal | - | - |  |
| is_paid | tinyint | - | - |  |
| paid_by | int | - | - |  |
| paid_date | datetime | - | - |  |
| company_comission | decimal | - | - |  |
| sales_comission | decimal | - | - |  |
| salepot_table_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "date_added": "2016-08-18 15:38:46",
    "no_of_shipments": 5,
    "comission": "50.00",
    "is_paid": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_agent_mapping`
- **Record Count:** 21
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| serviceid | int | - | - |  |
| agentid | int | - | - |  |
| linehaul_agent | int | - | - |  |
| account_number | varchar | - | - |  |
| api_url | varchar | - | - |  |
| api_username | varchar | - | - |  |
| api_password | varchar | - | - |  |
| ftp_host | varchar | - | - |  |
| ftp_username | varchar | - | - |  |
| ftp_password | varchar | - | - |  |
| integration_type | varchar | - | - |  |
| class_file_name | varchar | - | - |  |
| insurance_charges | decimal | - | - |  |
| insurance_cover | decimal | - | - |  |
| reroute_charges | decimal | - | - |  |
| oversize_charges | decimal | - | - |  |
| address_change_charges | decimal | - | - |  |
| other_surcharges | decimal | - | - |  |
| return_charges | decimal | - | - |  |
| relabel_charges | decimal | - | - |  |
| wrong_address_charges | decimal | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| email | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 45,
    "serviceid": 63,
    "agentid": 4,
    "linehaul_agent": 0,
    "account_number": ""
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_collection_county`
- **Record Count:** 6
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| country_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 7,
    "service_id": 300,
    "country_id": 80
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_constant`
- **Record Count:** 28
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| constant | varchar | - | - |  |
| carrier_id | int | - | - |  |
| caption | varchar | - | - |  |
| description | varchar | - | - |  |
| design_control | varchar | - | - |  |
| mandatory | bit | - | - |  |
| sort_order | int | - | - |  |
| integration_type | enum | - | - |  |
| default_values | text | - | - |  |
| field_size | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "constant": "YODEL_SHIPPER_CONTACT",
    "carrier_id": 16,
    "caption": "Shipper Contact",
    "description": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_constant_value`
- **Record Count:** 15
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| constant_value | varchar | - | - |  |
| service_id | int | - | - |  |
| agent_id | int | - | - |  |
| constant_id | int | - | - |  |
| date_created | datetime | - | - |  |
| added_by | int | - | - |  |
| date_update | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "constant_value": "ONE WORLD EXPRESS",
    "service_id": 226,
    "agent_id": 1,
    "constant_id": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_country_ttime`
- **Record Count:** 28
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| id_country | int | - | - |  |
| id_service | int | - | - |  |
| transit_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 11327,
    "id_country": 65,
    "id_service": 322,
    "transit_time": 5
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_document`
- **Record Count:** 4
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| document_id | int | - | - |  |
| agent_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6,
    "service_id": 1,
    "document_id": 7,
    "agent_id": 3,
    "document_name": "1508154586aramex.png"
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_log`
- **Record Count:** 14
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 512,
    "userid": 58,
    "logdate": "2020-03-10 13:44:55",
    "ipaddress": "393717774",
    "log_id": 299
}
... (more columns)
```

---

## Table: `smarttrack_staging.service_range_mapping`
- **Record Count:** 137
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| service_id | int | - | - |  |
| agent_id | int | - | - |  |
| licence_plate_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 10,
    "service_id": 226,
    "agent_id": 1,
    "licence_plate_id": 92
}
... (more columns)
```

---

## Table: `smarttrack_staging.services`
- **Record Count:** 2
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| name | varchar | - | - |  |
| code | varchar | - | - |  |
| carrier_id | int | - | - |  |
| account_number | varchar | - | - |  |
| type | varchar | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| wieght_type | int | - | - |  |
| supplier | varchar | - | - |  |
| service_type | enum | - | - |  |
| drop_off_service_id | bigint | - | - |  |
| description | text | - | - |  |
| fuel_surcharge_cost | decimal | - | - |  |
| fuel_surcharge | decimal | - | - |  |
| fuel_surcharge_type | char | - | - |  |
| max_length | decimal | - | - |  |
| max_width | decimal | - | - |  |
| max_height | decimal | - | - |  |
| max_volumetric_weight | decimal | - | - |  |
| volumetric_denominator | int | - | - |  |
| send_data_courier | tinyint | - | - |  |
| is_document | tinyint | - | - |  |
| friday_only_flag | tinyint | - | - |  |
| saturday_only_flag | tinyint | - | - |  |
| sunday_only_flag | tinyint | - | - |  |
| product_owner | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | tinyint | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| uploaded_currency | varchar | - | - |  |
| uploaded_currency_value | decimal | - | - |  |
| registration_fee | decimal | - | - |  |
| weight_after | decimal | - | - |  |
| aditional_charge | decimal | - | - |  |
| origin_country | int | - | - |  |
| is_untrack | bit | - | - |  |
| account_owner | int | - | - |  |
| remotearea | enum | - | - |  |
| carrier_address_limit | int | - | - |  |
| label_class_name | varchar | - | - |  |
| transit_time | int | - | - |  |
| required_email | tinyint | - | - |  |
| required_telephone | tinyint | - | - |  |
| shipment_type | enum | - | - |  |
| pre_sort | enum | - | - |  |
| proforma_invoice | tinyint | - | - |  |
| agent_dispatch | enum | - | - |  |
| brief_manifest | enum | - | - |  |
| delivery_mode | tinyint | - | - |  |
| insurance_available | tinyint | - | - |  |
| vol_wgt_formula | varchar | - | - |  |
| is_remotearea | enum | - | - |  |
| is_customized | bit | - | - |  |
| pre_advise | enum | - | - |  |
| pre_alert | enum | - | - |  |
| pre_alert_email | text | - | - |  |
| cut_off_time | varchar | - | - |  |
| label_charges | decimal | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| maximum_allowed_dimension | int | - | - |  |
| maximum_dim_formula | varchar | - | - |  |
| validation_type | enum | - | - |  |
| zone_type | enum | - | - |  |
| tariff_type | enum | - | - |  |
| girth | decimal | - | - |  |
| girth_formula | varchar | - | - |  |
| mail_type | enum | - | - |  |
| mail_option | enum | - | - |  |
| is_reschedulable | int | - | - |  |
| carrier_service_code | varchar | - | - |  |
| is_eori_required | int | - | - |  |
| delivery_type | enum | - | - |  |
| is_commercials | enum | - | - |  |
| is_cn | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 226,
    "name": "Next Day Delivery",
    "code": "AMZ001",
    "carrier_id": 16,
    "account_number": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.services_dpd`
- **Record Count:** 83
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| 2_digit_service_code | varchar | - | - |  |
| 3_digit_service_code | varchar | - | - |  |
| dpd_product_desc | varchar | - | - |  |
| dpd_label_service | varchar | - | - |  |
| ilk_product_desc | varchar | - | - |  |
| ilk_alternative_service_desc | varchar | - | - |  |
| premium | varchar | - | - |  |
| sec_dpd | varchar | - | - |  |
| sec_ilk | varchar | - | - |  |
| ilk_max_parcels_per_con | int | - | - |  |
| ilk_max_weight_per_parcel | int | - | - |  |
| dpd_max_parcels_per_con | int | - | - |  |
| dpd_max_weight_per_parcel | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "2_digit_service_code": "01",
    "3_digit_service_code": "801",
    "dpd_product_desc": "",
    "dpd_label_service": ""
}
... (more columns)
```

---

## Table: `smarttrack_staging.shopping_platform`
- **Record Count:** 16
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| title | varchar | - | - |  |
| page_key | varchar | - | - |  |
| description | longtext | - | - |  |
| translation_key | varchar | - | - |  |
| plugin_key | varchar | - | - |  |
| integration_logo | varchar | - | - |  |
| display_option | tinyint | - | - |  |
| connect_url | varchar | - | - |  |
| active | tinyint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "title": "Magento 1.0",
    "page_key": "magento",
    "description": "<center><img src=\"..\/images\/magento.jpg\" \/><\/center>\n <div class=\"row\">\n <div class=\"col-md-9 col-sm-9 col-xs-9\">\n <div class=\"tab-content\">\n <div class=\"tab-pane active\" id=\"tab_7_1\">\n <h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.<\/h3>\n <p>Oneworld's multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.<\/p>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_2\">\n <p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, \n our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development \n teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier \n integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,\n OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld's innovative multi-carrier shipping software provides seamless \n integration with a wide range of UK and international parcel, packet and mail service providers, including:<\/p>\n <ul>\n <li>YODEL<\/li>\n <li>DHL Express<\/li>\n <li>UK Mail<\/li>\n <li>TNT Express<\/li>\n <li>DX Freight<\/li>\n <li>Whistl<\/li>\n <li>Asendia<\/li>\n <li>OneWorldorce<\/li>\n <li>Hermes \/ MyHermes<\/li>\n <li>Collect Plus<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_3\">\n <ul>\n <li>Integrates multiple carriers, reducing time and paperwork.<\/li>\n <li>Access OneWorld's 'pooled volume' discounted rates and save up to 80% on your existing rates.<\/li>\n <li>Multi-carrier web based tracking.<\/li>\n <li>Free Magento integration provided by our team of in-house software developers.<\/li>\n <li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).<\/li>\n <li>Dedicated in-house customer services team.<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_4\">\n <ul>\n <li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager<\/li>\n <li>2. Log in using your Magento admin username and password<\/li>\n <li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;<\/li>\n <li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.<\/li>\n <\/ul>\n <\/div>\n <div class=\"tab-pane fade\" id=\"tab_7_5\">\n <h3>System Requirements<\/h3>\n <ul>\n <li>Magento 1.9 or higher<\/li>\n <li>OneWorld shipping account<\/li>\n <li>At least one OneWorld Service Preference List configured<\/li>\n <li>OneWorld tracking API Key (optional)<\/li>\n <\/ul>\n <h3>Setup<\/h3>\n After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.<\/div>\n <\/div>\n <\/div>\n <div class=\"col-md-3 col-sm-3 col-xs-3\">\n <ul class=\"nav nav-tabs tabs-right\">\n <li class=\"active\"><a href=\"#tab_7_1\" data-toggle=\"tab\"> Integration <\/a><\/li>\n <li><a href=\"#tab_7_2\" data-toggle=\"tab\"> Save time and money <\/a><\/li>\n <li><a href=\"#tab_7_3\" data-toggle=\"tab\"> Features and Benefits<\/a><\/li>\n <li><a href=\"#tab_7_4\" data-toggle=\"tab\"> Instructions for installing <\/a><\/li>\n <li><a href=\"#tab_7_5\" data-toggle=\"tab\"> Setup Guide <\/a><\/li>\n <\/ul>\n <\/div>\n <\/div>",
    "translation_key": "MAGENTO 1.0"
}
... (more columns)
```

---

## Table: `smarttrack_staging.sort_key_record`
- **Record Count:** 23
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pos_sld_sort_level_key | varchar | - | - |  |
| pos_sld_level_1_type | varchar | - | - |  |
| pos_sld_level_1_name | varchar | - | - |  |
| pos_sld_level_1_code | varchar | - | - |  |
| pos_sld_level_2_type | varchar | - | - |  |
| pos_sld_level_2_name | varchar | - | - |  |
| pos_sld_level_2_code | varchar | - | - |  |
| pos_sld_level_3_type | varchar | - | - |  |
| pos_sld_level_3_name | varchar | - | - |  |
| pos_sld_level_3_code | varchar | - | - |  |
| pos_sld_level_4_type | varchar | - | - |  |
| pos_sld_level_4_name | varchar | - | - |  |
| pos_sld_level_4_code | varchar | - | - |  |
| pos_sld_level_5_type | varchar | - | - |  |
| pos_sld_level_5_name | varchar | - | - |  |
| pos_sld_level_5_code | varchar | - | - |  |
| pos_sld_hermes_barcode_1_to_7 | varchar | - | - |  |
| pos_sld_hermes_barcode_seq_key | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 70857,
    "pos_sld_sort_level_key": "604",
    "pos_sld_level_1_type": "DEPOT",
    "pos_sld_level_1_name": "PER",
    "pos_sld_level_1_code": "33"
}
... (more columns)
```

---

## Table: `smarttrack_staging.sorter_postcode_zone`
- **Record Count:** 119
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| area | varchar | - | - |  |
| postcode | varchar | - | - |  |
| zone | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "area": "Aberdeen",
    "postcode": "AB",
    "zone": "1"
}
... (more columns)
```

---

## Table: `smarttrack_staging.sp_tariff_log`
- **Record Count:** 21
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| account_id | int | - | - |  |
| charges_type | varchar | - | - |  |
| charges | decimal | - | - |  |
| formulla | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| consignment_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2763,
    "account_id": 2288,
    "charges_type": "label charges",
    "charges": "0.00",
    "formulla": ",CURRENT_TIMESTAMP,@consignment_id_p),\n                                            (NULL, @f_user_ac"
}
... (more columns)
```

---

## Table: `smarttrack_staging.status_reason`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| status_id | int | - | - |  |
| reason | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.tagnumber_range`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| range_start | bigint | - | - |  |
| range_end | bigint | - | - |  |
| next_number | bigint | - | - |  |
| increment_date | datetime | - | - |  |
| service | varchar | - | - |  |
| country | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "range_start": 1000001,
    "range_end": 9999999,
    "next_number": 1001359,
    "increment_date": "0000-00-00 00:00:00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.tariff_additional_charges`
- **Record Count:** 39
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | bigint | - | - |  |
| consignment_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2,
    "tariff_id": 240542,
    "consignment_charges_types_id": 1,
    "charge": "2.00",
    "charge_type": "percentage"
}
... (more columns)
```

---

## Table: `smarttrack_staging.tariff_details`
- **Record Count:** 8
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_name | varchar | - | - |  |
| status | bit | - | - |  |
| tariff_type | varchar | - | - |  |
| start_date | datetime | - | - |  |
| end_date | datetime | - | - |  |
| date_created | timestamp | - | - |  |
| added_by | int | - | - |  |
| currency | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 8,
    "tariff_name": "jaimin test",
    "status": 0,
    "tariff_type": "CHARGE",
    "start_date": "2017-02-01 00:00:00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.tariff_service_charges`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| tariff_id | bigint | - | - |  |
| tariff_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.tariff_user_mapping`
- **Record Count:** 0
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_name | int | - | - |  |
| user_id | int | - | - |  |
| tariff_added_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.tariffs`
- **Record Count:** 14
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| carrier_id | int | - | - |  |
| service_id | int | - | - |  |
| name | varchar | - | - |  |
| status | tinyint | - | - |  |
| currency_id | int | - | - |  |
| tariff_type | enum | - | - |  |
| start_date | date | - | - |  |
| end_date | date | - | - |  |
| description | text | - | - |  |
| tariffs_pricing_rule_id | int | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 240577,
    "user_account_id": 2331,
    "carrier_id": 16,
    "service_id": 38,
    "name": "EURACC Tariff 1 Sup"
}
... (more columns)
```

---

## Table: `smarttrack_staging.tariffs_account_mapping`
- **Record Count:** 144
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| user_account_id | int | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_id": 240403,
    "user_account_id": 2290,
    "added_date": "2018-12-07 10:05:03",
    "added_by": 1544177103
}
... (more columns)
```

---

## Table: `smarttrack_staging.tariffs_details`
- **Record Count:** 14
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariffs_id | int | - | - |  |
| from_zone_id | int | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| weight_cost | decimal | - | - |  |
| piece_cost | decimal | - | - |  |
| formula | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 61470,
    "tariffs_id": 240676,
    "from_zone_id": 2819,
    "to_zone_id": 0,
    "weight_from": "0.50"
}
... (more columns)
```

---

## Table: `smarttrack_staging.tariffs_log`
- **Record Count:** 0
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| old_id | int | - | - |  |
| courier_service_id | int | - | - |  |
| collection_rateband_id | int | - | - |  |
| destination_rateband_id | int | - | - |  |
| collection_postcode_group_id | int | - | - |  |
| destination_postcode_group_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| tariff | decimal | - | - |  |
| add_unit_cost | decimal | - | - |  |
| unit_size | decimal | - | - |  |
| extra_tariff | decimal | - | - |  |
| extra_add_unit_cost | decimal | - | - |  |
| orderq | int | - | - |  |
| active | tinyint | - | - |  |
| deletedq | char | - | - |  |
| added_on | datetime | - | - |  |
| added_by | varchar | - | - |  |
| changed_on | datetime | - | - |  |
| changed_by | varchar | - | - |  |
| customer_id | varchar | - | - |  |
| formula | varchar | - | - |  |
| log_date | date | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.tariffs_pricing`
- **Record Count:** 0
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| tarif_pricing_type | enum | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| margin_type | enum | - | - |  |
| margin | decimal | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.tariffs_pricing_rules`
- **Record Count:** 38
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_id | int | - | - |  |
| name | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_id": 240393,
    "name": "UKMELL TARIFF CUSTOMER",
    "date_added": "2018-11-12 16:11:40",
    "added_by": 58
}
... (more columns)
```

---

## Table: `smarttrack_staging.tariffs_pricing_rules_details`
- **Record Count:** 23
- **Inferred Module:** Finance & Pricing
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| tariff_pricing_rule_id | int | - | - |  |
| to_zone_id | int | - | - |  |
| weight_from | decimal | - | - |  |
| weight_to | decimal | - | - |  |
| margin | decimal | - | - |  |
| margin_type | enum | - | - |  |
| margin_weight_cost | varchar | - | - |  |
| margin_piece_cost | varchar | - | - |  |
| linehaul | decimal | - | - |  |
| linehaul_type | enum | - | - |  |
| tariff_pricing_type | enum | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | int | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "tariff_pricing_rule_id": 1,
    "to_zone_id": 0,
    "weight_from": "0.00",
    "weight_to": "0.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.themes`
- **Record Count:** 6
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| name | varchar | - | - |  |
| slug | varchar | - | - |  |
| style_sheet | varchar | - | - |  |
| dashboard_template | varchar | - | - |  |
| is_active | bit | - | - |  |
| created_by | bigint | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "name": "Handlerbund",
    "slug": "hnd",
    "style_sheet": "",
    "dashboard_template": ""
}
... (more columns)
```

---

## Table: `smarttrack_staging.tourline_routine`
- **Record Count:** 20
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| agency_name | varchar | - | - |  |
| postal_code | varchar | - | - |  |
| agency_code | varchar | - | - |  |
| zone | varchar | - | - |  |
| province | varchar | - | - |  |
| route_code | varchar | - | - |  |
| km | varchar | - | - |  |
| town_name | varchar | - | - |  |
| kilometer | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 130497,
    "agency_name": "VISEU P03                                           ",
    "postal_code": "3511 ",
    "agency_code": "004034",
    "zone": "PTI"
}
... (more columns)
```

---

## Table: `smarttrack_staging.tracking_data`
- **Record Count:** 38
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| entity_id | int | - | - |  |
| entity_type | enum | - | - |  |
| tracking_number | varchar | - | - |  |
| user_id | int | - | - |  |
| track_point | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| ip_address | varchar | - | - |  |
| status_code_id | int | - | - |  |
| warehouse_id | int | - | - |  |
| pod_image | varchar | - | - |  |
| carrier_code | varchar | - | - |  |
| carrier_desc | varchar | - | - |  |
| signatory | varchar | - | - |  |
| date_added | timestamp | - | - |  |
| parcel_image | varchar | - | - |  |
| latitude | varchar | - | - |  |
| longitude | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 138677,
    "entity_id": 231616,
    "entity_type": "parcel",
    "tracking_number": "LM239194095SE",
    "user_id": 2253
}
... (more columns)
```

---

## Table: `smarttrack_staging.tracking_estimated_time`
- **Record Count:** 324
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| handeling_code | varchar | - | - |  |
| country_iso | varchar | - | - |  |
| estimated_time | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "handeling_code": "REGPOSTEURUTR",
    "country_iso": "BR",
    "estimated_time": "will be delivered in the upcoming days"
}
... (more columns)
```

---

## Table: `smarttrack_staging.tracking_status_codes`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| status_code | varchar | - | - |  |
| status_code_map | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "status_code": "Order Created \/ Label Created",
    "status_code_map": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.ukmail_authentication`
- **Record Count:** 4
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| authentication_token | varchar | - | - |  |
| date_created | datetime | - | - |  |
| user_account | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6,
    "authentication_token": "A188F266-CFE1-407D-B16A-F2F46DD8B064",
    "date_created": "2019-03-11 16:23:20",
    "user_account": "2297"
}
... (more columns)
```

---

## Table: `smarttrack_staging.ukpostcodelatlng`
- **Record Count:** 0
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| postcode | varchar | - | - |  |
| latitude | decimal | - | - |  |
| longitude | decimal | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.user`
- **Record Count:** 112
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_type | enum | - | - |  |
| user_name | varchar | - | - |  |
| user_pass | varchar | - | - |  |
| active_flag | bit | - | - |  |
| first_name | varchar | - | - |  |
| last_name | varchar | - | - |  |
| address | varchar | - | - |  |
| email | varchar | - | - |  |
| phone | varchar | - | - |  |
| country_id | int | - | - |  |
| api_key | varchar | - | - |  |
| api_secert | varchar | - | - |  |
| api_date | datetime | - | - |  |
| profile_image | varchar | - | - |  |
| is_employee | bit | - | - |  |
| warehouse_id | int | - | - |  |
| dashboard | enum | - | - |  |
| invalid_login_count | int | - | - |  |
| user_account_id | int | - | - |  |
| last_login_date | datetime | - | - |  |
| added_by | int | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | int | - | - |  |
| updated_date | datetime | - | - |  |
| is_deleted | bit | - | - |  |
| archive_server | bit | - | - |  |
| carrier_setup_agreement | bit | - | - |  |
| receive_email | enum | - | - |  |
| tc_agreed_date | date | - | - |  |
| is_tc_agreed | enum | - | - |  |
| address_2 | varchar | - | - |  |
| address_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| state | varchar | - | - |  |
| commission_break_event_amount | varchar | - | - |  |
| is_sale_pot_eligible | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 58,
    "user_type": "admin",
    "user_name": "admin_login",
    "user_pass": "$2y$10$IzGMeBEx4L.9prqHtJd91uVUhaVXvsfpXk9DteimuTfIXNWSm6DGq",
    "active_flag": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_account_log`
- **Record Count:** 13
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 773,
    "userid": 58,
    "logdate": "2020-03-10 14:58:25",
    "ipaddress": "859091698",
    "log_id": 2297
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_account_old`
- **Record Count:** 1
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account | varchar | - | - |  |
| active_flag | bit | - | - |  |
| company | varchar | - | - |  |
| full_name | varchar | - | - |  |
| return_address | varchar | - | - |  |
| sms_dpd | bit | - | - |  |
| user_service_type | enum | - | - |  |
| parentid | int | - | - |  |
| phone | varchar | - | - |  |
| logo | varchar | - | - |  |
| instant_label | bit | - | - |  |
| country | varchar | - | - |  |
| country_id | int | - | - |  |
| tracking_api_access | bit | - | - |  |
| import_data_csv | bit | - | - |  |
| proforma | bit | - | - |  |
| add_tracking | bit | - | - |  |
| collection | bit | - | - |  |
| default_description | varchar | - | - |  |
| default_notes | varchar | - | - |  |
| default_weight | decimal | - | - |  |
| payment_term | text | - | - |  |
| query_term | text | - | - |  |
| vat_number | varchar | - | - |  |
| billing_currency | varchar | - | - |  |
| vat_chargable | bit | - | - |  |
| vat_value | decimal | - | - |  |
| allow_remote_area | bit | - | - |  |
| telephone | varchar | - | - |  |
| billing_address | varchar | - | - |  |
| date_dispatch | bit | - | - |  |
| is_product | varchar | - | - |  |
| profile_image | varchar | - | - |  |
| send_courier_data | bit | - | - |  |
| archive_server | bit | - | - |  |
| credit_check | bit | - | - |  |
| tariff_agreed | bit | - | - |  |
| sales_person | varchar | - | - |  |
| scan_document | text | - | - |  |
| data_entry | bit | - | - |  |
| bank_account_title | varchar | - | - |  |
| bank_sortcode | varchar | - | - |  |
| bank_account_number | varchar | - | - |  |
| bank_branch_address | varchar | - | - |  |
| trade_name_i | varchar | - | - |  |
| trade_address_i | varchar | - | - |  |
| trade_email_i | varchar | - | - |  |
| trade_phone_i | varchar | - | - |  |
| trade_name_ii | varchar | - | - |  |
| trade_address_ii | varchar | - | - |  |
| trade_email_ii | varchar | - | - |  |
| trade_phone_ii | varchar | - | - |  |
| reg_number | varchar | - | - |  |
| reg_address | varchar | - | - |  |
| reg_postcode | varchar | - | - |  |
| reg_country | varchar | - | - |  |
| sale_agent | varchar | - | - |  |
| sale_date | datetime | - | - |  |
| fuel_charges | decimal | - | - |  |
| warehouse_id | int | - | - |  |
| user_signature | text | - | - |  |
| is_fuelcharges_include | bit | - | - |  |
| is_prepaid | bit | - | - |  |
| return_label | bit | - | - |  |
| finalmile_over_label | bit | - | - |  |
| request_manifest_collection | bit | - | - |  |
| create_pre_alert | bit | - | - |  |
| is_employee | bit | - | - |  |
| invoice_bank_details_id | int | - | - |  |
| check_list_account_form | bit | - | - |  |
| check_list_credit_check | bit | - | - |  |
| check_list_t_cs | bit | - | - |  |
| check_list_tariff_agreed | bit | - | - |  |
| check_list_sales_pot | bit | - | - |  |
| sales_pot_time_period | int | - | - |  |
| sales_pot_percentage | decimal | - | - |  |
| last_login_date | timestamp | - | - |  |
| invalid_login_count | int | - | - |  |
| token | varchar | - | - |  |
| token_updated | timestamp | - | - |  |
| lock_time | timestamp | - | - |  |
| opearation_manifest | bit | - | - |  |
| own_tariff | bit | - | - |  |
| user_warehouse | enum | - | - |  |
| api_key | varchar | - | - |  |
| api_secert | varchar | - | - |  |
| api_date | datetime | - | - |  |
| bagging | bit | - | - |  |
| retail_customer | bit | - | - |  |
| show_price | bit | - | - |  |
| sales_rate | decimal | - | - |  |
| collection_add_line_1 | varchar | - | - |  |
| collection_add_line_2 | varchar | - | - |  |
| collection_add_line_3 | varchar | - | - |  |
| collection_city | varchar | - | - |  |
| collection_postcode | varchar | - | - |  |
| collection_country | varchar | - | - |  |
| theme_id | int | - | - |  |
| user_code | int | - | - |  |
| website_link | varchar | - | - |  |
| allow_return_email | bit | - | - |  |
| default_lang | varchar | - | - |  |
| credit_limit | decimal | - | - |  |
| invoice_period | enum | - | - |  |
| label_price | decimal | - | - |  |
| discount | decimal | - | - |  |
| account_code | varchar | - | - |  |
| paypal_email | varchar | - | - |  |
| paypal_currency | varchar | - | - |  |
| email | varchar | - | - |  |
| paypal_client_secret | varchar | - | - |  |
| alternative_email | varchar | - | - |  |
| billing_email | varchar | - | - |  |
| date_created | timestamp | - | - |  |
| allow_oversize | tinyint | - | - |  |
| allow_overweight | tinyint | - | - |  |
| paypal_client_id | varchar | - | - |  |
| invoice_template_id | bigint | - | - |  |
| send_tracking_data | bit | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 148,
    "user_account": "Admin",
    "active_flag": 1,
    "company": "Company",
    "full_name": "Hadi Hussain"
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_account_service_charges`
- **Record Count:** 25
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| service_id | bigint | - | - |  |
| consignment_charges_types_id | bigint | - | - |  |
| charge | decimal | - | - |  |
| charge_type | enum | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "user_account_id": 2285,
    "service_id": 244,
    "consignment_charges_types_id": 2,
    "charge": "15.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_audit`
- **Record Count:** 48
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| table_name | varchar | - | - |  |
| table_key | bigint | - | - |  |
| message | text | - | - |  |
| old_data | longtext | - | - |  |
| new_data | longtext | - | - |  |
| ip_address | varchar | - | - |  |
| added_by | bigint | - | - |  |
| created_at | timestamp | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 2047,
    "table_name": "parcel",
    "table_key": 232080,
    "message": "usman usman has generated new Parcel",
    "old_data": "null"
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_department`
- **Record Count:** 4
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| user_id | bigint | - | - |  |
| department_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "user_id": 2141,
    "department_id": 1
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_document`
- **Record Count:** 5
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| document_id | int | - | - |  |
| document_name | varchar | - | - |  |
| added_by | bigint | - | - |  |
| added_date | datetime | - | - |  |
| updated_by | bigint | - | - |  |
| updated_date | datetime | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 20,
    "user_account_id": null,
    "document_id": 4,
    "document_name": "1507639322images.jpg",
    "added_by": 148
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_log`
- **Record Count:** 611
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "userid": 58,
    "logdate": "2018-08-07 12:09:58",
    "ipaddress": "1506093954",
    "log_id": 2258
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_market_places_mapping`
- **Record Count:** 19
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| market_places_id | bigint | - | - |  |
| user_account_id | bigint | - | - |  |
| auth_data | text | - | - |  |
| store_key | varchar | - | - |  |
| active | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "market_places_id": 1,
    "user_account_id": 2191,
    "auth_data": "{\"AWS_ACCESS_KEY_ID\":\"AKIAJBUWT3ZBRDV3QITA\",\"AWS_SECRET_ACCESS_KEY\":\"6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe\",\"MERCHANT_ID\":\"A3LX344APRTG2Z\",\"MARKETPLACE_ID\":\"A1F83G8C2ARO7P\"}",
    "store_key": null
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_services_charges`
- **Record Count:** 19
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| service_id | int | - | - |  |
| sur_charge | decimal | - | - |  |
| sur_charge_type | enum | - | - |  |
| extra_charge | decimal | - | - |  |
| extra_charge_type | enum | - | - |  |
| discount | decimal | - | - |  |
| discount_type | enum | - | - |  |
| additional_charges_type | enum | - | - |  |
| additional_charges | decimal | - | - |  |
| additional_charges_details | text | - | - |  |
| last_updated | datetime | - | - |  |
| over_weight | decimal | - | - |  |
| over_size | decimal | - | - |  |
| over_weight_type | enum | - | - |  |
| over_size_type | enum | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 6,
    "user_account_id": null,
    "service_id": 0,
    "sur_charge": "0.00",
    "sur_charge_type": "percentage"
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_services_charges_log`
- **Record Count:** 0
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| userid | int | - | - |  |
| logdate | datetime | - | - |  |
| ipaddress | varchar | - | - |  |
| log_id | int | - | - |  |
| log_type | varchar | - | - |  |
| message | text | - | - |  |
| previous_data | text | - | - |  |
| current_data | text | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

---

## Table: `smarttrack_staging.user_services_routing`
- **Record Count:** 18
- **Inferred Module:** Service Management
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| user_account_id | int | - | - |  |
| country_id | int | - | - |  |
| from_weight | decimal | - | - |  |
| to_weight | decimal | - | - |  |
| status | bit | - | - |  |
| service_id | int | - | - |  |
| is_remotearea | bit | - | - |  |
| is_over_label | bit | - | - |  |
| added_by | int | - | - |  |
| is_agreed | bit | - | - |  |
| label_charges | decimal | - | - |  |
| is_dead_weight | bit | - | - |  |
| is_over_size | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 148474,
    "user_account_id": 148,
    "country_id": 97,
    "from_weight": "0.00",
    "to_weight": "300.00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.user_shopping_platforms`
- **Record Count:** 12
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| shopping_platform_id | int | - | - |  |
| reference | varchar | - | - |  |
| site_url | varchar | - | - |  |
| user_id | int | - | - |  |
| status | tinyint | - | - |  |
| date_created | timestamp | - | - |  |
| api_key | varchar | - | - |  |
| api_secrete | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "shopping_platform_id": 1,
    "reference": "asdasd",
    "site_url": "https:\/\/one.com",
    "user_id": 148
}
... (more columns)
```

---

## Table: `smarttrack_staging.userhasgroups`
- **Record Count:** 654
- **Inferred Module:** User & Access
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| admin_id | int | - | - |  |
| group_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 44,
    "admin_id": 4,
    "group_id": 17
}
... (more columns)
```

---

## Table: `smarttrack_staging.vehicle`
- **Record Count:** 12
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| vehicle_type | varchar | - | - |  |
| vehicle_make | varchar | - | - |  |
| model_year | int | - | - |  |
| vehicle_model | varchar | - | - |  |
| registration_number | varchar | - | - |  |
| vehicle_color | varchar | - | - |  |
| vehicle_capacity | varchar | - | - |  |
| is_deleted | tinyint | - | - |  |
| added_date | timestamp | - | - |  |
| added_by | int | - | - |  |
| vehicle_number | varchar | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "vehicle_type": "Vehicle Type",
    "vehicle_make": "Honda",
    "model_year": 2018,
    "vehicle_model": "Vehicle Model"
}
... (more columns)
```

---

## Table: `smarttrack_staging.vehicle_driver`
- **Record Count:** 15
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| driver_id | int | - | - |  |
| vehicle_id | int | - | - |  |
| driver_start_time | time | - | - |  |
| driver_end_time | time | - | - |  |
| joining_date | date | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| deleted_by | int | - | - |  |
| added_date | timestamp | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "driver_id": 2183,
    "vehicle_id": 1,
    "driver_start_time": "16:05:00",
    "driver_end_time": "18:45:00"
}
... (more columns)
```

---

## Table: `smarttrack_staging.vehicle_parcel_mapping`
- **Record Count:** 316
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | bigint | - | - |  |
| parcel_id | bigint | - | - |  |
| vehicle_id | bigint | - | - |  |
| driver_id | bigint | - | - |  |
| pickup_date | date | - | - |  |
| date_added | timestamp | - | - |  |
| added_by | bigint | - | - |  |
| is_active | bit | - | - |  |
| date_updated | datetime | - | - |  |
| updated_by | bigint | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "parcel_id": 11595,
    "vehicle_id": 3,
    "driver_id": 2225,
    "pickup_date": "2019-05-22"
}
... (more columns)
```

---

## Table: `smarttrack_staging.warehouse`
- **Record Count:** 11
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| warehouse_name | varchar | - | - |  |
| addressline1 | varchar | - | - |  |
| addressline2 | varchar | - | - |  |
| stateregion | varchar | - | - |  |
| citytown | varchar | - | - |  |
| postzipcode | varchar | - | - |  |
| countryid | int | - | - |  |
| phone | varchar | - | - |  |
| description | text | - | - |  |
| is_active | tinyint | - | - |  |
| is_deleted | tinyint | - | - |  |
| added_date | datetime | - | - |  |
| added_by | int | - | - |  |
| updated_date | timestamp | - | - |  |
| updated_by | int | - | - |  |
| hub | varchar | - | - |  |
| email | text | - | - |  |
| warehouse_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 9,
    "warehouse_name": "Birmingham3",
    "addressline1": "One World House",
    "addressline2": "",
    "stateregion": "Bartley Green"
}
... (more columns)
```

---

## Table: `smarttrack_staging.warehouse_processing_time`
- **Record Count:** 48
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| warehouse_id | int | - | - |  |
| parcel_processing_time | int | - | - |  |
| service_id | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 3,
    "warehouse_id": 13,
    "parcel_processing_time": 39,
    "service_id": 162
}
... (more columns)
```

---

## Table: `smarttrack_staging.warehouse_warehouse_ttime`
- **Record Count:** 5
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| from_warehouse_id | int | - | - |  |
| to_warehouse_id | int | - | - |  |
| transit_time | int | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "from_warehouse_id": 28,
    "to_warehouse_id": 27,
    "transit_time": 2
}
... (more columns)
```

---

## Table: `smarttrack_staging.whistl_depo_details`
- **Record Count:** 9
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| depo_id | varchar | - | - |  |
| depo_detail | varchar | - | - |  |
| depo_address | varchar | - | - |  |
| from_postcode | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 966,
    "depo_id": "36360",
    "depo_detail": "ROMFORD MC",
    "depo_address": "SOUTH OCKENDON",
    "from_postcode": "RM15"
}
... (more columns)
```

---

## Table: `smarttrack_staging.yodel_hubs`
- **Record Count:** 8
- **Inferred Module:** General / Uncategorized
### Columns
| Column | Type | Nullable | Key | Guess Logic |
|--------|------|----------|-----|-------------|
| id | int | - | - |  |
| pre_sort_carrier_id | int | - | - |  |
| hub | varchar | - | - |  |
| routing_code | varchar | - | - |  |
| company | varchar | - | - |  |
| contact | varchar | - | - |  |
| address_line_1 | varchar | - | - |  |
| address_line_2 | varchar | - | - |  |
| address_line_3 | varchar | - | - |  |
| city | varchar | - | - |  |
| postcode | varchar | - | - |  |
| country_iso_code | varchar | - | - |  |

### Logic & Relationships
- No obvious foreign keys detected by naming convention.

### Sample Data (First Record)
```json
{
    "id": 1,
    "pre_sort_carrier_id": 38,
    "hub": "52_INVERNESS",
    "routing_code": "52",
    "company": "YODEL"
}
... (more columns)
```

---

