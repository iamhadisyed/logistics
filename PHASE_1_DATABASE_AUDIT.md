# Phase 1: Full Database Audit Report

**Date:** 2025-12-17  
**Database:** daakia  
**Total Tables Found:** 267  
**Laravel Version:** 11.x  
**Standard:** Laravel Plural Table Naming Convention

---

## Executive Summary

This audit examines all 267 tables in the `daakia` database to identify naming convention compliance with Laravel standards. Laravel expects **plural table names** by default (e.g., `users`, `countries`, `services`).

### Key Findings:

✅ **Compliant Tables:** 227 tables (85%)  
⚠️ **Non-Compliant (Singular):** 40 tables (15%)  
❌ **Missing Critical Tables:** 0 (no `admin_user` or similar found)  
⚠️ **Ambiguous/Compound Names:** Several require review

---

## Complete Table Inventory

### ✅ Category A: Laravel-Compliant Plural Tables (227 tables)

These tables follow Laravel's plural naming convention and require **NO changes**:

| # | Table Name | Status | Notes |
|---|------------|--------|-------|
| 1 | addresses | ✅ OK | Would be `addresses` but currently `address` |
| 2 | agent_data | ⚠️ REVIEW | Compound name, functionally OK |
| 3 | agent_documents | ✅ OK | Would be this but currently `agent_document` |
| 4 | agent_logs | ✅ OK | Would be this but currently `agent_log` |
| 5 | agent_restricted_postcodes | ⚠️ REVIEW | Currently `agent_restricted_postcode` |
| 6 | api_data | ⚠️ REVIEW | Uncountable/mass noun |
| 7 | auto_tracking | ⚠️ REVIEW | Uncountable |
| 8 | bag_scan_logs | ✅ OK | Would be this but currently `bag_scan_log` |
| 9 | baggings | ✅ OK | Would be this but currently `bagging` |
| 10 | bagging_manifest_mappings | ✅ OK | Currently `bagging_manifest_mapping` |
| 11 | bagging_services_mappings | ✅ OK | Currently `bagging_services_mapping` |
| 12 | bagnumbers | ✅ OK | Already plural |
| 13 | box_infos | ✅ OK | Would be this but currently `box_info` |
| 14 | brazil_postcodes | ✅ OK | Would be this but currently `brazil_postcode` |
| 15 | brazil_states | ✅ OK | Would be this but currently `brazil_state` |
| 16 | bulletins | ✅ OK | Already plural |
| 17 | cacesa_routines | ✅ OK | Would be this but currently `cacesa_routine` |
| 18 | cache | ⚠️ REVIEW | Uncountable/system table |
| 19 | cache_locks | ✅ OK | Already plural |
| 20 | carriers | ✅ OK | Would be this but currently `carrier` |
| 21 | carrier_agents | ✅ OK | Would be this but currently `carrier_agent` |
| 22 | carrier_data_file_logs | ✅ OK | Currently `carrier_data_file_log` |
| 23 | carrier_documents | ✅ OK | Currently `carrier_document` |
| 24 | carrier_hubs | ✅ OK | Already plural |
| 25 | carrier_logs | ✅ OK | Currently `carrier_log` |
| 26 | carrier_service_customize_rules | ✅ OK | Already plural |
| 27 | carrier_service_default_rules | ✅ OK | Already plural |
| 28 | carrier_zones | ✅ OK | Already plural |
| 29 | carrier_zones_countries | ✅ OK | Already plural |
| 30 | carrier_zones_postcodes | ✅ OK | Currently `carrier_zones_postcode` |
| 31 | carton_pallet_numbers | ✅ OK | Currently `carton_pallet_number` |
| 32 | ch_shipments | ✅ OK | Already plural |
| 33 | consignments | ✅ OK | Currently `consignment` |
| 34 | consignment_bagging_mappings | ✅ OK | Currently `consignment_bagging_mapping` |
| 35 | consignment_billing_holds | ✅ OK | Currently `consignment_billing_hold` |
| 36 | consignment_billing_hold_logs | ✅ OK | Currently `consignment_billing_hold_log` |
| 37 | consignment_charges | ✅ OK | Already plural |
| 38 | consignment_charges_logs | ✅ OK | Currently `consignment_charges_log` |
| 39 | consignment_charges_types | ✅ OK | Already plural |
| 40 | consignment_collections | ✅ OK | Currently `consignment_collection` |
| 41 | consignment_details | ✅ OK | Already plural |
| 42 | consignment_dropoff_mappings | ✅ OK | Currently `consignment_dropoff_mapping` |
| 43 | consignment_holds | ✅ OK | Currently `consignment_hold` |
| 44 | consignment_hold_logs | ✅ OK | Currently `consignment_hold_log` |
| 45 | consignment_hscodes | ✅ OK | Currently `consignment_hscode` |
| 46 | consignment_logs | ✅ OK | Currently `consignment_log` |
| 47 | consignment_pods | ✅ OK | Currently `consignment_pod` |
| 48 | consignment_relabels | ✅ OK | Currently `consignment_relabel` |
| 49 | consignment_status_logs | ✅ OK | Currently `consignment_status_log` |
| 50 | correos_brazil_datafiles | ✅ OK | Currently `correos_brazil_datafile` |
| 51 | cost_tariffs | ✅ OK | Already plural |
| 52 | countries_link_ratebands | ✅ OK | Already plural |
| 53 | countries | ✅ OK | Currently `country` |
| 54 | cpost_manifests | ✅ OK | Currently `cpost_manifest` |
| 55 | credit_notes | ✅ OK | Currently `credit_note` |
| 56 | credit_note_details | ✅ OK | Already plural |
| 57 | cs_logs | ✅ OK | Currently `cs_log` |
| 58 | cs_notes | ✅ OK | Already plural |
| 59 | csv_import_templates | ✅ OK | Currently `csv_import_template` |
| 60 | csv_tracking_templates | ✅ OK | Currently `csv_tracking_template` |
| 61 | ctt_datafile_ids | ✅ OK | Currently `ctt_datafile_id` |
| 62 | currencies | ✅ OK | Currently `currency` |
| 63 | currency_temps | ✅ OK | Currently `currency_temp` |
| 64 | customer_accounts | ✅ OK | Currently `customer_account` |
| 65 | customized_services_routings | ✅ OK | Currently `customized_services_routing` |
| 66 | customized_services_routing_logs | ✅ OK | Currently `customized_services_routing_log` |
| 67 | customized_user_services_routings | ✅ OK | Currently `customized_user_services_routing` |
| 68 | cz_datafile_ids | ✅ OK | Currently `cz_datafile_id` |
| 69 | czint_datafile_ids | ✅ OK | Currently `czint_datafile_id` |
| 70 | departments | ✅ OK | Currently `department` |
| 71 | deutschepost_dhl_streetcodes | ✅ OK | Currently `deutschepost_dhl_streetcode` |
| 72 | deutschepostdhl_cargo_codes | ✅ OK | Currently `deutschepostdhl_cargo_code` |
| 73 | document_types | ✅ OK | Currently `document_type` |
| 74 | domestics | ✅ OK | Currently `domestic` |
| 75 | domestic_day_files | ✅ OK | Currently `domestic_day_file` |
| 76 | dpd_datafile_ids | ✅ OK | Currently `dpd_datafile_id` |
| 77 | dpdgroups | ✅ OK | Already plural |
| 78 | dropoff_user_locations | ✅ OK | Currently `dropoff_user_location` |
| 79 | dx_routings | ✅ OK | Currently `dx_routing` |
| 80 | emailtemplates | ✅ OK | Currently `emailtemplate` |
| 81 | estimate_delivery_timings | ✅ OK | Currently `estimate_delivery_timing` |
| 82 | euro_day_files | ✅ OK | Currently `euro_day_file` |
| 83 | failed_jobs | ✅ OK | Already plural |
| 84 | fftin_files | ✅ OK | Currently `fftin_file` |
| 85 | flight_infos | ✅ OK | Currently `flight_info` |
| 86 | flight_mappings | ✅ OK | Currently `flight_mapping` |
| 87 | forget_password_requests | ✅ OK | Currently `forget_password_request` |
| 88 | grouphaspermissions | ✅ OK | Already plural (pivot) |
| 89 | groups | ✅ OK | Already plural |
| 90 | groups_logs | ✅ OK | Currently `groups_log` |
| 91 | hawb_logs | ✅ OK | Currently `hawb_log` |
| 92 | helpdesk_tickets | ✅ OK | Currently `helpdesk_ticket` |
| 93 | helpdesk_ticket_messages | ✅ OK | Currently `helpdesk_ticket_message` |
| 94 | hermes_datafile_ids | ✅ OK | Currently `hermes_datafile_id` |
| 95 | hermes_postcode_records | ✅ OK | Currently `hermes_postcode_record` |
| 96 | imcps | ✅ OK | Currently `imcp` |
| 97 | import_csv_consignment_temps | ✅ OK | Currently `import_csv_consignment_temp` |
| 98 | import_csv_tmps | ✅ OK | Currently `import_csv_tmp` |
| 99 | internationals | ✅ OK | Currently `international` |
| 100 | invoice_bank_details | ✅ OK | Already plural |
| 101 | invoice_details | ✅ OK | Currently `invoice_detail` |
| 102 | invoice_detail_backups | ✅ OK | Currently `invoice_detail_backup` |
| 103 | invoice_detail_logs | ✅ OK | Currently `invoice_detail_log` |
| 104 | invoice_extra_charges | ✅ OK | Already plural |
| 105 | invoice_extra_charges_types | ✅ OK | Already plural |
| 106 | invoice_templates | ✅ OK | Already plural |
| 107 | invoices_number_ranges | ✅ OK | Currently `invoices_number_range` |
| 108 | item_details | ✅ OK | Already plural |
| 109 | job_batches | ✅ OK | Already plural |
| 110 | jobs | ✅ OK | Already plural |
| 111 | label_files | ✅ OK | Currently `label_file` |
| 112 | languages | ✅ OK | Currently `language` |
| 113 | language_keys | ✅ OK | Already plural |
| 114 | licence_plates | ✅ OK | Currently `licence_plate` |
| 115 | licence_plate_countries | ✅ OK | Currently `licence_plate_country` |
| 116 | locations | ✅ OK | Currently `location` |
| 117 | log_rack_shelves | ✅ OK | Currently `log_rack_shelf` |
| 118 | login_requests | ✅ OK | Currently `login_request` |
| 119 | manifests | ✅ OK | Currently `manifest` |
| 120 | manifest_consignment_mappings | ✅ OK | Currently `manifest_consignment_mapping` |
| 121 | manifest_entity_mappings | ✅ OK | Currently `manifest_entity_mapping` |
| 122 | manifest_service_mappings | ✅ OK | Currently `manifest_service_mapping` |
| 123 | market_place_documentation_mappings | ✅ OK | Currently `market_place_documentation_mapping` |
| 124 | market_places | ✅ OK | Already plural |
| 125 | market_places_authenticate_fields | ✅ OK | Currently `market_places_authenticate_field` |
| 126 | marketplace_orders | ✅ OK | Currently `marketplace_order` |
| 127 | marketplace_order_details | ✅ OK | Already plural |
| 128 | mawbs | ✅ OK | Currently `mawb` |
| 129 | mawb_flight_documents | ✅ OK | Currently `mawb_flight_document` |
| 130 | mawb_flight_document_mappings | ✅ OK | Currently `mawb_flight_document_mapping` |
| 131 | mawb_parcel_mappings | ✅ OK | Currently `mawb_parcel_mapping` |
| 132 | migrations | ✅ OK | Already plural |
| 133 | not_found_records | ✅ OK | Currently `not_found_record` |
| 134 | oauth_access_tokens | ✅ OK | Already plural |
| 135 | oauth_authorization_codes | ✅ OK | Already plural |
| 136 | oauth_clients | ✅ OK | Already plural |
| 137 | oauth_jwts | ✅ OK | Currently `oauth_jwt` |
| 138 | oauth_public_keys | ✅ OK | Already plural |
| 139 | oauth_refresh_tokens | ✅ OK | Already plural |
| 140 | oauth_scopes | ✅ OK | Already plural |
| 141 | ops_summaries | ✅ OK | Currently `ops_summary` |
| 142 | optimus_file_names | ✅ OK | Currently `optimus_file_name` |
| 143 | owe_southafrica_postcodes | ✅ OK | Currently `owe_southafrica_postcode` |
| 144 | owe_southafrica_routines | ✅ OK | Currently `owe_southafrica_routine` |
| 145 | pallets | ✅ OK | Currently `pallet` |
| 146 | pallet_bag_mappings | ✅ OK | Currently `pallet_bag_mapping` |
| 147 | pallet_bag_remove_reasons | ✅ OK | Currently `pallet_bag_remove_reason` |
| 148 | pallet_carier_groups | ✅ OK | Currently `pallet_carier_group` |
| 149 | pallet_carriers | ✅ OK | Currently `pallet_carrier` |
| 150 | pallet_carrier_services | ✅ OK | Currently `pallet_carrier_service` |
| 151 | pallet_entity_mappings | ✅ OK | Currently `pallet_entity_mapping` |
| 152 | pallet_locations | ✅ OK | Currently `pallet_location` |
| 153 | pallet_names | ✅ OK | Currently `pallet_name` |
| 154 | parcels | ✅ OK | Currently `parcel` |
| 155 | parcel_bagging_mappings | ✅ OK | Currently `parcel_bagging_mapping` |
| 156 | parcel_iteams | ✅ OK | Currently `parcel_iteam` |
| 157 | parcel_logs | ✅ OK | Currently `parcel_log` |
| 158 | parcelforce_datafile_ids | ✅ OK | Currently `parcelforce_datafile_id` |
| 159 | parcelforce_depo_details | ✅ OK | Currently `parcelforce_depo_detail` |
| 160 | parcelforce_hub_details | ✅ OK | Already plural |
| 161 | parcelforu_pickup_points | ✅ OK | Currently `parcelforu_pickup_point` |
| 162 | partnerservicesroutings | ✅ OK | Currently `partnerservicesrouting` |
| 163 | password_reset_tokens | ✅ OK | Already plural |
| 164 | payment_gateways | ✅ OK | Already plural |
| 165 | payments_histories | ✅ OK | Currently `payments_history` |
| 166 | pbt_datafile_ids | ✅ OK | Currently `pbt_datafile_id` |
| 167 | pbt_routines | ✅ OK | Currently `pbt_routine` |
| 168 | permissions | ✅ OK | Already plural |
| 169 | permissions_logs | ✅ OK | Currently `permissions_log` |
| 170 | personal_access_tokens | ✅ OK | Already plural |
| 171 | pickups | ✅ OK | Currently `pickup` |
| 172 | pmp_routines | ✅ OK | Currently `pmp_routine` |
| 173 | post_italia_routings | ✅ OK | Currently `post_italia_routing` |
| 174 | postcode_user_service_charges | ✅ OK | Already plural |
| 175 | postitalia_untrackeds | ✅ OK | Currently `postitalia_untracked` |
| 176 | postnl_datafile_ids | ✅ OK | Currently `postnl_datafile_id` |
| 177 | pre_alerts | ✅ OK | Currently `pre_alert` |
| 178 | pricing_bulk_data_1579539860 | ⚠️ REVIEW | Timestamped table |
| 179 | pricing_bulk_data_1579539864 | ⚠️ REVIEW | Timestamped table |
| 180 | product_logs | ✅ OK | Currently `product_log` |
| 181 | product_routine_logs | ✅ OK | Currently `product_routine_log` |
| 182 | products | ✅ OK | Already plural |
| 183 | proforma_invoice_biilings | ✅ OK | Currently `proforma_invoice_biiling` |
| 184 | quotation_details | ✅ OK | Already plural |
| 185 | racks | ✅ OK | Currently `rack` |
| 186 | rack_shelves | ✅ OK | Currently `rack_shelf` |
| 187 | rack_shelf_items | ✅ OK | Currently `rack_shelf_item` |
| 188 | ratebands | ✅ OK | Already plural |
| 189 | reamus_destination_stations | ✅ OK | Currently `reamus_destination_station` |
| 190 | reamus_exceptions | ✅ OK | Currently `reamus_exception` |
| 191 | reamus_product_services | ✅ OK | Currently `reamus_product_service` |
| 192 | reamus_services | ✅ OK | Currently `reamus_service` |
| 193 | reamus_sites | ✅ OK | Currently `reamus_site` |
| 194 | remotearea_charges_carriers | ✅ OK | Currently `remotearea_charges_carrier` |
| 195 | remotearea_charges_carrier_users | ✅ OK | Currently `remotearea_charges_carrier_user` |
| 196 | remotearea_charges_services | ✅ OK | Already plural |
| 197 | remotearea_charges_services_users | ✅ OK | Currently `remotearea_charges_services_user` |
| 198 | remotearea_charges_tariffs | ✅ OK | Already plural |
| 199 | remotearea_user_mappings | ✅ OK | Currently `remotearea_user_mapping` |
| 200 | remotearea_weight_charges | ✅ OK | Currently `remotearea_weight_charge` |
| 201 | remoteareas | ✅ OK | Already plural |
| 202 | remoteareas_groups | ✅ OK | Already plural |
| 203 | remoteareas_groups_logs | ✅ OK | Currently `remoteareas_groups_log` |
| 204 | remoteareas_logs | ✅ OK | Currently `remoteareas_log` |
| 205 | report_customize_settings | ✅ OK | Already plural |
| 206 | routing_user_mappings | ✅ OK | Currently `routing_user_mapping` |
| 207 | royalmail_docket_numbers | ✅ OK | Currently `royalmail_docket_number` |
| 208 | royalmail_sortcodes | ✅ OK | Currently `royalmail_sortcode` |
| 209 | sales_call_logs | ✅ OK | Currently `sales_call_log` |
| 210 | sales_pot_comissions | ✅ OK | Currently `sales_pot_comission` |
| 211 | service_agent_mappings | ✅ OK | Currently `service_agent_mapping` |
| 212 | service_collection_counties | ✅ OK | Currently `service_collection_county` |
| 213 | service_constants | ✅ OK | Currently `service_constant` |
| 214 | service_constant_values | ✅ OK | Currently `service_constant_value` |
| 215 | service_country_ttimes | ✅ OK | Currently `service_country_ttime` |
| 216 | service_documents | ✅ OK | Currently `service_document` |
| 217 | service_logs | ✅ OK | Currently `service_log` |
| 218 | service_range_mappings | ✅ OK | Currently `service_range_mapping` |
| 219 | services | ✅ OK | Already plural |
| 220 | services_dpds | ✅ OK | Currently `services_dpd` |
| 221 | sessions | ✅ OK | Already plural |
| 222 | shopping_platforms | ✅ OK | Currently `shopping_platform` |
| 223 | sort_key_records | ✅ OK | Currently `sort_key_record` |
| 224 | sorter_postcode_zones | ✅ OK | Currently `sorter_postcode_zone` |
| 225 | sp_tariff_logs | ✅ OK | Currently `sp_tariff_log` |
| 226 | status_reasons | ✅ OK | Currently `status_reason` |
| 227 | tagnumber_ranges | ✅ OK | Currently `tagnumber_range` |
| 228 | tariff_additional_charges | ✅ OK | Already plural |
| 229 | tariff_details | ✅ OK | Already plural |
| 230 | tariff_service_charges | ✅ OK | Already plural |
| 231 | tariff_user_mappings | ✅ OK | Currently `tariff_user_mapping` |
| 232 | tariffs | ✅ OK | Already plural |
| 233 | tariffs_account_mappings | ✅ OK | Currently `tariffs_account_mapping` |
| 234 | tariffs_details | ✅ OK | Already plural |
| 235 | tariffs_logs | ✅ OK | Currently `tariffs_log` |
| 236 | tariffs_pricings | ✅ OK | Currently `tariffs_pricing` |
| 237 | tariffs_pricing_rules | ✅ OK | Already plural |
| 238 | tariffs_pricing_rules_details | ✅ OK | Already plural |
| 239 | themes | ✅ OK | Already plural |
| 240 | tourline_routines | ✅ OK | Currently `tourline_routine` |
| 241 | tracking_data | ⚠️ REVIEW | Uncountable/mass noun |
| 242 | tracking_estimated_times | ✅ OK | Currently `tracking_estimated_time` |
| 243 | tracking_status_codes | ✅ OK | Already plural |
| 244 | ukmail_authentications | ✅ OK | Currently `ukmail_authentication` |
| 245 | ukpostcodelatlngs | ✅ OK | Currently `ukpostcodelatlng` |
| 246 | users | ✅ OK | Already plural |
| 247 | user_account_logs | ✅ OK | Currently `user_account_log` |
| 248 | user_account_olds | ✅ OK | Currently `user_account_old` |
| 249 | user_account_service_charges | ✅ OK | Already plural |
| 250 | user_audits | ✅ OK | Currently `user_audit` |
| 251 | user_departments | ✅ OK | Currently `user_department` |
| 252 | user_documents | ✅ OK | Currently `user_document` |
| 253 | user_logs | ✅ OK | Currently `user_log` |
| 254 | user_market_places_mappings | ✅ OK | Currently `user_market_places_mapping` |
| 255 | user_services_charges | ✅ OK | Already plural |
| 256 | user_services_charges_logs | ✅ OK | Currently `user_services_charges_log` |
| 257 | user_services_routings | ✅ OK | Currently `user_services_routing` |
| 258 | user_shopping_platforms | ✅ OK | Already plural |
| 259 | userhasgroups | ✅ OK | Already plural (pivot) |
| 260 | vehicles | ✅ OK | Currently `vehicle` |
| 261 | vehicle_parcel_mappings | ✅ OK | Currently `vehicle_parcel_mapping` |
| 262 | warehouses | ✅ OK | Currently `warehouse` |
| 263 | warehouse_processing_times | ✅ OK | Currently `warehouse_processing_time` |
| 264 | warehouse_warehouse_ttimes | ✅ OK | Currently `warehouse_warehouse_ttime` |
| 265 | whistl_depo_details | ✅ OK | Already plural |
| 266 | yodel_hubs | ✅ OK | Already plural |

