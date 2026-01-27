<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `address` (
  `id` int(10) NOT NULL,
  `phone_number` varchar(45) DEFAULT NULL,
  `company` varchar(45) DEFAULT NULL,
  `contact` varchar(45) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address_line_1` varchar(45) DEFAULT NULL,
  `address_line_2` varchar(45) DEFAULT NULL,
  `address_line_3` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `country` varchar(3) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `agent_data` (
  `id` int(11) UNSIGNED NOT NULL,
  `agent_code` varchar(45) DEFAULT NULL,
  `agent_name` varchar(45) DEFAULT NULL,
  `active` tinyint(2) DEFAULT NULL,
  `contact_name` varchar(45) DEFAULT NULL,
  `address_line_1` varchar(45) DEFAULT NULL,
  `address_line_2` varchar(45) DEFAULT NULL,
  `address_line_3` varchar(45) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `county` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `telephone` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `email` varchar(1000) DEFAULT NULL,
  `alternative_contact_1` varchar(45) DEFAULT NULL,
  `alternative1_telephone` varchar(45) DEFAULT NULL,
  `alternative1_mobile` varchar(45) DEFAULT NULL,
  `alternative1_fax` varchar(45) DEFAULT NULL,
  `alternative1_email` varchar(45) DEFAULT NULL,
  `alternative_contact_2` varchar(45) DEFAULT NULL,
  `alternative2_telephone` varchar(45) DEFAULT NULL,
  `alternative2_mobile` varchar(45) DEFAULT NULL,
  `alternative2_fax` varchar(45) DEFAULT NULL,
  `alternative2_email` varchar(45) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_deleted` bit(1) DEFAULT b'0',
  `logo` varchar(45) DEFAULT NULL,
  `agent_type` enum('carrier','dispatch','both') NOT NULL DEFAULT 'carrier'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `agent_document` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `agent_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `agent_restricted_postcode` (
  `id` bigint(20) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `postcode_city` varchar(100) DEFAULT NULL,
  `is_city` bit(1) DEFAULT b'0' COMMENT 'city = 1\npostcode = 0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `api_data` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `api_request` text DEFAULT NULL,
  `api_response` text DEFAULT NULL,
  `added_by` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `api_reason` varchar(45) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `auto_tracking` (
  `id` int(10) UNSIGNED NOT NULL,
  `next_number` int(10) DEFAULT NULL,
  `range_end` int(10) DEFAULT NULL,
  `increment_date` datetime DEFAULT NULL,
  `service_name` varchar(45) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `bagging` (
  `id` int(11) NOT NULL,
  `bagnumber` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `csv` varchar(200) DEFAULT NULL,
  `pdf` varchar(200) DEFAULT NULL,
  `manifestid` int(11) DEFAULT NULL,
  `account` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `manifest_pdf` varchar(200) DEFAULT NULL,
  `bag_status` tinyint(2) DEFAULT 0 COMMENT '0 for Open,1 for Hold,2 for Close',
  `date_updated` datetime DEFAULT NULL,
  `isdeleted` tinyint(2) DEFAULT 0,
  `service` varchar(200) DEFAULT NULL,
  `serviceid` int(11) DEFAULT NULL COMMENT 'Assigned service id to bag',
  `country` varchar(45) DEFAULT NULL COMMENT 'Assigned country id to bag',
  `country_iso_code` varchar(2) DEFAULT NULL,
  `bag_type` varchar(45) DEFAULT NULL COMMENT '0 for Mixed and 1 for normal',
  `actual_weight` decimal(10,2) DEFAULT NULL COMMENT 'after scaning Calculated weight',
  `length` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `pieces` int(11) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL COMMENT 'Customers assigned weight',
  `bag_label` varchar(255) DEFAULT NULL,
  `bag_source_country_id` int(11) DEFAULT NULL,
  `bag_source_warehouse_id` int(11) DEFAULT NULL,
  `bag_destination_country_id` int(11) DEFAULT NULL,
  `bag_destination_warehouse_id` int(11) DEFAULT NULL,
  `is_closed` bit(1) DEFAULT b'0',
  `closed_by` bigint(20) DEFAULT NULL,
  `closed_date` datetime DEFAULT NULL,
  `reopen_by` bigint(20) DEFAULT NULL,
  `reopen_date` datetime DEFAULT NULL,
  `bag_manifest` varchar(255) DEFAULT NULL,
  `bag_value` enum('hv','lv','mv') NOT NULL DEFAULT 'lv'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `bagging_manifest_mapping` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `manifest_id` int(11) NOT NULL,
  `manifest_entity_type` enum('p','b') NOT NULL DEFAULT 'p' COMMENT 'p for parcel and b for bagging'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `bagging_services_mapping` (
  `id` int(11) NOT NULL,
  `bag_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `bagnumbers` (
  `id` int(11) NOT NULL,
  `hawb` varchar(45) DEFAULT NULL,
  `consignment_id` varchar(45) DEFAULT NULL,
  `parcel_id` int(11) DEFAULT NULL,
  `tag_number` varchar(45) DEFAULT NULL,
  `bag_number` int(11) DEFAULT NULL,
  `service` varchar(45) DEFAULT NULL,
  `value` varchar(45) DEFAULT NULL,
  `weight` varchar(45) DEFAULT NULL,
  `number_pieces` varchar(45) DEFAULT NULL,
  `label_file` varchar(45) DEFAULT NULL,
  `manifest_file` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `date_created` varchar(45) DEFAULT NULL,
  `date_printed` varchar(45) DEFAULT NULL,
  `flightnumber` varchar(45) DEFAULT NULL,
  `flight_id` int(11) DEFAULT NULL,
  `mawb` int(11) DEFAULT NULL,
  `accountnumber` varchar(45) DEFAULT NULL,
  `destination_addr` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `dispatchdate` varchar(45) DEFAULT NULL,
  `mail_number` varchar(45) DEFAULT NULL,
  `last_bag` varchar(45) DEFAULT NULL,
  `flight_datetime` varchar(45) DEFAULT NULL,
  `bag_type` varchar(10) DEFAULT NULL,
  `is_track` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `bag_scan_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `box_info` (
  `id` int(11) NOT NULL,
  `box_number` varchar(50) DEFAULT NULL,
  `box_size` varchar(20) DEFAULT NULL,
  `box_weight` decimal(6,2) DEFAULT NULL,
  `tracking_numbers` text DEFAULT NULL,
  `manifest_number` varchar(25) DEFAULT NULL,
  `mawb_number` varchar(25) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `date_scanned` datetime DEFAULT NULL,
  `api_data` text DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `brazil_postcode` (
  `id` int(11) NOT NULL,
  `state` varchar(45) DEFAULT NULL,
  `locality` varchar(200) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `zone` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `brazil_state` (
  `id` int(11) NOT NULL,
  `state_code` varchar(45) DEFAULT NULL,
  `state_name` varchar(45) DEFAULT NULL,
  `city_code` varchar(45) DEFAULT NULL,
  `city_name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `bulletins` (
  `id` int(10) NOT NULL,
  `heading` varchar(300) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `cacesa_routine` (
  `id` int(11) NOT NULL,
  `type` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `agency` varchar(45) DEFAULT NULL,
  `route_description` varchar(45) DEFAULT NULL,
  `route_id` varchar(45) DEFAULT NULL,
  `courier` varchar(45) DEFAULT NULL,
  `routing` varchar(100) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier` (
  `id` int(11) NOT NULL,
  `carrier` varchar(45) DEFAULT NULL,
  `logo` varchar(45) DEFAULT NULL,
  `cut_off_time` varchar(45) DEFAULT NULL,
  `carrier_display_name` varchar(45) DEFAULT NULL,
  `status` int(1) DEFAULT NULL COMMENT '0 for deactive\n1 for  active\n2 for delete',
  `country_id` int(11) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `currency_code` varchar(3) DEFAULT 'GBP',
  `remotearea_check` enum('c','s') NOT NULL DEFAULT 'c',
  `zone_base` bit(1) DEFAULT b'0' COMMENT '1 for carrier and 0 for service',
  `zone_type` enum('country','postcode') DEFAULT 'country',
  `on_contract` bit(1) DEFAULT b'0',
  `is_gazetteer` tinyint(1) NOT NULL DEFAULT 0,
  `is_reconcile` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_agent` (
  `id` int(11) NOT NULL,
  `account_number` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `company` varchar(45) DEFAULT NULL,
  `address_line_1` varchar(45) DEFAULT NULL,
  `address_line_2` varchar(45) DEFAULT NULL,
  `address_line_3` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `country_iso_code` varchar(2) DEFAULT NULL,
  `api_username` varchar(45) DEFAULT NULL,
  `api_password` varchar(45) DEFAULT NULL,
  `ftp_host` varchar(45) DEFAULT NULL,
  `ftp_username` varchar(45) DEFAULT NULL,
  `ftp_password` varchar(45) DEFAULT NULL,
  `integration_type` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_data_file_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `carrier_id` bigint(20) UNSIGNED NOT NULL,
  `agent_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(50) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `run_number` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_document` (
  `id` int(11) NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_hubs` (
  `id` int(11) NOT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `hub` varchar(100) DEFAULT NULL,
  `routing_code` varchar(45) DEFAULT NULL,
  `company` varchar(45) DEFAULT NULL,
  `contact` varchar(45) DEFAULT NULL,
  `address_line_1` varchar(45) DEFAULT NULL,
  `address_line_2` varchar(45) DEFAULT NULL,
  `address_line_3` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `country_iso_code` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_service_customize_rules` (
  `id` int(11) NOT NULL,
  `serviceid` int(11) DEFAULT NULL,
  `agentid` int(11) DEFAULT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `from_weight` decimal(10,3) DEFAULT NULL,
  `to_weight` decimal(10,3) DEFAULT NULL,
  `status` bit(1) DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_service_default_rules` (
  `id` int(11) NOT NULL,
  `serviceid` int(11) DEFAULT NULL,
  `agentid` int(11) DEFAULT NULL,
  `from_weight` decimal(10,3) DEFAULT NULL,
  `to_weight` decimal(10,3) DEFAULT NULL,
  `is_default` bit(1) DEFAULT NULL,
  `agent_type` enum('outbound','dispatch') NOT NULL DEFAULT 'outbound'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_zones` (
  `id` int(11) UNSIGNED NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `service_id` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `sort_order` int(5) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_zones_countries` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) UNSIGNED NOT NULL,
  `carrier_zone_id` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carrier_zones_postcode` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `carrier_zone_id` int(11) UNSIGNED NOT NULL,
  `postcode` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `carton_pallet_number` (
  `id` int(11) NOT NULL,
  `type` varchar(3) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `start_number` varchar(45) DEFAULT NULL,
  `end_number` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ch_shipments` (
  `id` int(11) NOT NULL,
  `hawb` varchar(45) DEFAULT NULL,
  `reference` varchar(45) DEFAULT NULL,
  `awb` varchar(45) DEFAULT NULL,
  `account` varchar(45) DEFAULT NULL,
  `country_iso_code` varchar(3) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `product` varchar(45) DEFAULT NULL,
  `service` varchar(45) DEFAULT NULL,
  `bagnumber` varchar(45) DEFAULT NULL,
  `mawb` varchar(45) DEFAULT NULL,
  `charge_able_weight` decimal(10,3) DEFAULT NULL,
  `total_charge` decimal(10,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment` (
  `id` int(11) UNSIGNED NOT NULL,
  `agent_id` int(11) DEFAULT 51,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `customized_service_id` int(11) DEFAULT 0,
  `warehouse_user_id` int(11) DEFAULT NULL COMMENT 'User Id for those shipment which created by warehouse  perosn',
  `warehouse_id` int(11) DEFAULT NULL,
  `sales_pot_id` bigint(20) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `credit_id` int(11) DEFAULT NULL,
  `is_invoiced` int(1) DEFAULT 0,
  `invoice_type` enum('INV','MNI') DEFAULT NULL,
  `shipment_status` int(4) DEFAULT 0 COMMENT '4 digit code for shipment status which also belong',
  `shipment_type` enum('C','D','P','DO') DEFAULT 'D' COMMENT 'C for collection \nD for dispatched\nP product, DO dropoff',
  `awb` varchar(30) DEFAULT NULL,
  `consignment_status` varchar(20) DEFAULT NULL,
  `return_awb` varchar(30) DEFAULT NULL,
  `hawb` varchar(40) NOT NULL,
  `mawb` varchar(40) DEFAULT NULL,
  `service_name` varchar(50) DEFAULT NULL,
  `reference` varchar(20) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `date_label_created` int(11) DEFAULT NULL,
  `date_booked` int(11) DEFAULT NULL,
  `date_delivered` int(11) DEFAULT NULL,
  `is_customer_manifested` int(1) DEFAULT 0,
  `booked_file_id` varchar(50) NOT NULL DEFAULT '0',
  `company` varchar(100) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `address_line_1` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_polish_ci DEFAULT NULL,
  `address_line_2` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `address_line_3` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_polish_ci DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `postcode` varchar(15) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `telephone` varchar(17) DEFAULT NULL,
  `number_pieces` int(3) UNSIGNED DEFAULT NULL,
  `weight_type` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT 'PP',
  `weight` decimal(8,3) UNSIGNED DEFAULT NULL,
  `update_weight` decimal(8,3) DEFAULT NULL,
  `fake_weight` decimal(8,3) DEFAULT NULL COMMENT 'sending 20-30% decrease weight to the carrier(Hungary Post) as compared to the actual weight.',
  `charge_weight` decimal(8,3) DEFAULT NULL,
  `vol_weight` decimal(8,3) DEFAULT NULL COMMENT 'vol weight will updated by scanning system ',
  `vol_demonimator` int(11) DEFAULT NULL COMMENT 'Denominator will be updated from service table on consignment add',
  `hv_lv` enum('L','H','M') DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `value` decimal(10,2) UNSIGNED DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `sender_name` varchar(45) NOT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sender_checked` int(1) DEFAULT 0,
  `message` varchar(400) DEFAULT NULL,
  `sorter_image` varchar(200) DEFAULT NULL,
  `label_file` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `is_doc` int(1) DEFAULT 0,
  `email` varchar(45) DEFAULT NULL,
  `itemtype` varchar(60) DEFAULT NULL,
  `routing_code` varchar(3) DEFAULT NULL,
  `routing_code_eur` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `other_routing_code` varchar(250) DEFAULT NULL,
  `billing_hold` int(1) DEFAULT 0 COMMENT 'Account use this field for making billing hold as YES and NO',
  `send_courier_data` int(1) DEFAULT 0 COMMENT 'Set 1 when data send to all courier',
  `remote_charges` int(1) DEFAULT 0,
  `reinvoices` int(1) DEFAULT 0,
  `optimus_sorter` int(1) DEFAULT 0,
  `full_pallet` int(2) DEFAULT 0,
  `half_pallet` int(2) DEFAULT 0,
  `quarter_pallet` int(2) DEFAULT 0,
  `date_scanned` datetime DEFAULT NULL,
  `consignment_type` enum('return','outbound') NOT NULL DEFAULT 'outbound',
  `api_uuid` varchar(200) DEFAULT NULL,
  `sender_company` varchar(100) DEFAULT NULL,
  `sender_email` varchar(45) DEFAULT NULL,
  `sender_telephone` varchar(17) DEFAULT NULL,
  `sender_address_line_1` varchar(255) DEFAULT NULL,
  `sender_address_line_2` varchar(255) DEFAULT NULL,
  `sender_address_line_3` varchar(255) DEFAULT NULL,
  `sender_city` varchar(50) DEFAULT NULL,
  `sender_postcode` varchar(15) DEFAULT NULL,
  `sender_country_id` int(11) DEFAULT NULL,
  `sender_state` varchar(45) DEFAULT NULL,
  `collection_date` date DEFAULT NULL,
  `collection_start_time` varchar(5) DEFAULT NULL,
  `collection_end_time` varchar(5) DEFAULT NULL,
  `collection_confirmation_no` varchar(45) DEFAULT NULL,
  `created_from` enum('web','api','csv') DEFAULT 'web',
  `is_white_label` tinyint(4) NOT NULL DEFAULT 0,
  `is_dead_weight_chargable` tinyint(4) DEFAULT 0,
  `is_customer_billable` int(1) NOT NULL DEFAULT 0,
  `ioss_number` varchar(50) NOT NULL DEFAULT '0',
  `eori_number` varchar(50) NOT NULL DEFAULT '0',
  `vat_number` varchar(50) NOT NULL DEFAULT '0',
  `is_over_size_chargable` int(5) NOT NULL DEFAULT 0,
  `is_insured` int(1) NOT NULL DEFAULT 0,
  `destination_warehouse_id` int(5) DEFAULT 0,
  `consignment_seller` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_bagging_mapping` (
  `consignmentid` int(11) DEFAULT NULL,
  `bagid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_billing_hold` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `user_account_id_from` int(11) DEFAULT NULL,
  `user_account_id_to` int(11) DEFAULT NULL,
  `reason_for_hold` varchar(500) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_billing_hold_log` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `user_account_id_from` int(11) DEFAULT NULL,
  `user_account_id_to` int(11) DEFAULT NULL,
  `status` enum('hold','unhold') DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_charges` (
  `id` bigint(20) NOT NULL,
  `tariff_id` int(11) DEFAULT 0,
  `account_id` int(11) NOT NULL,
  `consignment_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `charge_type_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `cost_type` enum('customer','agent','purchase_invoice') NOT NULL DEFAULT 'customer',
  `cost` decimal(11,2) DEFAULT 0.00,
  `cost_currency` varchar(3) DEFAULT NULL,
  `cost_supplier_currency` decimal(11,2) DEFAULT NULL,
  `supplier_currency` varchar(3) DEFAULT NULL,
  `cost_company_currency` decimal(11,2) DEFAULT NULL,
  `company_currency` varchar(3) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `changes_reference` varchar(110) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_charges_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(40) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(10) NOT NULL DEFAULT 'A',
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Stores activity log.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_charges_types` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `charges_key` varchar(255) NOT NULL,
  `charge_type` enum('both','customer','agent') NOT NULL DEFAULT 'both',
  `apply_per_kg` bit(1) NOT NULL DEFAULT b'0',
  `is_extra_charge` bit(1) NOT NULL DEFAULT b'0',
  `is_vat` bit(1) NOT NULL DEFAULT b'0',
  `has_account_default_value` bit(1) NOT NULL DEFAULT b'0',
  `is_replace_charges` bit(1) DEFAULT b'0',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 for inactive 1 for active 2 for deleted',
  `is_delete` bit(1) DEFAULT b'0',
  `added_by` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_collection` (
  `id` int(11) UNSIGNED NOT NULL,
  `consignment_id` int(20) UNSIGNED NOT NULL,
  `sender_company` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_polish_ci DEFAULT NULL,
  `sender_contact` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_polish_ci DEFAULT NULL,
  `sender_email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_polish_ci DEFAULT NULL,
  `sender_address_line_1` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_polish_ci DEFAULT NULL,
  `sender_address_line_2` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `sender_address_line_3` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_polish_ci DEFAULT NULL,
  `sender_city` varchar(50) DEFAULT NULL,
  `sender_country_iso_code` char(3) DEFAULT NULL,
  `sender_postcode` varchar(10) DEFAULT NULL,
  `sender_telephone` varchar(20) DEFAULT NULL,
  `date_collection` int(11) DEFAULT NULL,
  `earliest_latest_time` int(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_details` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `custom_export_number` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_dropoff_mapping` (
  `id` bigint(20) NOT NULL,
  `dropoff_consignment_id` bigint(20) NOT NULL,
  `dispatch_consignment_id` bigint(20) NOT NULL,
  `dropoff_consignment_tracking` text NOT NULL,
  `dispatch_consignment_tracking` text NOT NULL,
  `parcel_tracking` text DEFAULT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_hold` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `comments` varchar(500) DEFAULT NULL,
  `tracking_number` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `action` varchar(45) DEFAULT NULL,
  `reason_tag` varchar(45) DEFAULT NULL,
  `weight` varchar(45) DEFAULT NULL,
  `width` varchar(45) DEFAULT NULL,
  `height` varchar(45) DEFAULT NULL,
  `length` varchar(45) DEFAULT NULL,
  `volume` varchar(45) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `account` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_hold_log` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `user_account_id_from` int(11) DEFAULT NULL,
  `user_account_id_to` int(11) DEFAULT NULL,
  `status` enum('hold','unhold') DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_hscode` (
  `id` int(11) UNSIGNED NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `hscode` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(40) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(10) NOT NULL DEFAULT 'A',
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Stores activity log.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_pod` (
  `id` int(11) NOT NULL,
  `consignmentid` int(11) DEFAULT NULL,
  `signature` varchar(45) DEFAULT NULL,
  `pod_date` varchar(45) DEFAULT NULL,
  `pod_image` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_relabel` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `old_tracking_no` varchar(45) DEFAULT NULL,
  `new_tracking_no` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `old_consignment_data` text DEFAULT NULL,
  `old_parcel_tracking_no` text DEFAULT NULL,
  `old_new_tracking_mapping` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `consignment_status_log` (
  `id` bigint(20) NOT NULL,
  `parcel_id` bigint(20) NOT NULL,
  `old_status` varchar(100) NOT NULL,
  `new_status` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `correos_brazil_datafile` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `cost_tariffs` (
  `id` int(11) UNSIGNED NOT NULL,
  `courier_service_id` int(11) UNSIGNED NOT NULL,
  `collection_rateband_id` int(11) UNSIGNED NOT NULL,
  `destination_rateband_id` int(11) UNSIGNED NOT NULL,
  `collection_postcode_group_id` int(11) NOT NULL,
  `destination_postcode_group_id` int(11) DEFAULT NULL,
  `weight_from` decimal(7,2) NOT NULL,
  `weight_to` decimal(7,2) NOT NULL,
  `tariff_cost` decimal(9,2) NOT NULL,
  `unit_cost` decimal(5,2) NOT NULL,
  `unit_size` decimal(5,2) NOT NULL DEFAULT 0.00,
  `active` tinyint(4) DEFAULT 1,
  `deletedq` char(1) DEFAULT 'N',
  `added_on` datetime DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  `changed_on` datetime DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `tariff_name` varchar(255) NOT NULL,
  `formula` varchar(100) DEFAULT 'Q * ( ITMCHR + REG ) + W * CHRG' COMMENT 'Formulla for calculation '
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `countries_link_ratebands` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) UNSIGNED NOT NULL,
  `rateband_id` int(11) UNSIGNED NOT NULL,
  `orderq` int(5) NOT NULL DEFAULT 0,
  `active` tinyint(4) DEFAULT 1,
  `deletedq` char(1) DEFAULT 'N',
  `added_on` datetime DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  `changed_on` datetime DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL,
  `iso` char(3) NOT NULL,
  `name` varchar(80) DEFAULT NULL,
  `region` varchar(45) DEFAULT NULL,
  `postcode_required` enum('YES','NO') DEFAULT 'YES',
  `type` varchar(1) DEFAULT NULL,
  `region_collection` varchar(45) DEFAULT NULL,
  `numcode` int(3) UNSIGNED ZEROFILL DEFAULT NULL COMMENT 'This is 3 digit country iso code',
  `allow_express` char(1) DEFAULT NULL,
  `allow_classic` char(1) DEFAULT NULL,
  `eu_country` char(1) DEFAULT NULL,
  `shipping_advice` varchar(255) DEFAULT NULL,
  `is_vatable` enum('YES','NO') DEFAULT 'NO',
  `vat_rate` decimal(2,2) DEFAULT NULL COMMENT 'VAT is always in percentage',
  `printable_name` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `export_flag` int(11) DEFAULT NULL,
  `timezone_difference` int(11) DEFAULT NULL,
  `has_postcodeq` char(1) NOT NULL,
  `has_subzonesq` char(1) NOT NULL,
  `orderq` int(5) NOT NULL,
  `active` tinyint(2) NOT NULL DEFAULT 1,
  `deletedq` char(1) NOT NULL DEFAULT 'N',
  `added_on` datetime NOT NULL,
  `added_by` varchar(100) NOT NULL,
  `changed_on` datetime NOT NULL,
  `changed_by` varchar(100) NOT NULL,
  `vat_charged_flag` int(11) DEFAULT NULL,
  `customs_flag` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `country_image` varchar(75) DEFAULT NULL,
  `metakeywords` varchar(300) DEFAULT NULL,
  `metadescription` varchar(300) DEFAULT NULL,
  `pagetitle` varchar(100) DEFAULT NULL,
  `countrybanner` varchar(75) DEFAULT NULL,
  `opcode` varchar(5) DEFAULT NULL,
  `iso_three` varchar(3) DEFAULT NULL,
  `german_name` varchar(80) DEFAULT NULL,
  `manifest_template` varchar(80) DEFAULT NULL,
  `bag_template` varchar(80) DEFAULT NULL,
  `bag_weight_limit` int(11) DEFAULT NULL,
  `bag_low_value` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `cpost_manifest` (
  `id` int(11) NOT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  `manifest_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `credit_note` (
  `id` int(11) NOT NULL,
  `credit_note_number` varchar(120) DEFAULT NULL,
  `user_account_id` int(11) NOT NULL,
  `invoice_type` enum('INV','MNI') NOT NULL,
  `invoice_number` varchar(45) DEFAULT NULL,
  `credit_note_type` enum('PARTIAL','FULL','OTHER') DEFAULT NULL,
  `hawb` text DEFAULT NULL,
  `credit_note_heading` text DEFAULT NULL,
  `credit_date` datetime DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `vat_amount` decimal(10,2) DEFAULT NULL,
  `credit_total` decimal(10,2) DEFAULT NULL,
  `credit_note_by` int(11) DEFAULT NULL,
  `pdf` varchar(200) DEFAULT NULL,
  `is_email` bit(1) DEFAULT b'0',
  `is_read` bit(1) DEFAULT b'0',
  `added_by` int(11) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `currency_id` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `credit_note_details` (
  `id` int(11) NOT NULL,
  `credit_note_id` int(11) NOT NULL,
  `hawb` varchar(50) DEFAULT NULL,
  `date_booked` datetime DEFAULT NULL,
  `reference` varchar(45) DEFAULT NULL,
  `invoice_amount` decimal(10,2) DEFAULT 0.00,
  `chargeable_amount` decimal(10,2) DEFAULT 0.00,
  `credit_amount` decimal(10,2) DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `is_vatable` enum('YES','NO') DEFAULT 'NO',
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL,
  `date_updated` datetime NOT NULL,
  `vat_amount` decimal(10,2) DEFAULT 0.00,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `csv_import_template` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_account_id` bigint(20) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template` text NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `update_by` bigint(20) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `csv_tracking_template` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_account_id` bigint(20) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template` text NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `update_by` bigint(20) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `service_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `cs_log` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `internal_message` text DEFAULT NULL,
  `customer_message` text DEFAULT NULL,
  `cust_mail` varchar(45) DEFAULT NULL,
  `agent_mail` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `reminder` varchar(45) DEFAULT NULL,
  `reminder_expiry` datetime DEFAULT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `cs_notes` (
  `id` int(10) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ctt_datafile_id` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `currency` (
  `id` int(11) NOT NULL,
  `currencyname` varchar(50) NOT NULL,
  `leftsymbol` varchar(10) NOT NULL,
  `rightsymbol` varchar(10) NOT NULL,
  `isdefault` tinyint(1) NOT NULL,
  `currencyexchangerate` decimal(20,6) NOT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT 0,
  `clientdisplay` tinyint(1) NOT NULL DEFAULT 0,
  `currencyid` varchar(45) DEFAULT 'null'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `currency_temp` (
  `country` varchar(100) DEFAULT NULL,
  `currency` varchar(100) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `symbol` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `customer_account` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_account` varchar(30) DEFAULT NULL,
  `active_flag` bit(1) NOT NULL DEFAULT b'0',
  `company` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `return_address` varchar(255) DEFAULT NULL,
  `sms_dpd` bit(1) DEFAULT b'0',
  `user_service_type` enum('CHOICE','ROUTING','BOTH') DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `instant_label` bit(1) DEFAULT b'0',
  `country` varchar(3) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `tracking_api_access` bit(1) DEFAULT b'0',
  `import_data_csv` bit(1) DEFAULT b'0',
  `proforma` bit(1) DEFAULT b'0',
  `add_tracking` bit(1) DEFAULT b'0',
  `collection` bit(1) DEFAULT b'0',
  `default_description` varchar(255) DEFAULT NULL,
  `default_notes` varchar(255) DEFAULT NULL,
  `default_weight` decimal(5,2) DEFAULT NULL,
  `payment_term` text DEFAULT NULL,
  `query_term` text DEFAULT NULL,
  `vat_number` varchar(40) DEFAULT NULL,
  `billing_currency` varchar(3) DEFAULT 'GBP',
  `vat_chargable` bit(1) DEFAULT b'0',
  `vat_value` decimal(10,2) DEFAULT NULL COMMENT 'this is percentage field',
  `allow_remote_area` bit(1) DEFAULT b'0',
  `telephone` varchar(20) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `date_dispatch` bit(1) DEFAULT b'0',
  `is_product` varchar(2) DEFAULT '0',
  `profile_image` varchar(255) DEFAULT NULL,
  `send_courier_data` bit(1) DEFAULT b'0' COMMENT 'Send data on label Creation',
  `archive_server` bit(1) DEFAULT b'0',
  `credit_check` bit(1) DEFAULT b'0',
  `tariff_agreed` bit(1) DEFAULT b'0',
  `sales_person` varchar(45) DEFAULT NULL,
  `scan_document` text DEFAULT NULL,
  `data_entry` bit(1) DEFAULT b'0',
  `bank_account_title` varchar(45) DEFAULT NULL,
  `bank_sortcode` varchar(10) DEFAULT NULL,
  `bank_account_number` varchar(20) DEFAULT NULL,
  `bank_branch_address` varchar(255) DEFAULT NULL,
  `trade_name_i` varchar(50) DEFAULT NULL,
  `trade_address_i` varchar(255) DEFAULT NULL,
  `trade_email_i` varchar(255) DEFAULT NULL,
  `trade_phone_i` varchar(50) DEFAULT NULL,
  `trade_name_ii` varchar(50) DEFAULT NULL,
  `trade_address_ii` varchar(255) DEFAULT NULL,
  `trade_email_ii` varchar(255) DEFAULT NULL,
  `trade_phone_ii` varchar(50) DEFAULT NULL,
  `reg_number` varchar(20) DEFAULT NULL,
  `reg_address` varchar(255) DEFAULT NULL,
  `reg_postcode` varchar(10) DEFAULT NULL,
  `reg_country` varchar(5) DEFAULT NULL,
  `sale_agent` varchar(10) DEFAULT NULL,
  `sale_date` datetime DEFAULT NULL,
  `fuel_charges` decimal(10,2) DEFAULT 0.00,
  `warehouse_id` int(11) DEFAULT NULL,
  `user_signature` text DEFAULT NULL,
  `is_fuelcharges_include` bit(1) DEFAULT b'0',
  `is_prepaid` bit(1) DEFAULT b'0',
  `return_label` bit(1) DEFAULT b'0',
  `finalmile_over_label` bit(1) DEFAULT b'0',
  `request_manifest_collection` bit(1) DEFAULT b'0',
  `create_pre_alert` bit(1) DEFAULT b'0',
  `is_employee` bit(1) NOT NULL DEFAULT b'0',
  `invoice_bank_details_id` int(11) DEFAULT 0,
  `check_list_account_form` bit(1) DEFAULT b'0',
  `check_list_credit_check` bit(1) DEFAULT b'0',
  `check_list_t_cs` bit(1) DEFAULT b'0',
  `check_list_tariff_agreed` bit(1) DEFAULT b'0',
  `check_list_sales_pot` bit(1) DEFAULT b'0',
  `sales_pot_time_period` int(5) DEFAULT 0,
  `sales_pot_percentage` decimal(5,2) DEFAULT 0.00,
  `last_login_date` timestamp NULL DEFAULT NULL,
  `invalid_login_count` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_updated` timestamp NULL DEFAULT NULL,
  `lock_time` timestamp NULL DEFAULT NULL,
  `opearation_manifest` bit(1) DEFAULT b'0',
  `own_tariff` bit(1) DEFAULT b'0',
  `user_warehouse` enum('NON','BIRMINGHAM','HAYES') DEFAULT 'NON',
  `api_key` varchar(100) DEFAULT NULL,
  `api_secert` varchar(100) DEFAULT NULL,
  `api_date` datetime DEFAULT NULL,
  `bagging` bit(1) DEFAULT b'0',
  `retail_customer` bit(1) DEFAULT b'0',
  `show_price` bit(1) DEFAULT b'0',
  `sales_rate` decimal(10,2) DEFAULT NULL,
  `collection_add_line_1` varchar(50) DEFAULT NULL,
  `collection_add_line_2` varchar(50) DEFAULT NULL,
  `collection_add_line_3` varchar(50) DEFAULT NULL,
  `collection_city` varchar(45) DEFAULT NULL,
  `collection_postcode` varchar(45) DEFAULT NULL,
  `collection_country` varchar(45) DEFAULT NULL,
  `theme_id` int(5) DEFAULT NULL,
  `user_code` int(11) DEFAULT NULL,
  `website_link` varchar(200) DEFAULT NULL,
  `allow_return_email` bit(1) DEFAULT b'0',
  `default_lang` varchar(10) DEFAULT 'en-GB',
  `credit_limit` decimal(10,2) DEFAULT 0.00,
  `invoice_period` enum('daily','weekly','bi-monthly','monthly') DEFAULT 'daily',
  `label_price` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `account_code` varchar(4) DEFAULT NULL,
  `paypal_email` varchar(70) DEFAULT NULL,
  `paypal_currency` varchar(20) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `paypal_client_secret` varchar(255) DEFAULT NULL,
  `alternative_email` varchar(500) DEFAULT NULL,
  `billing_email` varchar(500) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `allow_oversize` tinyint(4) NOT NULL DEFAULT 0,
  `allow_overweight` tinyint(4) NOT NULL DEFAULT 0,
  `paypal_client_id` varchar(255) DEFAULT NULL,
  `invoice_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `send_tracking_data` bit(1) DEFAULT NULL,
  `billing_contact` varchar(50) NOT NULL,
  `ftp_shipment_upload` int(2) NOT NULL DEFAULT 0,
  `balance_alert_percentage` int(2) NOT NULL DEFAULT 0,
  `commission_break_event_account_amount` int(2) NOT NULL DEFAULT 0,
  `tracking_order_prefix` varchar(50) NOT NULL,
  `return_shipment_allow` int(2) NOT NULL DEFAULT 0,
  `account_balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `customized_services_routing` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) NOT NULL,
  `from_weight` decimal(10,2) DEFAULT NULL,
  `to_weight` decimal(10,2) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `customize_service_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `customized_services_routing_log` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `logdate` datetime DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `log_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `customized_user_services_routing` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `routing_added_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `czint_datafile_id` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `file_name_id` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `cz_datafile_id` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `file_name_id` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `department` (
  `id` int(5) NOT NULL,
  `title` varchar(100) NOT NULL,
  `department_code` varchar(5) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `department_head` bigint(20) NOT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `addedby` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updatedby` bigint(20) NOT NULL,
  `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `deutschepostdhl_cargo_code` (
  `id` int(11) NOT NULL,
  `start_postcode` int(10) DEFAULT NULL,
  `end_postcode` int(10) DEFAULT NULL,
  `cargo_code` int(3) DEFAULT NULL,
  `municipality_name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `deutschepost_dhl_streetcode` (
  `id` int(11) NOT NULL,
  `street` varchar(45) DEFAULT NULL,
  `zipcode` varchar(45) DEFAULT NULL,
  `street_code` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `document_type` (
  `id` int(11) NOT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `document_type` enum('company_contract','service_contract','agent_contract','service_agent') DEFAULT NULL,
  `is_active` enum('0','1') DEFAULT '1',
  `added_by` bigint(20) DEFAULT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_delete` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `domestic` (
  `id` int(11) NOT NULL,
  `postcode_sector` varchar(45) DEFAULT NULL,
  `dpd_depot` varchar(45) DEFAULT NULL,
  `dpd_services_group` varchar(45) DEFAULT NULL,
  `dpd_offshore_zone` varchar(45) DEFAULT NULL,
  `timeslots_code` varchar(45) DEFAULT NULL,
  `cluster` varchar(45) DEFAULT NULL,
  `ilk_depot` varchar(45) DEFAULT NULL,
  `ilk_services_group` varchar(45) DEFAULT NULL,
  `ilk_offshore_zone` varchar(45) DEFAULT NULL,
  `ilk_alternate_service` varchar(45) DEFAULT NULL,
  `new_postcode` varchar(45) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `domestic_day_file` (
  `id` int(5) UNSIGNED ZEROFILL NOT NULL,
  `file_name` varchar(50) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `dpdgroups` (
  `id` int(11) NOT NULL,
  `lookup_code` varchar(15) DEFAULT NULL,
  `list_of_available_services` varchar(100) DEFAULT NULL,
  `Business` varchar(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `dpd_datafile_id` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `dropoff_user_location` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `companyname` varchar(100) DEFAULT NULL,
  `address_line_1` varchar(50) DEFAULT NULL,
  `address_line_2` varchar(50) DEFAULT NULL,
  `address_line_3` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `telephone` varchar(17) DEFAULT NULL,
  `mon` varchar(20) DEFAULT NULL,
  `tue` varchar(20) DEFAULT NULL,
  `wed` varchar(20) DEFAULT NULL,
  `thu` varchar(20) DEFAULT NULL,
  `fri` varchar(20) DEFAULT NULL,
  `sat` varchar(20) DEFAULT NULL,
  `sun` varchar(20) DEFAULT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `lng` varchar(20) DEFAULT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `dx_routing` (
  `id` int(11) NOT NULL,
  `district` varchar(45) DEFAULT NULL,
  `sector` varchar(45) DEFAULT NULL,
  `depot` varchar(45) DEFAULT NULL,
  `depotid` varchar(45) DEFAULT NULL,
  `region_id` varchar(45) DEFAULT NULL,
  `delivery_method` varchar(45) DEFAULT NULL,
  `delivery_method_id` varchar(45) DEFAULT NULL,
  `delivery_method_description` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `emailtemplate` (
  `emailtemplateid` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `shortkey` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `content` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `isactive` tinyint(1) DEFAULT NULL,
  `createdon` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT NULL,
  `type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `pagetitle` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `metatitle` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `metadescription` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `metakeywords` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `sorder` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `estimate_delivery_timing` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `from_rateband` int(11) DEFAULT NULL,
  `to_rateband` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `created_by` varchar(45) DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'INACTIVE',
  `delivery_timing` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `euro_day_file` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(50) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `fftin_file` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(35) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `label_link` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `flight_info` (
  `id` int(11) NOT NULL,
  `flight_number` varchar(45) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `destination_country_id` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `current_status` enum('in_tranist','arrived_lhr','clearance_in_process','collection_in_process') DEFAULT NULL,
  `status` enum('not_assigned','assigned','in_warehouse') DEFAULT NULL,
  `address_line_1` varchar(45) DEFAULT NULL,
  `address_line_2` varchar(100) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `signature` varchar(45) DEFAULT NULL,
  `carrier` varchar(45) DEFAULT NULL,
  `carriage_value` decimal(18,2) DEFAULT NULL,
  `custom_value` decimal(18,2) DEFAULT NULL,
  `insurance_amount` decimal(18,2) DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `connecting_flight_number` varchar(45) DEFAULT NULL,
  `weight_type` varchar(2) DEFAULT NULL,
  `rate_charge` varchar(45) DEFAULT NULL,
  `iata_code` varchar(45) DEFAULT NULL,
  `departure_airport` varchar(45) DEFAULT NULL,
  `phone_number` varchar(45) DEFAULT NULL,
  `shipper_co` varchar(100) DEFAULT NULL,
  `consignee_co` varchar(100) DEFAULT NULL,
  `arrival_airport` varchar(45) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `shippers_name` varchar(45) DEFAULT NULL,
  `shippers_addressline1` varchar(100) DEFAULT NULL,
  `shippers_addressline2` varchar(100) DEFAULT NULL,
  `accounting_reference` varchar(100) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `rate_change` varchar(50) DEFAULT NULL,
  `low_value_manifest` varchar(255) DEFAULT NULL,
  `high_value_manifest` varchar(255) DEFAULT NULL,
  `invoice` varchar(255) DEFAULT NULL,
  `files_hv` varchar(255) DEFAULT NULL,
  `hscodes` varchar(5000) DEFAULT NULL,
  `etd` varchar(20) DEFAULT NULL,
  `eta` varchar(20) DEFAULT NULL,
  `shed` varchar(45) DEFAULT NULL,
  `files_lv` varchar(255) DEFAULT NULL,
  `cleared` varchar(45) DEFAULT NULL,
  `comments` varchar(45) DEFAULT NULL,
  `weight` varchar(45) DEFAULT NULL,
  `pieces` varchar(45) DEFAULT NULL,
  `created_by` bigint(20) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `is_closed` tinyint(1) DEFAULT 0,
  `account_number` varchar(50) DEFAULT NULL,
  `airway_bill` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `currancy` varchar(50) DEFAULT NULL,
  `files` varchar(255) DEFAULT NULL,
  `destination_company` varchar(255) DEFAULT NULL,
  `destination_phone_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `flight_mapping` (
  `id` int(11) NOT NULL,
  `flight_info_id` int(11) DEFAULT NULL,
  `flight_number` varchar(45) DEFAULT NULL,
  `mawb` varchar(45) DEFAULT NULL,
  `mawb_id` int(11) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `forget_password_request` (
  `id` bigint(20) NOT NULL,
  `user_name` varchar(45) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(45) DEFAULT NULL,
  `token` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_expire` datetime DEFAULT NULL,
  `is_expire` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `grouphaspermissions` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(150) DEFAULT NULL,
  `group_slug` varchar(255) DEFAULT NULL,
  `group_desc` varchar(255) DEFAULT NULL,
  `group_type` enum('admin','corporate','client') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `groups_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `hawb_log` (
  `id` int(11) NOT NULL,
  `hawb` varchar(45) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `helpdesk_ticket` (
  `id` int(11) NOT NULL,
  `ticket_code` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL DEFAULT 0,
  `priority` enum('low','medium','high','urgent','critical') NOT NULL DEFAULT 'low',
  `subject` varchar(255) NOT NULL,
  `status` enum('open','in_progress','cancel','fixed','close') NOT NULL DEFAULT 'open',
  `addedby` int(11) NOT NULL DEFAULT 0,
  `added_date` datetime NOT NULL,
  `updatedby` int(11) DEFAULT 0,
  `updated_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `helpdesk_ticket_message` (
  `id` bigint(20) NOT NULL,
  `ticketid` bigint(20) NOT NULL DEFAULT 0,
  `message` varchar(255) NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `addedby` int(11) NOT NULL DEFAULT 0,
  `added_date` datetime NOT NULL,
  `updatedby` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `hermes_datafile_id` (
  `id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `hermes_postcode_record` (
  `id` int(11) NOT NULL,
  `fullpostcode` varchar(8) NOT NULL,
  `pos_pcd_postcode_excluded_indicator` char(1) NOT NULL DEFAULT 'N',
  `sort_level_key` varchar(8) NOT NULL,
  `next_day_service` char(1) NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `imcp` (
  `id` int(11) NOT NULL,
  `opcode` varchar(5) DEFAULT NULL,
  `imcpcode` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `import_csv_consignment_temp` (
  `id` bigint(20) NOT NULL,
  `date_added` date DEFAULT NULL,
  `shipper_country_iso` varchar(50) DEFAULT NULL,
  `receiver_country_iso` varchar(50) DEFAULT NULL,
  `service_code` varchar(50) DEFAULT NULL,
  `order_reference` varchar(50) DEFAULT NULL,
  `shipper_company` varchar(50) DEFAULT NULL,
  `shipper_contact` varchar(50) DEFAULT NULL,
  `shipper_email` varchar(50) DEFAULT NULL,
  `shipper_telephone` varchar(50) DEFAULT NULL,
  `shipper_address_line_1` varchar(50) DEFAULT NULL,
  `shipper_address_line_2` varchar(50) DEFAULT NULL,
  `shipper_address_line_3` varchar(50) DEFAULT NULL,
  `shipper_city` varchar(50) DEFAULT NULL,
  `shipper_state` varchar(50) DEFAULT NULL,
  `shipper_postcode` varchar(50) DEFAULT NULL,
  `receiver_company` varchar(50) DEFAULT NULL,
  `receiver_contact` varchar(50) DEFAULT NULL,
  `receiver_email` varchar(50) DEFAULT NULL,
  `receiver_telephone` varchar(50) DEFAULT NULL,
  `receiver_address_line_1` varchar(50) DEFAULT NULL,
  `receiver_address_line_2` varchar(50) DEFAULT NULL,
  `receiver_address_line_3` varchar(50) DEFAULT NULL,
  `receiver_city` varchar(50) DEFAULT NULL,
  `receiver_state` varchar(50) DEFAULT NULL,
  `receiver_postcode` varchar(50) DEFAULT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `items_value` decimal(10,2) DEFAULT NULL,
  `items_currency` varchar(5) DEFAULT NULL,
  `item_type` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `bag_number` varchar(50) DEFAULT NULL,
  `tracking_number` varchar(20) DEFAULT NULL,
  `mawb_number` varchar(50) DEFAULT NULL,
  `flight_number` varchar(50) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '0',
  `is_complete` enum('0','1') DEFAULT '0',
  `batch_number` varchar(50) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `weight` text DEFAULT NULL,
  `length` text DEFAULT NULL,
  `height` text DEFAULT NULL,
  `width` text DEFAULT NULL,
  `itemvalue` text DEFAULT NULL,
  `parcel_item_desc` varchar(255) DEFAULT NULL,
  `parcel_item_sku` varchar(255) DEFAULT NULL,
  `parcel_item_url` varchar(255) DEFAULT NULL,
  `parcel_item_quantity` int(5) DEFAULT NULL,
  `parcel_item_value` decimal(10,2) DEFAULT NULL,
  `parcel_item_weight` decimal(10,2) DEFAULT NULL,
  `parcel_item_hs_code` varchar(50) DEFAULT NULL,
  `parcel_item_manufacture_country` varchar(50) DEFAULT NULL,
  `eori_number` varchar(50) DEFAULT NULL,
  `vat_number` varchar(50) DEFAULT NULL,
  `ioss_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `import_csv_tmp` (
  `id` bigint(20) NOT NULL,
  `account` varchar(255) DEFAULT NULL,
  `hawb` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `service_code` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `date_submitted` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `address_line3` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `post_code` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `number_of_pieces` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `routing_non_routing` varchar(255) DEFAULT NULL,
  `full_pallet` varchar(255) DEFAULT NULL,
  `half_pallet` varchar(255) DEFAULT NULL,
  `quarter_pallet` varchar(255) DEFAULT NULL,
  `all_weight` varchar(255) DEFAULT NULL,
  `width` varchar(255) DEFAULT NULL,
  `heigh` varchar(255) DEFAULT NULL,
  `length` varchar(255) DEFAULT NULL,
  `item_type` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `flight_number` varchar(255) DEFAULT NULL,
  `bag_number` varchar(255) DEFAULT NULL,
  `mawb` varchar(255) DEFAULT NULL,
  `is_complete` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  `batch_number` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `international` (
  `id` int(11) NOT NULL,
  `iata_country_code` char(2) NOT NULL,
  `zipcode_from` varchar(10) DEFAULT NULL,
  `zipcode_to` varchar(10) DEFAULT NULL,
  `air_express_depot` varchar(10) DEFAULT NULL,
  `air_express_osort` varchar(10) DEFAULT NULL,
  `air_express_dsort` varchar(10) DEFAULT NULL,
  `dpd_classic_deport` varchar(10) DEFAULT NULL,
  `dpd_classic_osort` varchar(10) DEFAULT NULL,
  `dpd_classic_dsort` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `fuel_charges` decimal(10,2) DEFAULT NULL,
  `vatable_amount` decimal(10,2) DEFAULT NULL,
  `vat` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `exchange_rate` decimal(10,2) DEFAULT NULL,
  `credit_type` enum('PARTIAL','FULL') DEFAULT 'PARTIAL',
  `credit_amount` decimal(10,2) DEFAULT 0.00,
  `invoice_date` datetime DEFAULT NULL,
  `summary_pdf` varchar(200) DEFAULT NULL,
  `pdf` varchar(200) DEFAULT NULL,
  `csv` varchar(200) DEFAULT NULL,
  `invoice_by` int(11) DEFAULT NULL,
  `invoice_type` enum('INV','MNI') DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_email` bit(1) DEFAULT b'0',
  `date_deleted` timestamp NULL DEFAULT NULL,
  `is_paid` bit(1) NOT NULL DEFAULT b'0',
  `is_cancel` bit(1) NOT NULL DEFAULT b'0',
  `is_read` bit(1) DEFAULT b'0',
  `paid_date` datetime DEFAULT NULL,
  `salepot_id` int(11) DEFAULT NULL,
  `added_by` int(11) NOT NULL DEFAULT 58,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `invoice_heading` text DEFAULT NULL,
  `attached_files` text DEFAULT NULL,
  `invoice_reference` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoices_manual` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(45) DEFAULT NULL,
  `account_number` varchar(15) DEFAULT NULL,
  `invoice_heading` varchar(255) DEFAULT NULL,
  `invoice_amount` varchar(20) DEFAULT NULL,
  `invoice_weight` varchar(20) DEFAULT NULL,
  `vat_amount` varchar(10) DEFAULT NULL,
  `invoice_total_amount` varchar(20) DEFAULT NULL,
  `currency` varchar(15) DEFAULT NULL,
  `exchange_rate` varchar(15) DEFAULT NULL,
  `invoice_status` varchar(50) DEFAULT NULL,
  `added_by` varchar(25) DEFAULT NULL,
  `invoice_file` varchar(500) DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `is_deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `invoice_date` timestamp NULL DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_deleted` timestamp NULL DEFAULT NULL,
  `is_email` enum('YES','NO') DEFAULT 'NO',
  `is_paid` enum('YES','NO') DEFAULT 'NO',
  `paid_date` timestamp NULL DEFAULT NULL,
  `salepot_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoices_manual_details` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(45) DEFAULT NULL,
  `hawb` varchar(15) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `date_booked` datetime DEFAULT NULL,
  `reference` varchar(20) DEFAULT NULL,
  `weight` varchar(20) DEFAULT NULL,
  `amount` varchar(10) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `vat_amount` decimal(10,0) DEFAULT NULL,
  `is_vat` enum('YES','NO') DEFAULT 'NO',
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(11) DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoices_number_range` (
  `id` int(11) UNSIGNED NOT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT 0,
  `range_start` bigint(20) UNSIGNED NOT NULL,
  `range_end` bigint(20) UNSIGNED NOT NULL,
  `next_number` bigint(20) UNSIGNED NOT NULL,
  `increment_date` datetime DEFAULT NULL,
  `user_account_id` int(11) NOT NULL,
  `prefix` varchar(10) DEFAULT NULL,
  `sufix` varchar(10) DEFAULT NULL,
  `range_type` enum('AUTO','MANUAL','CREDIT') DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL,
  `addedby` int(11) DEFAULT NULL,
  `updatedby` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoice_bank_details` (
  `id` int(11) NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `account_title` varchar(45) DEFAULT NULL,
  `account_sortcode` varchar(10) DEFAULT NULL,
  `account_number` int(12) DEFAULT NULL,
  `account_iban` varchar(45) DEFAULT NULL,
  `bank_name` varchar(45) DEFAULT NULL,
  `bank_branch` varchar(45) DEFAULT NULL,
  `bank_address` varchar(100) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoice_detail` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `charges_detail` text DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `vat` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `added_by` varchar(15) NOT NULL DEFAULT 'AUTOMATED_PRICE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoice_detail_backup` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `hawb` varchar(25) DEFAULT NULL,
  `basic_charges` decimal(10,2) DEFAULT NULL,
  `fuel_charges` decimal(10,2) DEFAULT NULL,
  `additional_charges` decimal(10,2) DEFAULT NULL,
  `remote_area_charge` decimal(10,2) DEFAULT NULL,
  `on_farword_charges` decimal(10,2) DEFAULT NULL,
  `ndx` decimal(10,2) DEFAULT NULL,
  `ddp` decimal(10,2) DEFAULT NULL,
  `extra` decimal(10,2) DEFAULT NULL,
  `hv` decimal(11,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `agent_basic_charges` decimal(10,2) DEFAULT NULL,
  `agent_fuel_charges` decimal(10,2) DEFAULT NULL,
  `agent_additional_charges` decimal(10,2) DEFAULT NULL,
  `agent_remote_area_charge` decimal(10,2) DEFAULT NULL,
  `agent_on_farword_charges` decimal(10,2) DEFAULT NULL,
  `agent_ndx` decimal(10,2) DEFAULT NULL,
  `agent_ddp` decimal(10,2) DEFAULT NULL,
  `agent_extra` decimal(10,2) DEFAULT NULL,
  `agent_amount` decimal(10,2) DEFAULT NULL,
  `agent_linehaul_cost` decimal(10,2) DEFAULT NULL,
  `agent_handling_charges` decimal(10,2) DEFAULT NULL,
  `reference` varchar(350) DEFAULT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `added_by` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoice_detail_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(40) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(10) NOT NULL DEFAULT 'A',
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Stores activity log.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoice_extra_charges` (
  `id` bigint(20) NOT NULL,
  `invoice_detail_id` int(11) NOT NULL,
  `charge_type_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `cost_type` enum('customer','agent') NOT NULL DEFAULT 'customer',
  `cost` decimal(11,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoice_extra_charges_types` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT 0,
  `added_by` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `invoice_templates` (
  `id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `image` varchar(200) DEFAULT NULL,
  `invoice_function` varchar(250) DEFAULT NULL,
  `summary_invoice_function` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `item_details` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `parcel_count` int(5) NOT NULL,
  `item_detail` text NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `label_file` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(30) DEFAULT NULL,
  `hawb_list` text DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `error_list` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL,
  `language` varchar(10) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `is_active` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `language_keys` (
  `id` int(11) NOT NULL,
  `keyword` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `language` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  `updatedby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `licence_plate` (
  `id` int(11) UNSIGNED NOT NULL,
  `range_name` varchar(45) DEFAULT NULL,
  `range_start` bigint(20) UNSIGNED NOT NULL,
  `range_end` bigint(20) UNSIGNED NOT NULL,
  `next_number` bigint(20) UNSIGNED NOT NULL,
  `increment_date` datetime DEFAULT NULL,
  `delivery_network` varchar(45) NOT NULL,
  `prefix` varchar(20) DEFAULT NULL,
  `sufix` varchar(20) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `addedby` int(11) DEFAULT NULL,
  `updatedby` int(11) DEFAULT NULL,
  `country_range` bit(1) DEFAULT b'0',
  `country_list` text DEFAULT NULL,
  `range_reminder_limit` int(5) DEFAULT 1000
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `licence_plate_country` (
  `id` int(11) NOT NULL,
  `licence_plate_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `range_name` varchar(45) DEFAULT NULL,
  `range_start` bigint(20) DEFAULT NULL,
  `range_end` bigint(20) DEFAULT NULL,
  `next_number` bigint(20) DEFAULT NULL,
  `increment_date` datetime DEFAULT NULL,
  `prefix` varchar(10) DEFAULT NULL,
  `sufix` varchar(10) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `addedby` int(11) DEFAULT NULL,
  `updatedby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `location` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `date_updated` varchar(45) DEFAULT NULL,
  `updatedby` timestamp NULL DEFAULT NULL,
  `active` varchar(1) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `warehouseid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `login_request` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(45) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_status` tinyint(1) NOT NULL DEFAULT 0,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `logout_time` timestamp NULL DEFAULT NULL,
  `session_id` varchar(45) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `log_rack_shelf` (
  `id` bigint(20) NOT NULL,
  `rack_shelf_id` bigint(20) NOT NULL,
  `rack_shelf_item_id` bigint(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `in_date` datetime NOT NULL,
  `in_by` int(11) NOT NULL,
  `out_date` datetime DEFAULT NULL,
  `out_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `manifest` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_name` varchar(500) DEFAULT NULL,
  `label_link` varchar(100) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `pieces` varchar(45) DEFAULT NULL,
  `agent_id` bigint(20) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `handling` varchar(200) DEFAULT NULL,
  `pdf_file` varchar(500) DEFAULT NULL,
  `flight_number` varchar(45) DEFAULT NULL,
  `mawb` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `collection_comment` text DEFAULT NULL,
  `collection_date` datetime DEFAULT NULL,
  `collection_date_to` datetime DEFAULT NULL,
  `pickup_date` datetime DEFAULT NULL,
  `delivery_note` text DEFAULT NULL,
  `signature` varchar(100) DEFAULT NULL,
  `pickup_id` int(11) DEFAULT NULL,
  `route_warehouse_id` int(11) DEFAULT NULL,
  `routing_email_date` datetime DEFAULT NULL,
  `date_received` datetime DEFAULT NULL,
  `received_by` varchar(45) DEFAULT NULL,
  `name_of_driver` varchar(100) DEFAULT NULL,
  `licence_number` varchar(100) DEFAULT NULL,
  `account_owner` varchar(45) DEFAULT NULL,
  `number_bag` varchar(45) DEFAULT NULL,
  `product` varchar(500) DEFAULT NULL,
  `carrier_note` varchar(5000) DEFAULT NULL,
  `carrier_pdf` varchar(500) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `is_dispatched` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_send_email` enum('Y','N') NOT NULL DEFAULT 'N',
  `manifest_by` enum('operation','client') NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `manifest_consignment_mapping` (
  `manifestid` int(11) NOT NULL,
  `consignmentid` int(11) NOT NULL,
  `export_mawb` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `manifest_entity_mapping` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `manifest_id` int(11) NOT NULL,
  `manifest_entity_type` enum('p','b','pl') NOT NULL DEFAULT 'p' COMMENT 'p for parcel, b for bagging and pl for parcel'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `manifest_service_mapping` (
  `id` bigint(20) NOT NULL,
  `manifest_id` bigint(20) NOT NULL,
  `service_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `marketplace_order` (
  `id` bigint(20) NOT NULL,
  `marketplace_id` bigint(20) DEFAULT NULL,
  `marketplace_order_number` varchar(45) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `order_status` varchar(45) DEFAULT NULL,
  `receiver_name` varchar(50) DEFAULT NULL,
  `receiver_phone` varchar(45) DEFAULT NULL,
  `receiver_state` varchar(45) DEFAULT NULL,
  `receiver_city` varchar(45) DEFAULT NULL,
  `receiver_country_id` bigint(20) DEFAULT NULL,
  `receiver_addressline1` varchar(100) DEFAULT NULL,
  `receiver_addressline2` varchar(100) DEFAULT NULL,
  `receiver_postcode` varchar(45) DEFAULT NULL,
  `payment_method` varchar(45) DEFAULT NULL,
  `order_total` varchar(45) DEFAULT NULL,
  `tracking_number` varchar(45) DEFAULT NULL,
  `receiver_email` varchar(45) DEFAULT NULL,
  `sender_email` varchar(45) DEFAULT NULL,
  `Ack` varchar(45) DEFAULT NULL,
  `error_code` varchar(45) DEFAULT NULL,
  `error_message` varchar(45) DEFAULT NULL,
  `consignment_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `shipped_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `marketplace_order_details` (
  `id` bigint(20) NOT NULL,
  `marketplace_order_id` bigint(20) DEFAULT NULL,
  `marketplace_item_id` varchar(45) DEFAULT NULL,
  `sku` varchar(45) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `quantity_purchased` varchar(45) DEFAULT NULL,
  `asin` varchar(45) DEFAULT NULL,
  `item_price` varchar(45) DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `market_places` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `translation_key` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `page_link` varchar(255) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `integration_logo` varchar(45) DEFAULT NULL,
  `manual_link` varchar(45) DEFAULT NULL,
  `plugin_key` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT 0,
  `integration_type` int(3) DEFAULT 0,
  `channel_type` int(3) DEFAULT 0,
  `country_id` int(11) DEFAULT 0,
  `last_sync` datetime DEFAULT NULL,
  `is_featured` bit(1) DEFAULT b'0',
  `is_api2cart` bit(1) DEFAULT b'0',
  `help_doc` varchar(100) DEFAULT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `documentation_title` varchar(255) DEFAULT NULL,
  `documentation_cover_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `market_places_authenticate_field` (
  `id` bigint(20) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_value` varchar(255) NOT NULL,
  `market_places_id` bigint(20) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime DEFAULT NULL,
  `is_delete` tinyint(1) DEFAULT 0,
  `auto_generate_value` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `market_place_documentation_mapping` (
  `id` int(10) UNSIGNED NOT NULL,
  `marketplace_id` int(11) DEFAULT NULL,
  `step_title` varchar(250) DEFAULT NULL,
  `step_description` text DEFAULT NULL,
  `step_image` varchar(255) DEFAULT NULL,
  `step_order` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `mawb` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mawb_number` varchar(50) NOT NULL,
  `mawb_source_country_id` int(11) NOT NULL,
  `mawb_source_warehouse_id` int(11) DEFAULT NULL,
  `mawb_destination_country_id` int(11) NOT NULL,
  `mawb_destination_warehouse_id` int(11) DEFAULT NULL,
  `is_active` enum('y','n') NOT NULL DEFAULT 'n',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `mawb_status` enum('d','pd','o') NOT NULL DEFAULT 'o' COMMENT 'o for open, d for disptach, pd for partial dispatch',
  `manifest_label` varchar(255) DEFAULT NULL,
  `mawb_lv_manifest` varchar(255) DEFAULT NULL,
  `mawb_hv_manifest` varchar(255) DEFAULT NULL,
  `bagging_type` enum('p','f') DEFAULT NULL COMMENT 'p for postal and f for freight',
  `bag_is_hv_lv` enum('y','n') DEFAULT 'y',
  `mawb_class` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `mawb_flight_document` (
  `id` bigint(20) NOT NULL,
  `document_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `mawb_flight_document_mapping` (
  `id` bigint(20) NOT NULL,
  `country_id` bigint(20) NOT NULL,
  `document_id` bigint(20) NOT NULL,
  `template_id` bigint(20) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `mawb_parcel_mapping` (
  `id` int(11) NOT NULL,
  `mawb_id` int(11) NOT NULL,
  `parcel_id` int(11) NOT NULL,
  `wharehouse_id` int(11) NOT NULL,
  `bag_id` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `not_found_record` (
  `id` int(11) UNSIGNED NOT NULL,
  `mawb` varchar(45) DEFAULT NULL,
  `bag_number` varchar(45) DEFAULT NULL,
  `tracking_number` varchar(45) DEFAULT NULL,
  `scanned_by` bigint(20) DEFAULT NULL,
  `length` decimal(8,2) DEFAULT NULL,
  `width` decimal(8,2) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `reason` varchar(45) DEFAULT NULL,
  `image` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `oauth_public_keys` (
  `client_id` varchar(80) DEFAULT NULL,
  `public_key` varchar(8000) DEFAULT NULL,
  `private_key` varchar(8000) DEFAULT NULL,
  `encryption_algorithm` varchar(80) DEFAULT 'RS256'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `oauth_scopes` (
  `scope` varchar(80) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ops_summary` (
  `id` int(11) NOT NULL,
  `account` varchar(45) DEFAULT NULL,
  `services` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `quantity` varchar(40) DEFAULT NULL,
  `weight` varchar(45) DEFAULT NULL,
  `carrier` varchar(45) DEFAULT NULL,
  `date_submitted` datetime DEFAULT NULL,
  `reference` varchar(45) DEFAULT NULL,
  `mawb` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `optimus_file_name` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `file_id` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `owe_southafrica_postcode` (
  `id` int(11) NOT NULL,
  `zone` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `main_outlying` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `owe_southafrica_routine` (
  `id` int(11) NOT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zone` varchar(45) DEFAULT NULL,
  `route` varchar(45) DEFAULT NULL,
  `delivery_time` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet` (
  `id` int(11) NOT NULL,
  `palletno` varchar(45) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `close` tinyint(1) DEFAULT 0 COMMENT '1 for Yes 0 for No',
  `userid` int(11) DEFAULT NULL,
  `pallet_carrier_id` int(11) DEFAULT NULL,
  `date_dispatch` timestamp NULL DEFAULT NULL,
  `dispatch_userid` int(11) DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `manifestid` int(11) DEFAULT 0,
  `hub` varchar(45) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0 COMMENT 'O for No and 1 for Yes',
  `comments` varchar(100) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `pallet_source_country_id` int(11) DEFAULT NULL,
  `pallet_source_warehouse_id` int(11) DEFAULT NULL,
  `pallet_destination_country_id` int(11) DEFAULT NULL,
  `pallet_destination_warehouse_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_bag_mapping` (
  `palletid` int(11) NOT NULL,
  `bagid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_bag_remove_reason` (
  `id` int(11) NOT NULL,
  `pallet_id` int(11) NOT NULL,
  `bag_id` int(11) NOT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_carier_group` (
  `id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `carrier_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_carrier` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `service_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_carrier_service` (
  `id` int(11) NOT NULL,
  `carrier_group_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_entity_mapping` (
  `id` int(11) NOT NULL,
  `pallet_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `pallet_entity_type` enum('p','b') NOT NULL DEFAULT 'p' COMMENT 'p for parcel and b for bagging',
  `pre_sort` enum('y','n') NOT NULL DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_location` (
  `id` int(11) NOT NULL,
  `locationid` int(11) DEFAULT NULL,
  `palletid` int(11) DEFAULT NULL,
  `consignmentid` int(11) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `comments` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pallet_name` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `serviceid` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcel` (
  `id` int(11) UNSIGNED NOT NULL,
  `consignment_id` int(11) UNSIGNED NOT NULL,
  `tracking_number` varchar(32) DEFAULT NULL COMMENT 'tracking number for parcel',
  `do_tracking_number` varchar(32) DEFAULT NULL,
  `length` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `width` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `height` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `weight` decimal(4,2) NOT NULL,
  `description` text DEFAULT NULL,
  `parcel_message` text DEFAULT NULL,
  `qty` varchar(45) DEFAULT NULL COMMENT 'Number of items in parcel',
  `commoditycode` varchar(300) DEFAULT NULL,
  `hscode` varchar(300) DEFAULT NULL,
  `grossweight` decimal(10,2) DEFAULT NULL,
  `pweight` varchar(45) DEFAULT NULL,
  `itemvalue` varchar(300) DEFAULT NULL,
  `number_item` int(2) DEFAULT NULL,
  `tarrif_no` varchar(45) DEFAULT NULL,
  `update_weight` decimal(10,2) DEFAULT NULL,
  `owe_status_code` varchar(200) DEFAULT NULL COMMENT 'For tracking purpose',
  `chute_sorted` int(4) DEFAULT NULL,
  `parcel_status_code` int(3) DEFAULT NULL,
  `routing_code` varchar(45) DEFAULT NULL,
  `last_tracking_update` datetime DEFAULT NULL,
  `parcel_item_desc` text DEFAULT NULL,
  `parcel_label` varchar(255) DEFAULT NULL,
  `itemsku` varchar(255) DEFAULT NULL,
  `itemurl` varchar(255) DEFAULT NULL,
  `sort_type` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcelforce_datafile_id` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `file_name_id` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcelforce_depo_detail` (
  `id` int(11) NOT NULL,
  `depo_name` varchar(200) DEFAULT NULL,
  `depo_short_name` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `route_number` varchar(45) DEFAULT NULL,
  `pfw_ect` varchar(45) DEFAULT NULL,
  `pfw_lat` varchar(45) DEFAULT NULL,
  `pfw_lct` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcelforce_hub_details` (
  `id` int(11) NOT NULL,
  `depo_name` varchar(300) DEFAULT NULL,
  `depo_number` varchar(45) DEFAULT NULL,
  `mon_hub_24` varchar(45) DEFAULT NULL,
  `mon_chute_24` varchar(45) DEFAULT NULL,
  `mon_hub_48` varchar(45) DEFAULT NULL,
  `mon_chute_48` varchar(45) DEFAULT NULL,
  `tue_hub_24` varchar(45) DEFAULT NULL,
  `tue_chute_24` varchar(45) DEFAULT NULL,
  `tue_hub_48` varchar(45) DEFAULT NULL,
  `tue_chute_48` varchar(45) DEFAULT NULL,
  `wed_hub_24` varchar(45) DEFAULT NULL,
  `wed_chute_24` varchar(45) DEFAULT NULL,
  `wed_hub_48` varchar(45) DEFAULT NULL,
  `wed_chute_48` varchar(45) DEFAULT NULL,
  `thu_hub_24` varchar(45) DEFAULT NULL,
  `thu_chute_24` varchar(45) DEFAULT NULL,
  `thu_hub_48` varchar(45) DEFAULT NULL,
  `thu_chute_48` varchar(45) DEFAULT NULL,
  `fri_hub_24` varchar(45) DEFAULT NULL,
  `fri_chute_24` varchar(45) DEFAULT NULL,
  `fri_hub_48` varchar(45) DEFAULT NULL,
  `fri_chute_48` varchar(45) DEFAULT NULL,
  `sat_hub_24` varchar(45) DEFAULT NULL,
  `sat_chute_24` varchar(45) DEFAULT NULL,
  `sat_hub_48` varchar(45) DEFAULT NULL,
  `sat_chute_48` varchar(45) DEFAULT NULL,
  `sun_hub_24` varchar(45) DEFAULT NULL,
  `sun_chute_24` varchar(45) DEFAULT NULL,
  `sun_hub_48` varchar(45) DEFAULT NULL,
  `sun_chute_48` varchar(45) DEFAULT NULL,
  `sat_delivery_hub` varchar(45) DEFAULT NULL,
  `sat_delivery_chute` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcelforu_pickup_point` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address_line_1` varchar(250) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `country_iso` varchar(2) DEFAULT NULL,
  `statuscode` varchar(2) DEFAULT NULL,
  `status_description` varchar(100) DEFAULT NULL,
  `latitude` varchar(45) DEFAULT NULL,
  `longitude` varchar(45) DEFAULT NULL,
  `mon` varchar(45) DEFAULT NULL,
  `tue` varchar(45) DEFAULT NULL,
  `wed` varchar(45) DEFAULT NULL,
  `thu` varchar(45) DEFAULT NULL,
  `fri` varchar(45) DEFAULT NULL,
  `sat` varchar(45) DEFAULT NULL,
  `sun` varchar(45) DEFAULT NULL,
  `label_routing` varchar(45) DEFAULT NULL,
  `branch_id` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcel_bagging_mapping` (
  `id` int(11) NOT NULL,
  `parcel_id` int(11) NOT NULL,
  `bag_id` int(11) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcel_iteam` (
  `id` int(11) NOT NULL,
  `parcel_id` int(11) NOT NULL,
  `iteam_name` varchar(255) DEFAULT NULL,
  `iteam_weight` decimal(10,2) DEFAULT NULL,
  `iteam_weight_unit` enum('kg','pound') DEFAULT 'kg',
  `iteam_value` int(11) DEFAULT NULL,
  `iteam_quantity` int(11) DEFAULT NULL,
  `iteam_country_id` int(3) DEFAULT NULL,
  `iteam_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `parcel_log` (
  `id` int(11) NOT NULL DEFAULT 0,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `partnerservicesrouting` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) NOT NULL,
  `from_weight` decimal(10,2) DEFAULT NULL,
  `to_weight` decimal(10,2) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `product_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `payments_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `paypal_payment_id` varchar(100) DEFAULT NULL,
  `txn_id` varchar(150) DEFAULT NULL,
  `billing_id` varchar(150) DEFAULT NULL,
  `sender_email` varchar(150) DEFAULT NULL,
  `amount` double(10,2) NOT NULL,
  `amount_currency_id` int(11) NOT NULL,
  `payment_method` enum('cash','paypal','bank_transfer','cheque') DEFAULT NULL,
  `payment_detail` varchar(150) DEFAULT NULL,
  `user_currency_id` int(11) NOT NULL,
  `debit` decimal(11,2) DEFAULT 0.00,
  `credit` decimal(11,2) DEFAULT 0.00,
  `module_name` varchar(150) DEFAULT NULL,
  `module_id` varchar(50) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `is_completed` enum('yes','no') NOT NULL DEFAULT 'no',
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `payment_gateways` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `gateway_type` enum('paypal') DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `currency_id` varchar(30) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pbt_datafile_id` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pbt_routine` (
  `id` int(11) NOT NULL,
  `file_code` varchar(45) DEFAULT NULL,
  `courier_file_label_code` varchar(45) DEFAULT NULL,
  `transport_label_code` varchar(45) DEFAULT NULL,
  `courier_charges_code` varchar(45) DEFAULT NULL,
  `area_desc` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL,
  `lang_key` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `file_name` varchar(150) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `query_string` varchar(100) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `is_menu_item` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `permissions_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pickup` (
  `id` int(11) NOT NULL,
  `pickup_number` varchar(100) DEFAULT NULL,
  `pickup_date` timestamp NULL DEFAULT NULL,
  `delivery_note` varchar(100) DEFAULT NULL,
  `pick_up_pdf` varchar(100) DEFAULT NULL,
  `collection_pdf` varchar(100) DEFAULT NULL,
  `collection_address` varchar(300) DEFAULT NULL,
  `address_line_1` varchar(100) DEFAULT NULL,
  `address_line_2` varchar(100) DEFAULT NULL,
  `address_line_3` varchar(100) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pmp_routine` (
  `id` int(11) NOT NULL,
  `storeid` int(11) DEFAULT NULL,
  `store_name` varchar(200) DEFAULT NULL,
  `is_active` bit(1) DEFAULT b'1',
  `country` varchar(3) DEFAULT NULL,
  `address_line_1` varchar(45) DEFAULT NULL,
  `address_line_2` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `depot_no` int(11) DEFAULT NULL,
  `depot_description` varchar(45) DEFAULT NULL,
  `round1` int(5) DEFAULT NULL,
  `drop1` int(5) DEFAULT NULL,
  `round2` int(5) DEFAULT NULL,
  `drop2` int(5) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `postcode_user_service_charges` (
  `id` int(11) NOT NULL,
  `from_postcode` varchar(45) DEFAULT NULL COMMENT '-',
  `to_postcode` varchar(45) DEFAULT NULL,
  `postcode_name` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `city_name` varchar(45) DEFAULT NULL,
  `country_iso` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `postitalia_untracked` (
  `id` bigint(32) NOT NULL,
  `postcode` int(8) DEFAULT NULL,
  `region` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `provenience` varchar(45) DEFAULT NULL,
  `sortation` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `postnl_datafile_id` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `service_country` varchar(45) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `post_italia_routing` (
  `id` int(11) NOT NULL,
  `zip_code` varchar(45) NOT NULL,
  `routing_file` varchar(45) NOT NULL,
  `province` varchar(45) NOT NULL,
  `province_iso_code` varchar(45) NOT NULL,
  `sortation_name` varchar(45) DEFAULT NULL,
  `sortation_id` varchar(45) DEFAULT NULL,
  `sortation_name_on_bag` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pre_alert` (
  `id` int(11) NOT NULL,
  `mawb_id` int(11) DEFAULT NULL,
  `flight_number` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `pieces` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `weight` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `etd` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `eta` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `current_status` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `date_time` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `cleared` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `status` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `comments` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `account` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `files` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `uploadby` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `shed` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `date_entry` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_updated` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `type` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pricing_bulk_data_1579539860` (
  `id` int(11) NOT NULL,
  `tracking_number` varchar(45) DEFAULT NULL,
  `hawb` varchar(45) DEFAULT NULL,
  `charges_reference` varchar(45) DEFAULT NULL,
  `basic_charges` decimal(10,2) DEFAULT NULL,
  `fuel_charges` decimal(10,2) DEFAULT NULL,
  `additional_charges` decimal(10,2) DEFAULT NULL,
  `ndx` decimal(10,2) DEFAULT NULL,
  `ddp` decimal(10,2) DEFAULT NULL,
  `remote_area_charges` decimal(10,2) DEFAULT NULL,
  `handling_charges` decimal(10,2) DEFAULT NULL,
  `reference` decimal(10,2) DEFAULT NULL,
  `linehaul` decimal(10,2) DEFAULT NULL,
  `address_correction` decimal(10,2) DEFAULT NULL,
  `airline_handling` decimal(10,2) DEFAULT NULL,
  `clearance` decimal(10,2) DEFAULT NULL,
  `collections` decimal(10,2) DEFAULT NULL,
  `ddp_admin_fee` decimal(10,2) DEFAULT NULL,
  `ddp_charge` decimal(10,2) DEFAULT NULL,
  `delivery` decimal(10,2) DEFAULT NULL,
  `dispatch` decimal(10,2) DEFAULT NULL,
  `labour` decimal(10,2) DEFAULT NULL,
  `other` decimal(10,2) DEFAULT NULL,
  `out_of_gauge` decimal(10,2) DEFAULT NULL,
  `over_weight` decimal(10,2) DEFAULT NULL,
  `ras` decimal(10,2) DEFAULT NULL,
  `redelivery` decimal(10,2) DEFAULT NULL,
  `label_charges` decimal(10,2) DEFAULT NULL,
  `vat` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `mobile_tracking` decimal(10,2) DEFAULT NULL,
  `user_account` varchar(45) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `is_complete` tinyint(1) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `batch_number` varchar(45) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `data_result` text DEFAULT NULL,
  `data_result_count` tinyint(2) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `pricing_bulk_data_1579539864` (
  `id` int(11) NOT NULL,
  `tracking_number` varchar(45) DEFAULT NULL,
  `hawb` varchar(45) DEFAULT NULL,
  `charges_reference` varchar(45) DEFAULT NULL,
  `basic_charges` decimal(10,2) DEFAULT NULL,
  `fuel_charges` decimal(10,2) DEFAULT NULL,
  `additional_charges` decimal(10,2) DEFAULT NULL,
  `ndx` decimal(10,2) DEFAULT NULL,
  `ddp` decimal(10,2) DEFAULT NULL,
  `remote_area_charges` decimal(10,2) DEFAULT NULL,
  `handling_charges` decimal(10,2) DEFAULT NULL,
  `reference` decimal(10,2) DEFAULT NULL,
  `linehaul` decimal(10,2) DEFAULT NULL,
  `address_correction` decimal(10,2) DEFAULT NULL,
  `airline_handling` decimal(10,2) DEFAULT NULL,
  `clearance` decimal(10,2) DEFAULT NULL,
  `collections` decimal(10,2) DEFAULT NULL,
  `ddp_admin_fee` decimal(10,2) DEFAULT NULL,
  `ddp_charge` decimal(10,2) DEFAULT NULL,
  `delivery` decimal(10,2) DEFAULT NULL,
  `dispatch` decimal(10,2) DEFAULT NULL,
  `labour` decimal(10,2) DEFAULT NULL,
  `other` decimal(10,2) DEFAULT NULL,
  `out_of_gauge` decimal(10,2) DEFAULT NULL,
  `over_weight` decimal(10,2) DEFAULT NULL,
  `ras` decimal(10,2) DEFAULT NULL,
  `redelivery` decimal(10,2) DEFAULT NULL,
  `label_charges` decimal(10,2) DEFAULT NULL,
  `vat` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `mobile_tracking` decimal(10,2) DEFAULT NULL,
  `user_account` varchar(45) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `is_complete` tinyint(1) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `batch_number` varchar(45) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `data_result` text DEFAULT NULL,
  `data_result_count` tinyint(2) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `product_name` varchar(45) NOT NULL,
  `insurance` decimal(5,2) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT 1 COMMENT '0 - in-active\n1-active\n2 -deleted',
  `from_weight` decimal(10,3) DEFAULT NULL,
  `to_weight` decimal(10,3) DEFAULT NULL,
  `logo` varchar(45) DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `vol_weight` decimal(10,3) DEFAULT NULL,
  `vol_denominator` int(1) DEFAULT NULL,
  `is_untrack` int(1) DEFAULT NULL,
  `remotearea_charges` decimal(10,3) DEFAULT NULL,
  `fuel_charges` decimal(10,3) DEFAULT NULL,
  `added_date` timestamp NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `transit_time` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `product_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `product_routine_log` (
  `id` int(11) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `logdate` datetime DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `log_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `proforma_invoice_biiling` (
  `id` int(11) NOT NULL,
  `consignment_id` int(11) NOT NULL,
  `billing_company` varchar(100) DEFAULT NULL,
  `billing_contact` varchar(100) DEFAULT NULL,
  `billing_address_line_1` varchar(50) DEFAULT NULL,
  `billing_address_line_2` varchar(50) DEFAULT NULL,
  `billing_address_line_3` varchar(50) DEFAULT NULL,
  `billing_city` varchar(50) DEFAULT NULL,
  `billing_country` varchar(50) DEFAULT NULL,
  `billing_postcode` varchar(10) DEFAULT NULL,
  `billing_telephone` varchar(20) DEFAULT NULL,
  `payment_terms` varchar(45) DEFAULT NULL,
  `export_type` varchar(45) DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `delivery_terms` varchar(45) DEFAULT NULL,
  `link_file` varchar(200) DEFAULT NULL,
  `payer_vat` varchar(45) DEFAULT NULL,
  `harm_comm_code` varchar(45) DEFAULT NULL,
  `export` varchar(45) DEFAULT NULL,
  `invoice_type` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `quotation_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `shipping_from` bigint(20) DEFAULT NULL,
  `shipping_to` bigint(20) DEFAULT NULL,
  `carrier_id` bigint(20) DEFAULT NULL,
  `service_id` bigint(20) DEFAULT NULL,
  `account_id` bigint(20) DEFAULT NULL,
  `price_type` enum('user','agent','manual') DEFAULT 'user',
  `pieces` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `dimensions` longtext DEFAULT NULL,
  `volumn_weight` decimal(10,3) DEFAULT NULL,
  `basic_charge` decimal(10,2) DEFAULT 0.00,
  `vat_charge` decimal(10,2) DEFAULT 0.00,
  `extra_charge` decimal(10,2) DEFAULT 0.00,
  `sub_total` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `user_email` varchar(45) DEFAULT NULL,
  `discount_type` enum('fixed','percentage') DEFAULT NULL,
  `total_charge` decimal(10,2) DEFAULT 0.00,
  `remark` text DEFAULT NULL,
  `status` enum('active','inactive','remove') DEFAULT 'active',
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `conversionrate` float DEFAULT NULL,
  `pdf` varchar(99) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `rack` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_title` varchar(100) NOT NULL,
  `rack_rows` int(5) NOT NULL,
  `rack_cols` int(5) DEFAULT NULL,
  `shelf_dimension` varchar(50) NOT NULL,
  `shelf_max_weight` decimal(11,2) NOT NULL,
  `is_york` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `added_date` datetime NOT NULL,
  `added_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `rack_shelf` (
  `id` bigint(20) NOT NULL,
  `rack_id` int(11) NOT NULL,
  `shelf_no` int(5) NOT NULL,
  `is_filled` tinyint(1) NOT NULL DEFAULT 0,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `rack_shelf_item` (
  `id` bigint(20) NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  `tracking_number` varchar(45) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `weight` decimal(11,2) NOT NULL,
  `dimension` varchar(50) NOT NULL,
  `added_date` datetime NOT NULL,
  `added_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ratebands` (
  `id` int(11) UNSIGNED NOT NULL,
  `courier_service_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `orderq` int(5) NOT NULL DEFAULT 0,
  `active` tinyint(4) DEFAULT 1,
  `deletedq` char(1) DEFAULT 'N',
  `added_on` datetime DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  `changed_on` datetime DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `reamus_destination_station` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `postcode_from` varchar(10) DEFAULT NULL,
  `postcode_to` varchar(10) DEFAULT NULL,
  `product_code` varchar(2) DEFAULT NULL,
  `station_id` varchar(10) DEFAULT NULL,
  `hub_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `reamus_exception` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_code` varchar(5) DEFAULT NULL,
  `postcode_from` varchar(10) DEFAULT NULL,
  `postcode_to` varchar(10) DEFAULT NULL,
  `product_code` varchar(5) DEFAULT NULL,
  `feature_code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `reamus_product_service` (
  `id` int(11) UNSIGNED NOT NULL,
  `reamus_id` varchar(19) DEFAULT NULL,
  `product_code` varchar(2) DEFAULT NULL,
  `feature_code` varchar(5) DEFAULT NULL,
  `exception` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `reamus_service` (
  `id` int(11) UNSIGNED NOT NULL,
  `service_id` int(11) UNSIGNED DEFAULT NULL,
  `service_description` varchar(50) DEFAULT NULL,
  `product_line1` varchar(15) DEFAULT NULL,
  `product_line2` varchar(35) DEFAULT NULL,
  `product_code` varchar(2) DEFAULT NULL,
  `date_code` varchar(2) DEFAULT NULL,
  `day_text` varchar(1) DEFAULT NULL,
  `time_code` varchar(1) DEFAULT NULL,
  `time_text` varchar(1) DEFAULT NULL,
  `handling` varchar(15) DEFAULT NULL,
  `feature_id` varchar(3) DEFAULT NULL,
  `feature_code` varchar(2) DEFAULT NULL,
  `file_type` varchar(3) DEFAULT NULL,
  `consignment_flag` varchar(3) DEFAULT NULL,
  `ds_flag` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `reamus_site` (
  `id` int(11) UNSIGNED NOT NULL,
  `reamus_id` varchar(10) DEFAULT NULL,
  `site` varchar(100) DEFAULT NULL,
  `reamus_id2` varchar(10) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remoteareas` (
  `id` int(11) NOT NULL,
  `remoteareas_groups_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `from_postcode` varchar(8) DEFAULT NULL,
  `to_postcode` varchar(8) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remoteareas_groups` (
  `id` int(11) NOT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `group_name` varchar(45) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remoteareas_groups_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remoteareas_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remotearea_charges_carrier` (
  `id` int(11) NOT NULL,
  `remotearea_group_id` int(11) DEFAULT NULL,
  `remotearea_charges` decimal(10,2) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remotearea_charges_carrier_user` (
  `id` int(11) NOT NULL,
  `remotearea_group_id` int(11) DEFAULT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `remotearea_charges` decimal(10,2) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remotearea_charges_services` (
  `id` int(11) NOT NULL,
  `remotearea_group_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `remotearea_charges` decimal(10,2) DEFAULT NULL,
  `from_weight` decimal(10,2) DEFAULT NULL,
  `to_weight` decimal(10,2) DEFAULT NULL,
  `formulla` varchar(45) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remotearea_charges_services_user` (
  `id` int(11) NOT NULL,
  `remotearea_group_id` int(11) DEFAULT NULL,
  `remotearea_charges` decimal(10,2) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `from_weight` decimal(10,2) DEFAULT NULL,
  `to_weight` decimal(10,2) DEFAULT NULL,
  `formulla` varchar(45) DEFAULT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remotearea_charges_tariffs` (
  `id` int(11) NOT NULL,
  `remotearea_group_id` int(11) DEFAULT NULL,
  `remotearea_charges` decimal(10,2) DEFAULT NULL,
  `tariff_id` int(11) DEFAULT NULL,
  `from_weight` decimal(10,2) DEFAULT NULL,
  `to_weight` decimal(10,2) DEFAULT NULL,
  `formulla` varchar(45) DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remotearea_user_mapping` (
  `id` int(11) NOT NULL,
  `user_account` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `postcode_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '-',
  `service_code` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `charges` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `remotearea_added_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `remotearea_weight_charge` (
  `id` int(11) NOT NULL,
  `weight_from` decimal(10,2) DEFAULT NULL,
  `weight_to` decimal(10,2) DEFAULT NULL,
  `service_code` varchar(45) DEFAULT NULL,
  `country_iso` varchar(10) DEFAULT NULL,
  `postcode_name` varchar(45) DEFAULT NULL,
  `formulla` varchar(255) DEFAULT NULL,
  `charges` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `report_customize_settings` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `report_title` varchar(100) DEFAULT NULL,
  `report_key` varchar(100) DEFAULT NULL,
  `fields_data` longtext DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `routing_user_mapping` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routing_added_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `royalmail_docket_number` (
  `id` int(11) NOT NULL,
  `tracking_number` varchar(45) DEFAULT NULL,
  `docket_number` varchar(45) DEFAULT NULL,
  `file_name` varchar(45) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `royalmail_sortcode` (
  `id` int(11) NOT NULL,
  `postcode_area` varchar(5) DEFAULT NULL,
  `sortcode` varchar(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `sales_call_log` (
  `id` int(10) NOT NULL,
  `date_call` date DEFAULT NULL,
  `meeting_date` timestamp NULL DEFAULT NULL,
  `customer_code` varchar(45) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `detail_discussed` text DEFAULT NULL,
  `document_link` varchar(200) DEFAULT NULL,
  `follow_meeting_date` timestamp NULL DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `email_send` char(1) DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `sales_pot_comission` (
  `id` bigint(20) NOT NULL,
  `date_added` datetime NOT NULL,
  `no_of_shipments` int(5) NOT NULL DEFAULT 0,
  `comission` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `paid_by` int(5) DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `company_comission` decimal(10,2) DEFAULT NULL,
  `sales_comission` decimal(10,2) DEFAULT NULL,
  `salepot_table_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `code` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `type` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `from_weight` decimal(10,3) DEFAULT NULL,
  `to_weight` decimal(10,3) DEFAULT NULL,
  `wieght_type` int(11) DEFAULT 1 COMMENT '1 for parcel \n2 for shipment',
  `supplier` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `service_type` enum('D','C','B','DO') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'D' COMMENT 'D dispatch, B both, C collection, DO for drop off',
  `drop_off_service_id` bigint(20) DEFAULT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `fuel_surcharge_cost` decimal(9,2) DEFAULT NULL,
  `fuel_surcharge` decimal(9,2) NOT NULL,
  `fuel_surcharge_type` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `max_length` decimal(9,2) NOT NULL,
  `max_width` decimal(9,2) NOT NULL,
  `max_height` decimal(9,2) NOT NULL,
  `max_volumetric_weight` decimal(9,2) DEFAULT NULL,
  `volumetric_denominator` int(11) DEFAULT 5000,
  `send_data_courier` tinyint(4) DEFAULT 0,
  `is_document` tinyint(1) DEFAULT 0,
  `friday_only_flag` tinyint(1) DEFAULT NULL,
  `saturday_only_flag` tinyint(1) DEFAULT 0,
  `sunday_only_flag` tinyint(1) DEFAULT 0,
  `product_owner` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `deletedq` tinyint(1) DEFAULT 0,
  `added_on` datetime DEFAULT NULL,
  `added_by` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `changed_on` datetime DEFAULT NULL,
  `changed_by` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `uploaded_currency` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `uploaded_currency_value` decimal(10,2) DEFAULT NULL,
  `registration_fee` decimal(10,2) DEFAULT 0.00,
  `weight_after` decimal(10,2) DEFAULT 0.00,
  `aditional_charge` decimal(10,2) DEFAULT 0.00,
  `origin_country` int(11) DEFAULT 255,
  `is_untrack` bit(1) DEFAULT b'0',
  `account_owner` int(11) DEFAULT NULL,
  `remotearea` enum('ON_WEIGHT','ON_PIECE') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'ON_PIECE',
  `carrier_address_limit` int(5) DEFAULT 30,
  `label_class_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `transit_time` int(3) DEFAULT NULL,
  `required_email` tinyint(1) DEFAULT 0,
  `required_telephone` tinyint(1) DEFAULT 0,
  `shipment_type` enum('LETTER','PARCEL') DEFAULT 'PARCEL',
  `pre_sort` enum('YES','NO') DEFAULT 'NO',
  `proforma_invoice` tinyint(1) DEFAULT 0,
  `agent_dispatch` enum('Y','N') DEFAULT 'N',
  `brief_manifest` enum('Y','N') DEFAULT 'N',
  `delivery_mode` tinyint(1) DEFAULT NULL COMMENT '1 - Door to Door Delivery\n2 - Parcel Shops\n3 - Door to Door Delivery (POD)\n',
  `insurance_available` tinyint(1) DEFAULT 0,
  `vol_wgt_formula` varchar(50) DEFAULT NULL COMMENT 'L* W * H / 5000',
  `is_remotearea` enum('Y','N') DEFAULT 'N',
  `is_customized` bit(1) DEFAULT b'0',
  `pre_advise` enum('Y','N') NOT NULL DEFAULT 'N',
  `pre_alert` enum('Y','N') NOT NULL DEFAULT 'N',
  `pre_alert_email` text DEFAULT NULL,
  `cut_off_time` varchar(5) DEFAULT NULL,
  `label_charges` decimal(10,2) DEFAULT 0.00,
  `allow_oversize` tinyint(4) NOT NULL DEFAULT 0,
  `allow_overweight` tinyint(4) NOT NULL DEFAULT 0,
  `maximum_allowed_dimension` int(4) NOT NULL DEFAULT 0,
  `maximum_dim_formula` varchar(100) DEFAULT NULL,
  `validation_type` enum('mail','courier') NOT NULL DEFAULT 'mail',
  `zone_type` enum('country','postcode') DEFAULT 'country',
  `tariff_type` enum('multi','single') DEFAULT 'single',
  `girth` decimal(10,2) DEFAULT NULL,
  `girth_formula` varchar(255) DEFAULT NULL,
  `mail_type` enum('letter','boxable','non-boxable') DEFAULT NULL,
  `mail_option` enum('commercial','freight_to_post') DEFAULT NULL,
  `is_reschedulable` int(1) NOT NULL DEFAULT 0,
  `carrier_service_code` varchar(50) DEFAULT NULL,
  `is_eori_required` int(2) NOT NULL DEFAULT 0,
  `delivery_type` enum('all','economy','priority','') NOT NULL DEFAULT 'all',
  `is_commercials` enum('required','not required') NOT NULL DEFAULT 'not required',
  `is_cn` enum('required','not required') NOT NULL DEFAULT 'not required'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `services_dpd` (
  `id` int(11) NOT NULL,
  `2_digit_service_code` varchar(2) DEFAULT NULL,
  `3_digit_service_code` varchar(3) DEFAULT NULL,
  `dpd_product_desc` varchar(45) DEFAULT NULL,
  `dpd_label_service` varchar(45) DEFAULT NULL,
  `ilk_product_desc` varchar(45) DEFAULT NULL,
  `ilk_alternative_service_desc` varchar(45) DEFAULT NULL,
  `premium` varchar(45) DEFAULT NULL,
  `sec_dpd` varchar(45) DEFAULT NULL,
  `sec_ilk` varchar(45) DEFAULT NULL,
  `ilk_max_parcels_per_con` int(11) DEFAULT NULL,
  `ilk_max_weight_per_parcel` int(11) DEFAULT NULL,
  `dpd_max_parcels_per_con` int(11) DEFAULT NULL,
  `dpd_max_weight_per_parcel` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_agent_mapping` (
  `id` int(11) NOT NULL,
  `serviceid` int(11) DEFAULT NULL,
  `agentid` int(11) DEFAULT NULL,
  `linehaul_agent` int(1) DEFAULT 0,
  `account_number` varchar(45) DEFAULT NULL,
  `api_url` varchar(200) DEFAULT NULL,
  `api_username` varchar(45) DEFAULT NULL,
  `api_password` varchar(45) DEFAULT NULL,
  `ftp_host` varchar(45) DEFAULT NULL,
  `ftp_username` varchar(45) DEFAULT NULL,
  `ftp_password` varchar(45) DEFAULT NULL,
  `integration_type` varchar(5) DEFAULT NULL,
  `class_file_name` varchar(45) DEFAULT NULL,
  `insurance_charges` decimal(8,2) DEFAULT 0.00,
  `insurance_cover` decimal(8,2) DEFAULT 0.00,
  `reroute_charges` decimal(8,2) DEFAULT 0.00,
  `oversize_charges` decimal(8,2) DEFAULT 0.00,
  `address_change_charges` decimal(8,2) DEFAULT 0.00,
  `other_surcharges` decimal(8,2) DEFAULT 0.00,
  `return_charges` decimal(8,2) DEFAULT 0.00,
  `relabel_charges` decimal(8,2) DEFAULT 0.00,
  `wrong_address_charges` decimal(8,2) DEFAULT 0.00,
  `from_weight` decimal(8,2) DEFAULT 0.00,
  `to_weight` decimal(8,2) DEFAULT 0.00,
  `email` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_collection_county` (
  `id` bigint(20) NOT NULL,
  `service_id` bigint(20) NOT NULL,
  `country_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_constant` (
  `id` int(11) NOT NULL,
  `constant` varchar(200) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `caption` varchar(200) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `design_control` varchar(45) DEFAULT NULL,
  `mandatory` bit(1) DEFAULT b'0',
  `sort_order` int(11) DEFAULT NULL,
  `integration_type` enum('API','EDI','SOFTWARE') DEFAULT NULL,
  `default_values` text DEFAULT NULL,
  `field_size` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_constant_value` (
  `id` int(11) NOT NULL,
  `constant_value` varchar(250) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `constant_id` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_country_ttime` (
  `id` int(11) NOT NULL COMMENT 'Primary Key of the table',
  `id_country` int(11) DEFAULT NULL COMMENT 'This field is used as a foreign key from country Table in order to find the transit time of which country with respect to what service ',
  `id_service` int(11) DEFAULT NULL COMMENT 'This field is used as a foreign key from Service Table in order to find the transit time of which service with respect to what country ',
  `transit_time` int(1) DEFAULT NULL COMMENT 'It will tell you the service time in days to the country it is going to.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='This table is to get service with respect to country Transit Time. For Example WPX service is taking 3 days to India but 7 Days to Afghanistan.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_document` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT 0,
  `document_name` varchar(255) NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(11) NOT NULL,
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `service_range_mapping` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `licence_plate_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `shopping_platform` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `page_key` varchar(30) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `translation_key` varchar(255) DEFAULT NULL,
  `plugin_key` varchar(255) DEFAULT NULL,
  `integration_logo` varchar(45) DEFAULT NULL,
  `display_option` tinyint(2) DEFAULT 0 COMMENT '0 - manual / connect\n1 - manual\n2 - connect\n',
  `connect_url` varchar(45) DEFAULT NULL,
  `active` tinyint(2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `sorter_postcode_zone` (
  `id` int(11) UNSIGNED NOT NULL,
  `area` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `zone` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `sort_key_record` (
  `id` int(11) NOT NULL,
  `pos_sld_sort_level_key` varchar(8) NOT NULL,
  `pos_sld_level_1_type` varchar(8) NOT NULL,
  `pos_sld_level_1_name` varchar(8) NOT NULL,
  `pos_sld_level_1_code` varchar(8) NOT NULL,
  `pos_sld_level_2_type` varchar(8) NOT NULL,
  `pos_sld_level_2_name` varchar(8) NOT NULL,
  `pos_sld_level_2_code` varchar(8) NOT NULL,
  `pos_sld_level_3_type` varchar(8) NOT NULL,
  `pos_sld_level_3_name` varchar(8) NOT NULL,
  `pos_sld_level_3_code` varchar(8) NOT NULL,
  `pos_sld_level_4_type` varchar(8) NOT NULL,
  `pos_sld_level_4_name` varchar(8) NOT NULL,
  `pos_sld_level_4_code` varchar(8) NOT NULL,
  `pos_sld_level_5_type` varchar(8) NOT NULL,
  `pos_sld_level_5_name` varchar(8) NOT NULL,
  `pos_sld_level_5_code` varchar(8) NOT NULL,
  `pos_sld_hermes_barcode_1_to_7` varchar(7) NOT NULL,
  `pos_sld_hermes_barcode_seq_key` varchar(7) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `sp_tariff_log` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `charges_type` varchar(45) DEFAULT NULL,
  `charges` decimal(10,2) DEFAULT 0.00,
  `formulla` varchar(100) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `consignment_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `status_reason` (
  `id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tagnumber_range` (
  `id` int(11) NOT NULL,
  `range_start` bigint(20) DEFAULT NULL,
  `range_end` bigint(20) DEFAULT NULL,
  `next_number` bigint(20) DEFAULT NULL,
  `increment_date` datetime DEFAULT NULL,
  `service` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariffs` (
  `id` int(11) NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `currency_id` int(11) DEFAULT 2,
  `tariff_type` enum('customer','supplier') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `tariffs_pricing_rule_id` int(11) DEFAULT 0,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariffs_account_mapping` (
  `id` int(11) NOT NULL,
  `tariff_id` int(11) NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `added_date` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariffs_details` (
  `id` int(11) UNSIGNED NOT NULL,
  `tariffs_id` int(11) NOT NULL,
  `from_zone_id` int(11) UNSIGNED NOT NULL,
  `to_zone_id` int(11) UNSIGNED NOT NULL,
  `weight_from` decimal(7,2) NOT NULL,
  `weight_to` decimal(7,2) NOT NULL,
  `weight_cost` decimal(9,2) NOT NULL,
  `piece_cost` decimal(5,2) NOT NULL,
  `formula` varchar(255) DEFAULT 'Q  ( ITMCHR + REG ) + W  CHRG' COMMENT 'Formulla for calculation '
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariffs_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `old_id` int(11) UNSIGNED DEFAULT NULL,
  `courier_service_id` int(11) UNSIGNED NOT NULL,
  `collection_rateband_id` int(11) UNSIGNED NOT NULL,
  `destination_rateband_id` int(11) UNSIGNED NOT NULL,
  `collection_postcode_group_id` int(11) NOT NULL,
  `destination_postcode_group_id` int(11) DEFAULT NULL,
  `weight_from` decimal(7,2) NOT NULL,
  `weight_to` decimal(7,2) NOT NULL,
  `tariff` decimal(9,2) NOT NULL,
  `add_unit_cost` decimal(5,2) NOT NULL,
  `unit_size` decimal(5,2) NOT NULL DEFAULT 0.00,
  `extra_tariff` decimal(7,2) DEFAULT NULL,
  `extra_add_unit_cost` decimal(7,2) DEFAULT NULL,
  `orderq` int(5) NOT NULL DEFAULT 0,
  `active` tinyint(4) DEFAULT 1,
  `deletedq` char(1) DEFAULT 'N',
  `added_on` datetime DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  `changed_on` datetime DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `customer_id` varchar(255) NOT NULL,
  `formula` varchar(100) DEFAULT 'Q * ( ITMCHR + REG ) + W * CHRG' COMMENT 'Formulla for calculation ',
  `log_date` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariffs_pricing` (
  `id` int(11) NOT NULL,
  `tariff_id` int(11) NOT NULL,
  `tarif_pricing_type` enum('single_tariff','whole_tariff') DEFAULT NULL,
  `to_zone_id` int(11) DEFAULT NULL,
  `weight_from` decimal(7,2) DEFAULT NULL,
  `weight_to` decimal(7,2) DEFAULT NULL,
  `margin_type` enum('percentage','price') DEFAULT NULL,
  `margin` decimal(9,2) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariffs_pricing_rules` (
  `id` int(11) NOT NULL,
  `tariff_id` int(11) DEFAULT 0,
  `name` varchar(100) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariffs_pricing_rules_details` (
  `id` int(11) NOT NULL,
  `tariff_pricing_rule_id` int(11) DEFAULT NULL,
  `to_zone_id` int(11) DEFAULT NULL,
  `weight_from` decimal(7,2) DEFAULT NULL,
  `weight_to` decimal(7,2) DEFAULT NULL,
  `margin` decimal(9,2) DEFAULT NULL,
  `margin_type` enum('percentage','price') DEFAULT NULL,
  `margin_weight_cost` varchar(20) DEFAULT NULL,
  `margin_piece_cost` varchar(20) DEFAULT NULL,
  `linehaul` decimal(7,2) DEFAULT NULL,
  `linehaul_type` enum('kilogram','flat') DEFAULT NULL,
  `tariff_pricing_type` enum('whole','multiple') DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariff_additional_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tariff_id` bigint(20) UNSIGNED NOT NULL,
  `consignment_charges_types_id` bigint(20) UNSIGNED NOT NULL,
  `charge` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `charge_type` enum('fixed','percentage') DEFAULT 'fixed',
  `added_by` bigint(20) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariff_details` (
  `id` int(11) NOT NULL,
  `tariff_name` varchar(45) DEFAULT NULL,
  `status` bit(1) DEFAULT b'0',
  `tariff_type` varchar(10) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'GBP'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariff_service_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tariff_id` bigint(20) UNSIGNED NOT NULL,
  `tariff_charges_types_id` bigint(20) UNSIGNED NOT NULL,
  `charge` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `charge_type` enum('fixed','percentage') DEFAULT 'fixed',
  `added_by` bigint(20) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tariff_user_mapping` (
  `id` int(11) NOT NULL,
  `tariff_name` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tariff_added_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `themes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `style_sheet` varchar(100) DEFAULT NULL,
  `dashboard_template` varchar(80) DEFAULT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `created_by` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tourline_routine` (
  `id` bigint(20) NOT NULL,
  `agency_name` varchar(60) DEFAULT NULL,
  `postal_code` varchar(45) DEFAULT NULL,
  `agency_code` varchar(45) DEFAULT NULL,
  `zone` varchar(45) DEFAULT NULL,
  `province` varchar(45) DEFAULT NULL,
  `route_code` varchar(45) DEFAULT NULL,
  `km` varchar(45) DEFAULT NULL,
  `town_name` varchar(100) DEFAULT NULL,
  `kilometer` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tracking_data` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` enum('parcel','shipment') NOT NULL DEFAULT 'parcel',
  `tracking_number` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `track_point` varchar(100) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status_code_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `pod_image` varchar(200) DEFAULT NULL,
  `carrier_code` varchar(100) DEFAULT NULL,
  `carrier_desc` varchar(255) DEFAULT NULL,
  `signatory` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `parcel_image` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tracking_estimated_time` (
  `id` int(11) NOT NULL,
  `handeling_code` varchar(45) DEFAULT NULL,
  `country_iso` varchar(45) DEFAULT NULL,
  `estimated_time` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `tracking_status_codes` (
  `id` int(11) NOT NULL,
  `status_code` varchar(45) DEFAULT NULL,
  `status_code_map` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ukmail_authentication` (
  `id` int(11) NOT NULL,
  `authentication_token` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `user_account` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `ukpostcodelatlng` (
  `id` int(11) NOT NULL,
  `postcode` varchar(8) NOT NULL,
  `latitude` decimal(18,15) NOT NULL,
  `longitude` decimal(18,15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_type` enum('corporate','client','admin','driver') NOT NULL DEFAULT 'client',
  `user_name` varchar(30) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `active_flag` bit(1) NOT NULL DEFAULT b'0',
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `api_key` varchar(100) DEFAULT NULL,
  `api_secert` varchar(100) DEFAULT NULL,
  `api_date` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_employee` bit(1) DEFAULT b'0',
  `warehouse_id` int(11) DEFAULT NULL,
  `dashboard` enum('corporate','operation','customer_service','account','driver') NOT NULL DEFAULT 'corporate',
  `invalid_login_count` int(11) DEFAULT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_deleted` bit(1) DEFAULT b'0',
  `archive_server` bit(1) DEFAULT b'0',
  `carrier_setup_agreement` bit(1) DEFAULT b'0',
  `receive_email` enum('y','n') DEFAULT 'n',
  `tc_agreed_date` date DEFAULT NULL,
  `is_tc_agreed` enum('y','n','i') NOT NULL DEFAULT 'n',
  `address_2` varchar(50) DEFAULT NULL,
  `address_3` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postcode` varchar(15) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `commission_break_event_amount` varchar(50) NOT NULL,
  `is_sale_pot_eligible` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `userhasgroups` (
  `id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_account_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(40) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(10) NOT NULL DEFAULT 'A',
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Stores activity log.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_account_old` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_account` varchar(30) DEFAULT NULL,
  `active_flag` bit(1) NOT NULL DEFAULT b'0',
  `company` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `return_address` varchar(255) DEFAULT NULL,
  `sms_dpd` bit(1) DEFAULT b'0',
  `user_service_type` enum('CHOICE','ROUTING','BOTH') DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `instant_label` bit(1) DEFAULT b'1',
  `country` varchar(3) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `tracking_api_access` bit(1) DEFAULT b'0',
  `import_data_csv` bit(1) DEFAULT b'0',
  `proforma` bit(1) DEFAULT b'0',
  `add_tracking` bit(1) DEFAULT b'0',
  `collection` bit(1) DEFAULT b'0',
  `default_description` varchar(255) DEFAULT NULL,
  `default_notes` varchar(255) DEFAULT NULL,
  `default_weight` decimal(5,2) DEFAULT NULL,
  `payment_term` text DEFAULT NULL,
  `query_term` text DEFAULT NULL,
  `vat_number` varchar(40) DEFAULT NULL,
  `billing_currency` varchar(3) DEFAULT 'GBP',
  `vat_chargable` bit(1) DEFAULT b'0',
  `vat_value` decimal(10,2) DEFAULT NULL COMMENT 'this is percentage field',
  `allow_remote_area` bit(1) DEFAULT b'0',
  `telephone` varchar(20) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `date_dispatch` bit(1) DEFAULT b'0',
  `is_product` varchar(2) DEFAULT '0',
  `profile_image` varchar(255) DEFAULT NULL,
  `send_courier_data` bit(1) DEFAULT b'0' COMMENT 'Send data on label Creation',
  `archive_server` bit(1) DEFAULT b'0',
  `credit_check` bit(1) DEFAULT b'0',
  `tariff_agreed` bit(1) DEFAULT b'0',
  `sales_person` varchar(45) DEFAULT NULL,
  `scan_document` text DEFAULT NULL,
  `data_entry` bit(1) DEFAULT b'0',
  `bank_account_title` varchar(45) DEFAULT NULL,
  `bank_sortcode` varchar(10) DEFAULT NULL,
  `bank_account_number` varchar(20) DEFAULT NULL,
  `bank_branch_address` varchar(255) DEFAULT NULL,
  `trade_name_i` varchar(50) DEFAULT NULL,
  `trade_address_i` varchar(255) DEFAULT NULL,
  `trade_email_i` varchar(255) DEFAULT NULL,
  `trade_phone_i` varchar(50) DEFAULT NULL,
  `trade_name_ii` varchar(50) DEFAULT NULL,
  `trade_address_ii` varchar(255) DEFAULT NULL,
  `trade_email_ii` varchar(255) DEFAULT NULL,
  `trade_phone_ii` varchar(50) DEFAULT NULL,
  `reg_number` varchar(20) DEFAULT NULL,
  `reg_address` varchar(255) DEFAULT NULL,
  `reg_postcode` varchar(10) DEFAULT NULL,
  `reg_country` varchar(5) DEFAULT NULL,
  `sale_agent` varchar(10) DEFAULT NULL,
  `sale_date` datetime DEFAULT NULL,
  `fuel_charges` decimal(10,2) DEFAULT 0.00,
  `warehouse_id` int(11) DEFAULT NULL,
  `user_signature` text DEFAULT NULL,
  `is_fuelcharges_include` bit(1) DEFAULT b'0',
  `is_prepaid` bit(1) DEFAULT b'0',
  `return_label` bit(1) DEFAULT b'0',
  `finalmile_over_label` bit(1) DEFAULT b'0',
  `request_manifest_collection` bit(1) DEFAULT b'0',
  `create_pre_alert` bit(1) DEFAULT b'0',
  `is_employee` bit(1) NOT NULL DEFAULT b'0',
  `invoice_bank_details_id` int(11) DEFAULT 0,
  `check_list_account_form` bit(1) DEFAULT b'0',
  `check_list_credit_check` bit(1) DEFAULT b'0',
  `check_list_t_cs` bit(1) DEFAULT b'0',
  `check_list_tariff_agreed` bit(1) DEFAULT b'0',
  `check_list_sales_pot` bit(1) DEFAULT b'0',
  `sales_pot_time_period` int(5) DEFAULT 0,
  `sales_pot_percentage` decimal(5,2) DEFAULT 0.00,
  `last_login_date` timestamp NULL DEFAULT NULL,
  `invalid_login_count` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_updated` timestamp NULL DEFAULT NULL,
  `lock_time` timestamp NULL DEFAULT NULL,
  `opearation_manifest` bit(1) DEFAULT b'0',
  `own_tariff` bit(1) DEFAULT b'0',
  `user_warehouse` enum('NON','BIRMINGHAM','HAYES') DEFAULT 'NON',
  `api_key` varchar(100) DEFAULT NULL,
  `api_secert` varchar(100) DEFAULT NULL,
  `api_date` datetime DEFAULT NULL,
  `bagging` bit(1) DEFAULT b'0',
  `retail_customer` bit(1) DEFAULT b'0',
  `show_price` bit(1) DEFAULT b'0',
  `sales_rate` decimal(10,2) DEFAULT NULL,
  `collection_add_line_1` varchar(50) DEFAULT NULL,
  `collection_add_line_2` varchar(50) DEFAULT NULL,
  `collection_add_line_3` varchar(50) DEFAULT NULL,
  `collection_city` varchar(45) DEFAULT NULL,
  `collection_postcode` varchar(45) DEFAULT NULL,
  `collection_country` varchar(45) DEFAULT NULL,
  `theme_id` int(5) DEFAULT NULL,
  `user_code` int(11) DEFAULT NULL,
  `website_link` varchar(200) DEFAULT NULL,
  `allow_return_email` bit(1) DEFAULT b'0',
  `default_lang` varchar(10) DEFAULT 'en-GB',
  `credit_limit` decimal(10,2) DEFAULT 0.00,
  `invoice_period` enum('daily','weekly','bi-monthly','monthly') DEFAULT 'daily',
  `label_price` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `account_code` varchar(4) DEFAULT NULL,
  `paypal_email` varchar(70) DEFAULT NULL,
  `paypal_currency` varchar(20) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `paypal_client_secret` varchar(255) DEFAULT NULL,
  `alternative_email` varchar(500) DEFAULT NULL,
  `billing_email` varchar(500) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `allow_oversize` tinyint(4) NOT NULL DEFAULT 0,
  `allow_overweight` tinyint(4) NOT NULL DEFAULT 0,
  `paypal_client_id` varchar(255) DEFAULT NULL,
  `invoice_template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `send_tracking_data` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_account_service_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_account_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `consignment_charges_types_id` bigint(20) UNSIGNED NOT NULL,
  `charge` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `charge_type` enum('fixed','percentage') DEFAULT 'fixed',
  `added_by` bigint(20) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_audit` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `table_key` bigint(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `old_data` longtext DEFAULT NULL,
  `new_data` longtext DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_department` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_document` (
  `id` int(11) NOT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `added_by` bigint(20) DEFAULT NULL,
  `added_date` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(40) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(10) NOT NULL DEFAULT 'A',
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Stores activity log.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_market_places_mapping` (
  `id` bigint(20) NOT NULL,
  `market_places_id` bigint(20) NOT NULL,
  `user_account_id` bigint(20) NOT NULL,
  `auth_data` text DEFAULT NULL,
  `store_key` varchar(200) DEFAULT NULL,
  `active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_services_charges` (
  `id` int(11) NOT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `sur_charge` decimal(10,2) DEFAULT NULL,
  `sur_charge_type` enum('fixed','percentage') NOT NULL DEFAULT 'percentage',
  `extra_charge` decimal(10,2) DEFAULT NULL,
  `extra_charge_type` enum('fixed','percentage') NOT NULL DEFAULT 'percentage',
  `discount` decimal(10,2) DEFAULT NULL,
  `discount_type` enum('fixed','percentage') NOT NULL DEFAULT 'percentage',
  `additional_charges_type` enum('fixed','percentage','per_kg') DEFAULT 'per_kg',
  `additional_charges` decimal(10,2) DEFAULT NULL,
  `additional_charges_details` text DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `over_weight` decimal(10,2) DEFAULT NULL COMMENT 'Per peice charges',
  `over_size` decimal(10,2) DEFAULT NULL COMMENT 'Per peice charges',
  `over_weight_type` enum('fixed','percentage','per_pcs') DEFAULT 'per_pcs',
  `over_size_type` enum('fixed','percentage','per_pcs') DEFAULT 'per_pcs'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_services_charges_log` (
  `id` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `logdate` datetime NOT NULL,
  `ipaddress` varchar(40) NOT NULL,
  `log_id` int(11) NOT NULL,
  `log_type` varchar(10) NOT NULL DEFAULT 'A',
  `message` text DEFAULT NULL,
  `previous_data` text DEFAULT NULL,
  `current_data` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Stores activity log.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_services_routing` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `from_weight` decimal(10,2) DEFAULT NULL,
  `to_weight` decimal(10,2) DEFAULT NULL,
  `status` bit(1) NOT NULL DEFAULT b'0',
  `service_id` int(11) DEFAULT NULL,
  `is_remotearea` bit(1) NOT NULL DEFAULT b'0',
  `is_over_label` bit(1) DEFAULT b'0',
  `added_by` int(11) NOT NULL,
  `is_agreed` bit(1) DEFAULT b'0',
  `label_charges` decimal(10,2) DEFAULT 0.00,
  `is_dead_weight` bit(1) DEFAULT b'0',
  `is_over_size` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `user_shopping_platforms` (
  `id` int(11) NOT NULL,
  `shopping_platform_id` int(11) DEFAULT NULL,
  `reference` varchar(45) DEFAULT NULL,
  `site_url` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0 COMMENT '0 - for delete\n1 - for active\n2 - for inactive\n',
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `api_key` varchar(255) DEFAULT NULL,
  `api_secrete` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `vehicle` (
  `id` int(10) UNSIGNED NOT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `vehicle_make` varchar(255) DEFAULT NULL,
  `model_year` int(11) DEFAULT NULL,
  `vehicle_model` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `vehicle_color` varchar(50) DEFAULT NULL,
  `vehicle_capacity` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `vehicle_number` varchar(15) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `vehicle_driver` (
  `id` int(10) UNSIGNED NOT NULL,
  `driver_id` int(10) UNSIGNED NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `driver_start_time` time DEFAULT NULL,
  `driver_end_time` time DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_by` int(11) DEFAULT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `vehicle_parcel_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parcel_id` bigint(20) NOT NULL,
  `vehicle_id` bigint(20) DEFAULT NULL,
  `driver_id` bigint(20) DEFAULT NULL,
  `pickup_date` date NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` bigint(20) DEFAULT NULL,
  `is_active` bit(1) DEFAULT b'1',
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `warehouse` (
  `id` int(11) NOT NULL,
  `warehouse_name` varchar(50) NOT NULL,
  `addressline1` varchar(100) DEFAULT NULL,
  `addressline2` varchar(100) NOT NULL,
  `stateregion` varchar(50) NOT NULL,
  `citytown` varchar(50) DEFAULT NULL,
  `postzipcode` varchar(10) DEFAULT NULL,
  `countryid` int(11) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0,
  `added_date` datetime NOT NULL,
  `added_by` int(11) NOT NULL,
  `updated_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `hub` varchar(200) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `warehouse_code` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `warehouse_processing_time` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL COMMENT 'This field is acting as foreign key in order to get the warehouse ID. This will help to find out which service in which warehouse is taking what time to process the parcel.',
  `parcel_processing_time` int(2) DEFAULT NULL COMMENT 'This service is used to find out the parcel process time with respect to the particular warehouse.',
  `service_id` int(11) DEFAULT NULL COMMENT 'Th service_id field acts as a forign key of the service table in order to find out the service.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='This table is used to get information of the parcel processing time in any warehouse with respect to the service or carrier.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `warehouse_warehouse_ttime` (
  `id` int(11) NOT NULL,
  `from_warehouse_id` int(11) DEFAULT NULL COMMENT 'This is the id of the warehouse table from the parcel is sending.',
  `to_warehouse_id` int(11) DEFAULT NULL COMMENT 'This is the id of the warehouse table to where parcel is dispatching. ',
  `transit_time` int(2) DEFAULT NULL COMMENT 'This field we are using in order to find out the time of the parcel delivery betwee two warehouses . '
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='This is the table we are using in order to get the parcel transit time from one warehouse to other warehouse.';
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `whistl_depo_details` (
  `id` int(11) NOT NULL,
  `depo_id` varchar(45) DEFAULT NULL,
  `depo_detail` varchar(200) DEFAULT NULL,
  `depo_address` varchar(200) DEFAULT NULL,
  `from_postcode` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::connection()->getPdo()->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `yodel_hubs` (
  `id` int(11) NOT NULL,
  `pre_sort_carrier_id` int(11) DEFAULT NULL,
  `hub` varchar(100) DEFAULT NULL,
  `routing_code` varchar(45) DEFAULT NULL,
  `company` varchar(45) DEFAULT NULL,
  `contact` varchar(45) DEFAULT NULL,
  `address_line_1` varchar(45) DEFAULT NULL,
  `address_line_2` varchar(45) DEFAULT NULL,
  `address_line_3` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `country_iso_code` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SQL
            );
        } catch (\Exception $e) {
            // Ignore
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
    }
};