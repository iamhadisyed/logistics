# Database Analysis Summary

## Module: Carriers & Services

### Table: `agent_data`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`
- **Relationships**: `user_id` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `agent_code` | `varchar(45)` | YES | `` |  |
| `agent_name` | `varchar(45)` | YES | `` |  |
| `active` | `tinyint(2)` | YES | `` |  |
| `contact_name` | `varchar(45)` | YES | `` |  |
| `address_line_1` | `varchar(45)` | YES | `` |  |
| `address_line_2` | `varchar(45)` | YES | `` |  |
| `address_line_3` | `varchar(45)` | YES | `` |  |
| `country_id` | `int(11)` | YES | `` |  |
| `county` | `varchar(45)` | YES | `` |  |
| `city` | `varchar(45)` | YES | `` |  |
| `postcode` | `varchar(45)` | YES | `` |  |
| `telephone` | `varchar(45)` | YES | `` |  |
| `mobile` | `varchar(45)` | YES | `` |  |
| `fax` | `varchar(45)` | YES | `` |  |
| `email` | `varchar(1000)` | YES | `` |  |
| `alternative_contact_1` | `varchar(45)` | YES | `` |  |
| `alternative1_telephone` | `varchar(45)` | YES | `` |  |
| `alternative1_mobile` | `varchar(45)` | YES | `` |  |
| `alternative1_fax` | `varchar(45)` | YES | `` |  |
| `alternative1_email` | `varchar(45)` | YES | `` |  |
| `alternative_contact_2` | `varchar(45)` | YES | `` |  |
| `alternative2_telephone` | `varchar(45)` | YES | `` |  |
| `alternative2_mobile` | `varchar(45)` | YES | `` |  |
| `alternative2_fax` | `varchar(45)` | YES | `` |  |
| `alternative2_email` | `varchar(45)` | YES | `` |  |
| `remarks` | `text` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `user_id` | `int(11)` | YES | `` |  |
| `is_deleted` | `bit(1)` | YES | `b'0'` |  |
| `logo` | `varchar(45)` | YES | `` |  |
| `agent_type` | `enum('carrier','dispatch','both')` | NO | `carrier` | REQUIRED |

---
### Table: `agent_documents`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `agent_id` | `int(11)` | YES | `` |  |
| `document_id` | `int(11)` | YES | `` |  |
| `document_name` | `varchar(255)` | YES | `` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `agent_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `agent_restricted_postcodes`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `agent_id` | `int(11)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |
| `postcode_city` | `varchar(100)` | YES | `` |  |
| `is_city` | `bit(1)` | YES | `b'0'` |  |

---
### Table: `bagging_manifest_mappings`
- **Primary Key**: NONE
- **Relationships**: `manifest_id` -> `manifests`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `entity_id` | `int(11)` | NO | `` | REQUIRED |
| `manifest_id` | `int(11)` | NO | `` | REQUIRED |
| `manifest_entity_type` | `enum('p','b')` | NO | `p` | REQUIRED |

---
### Table: `bagging_services_mappings`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `bag_id` | `int(11)` | NO | `` | REQUIRED |
| `service_id` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `carrier_agents`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `account_number` | `varchar(45)` | YES | `` |  |
| `name` | `varchar(45)` | YES | `` |  |
| `company` | `varchar(45)` | YES | `` |  |
| `address_line_1` | `varchar(45)` | YES | `` |  |
| `address_line_2` | `varchar(45)` | YES | `` |  |
| `address_line_3` | `varchar(45)` | YES | `` |  |
| `city` | `varchar(45)` | YES | `` |  |
| `country_iso_code` | `varchar(2)` | YES | `` |  |
| `api_username` | `varchar(45)` | YES | `` |  |
| `api_password` | `varchar(45)` | YES | `` |  |
| `ftp_host` | `varchar(45)` | YES | `` |  |
| `ftp_username` | `varchar(45)` | YES | `` |  |
| `ftp_password` | `varchar(45)` | YES | `` |  |
| `integration_type` | `varchar(45)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `created_by` | `varchar(45)` | YES | `` |  |

---
### Table: `carrier_data_file_logs`
- **Primary Key**: NONE
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `carrier_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `agent_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `file_name` | `varchar(50)` | NO | `` | REQUIRED |
| `date_created` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `run_number` | `int(10) unsigned` | NO | `1` | REQUIRED |

---
### Table: `carrier_documents`
- **Primary Key**: NONE
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `carrier_id` | `int(11)` | NO | `` | REQUIRED |
| `document_id` | `int(11)` | NO | `` | REQUIRED |
| `document_name` | `varchar(255)` | NO | `` | REQUIRED |
| `added_by` | `bigint(20)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `carrier_hubs`
- **Primary Key**: NONE
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `carrier_id` | `int(11)` | YES | `` |  |
| `hub` | `varchar(100)` | YES | `` |  |
| `routing_code` | `varchar(45)` | YES | `` |  |
| `company` | `varchar(45)` | YES | `` |  |
| `contact` | `varchar(45)` | YES | `` |  |
| `address_line_1` | `varchar(45)` | YES | `` |  |
| `address_line_2` | `varchar(45)` | YES | `` |  |
| `address_line_3` | `varchar(45)` | YES | `` |  |
| `city` | `varchar(45)` | YES | `` |  |
| `postcode` | `varchar(45)` | YES | `` |  |
| `country_iso_code` | `varchar(3)` | YES | `` |  |

---
### Table: `carrier_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `carrier_service_customize_rules`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `serviceid` -> `services`, `agentid` -> `carriers`, `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `serviceid` | `int(11)` | YES | `` |  |
| `agentid` | `int(11)` | YES | `` |  |
| `user_account_id` | `int(11)` | YES | `` |  |
| `from_weight` | `decimal(10,3)` | YES | `` |  |
| `to_weight` | `decimal(10,3)` | YES | `` |  |
| `status` | `bit(1)` | YES | `b'1'` |  |

---
### Table: `carrier_service_default_rules`
- **Primary Key**: NONE
- **Relationships**: `serviceid` -> `services`, `agentid` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `serviceid` | `int(11)` | YES | `` |  |
| `agentid` | `int(11)` | YES | `` |  |
| `from_weight` | `decimal(10,3)` | YES | `` |  |
| `to_weight` | `decimal(10,3)` | YES | `` |  |
| `is_default` | `bit(1)` | YES | `` |  |
| `agent_type` | `enum('outbound','dispatch')` | NO | `outbound` | REQUIRED |

---
### Table: `carrier_zones`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`, `deleted`
- **Relationships**: `carrier_id` -> `carriers`, `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `carrier_id` | `int(11)` | NO | `` | REQUIRED |
| `service_id` | `int(11) unsigned` | YES | `` |  |
| `name` | `varchar(100)` | NO | `` | REQUIRED |
| `sort_order` | `int(5)` | NO | `0` | REQUIRED |
| `status` | `tinyint(1)` | NO | `1` | REQUIRED |
| `deleted` | `tinyint(1)` | NO | `0` | REQUIRED |
| `date_added` | `datetime` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
### Table: `carrier_zones_countries`
- **Primary Key**: NONE
- **Relationships**: `carrier_zone_id` -> `carrier_zones`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `country_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `carrier_zone_id` | `int(11) unsigned` | NO | `` | REQUIRED |

---
### Table: `carrier_zones_postcodes`
- **Primary Key**: NONE
- **Relationships**: `carrier_zone_id` -> `carrier_zones`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `0` | REQUIRED |
| `carrier_zone_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `postcode` | `varchar(15)` | NO | `` | REQUIRED |

---
### Table: `carriers`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `carrier` | `varchar(45)` | YES | `` |  |
| `logo` | `varchar(45)` | YES | `` |  |
| `cut_off_time` | `varchar(45)` | YES | `` |  |
| `carrier_display_name` | `varchar(45)` | YES | `` |  |
| `status` | `int(1)` | YES | `` |  |
| `country_id` | `int(11)` | YES | `` |  |
| `carrier_id` | `int(11)` | YES | `` |  |
| `currency_code` | `varchar(3)` | YES | `GBP` |  |
| `remotearea_check` | `enum('c','s')` | NO | `c` | REQUIRED |
| `zone_base` | `bit(1)` | YES | `b'0'` |  |
| `zone_type` | `enum('country','postcode')` | YES | `country` |  |
| `on_contract` | `bit(1)` | YES | `b'0'` |  |
| `is_gazetteer` | `tinyint(1)` | NO | `0` | REQUIRED |
| `is_reconcile` | `int(10)` | NO | `0` | REQUIRED |

---
### Table: `consignment_bagging_mappings`
- **Primary Key**: NONE
- **Relationships**: `consignmentid` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `consignmentid` | `int(11)` | YES | `` |  |
| `bagid` | `int(11)` | YES | `` |  |

---
### Table: `consignment_dropoff_mappings`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `dropoff_consignment_id` | `bigint(20)` | NO | `` | REQUIRED |
| `dispatch_consignment_id` | `bigint(20)` | NO | `` | REQUIRED |
| `dropoff_consignment_tracking` | `text` | NO | `` | REQUIRED |
| `dispatch_consignment_tracking` | `text` | NO | `` | REQUIRED |
| `parcel_tracking` | `text` | YES | `` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |

---
### Table: `customized_services_routing_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(11)` | YES | `` |  |
| `logdate` | `datetime` | YES | `` |  |
| `ipaddress` | `varchar(100)` | YES | `` |  |
| `log_id` | `int(11)` | YES | `` |  |
| `message` | `text` | YES | `` |  |

---
### Table: `customized_services_routings`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `country_id` | `int(11)` | NO | `` | REQUIRED |
| `from_weight` | `decimal(10,2)` | YES | `` |  |
| `to_weight` | `decimal(10,2)` | YES | `` |  |
| `status` | `int(1)` | NO | `1` | REQUIRED |
| `customize_service_id` | `int(11)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |

---
### Table: `dx_routings`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `district` | `varchar(45)` | YES | `` |  |
| `sector` | `varchar(45)` | YES | `` |  |
| `depot` | `varchar(45)` | YES | `` |  |
| `depotid` | `varchar(45)` | YES | `` |  |
| `region_id` | `varchar(45)` | YES | `` |  |
| `delivery_method` | `varchar(45)` | YES | `` |  |
| `delivery_method_id` | `varchar(45)` | YES | `` |  |
| `delivery_method_description` | `varchar(45)` | YES | `` |  |

---
### Table: `flight_mappings`
- **Primary Key**: NONE
- **Relationships**: `flight_info_id` -> `flight_infos`, `mawb_id` -> `mawbs`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `flight_info_id` | `int(11)` | YES | `` |  |
| `flight_number` | `varchar(45)` | YES | `` |  |
| `mawb` | `varchar(45)` | YES | `` |  |
| `mawb_id` | `int(11)` | YES | `` |  |
| `is_delete` | `tinyint(1)` | NO | `0` | REQUIRED |

---
### Table: `manifest_consignment_mappings`
- **Primary Key**: NONE
- **Relationships**: `manifestid` -> `manifests`, `consignmentid` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `manifestid` | `int(11)` | NO | `` | REQUIRED |
| `consignmentid` | `int(11)` | NO | `` | REQUIRED |
| `export_mawb` | `varchar(50)` | NO | `` | REQUIRED |

---
### Table: `manifest_entity_mappings`
- **Primary Key**: NONE
- **Relationships**: `manifest_id` -> `manifests`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `entity_id` | `int(11)` | NO | `` | REQUIRED |
| `manifest_id` | `int(11)` | NO | `` | REQUIRED |
| `manifest_entity_type` | `enum('p','b','pl')` | NO | `p` | REQUIRED |

---
### Table: `manifest_service_mappings`
- **Primary Key**: NONE
- **Relationships**: `manifest_id` -> `manifests`, `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `manifest_id` | `bigint(20)` | NO | `` | REQUIRED |
| `service_id` | `bigint(20)` | NO | `` | REQUIRED |

---
### Table: `market_place_documentation_mappings`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | `` | REQUIRED |
| `marketplace_id` | `int(11)` | YES | `` |  |
| `step_title` | `varchar(250)` | YES | `` |  |
| `step_description` | `text` | YES | `` |  |
| `step_image` | `varchar(255)` | YES | `` |  |
| `step_order` | `int(11)` | YES | `` |  |
| `created_at` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `updated_at` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `added_by` | `int(11)` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
### Table: `mawb_flight_document_mappings`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `country_id` | `bigint(20)` | NO | `` | REQUIRED |
| `document_id` | `bigint(20)` | NO | `` | REQUIRED |
| `template_id` | `bigint(20)` | NO | `` | REQUIRED |
| `added_by` | `bigint(20)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | YES | `` |  |

---
### Table: `mawb_parcel_mappings`
- **Primary Key**: NONE
- **Relationships**: `mawb_id` -> `mawbs`, `parcel_id` -> `parcels`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `mawb_id` | `int(11)` | NO | `` | REQUIRED |
| `parcel_id` | `int(11)` | NO | `` | REQUIRED |
| `wharehouse_id` | `int(11)` | NO | `` | REQUIRED |
| `bag_id` | `int(11)` | YES | `` |  |
| `date_added` | `datetime` | YES | `` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |

---
### Table: `pallet_bag_mappings`
- **Primary Key**: NONE
- **Relationships**: `palletid` -> `pallets`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `palletid` | `int(11)` | NO | `` | REQUIRED |
| `bagid` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `pallet_carrier_services`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `carrier_group_id` | `int(11)` | NO | `` | REQUIRED |
| `service_id` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `pallet_carriers`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `name` | `varchar(100)` | YES | `` |  |
| `service_id` | `varchar(100)` | YES | `` |  |

---
### Table: `pallet_entity_mappings`
- **Primary Key**: NONE
- **Relationships**: `pallet_id` -> `pallets`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `pallet_id` | `int(11)` | NO | `` | REQUIRED |
| `entity_id` | `int(11)` | NO | `` | REQUIRED |
| `pallet_entity_type` | `enum('p','b')` | NO | `p` | REQUIRED |
| `pre_sort` | `enum('y','n')` | NO | `n` | REQUIRED |