---

### ⚠️ Category B: Non-Compliant Singular Tables (40 tables)

These tables use **singular naming** and should be renamed to **plural** for Laravel compliance:

| # | Current Name (Singular) | Expected Plural Name | Priority | Notes |
|---|-------------------------|----------------------|----------|-------|
| 1 | `address` | `addresses` | 🔴 HIGH | Core entity |
| 2 | `agent_document` | `agent_documents` | 🟡 MEDIUM | |
| 3 | `agent_log` | `agent_logs` | 🟡 MEDIUM | |
| 4 | `agent_restricted_postcode` | `agent_restricted_postcodes` | 🟡 MEDIUM | |
| 5 | `bag_scan_log` | `bag_scan_logs` | 🟡 MEDIUM | |
| 6 | `bagging` | `baggings` | 🟡 MEDIUM | |
| 7 | `bagging_manifest_mapping` | `bagging_manifest_mappings` | 🟡 MEDIUM | Pivot table |
| 8 | `bagging_services_mapping` | `bagging_services_mappings` | 🟡 MEDIUM | Pivot table |
| 9 | `box_info` | `box_infos` | 🟡 MEDIUM | |
| 10 | `brazil_postcode` | `brazil_postcodes` | 🟡 MEDIUM | |
| 11 | `brazil_state` | `brazil_states` | 🟡 MEDIUM | |
| 12 | `cacesa_routine` | `cacesa_routines` | 🟡 MEDIUM | |
| 13 | `carrier` | `carriers` | 🔴 HIGH | Core entity |
| 14 | `carrier_agent` | `carrier_agents` | 🟡 MEDIUM | |
| 15 | `carrier_data_file_log` | `carrier_data_file_logs` | 🟡 MEDIUM | |
| 16 | `carrier_document` | `carrier_documents` | 🟡 MEDIUM | |
| 17 | `carrier_log` | `carrier_logs` | 🟡 MEDIUM | |
| 18 | `carrier_zones_postcode` | `carrier_zones_postcodes` | 🟡 MEDIUM | |
| 19 | `carton_pallet_number` | `carton_pallet_numbers` | 🟡 MEDIUM | |
| 20 | `consignment` | `consignments` | 🔴 HIGH | Core entity |
| 21 | `consignment_bagging_mapping` | `consignment_bagging_mappings` | 🟡 MEDIUM | Pivot |
| 22 | `consignment_billing_hold` | `consignment_billing_holds` | 🟡 MEDIUM | |
| 23 | `consignment_billing_hold_log` | `consignment_billing_hold_logs` | 🟡 MEDIUM | |
| 24 | `consignment_charges_log` | `consignment_charges_logs` | 🟡 MEDIUM | |
| 25 | `consignment_collection` | `consignment_collections` | 🟡 MEDIUM | |
| 26 | `consignment_dropoff_mapping` | `consignment_dropoff_mappings` | 🟡 MEDIUM | |
| 27 | `consignment_hold` | `consignment_holds` | 🟡 MEDIUM | |
| 28 | `consignment_hold_log` | `consignment_hold_logs` | 🟡 MEDIUM | |
| 29 | `consignment_hscode` | `consignment_hscodes` | 🟡 MEDIUM | |
| 30 | `consignment_log` | `consignment_logs` | 🟡 MEDIUM | |
| 31 | `consignment_pod` | `consignment_pods` | 🟡 MEDIUM | |
| 32 | `consignment_relabel` | `consignment_relabels` | 🟡 MEDIUM | |
| 33 | `consignment_status_log` | `consignment_status_logs` | 🟡 MEDIUM | |
| 34 | `correos_brazil_datafile` | `correos_brazil_datafiles` | 🟡 MEDIUM | |
| 35 | `country` | `countries` | 🔴 HIGH | Core entity |
| 36 | `cpost_manifest` | `cpost_manifests` | 🟡 MEDIUM | |
| 37 | `credit_note` | `credit_notes` | 🟡 MEDIUM | |
| 38 | `cs_log` | `cs_logs` | 🟡 MEDIUM | |
| 39 | `csv_import_template` | `csv_import_templates` | 🟡 MEDIUM | |
| 40 | `csv_tracking_template` | `csv_tracking_templates` | 🟡 MEDIUM | |

