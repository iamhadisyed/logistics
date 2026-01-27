<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReportCustomizeSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('report_customize_settings')->delete();
        
        \DB::table('report_customize_settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'account_id' => 148,
                'report_title' => 'Royal mail Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-14 19:11:11',
                'added_by' => 58,
                'date_updated' => '2018-11-14 19:11:11',
                'updated_by' => 58,
            ),
            1 => 
            array (
                'id' => 2,
                'account_id' => 2234,
                'report_title' => 'TAHIR',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:9:{i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 16:23:45',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 16:23:45',
                'updated_by' => 2082,
            ),
            2 => 
            array (
                'id' => 3,
                'account_id' => 2234,
                'report_title' => 'Boss1',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:10:05',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:10:05',
                'updated_by' => 2082,
            ),
            3 => 
            array (
                'id' => 4,
                'account_id' => 2234,
                'report_title' => 'Boss',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:09:39',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:09:39',
                'updated_by' => 2082,
            ),
            4 => 
            array (
                'id' => 5,
                'account_id' => 148,
                'report_title' => 'danish test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:7:{i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}}',
                'date_added' => '2018-11-15 16:42:28',
                'added_by' => 58,
                'date_updated' => '2018-11-15 16:42:28',
                'updated_by' => 58,
            ),
            5 => 
            array (
                'id' => 6,
                'account_id' => 148,
                'report_title' => 'Boss2',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:45:02',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:45:02',
                'updated_by' => 58,
            ),
            6 => 
            array (
                'id' => 7,
                'account_id' => 148,
                'report_title' => 'Test Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:48:41',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:48:41',
                'updated_by' => 58,
            ),
            7 => 
            array (
                'id' => 8,
                'account_id' => 2234,
                'report_title' => 'gordo',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:11:20',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:11:20',
                'updated_by' => 2082,
            ),
            8 => 
            array (
                'id' => 9,
                'account_id' => 148,
                'report_title' => 'Usman Test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:17:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-10 10:11:32',
                'added_by' => 58,
                'date_updated' => '2019-01-10 10:11:32',
                'updated_by' => 58,
            ),
            9 => 
            array (
                'id' => 10,
                'account_id' => 2294,
                'report_title' => 'Parent Template Left',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:12:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-11 12:55:55',
                'added_by' => 2181,
                'date_updated' => '2019-01-11 12:55:55',
                'updated_by' => 2181,
            ),
            10 => 
            array (
                'id' => 1,
                'account_id' => 148,
                'report_title' => 'Royal mail Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-14 19:11:11',
                'added_by' => 58,
                'date_updated' => '2018-11-14 19:11:11',
                'updated_by' => 58,
            ),
            11 => 
            array (
                'id' => 2,
                'account_id' => 2234,
                'report_title' => 'TAHIR',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:9:{i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 16:23:45',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 16:23:45',
                'updated_by' => 2082,
            ),
            12 => 
            array (
                'id' => 3,
                'account_id' => 2234,
                'report_title' => 'Boss1',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:10:05',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:10:05',
                'updated_by' => 2082,
            ),
            13 => 
            array (
                'id' => 4,
                'account_id' => 2234,
                'report_title' => 'Boss',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:09:39',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:09:39',
                'updated_by' => 2082,
            ),
            14 => 
            array (
                'id' => 5,
                'account_id' => 148,
                'report_title' => 'danish test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:7:{i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}}',
                'date_added' => '2018-11-15 16:42:28',
                'added_by' => 58,
                'date_updated' => '2018-11-15 16:42:28',
                'updated_by' => 58,
            ),
            15 => 
            array (
                'id' => 6,
                'account_id' => 148,
                'report_title' => 'Boss2',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:45:02',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:45:02',
                'updated_by' => 58,
            ),
            16 => 
            array (
                'id' => 7,
                'account_id' => 148,
                'report_title' => 'Test Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:48:41',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:48:41',
                'updated_by' => 58,
            ),
            17 => 
            array (
                'id' => 8,
                'account_id' => 2234,
                'report_title' => 'gordo',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:11:20',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:11:20',
                'updated_by' => 2082,
            ),
            18 => 
            array (
                'id' => 9,
                'account_id' => 148,
                'report_title' => 'Usman Test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:17:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-10 10:11:32',
                'added_by' => 58,
                'date_updated' => '2019-01-10 10:11:32',
                'updated_by' => 58,
            ),
            19 => 
            array (
                'id' => 10,
                'account_id' => 2294,
                'report_title' => 'Parent Template Left',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:12:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-11 12:55:55',
                'added_by' => 2181,
                'date_updated' => '2019-01-11 12:55:55',
                'updated_by' => 2181,
            ),
            20 => 
            array (
                'id' => 1,
                'account_id' => 148,
                'report_title' => 'Royal mail Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-14 19:11:11',
                'added_by' => 58,
                'date_updated' => '2018-11-14 19:11:11',
                'updated_by' => 58,
            ),
            21 => 
            array (
                'id' => 2,
                'account_id' => 2234,
                'report_title' => 'TAHIR',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:9:{i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 16:23:45',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 16:23:45',
                'updated_by' => 2082,
            ),
            22 => 
            array (
                'id' => 3,
                'account_id' => 2234,
                'report_title' => 'Boss1',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:10:05',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:10:05',
                'updated_by' => 2082,
            ),
            23 => 
            array (
                'id' => 4,
                'account_id' => 2234,
                'report_title' => 'Boss',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:09:39',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:09:39',
                'updated_by' => 2082,
            ),
            24 => 
            array (
                'id' => 5,
                'account_id' => 148,
                'report_title' => 'danish test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:7:{i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}}',
                'date_added' => '2018-11-15 16:42:28',
                'added_by' => 58,
                'date_updated' => '2018-11-15 16:42:28',
                'updated_by' => 58,
            ),
            25 => 
            array (
                'id' => 6,
                'account_id' => 148,
                'report_title' => 'Boss2',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:45:02',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:45:02',
                'updated_by' => 58,
            ),
            26 => 
            array (
                'id' => 7,
                'account_id' => 148,
                'report_title' => 'Test Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:48:41',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:48:41',
                'updated_by' => 58,
            ),
            27 => 
            array (
                'id' => 8,
                'account_id' => 2234,
                'report_title' => 'gordo',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:11:20',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:11:20',
                'updated_by' => 2082,
            ),
            28 => 
            array (
                'id' => 9,
                'account_id' => 148,
                'report_title' => 'Usman Test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:17:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-10 10:11:32',
                'added_by' => 58,
                'date_updated' => '2019-01-10 10:11:32',
                'updated_by' => 58,
            ),
            29 => 
            array (
                'id' => 10,
                'account_id' => 2294,
                'report_title' => 'Parent Template Left',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:12:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-11 12:55:55',
                'added_by' => 2181,
                'date_updated' => '2019-01-11 12:55:55',
                'updated_by' => 2181,
            ),
            30 => 
            array (
                'id' => 1,
                'account_id' => 148,
                'report_title' => 'Royal mail Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-14 19:11:11',
                'added_by' => 58,
                'date_updated' => '2018-11-14 19:11:11',
                'updated_by' => 58,
            ),
            31 => 
            array (
                'id' => 2,
                'account_id' => 2234,
                'report_title' => 'TAHIR',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:9:{i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 16:23:45',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 16:23:45',
                'updated_by' => 2082,
            ),
            32 => 
            array (
                'id' => 3,
                'account_id' => 2234,
                'report_title' => 'Boss1',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:10:05',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:10:05',
                'updated_by' => 2082,
            ),
            33 => 
            array (
                'id' => 4,
                'account_id' => 2234,
                'report_title' => 'Boss',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:09:39',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:09:39',
                'updated_by' => 2082,
            ),
            34 => 
            array (
                'id' => 5,
                'account_id' => 148,
                'report_title' => 'danish test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:7:{i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}}',
                'date_added' => '2018-11-15 16:42:28',
                'added_by' => 58,
                'date_updated' => '2018-11-15 16:42:28',
                'updated_by' => 58,
            ),
            35 => 
            array (
                'id' => 6,
                'account_id' => 148,
                'report_title' => 'Boss2',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:45:02',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:45:02',
                'updated_by' => 58,
            ),
            36 => 
            array (
                'id' => 7,
                'account_id' => 148,
                'report_title' => 'Test Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:48:41',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:48:41',
                'updated_by' => 58,
            ),
            37 => 
            array (
                'id' => 8,
                'account_id' => 2234,
                'report_title' => 'gordo',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:11:20',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:11:20',
                'updated_by' => 2082,
            ),
            38 => 
            array (
                'id' => 9,
                'account_id' => 148,
                'report_title' => 'Usman Test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:17:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-10 10:11:32',
                'added_by' => 58,
                'date_updated' => '2019-01-10 10:11:32',
                'updated_by' => 58,
            ),
            39 => 
            array (
                'id' => 10,
                'account_id' => 2294,
                'report_title' => 'Parent Template Left',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:12:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-11 12:55:55',
                'added_by' => 2181,
                'date_updated' => '2019-01-11 12:55:55',
                'updated_by' => 2181,
            ),
            40 => 
            array (
                'id' => 1,
                'account_id' => 148,
                'report_title' => 'Royal mail Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-14 19:11:11',
                'added_by' => 58,
                'date_updated' => '2018-11-14 19:11:11',
                'updated_by' => 58,
            ),
            41 => 
            array (
                'id' => 2,
                'account_id' => 2234,
                'report_title' => 'TAHIR',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:9:{i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 16:23:45',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 16:23:45',
                'updated_by' => 2082,
            ),
            42 => 
            array (
                'id' => 3,
                'account_id' => 2234,
                'report_title' => 'Boss1',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:10:05',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:10:05',
                'updated_by' => 2082,
            ),
            43 => 
            array (
                'id' => 4,
                'account_id' => 2234,
                'report_title' => 'Boss',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:09:39',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:09:39',
                'updated_by' => 2082,
            ),
            44 => 
            array (
                'id' => 5,
                'account_id' => 148,
                'report_title' => 'danish test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:7:{i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}}',
                'date_added' => '2018-11-15 16:42:28',
                'added_by' => 58,
                'date_updated' => '2018-11-15 16:42:28',
                'updated_by' => 58,
            ),
            45 => 
            array (
                'id' => 6,
                'account_id' => 148,
                'report_title' => 'Boss2',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:21:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:45:02',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:45:02',
                'updated_by' => 58,
            ),
            46 => 
            array (
                'id' => 7,
                'account_id' => 148,
                'report_title' => 'Test Template',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:1;a:2:{s:3:"key";s:15:"tracking_number";s:5:"title";s:15:"Tracking Number";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 17:48:41',
                'added_by' => 58,
                'date_updated' => '2018-11-15 17:48:41',
                'updated_by' => 58,
            ),
            47 => 
            array (
                'id' => 8,
                'account_id' => 2234,
                'report_title' => 'gordo',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:15:{i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:7;a:2:{s:3:"key";s:17:"volumetric_weight";s:5:"title";s:17:"Volumetric Weight";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:9;a:2:{s:3:"key";s:5:"lxwxh";s:5:"title";s:5:"LXWXH";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:11;a:2:{s:3:"key";s:6:"status";s:5:"title";s:6:"Status";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2018-11-15 18:11:20',
                'added_by' => 2082,
                'date_updated' => '2018-11-15 18:11:20',
                'updated_by' => 2082,
            ),
            48 => 
            array (
                'id' => 9,
                'account_id' => 148,
                'report_title' => 'Usman Test',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:17:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:3;a:2:{s:3:"key";s:7:"service";s:5:"title";s:7:"Service";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:5;a:2:{s:3:"key";s:7:"country";s:5:"title";s:7:"Country";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:15;a:2:{s:3:"key";s:40:"total_no_of_days_booking_to_hub_received";s:5:"title";s:42:"Total No of Days (Booking To Hub Received)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:17;a:2:{s:3:"key";s:52:"total_no_of_days_from_carrier_received_calendar_days";s:5:"title";s:54:"Total No of Days From Carrier Received (Calendar Days)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:19;a:2:{s:3:"key";s:32:"total_transit_time_calendar_days";s:5:"title";s:34:"Total Transit Time (Calendar Days)";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-10 10:11:32',
                'added_by' => 58,
                'date_updated' => '2019-01-10 10:11:32',
                'updated_by' => 58,
            ),
            49 => 
            array (
                'id' => 10,
                'account_id' => 2294,
                'report_title' => 'Parent Template Left',
                'report_key' => 'tracking_status_report',
            'fields_data' => 'a:12:{i:0;a:2:{s:3:"key";s:4:"date";s:5:"title";s:4:"Date";}i:2;a:2:{s:3:"key";s:4:"hawb";s:5:"title";s:4:"HAWB";}i:4;a:2:{s:3:"key";s:4:"city";s:5:"title";s:4:"City";}i:6;a:2:{s:3:"key";s:6:"weight";s:5:"title";s:10:"Weight(Kg)";}i:8;a:2:{s:3:"key";s:16:"volumetric_liter";s:5:"title";s:16:"Volumetric Liter";}i:10;a:2:{s:3:"key";s:24:"last_event_tracking_date";s:5:"title";s:24:"Last Event Tracking Date";}i:12;a:2:{s:3:"key";s:15:"tracking_detail";s:5:"title";s:15:"Tracking Detail";}i:13;a:2:{s:3:"key";s:16:"delivery_on_time";s:5:"title";s:16:"Delivery On Time";}i:14;a:2:{s:3:"key";s:25:"delivery_aim_working_days";s:5:"title";s:27:"Delivery Aim (Working Days)";}i:16;a:2:{s:3:"key";s:49:"total_no_of_days_hub_received_to_carrier_received";s:5:"title";s:52:"Total No of Days  (Hub Received to carrier received)";}i:18;a:2:{s:3:"key";s:46:"total_no_of_working_days_from_carrier_received";s:5:"title";s:46:"Total No of Working Days From Carrier Received";}i:20;a:2:{s:3:"key";s:31:"total_transit_time_working_days";s:5:"title";s:33:"Total Transit Time (Working Days)";}}',
                'date_added' => '2019-01-11 12:55:55',
                'added_by' => 2181,
                'date_updated' => '2019-01-11 12:55:55',
                'updated_by' => 2181,
            ),
        ));
        
        
    }
}