---
### Table: `parcel_bagging_mappings`
- **Primary Key**: NONE
- **Relationships**: `parcel_id` -> `parcels`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `parcel_id` | `int(11)` | NO | `` | REQUIRED |
| `bag_id` | `int(11)` | NO | `` | REQUIRED |
| `added_by` | `bigint(20)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | NO | `` | REQUIRED |

---
### Table: `partnerservicesroutings`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `product_id` -> `products`, `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `country_id` | `int(11)` | NO | `` | REQUIRED |
| `from_weight` | `decimal(10,2)` | YES | `` |  |
| `to_weight` | `decimal(10,2)` | YES | `` |  |
| `status` | `int(1)` | NO | `1` | REQUIRED |
| `product_id` | `int(11)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |

---
### Table: `post_italia_routings`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `zip_code` | `varchar(45)` | NO | `` | REQUIRED |
| `routing_file` | `varchar(45)` | NO | `` | REQUIRED |
| `province` | `varchar(45)` | NO | `` | REQUIRED |
| `province_iso_code` | `varchar(45)` | NO | `` | REQUIRED |
| `sortation_name` | `varchar(45)` | YES | `` |  |
| `sortation_id` | `varchar(45)` | YES | `` |  |
| `sortation_name_on_bag` | `varchar(45)` | YES | `` |  |

---
### Table: `reamus_product_services`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `reamus_id` | `varchar(19)` | YES | `` |  |
| `product_code` | `varchar(2)` | YES | `` |  |
| `feature_code` | `varchar(5)` | YES | `` |  |
| `exception` | `varchar(5)` | YES | `` |  |

---
### Table: `reamus_services`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `service_id` | `int(11) unsigned` | YES | `` |  |
| `service_description` | `varchar(50)` | YES | `` |  |
| `product_line1` | `varchar(15)` | YES | `` |  |
| `product_line2` | `varchar(35)` | YES | `` |  |
| `product_code` | `varchar(2)` | YES | `` |  |
| `date_code` | `varchar(2)` | YES | `` |  |
| `day_text` | `varchar(1)` | YES | `` |  |
| `time_code` | `varchar(1)` | YES | `` |  |
| `time_text` | `varchar(1)` | YES | `` |  |
| `handling` | `varchar(15)` | YES | `` |  |
| `feature_id` | `varchar(3)` | YES | `` |  |
| `feature_code` | `varchar(2)` | YES | `` |  |
| `file_type` | `varchar(3)` | YES | `` |  |
| `consignment_flag` | `varchar(3)` | YES | `` |  |
| `ds_flag` | `varchar(3)` | YES | `` |  |

---
### Table: `remotearea_charges_carriers`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `remotearea_group_id` | `int(11)` | YES | `` |  |
| `remotearea_charges` | `decimal(10,2)` | YES | `` |  |
| `is_deleted` | `enum('Y','N')` | YES | `N` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `remotearea_charges_services`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `remotearea_group_id` | `int(11)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |
| `remotearea_charges` | `decimal(10,2)` | YES | `` |  |
| `from_weight` | `decimal(10,2)` | YES | `` |  |
| `to_weight` | `decimal(10,2)` | YES | `` |  |
| `formulla` | `varchar(45)` | YES | `` |  |
| `is_deleted` | `enum('Y','N')` | YES | `N` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `service_agent_mappings`
- **Primary Key**: NONE
- **Relationships**: `serviceid` -> `services`, `agentid` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `serviceid` | `int(11)` | YES | `` |  |
| `agentid` | `int(11)` | YES | `` |  |
| `linehaul_agent` | `int(1)` | YES | `0` |  |
| `account_number` | `varchar(45)` | YES | `` |  |
| `api_url` | `varchar(200)` | YES | `` |  |
| `api_username` | `varchar(45)` | YES | `` |  |
| `api_password` | `varchar(45)` | YES | `` |  |
| `ftp_host` | `varchar(45)` | YES | `` |  |
| `ftp_username` | `varchar(45)` | YES | `` |  |
| `ftp_password` | `varchar(45)` | YES | `` |  |
| `integration_type` | `varchar(5)` | YES | `` |  |
| `class_file_name` | `varchar(45)` | YES | `` |  |
| `insurance_charges` | `decimal(8,2)` | YES | `0.00` |  |
| `insurance_cover` | `decimal(8,2)` | YES | `0.00` |  |
| `reroute_charges` | `decimal(8,2)` | YES | `0.00` |  |
| `oversize_charges` | `decimal(8,2)` | YES | `0.00` |  |
| `address_change_charges` | `decimal(8,2)` | YES | `0.00` |  |
| `other_surcharges` | `decimal(8,2)` | YES | `0.00` |  |
| `return_charges` | `decimal(8,2)` | YES | `0.00` |  |
| `relabel_charges` | `decimal(8,2)` | YES | `0.00` |  |
| `wrong_address_charges` | `decimal(8,2)` | YES | `0.00` |  |
| `from_weight` | `decimal(8,2)` | YES | `0.00` |  |
| `to_weight` | `decimal(8,2)` | YES | `0.00` |  |
| `email` | `text` | YES | `` |  |

---
### Table: `service_collection_counties`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `service_id` | `bigint(20)` | NO | `` | REQUIRED |
| `country_id` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `service_constant_values`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `constant_value` | `varchar(250)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |
| `agent_id` | `int(11)` | YES | `` |  |
| `constant_id` | `int(11)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `date_update` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
### Table: `service_constants`
- **Primary Key**: NONE
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `constant` | `varchar(200)` | YES | `` |  |
| `carrier_id` | `int(11)` | YES | `` |  |
| `caption` | `varchar(200)` | YES | `` |  |
| `description` | `varchar(200)` | YES | `` |  |
| `design_control` | `varchar(45)` | YES | `` |  |
| `mandatory` | `bit(1)` | YES | `b'0'` |  |
| `sort_order` | `int(11)` | YES | `` |  |
| `integration_type` | `enum('API','EDI','SOFTWARE')` | YES | `` |  |
| `default_values` | `text` | YES | `` |  |
| `field_size` | `int(11)` | YES | `` |  |

---
### Table: `service_country_ttimes`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `id_country` | `int(11)` | YES | `` |  |
| `id_service` | `int(11)` | YES | `` |  |
| `transit_time` | `int(1)` | YES | `` |  |

---
### Table: `service_documents`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `service_id` | `int(11)` | NO | `` | REQUIRED |
| `document_id` | `int(11)` | NO | `` | REQUIRED |
| `agent_id` | `int(11)` | YES | `0` |  |
| `document_name` | `varchar(255)` | NO | `` | REQUIRED |
| `added_by` | `bigint(20)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `service_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `service_range_mappings`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`, `licence_plate_id` -> `licence_plates`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `service_id` | `int(11)` | YES | `` |  |
| `agent_id` | `int(11)` | YES | `` |  |
| `licence_plate_id` | `int(11)` | YES | `` |  |

---
### Table: `services`
- **Primary Key**: NONE
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | `` | REQUIRED |
| `name` | `varchar(45)` | NO | `` | REQUIRED |
| `code` | `varchar(20)` | NO | `` | REQUIRED |
| `carrier_id` | `int(11)` | NO | `` | REQUIRED |
| `account_number` | `varchar(20)` | YES | `` |  |
| `type` | `varchar(5)` | YES | `` |  |
| `from_weight` | `decimal(10,3)` | YES | `` |  |
| `to_weight` | `decimal(10,3)` | YES | `` |  |
| `wieght_type` | `int(11)` | YES | `1` |  |
| `supplier` | `varchar(100)` | YES | `` |  |
| `service_type` | `enum('D','C','B','DO')` | NO | `D` | REQUIRED |
| `drop_off_service_id` | `bigint(20)` | YES | `` |  |
| `description` | `text` | YES | `` |  |
| `fuel_surcharge_cost` | `decimal(9,2)` | YES | `` |  |
| `fuel_surcharge` | `decimal(9,2)` | NO | `` | REQUIRED |
| `fuel_surcharge_type` | `char(1)` | NO | `` | REQUIRED |
| `max_length` | `decimal(9,2)` | NO | `` | REQUIRED |
| `max_width` | `decimal(9,2)` | NO | `` | REQUIRED |
| `max_height` | `decimal(9,2)` | NO | `` | REQUIRED |
| `max_volumetric_weight` | `decimal(9,2)` | YES | `` |  |
| `volumetric_denominator` | `int(11)` | YES | `5000` |  |
| `send_data_courier` | `tinyint(4)` | YES | `0` |  |
| `is_document` | `tinyint(1)` | YES | `0` |  |
| `friday_only_flag` | `tinyint(1)` | YES | `` |  |
| `saturday_only_flag` | `tinyint(1)` | YES | `0` |  |
| `sunday_only_flag` | `tinyint(1)` | YES | `0` |  |
| `product_owner` | `int(11)` | YES | `` |  |
| `active` | `tinyint(1)` | YES | `1` |  |
| `deletedq` | `tinyint(1)` | YES | `0` |  |
| `added_on` | `datetime` | YES | `` |  |
| `added_by` | `varchar(100)` | YES | `` |  |
| `changed_on` | `datetime` | YES | `` |  |
| `changed_by` | `varchar(100)` | YES | `` |  |
| `uploaded_currency` | `varchar(3)` | YES | `` |  |
| `uploaded_currency_value` | `decimal(10,2)` | YES | `` |  |
| `registration_fee` | `decimal(10,2)` | YES | `0.00` |  |
| `weight_after` | `decimal(10,2)` | YES | `0.00` |  |
| `aditional_charge` | `decimal(10,2)` | YES | `0.00` |  |
| `origin_country` | `int(11)` | YES | `255` |  |
| `is_untrack` | `bit(1)` | YES | `b'0'` |  |
| `account_owner` | `int(11)` | YES | `` |  |
| `remotearea` | `enum('ON_WEIGHT','ON_PIECE')` | YES | `ON_PIECE` |  |
| `carrier_address_limit` | `int(5)` | YES | `30` |  |
| `label_class_name` | `varchar(100)` | YES | `` |  |
| `transit_time` | `int(3)` | YES | `` |  |
| `required_email` | `tinyint(1)` | YES | `0` |  |
| `required_telephone` | `tinyint(1)` | YES | `0` |  |
| `shipment_type` | `enum('LETTER','PARCEL')` | YES | `PARCEL` |  |
| `pre_sort` | `enum('YES','NO')` | YES | `NO` |  |
| `proforma_invoice` | `tinyint(1)` | YES | `0` |  |
| `agent_dispatch` | `enum('Y','N')` | YES | `N` |  |
| `brief_manifest` | `enum('Y','N')` | YES | `N` |  |
| `delivery_mode` | `tinyint(1)` | YES | `` |  |
| `insurance_available` | `tinyint(1)` | YES | `0` |  |
| `vol_wgt_formula` | `varchar(50)` | YES | `` |  |
| `is_remotearea` | `enum('Y','N')` | YES | `N` |  |
| `is_customized` | `bit(1)` | YES | `b'0'` |  |
| `pre_advise` | `enum('Y','N')` | NO | `N` | REQUIRED |
| `pre_alert` | `enum('Y','N')` | NO | `N` | REQUIRED |
| `pre_alert_email` | `text` | YES | `` |  |
| `cut_off_time` | `varchar(5)` | YES | `` |  |
| `label_charges` | `decimal(10,2)` | YES | `0.00` |  |
| `allow_oversize` | `tinyint(4)` | NO | `0` | REQUIRED |
| `allow_overweight` | `tinyint(4)` | NO | `0` | REQUIRED |
| `maximum_allowed_dimension` | `int(4)` | NO | `0` | REQUIRED |
| `maximum_dim_formula` | `varchar(100)` | YES | `` |  |
| `validation_type` | `enum('mail','courier')` | NO | `mail` | REQUIRED |
| `zone_type` | `enum('country','postcode')` | YES | `country` |  |
| `tariff_type` | `enum('multi','single')` | YES | `single` |  |
| `girth` | `decimal(10,2)` | YES | `` |  |
| `girth_formula` | `varchar(255)` | YES | `` |  |
| `mail_type` | `enum('letter','boxable','non-boxable')` | YES | `` |  |
| `mail_option` | `enum('commercial','freight_to_post')` | YES | `` |  |
| `is_reschedulable` | `int(1)` | NO | `0` | REQUIRED |
| `carrier_service_code` | `varchar(50)` | YES | `` |  |
| `is_eori_required` | `int(2)` | NO | `0` | REQUIRED |
| `delivery_type` | `enum('all','economy','priority','')` | NO | `all` | REQUIRED |
| `is_commercials` | `enum('required','not required')` | NO | `not required` | REQUIRED |
| `is_cn` | `enum('required','not required')` | NO | `not required` | REQUIRED |

---
### Table: `services_dpds`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `2_digit_service_code` | `varchar(2)` | YES | `` |  |
| `3_digit_service_code` | `varchar(3)` | YES | `` |  |
| `dpd_product_desc` | `varchar(45)` | YES | `` |  |
| `dpd_label_service` | `varchar(45)` | YES | `` |  |
| `ilk_product_desc` | `varchar(45)` | YES | `` |  |
| `ilk_alternative_service_desc` | `varchar(45)` | YES | `` |  |
| `premium` | `varchar(45)` | YES | `` |  |
| `sec_dpd` | `varchar(45)` | YES | `` |  |
| `sec_ilk` | `varchar(45)` | YES | `` |  |
| `ilk_max_parcels_per_con` | `int(11)` | YES | `` |  |
| `ilk_max_weight_per_parcel` | `int(11)` | YES | `` |  |
| `dpd_max_parcels_per_con` | `int(11)` | YES | `` |  |
| `dpd_max_weight_per_parcel` | `int(11)` | YES | `` |  |

---
### Table: `tariff_service_charges`
- **Primary Key**: NONE
- **Relationships**: `tariff_id` -> `tariffs`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `tariff_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `tariff_charges_types_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `charge` | `decimal(10,2) unsigned` | NO | `0.00` | REQUIRED |
| `charge_type` | `enum('fixed','percentage')` | YES | `fixed` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `vehicle_parcel_mappings`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_active`
- **Relationships**: `parcel_id` -> `parcels`, `vehicle_id` -> `vehicles`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `parcel_id` | `bigint(20)` | NO | `` | REQUIRED |
| `vehicle_id` | `bigint(20)` | YES | `` |  |
| `driver_id` | `bigint(20)` | YES | `` |  |
| `pickup_date` | `date` | NO | `` | REQUIRED |
| `date_added` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `added_by` | `bigint(20)` | YES | `` |  |
| `is_active` | `bit(1)` | YES | `b'1'` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |

---
## Module: Consignments & Tracking

### Table: `auto_tracking`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | `` | REQUIRED |
| `next_number` | `int(10)` | YES | `` |  |
| `range_end` | `int(10)` | YES | `` |  |
| `increment_date` | `datetime` | YES | `` |  |
| `service_name` | `varchar(45)` | YES | `` |  |

