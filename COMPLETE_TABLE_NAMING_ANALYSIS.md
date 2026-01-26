# Complete Table Naming Analysis

**Database:** daakia  
**Total Tables:** 267  
**Analysis Date:** 2025-12-17

---

## Table Status Legend

- ✅ **OK** - Already plural, no action needed
- ⚠️ **RENAME** - Singular, needs pluralization
- 🔍 **REVIEW** - Special case, requires decision
- 🔴 **CRITICAL** - Urgent issue requiring immediate attention

---

## Complete Table List (Alphabetical)

| # | Current Name | Expected Name | Status | Priority | Notes |
|---|--------------|---------------|--------|----------|-------|
| 1 | address | addresses | ⚠️ RENAME | 🔴 HIGH | Core entity, has model |
| 2 | agent_data | agent_data | ✅ OK | - | Compound name, acceptable |
| 3 | agent_document | agent_documents | ⚠️ RENAME | 🟡 MEDIUM | |
| 4 | agent_log | agent_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 5 | agent_restricted_postcode | agent_restricted_postcodes | ⚠️ RENAME | 🟡 MEDIUM | |
| 6 | api_data | api_data | 🔍 REVIEW | - | Mass noun, consider api_requests |
| 7 | auto_tracking | auto_tracking | 🔍 REVIEW | - | Uncountable |
| 8 | bag_scan_log | bag_scan_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 9 | bagging | baggings | ⚠️ RENAME | 🟡 MEDIUM | |
| 10 | bagging_manifest_mapping | bagging_manifest_mappings | ⚠️ RENAME | 🟡 MEDIUM | Pivot table |
| 11 | bagging_services_mapping | bagging_services_mappings | ⚠️ RENAME | 🟡 MEDIUM | Pivot table |
| 12 | bagnumbers | bagnumbers | ✅ OK | - | Already plural |
| 13 | box_info | box_infos | ⚠️ RENAME | 🟡 MEDIUM | |
| 14 | brazil_postcode | brazil_postcodes | ⚠️ RENAME | 🟡 MEDIUM | |
| 15 | brazil_state | brazil_states | ⚠️ RENAME | 🟡 MEDIUM | |
| 16 | bulletins | bulletins | ✅ OK | - | Already plural |
| 17 | cacesa_routine | cacesa_routines | ⚠️ RENAME | 🟡 MEDIUM | |
| 18 | cache | cache | ✅ OK | - | Laravel system table |
| 19 | cache_locks | cache_locks | ✅ OK | - | Already plural |
| 20 | carrier | carriers | ⚠️ RENAME | 🔴 HIGH | Core entity, has model |
| 21 | carrier_agent | carrier_agents | ⚠️ RENAME | 🟡 MEDIUM | |
| 22 | carrier_data_file_log | carrier_data_file_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 23 | carrier_document | carrier_documents | ⚠️ RENAME | 🟡 MEDIUM | |
| 24 | carrier_hubs | carrier_hubs | ✅ OK | - | Already plural |
| 25 | carrier_log | carrier_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 26 | carrier_service_customize_rules | carrier_service_customize_rules | ✅ OK | - | Already plural |
| 27 | carrier_service_default_rules | carrier_service_default_rules | ✅ OK | - | Already plural |
| 28 | carrier_zones | carrier_zones | ✅ OK | - | Already plural |
| 29 | carrier_zones_countries | carrier_zones_countries | ✅ OK | - | Already plural |
| 30 | carrier_zones_postcode | carrier_zones_postcodes | ⚠️ RENAME | 🟡 MEDIUM | |
| 31 | carton_pallet_number | carton_pallet_numbers | ⚠️ RENAME | 🟡 MEDIUM | |
| 32 | ch_shipments | ch_shipments | ✅ OK | - | Already plural |
| 33 | consignment | consignments | ⚠️ RENAME | 🔴 HIGH | Core entity, has model |
| 34 | consignment_bagging_mapping | consignment_bagging_mappings | ⚠️ RENAME | 🟡 MEDIUM | Pivot |
| 35 | consignment_billing_hold | consignment_billing_holds | ⚠️ RENAME | 🟡 MEDIUM | |
| 36 | consignment_billing_hold_log | consignment_billing_hold_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 37 | consignment_charges | consignment_charges | ✅ OK | - | Already plural |
| 38 | consignment_charges_log | consignment_charges_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 39 | consignment_charges_types | consignment_charges_types | ✅ OK | - | Already plural |
| 40 | consignment_collection | consignment_collections | ⚠️ RENAME | 🟡 MEDIUM | |
| 41 | consignment_details | consignment_details | ✅ OK | - | Already plural |
| 42 | consignment_dropoff_mapping | consignment_dropoff_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 43 | consignment_hold | consignment_holds | ⚠️ RENAME | 🟡 MEDIUM | |
| 44 | consignment_hold_log | consignment_hold_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 45 | consignment_hscode | consignment_hscodes | ⚠️ RENAME | 🟡 MEDIUM | |
| 46 | consignment_log | consignment_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 47 | consignment_pod | consignment_pods | ⚠️ RENAME | 🟡 MEDIUM | |
| 48 | consignment_relabel | consignment_relabels | ⚠️ RENAME | 🟡 MEDIUM | |
| 49 | consignment_status_log | consignment_status_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 50 | correos_brazil_datafile | correos_brazil_datafiles | ⚠️ RENAME | 🟡 MEDIUM | |
| 51 | cost_tariffs | cost_tariffs | ✅ OK | - | Already plural |
| 52 | countries_link_ratebands | countries_link_ratebands | ✅ OK | - | Already plural |
| 53 | country | countries | ⚠️ RENAME | 🔴 HIGH | Core entity, has model |
| 54 | cpost_manifest | cpost_manifests | ⚠️ RENAME | 🟡 MEDIUM | |
| 55 | credit_note | credit_notes | ⚠️ RENAME | 🟡 MEDIUM | |
| 56 | credit_note_details | credit_note_details | ✅ OK | - | Already plural |
| 57 | cs_log | cs_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 58 | cs_notes | cs_notes | ✅ OK | - | Already plural |
| 59 | csv_import_template | csv_import_templates | ⚠️ RENAME | 🟡 MEDIUM | |
| 60 | csv_tracking_template | csv_tracking_templates | ⚠️ RENAME | 🟡 MEDIUM | |
| 61 | ctt_datafile_id | ctt_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 62 | currency | currencies | ⚠️ RENAME | 🟡 MEDIUM | |
| 63 | currency_temp | currency_temps | ⚠️ RENAME | 🟡 MEDIUM | |
| 64 | customer_account | customer_accounts | ⚠️ RENAME | 🟡 MEDIUM | |
| 65 | customized_services_routing | customized_services_routings | ⚠️ RENAME | 🟡 MEDIUM | Has model |
| 66 | customized_services_routing_log | customized_services_routing_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 67 | customized_user_services_routing | customized_user_services_routings | ⚠️ RENAME | 🟡 MEDIUM | |
| 68 | cz_datafile_id | cz_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 69 | czint_datafile_id | czint_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 70 | department | departments | ⚠️ RENAME | 🟡 MEDIUM | |
| 71 | deutschepost_dhl_streetcode | deutschepost_dhl_streetcodes | ⚠️ RENAME | 🟡 MEDIUM | |
| 72 | deutschepostdhl_cargo_code | deutschepostdhl_cargo_codes | ⚠️ RENAME | 🟡 MEDIUM | |
| 73 | document_type | document_types | ⚠️ RENAME | 🟡 MEDIUM | |
| 74 | domestic | domestics | ⚠️ RENAME | 🟡 MEDIUM | |
| 75 | domestic_day_file | domestic_day_files | ⚠️ RENAME | 🟡 MEDIUM | |
| 76 | dpd_datafile_id | dpd_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 77 | dpdgroups | dpdgroups | ✅ OK | - | Already plural |
| 78 | dropoff_user_location | dropoff_user_locations | ⚠️ RENAME | 🟡 MEDIUM | |
| 79 | dx_routing | dx_routings | ⚠️ RENAME | 🟡 MEDIUM | |
| 80 | emailtemplate | emailtemplates | ⚠️ RENAME | 🟡 MEDIUM | |
| 81 | estimate_delivery_timing | estimate_delivery_timings | ⚠️ RENAME | 🟡 MEDIUM | |
| 82 | euro_day_file | euro_day_files | ⚠️ RENAME | 🟡 MEDIUM | |
| 83 | failed_jobs | failed_jobs | ✅ OK | - | Laravel system table |
| 84 | fftin_file | fftin_files | ⚠️ RENAME | 🟡 MEDIUM | |
| 85 | flight_info | flight_infos | ⚠️ RENAME | 🟡 MEDIUM | |
| 86 | flight_mapping | flight_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 87 | forget_password_request | forget_password_requests | ⚠️ RENAME | 🟡 MEDIUM | |
| 88 | grouphaspermissions | grouphaspermissions | ✅ OK | - | Pivot table |
| 89 | groups | groups | ✅ OK | - | Already plural |
| 90 | groups_log | groups_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 91 | hawb_log | hawb_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 92 | helpdesk_ticket | helpdesk_tickets | ⚠️ RENAME | 🟡 MEDIUM | |
| 93 | helpdesk_ticket_message | helpdesk_ticket_messages | ⚠️ RENAME | 🟡 MEDIUM | |
| 94 | hermes_datafile_id | hermes_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 95 | hermes_postcode_record | hermes_postcode_records | ⚠️ RENAME | 🟡 MEDIUM | |
| 96 | imcp | imcps | ⚠️ RENAME | 🟡 MEDIUM | |
| 97 | import_csv_consignment_temp | import_csv_consignment_temps | ⚠️ RENAME | 🟡 MEDIUM | |
| 98 | import_csv_tmp | import_csv_tmps | ⚠️ RENAME | 🟡 MEDIUM | |
| 99 | international | internationals | ⚠️ RENAME | 🟡 MEDIUM | |
| 100 | invoice_bank_details | invoice_bank_details | ✅ OK | - | Already plural |
| 101 | invoice_detail | invoice_details | ⚠️ RENAME | 🟡 MEDIUM | |
| 102 | invoice_detail_backup | invoice_detail_backups | ⚠️ RENAME | 🟡 MEDIUM | |
| 103 | invoice_detail_log | invoice_detail_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 104 | invoice_extra_charges | invoice_extra_charges | ✅ OK | - | Already plural |
| 105 | invoice_extra_charges_types | invoice_extra_charges_types | ✅ OK | - | Already plural |
| 106 | invoice_templates | invoice_templates | ✅ OK | - | Already plural |
| 107 | invoices_number_range | invoices_number_ranges | ⚠️ RENAME | 🟡 MEDIUM | |
| 108 | item_details | item_details | ✅ OK | - | Already plural |
| 109 | job_batches | job_batches | ✅ OK | - | Laravel system table |
| 110 | jobs | jobs | ✅ OK | - | Laravel system table |
| 111 | label_file | label_files | ⚠️ RENAME | 🟡 MEDIUM | |
| 112 | language | languages | ⚠️ RENAME | 🟡 MEDIUM | |
| 113 | language_keys | language_keys | ✅ OK | - | Already plural |
| 114 | licence_plate | licence_plates | ⚠️ RENAME | 🟡 MEDIUM | |
| 115 | licence_plate_country | licence_plate_countries | ⚠️ RENAME | 🟡 MEDIUM | |
| 116 | location | locations | ⚠️ RENAME | 🟡 MEDIUM | |
| 117 | log_rack_shelf | log_rack_shelves | ⚠️ RENAME | 🟡 MEDIUM | |
| 118 | login_request | login_requests | ⚠️ RENAME | 🟡 MEDIUM | |
| 119 | manifest | manifests | ⚠️ RENAME | 🟡 MEDIUM | |
| 120 | manifest_consignment_mapping | manifest_consignment_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 121 | manifest_entity_mapping | manifest_entity_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 122 | manifest_service_mapping | manifest_service_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 123 | market_place_documentation_mapping | market_place_documentation_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 124 | market_places | market_places | ✅ OK | - | Already plural |
| 125 | market_places_authenticate_field | market_places_authenticate_fields | ⚠️ RENAME | 🟡 MEDIUM | |
| 126 | marketplace_order | marketplace_orders | ⚠️ RENAME | 🟡 MEDIUM | |
| 127 | marketplace_order_details | marketplace_order_details | ✅ OK | - | Already plural |
| 128 | mawb | mawbs | ⚠️ RENAME | 🟡 MEDIUM | |
| 129 | mawb_flight_document | mawb_flight_documents | ⚠️ RENAME | 🟡 MEDIUM | |
| 130 | mawb_flight_document_mapping | mawb_flight_document_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 131 | mawb_parcel_mapping | mawb_parcel_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 132 | migrations | migrations | ✅ OK | - | Laravel system table |
| 133 | not_found_record | not_found_records | ⚠️ RENAME | 🟡 MEDIUM | |
| 134 | oauth_access_tokens | oauth_access_tokens | ✅ OK | - | Already plural |
| 135 | oauth_authorization_codes | oauth_authorization_codes | ✅ OK | - | Already plural |
| 136 | oauth_clients | oauth_clients | ✅ OK | - | Already plural |
| 137 | oauth_jwt | oauth_jwts | ⚠️ RENAME | 🟡 MEDIUM | |
| 138 | oauth_public_keys | oauth_public_keys | ✅ OK | - | Already plural |
| 139 | oauth_refresh_tokens | oauth_refresh_tokens | ✅ OK | - | Already plural |
| 140 | oauth_scopes | oauth_scopes | ✅ OK | - | Already plural |
| 141 | ops_summary | ops_summaries | ⚠️ RENAME | 🟡 MEDIUM | |
| 142 | optimus_file_name | optimus_file_names | ⚠️ RENAME | 🟡 MEDIUM | |
| 143 | owe_southafrica_postcode | owe_southafrica_postcodes | ⚠️ RENAME | 🟡 MEDIUM | |
| 144 | owe_southafrica_routine | owe_southafrica_routines | ⚠️ RENAME | 🟡 MEDIUM | |
| 145 | pallet | pallets | ⚠️ RENAME | 🟡 MEDIUM | |
| 146 | pallet_bag_mapping | pallet_bag_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 147 | pallet_bag_remove_reason | pallet_bag_remove_reasons | ⚠️ RENAME | 🟡 MEDIUM | |
| 148 | pallet_carier_group | pallet_carier_groups | ⚠️ RENAME | 🟡 MEDIUM | |
| 149 | pallet_carrier | pallet_carriers | ⚠️ RENAME | 🟡 MEDIUM | |
| 150 | pallet_carrier_service | pallet_carrier_services | ⚠️ RENAME | 🟡 MEDIUM | |
| 151 | pallet_entity_mapping | pallet_entity_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 152 | pallet_location | pallet_locations | ⚠️ RENAME | 🟡 MEDIUM | |
| 153 | pallet_name | pallet_names | ⚠️ RENAME | 🟡 MEDIUM | |
| 154 | parcel | parcels | ⚠️ RENAME | 🔴 HIGH | Core entity, has model |
| 155 | parcel_bagging_mapping | parcel_bagging_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 156 | parcel_iteam | parcel_iteams | ⚠️ RENAME | 🟡 MEDIUM | |
| 157 | parcel_log | parcel_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 158 | parcelforce_datafile_id | parcelforce_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 159 | parcelforce_depo_detail | parcelforce_depo_details | ⚠️ RENAME | 🟡 MEDIUM | |
| 160 | parcelforce_hub_details | parcelforce_hub_details | ✅ OK | - | Already plural |
| 161 | parcelforu_pickup_point | parcelforu_pickup_points | ⚠️ RENAME | 🟡 MEDIUM | |
| 162 | partnerservicesrouting | partnerservicesroutings | ⚠️ RENAME | 🟡 MEDIUM | |
| 163 | password_reset_tokens | password_reset_tokens | ✅ OK | - | Laravel system table |
| 164 | payment_gateways | payment_gateways | ✅ OK | - | Already plural |
| 165 | payments_history | payments_histories | ⚠️ RENAME | 🟡 MEDIUM | |
| 166 | pbt_datafile_id | pbt_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 167 | pbt_routine | pbt_routines | ⚠️ RENAME | 🟡 MEDIUM | |
| 168 | permissions | permissions | ✅ OK | - | Already plural |
| 169 | permissions_log | permissions_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 170 | personal_access_tokens | personal_access_tokens | ✅ OK | - | Laravel system table |
| 171 | pickup | pickups | ⚠️ RENAME | 🟡 MEDIUM | |
| 172 | pmp_routine | pmp_routines | ⚠️ RENAME | 🟡 MEDIUM | |
| 173 | post_italia_routing | post_italia_routings | ⚠️ RENAME | 🟡 MEDIUM | |
| 174 | postcode_user_service_charges | postcode_user_service_charges | ✅ OK | - | Already plural |
| 175 | postitalia_untracked | postitalia_untrackeds | ⚠️ RENAME | 🟡 MEDIUM | |
| 176 | postnl_datafile_id | postnl_datafile_ids | ⚠️ RENAME | 🟡 MEDIUM | |
| 177 | pre_alert | pre_alerts | ⚠️ RENAME | 🟡 MEDIUM | |
| 178 | pricing_bulk_data_1579539860 | pricing_bulk_data_1579539860 | 🔍 REVIEW | - | Timestamped, review for deletion |
| 179 | pricing_bulk_data_1579539864 | pricing_bulk_data_1579539864 | 🔍 REVIEW | - | Timestamped, review for deletion |
| 180 | product_log | product_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 181 | product_routine_log | product_routine_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 182 | products | products | ✅ OK | - | Already plural |
| 183 | proforma_invoice_biiling | proforma_invoice_biilings | ⚠️ RENAME | 🟡 MEDIUM | Typo in name |
| 184 | quotation_details | quotation_details | ✅ OK | - | Already plural |
| 185 | rack | racks | ⚠️ RENAME | 🟡 MEDIUM | |
| 186 | rack_shelf | rack_shelves | ⚠️ RENAME | 🟡 MEDIUM | |
| 187 | rack_shelf_item | rack_shelf_items | ⚠️ RENAME | 🟡 MEDIUM | |
| 188 | ratebands | ratebands | ✅ OK | - | Already plural |
| 189 | reamus_destination_station | reamus_destination_stations | ⚠️ RENAME | 🟡 MEDIUM | |
| 190 | reamus_exception | reamus_exceptions | ⚠️ RENAME | 🟡 MEDIUM | |
| 191 | reamus_product_service | reamus_product_services | ⚠️ RENAME | 🟡 MEDIUM | |
| 192 | reamus_service | reamus_services | ⚠️ RENAME | 🟡 MEDIUM | |
| 193 | reamus_site | reamus_sites | ⚠️ RENAME | 🟡 MEDIUM | |
| 194 | remotearea_charges_carrier | remotearea_charges_carriers | ⚠️ RENAME | 🟡 MEDIUM | |
| 195 | remotearea_charges_carrier_user | remotearea_charges_carrier_users | ⚠️ RENAME | 🟡 MEDIUM | |
| 196 | remotearea_charges_services | remotearea_charges_services | ✅ OK | - | Already plural |
| 197 | remotearea_charges_services_user | remotearea_charges_services_users | ⚠️ RENAME | 🟡 MEDIUM | |
| 198 | remotearea_charges_tariffs | remotearea_charges_tariffs | ✅ OK | - | Already plural |
| 199 | remotearea_user_mapping | remotearea_user_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 200 | remotearea_weight_charge | remotearea_weight_charges | ⚠️ RENAME | 🟡 MEDIUM | |
| 201 | remoteareas | remoteareas | ✅ OK | - | Already plural |
| 202 | remoteareas_groups | remoteareas_groups | ✅ OK | - | Already plural |
| 203 | remoteareas_groups_log | remoteareas_groups_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 204 | remoteareas_log | remoteareas_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 205 | report_customize_settings | report_customize_settings | ✅ OK | - | Already plural |
| 206 | routing_user_mapping | routing_user_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 207 | royalmail_docket_number | royalmail_docket_numbers | ⚠️ RENAME | 🟡 MEDIUM | |
| 208 | royalmail_sortcode | royalmail_sortcodes | ⚠️ RENAME | 🟡 MEDIUM | |
| 209 | sales_call_log | sales_call_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 210 | sales_pot_comission | sales_pot_comissions | ⚠️ RENAME | 🟡 MEDIUM | Typo: commission |
| 211 | service_agent_mapping | service_agent_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 212 | service_collection_county | service_collection_counties | ⚠️ RENAME | 🟡 MEDIUM | |
| 213 | service_constant | service_constants | ⚠️ RENAME | 🟡 MEDIUM | |
| 214 | service_constant_value | service_constant_values | ⚠️ RENAME | 🟡 MEDIUM | |
| 215 | service_country_ttime | service_country_ttimes | ⚠️ RENAME | 🟡 MEDIUM | |
| 216 | service_document | service_documents | ⚠️ RENAME | 🟡 MEDIUM | |
| 217 | service_log | service_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 218 | service_range_mapping | service_range_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 219 | services | services | ✅ OK | - | Already plural, has model |
| 220 | services_dpd | services_dpds | ⚠️ RENAME | 🟡 MEDIUM | |
| 221 | sessions | sessions | ✅ OK | - | Laravel system table |
| 222 | shopping_platform | shopping_platforms | ⚠️ RENAME | 🟡 MEDIUM | |
| 223 | sort_key_record | sort_key_records | ⚠️ RENAME | 🟡 MEDIUM | |
| 224 | sorter_postcode_zone | sorter_postcode_zones | ⚠️ RENAME | 🟡 MEDIUM | |
| 225 | sp_tariff_log | sp_tariff_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 226 | status_reason | status_reasons | ⚠️ RENAME | 🟡 MEDIUM | |
| 227 | tagnumber_range | tagnumber_ranges | ⚠️ RENAME | 🟡 MEDIUM | |
| 228 | tariff_additional_charges | tariff_additional_charges | ✅ OK | - | Already plural |
| 229 | tariff_details | tariff_details | ✅ OK | - | Already plural |
| 230 | tariff_service_charges | tariff_service_charges | ✅ OK | - | Already plural |
| 231 | tariff_user_mapping | tariff_user_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 232 | tariffs | tariffs | ✅ OK | - | Already plural |
| 233 | tariffs_account_mapping | tariffs_account_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 234 | tariffs_details | tariffs_details | ✅ OK | - | Already plural |
| 235 | tariffs_log | tariffs_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 236 | tariffs_pricing | tariffs_pricings | ⚠️ RENAME | 🟡 MEDIUM | |
| 237 | tariffs_pricing_rules | tariffs_pricing_rules | ✅ OK | - | Already plural |
| 238 | tariffs_pricing_rules_details | tariffs_pricing_rules_details | ✅ OK | - | Already plural |
| 239 | themes | themes | ✅ OK | - | Already plural |
| 240 | tourline_routine | tourline_routines | ⚠️ RENAME | 🟡 MEDIUM | |
| 241 | tracking_data | tracking_data | 🔍 REVIEW | - | Mass noun, consider tracking_records |
| 242 | tracking_estimated_time | tracking_estimated_times | ⚠️ RENAME | 🟡 MEDIUM | |
| 243 | tracking_status_codes | tracking_status_codes | ✅ OK | - | Already plural |
| 244 | ukmail_authentication | ukmail_authentications | ⚠️ RENAME | 🟡 MEDIUM | |
| 245 | ukpostcodelatlng | ukpostcodelatlngs | ⚠️ RENAME | 🟡 MEDIUM | |
| 246 | user | **DROP/MERGE** | 🔴 CRITICAL | 🔴 CRITICAL | Conflicts with `users` |
| 247 | user_account_log | user_account_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 248 | user_account_old | user_account_olds | ⚠️ RENAME | 🟡 MEDIUM | |
| 249 | user_account_service_charges | user_account_service_charges | ✅ OK | - | Already plural |
| 250 | user_audit | user_audits | ⚠️ RENAME | 🟡 MEDIUM | |
| 251 | user_department | user_departments | ⚠️ RENAME | 🟡 MEDIUM | |
| 252 | user_document | user_documents | ⚠️ RENAME | 🟡 MEDIUM | |
| 253 | user_log | user_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 254 | user_market_places_mapping | user_market_places_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 255 | user_services_charges | user_services_charges | ✅ OK | - | Already plural |
| 256 | user_services_charges_log | user_services_charges_logs | ⚠️ RENAME | 🟡 MEDIUM | |
| 257 | user_services_routing | user_services_routings | ⚠️ RENAME | 🟡 MEDIUM | Has model |
| 258 | user_shopping_platforms | user_shopping_platforms | ✅ OK | - | Already plural |
| 259 | userhasgroups | userhasgroups | ✅ OK | - | Pivot table |
| 260 | users | users | ✅ OK | - | Laravel standard, has model |
| 261 | vehicle | vehicles | ⚠️ RENAME | 🟡 MEDIUM | |
| 262 | vehicle_parcel_mapping | vehicle_parcel_mappings | ⚠️ RENAME | 🟡 MEDIUM | |
| 263 | warehouse | warehouses | ⚠️ RENAME | 🟡 MEDIUM | |
| 264 | warehouse_processing_time | warehouse_processing_times | ⚠️ RENAME | 🟡 MEDIUM | |
| 265 | warehouse_warehouse_ttime | warehouse_warehouse_ttimes | ⚠️ RENAME | 🟡 MEDIUM | |
| 266 | whistl_depo_details | whistl_depo_details | ✅ OK | - | Already plural |
| 267 | yodel_hubs | yodel_hubs | ✅ OK | - | Already plural |

---

## Summary Statistics

| Category | Count | Percentage |
|----------|-------|------------|
| ✅ Already Plural (OK) | 67 | 25.1% |
| ⚠️ Needs Rename | 193 | 72.3% |
| 🔍 Needs Review | 6 | 2.2% |
| 🔴 Critical Issues | 1 | 0.4% |
| **TOTAL** | **267** | **100%** |

---

## Priority Breakdown

| Priority | Count | Tables |
|----------|-------|--------|
| 🔴 **CRITICAL** | 1 | user (conflicts with users) |
| 🔴 **HIGH** | 6 | address, carrier, consignment, country, parcel, service |
| 🟡 **MEDIUM** | 186 | All other singular tables |
| 🔍 **REVIEW** | 6 | cache, tracking_data, api_data, agent_data, pricing_bulk_data_* |

---

**Generated:** 2025-12-17  
**Next Step:** Phase 2 - Create detailed renaming and migration plan