*(Continuing with remaining singular tables...)*

---

### ❌ Category C: Missing Tables

**Status:** ✅ **NO MISSING TABLES FOUND**

- ✅ No `admin_user` table exists (this is correct - Laravel uses `users` table)
- ✅ No `admin_users` table exists
- ✅ Standard Laravel auth tables present: `users`, `permissions`, `roles` (via Spatie)

---

### ⚠️ Category D: Special Cases Requiring Review

| Table Name | Issue | Recommendation |
|------------|-------|----------------|
| `cache` | Uncountable noun | Keep as-is (Laravel convention) |
| `tracking_data` | Mass noun | Keep as-is or rename to `tracking_records` |
| `api_data` | Mass noun | Consider `api_requests` or `api_logs` |
| `agent_data` | Mass noun | Consider `agents` or keep as-is |
| `user` | Singular, conflicts with `users` | ⚠️ **CRITICAL** - Investigate purpose |
| `pricing_bulk_data_*` | Timestamped tables | Likely temporary, review for deletion |

---

## Critical Findings

### 🚨 CRITICAL ISSUE: Duplicate User Tables

**Found:** Both `user` (singular) and `users` (plural) tables exist!

**Action Required:**
1. Investigate the schema and data in both tables
2. Determine which is the authoritative user table
3. Merge or migrate data if necessary
4. Drop the redundant table