---
### Table: `baggings`
- **Primary Key**: NONE
- **Relationships**: `manifestid` -> `manifests`, `user_id` -> `users`, `serviceid` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `bagnumber` | `varchar(45)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `csv` | `varchar(200)` | YES | `` |  |
| `pdf` | `varchar(200)` | YES | `` |  |
| `manifestid` | `int(11)` | YES | `` |  |
| `account` | `varchar(45)` | YES | `` |  |
| `user_id` | `int(11)` | YES | `` |  |
| `manifest_pdf` | `varchar(200)` | YES | `` |  |
| `bag_status` | `tinyint(2)` | YES | `0` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `isdeleted` | `tinyint(2)` | YES | `0` |  |
| `service` | `varchar(200)` | YES | `` |  |
| `serviceid` | `int(11)` | YES | `` |  |
| `country` | `varchar(45)` | YES | `` |  |
| `country_iso_code` | `varchar(2)` | YES | `` |  |
| `bag_type` | `varchar(45)` | YES | `` |  |
| `actual_weight` | `decimal(10,2)` | YES | `` |  |
| `length` | `decimal(10,2)` | YES | `` |  |
| `width` | `decimal(10,2)` | YES | `` |  |
| `height` | `decimal(10,2)` | YES | `` |  |
| `pieces` | `int(11)` | YES | `` |  |
| `weight` | `decimal(10,3)` | YES | `` |  |
| `bag_label` | `varchar(255)` | YES | `` |  |
| `bag_source_country_id` | `int(11)` | YES | `` |  |
| `bag_source_warehouse_id` | `int(11)` | YES | `` |  |
| `bag_destination_country_id` | `int(11)` | YES | `` |  |
| `bag_destination_warehouse_id` | `int(11)` | YES | `` |  |
| `is_closed` | `bit(1)` | YES | `b'0'` |  |
| `closed_by` | `bigint(20)` | YES | `` |  |
| `closed_date` | `datetime` | YES | `` |  |
| `reopen_by` | `bigint(20)` | YES | `` |  |
| `reopen_date` | `datetime` | YES | `` |  |
| `bag_manifest` | `varchar(255)` | YES | `` |  |
| `bag_value` | `enum('hv','lv','mv')` | NO | `lv` | REQUIRED |

---
### Table: `consignment_billing_hold_logs`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `user_account_id_from` | `int(11)` | YES | `` |  |
| `user_account_id_to` | `int(11)` | YES | `` |  |
| `status` | `enum('hold','unhold')` | YES | `` |  |
| `reason` | `varchar(500)` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `date_added` | `timestamp` | YES | `current_timestamp()` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |

---
### Table: `consignment_billing_holds`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `user_account_id_from` | `int(11)` | YES | `` |  |
| `user_account_id_to` | `int(11)` | YES | `` |  |
| `reason_for_hold` | `varchar(500)` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `date_added` | `timestamp` | YES | `current_timestamp()` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |

---
### Table: `consignment_charges`
- **Primary Key**: NONE
- **Relationships**: `tariff_id` -> `tariffs`, `account_id` -> `user_accounts`, `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `tariff_id` | `int(11)` | YES | `0` |  |
| `account_id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | NO | `` | REQUIRED |
| `invoice_id` | `int(11)` | YES | `` |  |
| `charge_type_id` | `int(11)` | NO | `` | REQUIRED |
| `agent_id` | `int(11)` | YES | `` |  |
| `cost_type` | `enum('customer','agent','purchase_invoice')` | NO | `customer` | REQUIRED |
| `cost` | `decimal(11,2)` | YES | `0.00` |  |
| `cost_currency` | `varchar(3)` | YES | `` |  |
| `cost_supplier_currency` | `decimal(11,2)` | YES | `` |  |
| `supplier_currency` | `varchar(3)` | YES | `` |  |
| `cost_company_currency` | `decimal(11,2)` | YES | `` |  |
| `company_currency` | `varchar(3)` | YES | `` |  |
| `description` | `varchar(255)` | YES | `` |  |
| `changes_reference` | `varchar(110)` | YES | `` |  |
| `added_by` | `int(11)` | NO | `` | REQUIRED |
| `added_date` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `consignment_charges_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(40)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(10)` | NO | `A` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `consignment_charges_types`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `title` | `varchar(255)` | NO | `` | REQUIRED |
| `charges_key` | `varchar(255)` | NO | `` | REQUIRED |
| `charge_type` | `enum('both','customer','agent')` | NO | `both` | REQUIRED |
| `apply_per_kg` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `is_extra_charge` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `is_vat` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `has_account_default_value` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `is_replace_charges` | `bit(1)` | YES | `b'0'` |  |
| `status` | `tinyint(1)` | NO | `1` | REQUIRED |
| `is_delete` | `bit(1)` | YES | `b'0'` |  |
| `added_by` | `int(11)` | NO | `` | REQUIRED |
| `added_date` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `consignment_collections`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `consignment_id` | `int(20) unsigned` | NO | `` | REQUIRED |
| `sender_company` | `varchar(50)` | YES | `` |  |
| `sender_contact` | `varchar(50)` | YES | `` |  |
| `sender_email` | `varchar(50)` | YES | `` |  |
| `sender_address_line_1` | `varchar(50)` | YES | `` |  |
| `sender_address_line_2` | `varchar(50)` | YES | `` |  |
| `sender_address_line_3` | `varchar(50)` | YES | `` |  |
| `sender_city` | `varchar(50)` | YES | `` |  |
| `sender_country_iso_code` | `char(3)` | YES | `` |  |
| `sender_postcode` | `varchar(10)` | YES | `` |  |
| `sender_telephone` | `varchar(20)` | YES | `` |  |
| `date_collection` | `int(11)` | YES | `` |  |
| `earliest_latest_time` | `int(3) unsigned` | YES | `` |  |

---
### Table: `consignment_details`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `custom_export_number` | `varchar(45)` | YES | `` |  |

---
### Table: `consignment_hold_logs`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `user_account_id_from` | `int(11)` | YES | `` |  |
| `user_account_id_to` | `int(11)` | YES | `` |  |
| `status` | `enum('hold','unhold')` | YES | `` |  |
| `reason` | `varchar(500)` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `date_added` | `timestamp` | YES | `current_timestamp()` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |

---
### Table: `consignment_holds`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(11)` | YES | `` |  |
| `comments` | `varchar(500)` | YES | `` |  |
| `tracking_number` | `varchar(45)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `action` | `varchar(45)` | YES | `` |  |
| `reason_tag` | `varchar(45)` | YES | `` |  |
| `weight` | `varchar(45)` | YES | `` |  |
| `width` | `varchar(45)` | YES | `` |  |
| `height` | `varchar(45)` | YES | `` |  |
| `length` | `varchar(45)` | YES | `` |  |
| `volume` | `varchar(45)` | YES | `` |  |
| `image` | `varchar(200)` | YES | `` |  |
| `account` | `varchar(45)` | YES | `` |  |

---
### Table: `consignment_hscodes`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `hscode` | `varchar(45)` | YES | `` |  |

---
### Table: `consignment_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(40)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(10)` | NO | `A` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `consignment_pods`
- **Primary Key**: NONE
- **Relationships**: `consignmentid` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignmentid` | `int(11)` | YES | `` |  |
| `signature` | `varchar(45)` | YES | `` |  |
| `pod_date` | `varchar(45)` | YES | `` |  |
| `pod_image` | `varchar(45)` | YES | `` |  |

---
### Table: `consignment_relabels`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`, `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `old_tracking_no` | `varchar(45)` | YES | `` |  |
| `new_tracking_no` | `varchar(45)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `userid` | `int(11)` | YES | `` |  |
| `old_consignment_data` | `text` | YES | `` |  |
| `old_parcel_tracking_no` | `text` | YES | `` |  |
| `old_new_tracking_mapping` | `text` | YES | `` |  |

---
### Table: `consignment_status_logs`
- **Primary Key**: NONE
- **Relationships**: `parcel_id` -> `parcels`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `parcel_id` | `bigint(20)` | NO | `` | REQUIRED |
| `old_status` | `varchar(100)` | NO | `` | REQUIRED |
| `new_status` | `varchar(100)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `date_added` | `timestamp` | NO | `current_timestamp()` | REQUIRED |

---
### Table: `consignments`
- **Primary Key**: NONE
- **Relationships**: `user_id` -> `users`, `service_id` -> `services`, `warehouse_id` -> `warehouses`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `agent_id` | `int(11)` | YES | `51` |  |
| `user_id` | `int(11)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |
| `customized_service_id` | `int(11)` | YES | `0` |  |
| `warehouse_user_id` | `int(11)` | YES | `` |  |
| `warehouse_id` | `int(11)` | YES | `` |  |
| `sales_pot_id` | `bigint(20)` | YES | `` |  |
| `invoice_id` | `int(11)` | YES | `` |  |
| `credit_id` | `int(11)` | YES | `` |  |
| `is_invoiced` | `int(1)` | YES | `0` |  |
| `invoice_type` | `enum('INV','MNI')` | YES | `` |  |
| `shipment_status` | `int(4)` | YES | `0` |  |
| `shipment_type` | `enum('C','D','P','DO')` | YES | `D` |  |
| `awb` | `varchar(30)` | YES | `` |  |
| `consignment_status` | `varchar(20)` | YES | `` |  |
| `return_awb` | `varchar(30)` | YES | `` |  |
| `hawb` | `varchar(40)` | NO | `` | REQUIRED |
| `mawb` | `varchar(40)` | YES | `` |  |
| `service_name` | `varchar(50)` | YES | `` |  |
| `reference` | `varchar(20)` | YES | `` |  |
| `date_created` | `timestamp` | YES | `current_timestamp()` |  |
| `date_label_created` | `int(11)` | YES | `` |  |
| `date_booked` | `int(11)` | YES | `` |  |
| `date_delivered` | `int(11)` | YES | `` |  |
| `is_customer_manifested` | `int(1)` | YES | `0` |  |
| `booked_file_id` | `varchar(50)` | NO | `0` | REQUIRED |
| `company` | `varchar(100)` | YES | `` |  |
| `contact` | `varchar(100)` | YES | `` |  |
| `address_line_1` | `varchar(50)` | YES | `` |  |
| `address_line_2` | `varchar(50)` | YES | `` |  |
| `address_line_3` | `varchar(50)` | YES | `` |  |
| `city` | `varchar(50)` | YES | `` |  |
| `state` | `varchar(45)` | YES | `` |  |
| `postcode` | `varchar(15)` | YES | `` |  |
| `country_id` | `int(11)` | YES | `` |  |
| `telephone` | `varchar(17)` | YES | `` |  |
| `number_pieces` | `int(3) unsigned` | YES | `` |  |
| `weight_type` | `varchar(2)` | YES | `PP` |  |
| `weight` | `decimal(8,3) unsigned` | YES | `` |  |
| `update_weight` | `decimal(8,3)` | YES | `` |  |
| `fake_weight` | `decimal(8,3)` | YES | `` |  |
| `charge_weight` | `decimal(8,3)` | YES | `` |  |
| `vol_weight` | `decimal(8,3)` | YES | `` |  |
| `vol_demonimator` | `int(11)` | YES | `` |  |
| `hv_lv` | `enum('L','H','M')` | YES | `` |  |
| `description` | `varchar(255)` | YES | `` |  |
| `notes` | `varchar(255)` | YES | `` |  |
| `value` | `decimal(10,2) unsigned` | YES | `` |  |
| `currency` | `varchar(3)` | YES | `` |  |
| `sender_name` | `varchar(45)` | NO | `` | REQUIRED |
| `username` | `varchar(45)` | YES | `` |  |
| `sender_checked` | `int(1)` | YES | `0` |  |
| `message` | `varchar(400)` | YES | `` |  |
| `sorter_image` | `varchar(200)` | YES | `` |  |
| `label_file` | `varchar(255)` | YES | `` |  |
| `is_doc` | `int(1)` | YES | `0` |  |
| `email` | `varchar(45)` | YES | `` |  |
| `itemtype` | `varchar(60)` | YES | `` |  |
| `routing_code` | `varchar(3)` | YES | `` |  |
| `routing_code_eur` | `varchar(45)` | YES | `` |  |
| `other_routing_code` | `varchar(250)` | YES | `` |  |
| `billing_hold` | `int(1)` | YES | `0` |  |
| `send_courier_data` | `int(1)` | YES | `0` |  |
| `remote_charges` | `int(1)` | YES | `0` |  |
| `reinvoices` | `int(1)` | YES | `0` |  |
| `optimus_sorter` | `int(1)` | YES | `0` |  |
| `full_pallet` | `int(2)` | YES | `0` |  |
| `half_pallet` | `int(2)` | YES | `0` |  |
| `quarter_pallet` | `int(2)` | YES | `0` |  |
| `date_scanned` | `datetime` | YES | `` |  |
| `consignment_type` | `enum('return','outbound')` | NO | `outbound` | REQUIRED |
| `api_uuid` | `varchar(200)` | YES | `` |  |
| `sender_company` | `varchar(100)` | YES | `` |  |
| `sender_email` | `varchar(45)` | YES | `` |  |
| `sender_telephone` | `varchar(17)` | YES | `` |  |
| `sender_address_line_1` | `varchar(255)` | YES | `` |  |
| `sender_address_line_2` | `varchar(255)` | YES | `` |  |
| `sender_address_line_3` | `varchar(255)` | YES | `` |  |
| `sender_city` | `varchar(50)` | YES | `` |  |
| `sender_postcode` | `varchar(15)` | YES | `` |  |
| `sender_country_id` | `int(11)` | YES | `` |  |
| `sender_state` | `varchar(45)` | YES | `` |  |
| `collection_date` | `date` | YES | `` |  |
| `collection_start_time` | `varchar(5)` | YES | `` |  |
| `collection_end_time` | `varchar(5)` | YES | `` |  |
| `collection_confirmation_no` | `varchar(45)` | YES | `` |  |
| `created_from` | `enum('web','api','csv')` | YES | `web` |  |
| `is_white_label` | `tinyint(4)` | NO | `0` | REQUIRED |
| `is_dead_weight_chargable` | `tinyint(4)` | YES | `0` |  |
| `is_customer_billable` | `int(1)` | NO | `0` | REQUIRED |
| `ioss_number` | `varchar(50)` | NO | `0` | REQUIRED |
| `eori_number` | `varchar(50)` | NO | `0` | REQUIRED |
| `vat_number` | `varchar(50)` | NO | `0` | REQUIRED |
| `is_over_size_chargable` | `int(5)` | NO | `0` | REQUIRED |
| `is_insured` | `int(1)` | NO | `0` | REQUIRED |
| `destination_warehouse_id` | `int(5)` | YES | `0` |  |
| `consignment_seller` | `text` | YES | `` |  |

---
### Table: `cpost_manifests`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `file_name` | `varchar(200)` | YES | `` |  |
| `manifest_date` | `datetime` | YES | `` |  |

---
### Table: `csv_tracking_templates`
- **Primary Key**: NONE
- **Relationships**: `user_id` -> `users`, `user_account_id` -> `user_accounts`, `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `user_id` | `bigint(20)` | NO | `` | REQUIRED |
| `user_account_id` | `bigint(20)` | NO | `` | REQUIRED |
| `template_name` | `varchar(255)` | NO | `` | REQUIRED |
| `template` | `text` | NO | `` | REQUIRED |
| `added_by` | `bigint(20)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | NO | `` | REQUIRED |
| `update_by` | `bigint(20)` | YES | `` |  |
| `update_date` | `datetime` | YES | `` |  |
| `service_id` | `bigint(20)` | YES | `` |  |

---
### Table: `import_csv_consignment_temps`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `user_id` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `date_added` | `date` | YES | `` |  |
| `shipper_country_iso` | `varchar(50)` | YES | `` |  |
| `receiver_country_iso` | `varchar(50)` | YES | `` |  |
| `service_code` | `varchar(50)` | YES | `` |  |
| `order_reference` | `varchar(50)` | YES | `` |  |
| `shipper_company` | `varchar(50)` | YES | `` |  |
| `shipper_contact` | `varchar(50)` | YES | `` |  |
| `shipper_email` | `varchar(50)` | YES | `` |  |
| `shipper_telephone` | `varchar(50)` | YES | `` |  |
| `shipper_address_line_1` | `varchar(50)` | YES | `` |  |
| `shipper_address_line_2` | `varchar(50)` | YES | `` |  |
| `shipper_address_line_3` | `varchar(50)` | YES | `` |  |
| `shipper_city` | `varchar(50)` | YES | `` |  |
| `shipper_state` | `varchar(50)` | YES | `` |  |
| `shipper_postcode` | `varchar(50)` | YES | `` |  |
| `receiver_company` | `varchar(50)` | YES | `` |  |
| `receiver_contact` | `varchar(50)` | YES | `` |  |
| `receiver_email` | `varchar(50)` | YES | `` |  |
| `receiver_telephone` | `varchar(50)` | YES | `` |  |
| `receiver_address_line_1` | `varchar(50)` | YES | `` |  |
| `receiver_address_line_2` | `varchar(50)` | YES | `` |  |
| `receiver_address_line_3` | `varchar(50)` | YES | `` |  |
| `receiver_city` | `varchar(50)` | YES | `` |  |
| `receiver_state` | `varchar(50)` | YES | `` |  |
| `receiver_postcode` | `varchar(50)` | YES | `` |  |
| `reference` | `varchar(50)` | YES | `` |  |
| `items_value` | `decimal(10,2)` | YES | `` |  |
| `items_currency` | `varchar(5)` | YES | `` |  |
| `item_type` | `varchar(50)` | YES | `` |  |
| `note` | `text` | YES | `` |  |
| `description` | `text` | YES | `` |  |
| `bag_number` | `varchar(50)` | YES | `` |  |
| `tracking_number` | `varchar(20)` | YES | `` |  |
| `mawb_number` | `varchar(50)` | YES | `` |  |
| `flight_number` | `varchar(50)` | YES | `` |  |
| `status` | `enum('0','1')` | YES | `0` |  |
| `is_complete` | `enum('0','1')` | YES | `0` |  |
| `batch_number` | `varchar(50)` | YES | `` |  |
| `user_id` | `bigint(20)` | YES | `` |  |
| `message` | `text` | YES | `` |  |
| `weight` | `text` | YES | `` |  |
| `length` | `text` | YES | `` |  |
| `height` | `text` | YES | `` |  |
| `width` | `text` | YES | `` |  |
| `itemvalue` | `text` | YES | `` |  |
| `parcel_item_desc` | `varchar(255)` | YES | `` |  |
| `parcel_item_sku` | `varchar(255)` | YES | `` |  |
| `parcel_item_url` | `varchar(255)` | YES | `` |  |
| `parcel_item_quantity` | `int(5)` | YES | `` |  |
| `parcel_item_value` | `decimal(10,2)` | YES | `` |  |
| `parcel_item_weight` | `decimal(10,2)` | YES | `` |  |
| `parcel_item_hs_code` | `varchar(50)` | YES | `` |  |
| `parcel_item_manufacture_country` | `varchar(50)` | YES | `` |  |
| `eori_number` | `varchar(50)` | YES | `` |  |
| `vat_number` | `varchar(50)` | YES | `` |  |
| `ioss_number` | `varchar(50)` | YES | `` |  |

---
### Table: `item_details`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`, `session_id` -> `sessions`, `user_id` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | NO | `` | REQUIRED |
| `session_id` | `varchar(50)` | NO | `` | REQUIRED |
| `parcel_count` | `int(5)` | NO | `` | REQUIRED |
| `item_detail` | `text` | NO | `` | REQUIRED |
| `user_id` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `label_files`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `file_name` | `varchar(100)` | YES | `` |  |
| `account_number` | `varchar(30)` | YES | `` |  |
| `hawb_list` | `text` | YES | `` |  |
| `created_date` | `datetime` | YES | `` |  |
| `error_list` | `varchar(255)` | YES | `` |  |

