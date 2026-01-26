# Legacy Database Audit & Migration Checklist

This file tracks the migration status of all 938 tables found in the database. Use this to track progress chunk by chunk.

| Table Name | Row Count | Inferred Module | Status | Migrated? |
|------------|-----------|-----------------|--------|-----------|
| `daakia.address` | 612 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `daakia.agent_data` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.agent_document` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.agent_log` | 62 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.agent_restricted_postcode` | 6 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `daakia.api_data` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `daakia.auto_tracking` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.bag_scan_log` | 450 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `daakia.bagging` | 592 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `daakia.bagging_manifest_mapping` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `daakia.bagging_services_mapping` | 8492 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.bagnumbers` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `daakia.box_info` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.brazil_postcode` | 0 | **Geographic Data** | ⚠️ Empty | ⬜ Pending |
| `daakia.brazil_state` | 8126 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.bulletins` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.cacesa_routine` | 27978 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.cache` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.cache_locks` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.carrier` | 2 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_agent` | 0 | **Carriers** | ⚠️ Empty | ⬜ Pending |
| `daakia.carrier_data_file_log` | 316 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_document` | 18 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_hubs` | 120 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_log` | 182 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_service_customize_rules` | 44 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_service_default_rules` | 302 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_zones` | 4100 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_zones_countries` | 5456 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carrier_zones_postcode` | 10 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.carton_pallet_number` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.ch_shipments` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment` | 52 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `daakia.consignment_bagging_mapping` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_billing_hold` | 12 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `daakia.consignment_billing_hold_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_charges` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_charges_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_charges_types` | 60 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `daakia.consignment_collection` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_details` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_dropoff_mapping` | 16 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `daakia.consignment_hold` | 2 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `daakia.consignment_hold_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_hscode` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_pod` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.consignment_relabel` | 216 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `daakia.consignment_status_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.correos_brazil_datafile` | 18 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.cost_tariffs` | 20 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.countries_link_ratebands` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.country` | 538 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `daakia.cpost_manifest` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `daakia.credit_note` | 42 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.credit_note_details` | 50 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.cs_log` | 16 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.cs_notes` | 8 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.csv_import_template` | 34 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.csv_tracking_template` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.ctt_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.currency` | 180 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.currency_temp` | 192 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.customer_account` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.customized_services_routing` | 76 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.customized_services_routing_log` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `daakia.customized_user_services_routing` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `daakia.cz_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.czint_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.department` | 14 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.deutschepost_dhl_streetcode` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.deutschepostdhl_cargo_code` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.document_type` | 34 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.domestic` | 22 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.domestic_day_file` | 1410 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.dpd_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.dpdgroups` | 158 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.dropoff_user_location` | 58 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.dx_routing` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.emailtemplate` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.estimate_delivery_timing` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.euro_day_file` | 200 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.failed_jobs` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.fftin_file` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.flight_info` | 64 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.flight_mapping` | 82 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.forget_password_request` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.grouphaspermissions` | 3844 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.groups` | 102 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.groups_log` | 442 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.hawb_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `daakia.helpdesk_ticket` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.helpdesk_ticket_message` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.hermes_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.hermes_postcode_record` | 0 | **Geographic Data** | ⚠️ Empty | ⬜ Pending |
| `daakia.imcp` | 3706 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.import_csv_consignment_temp` | 42 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `daakia.import_csv_tmp` | 250 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.international` | 108 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.invoice_bank_details` | 16 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.invoice_detail` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `daakia.invoice_detail_backup` | 276 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.invoice_detail_log` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `daakia.invoice_extra_charges` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `daakia.invoice_extra_charges_types` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `daakia.invoice_templates` | 4 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.invoices_number_range` | 114 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.item_details` | 552 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.job_batches` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.jobs` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.label_file` | 0 | **Labels & Printing** | ⚠️ Empty | ⬜ Pending |
| `daakia.language` | 8 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.language_keys` | 28 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.licence_plate` | 166 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.licence_plate_country` | 8 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `daakia.location` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.log_rack_shelf` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `daakia.login_request` | 74 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.manifest` | 18 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `daakia.manifest_consignment_mapping` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `daakia.manifest_entity_mapping` | 690 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `daakia.manifest_service_mapping` | 40 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.market_place_documentation_mapping` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.market_places` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.market_places_authenticate_field` | 374 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.marketplace_order` | 22 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.marketplace_order_details` | 48 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.mawb` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.mawb_flight_document` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.mawb_flight_document_mapping` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.mawb_parcel_mapping` | 30 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.migrations` | 5 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.not_found_record` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.oauth_access_tokens` | 24 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.oauth_authorization_codes` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.oauth_clients` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.oauth_jwt` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.oauth_public_keys` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.oauth_refresh_tokens` | 20 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.oauth_scopes` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.ops_summary` | 22 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.optimus_file_name` | 28 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.owe_southafrica_postcode` | 26 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `daakia.owe_southafrica_routine` | 22 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet` | 38 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet_bag_mapping` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `daakia.pallet_bag_remove_reason` | 2 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet_carier_group` | 12 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet_carrier` | 6 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet_carrier_service` | 22 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet_entity_mapping` | 66 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet_location` | 22 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pallet_name` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.parcel` | 72 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.parcel_bagging_mapping` | 74 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `daakia.parcel_iteam` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.parcel_log` | 2 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.parcelforce_datafile_id` | 52 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.parcelforce_depo_detail` | 34 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.parcelforce_hub_details` | 16 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.parcelforu_pickup_point` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.partnerservicesrouting` | 256 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.password_reset_tokens` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.payment_gateways` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.payments_history` | 92 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pbt_datafile_id` | 26 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pbt_routine` | 74 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.permissions` | 812 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.permissions_log` | 432 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.personal_access_tokens` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.pickup` | 20 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pmp_routine` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.post_italia_routing` | 56 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.postcode_user_service_charges` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `daakia.postitalia_untracked` | 26 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.postnl_datafile_id` | 74 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pre_alert` | 58 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.pricing_bulk_data_1579539860` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.pricing_bulk_data_1579539864` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.product_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `daakia.product_routine_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `daakia.products` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.proforma_invoice_biiling` | 78 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.quotation_details` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.rack` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.rack_shelf` | 30 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.rack_shelf_item` | 32 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.ratebands` | 1658 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.reamus_destination_station` | 62 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.reamus_exception` | 26 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.reamus_product_service` | 26 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.reamus_service` | 154 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.reamus_site` | 28 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.remotearea_charges_carrier` | 2 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.remotearea_charges_carrier_user` | 4 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `daakia.remotearea_charges_services` | 2 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.remotearea_charges_services_user` | 40 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.remotearea_charges_tariffs` | 908 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.remotearea_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `daakia.remotearea_weight_charge` | 480 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.remoteareas` | 30 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.remoteareas_groups` | 96 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.remoteareas_groups_log` | 42 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.remoteareas_log` | 46 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `daakia.report_customize_settings` | 20 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.routing_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `daakia.royalmail_docket_number` | 22 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.royalmail_sortcode` | 852 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.sales_call_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `daakia.sales_pot_comission` | 30 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.service_agent_mapping` | 42 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.service_collection_county` | 12 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.service_constant` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `daakia.service_constant_value` | 30 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.service_country_ttime` | 56 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.service_document` | 8 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.service_log` | 28 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.service_range_mapping` | 274 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.services` | 4 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.services_dpd` | 166 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.sessions` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.shopping_platform` | 32 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.sort_key_record` | 46 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.sorter_postcode_zone` | 238 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `daakia.sp_tariff_log` | 42 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.status_reason` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.tagnumber_range` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.tariff_additional_charges` | 78 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.tariff_details` | 16 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.tariff_service_charges` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `daakia.tariff_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `daakia.tariffs` | 28 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.tariffs_account_mapping` | 288 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.tariffs_details` | 28 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.tariffs_log` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `daakia.tariffs_pricing` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `daakia.tariffs_pricing_rules` | 76 | **Finance** | ✅ Has Data | ⬜ Pending |
| `daakia.tariffs_pricing_rules_details` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `daakia.themes` | 12 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.tourline_routine` | 40 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.tracking_data` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `daakia.tracking_estimated_time` | 648 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.tracking_status_codes` | 16 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.ukmail_authentication` | 8 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.ukpostcodelatlng` | 0 | **Geographic Data** | ⚠️ Empty | ⬜ Pending |
| `daakia.user` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `daakia.user_account_log` | 26 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.user_account_old` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `daakia.user_account_service_charges` | 50 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.user_audit` | 96 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.user_department` | 8 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.user_document` | 10 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.user_log` | 1222 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.user_market_places_mapping` | 38 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.user_services_charges` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `daakia.user_services_charges_log` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `daakia.user_services_routing` | 36 | **Services** | ✅ Has Data | ⬜ Pending |
| `daakia.user_shopping_platforms` | 24 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.userhasgroups` | 1308 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `daakia.users` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `daakia.vehicle` | 24 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.vehicle_parcel_mapping` | 632 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.warehouse` | 22 | **Warehouse** | ✅ Has Data | ⬜ Pending |
| `daakia.warehouse_processing_time` | 96 | **Warehouse** | ✅ Has Data | ⬜ Pending |
| `daakia.warehouse_warehouse_ttime` | 10 | **Warehouse** | ✅ Has Data | ⬜ Pending |
| `daakia.whistl_depo_details` | 18 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `daakia.yodel_hubs` | 16 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `logistics.address` | 306 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.agent_data` | 55 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.agent_document` | 3 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.agent_log` | 31 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.agent_restricted_postcode` | 3 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.api_data` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.auto_tracking` | 16 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.bag_scan_log` | 225 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `logistics.bagging` | 296 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `logistics.bagging_manifest_mapping` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `logistics.bagging_services_mapping` | 4246 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.bagnumbers` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `logistics.box_info` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.brazil_postcode` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.brazil_state` | 4063 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.bulletins` | 3 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.cacesa_routine` | 13989 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier` | 1 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_agent` | 0 | **Carriers** | ⚠️ Empty | ⬜ Pending |
| `logistics.carrier_data_file_log` | 158 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_document` | 9 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_hubs` | 60 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_log` | 91 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_service_customize_rules` | 22 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_service_default_rules` | 151 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_zones` | 2050 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_zones_countries` | 2728 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carrier_zones_postcode` | 5 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.carton_pallet_number` | 492 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.ch_shipments` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment` | 286 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `logistics.consignment_bagging_mapping` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_billing_hold` | 6 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `logistics.consignment_billing_hold_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_charges` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_charges_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_charges_types` | 30 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `logistics.consignment_collection` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_details` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_dropoff_mapping` | 8 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `logistics.consignment_hold` | 1 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `logistics.consignment_hold_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_hscode` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_pod` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.consignment_relabel` | 108 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `logistics.consignment_status_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.correos_brazil_datafile` | 9 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.cost_tariffs` | 10 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.countries_link_ratebands` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.country` | 269 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.cpost_manifest` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `logistics.credit_note` | 21 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.credit_note_details` | 25 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.cs_log` | 8 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.cs_notes` | 4 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.csv_import_template` | 17 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.csv_tracking_template` | 2 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.ctt_datafile_id` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.currency` | 90 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.currency_temp` | 96 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.customer_account` | 4 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.customized_services_routing` | 13953 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.customized_services_routing_log` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `logistics.customized_user_services_routing` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `logistics.cz_datafile_id` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.czint_datafile_id` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.department` | 7 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.deutschepost_dhl_streetcode` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.deutschepostdhl_cargo_code` | 345 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.document_type` | 17 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.domestic` | 12000 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.domestic_day_file` | 9339 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.dpd_datafile_id` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.dpdgroups` | 79 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.dropoff_user_location` | 29 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `logistics.dx_routing` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.emailtemplate` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.estimate_delivery_timing` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.euro_day_file` | 100 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.fftin_file` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.flight_info` | 32 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.flight_mapping` | 41 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.forget_password_request` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.grouphaspermissions` | 1922 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.groups` | 51 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.groups_log` | 221 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.hawb_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.helpdesk_ticket` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.helpdesk_ticket_message` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.hermes_datafile_id` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.hermes_postcode_record` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.imcp` | 1853 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.import_csv_consignment_temp` | 247 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `logistics.import_csv_tmp` | 5891 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.international` | 128254 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.invoice_bank_details` | 8 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.invoice_detail` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `logistics.invoice_detail_backup` | 9624 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.invoice_detail_log` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `logistics.invoice_extra_charges` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `logistics.invoice_extra_charges_types` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `logistics.invoice_templates` | 2 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.invoices` | 1762 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.invoices_manual` | 580 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.invoices_manual_details` | 964 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.invoices_number_range` | 57 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.item_details` | 276 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.label_file` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.language` | 4 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.language_keys` | 3364 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.licence_plate` | 83 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.licence_plate_country` | 4 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.location` | 15 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.log_rack_shelf` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.login_request` | 15397 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.manifest` | 9 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `logistics.manifest_consignment_mapping` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `logistics.manifest_entity_mapping` | 345 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `logistics.manifest_service_mapping` | 20 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.market_place_documentation_mapping` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.market_places` | 81 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.market_places_authenticate_field` | 187 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.marketplace_order` | 11 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.marketplace_order_details` | 24 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.mawb` | 106 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.mawb_flight_document` | 1 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.mawb_flight_document_mapping` | 1 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.mawb_parcel_mapping` | 4266 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.not_found_record` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.oauth_access_tokens` | 84924 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.oauth_authorization_codes` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.oauth_clients` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.oauth_jwt` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.oauth_public_keys` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.oauth_refresh_tokens` | 84483 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.oauth_scopes` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.ops_summary` | 2583 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.optimus_file_name` | 40745 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.owe_southafrica_postcode` | 5864 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.owe_southafrica_routine` | 571 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet` | 3369 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet_bag_mapping` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `logistics.pallet_bag_remove_reason` | 1 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet_carier_group` | 6 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet_carrier` | 3 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet_carrier_service` | 11 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet_entity_mapping` | 33 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet_location` | 106533 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pallet_name` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.parcel` | 149163 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.parcel_bagging_mapping` | 4066 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `logistics.parcel_iteam` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.parcel_log` | 1 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.parcelforce_datafile_id` | 155 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.parcelforce_depo_detail` | 11312 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.parcelforce_hub_details` | 382 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.parcelforu_pickup_point` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.partnerservicesrouting` | 128 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.payment_gateways` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.payments_history` | 19404 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pbt_datafile_id` | 13 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pbt_routine` | 2322 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.permissions` | 406 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.permissions_log` | 216 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pickup` | 434 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pmp_routine` | 3055 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.post_italia_routing` | 4616 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.postcode_user_service_charges` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `logistics.postitalia_untracked` | 4643 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.postnl_datafile_id` | 37 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pre_alert` | 3460 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.pricing_bulk_data_1579539860` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.pricing_bulk_data_1579539864` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.product_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.product_routine_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.products` | 2 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.proforma_invoice_biiling` | 5135 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.quotation_details` | 58 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.rack` | 17 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.rack_shelf` | 5044 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.rack_shelf_item` | 1730 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.ratebands` | 829 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.reamus_destination_station` | 202813 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.reamus_exception` | 7698 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.reamus_product_service` | 3397 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.reamus_service` | 77 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.reamus_site` | 2316 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.remotearea_charges_carrier` | 1 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.remotearea_charges_carrier_user` | 2 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `logistics.remotearea_charges_services` | 1 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.remotearea_charges_services_user` | 20 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.remotearea_charges_tariffs` | 454 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.remotearea_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `logistics.remotearea_weight_charge` | 240 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.remoteareas` | 11492 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.remoteareas_groups` | 322 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.remoteareas_groups_log` | 21 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.remoteareas_log` | 56 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.report_customize_settings` | 10 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.routing_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `logistics.royalmail_docket_number` | 69890 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.royalmail_sortcode` | 426 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.sales_call_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.sales_pot_comission` | 15 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.service_agent_mapping` | 157 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.service_collection_county` | 6 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.service_constant` | 434 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.service_constant_value` | 1791 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.service_country_ttime` | 9588 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.service_document` | 4 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.service_log` | 525 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.service_range_mapping` | 137 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.services` | 2 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.services_dpd` | 83 | **Services** | ✅ Has Data | ⬜ Pending |
| `logistics.shopping_platform` | 16 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.sort_key_record` | 24184 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.sorter_postcode_zone` | 119 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.sp_tariff_log` | 2783 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.status_reason` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `logistics.tagnumber_range` | 8 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.tariff_additional_charges` | 39 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.tariff_details` | 8 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.tariff_service_charges` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `logistics.tariff_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `logistics.tariffs` | 19083 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.tariffs_account_mapping` | 144 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.tariffs_details` | 27391 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.tariffs_log` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `logistics.tariffs_pricing` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `logistics.tariffs_pricing_rules` | 38 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.tariffs_pricing_rules_details` | 23 | **Finance** | ✅ Has Data | ⬜ Pending |
| `logistics.themes` | 6 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `logistics.tourline_routine` | 30023 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `phpmyadmin.pma__bookmark` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__central_columns` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__column_info` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__designer_settings` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__export_templates` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__favorite` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__history` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__navigationhiding` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__pdf_pages` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__recent` | 1 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `phpmyadmin.pma__relation` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__savedsearches` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__table_coords` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__table_info` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__table_uiprefs` | 2 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `phpmyadmin.pma__tracking` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__userconfig` | 1 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `phpmyadmin.pma__usergroups` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `phpmyadmin.pma__users` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `smart_school.alumni_events` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.alumni_students` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.attendence_type` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.book_issues` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.books` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.captcha` | 5 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.categories` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.certificates` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.chat_connections` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.chat_messages` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.chat_users` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `smart_school.class_sections` | 13 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.class_teacher` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.classes` | 7 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.complaint` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.complaint_type` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.content_for` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.contents` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.custom_field_values` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.custom_fields` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.department` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.disable_reason` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.dispatch_receive` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.email_config` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.enquiry` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.enquiry_type` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.events` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_group_class_batch_exam_students` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_group_class_batch_exam_subjects` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_group_class_batch_exams` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_group_exam_connections` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_group_exam_results` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_group_students` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_groups` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_results` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exam_schedules` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.exams` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.expense_head` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.expenses` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.fee_groups` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.fee_groups_feetype` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.fee_receipt_no` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.fee_session_groups` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.feecategory` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.feemasters` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.fees_discounts` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.fees_reminder` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.feetype` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.filetypes` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.follow_up` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.front_cms_media_gallery` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.front_cms_menu_items` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.front_cms_menus` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.front_cms_page_contents` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.front_cms_pages` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.front_cms_program_photos` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.front_cms_programs` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.front_cms_settings` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.general_calls` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.grades` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.homework` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.homework_evaluation` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.hostel` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.hostel_rooms` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.id_card` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.income` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.income_head` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.item` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.item_category` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.item_issue` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.item_stock` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.item_store` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.item_supplier` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.languages` | 76 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.leave_types` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.lesson` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.libarary_members` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.logs` | 160 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smart_school.messages` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.migrations` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.multi_class_students` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.notification_roles` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `smart_school.notification_setting` | 12 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.online_admission_fields` | 40 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.online_admission_payment` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.online_admissions` | 13 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.onlineexam` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.onlineexam_attempts` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.onlineexam_questions` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.onlineexam_student_results` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.onlineexam_students` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.payment_settings` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.payslip_allowance` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.permission_category` | 201 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.permission_group` | 28 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.permission_student` | 18 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.print_headerfooter` | 3 | **Labels & Printing** | ✅ Has Data | ⬜ Pending |
| `smart_school.question_answers` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.question_options` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.questions` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.read_notification` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.reference` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.roles` | 6 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smart_school.roles_permissions` | 579 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smart_school.room_types` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.sch_settings` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.school_houses` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.sections` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.send_notification` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.sessions` | 14 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.sms_config` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.source` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.staff` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.staff_attendance` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.staff_attendance_type` | 5 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.staff_designation` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.staff_id_card` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.staff_leave_details` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.staff_leave_request` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.staff_payroll` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.staff_payslip` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.staff_rating` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.staff_roles` | 2 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smart_school.staff_timeline` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_applyleave` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_attendences` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_doc` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_edit_fields` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_fees` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_fees_deposite` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.student_fees_discounts` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_fees_master` | 12 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.student_session` | 30 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.student_sibling` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_subject_attendances` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.student_timeline` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.students` | 11 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.subject_group_class_sections` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.subject_group_subjects` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.subject_groups` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.subject_syllabus` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.subject_timetable` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.subjects` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.submit_assignment` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.teacher_subjects` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.template_admitcards` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.template_marksheets` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.timetables` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.topic` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smart_school.transport_route` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.userlog` | 34 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smart_school.users` | 22 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smart_school.users_authentication` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `smart_school.vehicle_routes` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.vehicles` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.visitors_book` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smart_school.visitors_purpose` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.address` | 306 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.agent_data` | 55 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.agent_document` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.agent_log` | 31 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.agent_restricted_postcode` | 3 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.api_data` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.auto_tracking` | 16 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.bag_scan_log` | 225 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.bagging` | 296 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.bagging_manifest_mapping` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.bagging_services_mapping` | 4246 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.bagnumbers` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.box_info` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.brazil_postcode` | 0 | **Geographic Data** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.brazil_state` | 4063 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.bulletins` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.cacesa_routine` | 13989 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier` | 1 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_agent` | 0 | **Carriers** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.carrier_data_file_log` | 158 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_document` | 9 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_hubs` | 60 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_log` | 91 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_service_customize_rules` | 22 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_service_default_rules` | 151 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_zones` | 2050 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_zones_countries` | 2728 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carrier_zones_postcode` | 5 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.carton_pallet_number` | 492 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.ch_shipments` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment` | 26 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.consignment_bagging_mapping` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_billing_hold` | 6 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.consignment_billing_hold_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_charges` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_charges_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_charges_types` | 30 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.consignment_collection` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_details` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_dropoff_mapping` | 8 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.consignment_hold` | 1 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.consignment_hold_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_hscode` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_pod` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.consignment_relabel` | 108 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.consignment_status_log` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.correos_brazil_datafile` | 9 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.cost_tariffs` | 10 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.countries_link_ratebands` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.country` | 269 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.cpost_manifest` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.credit_note` | 21 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.credit_note_details` | 25 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.cs_log` | 8 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.cs_notes` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.csv_import_template` | 17 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.csv_tracking_template` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.ctt_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.currency` | 90 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.currency_temp` | 96 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.customer_account` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.customized_services_routing` | 38 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.customized_services_routing_log` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.customized_user_services_routing` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.cz_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.czint_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.department` | 7 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.deutschepost_dhl_streetcode` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.deutschepostdhl_cargo_code` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.document_type` | 17 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.domestic` | 11 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.domestic_day_file` | 705 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.dpd_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.dpdgroups` | 79 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.dropoff_user_location` | 29 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.dx_routing` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.emailtemplate` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.estimate_delivery_timing` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.euro_day_file` | 100 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.fftin_file` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.flight_info` | 32 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.flight_mapping` | 41 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.forget_password_request` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.grouphaspermissions` | 1922 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.groups` | 51 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.groups_log` | 221 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.hawb_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.helpdesk_ticket` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.helpdesk_ticket_message` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.hermes_datafile_id` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.hermes_postcode_record` | 0 | **Geographic Data** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.imcp` | 1853 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.import_csv_consignment_temp` | 21 | **Consignments** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.import_csv_tmp` | 125 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.international` | 54 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.invoice_bank_details` | 8 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.invoice_detail` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.invoice_detail_backup` | 138 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.invoice_detail_log` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.invoice_extra_charges` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.invoice_extra_charges_types` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.invoice_templates` | 2 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.invoices` | 110 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.invoices_manual` | 193 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.invoices_manual_details` | 13 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.invoices_number_range` | 57 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.item_details` | 276 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.label_file` | 0 | **Labels & Printing** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.language` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.language_keys` | 14 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.licence_plate` | 83 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.licence_plate_country` | 4 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.location` | 15 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.log_rack_shelf` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.login_request` | 37 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.manifest` | 9 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.manifest_consignment_mapping` | 0 | **Consignments** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.manifest_entity_mapping` | 345 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.manifest_service_mapping` | 20 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.market_place_documentation_mapping` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.market_places` | 81 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.market_places_authenticate_field` | 187 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.marketplace_order` | 11 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.marketplace_order_details` | 24 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.mawb` | 106 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.mawb_flight_document` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.mawb_flight_document_mapping` | 1 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.mawb_parcel_mapping` | 15 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.not_found_record` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.oauth_access_tokens` | 12 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.oauth_authorization_codes` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.oauth_clients` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.oauth_jwt` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.oauth_public_keys` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.oauth_refresh_tokens` | 10 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.oauth_scopes` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.ops_summary` | 11 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.optimus_file_name` | 14 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.owe_southafrica_postcode` | 13 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.owe_southafrica_routine` | 11 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet` | 19 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet_bag_mapping` | 0 | **Operations (Manifest/Bag)** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.pallet_bag_remove_reason` | 1 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet_carier_group` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet_carrier` | 3 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet_carrier_service` | 11 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet_entity_mapping` | 33 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet_location` | 11 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pallet_name` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.parcel` | 36 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.parcel_bagging_mapping` | 37 | **Operations (Manifest/Bag)** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.parcel_iteam` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.parcel_log` | 1 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.parcelforce_datafile_id` | 26 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.parcelforce_depo_detail` | 17 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.parcelforce_hub_details` | 8 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.parcelforu_pickup_point` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.partnerservicesrouting` | 128 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.payment_gateways` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.payments_history` | 46 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pbt_datafile_id` | 13 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pbt_routine` | 37 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.permissions` | 406 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.permissions_log` | 216 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pickup` | 10 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pmp_routine` | 3 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.post_italia_routing` | 28 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.postcode_user_service_charges` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.postitalia_untracked` | 13 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.postnl_datafile_id` | 37 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pre_alert` | 29 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.pricing_bulk_data_1579539860` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.pricing_bulk_data_1579539864` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.product_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.product_routine_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.products` | 2 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.proforma_invoice_biiling` | 39 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.quotation_details` | 58 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.rack` | 17 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.rack_shelf` | 15 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.rack_shelf_item` | 16 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.ratebands` | 829 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.reamus_destination_station` | 31 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.reamus_exception` | 13 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.reamus_product_service` | 13 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.reamus_service` | 77 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.reamus_site` | 14 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remotearea_charges_carrier` | 1 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remotearea_charges_carrier_user` | 2 | **Carriers** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remotearea_charges_services` | 1 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remotearea_charges_services_user` | 20 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remotearea_charges_tariffs` | 454 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remotearea_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.remotearea_weight_charge` | 240 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remoteareas` | 15 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remoteareas_groups` | 48 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remoteareas_groups_log` | 21 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.remoteareas_log` | 23 | **Logs & API** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.report_customize_settings` | 10 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.routing_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.royalmail_docket_number` | 11 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.royalmail_sortcode` | 426 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.sales_call_log` | 0 | **Logs & API** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.sales_pot_comission` | 15 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_agent_mapping` | 21 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_collection_county` | 6 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_constant` | 28 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_constant_value` | 15 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_country_ttime` | 28 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_document` | 4 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_log` | 14 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.service_range_mapping` | 137 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.services` | 2 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.services_dpd` | 83 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.shopping_platform` | 16 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.sort_key_record` | 23 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.sorter_postcode_zone` | 119 | **Geographic Data** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.sp_tariff_log` | 21 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.status_reason` | 0 | **Other / Utils** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.tagnumber_range` | 8 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tariff_additional_charges` | 39 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tariff_details` | 8 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tariff_service_charges` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.tariff_user_mapping` | 0 | **Users & Auth** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.tariffs` | 14 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tariffs_account_mapping` | 144 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tariffs_details` | 14 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tariffs_log` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.tariffs_pricing` | 0 | **Finance** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.tariffs_pricing_rules` | 38 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tariffs_pricing_rules_details` | 23 | **Finance** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.themes` | 6 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tourline_routine` | 20 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tracking_data` | 38 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tracking_estimated_time` | 324 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.tracking_status_codes` | 8 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.ukmail_authentication` | 4 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.ukpostcodelatlng` | 0 | **Geographic Data** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.user` | 112 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_account_log` | 13 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_account_old` | 1 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_account_service_charges` | 25 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_audit` | 48 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_department` | 4 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_document` | 5 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_log` | 611 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_market_places_mapping` | 19 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_services_charges` | 19 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_services_charges_log` | 0 | **Services** | ⚠️ Empty | ⬜ Pending |
| `smarttrack_staging.user_services_routing` | 18 | **Services** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.user_shopping_platforms` | 12 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.userhasgroups` | 654 | **Users & Auth** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.vehicle` | 12 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.vehicle_driver` | 15 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.vehicle_parcel_mapping` | 316 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.warehouse` | 11 | **Warehouse** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.warehouse_processing_time` | 48 | **Warehouse** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.warehouse_warehouse_ttime` | 5 | **Warehouse** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.whistl_depo_details` | 9 | **Other / Utils** | ✅ Has Data | ⬜ Pending |
| `smarttrack_staging.yodel_hubs` | 8 | **Other / Utils** | ✅ Has Data | ⬜ Pending |


## Summary Statistics
- **Total Legacy Tables:** 938
- **Tables With Data:** 582
- **Empty Tables:** 356

## Module Breakdown (Estimated)
- **Geographic Data**: 18 tables
- **Other / Utils**: 405 tables
- **Logs & API**: 168 tables
- **Operations (Manifest/Bag)**: 30 tables
- **Services**: 74 tables
- **Carriers**: 45 tables
- **Consignments**: 57 tables
- **Finance**: 72 tables
- **Users & Auth**: 60 tables
- **Labels & Printing**: 3 tables
- **Warehouse**: 6 tables
