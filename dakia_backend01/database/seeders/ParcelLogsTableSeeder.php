<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ParcelLogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('parcel_logs')->delete();
        
        \DB::table('parcel_logs')->insert(array (
            0 => 
            array (
                'id' => 0,
                'userid' => 58,
                'logdate' => '2019-07-12 19:45:27',
                'ipaddress' => '3937179205',
                'log_id' => 139002,
                'log_type' => 'Parcel',
                'message' => 'Parcel Data is chahnged',
                'previous_data' => '',
                'current_data' => 'O:6:"Parcel":6:{s:11:"' . "\0" . '*' . "\0" . 'valArray";a:23:{s:2:"id";s:6:"139002";s:14:"consignment_id";s:6:"106792";s:15:"tracking_number";s:18:"JD0002210161145345";s:18:"do_tracking_number";s:0:"";s:6:"length";s:1:"5";s:5:"width";s:1:"5";s:6:"height";s:1:"5";s:6:"weight";s:1:"5";s:11:"description";s:0:"";s:14:"parcel_message";s:0:"";s:3:"qty";s:1:"1";s:13:"commoditycode";s:0:"";s:11:"grossweight";s:4:"0.00";s:7:"pweight";s:1:"0";s:9:"itemvalue";s:1:"0";s:11:"number_item";s:1:"0";s:9:"tarrif_no";s:0:"";s:13:"update_weight";s:4:"0.00";s:15:"owe_status_code";s:13:"label created";s:12:"chute_sorted";s:1:"0";s:18:"parcel_status_code";s:2:"13";s:12:"routing_code";s:0:"";s:20:"last_tracking_update";N;}s:21:"' . "\0" . 'DbAccess3' . "\0" . 'primaryKey";s:2:"id";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'tableName";s:6:"parcel";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'fieldList";a:41:{s:2:"id";s:6:"number";s:14:"consignment_id";s:6:"number";s:15:"tracking_number";s:6:"string";s:18:"do_tracking_number";s:6:"string";s:6:"length";s:6:"number";s:5:"width";s:6:"number";s:6:"height";s:6:"number";s:6:"weight";s:6:"number";s:11:"description";s:6:"string";s:14:"parcel_message";s:6:"string";s:3:"qty";s:6:"string";s:13:"commoditycode";s:6:"string";s:11:"grossweight";s:6:"number";s:7:"pweight";s:6:"string";s:9:"itemvalue";s:6:"string";s:11:"number_item";s:6:"number";s:9:"tarrif_no";s:6:"string";s:13:"update_weight";s:6:"number";s:15:"owe_status_code";s:6:"string";s:12:"chute_sorted";s:6:"number";s:18:"parcel_status_code";s:6:"number";s:12:"routing_code";s:6:"string";s:20:"last_tracking_update";s:8:"datetime";s:14:"courier_status";s:9:"undefined";s:6:"pieces";s:9:"undefined";s:11:"mawb_number";s:9:"undefined";s:12:"actualweight";s:9:"undefined";s:9:"bagnumber";s:9:"undefined";s:4:"name";s:9:"undefined";s:11:"countryname";s:9:"undefined";s:12:"user_account";s:9:"undefined";s:15:"group_scan_date";s:9:"undefined";s:15:"group_parcel_id";s:9:"undefined";s:4:"dims";s:9:"undefined";s:10:"first_name";s:9:"undefined";s:7:"carrier";s:9:"undefined";s:8:"palletno";s:9:"undefined";s:12:"warehouse_id";s:9:"undefined";s:11:"parcel_type";s:9:"undefined";s:10:"date_added";s:9:"undefined";s:7:"message";s:9:"undefined";}s:14:"' . "\0" . '*' . "\0" . 'modifyArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}s:8:"logArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}}',
            ),
            1 => 
            array (
                'id' => 0,
                'userid' => 58,
                'logdate' => '2019-07-12 19:45:27',
                'ipaddress' => '3937179205',
                'log_id' => 139002,
                'log_type' => 'Parcel',
                'message' => 'Parcel Data is chahnged',
                'previous_data' => '',
                'current_data' => 'O:6:"Parcel":6:{s:11:"' . "\0" . '*' . "\0" . 'valArray";a:23:{s:2:"id";s:6:"139002";s:14:"consignment_id";s:6:"106792";s:15:"tracking_number";s:18:"JD0002210161145345";s:18:"do_tracking_number";s:0:"";s:6:"length";s:1:"5";s:5:"width";s:1:"5";s:6:"height";s:1:"5";s:6:"weight";s:1:"5";s:11:"description";s:0:"";s:14:"parcel_message";s:0:"";s:3:"qty";s:1:"1";s:13:"commoditycode";s:0:"";s:11:"grossweight";s:4:"0.00";s:7:"pweight";s:1:"0";s:9:"itemvalue";s:1:"0";s:11:"number_item";s:1:"0";s:9:"tarrif_no";s:0:"";s:13:"update_weight";s:4:"0.00";s:15:"owe_status_code";s:13:"label created";s:12:"chute_sorted";s:1:"0";s:18:"parcel_status_code";s:2:"13";s:12:"routing_code";s:0:"";s:20:"last_tracking_update";N;}s:21:"' . "\0" . 'DbAccess3' . "\0" . 'primaryKey";s:2:"id";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'tableName";s:6:"parcel";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'fieldList";a:41:{s:2:"id";s:6:"number";s:14:"consignment_id";s:6:"number";s:15:"tracking_number";s:6:"string";s:18:"do_tracking_number";s:6:"string";s:6:"length";s:6:"number";s:5:"width";s:6:"number";s:6:"height";s:6:"number";s:6:"weight";s:6:"number";s:11:"description";s:6:"string";s:14:"parcel_message";s:6:"string";s:3:"qty";s:6:"string";s:13:"commoditycode";s:6:"string";s:11:"grossweight";s:6:"number";s:7:"pweight";s:6:"string";s:9:"itemvalue";s:6:"string";s:11:"number_item";s:6:"number";s:9:"tarrif_no";s:6:"string";s:13:"update_weight";s:6:"number";s:15:"owe_status_code";s:6:"string";s:12:"chute_sorted";s:6:"number";s:18:"parcel_status_code";s:6:"number";s:12:"routing_code";s:6:"string";s:20:"last_tracking_update";s:8:"datetime";s:14:"courier_status";s:9:"undefined";s:6:"pieces";s:9:"undefined";s:11:"mawb_number";s:9:"undefined";s:12:"actualweight";s:9:"undefined";s:9:"bagnumber";s:9:"undefined";s:4:"name";s:9:"undefined";s:11:"countryname";s:9:"undefined";s:12:"user_account";s:9:"undefined";s:15:"group_scan_date";s:9:"undefined";s:15:"group_parcel_id";s:9:"undefined";s:4:"dims";s:9:"undefined";s:10:"first_name";s:9:"undefined";s:7:"carrier";s:9:"undefined";s:8:"palletno";s:9:"undefined";s:12:"warehouse_id";s:9:"undefined";s:11:"parcel_type";s:9:"undefined";s:10:"date_added";s:9:"undefined";s:7:"message";s:9:"undefined";}s:14:"' . "\0" . '*' . "\0" . 'modifyArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}s:8:"logArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}}',
            ),
            2 => 
            array (
                'id' => 0,
                'userid' => 58,
                'logdate' => '2019-07-12 19:45:27',
                'ipaddress' => '3937179205',
                'log_id' => 139002,
                'log_type' => 'Parcel',
                'message' => 'Parcel Data is chahnged',
                'previous_data' => '',
                'current_data' => 'O:6:"Parcel":6:{s:11:"' . "\0" . '*' . "\0" . 'valArray";a:23:{s:2:"id";s:6:"139002";s:14:"consignment_id";s:6:"106792";s:15:"tracking_number";s:18:"JD0002210161145345";s:18:"do_tracking_number";s:0:"";s:6:"length";s:1:"5";s:5:"width";s:1:"5";s:6:"height";s:1:"5";s:6:"weight";s:1:"5";s:11:"description";s:0:"";s:14:"parcel_message";s:0:"";s:3:"qty";s:1:"1";s:13:"commoditycode";s:0:"";s:11:"grossweight";s:4:"0.00";s:7:"pweight";s:1:"0";s:9:"itemvalue";s:1:"0";s:11:"number_item";s:1:"0";s:9:"tarrif_no";s:0:"";s:13:"update_weight";s:4:"0.00";s:15:"owe_status_code";s:13:"label created";s:12:"chute_sorted";s:1:"0";s:18:"parcel_status_code";s:2:"13";s:12:"routing_code";s:0:"";s:20:"last_tracking_update";N;}s:21:"' . "\0" . 'DbAccess3' . "\0" . 'primaryKey";s:2:"id";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'tableName";s:6:"parcel";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'fieldList";a:41:{s:2:"id";s:6:"number";s:14:"consignment_id";s:6:"number";s:15:"tracking_number";s:6:"string";s:18:"do_tracking_number";s:6:"string";s:6:"length";s:6:"number";s:5:"width";s:6:"number";s:6:"height";s:6:"number";s:6:"weight";s:6:"number";s:11:"description";s:6:"string";s:14:"parcel_message";s:6:"string";s:3:"qty";s:6:"string";s:13:"commoditycode";s:6:"string";s:11:"grossweight";s:6:"number";s:7:"pweight";s:6:"string";s:9:"itemvalue";s:6:"string";s:11:"number_item";s:6:"number";s:9:"tarrif_no";s:6:"string";s:13:"update_weight";s:6:"number";s:15:"owe_status_code";s:6:"string";s:12:"chute_sorted";s:6:"number";s:18:"parcel_status_code";s:6:"number";s:12:"routing_code";s:6:"string";s:20:"last_tracking_update";s:8:"datetime";s:14:"courier_status";s:9:"undefined";s:6:"pieces";s:9:"undefined";s:11:"mawb_number";s:9:"undefined";s:12:"actualweight";s:9:"undefined";s:9:"bagnumber";s:9:"undefined";s:4:"name";s:9:"undefined";s:11:"countryname";s:9:"undefined";s:12:"user_account";s:9:"undefined";s:15:"group_scan_date";s:9:"undefined";s:15:"group_parcel_id";s:9:"undefined";s:4:"dims";s:9:"undefined";s:10:"first_name";s:9:"undefined";s:7:"carrier";s:9:"undefined";s:8:"palletno";s:9:"undefined";s:12:"warehouse_id";s:9:"undefined";s:11:"parcel_type";s:9:"undefined";s:10:"date_added";s:9:"undefined";s:7:"message";s:9:"undefined";}s:14:"' . "\0" . '*' . "\0" . 'modifyArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}s:8:"logArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}}',
            ),
            3 => 
            array (
                'id' => 0,
                'userid' => 58,
                'logdate' => '2019-07-12 19:45:27',
                'ipaddress' => '3937179205',
                'log_id' => 139002,
                'log_type' => 'Parcel',
                'message' => 'Parcel Data is chahnged',
                'previous_data' => '',
                'current_data' => 'O:6:"Parcel":6:{s:11:"' . "\0" . '*' . "\0" . 'valArray";a:23:{s:2:"id";s:6:"139002";s:14:"consignment_id";s:6:"106792";s:15:"tracking_number";s:18:"JD0002210161145345";s:18:"do_tracking_number";s:0:"";s:6:"length";s:1:"5";s:5:"width";s:1:"5";s:6:"height";s:1:"5";s:6:"weight";s:1:"5";s:11:"description";s:0:"";s:14:"parcel_message";s:0:"";s:3:"qty";s:1:"1";s:13:"commoditycode";s:0:"";s:11:"grossweight";s:4:"0.00";s:7:"pweight";s:1:"0";s:9:"itemvalue";s:1:"0";s:11:"number_item";s:1:"0";s:9:"tarrif_no";s:0:"";s:13:"update_weight";s:4:"0.00";s:15:"owe_status_code";s:13:"label created";s:12:"chute_sorted";s:1:"0";s:18:"parcel_status_code";s:2:"13";s:12:"routing_code";s:0:"";s:20:"last_tracking_update";N;}s:21:"' . "\0" . 'DbAccess3' . "\0" . 'primaryKey";s:2:"id";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'tableName";s:6:"parcel";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'fieldList";a:41:{s:2:"id";s:6:"number";s:14:"consignment_id";s:6:"number";s:15:"tracking_number";s:6:"string";s:18:"do_tracking_number";s:6:"string";s:6:"length";s:6:"number";s:5:"width";s:6:"number";s:6:"height";s:6:"number";s:6:"weight";s:6:"number";s:11:"description";s:6:"string";s:14:"parcel_message";s:6:"string";s:3:"qty";s:6:"string";s:13:"commoditycode";s:6:"string";s:11:"grossweight";s:6:"number";s:7:"pweight";s:6:"string";s:9:"itemvalue";s:6:"string";s:11:"number_item";s:6:"number";s:9:"tarrif_no";s:6:"string";s:13:"update_weight";s:6:"number";s:15:"owe_status_code";s:6:"string";s:12:"chute_sorted";s:6:"number";s:18:"parcel_status_code";s:6:"number";s:12:"routing_code";s:6:"string";s:20:"last_tracking_update";s:8:"datetime";s:14:"courier_status";s:9:"undefined";s:6:"pieces";s:9:"undefined";s:11:"mawb_number";s:9:"undefined";s:12:"actualweight";s:9:"undefined";s:9:"bagnumber";s:9:"undefined";s:4:"name";s:9:"undefined";s:11:"countryname";s:9:"undefined";s:12:"user_account";s:9:"undefined";s:15:"group_scan_date";s:9:"undefined";s:15:"group_parcel_id";s:9:"undefined";s:4:"dims";s:9:"undefined";s:10:"first_name";s:9:"undefined";s:7:"carrier";s:9:"undefined";s:8:"palletno";s:9:"undefined";s:12:"warehouse_id";s:9:"undefined";s:11:"parcel_type";s:9:"undefined";s:10:"date_added";s:9:"undefined";s:7:"message";s:9:"undefined";}s:14:"' . "\0" . '*' . "\0" . 'modifyArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}s:8:"logArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}}',
            ),
            4 => 
            array (
                'id' => 0,
                'userid' => 58,
                'logdate' => '2019-07-12 19:45:27',
                'ipaddress' => '3937179205',
                'log_id' => 139002,
                'log_type' => 'Parcel',
                'message' => 'Parcel Data is chahnged',
                'previous_data' => '',
                'current_data' => 'O:6:"Parcel":6:{s:11:"' . "\0" . '*' . "\0" . 'valArray";a:23:{s:2:"id";s:6:"139002";s:14:"consignment_id";s:6:"106792";s:15:"tracking_number";s:18:"JD0002210161145345";s:18:"do_tracking_number";s:0:"";s:6:"length";s:1:"5";s:5:"width";s:1:"5";s:6:"height";s:1:"5";s:6:"weight";s:1:"5";s:11:"description";s:0:"";s:14:"parcel_message";s:0:"";s:3:"qty";s:1:"1";s:13:"commoditycode";s:0:"";s:11:"grossweight";s:4:"0.00";s:7:"pweight";s:1:"0";s:9:"itemvalue";s:1:"0";s:11:"number_item";s:1:"0";s:9:"tarrif_no";s:0:"";s:13:"update_weight";s:4:"0.00";s:15:"owe_status_code";s:13:"label created";s:12:"chute_sorted";s:1:"0";s:18:"parcel_status_code";s:2:"13";s:12:"routing_code";s:0:"";s:20:"last_tracking_update";N;}s:21:"' . "\0" . 'DbAccess3' . "\0" . 'primaryKey";s:2:"id";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'tableName";s:6:"parcel";s:20:"' . "\0" . 'DbAccess3' . "\0" . 'fieldList";a:41:{s:2:"id";s:6:"number";s:14:"consignment_id";s:6:"number";s:15:"tracking_number";s:6:"string";s:18:"do_tracking_number";s:6:"string";s:6:"length";s:6:"number";s:5:"width";s:6:"number";s:6:"height";s:6:"number";s:6:"weight";s:6:"number";s:11:"description";s:6:"string";s:14:"parcel_message";s:6:"string";s:3:"qty";s:6:"string";s:13:"commoditycode";s:6:"string";s:11:"grossweight";s:6:"number";s:7:"pweight";s:6:"string";s:9:"itemvalue";s:6:"string";s:11:"number_item";s:6:"number";s:9:"tarrif_no";s:6:"string";s:13:"update_weight";s:6:"number";s:15:"owe_status_code";s:6:"string";s:12:"chute_sorted";s:6:"number";s:18:"parcel_status_code";s:6:"number";s:12:"routing_code";s:6:"string";s:20:"last_tracking_update";s:8:"datetime";s:14:"courier_status";s:9:"undefined";s:6:"pieces";s:9:"undefined";s:11:"mawb_number";s:9:"undefined";s:12:"actualweight";s:9:"undefined";s:9:"bagnumber";s:9:"undefined";s:4:"name";s:9:"undefined";s:11:"countryname";s:9:"undefined";s:12:"user_account";s:9:"undefined";s:15:"group_scan_date";s:9:"undefined";s:15:"group_parcel_id";s:9:"undefined";s:4:"dims";s:9:"undefined";s:10:"first_name";s:9:"undefined";s:7:"carrier";s:9:"undefined";s:8:"palletno";s:9:"undefined";s:12:"warehouse_id";s:9:"undefined";s:11:"parcel_type";s:9:"undefined";s:10:"date_added";s:9:"undefined";s:7:"message";s:9:"undefined";}s:14:"' . "\0" . '*' . "\0" . 'modifyArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}s:8:"logArray";a:4:{s:5:"width";s:5:"width";s:6:"height";s:6:"height";s:6:"weight";s:6:"weight";s:6:"length";s:6:"length";}}',
            ),
        ));
        
        
    }
}