---
### Table: `manifests`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`
- **Relationships**: `user_id` -> `users`, `service_id` -> `services`, `pickup_id` -> `pickups`, `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `user_id` | `int(11)` | YES | `` |  |
| `file_name` | `varchar(500)` | YES | `` |  |
| `label_link` | `varchar(100)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `pieces` | `varchar(45)` | YES | `` |  |
| `agent_id` | `bigint(20)` | YES | `` |  |
| `weight` | `decimal(10,3)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |
| `handling` | `varchar(200)` | YES | `` |  |
| `pdf_file` | `varchar(500)` | YES | `` |  |
| `flight_number` | `varchar(45)` | YES | `` |  |
| `mawb` | `varchar(45)` | YES | `` |  |
| `type` | `varchar(45)` | YES | `` |  |
| `collection_comment` | `text` | YES | `` |  |
| `collection_date` | `datetime` | YES | `` |  |
| `collection_date_to` | `datetime` | YES | `` |  |
| `pickup_date` | `datetime` | YES | `` |  |
| `delivery_note` | `text` | YES | `` |  |
| `signature` | `varchar(100)` | YES | `` |  |
| `pickup_id` | `int(11)` | YES | `` |  |
| `route_warehouse_id` | `int(11)` | YES | `` |  |
| `routing_email_date` | `datetime` | YES | `` |  |
| `date_received` | `datetime` | YES | `` |  |
| `received_by` | `varchar(45)` | YES | `` |  |
| `name_of_driver` | `varchar(100)` | YES | `` |  |
| `licence_number` | `varchar(100)` | YES | `` |  |
| `account_owner` | `varchar(45)` | YES | `` |  |
| `number_bag` | `varchar(45)` | YES | `` |  |
| `product` | `varchar(500)` | YES | `` |  |
| `carrier_note` | `varchar(5000)` | YES | `` |  |
| `carrier_pdf` | `varchar(500)` | YES | `` |  |
| `carrier_id` | `int(11)` | YES | `` |  |
| `is_deleted` | `enum('Y','N')` | YES | `N` |  |
| `is_dispatched` | `enum('Y','N')` | NO | `N` | REQUIRED |
| `is_send_email` | `enum('Y','N')` | NO | `N` | REQUIRED |
| `manifest_by` | `enum('operation','client')` | NO | `client` | REQUIRED |

---
### Table: `parcel_iteams`
- **Primary Key**: NONE
- **Relationships**: `parcel_id` -> `parcels`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `parcel_id` | `int(11)` | NO | `` | REQUIRED |
| `iteam_name` | `varchar(255)` | YES | `` |  |
| `iteam_weight` | `decimal(10,2)` | YES | `` |  |
| `iteam_weight_unit` | `enum('kg','pound')` | YES | `kg` |  |
| `iteam_value` | `int(11)` | YES | `` |  |
| `iteam_quantity` | `int(11)` | YES | `` |  |
| `iteam_country_id` | `int(3)` | YES | `` |  |
| `iteam_description` | `varchar(255)` | YES | `` |  |

---
### Table: `parcel_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `0` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `parcelforce_datafile_ids`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `file_name` | `varchar(45)` | YES | `` |  |
| `sent_date` | `datetime` | YES | `` |  |
| `file_name_id` | `int(5)` | YES | `` |  |

---
### Table: `parcelforce_depo_details`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `depo_name` | `varchar(200)` | YES | `` |  |
| `depo_short_name` | `varchar(45)` | YES | `` |  |
| `postcode` | `varchar(45)` | YES | `` |  |
| `route_number` | `varchar(45)` | YES | `` |  |
| `pfw_ect` | `varchar(45)` | YES | `` |  |
| `pfw_lat` | `varchar(45)` | YES | `` |  |
| `pfw_lct` | `varchar(45)` | YES | `` |  |

---
### Table: `parcelforce_hub_details`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `depo_name` | `varchar(300)` | YES | `` |  |
| `depo_number` | `varchar(45)` | YES | `` |  |
| `mon_hub_24` | `varchar(45)` | YES | `` |  |
| `mon_chute_24` | `varchar(45)` | YES | `` |  |
| `mon_hub_48` | `varchar(45)` | YES | `` |  |
| `mon_chute_48` | `varchar(45)` | YES | `` |  |
| `tue_hub_24` | `varchar(45)` | YES | `` |  |
| `tue_chute_24` | `varchar(45)` | YES | `` |  |
| `tue_hub_48` | `varchar(45)` | YES | `` |  |
| `tue_chute_48` | `varchar(45)` | YES | `` |  |
| `wed_hub_24` | `varchar(45)` | YES | `` |  |
| `wed_chute_24` | `varchar(45)` | YES | `` |  |
| `wed_hub_48` | `varchar(45)` | YES | `` |  |
| `wed_chute_48` | `varchar(45)` | YES | `` |  |
| `thu_hub_24` | `varchar(45)` | YES | `` |  |
| `thu_chute_24` | `varchar(45)` | YES | `` |  |
| `thu_hub_48` | `varchar(45)` | YES | `` |  |
| `thu_chute_48` | `varchar(45)` | YES | `` |  |
| `fri_hub_24` | `varchar(45)` | YES | `` |  |
| `fri_chute_24` | `varchar(45)` | YES | `` |  |
| `fri_hub_48` | `varchar(45)` | YES | `` |  |
| `fri_chute_48` | `varchar(45)` | YES | `` |  |
| `sat_hub_24` | `varchar(45)` | YES | `` |  |
| `sat_chute_24` | `varchar(45)` | YES | `` |  |
| `sat_hub_48` | `varchar(45)` | YES | `` |  |
| `sat_chute_48` | `varchar(45)` | YES | `` |  |
| `sun_hub_24` | `varchar(45)` | YES | `` |  |
| `sun_chute_24` | `varchar(45)` | YES | `` |  |
| `sun_hub_48` | `varchar(45)` | YES | `` |  |
| `sun_chute_48` | `varchar(45)` | YES | `` |  |
| `sat_delivery_hub` | `varchar(45)` | YES | `` |  |
| `sat_delivery_chute` | `varchar(45)` | YES | `` |  |

---
### Table: `parcelforu_pickup_points`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `name` | `varchar(100)` | YES | `` |  |
| `company` | `varchar(100)` | YES | `` |  |
| `address_line_1` | `varchar(250)` | YES | `` |  |
| `city` | `varchar(45)` | YES | `` |  |
| `postcode` | `varchar(45)` | YES | `` |  |
| `country_iso` | `varchar(2)` | YES | `` |  |
| `statuscode` | `varchar(2)` | YES | `` |  |
| `status_description` | `varchar(100)` | YES | `` |  |
| `latitude` | `varchar(45)` | YES | `` |  |
| `longitude` | `varchar(45)` | YES | `` |  |
| `mon` | `varchar(45)` | YES | `` |  |
| `tue` | `varchar(45)` | YES | `` |  |
| `wed` | `varchar(45)` | YES | `` |  |
| `thu` | `varchar(45)` | YES | `` |  |
| `fri` | `varchar(45)` | YES | `` |  |
| `sat` | `varchar(45)` | YES | `` |  |
| `sun` | `varchar(45)` | YES | `` |  |
| `label_routing` | `varchar(45)` | YES | `` |  |
| `branch_id` | `varchar(45)` | YES | `` |  |

---
### Table: `parcels`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `consignment_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `tracking_number` | `varchar(32)` | YES | `` |  |
| `do_tracking_number` | `varchar(32)` | YES | `` |  |
| `length` | `decimal(10,2) unsigned` | NO | `0.00` | REQUIRED |
| `width` | `decimal(10,2) unsigned` | NO | `0.00` | REQUIRED |
| `height` | `decimal(10,2) unsigned` | NO | `0.00` | REQUIRED |
| `weight` | `decimal(4,2)` | NO | `` | REQUIRED |
| `description` | `text` | YES | `` |  |
| `parcel_message` | `text` | YES | `` |  |
| `qty` | `varchar(45)` | YES | `` |  |
| `commoditycode` | `varchar(300)` | YES | `` |  |
| `hscode` | `varchar(300)` | YES | `` |  |
| `grossweight` | `decimal(10,2)` | YES | `` |  |
| `pweight` | `varchar(45)` | YES | `` |  |
| `itemvalue` | `varchar(300)` | YES | `` |  |
| `number_item` | `int(2)` | YES | `` |  |
| `tarrif_no` | `varchar(45)` | YES | `` |  |
| `update_weight` | `decimal(10,2)` | YES | `` |  |
| `owe_status_code` | `varchar(200)` | YES | `` |  |
| `chute_sorted` | `int(4)` | YES | `` |  |
| `parcel_status_code` | `int(3)` | YES | `` |  |
| `routing_code` | `varchar(45)` | YES | `` |  |
| `last_tracking_update` | `datetime` | YES | `` |  |
| `parcel_item_desc` | `text` | YES | `` |  |
| `parcel_label` | `varchar(255)` | YES | `` |  |
| `itemsku` | `varchar(255)` | YES | `` |  |
| `itemurl` | `varchar(255)` | YES | `` |  |
| `sort_type` | `varchar(50)` | YES | `` |  |