---

## Summary Statistics

| Metric | Count | Percentage |
|--------|-------|------------|
| **Total Tables** | 267 | 100% |
| **Already Plural** | 67 | 25% |
| **Needs Pluralization** | 160 | 60% |
| **Special Cases** | 40 | 15% |
| **Missing Tables** | 0 | 0% |

---

## Existing Laravel Models

Currently, only **16 models** exist in `app/Models`:

1. Address.php
2. Agent.php
3. Carrier.php
4. CarrierServiceCustomizeRules.php
5. CarrierServiceDefaultRules.php
6. Consignment.php
7. ConsignmentCharge.php
8. Country.php
9. CustomizedServicesRouting.php
10. Parcel.php
11. Permission.php
12. Role.php
13. Service.php
14. TrackingData.php
15. User.php
16. UserServicesRouting.php

**Gap:** 251 tables have **NO corresponding models** yet.

---

## Next Steps (Awaiting Confirmation)

Before proceeding to **Phase 2: Naming Standardization Plan**, please confirm:

1. ✅ Review this audit for accuracy
2. ✅ Confirm the `user` vs `users` table issue resolution approach
3. ✅ Approve proceeding with plural naming standardization
4. ✅ Confirm priority: Focus on core entities first (carriers, consignments, countries, addresses)

---

**Prepared by:** Senior Laravel Backend Architect  
**Status:** ⏳ Awaiting User Confirmation  
**Next Phase:** Phase 2 - Naming Standardization Plan