---
### Table: `rack_shelf_items`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `goods_name` | `varchar(255)` | NO | `` | REQUIRED |
| `tracking_number` | `varchar(45)` | YES | `` |  |
| `description` | `text` | YES | `` |  |
| `weight` | `decimal(11,2)` | NO | `` | REQUIRED |
| `dimension` | `varchar(50)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | NO | `` | REQUIRED |
| `added_by` | `int(11)` | NO | `` | REQUIRED |
| `updated_date` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `updated_by` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `tracking_data`
- **Primary Key**: NONE
- **Relationships**: `user_id` -> `users`, `warehouse_id` -> `warehouses`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `entity_id` | `int(11)` | NO | `` | REQUIRED |
| `entity_type` | `enum('parcel','shipment')` | NO | `parcel` | REQUIRED |
| `tracking_number` | `varchar(45)` | YES | `` |  |
| `user_id` | `int(11)` | YES | `` |  |
| `track_point` | `varchar(100)` | YES | `` |  |
| `date_created` | `timestamp` | YES | `` |  |
| `ip_address` | `varchar(45)` | YES | `` |  |
| `status_code_id` | `int(11)` | YES | `` |  |
| `warehouse_id` | `int(11)` | YES | `` |  |
| `pod_image` | `varchar(200)` | YES | `` |  |
| `carrier_code` | `varchar(100)` | YES | `` |  |
| `carrier_desc` | `varchar(255)` | YES | `` |  |
| `signatory` | `varchar(255)` | YES | `` |  |
| `date_added` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `parcel_image` | `varchar(255)` | YES | `` |  |
| `latitude` | `varchar(255)` | YES | `` |  |
| `longitude` | `varchar(255)` | YES | `` |  |

---
### Table: `tracking_estimated_times`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `handeling_code` | `varchar(45)` | YES | `` |  |
| `country_iso` | `varchar(45)` | YES | `` |  |
| `estimated_time` | `varchar(255)` | YES | `` |  |

---
### Table: `tracking_status_codes`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `status_code` | `varchar(45)` | YES | `` |  |
| `status_code_map` | `varchar(45)` | YES | `` |  |

---
## Module: Settings & Logs

### Table: `bag_scan_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `cs_logs`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`, `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `internal_message` | `text` | YES | `` |  |
| `customer_message` | `text` | YES | `` |  |
| `cust_mail` | `varchar(45)` | YES | `` |  |
| `agent_mail` | `varchar(45)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `reminder` | `varchar(45)` | YES | `` |  |
| `reminder_expiry` | `datetime` | YES | `` |  |
| `userid` | `int(11)` | YES | `` |  |

---
### Table: `hawb_logs`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `hawb` | `varchar(45)` | YES | `` |  |
| `status` | `varchar(10)` | YES | `` |  |
| `date_created` | `timestamp` | YES | `current_timestamp()` |  |

---
### Table: `log_rack_shelves`
- **Primary Key**: NONE
- **Relationships**: `rack_shelf_item_id` -> `rack_shelf_items`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `rack_shelf_id` | `bigint(20)` | NO | `` | REQUIRED |
| `rack_shelf_item_id` | `bigint(20)` | NO | `` | REQUIRED |
| `customer_id` | `int(11)` | NO | `` | REQUIRED |
| `remarks` | `text` | YES | `` |  |
| `in_date` | `datetime` | NO | `` | REQUIRED |
| `in_by` | `int(11)` | NO | `` | REQUIRED |
| `out_date` | `datetime` | YES | `` |  |
| `out_by` | `int(11)` | YES | `` |  |

---
### Table: `login_requests`
- **Primary Key**: NONE
- **Relationships**: `user_id` -> `users`, `session_id` -> `sessions`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `user_id` | `int(11)` | YES | `` |  |
| `user_name` | `varchar(45)` | YES | `` |  |
| `ip_address` | `varchar(45)` | YES | `` |  |
| `user_agent` | `text` | YES | `` |  |
| `login_status` | `tinyint(1)` | NO | `0` | REQUIRED |
| `login_time` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `logout_time` | `timestamp` | YES | `` |  |
| `session_id` | `varchar(45)` | YES | `` |  |

---
### Table: `product_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `product_routine_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(11)` | YES | `` |  |
| `logdate` | `datetime` | YES | `` |  |
| `ipaddress` | `varchar(100)` | YES | `` |  |
| `log_id` | `int(11)` | YES | `` |  |
| `message` | `text` | YES | `` |  |

---
### Table: `remoteareas_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `report_customize_settings`
- **Primary Key**: NONE
- **Relationships**: `account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `account_id` | `int(11)` | YES | `` |  |
| `report_title` | `varchar(100)` | YES | `` |  |
| `report_key` | `varchar(100)` | YES | `` |  |
| `fields_data` | `longtext` | YES | `` |  |
| `date_added` | `datetime` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
### Table: `sales_call_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(10)` | NO | `` | REQUIRED |
| `date_call` | `date` | YES | `` |  |
| `meeting_date` | `timestamp` | YES | `` |  |
| `customer_code` | `varchar(45)` | YES | `` |  |
| `company` | `varchar(100)` | YES | `` |  |
| `contact` | `varchar(100)` | YES | `` |  |
| `address` | `varchar(250)` | YES | `` |  |
| `telephone` | `varchar(20)` | YES | `` |  |
| `email` | `varchar(200)` | YES | `` |  |
| `detail_discussed` | `text` | YES | `` |  |
| `document_link` | `varchar(200)` | YES | `` |  |
| `follow_meeting_date` | `timestamp` | YES | `` |  |
| `userid` | `int(11)` | YES | `` |  |
| `email_send` | `char(1)` | YES | `N` |  |

---
## Module: Postcodes & Geography

### Table: `brazil_postcodes`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `state` | `varchar(45)` | YES | `` |  |
| `locality` | `varchar(200)` | YES | `` |  |
| `postcode` | `varchar(45)` | YES | `` |  |
| `zone` | `varchar(45)` | YES | `` |  |

---
### Table: `brazil_states`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `state_code` | `varchar(45)` | YES | `` |  |
| `state_name` | `varchar(45)` | YES | `` |  |
| `city_code` | `varchar(45)` | YES | `` |  |
| `city_name` | `varchar(45)` | YES | `` |  |

---
### Table: `hermes_postcode_records`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `fullpostcode` | `varchar(8)` | NO | `` | REQUIRED |
| `pos_pcd_postcode_excluded_indicator` | `char(1)` | NO | `N` | REQUIRED |
| `sort_level_key` | `varchar(8)` | NO | `` | REQUIRED |
| `next_day_service` | `char(1)` | NO | `N` | REQUIRED |

---
### Table: `owe_southafrica_postcodes`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `zone` | `varchar(45)` | YES | `` |  |
| `postcode` | `varchar(45)` | YES | `` |  |
| `main_outlying` | `varchar(45)` | YES | `` |  |

---
### Table: `sorter_postcode_zones`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `area` | `varchar(45)` | YES | `` |  |
| `postcode` | `varchar(45)` | YES | `` |  |
| `zone` | `varchar(45)` | YES | `` |  |

---
### Table: `ukpostcodelatlngs`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `postcode` | `varchar(8)` | NO | `` | REQUIRED |
| `latitude` | `decimal(18,15)` | NO | `` | REQUIRED |
| `longitude` | `decimal(18,15)` | NO | `` | REQUIRED |

---
## Module: Rates & Pricing

### Table: `cost_tariffs`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `courier_service_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `collection_rateband_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `destination_rateband_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `collection_postcode_group_id` | `int(11)` | NO | `` | REQUIRED |
| `destination_postcode_group_id` | `int(11)` | YES | `` |  |
| `weight_from` | `decimal(7,2)` | NO | `` | REQUIRED |
| `weight_to` | `decimal(7,2)` | NO | `` | REQUIRED |
| `tariff_cost` | `decimal(9,2)` | NO | `` | REQUIRED |
| `unit_cost` | `decimal(5,2)` | NO | `` | REQUIRED |
| `unit_size` | `decimal(5,2)` | NO | `0.00` | REQUIRED |
| `active` | `tinyint(4)` | YES | `1` |  |
| `deletedq` | `char(1)` | YES | `N` |  |
| `added_on` | `datetime` | YES | `` |  |
| `added_by` | `varchar(100)` | YES | `` |  |
| `changed_on` | `datetime` | YES | `` |  |
| `changed_by` | `varchar(100)` | YES | `` |  |
| `tariff_name` | `varchar(255)` | NO | `` | REQUIRED |
| `formula` | `varchar(100)` | YES | `Q * ( ITMCHR + REG ) + W * CHRG` |  |

---
### Table: `countries_link_ratebands`
- **Primary Key**: NONE
- **Relationships**: `rateband_id` -> `ratebands`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `country_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `rateband_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `orderq` | `int(5)` | NO | `0` | REQUIRED |
| `active` | `tinyint(4)` | YES | `1` |  |
| `deletedq` | `char(1)` | YES | `N` |  |
| `added_on` | `datetime` | YES | `` |  |
| `added_by` | `varchar(100)` | YES | `` |  |
| `changed_on` | `datetime` | YES | `` |  |
| `changed_by` | `varchar(100)` | YES | `` |  |

---
### Table: `invoice_bank_details`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `user_account_id` | `int(11)` | NO | `` | REQUIRED |
| `account_title` | `varchar(45)` | YES | `` |  |
| `account_sortcode` | `varchar(10)` | YES | `` |  |
| `account_number` | `int(12)` | YES | `` |  |
| `account_iban` | `varchar(45)` | YES | `` |  |
| `bank_name` | `varchar(45)` | YES | `` |  |
| `bank_branch` | `varchar(45)` | YES | `` |  |
| `bank_address` | `varchar(100)` | YES | `` |  |
| `date_added` | `timestamp` | YES | `current_timestamp()` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `status` | `tinyint(1)` | YES | `1` |  |

---
### Table: `invoice_detail_backups`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | YES | `` |  |
| `invoice_no` | `varchar(50)` | YES | `` |  |
| `hawb` | `varchar(25)` | YES | `` |  |
| `basic_charges` | `decimal(10,2)` | YES | `` |  |
| `fuel_charges` | `decimal(10,2)` | YES | `` |  |
| `additional_charges` | `decimal(10,2)` | YES | `` |  |
| `remote_area_charge` | `decimal(10,2)` | YES | `` |  |
| `on_farword_charges` | `decimal(10,2)` | YES | `` |  |
| `ndx` | `decimal(10,2)` | YES | `` |  |
| `ddp` | `decimal(10,2)` | YES | `` |  |
| `extra` | `decimal(10,2)` | YES | `` |  |
| `hv` | `decimal(11,2)` | YES | `` |  |
| `amount` | `decimal(10,2)` | YES | `` |  |
| `agent_basic_charges` | `decimal(10,2)` | YES | `` |  |
| `agent_fuel_charges` | `decimal(10,2)` | YES | `` |  |
| `agent_additional_charges` | `decimal(10,2)` | YES | `` |  |
| `agent_remote_area_charge` | `decimal(10,2)` | YES | `` |  |
| `agent_on_farword_charges` | `decimal(10,2)` | YES | `` |  |
| `agent_ndx` | `decimal(10,2)` | YES | `` |  |
| `agent_ddp` | `decimal(10,2)` | YES | `` |  |
| `agent_extra` | `decimal(10,2)` | YES | `` |  |
| `agent_amount` | `decimal(10,2)` | YES | `` |  |
| `agent_linehaul_cost` | `decimal(10,2)` | YES | `` |  |
| `agent_handling_charges` | `decimal(10,2)` | YES | `` |  |
| `reference` | `varchar(350)` | YES | `` |  |
| `quotation_id` | `int(11)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `added_by` | `varchar(15)` | YES | `` |  |

---
### Table: `invoice_detail_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(40)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(10)` | NO | `A` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `invoice_details`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | NO | `` | REQUIRED |
| `invoice_id` | `int(11)` | YES | `` |  |
| `invoice_no` | `varchar(50)` | YES | `` |  |
| `charges_detail` | `text` | YES | `` |  |
| `total` | `int(11)` | YES | `` |  |
| `vat` | `int(11)` | YES | `` |  |
| `date_created` | `datetime` | YES | `` |  |
| `added_by` | `varchar(15)` | NO | `AUTOMATED_PRICE` | REQUIRED |

---
### Table: `invoice_extra_charges`
- **Primary Key**: NONE
- **Relationships**: `invoice_detail_id` -> `invoice_details`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `invoice_detail_id` | `int(11)` | NO | `` | REQUIRED |
| `charge_type_id` | `int(11)` | NO | `` | REQUIRED |
| `agent_id` | `int(11)` | NO | `` | REQUIRED |
| `cost_type` | `enum('customer','agent')` | NO | `customer` | REQUIRED |
| `cost` | `decimal(11,2)` | NO | `0.00` | REQUIRED |
| `description` | `varchar(255)` | YES | `` |  |
| `added_by` | `int(11)` | NO | `` | REQUIRED |
| `added_date` | `timestamp` | NO | `current_timestamp()` | REQUIRED |

---
### Table: `invoice_extra_charges_types`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `title` | `varchar(255)` | YES | `` |  |
| `isactive` | `tinyint(1)` | NO | `0` | REQUIRED |
| `added_by` | `int(11)` | NO | `` | REQUIRED |
| `added_date` | `timestamp` | NO | `current_timestamp()` | REQUIRED |

---
### Table: `invoice_templates`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `name` | `varchar(150)` | NO | `` | REQUIRED |
| `image` | `varchar(200)` | YES | `` |  |
| `invoice_function` | `varchar(250)` | YES | `` |  |
| `summary_invoice_function` | `varchar(250)` | YES | `` |  |

---
### Table: `invoices_number_ranges`
- **Primary Key**: NONE
- **Relationships**: `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `is_default` | `tinyint(4)` | NO | `0` | REQUIRED |
| `range_start` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `range_end` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `next_number` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `increment_date` | `datetime` | YES | `` |  |
| `user_account_id` | `int(11)` | NO | `` | REQUIRED |
| `prefix` | `varchar(10)` | YES | `` |  |
| `sufix` | `varchar(10)` | YES | `` |  |
| `range_type` | `enum('AUTO','MANUAL','CREDIT')` | YES | `` |  |
| `date_created` | `timestamp` | YES | `current_timestamp()` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `addedby` | `int(11)` | YES | `` |  |
| `updatedby` | `int(11)` | YES | `` |  |

---
### Table: `proforma_invoice_biilings`
- **Primary Key**: NONE
- **Relationships**: `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `consignment_id` | `int(11)` | NO | `` | REQUIRED |
| `billing_company` | `varchar(100)` | YES | `` |  |
| `billing_contact` | `varchar(100)` | YES | `` |  |
| `billing_address_line_1` | `varchar(50)` | YES | `` |  |
| `billing_address_line_2` | `varchar(50)` | YES | `` |  |
| `billing_address_line_3` | `varchar(50)` | YES | `` |  |
| `billing_city` | `varchar(50)` | YES | `` |  |
| `billing_country` | `varchar(50)` | YES | `` |  |
| `billing_postcode` | `varchar(10)` | YES | `` |  |
| `billing_telephone` | `varchar(20)` | YES | `` |  |
| `payment_terms` | `varchar(45)` | YES | `` |  |
| `export_type` | `varchar(45)` | YES | `` |  |
| `comments` | `varchar(200)` | YES | `` |  |
| `delivery_terms` | `varchar(45)` | YES | `` |  |
| `link_file` | `varchar(200)` | YES | `` |  |
| `payer_vat` | `varchar(45)` | YES | `` |  |
| `harm_comm_code` | `varchar(45)` | YES | `` |  |
| `export` | `varchar(45)` | YES | `` |  |
| `invoice_type` | `varchar(45)` | YES | `` |  |

---
### Table: `ratebands`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `courier_service_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `name` | `varchar(100)` | NO | `` | REQUIRED |
| `orderq` | `int(5)` | NO | `0` | REQUIRED |
| `active` | `tinyint(4)` | YES | `1` |  |
| `deletedq` | `char(1)` | YES | `N` |  |
| `added_on` | `datetime` | YES | `` |  |
| `added_by` | `varchar(100)` | YES | `` |  |
| `changed_on` | `datetime` | YES | `` |  |
| `changed_by` | `varchar(100)` | YES | `` |  |

---
### Table: `remotearea_charges_tariffs`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`
- **Relationships**: `tariff_id` -> `tariffs`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `remotearea_group_id` | `int(11)` | YES | `` |  |
| `remotearea_charges` | `decimal(10,2)` | YES | `` |  |
| `tariff_id` | `int(11)` | YES | `` |  |
| `from_weight` | `decimal(10,2)` | YES | `` |  |
| `to_weight` | `decimal(10,2)` | YES | `` |  |
| `formulla` | `varchar(45)` | YES | `` |  |
| `is_deleted` | `enum('Y','N')` | YES | `N` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `remotearea_weight_charges`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `weight_from` | `decimal(10,2)` | YES | `` |  |
| `weight_to` | `decimal(10,2)` | YES | `` |  |
| `service_code` | `varchar(45)` | YES | `` |  |
| `country_iso` | `varchar(10)` | YES | `` |  |
| `postcode_name` | `varchar(45)` | YES | `` |  |
| `formulla` | `varchar(255)` | YES | `` |  |
| `charges` | `decimal(10,2)` | YES | `` |  |

---
### Table: `sp_tariff_logs`
- **Primary Key**: NONE
- **Relationships**: `account_id` -> `user_accounts`, `consignment_id` -> `consignments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `account_id` | `int(11)` | YES | `` |  |
| `charges_type` | `varchar(45)` | YES | `` |  |
| `charges` | `decimal(10,2)` | YES | `0.00` |  |
| `formulla` | `varchar(100)` | YES | `` |  |
| `date_added` | `timestamp` | YES | `current_timestamp()` |  |
| `consignment_id` | `int(11)` | YES | `0` |  |

---
### Table: `tariff_additional_charges`
- **Primary Key**: NONE
- **Relationships**: `tariff_id` -> `tariffs`, `consignment_charges_types_id` -> `consignment_charges_types`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `tariff_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `consignment_charges_types_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `charge` | `decimal(10,2) unsigned` | NO | `0.00` | REQUIRED |
| `charge_type` | `enum('fixed','percentage')` | YES | `fixed` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `tariff_details`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `tariff_name` | `varchar(45)` | YES | `` |  |
| `status` | `bit(1)` | YES | `b'0'` |  |
| `tariff_type` | `varchar(10)` | YES | `` |  |
| `start_date` | `datetime` | YES | `` |  |
| `end_date` | `datetime` | YES | `` |  |
| `date_created` | `timestamp` | YES | `current_timestamp()` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `currency` | `varchar(3)` | YES | `GBP` |  |

---
### Table: `tariffs`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `user_account_id` -> `user_accounts`, `carrier_id` -> `carriers`, `service_id` -> `services`, `tariffs_pricing_rule_id` -> `tariffs_pricing_rules`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `user_account_id` | `int(11)` | NO | `` | REQUIRED |
| `carrier_id` | `int(11)` | NO | `` | REQUIRED |
| `service_id` | `int(11)` | YES | `` |  |
| `name` | `varchar(45)` | YES | `` |  |
| `status` | `tinyint(1)` | YES | `0` |  |
| `currency_id` | `int(11)` | YES | `2` |  |
| `tariff_type` | `enum('customer','supplier')` | YES | `` |  |
| `start_date` | `date` | YES | `` |  |
| `end_date` | `date` | YES | `` |  |
| `description` | `text` | YES | `` |  |
| `tariffs_pricing_rule_id` | `int(11)` | YES | `0` |  |
| `date_added` | `timestamp` | YES | `current_timestamp()` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
### Table: `tariffs_details`
- **Primary Key**: NONE
- **Relationships**: `tariffs_id` -> `tariffs`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `tariffs_id` | `int(11)` | NO | `` | REQUIRED |
| `from_zone_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `to_zone_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `weight_from` | `decimal(7,2)` | NO | `` | REQUIRED |
| `weight_to` | `decimal(7,2)` | NO | `` | REQUIRED |
| `weight_cost` | `decimal(9,2)` | NO | `` | REQUIRED |
| `piece_cost` | `decimal(5,2)` | NO | `` | REQUIRED |
| `formula` | `varchar(255)` | YES | `Q  ( ITMCHR + REG ) + W  CHRG` |  |

---
### Table: `tariffs_logs`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `old_id` | `int(11) unsigned` | YES | `` |  |
| `courier_service_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `collection_rateband_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `destination_rateband_id` | `int(11) unsigned` | NO | `` | REQUIRED |
| `collection_postcode_group_id` | `int(11)` | NO | `` | REQUIRED |
| `destination_postcode_group_id` | `int(11)` | YES | `` |  |
| `weight_from` | `decimal(7,2)` | NO | `` | REQUIRED |
| `weight_to` | `decimal(7,2)` | NO | `` | REQUIRED |
| `tariff` | `decimal(9,2)` | NO | `` | REQUIRED |
| `add_unit_cost` | `decimal(5,2)` | NO | `` | REQUIRED |
| `unit_size` | `decimal(5,2)` | NO | `0.00` | REQUIRED |
| `extra_tariff` | `decimal(7,2)` | YES | `` |  |
| `extra_add_unit_cost` | `decimal(7,2)` | YES | `` |  |
| `orderq` | `int(5)` | NO | `0` | REQUIRED |
| `active` | `tinyint(4)` | YES | `1` |  |
| `deletedq` | `char(1)` | YES | `N` |  |
| `added_on` | `datetime` | YES | `` |  |
| `added_by` | `varchar(100)` | YES | `` |  |
| `changed_on` | `datetime` | YES | `` |  |
| `changed_by` | `varchar(100)` | YES | `` |  |
| `customer_id` | `varchar(255)` | NO | `` | REQUIRED |
| `formula` | `varchar(100)` | YES | `Q * ( ITMCHR + REG ) + W * CHRG` |  |
| `log_date` | `date` | YES | `` |  |

---
### Table: `tariffs_pricing_rules`
- **Primary Key**: NONE
- **Relationships**: `tariff_id` -> `tariffs`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `tariff_id` | `int(11)` | YES | `0` |  |
| `name` | `varchar(100)` | YES | `` |  |
| `date_added` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `added_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
### Table: `tariffs_pricing_rules_details`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `tariff_pricing_rule_id` | `int(11)` | YES | `` |  |
| `to_zone_id` | `int(11)` | YES | `` |  |
| `weight_from` | `decimal(7,2)` | YES | `` |  |
| `weight_to` | `decimal(7,2)` | YES | `` |  |
| `margin` | `decimal(9,2)` | YES | `` |  |
| `margin_type` | `enum('percentage','price')` | YES | `` |  |
| `margin_weight_cost` | `varchar(20)` | YES | `` |  |
| `margin_piece_cost` | `varchar(20)` | YES | `` |  |
| `linehaul` | `decimal(7,2)` | YES | `` |  |
| `linehaul_type` | `enum('kilogram','flat')` | YES | `` |  |
| `tariff_pricing_type` | `enum('whole','multiple')` | YES | `` |  |
| `date_added` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `added_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
### Table: `tariffs_pricings`
- **Primary Key**: NONE
- **Relationships**: `tariff_id` -> `tariffs`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `tariff_id` | `int(11)` | NO | `` | REQUIRED |
| `tarif_pricing_type` | `enum('single_tariff','whole_tariff')` | YES | `` |  |
| `to_zone_id` | `int(11)` | YES | `` |  |
| `weight_from` | `decimal(7,2)` | YES | `` |  |
| `weight_to` | `decimal(7,2)` | YES | `` |  |
| `margin_type` | `enum('percentage','price')` | YES | `` |  |
| `margin` | `decimal(9,2)` | YES | `` |  |
| `date_added` | `timestamp` | NO | `current_timestamp()` | REQUIRED |
| `added_by` | `int(11)` | YES | `` |  |
| `date_updated` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |

---
## Module: Accounts & Users

### Table: `customer_accounts`
- **Primary Key**: NONE
- **Status/Soft Delete**: `active_flag`
- **Relationships**: `warehouse_id` -> `warehouses`, `invoice_bank_details_id` -> `invoice_bank_details`, `theme_id` -> `themes`, `invoice_template_id` -> `invoice_templates`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | `` | REQUIRED |
| `user_account` | `varchar(30)` | YES | `` |  |
| `active_flag` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `company` | `varchar(100)` | YES | `` |  |
| `full_name` | `varchar(100)` | YES | `` |  |
| `return_address` | `varchar(255)` | YES | `` |  |
| `sms_dpd` | `bit(1)` | YES | `b'0'` |  |
| `user_service_type` | `enum('CHOICE','ROUTING','BOTH')` | YES | `` |  |
| `parentid` | `int(11)` | YES | `` |  |
| `phone` | `varchar(20)` | YES | `` |  |
| `logo` | `varchar(255)` | YES | `` |  |
| `instant_label` | `bit(1)` | YES | `b'0'` |  |
| `country` | `varchar(3)` | YES | `` |  |
| `country_id` | `int(11)` | YES | `` |  |
| `tracking_api_access` | `bit(1)` | YES | `b'0'` |  |
| `import_data_csv` | `bit(1)` | YES | `b'0'` |  |
| `proforma` | `bit(1)` | YES | `b'0'` |  |
| `add_tracking` | `bit(1)` | YES | `b'0'` |  |
| `collection` | `bit(1)` | YES | `b'0'` |  |
| `default_description` | `varchar(255)` | YES | `` |  |
| `default_notes` | `varchar(255)` | YES | `` |  |
| `default_weight` | `decimal(5,2)` | YES | `` |  |
| `payment_term` | `text` | YES | `` |  |
| `query_term` | `text` | YES | `` |  |
| `vat_number` | `varchar(40)` | YES | `` |  |
| `billing_currency` | `varchar(3)` | YES | `GBP` |  |
| `vat_chargable` | `bit(1)` | YES | `b'0'` |  |
| `vat_value` | `decimal(10,2)` | YES | `` |  |
| `allow_remote_area` | `bit(1)` | YES | `b'0'` |  |
| `telephone` | `varchar(20)` | YES | `` |  |
| `billing_address` | `varchar(255)` | YES | `` |  |
| `date_dispatch` | `bit(1)` | YES | `b'0'` |  |
| `is_product` | `varchar(2)` | YES | `0` |  |
| `profile_image` | `varchar(255)` | YES | `` |  |
| `send_courier_data` | `bit(1)` | YES | `b'0'` |  |
| `archive_server` | `bit(1)` | YES | `b'0'` |  |
| `credit_check` | `bit(1)` | YES | `b'0'` |  |
| `tariff_agreed` | `bit(1)` | YES | `b'0'` |  |
| `sales_person` | `varchar(45)` | YES | `` |  |
| `scan_document` | `text` | YES | `` |  |
| `data_entry` | `bit(1)` | YES | `b'0'` |  |
| `bank_account_title` | `varchar(45)` | YES | `` |  |
| `bank_sortcode` | `varchar(10)` | YES | `` |  |
| `bank_account_number` | `varchar(20)` | YES | `` |  |
| `bank_branch_address` | `varchar(255)` | YES | `` |  |
| `trade_name_i` | `varchar(50)` | YES | `` |  |
| `trade_address_i` | `varchar(255)` | YES | `` |  |
| `trade_email_i` | `varchar(255)` | YES | `` |  |
| `trade_phone_i` | `varchar(50)` | YES | `` |  |
| `trade_name_ii` | `varchar(50)` | YES | `` |  |
| `trade_address_ii` | `varchar(255)` | YES | `` |  |
| `trade_email_ii` | `varchar(255)` | YES | `` |  |
| `trade_phone_ii` | `varchar(50)` | YES | `` |  |
| `reg_number` | `varchar(20)` | YES | `` |  |
| `reg_address` | `varchar(255)` | YES | `` |  |
| `reg_postcode` | `varchar(10)` | YES | `` |  |
| `reg_country` | `varchar(5)` | YES | `` |  |
| `sale_agent` | `varchar(10)` | YES | `` |  |
| `sale_date` | `datetime` | YES | `` |  |
| `fuel_charges` | `decimal(10,2)` | YES | `0.00` |  |
| `warehouse_id` | `int(11)` | YES | `` |  |
| `user_signature` | `text` | YES | `` |  |
| `is_fuelcharges_include` | `bit(1)` | YES | `b'0'` |  |
| `is_prepaid` | `bit(1)` | YES | `b'0'` |  |
| `return_label` | `bit(1)` | YES | `b'0'` |  |
| `finalmile_over_label` | `bit(1)` | YES | `b'0'` |  |
| `request_manifest_collection` | `bit(1)` | YES | `b'0'` |  |
| `create_pre_alert` | `bit(1)` | YES | `b'0'` |  |
| `is_employee` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `invoice_bank_details_id` | `int(11)` | YES | `0` |  |
| `check_list_account_form` | `bit(1)` | YES | `b'0'` |  |
| `check_list_credit_check` | `bit(1)` | YES | `b'0'` |  |
| `check_list_t_cs` | `bit(1)` | YES | `b'0'` |  |
| `check_list_tariff_agreed` | `bit(1)` | YES | `b'0'` |  |
| `check_list_sales_pot` | `bit(1)` | YES | `b'0'` |  |
| `sales_pot_time_period` | `int(5)` | YES | `0` |  |
| `sales_pot_percentage` | `decimal(5,2)` | YES | `0.00` |  |
| `last_login_date` | `timestamp` | YES | `` |  |
| `invalid_login_count` | `int(11)` | YES | `` |  |
| `token` | `varchar(255)` | YES | `` |  |
| `token_updated` | `timestamp` | YES | `` |  |
| `lock_time` | `timestamp` | YES | `` |  |
| `opearation_manifest` | `bit(1)` | YES | `b'0'` |  |
| `own_tariff` | `bit(1)` | YES | `b'0'` |  |
| `user_warehouse` | `enum('NON','BIRMINGHAM','HAYES')` | YES | `NON` |  |
| `api_key` | `varchar(100)` | YES | `` |  |
| `api_secert` | `varchar(100)` | YES | `` |  |
| `api_date` | `datetime` | YES | `` |  |
| `bagging` | `bit(1)` | YES | `b'0'` |  |
| `retail_customer` | `bit(1)` | YES | `b'0'` |  |
| `show_price` | `bit(1)` | YES | `b'0'` |  |
| `sales_rate` | `decimal(10,2)` | YES | `` |  |
| `collection_add_line_1` | `varchar(50)` | YES | `` |  |
| `collection_add_line_2` | `varchar(50)` | YES | `` |  |
| `collection_add_line_3` | `varchar(50)` | YES | `` |  |
| `collection_city` | `varchar(45)` | YES | `` |  |
| `collection_postcode` | `varchar(45)` | YES | `` |  |
| `collection_country` | `varchar(45)` | YES | `` |  |
| `theme_id` | `int(5)` | YES | `` |  |
| `user_code` | `int(11)` | YES | `` |  |
| `website_link` | `varchar(200)` | YES | `` |  |
| `allow_return_email` | `bit(1)` | YES | `b'0'` |  |
| `default_lang` | `varchar(10)` | YES | `en-GB` |  |
| `credit_limit` | `decimal(10,2)` | YES | `0.00` |  |
| `invoice_period` | `enum('daily','weekly','bi-monthly','monthly')` | YES | `daily` |  |
| `label_price` | `decimal(10,2)` | YES | `0.00` |  |
| `discount` | `decimal(10,2)` | YES | `0.00` |  |
| `account_code` | `varchar(4)` | YES | `` |  |
| `paypal_email` | `varchar(70)` | YES | `` |  |
| `paypal_currency` | `varchar(20)` | YES | `` |  |
| `email` | `varchar(500)` | YES | `` |  |
| `paypal_client_secret` | `varchar(255)` | YES | `` |  |
| `alternative_email` | `varchar(500)` | YES | `` |  |
| `billing_email` | `varchar(500)` | YES | `` |  |
| `date_created` | `timestamp` | YES | `current_timestamp()` |  |
| `allow_oversize` | `tinyint(4)` | NO | `0` | REQUIRED |
| `allow_overweight` | `tinyint(4)` | NO | `0` | REQUIRED |
| `paypal_client_id` | `varchar(255)` | YES | `` |  |
| `invoice_template_id` | `bigint(20) unsigned` | YES | `` |  |
| `send_tracking_data` | `bit(1)` | YES | `` |  |
| `billing_contact` | `varchar(50)` | NO | `` | REQUIRED |
| `ftp_shipment_upload` | `int(2)` | NO | `0` | REQUIRED |
| `balance_alert_percentage` | `int(2)` | NO | `0` | REQUIRED |
| `commission_break_event_account_amount` | `int(2)` | NO | `0` | REQUIRED |
| `tracking_order_prefix` | `varchar(50)` | NO | `` | REQUIRED |
| `return_shipment_allow` | `int(2)` | NO | `0` | REQUIRED |
| `account_balance` | `decimal(10,2)` | NO | `0.00` | REQUIRED |

---
### Table: `customized_user_services_routings`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`, `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `service_id` | `int(11)` | NO | `` | REQUIRED |
| `user_account_id` | `int(11)` | NO | `` | REQUIRED |
| `routing_added_date` | `datetime` | YES | `` |  |

---
### Table: `departments`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(5)` | NO | `` | REQUIRED |
| `title` | `varchar(100)` | NO | `` | REQUIRED |
| `department_code` | `varchar(5)` | NO | `` | REQUIRED |
| `description` | `varchar(255)` | YES | `` |  |
| `department_head` | `bigint(20)` | NO | `` | REQUIRED |
| `isactive` | `tinyint(1)` | NO | `1` | REQUIRED |
| `isdeleted` | `tinyint(1)` | NO | `0` | REQUIRED |
| `addedby` | `bigint(20)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | NO | `` | REQUIRED |
| `updatedby` | `bigint(20)` | NO | `` | REQUIRED |
| `updated_on` | `datetime` | YES | `` |  |

---
### Table: `dpdgroups`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `lookup_code` | `varchar(15)` | YES | `` |  |
| `list_of_available_services` | `varchar(100)` | YES | `` |  |
| `Business` | `varchar(2)` | YES | `` |  |

---
### Table: `dropoff_user_locations`
- **Primary Key**: NONE
- **Relationships**: `service_id` -> `services`, `user_id` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `service_id` | `int(11)` | YES | `` |  |
| `user_id` | `bigint(20)` | YES | `` |  |
| `companyname` | `varchar(100)` | YES | `` |  |
| `address_line_1` | `varchar(50)` | YES | `` |  |
| `address_line_2` | `varchar(50)` | YES | `` |  |
| `address_line_3` | `varchar(50)` | YES | `` |  |
| `city` | `varchar(50)` | YES | `` |  |
| `postcode` | `varchar(10)` | YES | `` |  |
| `country` | `varchar(45)` | YES | `` |  |
| `telephone` | `varchar(17)` | YES | `` |  |
| `mon` | `varchar(20)` | YES | `` |  |
| `tue` | `varchar(20)` | YES | `` |  |
| `wed` | `varchar(20)` | YES | `` |  |
| `thu` | `varchar(20)` | YES | `` |  |
| `fri` | `varchar(20)` | YES | `` |  |
| `sat` | `varchar(20)` | YES | `` |  |
| `sun` | `varchar(20)` | YES | `` |  |
| `lat` | `varchar(20)` | YES | `` |  |
| `lng` | `varchar(20)` | YES | `` |  |
| `added_by` | `bigint(20)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `grouphaspermissions`
- **Primary Key**: NONE
- **Relationships**: `group_id` -> `groups`, `perm_id` -> `permissions`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `group_id` | `int(11)` | NO | `` | REQUIRED |
| `perm_id` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `groups`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_active`, `is_deleted`
- **Relationships**: `group_id` -> `groups`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `group_id` | `int(11)` | NO | `` | REQUIRED |
| `group_name` | `varchar(150)` | YES | `` |  |
| `group_slug` | `varchar(255)` | YES | `` |  |
| `group_desc` | `varchar(255)` | YES | `` |  |
| `group_type` | `enum('admin','corporate','client')` | YES | `` |  |
| `is_active` | `tinyint(1)` | YES | `0` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `is_deleted` | `tinyint(1)` | YES | `0` |  |

---
### Table: `groups_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `pallet_carier_groups`
- **Primary Key**: NONE
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `group_name` | `varchar(255)` | NO | `` | REQUIRED |
| `carrier_id` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `permissions`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_active`, `is_deleted`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `lang_key` | `varchar(100)` | YES | `` |  |
| `parent_id` | `int(11)` | YES | `` |  |
| `file_name` | `varchar(150)` | YES | `` |  |
| `description` | `varchar(250)` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `query_string` | `varchar(100)` | YES | `` |  |
| `icon` | `varchar(50)` | YES | `` |  |
| `sort_order` | `int(11)` | YES | `` |  |
| `is_menu_item` | `tinyint(1)` | NO | `1` | REQUIRED |
| `is_active` | `tinyint(1)` | NO | `0` | REQUIRED |
| `is_deleted` | `tinyint(1)` | NO | `0` | REQUIRED |

---
### Table: `permissions_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `postcode_user_service_charges`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `from_postcode` | `varchar(45)` | YES | `` |  |
| `to_postcode` | `varchar(45)` | YES | `` |  |
| `postcode_name` | `varchar(45)` | YES | `` |  |
| `city_name` | `varchar(45)` | YES | `` |  |
| `country_iso` | `varchar(4)` | YES | `` |  |

---
### Table: `remotearea_charges_carrier_users`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`
- **Relationships**: `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `remotearea_group_id` | `int(11)` | YES | `` |  |
| `user_account_id` | `int(11)` | YES | `` |  |
| `remotearea_charges` | `decimal(10,2)` | YES | `` |  |
| `is_deleted` | `enum('Y','N')` | YES | `N` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `date` | YES | `` |  |

---
### Table: `remotearea_charges_services_users`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`
- **Relationships**: `service_id` -> `services`, `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `remotearea_group_id` | `int(11)` | YES | `` |  |
| `remotearea_charges` | `decimal(10,2)` | YES | `` |  |
| `service_id` | `int(11)` | YES | `` |  |
| `from_weight` | `decimal(10,2)` | YES | `` |  |
| `to_weight` | `decimal(10,2)` | YES | `` |  |
| `formulla` | `varchar(45)` | YES | `` |  |
| `user_account_id` | `int(11)` | YES | `` |  |
| `is_deleted` | `enum('Y','N')` | YES | `N` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `remotearea_user_mappings`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `user_account` | `varchar(255)` | YES | `` |  |
| `postcode_name` | `varchar(255)` | YES | `` |  |
| `service_code` | `varchar(45)` | YES | `` |  |
| `charges` | `varchar(45)` | YES | `` |  |
| `remotearea_added_date` | `datetime` | YES | `` |  |

---
### Table: `remoteareas_groups`
- **Primary Key**: NONE
- **Status/Soft Delete**: `is_deleted`
- **Relationships**: `carrier_id` -> `carriers`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `carrier_id` | `int(11)` | YES | `` |  |
| `group_name` | `varchar(45)` | YES | `` |  |
| `is_deleted` | `enum('Y','N')` | YES | `N` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `remoteareas_groups_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(100)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(11)` | NO | `` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `routing_user_mappings`
- **Primary Key**: NONE
- **Relationships**: `product_id` -> `products`, `user_id` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `product_id` | `int(11)` | NO | `` | REQUIRED |
| `user_id` | `int(11)` | NO | `` | REQUIRED |
| `routing_added_date` | `datetime` | YES | `` |  |

---
### Table: `tariff_user_mappings`
- **Primary Key**: NONE
- **Relationships**: `user_id` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `tariff_name` | `int(11)` | NO | `` | REQUIRED |
| `user_id` | `int(11)` | NO | `` | REQUIRED |
| `tariff_added_date` | `datetime` | YES | `` |  |

---
### Table: `tariffs_account_mappings`
- **Primary Key**: NONE
- **Relationships**: `tariff_id` -> `tariffs`, `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `tariff_id` | `int(11)` | NO | `` | REQUIRED |
| `user_account_id` | `int(11)` | NO | `` | REQUIRED |
| `added_date` | `datetime` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |

---
### Table: `user_account_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(40)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(10)` | NO | `A` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `user_account_service_charges`
- **Primary Key**: NONE
- **Relationships**: `user_account_id` -> `user_accounts`, `service_id` -> `services`, `consignment_charges_types_id` -> `consignment_charges_types`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `user_account_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `service_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `consignment_charges_types_id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `charge` | `decimal(10,2) unsigned` | NO | `0.00` | REQUIRED |
| `charge_type` | `enum('fixed','percentage')` | YES | `fixed` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `user_accounts`
- **Primary Key**: NONE
- **Status/Soft Delete**: `active_flag`
- **Relationships**: `warehouse_id` -> `warehouses`, `invoice_bank_details_id` -> `invoice_bank_details`, `theme_id` -> `themes`, `invoice_template_id` -> `invoice_templates`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | `` | REQUIRED |
| `user_account` | `varchar(30)` | YES | `` |  |
| `active_flag` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `company` | `varchar(100)` | YES | `` |  |
| `full_name` | `varchar(100)` | YES | `` |  |
| `return_address` | `varchar(255)` | YES | `` |  |
| `sms_dpd` | `bit(1)` | YES | `b'0'` |  |
| `user_service_type` | `enum('CHOICE','ROUTING','BOTH')` | YES | `` |  |
| `parentid` | `int(11)` | YES | `` |  |
| `phone` | `varchar(20)` | YES | `` |  |
| `logo` | `varchar(255)` | YES | `` |  |
| `instant_label` | `bit(1)` | YES | `b'1'` |  |
| `country` | `varchar(3)` | YES | `` |  |
| `country_id` | `int(11)` | YES | `` |  |
| `tracking_api_access` | `bit(1)` | YES | `b'0'` |  |
| `import_data_csv` | `bit(1)` | YES | `b'0'` |  |
| `proforma` | `bit(1)` | YES | `b'0'` |  |
| `add_tracking` | `bit(1)` | YES | `b'0'` |  |
| `collection` | `bit(1)` | YES | `b'0'` |  |
| `default_description` | `varchar(255)` | YES | `` |  |
| `default_notes` | `varchar(255)` | YES | `` |  |
| `default_weight` | `decimal(5,2)` | YES | `` |  |
| `payment_term` | `text` | YES | `` |  |
| `query_term` | `text` | YES | `` |  |
| `vat_number` | `varchar(40)` | YES | `` |  |
| `billing_currency` | `varchar(3)` | YES | `GBP` |  |
| `vat_chargable` | `bit(1)` | YES | `b'0'` |  |
| `vat_value` | `decimal(10,2)` | YES | `` |  |
| `allow_remote_area` | `bit(1)` | YES | `b'0'` |  |
| `telephone` | `varchar(20)` | YES | `` |  |
| `billing_address` | `varchar(255)` | YES | `` |  |
| `date_dispatch` | `bit(1)` | YES | `b'0'` |  |
| `is_product` | `varchar(2)` | YES | `0` |  |
| `profile_image` | `varchar(255)` | YES | `` |  |
| `send_courier_data` | `bit(1)` | YES | `b'0'` |  |
| `archive_server` | `bit(1)` | YES | `b'0'` |  |
| `credit_check` | `bit(1)` | YES | `b'0'` |  |
| `tariff_agreed` | `bit(1)` | YES | `b'0'` |  |
| `sales_person` | `varchar(45)` | YES | `` |  |
| `scan_document` | `text` | YES | `` |  |
| `data_entry` | `bit(1)` | YES | `b'0'` |  |
| `bank_account_title` | `varchar(45)` | YES | `` |  |
| `bank_sortcode` | `varchar(10)` | YES | `` |  |
| `bank_account_number` | `varchar(20)` | YES | `` |  |
| `bank_branch_address` | `varchar(255)` | YES | `` |  |
| `trade_name_i` | `varchar(50)` | YES | `` |  |
| `trade_address_i` | `varchar(255)` | YES | `` |  |
| `trade_email_i` | `varchar(255)` | YES | `` |  |
| `trade_phone_i` | `varchar(50)` | YES | `` |  |
| `trade_name_ii` | `varchar(50)` | YES | `` |  |
| `trade_address_ii` | `varchar(255)` | YES | `` |  |
| `trade_email_ii` | `varchar(255)` | YES | `` |  |
| `trade_phone_ii` | `varchar(50)` | YES | `` |  |
| `reg_number` | `varchar(20)` | YES | `` |  |
| `reg_address` | `varchar(255)` | YES | `` |  |
| `reg_postcode` | `varchar(10)` | YES | `` |  |
| `reg_country` | `varchar(5)` | YES | `` |  |
| `sale_agent` | `varchar(10)` | YES | `` |  |
| `sale_date` | `datetime` | YES | `` |  |
| `fuel_charges` | `decimal(10,2)` | YES | `0.00` |  |
| `warehouse_id` | `int(11)` | YES | `` |  |
| `user_signature` | `text` | YES | `` |  |
| `is_fuelcharges_include` | `bit(1)` | YES | `b'0'` |  |
| `is_prepaid` | `bit(1)` | YES | `b'0'` |  |
| `return_label` | `bit(1)` | YES | `b'0'` |  |
| `finalmile_over_label` | `bit(1)` | YES | `b'0'` |  |
| `request_manifest_collection` | `bit(1)` | YES | `b'0'` |  |
| `create_pre_alert` | `bit(1)` | YES | `b'0'` |  |
| `is_employee` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `invoice_bank_details_id` | `int(11)` | YES | `0` |  |
| `check_list_account_form` | `bit(1)` | YES | `b'0'` |  |
| `check_list_credit_check` | `bit(1)` | YES | `b'0'` |  |
| `check_list_t_cs` | `bit(1)` | YES | `b'0'` |  |
| `check_list_tariff_agreed` | `bit(1)` | YES | `b'0'` |  |
| `check_list_sales_pot` | `bit(1)` | YES | `b'0'` |  |
| `sales_pot_time_period` | `int(5)` | YES | `0` |  |
| `sales_pot_percentage` | `decimal(5,2)` | YES | `0.00` |  |
| `last_login_date` | `timestamp` | YES | `` |  |
| `invalid_login_count` | `int(11)` | YES | `` |  |
| `token` | `varchar(255)` | YES | `` |  |
| `token_updated` | `timestamp` | YES | `` |  |
| `lock_time` | `timestamp` | YES | `` |  |
| `opearation_manifest` | `bit(1)` | YES | `b'0'` |  |
| `own_tariff` | `bit(1)` | YES | `b'0'` |  |
| `user_warehouse` | `enum('NON','BIRMINGHAM','HAYES')` | YES | `NON` |  |
| `api_key` | `varchar(100)` | YES | `` |  |
| `api_secert` | `varchar(100)` | YES | `` |  |
| `api_date` | `datetime` | YES | `` |  |
| `bagging` | `bit(1)` | YES | `b'0'` |  |
| `retail_customer` | `bit(1)` | YES | `b'0'` |  |
| `show_price` | `bit(1)` | YES | `b'0'` |  |
| `sales_rate` | `decimal(10,2)` | YES | `` |  |
| `collection_add_line_1` | `varchar(50)` | YES | `` |  |
| `collection_add_line_2` | `varchar(50)` | YES | `` |  |
| `collection_add_line_3` | `varchar(50)` | YES | `` |  |
| `collection_city` | `varchar(45)` | YES | `` |  |
| `collection_postcode` | `varchar(45)` | YES | `` |  |
| `collection_country` | `varchar(45)` | YES | `` |  |
| `theme_id` | `int(5)` | YES | `` |  |
| `user_code` | `int(11)` | YES | `` |  |
| `website_link` | `varchar(200)` | YES | `` |  |
| `allow_return_email` | `bit(1)` | YES | `b'0'` |  |
| `default_lang` | `varchar(10)` | YES | `en-GB` |  |
| `credit_limit` | `decimal(10,2)` | YES | `0.00` |  |
| `invoice_period` | `enum('daily','weekly','bi-monthly','monthly')` | YES | `daily` |  |
| `label_price` | `decimal(10,2)` | YES | `0.00` |  |
| `discount` | `decimal(10,2)` | YES | `0.00` |  |
| `account_code` | `varchar(4)` | YES | `` |  |
| `paypal_email` | `varchar(70)` | YES | `` |  |
| `paypal_currency` | `varchar(20)` | YES | `` |  |
| `email` | `varchar(500)` | YES | `` |  |
| `paypal_client_secret` | `varchar(255)` | YES | `` |  |
| `alternative_email` | `varchar(500)` | YES | `` |  |
| `billing_email` | `varchar(500)` | YES | `` |  |
| `date_created` | `timestamp` | YES | `current_timestamp()` |  |
| `allow_oversize` | `tinyint(4)` | NO | `0` | REQUIRED |
| `allow_overweight` | `tinyint(4)` | NO | `0` | REQUIRED |
| `paypal_client_id` | `varchar(255)` | YES | `` |  |
| `invoice_template_id` | `bigint(20) unsigned` | YES | `` |  |
| `send_tracking_data` | `bit(1)` | YES | `` |  |

---
### Table: `user_audits`
- **Primary Key**: NONE

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | REQUIRED |
| `table_name` | `varchar(100)` | YES | `` |  |
| `table_key` | `bigint(20)` | YES | `` |  |
| `message` | `text` | YES | `` |  |
| `old_data` | `longtext` | YES | `` |  |
| `new_data` | `longtext` | YES | `` |  |
| `ip_address` | `varchar(100)` | YES | `` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `created_at` | `timestamp` | NO | `current_timestamp()` | REQUIRED |

---
### Table: `user_departments`
- **Primary Key**: NONE
- **Relationships**: `user_id` -> `users`, `department_id` -> `departments`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `user_id` | `bigint(20)` | NO | `` | REQUIRED |
| `department_id` | `int(11)` | NO | `` | REQUIRED |

---
### Table: `user_documents`
- **Primary Key**: NONE
- **Relationships**: `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `user_account_id` | `int(11)` | YES | `` |  |
| `document_id` | `int(11)` | YES | `` |  |
| `document_name` | `varchar(255)` | YES | `` |  |
| `added_by` | `bigint(20)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `bigint(20)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |

---
### Table: `user_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(40)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(10)` | NO | `A` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `user_market_places_mappings`
- **Primary Key**: NONE
- **Relationships**: `market_places_id` -> `market_places`, `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20)` | NO | `` | REQUIRED |
| `market_places_id` | `bigint(20)` | NO | `` | REQUIRED |
| `user_account_id` | `bigint(20)` | NO | `` | REQUIRED |
| `auth_data` | `text` | YES | `` |  |
| `store_key` | `varchar(200)` | YES | `` |  |
| `active` | `int(11)` | YES | `` |  |

---
### Table: `user_services_charges`
- **Primary Key**: NONE
- **Relationships**: `user_account_id` -> `user_accounts`, `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `user_account_id` | `int(11)` | YES | `` |  |
| `service_id` | `int(11)` | NO | `` | REQUIRED |
| `sur_charge` | `decimal(10,2)` | YES | `` |  |
| `sur_charge_type` | `enum('fixed','percentage')` | NO | `percentage` | REQUIRED |
| `extra_charge` | `decimal(10,2)` | YES | `` |  |
| `extra_charge_type` | `enum('fixed','percentage')` | NO | `percentage` | REQUIRED |
| `discount` | `decimal(10,2)` | YES | `` |  |
| `discount_type` | `enum('fixed','percentage')` | NO | `percentage` | REQUIRED |
| `additional_charges_type` | `enum('fixed','percentage','per_kg')` | YES | `per_kg` |  |
| `additional_charges` | `decimal(10,2)` | YES | `` |  |
| `additional_charges_details` | `text` | YES | `` |  |
| `last_updated` | `datetime` | YES | `` |  |
| `over_weight` | `decimal(10,2)` | YES | `` |  |
| `over_size` | `decimal(10,2)` | YES | `` |  |
| `over_weight_type` | `enum('fixed','percentage','per_pcs')` | YES | `per_pcs` |  |
| `over_size_type` | `enum('fixed','percentage','per_pcs')` | YES | `per_pcs` |  |

---
### Table: `user_services_charges_logs`
- **Primary Key**: NONE
- **Relationships**: `userid` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `userid` | `int(10)` | NO | `` | REQUIRED |
| `logdate` | `datetime` | NO | `` | REQUIRED |
| `ipaddress` | `varchar(40)` | NO | `` | REQUIRED |
| `log_id` | `int(11)` | NO | `` | REQUIRED |
| `log_type` | `varchar(10)` | NO | `A` | REQUIRED |
| `message` | `text` | YES | `` |  |
| `previous_data` | `text` | YES | `` |  |
| `current_data` | `text` | YES | `` |  |

---
### Table: `user_services_routings`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `user_account_id` -> `user_accounts`, `service_id` -> `services`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(10) unsigned` | NO | `` | REQUIRED |
| `user_account_id` | `int(11)` | NO | `` | REQUIRED |
| `country_id` | `int(11)` | NO | `` | REQUIRED |
| `from_weight` | `decimal(10,2)` | YES | `` |  |
| `to_weight` | `decimal(10,2)` | YES | `` |  |
| `status` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `service_id` | `int(11)` | YES | `` |  |
| `is_remotearea` | `bit(1)` | NO | `b'0'` | REQUIRED |
| `is_over_label` | `bit(1)` | YES | `b'0'` |  |
| `added_by` | `int(11)` | NO | `` | REQUIRED |
| `is_agreed` | `bit(1)` | YES | `b'0'` |  |
| `label_charges` | `decimal(10,2)` | YES | `0.00` |  |
| `is_dead_weight` | `bit(1)` | YES | `b'0'` |  |
| `is_over_size` | `int(2)` | NO | `0` | REQUIRED |

---
### Table: `user_shopping_platforms`
- **Primary Key**: NONE
- **Status/Soft Delete**: `status`
- **Relationships**: `shopping_platform_id` -> `shopping_platforms`, `user_id` -> `users`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | NO | `` | REQUIRED |
| `shopping_platform_id` | `int(11)` | YES | `` |  |
| `reference` | `varchar(45)` | YES | `` |  |
| `site_url` | `varchar(45)` | YES | `` |  |
| `user_id` | `int(11)` | YES | `` |  |
| `status` | `tinyint(1)` | YES | `0` |  |
| `date_created` | `timestamp` | YES | `current_timestamp()` |  |
| `api_key` | `varchar(255)` | YES | `` |  |
| `api_secrete` | `varchar(255)` | YES | `` |  |

---
### Table: `userhasgroups`
- **Primary Key**: NONE
- **Relationships**: `group_id` -> `groups`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `int(11)` | YES | `` |  |
| `admin_id` | `int(11)` | YES | `` |  |
| `group_id` | `int(11)` | YES | `` |  |

---
### Table: `users`
- **Primary Key**: `id`
- **Status/Soft Delete**: `active_flag`, `is_deleted`
- **Relationships**: `warehouse_id` -> `warehouses`, `user_account_id` -> `user_accounts`

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| `id` | `bigint(20) unsigned` | NO | `` | PK, REQUIRED, AI |
| `name` | `varchar(255)` | NO | `` | REQUIRED |
| `email` | `varchar(255)` | NO | `` | REQUIRED |
| `email_verified_at` | `timestamp` | YES | `` |  |
| `password` | `varchar(255)` | NO | `` | REQUIRED |
| `user_type` | `enum('corporate','client','admin','driver')` | NO | `client` | REQUIRED |
| `user_name` | `varchar(30)` | YES | `` |  |
| `active_flag` | `tinyint(1)` | NO | `0` | REQUIRED |
| `first_name` | `varchar(100)` | YES | `` |  |
| `last_name` | `varchar(255)` | YES | `` |  |
| `address` | `varchar(255)` | YES | `` |  |
| `phone` | `varchar(20)` | YES | `` |  |
| `country_id` | `int(11)` | YES | `` |  |
| `api_key` | `varchar(100)` | YES | `` |  |
| `api_secret` | `varchar(100)` | YES | `` |  |
| `api_date` | `datetime` | YES | `` |  |
| `profile_image` | `varchar(255)` | YES | `` |  |
| `is_employee` | `tinyint(1)` | NO | `0` | REQUIRED |
| `warehouse_id` | `int(11)` | YES | `` |  |
| `dashboard` | `enum('corporate','operation','customer_service','account','driver')` | NO | `corporate` | REQUIRED |
| `invalid_login_count` | `int(11)` | YES | `` |  |
| `user_account_id` | `int(11)` | YES | `` |  |
| `last_login_date` | `datetime` | YES | `` |  |
| `added_by` | `int(11)` | YES | `` |  |
| `added_date` | `datetime` | YES | `` |  |
| `updated_by` | `int(11)` | YES | `` |  |
| `updated_date` | `datetime` | YES | `` |  |
| `is_deleted` | `tinyint(1)` | NO | `0` | REQUIRED |
| `archive_server` | `tinyint(1)` | NO | `0` | REQUIRED |
| `carrier_setup_agreement` | `tinyint(1)` | NO | `0` | REQUIRED |
| `receive_email` | `enum('y','n')` | NO | `n` | REQUIRED |
| `tc_agreed_date` | `date` | YES | `` |  |
| `is_tc_agreed` | `enum('y','n','i')` | NO | `n` | REQUIRED |
| `address_2` | `varchar(50)` | YES | `` |  |
| `address_3` | `varchar(50)` | YES | `` |  |
| `city` | `varchar(50)` | YES | `` |  |
| `postcode` | `varchar(15)` | YES | `` |  |
| `state` | `varchar(50)` | YES | `` |  |
| `commission_break_event_amount` | `varchar(50)` | NO | `` | REQUIRED |
| `is_sale_pot_eligible` | `tinyint(1)` | NO | `0` | REQUIRED |
| `remember_token` | `varchar(100)` | YES | `` |  |
| `created_at` | `timestamp` | YES | `` |  |
| `updated_at` | `timestamp` | YES | `` |  |

---
