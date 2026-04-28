<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemDetailsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('item_details')->delete();
        
        \DB::table('item_details')->insert(array (
            0 => 
            array (
                'id' => 12,
                'consignment_id' => 170278,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"13232","manufacture_country_iso":"AU"}]',
                'user_id' => 58,
            ),
            1 => 
            array (
                'id' => 14,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.ecom","item_sku":"23423","no_of_items":"11","item_value":"1","weight":"1","tariff_no":"","hscode":"132345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            2 => 
            array (
                'id' => 15,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            3 => 
            array (
                'id' => 17,
                'consignment_id' => 170275,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            4 => 
            array (
                'id' => 18,
                'consignment_id' => 170274,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            5 => 
            array (
                'id' => 19,
                'consignment_id' => 170273,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            6 => 
            array (
                'id' => 20,
                'consignment_id' => 170272,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            7 => 
            array (
                'id' => 21,
                'consignment_id' => 170271,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            8 => 
            array (
                'id' => 23,
                'consignment_id' => 170268,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            9 => 
            array (
                'id' => 24,
                'consignment_id' => 170270,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            10 => 
            array (
                'id' => 25,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            11 => 
            array (
                'id' => 26,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            12 => 
            array (
                'id' => 27,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            13 => 
            array (
                'id' => 28,
                'consignment_id' => 170280,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            14 => 
            array (
                'id' => 29,
                'consignment_id' => 170281,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            15 => 
            array (
                'id' => 30,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            16 => 
            array (
                'id' => 31,
                'consignment_id' => 170283,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            17 => 
            array (
                'id' => 32,
                'consignment_id' => 170284,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            18 => 
            array (
                'id' => 33,
                'consignment_id' => 170212,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            19 => 
            array (
                'id' => 34,
                'consignment_id' => 170213,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            20 => 
            array (
                'id' => 35,
                'consignment_id' => 170287,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            21 => 
            array (
                'id' => 36,
                'consignment_id' => 170288,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            22 => 
            array (
                'id' => 37,
                'consignment_id' => 170289,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            23 => 
            array (
                'id' => 38,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            24 => 
            array (
                'id' => 39,
                'consignment_id' => 170291,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            25 => 
            array (
                'id' => 40,
                'consignment_id' => 170292,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            26 => 
            array (
                'id' => 41,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            27 => 
            array (
                'id' => 42,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            28 => 
            array (
                'id' => 43,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            29 => 
            array (
                'id' => 44,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            30 => 
            array (
                'id' => 45,
                'consignment_id' => 170296,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            31 => 
            array (
                'id' => 50,
                'consignment_id' => 0,
                'session_id' => '9b478d41c9eef15d449460b0d1705c83',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"343","no_of_items":"33","item_value":"3","weight":"3","tariff_no":"","hscode":"333","manufacture_country_iso":"AI"}]',
                'user_id' => 58,
            ),
            32 => 
            array (
                'id' => 52,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.go.com","item_sku":"324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            33 => 
            array (
                'id' => 61,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdfa.com","item_sku":"2423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            34 => 
            array (
                'id' => 62,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2334,
            ),
            35 => 
            array (
                'id' => 63,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"http:\\/\\/www.asdfa.com","item_sku":"123213","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            36 => 
            array (
                'id' => 64,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            37 => 
            array (
                'id' => 65,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            38 => 
            array (
                'id' => 66,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            39 => 
            array (
                'id' => 67,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            40 => 
            array (
                'id' => 68,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            41 => 
            array (
                'id' => 69,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            42 => 
            array (
                'id' => 70,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            43 => 
            array (
                'id' => 71,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            44 => 
            array (
                'id' => 72,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            45 => 
            array (
                'id' => 73,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            46 => 
            array (
                'id' => 74,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            47 => 
            array (
                'id' => 75,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            48 => 
            array (
                'id' => 76,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            49 => 
            array (
                'id' => 77,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            50 => 
            array (
                'id' => 78,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            51 => 
            array (
                'id' => 79,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            52 => 
            array (
                'id' => 80,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            53 => 
            array (
                'id' => 81,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            54 => 
            array (
                'id' => 82,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            55 => 
            array (
                'id' => 83,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            56 => 
            array (
                'id' => 84,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            57 => 
            array (
                'id' => 85,
                'consignment_id' => 170308,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            58 => 
            array (
                'id' => 86,
                'consignment_id' => 170309,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            59 => 
            array (
                'id' => 87,
                'consignment_id' => 170310,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            60 => 
            array (
                'id' => 88,
                'consignment_id' => 170311,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.adf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            61 => 
            array (
                'id' => 89,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            62 => 
            array (
                'id' => 90,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            63 => 
            array (
                'id' => 91,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            64 => 
            array (
                'id' => 92,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            65 => 
            array (
                'id' => 93,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            66 => 
            array (
                'id' => 94,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            67 => 
            array (
                'id' => 95,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            68 => 
            array (
                'id' => 96,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            69 => 
            array (
                'id' => 97,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            70 => 
            array (
                'id' => 98,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            71 => 
            array (
                'id' => 99,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            72 => 
            array (
                'id' => 100,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            73 => 
            array (
                'id' => 101,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            74 => 
            array (
                'id' => 102,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            75 => 
            array (
                'id' => 103,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            76 => 
            array (
                'id' => 104,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            77 => 
            array (
                'id' => 105,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            78 => 
            array (
                'id' => 106,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            79 => 
            array (
                'id' => 107,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            80 => 
            array (
                'id' => 108,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            81 => 
            array (
                'id' => 109,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            82 => 
            array (
                'id' => 110,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            83 => 
            array (
                'id' => 111,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            84 => 
            array (
                'id' => 112,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            85 => 
            array (
                'id' => 113,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            86 => 
            array (
                'id' => 114,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            87 => 
            array (
                'id' => 115,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            88 => 
            array (
                'id' => 116,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            89 => 
            array (
                'id' => 117,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            90 => 
            array (
                'id' => 118,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            91 => 
            array (
                'id' => 119,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            92 => 
            array (
                'id' => 120,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            93 => 
            array (
                'id' => 121,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            94 => 
            array (
                'id' => 122,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            95 => 
            array (
                'id' => 123,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            96 => 
            array (
                'id' => 124,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            97 => 
            array (
                'id' => 125,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            98 => 
            array (
                'id' => 126,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            99 => 
            array (
                'id' => 127,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            100 => 
            array (
                'id' => 128,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            101 => 
            array (
                'id' => 129,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            102 => 
            array (
                'id' => 130,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            103 => 
            array (
                'id' => 131,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            104 => 
            array (
                'id' => 132,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            105 => 
            array (
                'id' => 133,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            106 => 
            array (
                'id' => 134,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            107 => 
            array (
                'id' => 135,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            108 => 
            array (
                'id' => 136,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            109 => 
            array (
                'id' => 137,
                'consignment_id' => 170336,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdf.com","item_sku":"23434","no_of_items":"2","item_value":"2","weight":"2","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            110 => 
            array (
                'id' => 138,
                'consignment_id' => 170337,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            111 => 
            array (
                'id' => 139,
                'consignment_id' => 170338,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"","item_sku":"324234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            112 => 
            array (
                'id' => 140,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            113 => 
            array (
                'id' => 141,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            114 => 
            array (
                'id' => 142,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            115 => 
            array (
                'id' => 143,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            116 => 
            array (
                'id' => 144,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            117 => 
            array (
                'id' => 145,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            118 => 
            array (
                'id' => 146,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            119 => 
            array (
                'id' => 147,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            120 => 
            array (
                'id' => 148,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            121 => 
            array (
                'id' => 149,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            122 => 
            array (
                'id' => 150,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            123 => 
            array (
                'id' => 151,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            124 => 
            array (
                'id' => 152,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            125 => 
            array (
                'id' => 153,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            126 => 
            array (
                'id' => 154,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            127 => 
            array (
                'id' => 155,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            128 => 
            array (
                'id' => 156,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            129 => 
            array (
                'id' => 157,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            130 => 
            array (
                'id' => 158,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            131 => 
            array (
                'id' => 159,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            132 => 
            array (
                'id' => 160,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            133 => 
            array (
                'id' => 161,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            134 => 
            array (
                'id' => 162,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            135 => 
            array (
                'id' => 163,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            136 => 
            array (
                'id' => 164,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            137 => 
            array (
                'id' => 165,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            138 => 
            array (
                'id' => 166,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            139 => 
            array (
                'id' => 167,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            140 => 
            array (
                'id' => 168,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            141 => 
            array (
                'id' => 169,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            142 => 
            array (
                'id' => 170,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            143 => 
            array (
                'id' => 171,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            144 => 
            array (
                'id' => 172,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            145 => 
            array (
                'id' => 173,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            146 => 
            array (
                'id' => 174,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            147 => 
            array (
                'id' => 175,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            148 => 
            array (
                'id' => 176,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            149 => 
            array (
                'id' => 177,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            150 => 
            array (
                'id' => 178,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            151 => 
            array (
                'id' => 179,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            152 => 
            array (
                'id' => 180,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            153 => 
            array (
                'id' => 181,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            154 => 
            array (
                'id' => 182,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            155 => 
            array (
                'id' => 183,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            156 => 
            array (
                'id' => 184,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            157 => 
            array (
                'id' => 185,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            158 => 
            array (
                'id' => 186,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            159 => 
            array (
                'id' => 187,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            160 => 
            array (
                'id' => 188,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            161 => 
            array (
                'id' => 189,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            162 => 
            array (
                'id' => 190,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            163 => 
            array (
                'id' => 191,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            164 => 
            array (
                'id' => 192,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            165 => 
            array (
                'id' => 193,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            166 => 
            array (
                'id' => 194,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            167 => 
            array (
                'id' => 195,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            168 => 
            array (
                'id' => 196,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            169 => 
            array (
                'id' => 197,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            170 => 
            array (
                'id' => 198,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            171 => 
            array (
                'id' => 199,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            172 => 
            array (
                'id' => 200,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            173 => 
            array (
                'id' => 201,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            174 => 
            array (
                'id' => 202,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            175 => 
            array (
                'id' => 203,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            176 => 
            array (
                'id' => 204,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            177 => 
            array (
                'id' => 205,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            178 => 
            array (
                'id' => 206,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            179 => 
            array (
                'id' => 207,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            180 => 
            array (
                'id' => 208,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            181 => 
            array (
                'id' => 209,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            182 => 
            array (
                'id' => 210,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            183 => 
            array (
                'id' => 211,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            184 => 
            array (
                'id' => 212,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            185 => 
            array (
                'id' => 213,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            186 => 
            array (
                'id' => 214,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            187 => 
            array (
                'id' => 215,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            188 => 
            array (
                'id' => 216,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            189 => 
            array (
                'id' => 217,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            190 => 
            array (
                'id' => 218,
                'consignment_id' => 170378,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            191 => 
            array (
                'id' => 219,
                'consignment_id' => 170379,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            192 => 
            array (
                'id' => 220,
                'consignment_id' => 170380,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            193 => 
            array (
                'id' => 221,
                'consignment_id' => 170381,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            194 => 
            array (
                'id' => 222,
                'consignment_id' => 170382,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            195 => 
            array (
                'id' => 223,
                'consignment_id' => 170383,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            196 => 
            array (
                'id' => 224,
                'consignment_id' => 170384,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            197 => 
            array (
                'id' => 225,
                'consignment_id' => 170385,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            198 => 
            array (
                'id' => 226,
                'consignment_id' => 170386,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            199 => 
            array (
                'id' => 227,
                'consignment_id' => 170387,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            200 => 
            array (
                'id' => 228,
                'consignment_id' => 170388,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            201 => 
            array (
                'id' => 229,
                'consignment_id' => 170389,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            202 => 
            array (
                'id' => 230,
                'consignment_id' => 170390,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            203 => 
            array (
                'id' => 231,
                'consignment_id' => 170391,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            204 => 
            array (
                'id' => 232,
                'consignment_id' => 170392,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            205 => 
            array (
                'id' => 233,
                'consignment_id' => 170393,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            206 => 
            array (
                'id' => 234,
                'consignment_id' => 170394,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            207 => 
            array (
                'id' => 235,
                'consignment_id' => 170395,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            208 => 
            array (
                'id' => 236,
                'consignment_id' => 170396,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            209 => 
            array (
                'id' => 237,
                'consignment_id' => 170397,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            210 => 
            array (
                'id' => 238,
                'consignment_id' => 170398,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            211 => 
            array (
                'id' => 239,
                'consignment_id' => 170399,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            212 => 
            array (
                'id' => 240,
                'consignment_id' => 170400,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            213 => 
            array (
                'id' => 241,
                'consignment_id' => 170401,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            214 => 
            array (
                'id' => 242,
                'consignment_id' => 170402,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            215 => 
            array (
                'id' => 243,
                'consignment_id' => 170403,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            216 => 
            array (
                'id' => 244,
                'consignment_id' => 170404,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            217 => 
            array (
                'id' => 245,
                'consignment_id' => 170405,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            218 => 
            array (
                'id' => 246,
                'consignment_id' => 170406,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            219 => 
            array (
                'id' => 247,
                'consignment_id' => 170407,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            220 => 
            array (
                'id' => 248,
                'consignment_id' => 170408,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            221 => 
            array (
                'id' => 249,
                'consignment_id' => 170409,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"dfads455","no_of_items":"4","item_value":"1","weight":"1","tariff_no":"","hscode":"32434drre","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            222 => 
            array (
                'id' => 250,
                'consignment_id' => 170410,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            223 => 
            array (
                'id' => 251,
                'consignment_id' => 170411,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"fdasf435","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123asd432","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            224 => 
            array (
                'id' => 252,
                'consignment_id' => 170412,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"tet","item_url":"","item_sku":"adsfat43r","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"erwdsaf","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            225 => 
            array (
                'id' => 253,
                'consignment_id' => 170413,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            226 => 
            array (
                'id' => 254,
                'consignment_id' => 170414,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            227 => 
            array (
                'id' => 255,
                'consignment_id' => 170415,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            228 => 
            array (
                'id' => 256,
                'consignment_id' => 170416,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            229 => 
            array (
                'id' => 257,
                'consignment_id' => 170417,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            230 => 
            array (
                'id' => 258,
                'consignment_id' => 170418,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            231 => 
            array (
                'id' => 259,
                'consignment_id' => 170419,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            232 => 
            array (
                'id' => 260,
                'consignment_id' => 170420,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            233 => 
            array (
                'id' => 261,
                'consignment_id' => 170421,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            234 => 
            array (
                'id' => 262,
                'consignment_id' => 170422,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test tesat","item_url":"","item_sku":"adsff435hadi","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"dfasf324","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            235 => 
            array (
                'id' => 264,
                'consignment_id' => 170423,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            236 => 
            array (
                'id' => 265,
                'consignment_id' => 170424,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"etst","item_url":"","item_sku":"teasdf324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"adsf424","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            237 => 
            array (
                'id' => 266,
                'consignment_id' => 170430,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            238 => 
            array (
                'id' => 267,
                'consignment_id' => 170432,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            239 => 
            array (
                'id' => 268,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            240 => 
            array (
                'id' => 269,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.sd.com","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123242","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            241 => 
            array (
                'id' => 270,
                'consignment_id' => 170433,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            242 => 
            array (
                'id' => 271,
                'consignment_id' => 170434,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            243 => 
            array (
                'id' => 272,
                'consignment_id' => 170435,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            244 => 
            array (
                'id' => 273,
                'consignment_id' => 170436,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            245 => 
            array (
                'id' => 274,
                'consignment_id' => 170437,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            246 => 
            array (
                'id' => 275,
                'consignment_id' => 170438,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            247 => 
            array (
                'id' => 276,
                'consignment_id' => 170439,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            248 => 
            array (
                'id' => 277,
                'consignment_id' => 170440,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            249 => 
            array (
                'id' => 278,
                'consignment_id' => 170441,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"books","item_url":"http:\\/\\/www.dad.com","item_sku":"24234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            250 => 
            array (
                'id' => 279,
                'consignment_id' => 170442,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            251 => 
            array (
                'id' => 280,
                'consignment_id' => 170443,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            252 => 
            array (
                'id' => 281,
                'consignment_id' => 170444,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            253 => 
            array (
                'id' => 282,
                'consignment_id' => 170445,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            254 => 
            array (
                'id' => 283,
                'consignment_id' => 170446,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Official Star Wars Storm Trooper Holdall Cabin Travel Gym Shoulder Duffel Bag","item_url":"ebay.com","weight":"1.00","item_sku":"3.04972E+11","no_of_items":"1","item_value":"9.99","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            255 => 
            array (
                'id' => 284,
                'consignment_id' => 170447,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Vivid Arts Zoo Pet Pals REALSTIC Baby Gorilla Home  Garden Decor Cute Gift","item_url":"ebay.com","weight":"1.00","item_sku":"3.0524E+11","no_of_items":"1","item_value":"15.99","hscode":"hs12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            256 => 
            array (
                'id' => 285,
                'consignment_id' => 170448,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2.4M EXTENDABLE PROP LINE HEAVY DUTY CLOTHES WASHING POLE OUTDOOR SUPPORT","item_url":"ebay.com","weight":"1.00","item_sku":"3.05015E+11","no_of_items":"2","item_value":"6.42","hscode":"hs12346","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            257 => 
            array (
                'id' => 286,
                'consignment_id' => 170449,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Clear Plastic Storage Box Stackable Boxes with Lids Office File use Home Kitchen[45 L,5]","item_url":"ebay.com","weight":"1.00","item_sku":"3.05014E+11","no_of_items":"2","item_value":"44.15","hscode":"hs12347","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            258 => 
            array (
                'id' => 287,
                'consignment_id' => 170450,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"12 Muffin Cupcake Tin Tray Non Stick Carbon Steel Baking Pan Yorkshire Pudding","item_url":"ebay.com","weight":"1.00","item_sku":"3.0495E+11","no_of_items":"1","item_value":"6.99","hscode":"hs12348","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            259 => 
            array (
                'id' => 288,
                'consignment_id' => 170451,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Woodland LED Aroma Tree Cylinder Electric Lamp Wax Melt Oil Burner GIFT BOXED[Colour Changing Grey]","item_url":"ebay.com","weight":"1.00","item_sku":"3.03498E+11","no_of_items":"1","item_value":"19.99","hscode":"hs12349","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            260 => 
            array (
                'id' => 289,
                'consignment_id' => 170452,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Luxury 2 Pack Hungarian 5* Goose Feather and Down Non-allergenic Pillows Cushion[4 Pack]","item_url":"ebay.com","weight":"1.00","item_sku":"3.04831E+11","no_of_items":"2","item_value":"47.52","hscode":"hs12350","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            261 => 
            array (
                'id' => 290,
                'consignment_id' => 170453,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2X Unibos Dolly Trolley Platform 200kg Wheeled Wooden Board Transporter- 59X29CM","item_url":"ebay.com","weight":"1.00","item_sku":"3.04923E+11","no_of_items":"2","item_value":"26.59","hscode":"hs12351","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            262 => 
            array (
                'id' => 291,
                'consignment_id' => 170454,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            263 => 
            array (
                'id' => 292,
                'consignment_id' => 170455,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"DSF","item_url":"HTTP:\\/\\/ww.dcom","item_sku":"4234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            264 => 
            array (
                'id' => 293,
                'consignment_id' => 170456,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            265 => 
            array (
                'id' => 294,
                'consignment_id' => 170457,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            266 => 
            array (
                'id' => 295,
                'consignment_id' => 170458,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            267 => 
            array (
                'id' => 296,
                'consignment_id' => 170459,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            268 => 
            array (
                'id' => 297,
                'consignment_id' => 170460,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            269 => 
            array (
                'id' => 298,
                'consignment_id' => 170461,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            270 => 
            array (
                'id' => 299,
                'consignment_id' => 170462,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            271 => 
            array (
                'id' => 300,
                'consignment_id' => 170463,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            272 => 
            array (
                'id' => 301,
                'consignment_id' => 170464,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/asfa.com","item_sku":"32424","no_of_items":"1","item_value":"1","weight":"11","tariff_no":"","hscode":"122333","manufacture_country_iso":""}]',
                'user_id' => 2341,
            ),
            273 => 
            array (
                'id' => 302,
                'consignment_id' => 170465,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"4455","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            274 => 
            array (
                'id' => 307,
                'consignment_id' => 170466,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/we.com","item_sku":"234324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            275 => 
            array (
                'id' => 308,
                'consignment_id' => 170467,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.com.co","item_sku":"234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            276 => 
            array (
                'id' => 12,
                'consignment_id' => 170278,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"13232","manufacture_country_iso":"AU"}]',
                'user_id' => 58,
            ),
            277 => 
            array (
                'id' => 14,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.ecom","item_sku":"23423","no_of_items":"11","item_value":"1","weight":"1","tariff_no":"","hscode":"132345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            278 => 
            array (
                'id' => 15,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            279 => 
            array (
                'id' => 17,
                'consignment_id' => 170275,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            280 => 
            array (
                'id' => 18,
                'consignment_id' => 170274,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            281 => 
            array (
                'id' => 19,
                'consignment_id' => 170273,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            282 => 
            array (
                'id' => 20,
                'consignment_id' => 170272,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            283 => 
            array (
                'id' => 21,
                'consignment_id' => 170271,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            284 => 
            array (
                'id' => 23,
                'consignment_id' => 170268,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            285 => 
            array (
                'id' => 24,
                'consignment_id' => 170270,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            286 => 
            array (
                'id' => 25,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            287 => 
            array (
                'id' => 26,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            288 => 
            array (
                'id' => 27,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            289 => 
            array (
                'id' => 28,
                'consignment_id' => 170280,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            290 => 
            array (
                'id' => 29,
                'consignment_id' => 170281,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            291 => 
            array (
                'id' => 30,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            292 => 
            array (
                'id' => 31,
                'consignment_id' => 170283,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            293 => 
            array (
                'id' => 32,
                'consignment_id' => 170284,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            294 => 
            array (
                'id' => 33,
                'consignment_id' => 170212,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            295 => 
            array (
                'id' => 34,
                'consignment_id' => 170213,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            296 => 
            array (
                'id' => 35,
                'consignment_id' => 170287,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            297 => 
            array (
                'id' => 36,
                'consignment_id' => 170288,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            298 => 
            array (
                'id' => 37,
                'consignment_id' => 170289,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            299 => 
            array (
                'id' => 38,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            300 => 
            array (
                'id' => 39,
                'consignment_id' => 170291,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            301 => 
            array (
                'id' => 40,
                'consignment_id' => 170292,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            302 => 
            array (
                'id' => 41,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            303 => 
            array (
                'id' => 42,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            304 => 
            array (
                'id' => 43,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            305 => 
            array (
                'id' => 44,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            306 => 
            array (
                'id' => 45,
                'consignment_id' => 170296,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            307 => 
            array (
                'id' => 50,
                'consignment_id' => 0,
                'session_id' => '9b478d41c9eef15d449460b0d1705c83',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"343","no_of_items":"33","item_value":"3","weight":"3","tariff_no":"","hscode":"333","manufacture_country_iso":"AI"}]',
                'user_id' => 58,
            ),
            308 => 
            array (
                'id' => 52,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.go.com","item_sku":"324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            309 => 
            array (
                'id' => 61,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdfa.com","item_sku":"2423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            310 => 
            array (
                'id' => 62,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2334,
            ),
            311 => 
            array (
                'id' => 63,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"http:\\/\\/www.asdfa.com","item_sku":"123213","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            312 => 
            array (
                'id' => 64,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            313 => 
            array (
                'id' => 65,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            314 => 
            array (
                'id' => 66,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            315 => 
            array (
                'id' => 67,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            316 => 
            array (
                'id' => 68,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            317 => 
            array (
                'id' => 69,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            318 => 
            array (
                'id' => 70,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            319 => 
            array (
                'id' => 71,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            320 => 
            array (
                'id' => 72,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            321 => 
            array (
                'id' => 73,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            322 => 
            array (
                'id' => 74,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            323 => 
            array (
                'id' => 75,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            324 => 
            array (
                'id' => 76,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            325 => 
            array (
                'id' => 77,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            326 => 
            array (
                'id' => 78,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            327 => 
            array (
                'id' => 79,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            328 => 
            array (
                'id' => 80,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            329 => 
            array (
                'id' => 81,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            330 => 
            array (
                'id' => 82,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            331 => 
            array (
                'id' => 83,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            332 => 
            array (
                'id' => 84,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            333 => 
            array (
                'id' => 85,
                'consignment_id' => 170308,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            334 => 
            array (
                'id' => 86,
                'consignment_id' => 170309,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            335 => 
            array (
                'id' => 87,
                'consignment_id' => 170310,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            336 => 
            array (
                'id' => 88,
                'consignment_id' => 170311,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.adf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            337 => 
            array (
                'id' => 89,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            338 => 
            array (
                'id' => 90,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            339 => 
            array (
                'id' => 91,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            340 => 
            array (
                'id' => 92,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            341 => 
            array (
                'id' => 93,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            342 => 
            array (
                'id' => 94,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            343 => 
            array (
                'id' => 95,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            344 => 
            array (
                'id' => 96,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            345 => 
            array (
                'id' => 97,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            346 => 
            array (
                'id' => 98,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            347 => 
            array (
                'id' => 99,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            348 => 
            array (
                'id' => 100,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            349 => 
            array (
                'id' => 101,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            350 => 
            array (
                'id' => 102,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            351 => 
            array (
                'id' => 103,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            352 => 
            array (
                'id' => 104,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            353 => 
            array (
                'id' => 105,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            354 => 
            array (
                'id' => 106,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            355 => 
            array (
                'id' => 107,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            356 => 
            array (
                'id' => 108,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            357 => 
            array (
                'id' => 109,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            358 => 
            array (
                'id' => 110,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            359 => 
            array (
                'id' => 111,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            360 => 
            array (
                'id' => 112,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            361 => 
            array (
                'id' => 113,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            362 => 
            array (
                'id' => 114,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            363 => 
            array (
                'id' => 115,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            364 => 
            array (
                'id' => 116,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            365 => 
            array (
                'id' => 117,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            366 => 
            array (
                'id' => 118,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            367 => 
            array (
                'id' => 119,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            368 => 
            array (
                'id' => 120,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            369 => 
            array (
                'id' => 121,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            370 => 
            array (
                'id' => 122,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            371 => 
            array (
                'id' => 123,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            372 => 
            array (
                'id' => 124,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            373 => 
            array (
                'id' => 125,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            374 => 
            array (
                'id' => 126,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            375 => 
            array (
                'id' => 127,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            376 => 
            array (
                'id' => 128,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            377 => 
            array (
                'id' => 129,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            378 => 
            array (
                'id' => 130,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            379 => 
            array (
                'id' => 131,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            380 => 
            array (
                'id' => 132,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            381 => 
            array (
                'id' => 133,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            382 => 
            array (
                'id' => 134,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            383 => 
            array (
                'id' => 135,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            384 => 
            array (
                'id' => 136,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            385 => 
            array (
                'id' => 137,
                'consignment_id' => 170336,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdf.com","item_sku":"23434","no_of_items":"2","item_value":"2","weight":"2","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            386 => 
            array (
                'id' => 138,
                'consignment_id' => 170337,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            387 => 
            array (
                'id' => 139,
                'consignment_id' => 170338,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"","item_sku":"324234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            388 => 
            array (
                'id' => 140,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            389 => 
            array (
                'id' => 141,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            390 => 
            array (
                'id' => 142,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            391 => 
            array (
                'id' => 143,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            392 => 
            array (
                'id' => 144,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            393 => 
            array (
                'id' => 145,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            394 => 
            array (
                'id' => 146,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            395 => 
            array (
                'id' => 147,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            396 => 
            array (
                'id' => 148,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            397 => 
            array (
                'id' => 149,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            398 => 
            array (
                'id' => 150,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            399 => 
            array (
                'id' => 151,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            400 => 
            array (
                'id' => 152,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            401 => 
            array (
                'id' => 153,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            402 => 
            array (
                'id' => 154,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            403 => 
            array (
                'id' => 155,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            404 => 
            array (
                'id' => 156,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            405 => 
            array (
                'id' => 157,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            406 => 
            array (
                'id' => 158,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            407 => 
            array (
                'id' => 159,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            408 => 
            array (
                'id' => 160,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            409 => 
            array (
                'id' => 161,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            410 => 
            array (
                'id' => 162,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            411 => 
            array (
                'id' => 163,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            412 => 
            array (
                'id' => 164,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            413 => 
            array (
                'id' => 165,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            414 => 
            array (
                'id' => 166,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            415 => 
            array (
                'id' => 167,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            416 => 
            array (
                'id' => 168,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            417 => 
            array (
                'id' => 169,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            418 => 
            array (
                'id' => 170,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            419 => 
            array (
                'id' => 171,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            420 => 
            array (
                'id' => 172,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            421 => 
            array (
                'id' => 173,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            422 => 
            array (
                'id' => 174,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            423 => 
            array (
                'id' => 175,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            424 => 
            array (
                'id' => 176,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            425 => 
            array (
                'id' => 177,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            426 => 
            array (
                'id' => 178,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            427 => 
            array (
                'id' => 179,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            428 => 
            array (
                'id' => 180,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            429 => 
            array (
                'id' => 181,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            430 => 
            array (
                'id' => 182,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            431 => 
            array (
                'id' => 183,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            432 => 
            array (
                'id' => 184,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            433 => 
            array (
                'id' => 185,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            434 => 
            array (
                'id' => 186,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            435 => 
            array (
                'id' => 187,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            436 => 
            array (
                'id' => 188,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            437 => 
            array (
                'id' => 189,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            438 => 
            array (
                'id' => 190,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            439 => 
            array (
                'id' => 191,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            440 => 
            array (
                'id' => 192,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            441 => 
            array (
                'id' => 193,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            442 => 
            array (
                'id' => 194,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            443 => 
            array (
                'id' => 195,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            444 => 
            array (
                'id' => 196,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            445 => 
            array (
                'id' => 197,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            446 => 
            array (
                'id' => 198,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            447 => 
            array (
                'id' => 199,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            448 => 
            array (
                'id' => 200,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            449 => 
            array (
                'id' => 201,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            450 => 
            array (
                'id' => 202,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            451 => 
            array (
                'id' => 203,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            452 => 
            array (
                'id' => 204,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            453 => 
            array (
                'id' => 205,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            454 => 
            array (
                'id' => 206,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            455 => 
            array (
                'id' => 207,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            456 => 
            array (
                'id' => 208,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            457 => 
            array (
                'id' => 209,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            458 => 
            array (
                'id' => 210,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            459 => 
            array (
                'id' => 211,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            460 => 
            array (
                'id' => 212,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            461 => 
            array (
                'id' => 213,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            462 => 
            array (
                'id' => 214,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            463 => 
            array (
                'id' => 215,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            464 => 
            array (
                'id' => 216,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            465 => 
            array (
                'id' => 217,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            466 => 
            array (
                'id' => 218,
                'consignment_id' => 170378,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            467 => 
            array (
                'id' => 219,
                'consignment_id' => 170379,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            468 => 
            array (
                'id' => 220,
                'consignment_id' => 170380,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            469 => 
            array (
                'id' => 221,
                'consignment_id' => 170381,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            470 => 
            array (
                'id' => 222,
                'consignment_id' => 170382,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            471 => 
            array (
                'id' => 223,
                'consignment_id' => 170383,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            472 => 
            array (
                'id' => 224,
                'consignment_id' => 170384,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            473 => 
            array (
                'id' => 225,
                'consignment_id' => 170385,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            474 => 
            array (
                'id' => 226,
                'consignment_id' => 170386,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            475 => 
            array (
                'id' => 227,
                'consignment_id' => 170387,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            476 => 
            array (
                'id' => 228,
                'consignment_id' => 170388,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            477 => 
            array (
                'id' => 229,
                'consignment_id' => 170389,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            478 => 
            array (
                'id' => 230,
                'consignment_id' => 170390,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            479 => 
            array (
                'id' => 231,
                'consignment_id' => 170391,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            480 => 
            array (
                'id' => 232,
                'consignment_id' => 170392,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            481 => 
            array (
                'id' => 233,
                'consignment_id' => 170393,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            482 => 
            array (
                'id' => 234,
                'consignment_id' => 170394,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            483 => 
            array (
                'id' => 235,
                'consignment_id' => 170395,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            484 => 
            array (
                'id' => 236,
                'consignment_id' => 170396,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            485 => 
            array (
                'id' => 237,
                'consignment_id' => 170397,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            486 => 
            array (
                'id' => 238,
                'consignment_id' => 170398,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            487 => 
            array (
                'id' => 239,
                'consignment_id' => 170399,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            488 => 
            array (
                'id' => 240,
                'consignment_id' => 170400,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            489 => 
            array (
                'id' => 241,
                'consignment_id' => 170401,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            490 => 
            array (
                'id' => 242,
                'consignment_id' => 170402,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            491 => 
            array (
                'id' => 243,
                'consignment_id' => 170403,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            492 => 
            array (
                'id' => 244,
                'consignment_id' => 170404,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            493 => 
            array (
                'id' => 245,
                'consignment_id' => 170405,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            494 => 
            array (
                'id' => 246,
                'consignment_id' => 170406,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            495 => 
            array (
                'id' => 247,
                'consignment_id' => 170407,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            496 => 
            array (
                'id' => 248,
                'consignment_id' => 170408,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            497 => 
            array (
                'id' => 249,
                'consignment_id' => 170409,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"dfads455","no_of_items":"4","item_value":"1","weight":"1","tariff_no":"","hscode":"32434drre","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            498 => 
            array (
                'id' => 250,
                'consignment_id' => 170410,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            499 => 
            array (
                'id' => 251,
                'consignment_id' => 170411,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"fdasf435","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123asd432","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
        ));
        \DB::table('item_details')->insert(array (
            0 => 
            array (
                'id' => 252,
                'consignment_id' => 170412,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"tet","item_url":"","item_sku":"adsfat43r","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"erwdsaf","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            1 => 
            array (
                'id' => 253,
                'consignment_id' => 170413,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            2 => 
            array (
                'id' => 254,
                'consignment_id' => 170414,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            3 => 
            array (
                'id' => 255,
                'consignment_id' => 170415,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            4 => 
            array (
                'id' => 256,
                'consignment_id' => 170416,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            5 => 
            array (
                'id' => 257,
                'consignment_id' => 170417,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            6 => 
            array (
                'id' => 258,
                'consignment_id' => 170418,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            7 => 
            array (
                'id' => 259,
                'consignment_id' => 170419,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            8 => 
            array (
                'id' => 260,
                'consignment_id' => 170420,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            9 => 
            array (
                'id' => 261,
                'consignment_id' => 170421,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            10 => 
            array (
                'id' => 262,
                'consignment_id' => 170422,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test tesat","item_url":"","item_sku":"adsff435hadi","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"dfasf324","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            11 => 
            array (
                'id' => 264,
                'consignment_id' => 170423,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            12 => 
            array (
                'id' => 265,
                'consignment_id' => 170424,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"etst","item_url":"","item_sku":"teasdf324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"adsf424","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            13 => 
            array (
                'id' => 266,
                'consignment_id' => 170430,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            14 => 
            array (
                'id' => 267,
                'consignment_id' => 170432,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            15 => 
            array (
                'id' => 268,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            16 => 
            array (
                'id' => 269,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.sd.com","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123242","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            17 => 
            array (
                'id' => 270,
                'consignment_id' => 170433,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            18 => 
            array (
                'id' => 271,
                'consignment_id' => 170434,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            19 => 
            array (
                'id' => 272,
                'consignment_id' => 170435,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            20 => 
            array (
                'id' => 273,
                'consignment_id' => 170436,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            21 => 
            array (
                'id' => 274,
                'consignment_id' => 170437,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            22 => 
            array (
                'id' => 275,
                'consignment_id' => 170438,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            23 => 
            array (
                'id' => 276,
                'consignment_id' => 170439,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            24 => 
            array (
                'id' => 277,
                'consignment_id' => 170440,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            25 => 
            array (
                'id' => 278,
                'consignment_id' => 170441,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"books","item_url":"http:\\/\\/www.dad.com","item_sku":"24234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            26 => 
            array (
                'id' => 279,
                'consignment_id' => 170442,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            27 => 
            array (
                'id' => 280,
                'consignment_id' => 170443,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            28 => 
            array (
                'id' => 281,
                'consignment_id' => 170444,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            29 => 
            array (
                'id' => 282,
                'consignment_id' => 170445,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            30 => 
            array (
                'id' => 283,
                'consignment_id' => 170446,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Official Star Wars Storm Trooper Holdall Cabin Travel Gym Shoulder Duffel Bag","item_url":"ebay.com","weight":"1.00","item_sku":"3.04972E+11","no_of_items":"1","item_value":"9.99","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            31 => 
            array (
                'id' => 284,
                'consignment_id' => 170447,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Vivid Arts Zoo Pet Pals REALSTIC Baby Gorilla Home  Garden Decor Cute Gift","item_url":"ebay.com","weight":"1.00","item_sku":"3.0524E+11","no_of_items":"1","item_value":"15.99","hscode":"hs12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            32 => 
            array (
                'id' => 285,
                'consignment_id' => 170448,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2.4M EXTENDABLE PROP LINE HEAVY DUTY CLOTHES WASHING POLE OUTDOOR SUPPORT","item_url":"ebay.com","weight":"1.00","item_sku":"3.05015E+11","no_of_items":"2","item_value":"6.42","hscode":"hs12346","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            33 => 
            array (
                'id' => 286,
                'consignment_id' => 170449,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Clear Plastic Storage Box Stackable Boxes with Lids Office File use Home Kitchen[45 L,5]","item_url":"ebay.com","weight":"1.00","item_sku":"3.05014E+11","no_of_items":"2","item_value":"44.15","hscode":"hs12347","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            34 => 
            array (
                'id' => 287,
                'consignment_id' => 170450,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"12 Muffin Cupcake Tin Tray Non Stick Carbon Steel Baking Pan Yorkshire Pudding","item_url":"ebay.com","weight":"1.00","item_sku":"3.0495E+11","no_of_items":"1","item_value":"6.99","hscode":"hs12348","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            35 => 
            array (
                'id' => 288,
                'consignment_id' => 170451,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Woodland LED Aroma Tree Cylinder Electric Lamp Wax Melt Oil Burner GIFT BOXED[Colour Changing Grey]","item_url":"ebay.com","weight":"1.00","item_sku":"3.03498E+11","no_of_items":"1","item_value":"19.99","hscode":"hs12349","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            36 => 
            array (
                'id' => 289,
                'consignment_id' => 170452,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Luxury 2 Pack Hungarian 5* Goose Feather and Down Non-allergenic Pillows Cushion[4 Pack]","item_url":"ebay.com","weight":"1.00","item_sku":"3.04831E+11","no_of_items":"2","item_value":"47.52","hscode":"hs12350","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            37 => 
            array (
                'id' => 290,
                'consignment_id' => 170453,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2X Unibos Dolly Trolley Platform 200kg Wheeled Wooden Board Transporter- 59X29CM","item_url":"ebay.com","weight":"1.00","item_sku":"3.04923E+11","no_of_items":"2","item_value":"26.59","hscode":"hs12351","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            38 => 
            array (
                'id' => 291,
                'consignment_id' => 170454,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            39 => 
            array (
                'id' => 292,
                'consignment_id' => 170455,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"DSF","item_url":"HTTP:\\/\\/ww.dcom","item_sku":"4234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            40 => 
            array (
                'id' => 293,
                'consignment_id' => 170456,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            41 => 
            array (
                'id' => 294,
                'consignment_id' => 170457,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            42 => 
            array (
                'id' => 295,
                'consignment_id' => 170458,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            43 => 
            array (
                'id' => 296,
                'consignment_id' => 170459,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            44 => 
            array (
                'id' => 297,
                'consignment_id' => 170460,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            45 => 
            array (
                'id' => 298,
                'consignment_id' => 170461,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            46 => 
            array (
                'id' => 299,
                'consignment_id' => 170462,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            47 => 
            array (
                'id' => 300,
                'consignment_id' => 170463,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            48 => 
            array (
                'id' => 301,
                'consignment_id' => 170464,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/asfa.com","item_sku":"32424","no_of_items":"1","item_value":"1","weight":"11","tariff_no":"","hscode":"122333","manufacture_country_iso":""}]',
                'user_id' => 2341,
            ),
            49 => 
            array (
                'id' => 302,
                'consignment_id' => 170465,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"4455","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            50 => 
            array (
                'id' => 307,
                'consignment_id' => 170466,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/we.com","item_sku":"234324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            51 => 
            array (
                'id' => 308,
                'consignment_id' => 170467,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.com.co","item_sku":"234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            52 => 
            array (
                'id' => 12,
                'consignment_id' => 170278,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"13232","manufacture_country_iso":"AU"}]',
                'user_id' => 58,
            ),
            53 => 
            array (
                'id' => 14,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.ecom","item_sku":"23423","no_of_items":"11","item_value":"1","weight":"1","tariff_no":"","hscode":"132345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            54 => 
            array (
                'id' => 15,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            55 => 
            array (
                'id' => 17,
                'consignment_id' => 170275,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            56 => 
            array (
                'id' => 18,
                'consignment_id' => 170274,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            57 => 
            array (
                'id' => 19,
                'consignment_id' => 170273,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            58 => 
            array (
                'id' => 20,
                'consignment_id' => 170272,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            59 => 
            array (
                'id' => 21,
                'consignment_id' => 170271,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            60 => 
            array (
                'id' => 23,
                'consignment_id' => 170268,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            61 => 
            array (
                'id' => 24,
                'consignment_id' => 170270,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            62 => 
            array (
                'id' => 25,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            63 => 
            array (
                'id' => 26,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            64 => 
            array (
                'id' => 27,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            65 => 
            array (
                'id' => 28,
                'consignment_id' => 170280,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            66 => 
            array (
                'id' => 29,
                'consignment_id' => 170281,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            67 => 
            array (
                'id' => 30,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            68 => 
            array (
                'id' => 31,
                'consignment_id' => 170283,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            69 => 
            array (
                'id' => 32,
                'consignment_id' => 170284,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            70 => 
            array (
                'id' => 33,
                'consignment_id' => 170212,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            71 => 
            array (
                'id' => 34,
                'consignment_id' => 170213,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            72 => 
            array (
                'id' => 35,
                'consignment_id' => 170287,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            73 => 
            array (
                'id' => 36,
                'consignment_id' => 170288,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            74 => 
            array (
                'id' => 37,
                'consignment_id' => 170289,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            75 => 
            array (
                'id' => 38,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            76 => 
            array (
                'id' => 39,
                'consignment_id' => 170291,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            77 => 
            array (
                'id' => 40,
                'consignment_id' => 170292,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            78 => 
            array (
                'id' => 41,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            79 => 
            array (
                'id' => 42,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            80 => 
            array (
                'id' => 43,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            81 => 
            array (
                'id' => 44,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            82 => 
            array (
                'id' => 45,
                'consignment_id' => 170296,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            83 => 
            array (
                'id' => 50,
                'consignment_id' => 0,
                'session_id' => '9b478d41c9eef15d449460b0d1705c83',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"343","no_of_items":"33","item_value":"3","weight":"3","tariff_no":"","hscode":"333","manufacture_country_iso":"AI"}]',
                'user_id' => 58,
            ),
            84 => 
            array (
                'id' => 52,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.go.com","item_sku":"324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            85 => 
            array (
                'id' => 61,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdfa.com","item_sku":"2423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            86 => 
            array (
                'id' => 62,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2334,
            ),
            87 => 
            array (
                'id' => 63,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"http:\\/\\/www.asdfa.com","item_sku":"123213","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            88 => 
            array (
                'id' => 64,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            89 => 
            array (
                'id' => 65,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            90 => 
            array (
                'id' => 66,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            91 => 
            array (
                'id' => 67,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            92 => 
            array (
                'id' => 68,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            93 => 
            array (
                'id' => 69,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            94 => 
            array (
                'id' => 70,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            95 => 
            array (
                'id' => 71,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            96 => 
            array (
                'id' => 72,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            97 => 
            array (
                'id' => 73,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            98 => 
            array (
                'id' => 74,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            99 => 
            array (
                'id' => 75,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            100 => 
            array (
                'id' => 76,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            101 => 
            array (
                'id' => 77,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            102 => 
            array (
                'id' => 78,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            103 => 
            array (
                'id' => 79,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            104 => 
            array (
                'id' => 80,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            105 => 
            array (
                'id' => 81,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            106 => 
            array (
                'id' => 82,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            107 => 
            array (
                'id' => 83,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            108 => 
            array (
                'id' => 84,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            109 => 
            array (
                'id' => 85,
                'consignment_id' => 170308,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            110 => 
            array (
                'id' => 86,
                'consignment_id' => 170309,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            111 => 
            array (
                'id' => 87,
                'consignment_id' => 170310,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            112 => 
            array (
                'id' => 88,
                'consignment_id' => 170311,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.adf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            113 => 
            array (
                'id' => 89,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            114 => 
            array (
                'id' => 90,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            115 => 
            array (
                'id' => 91,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            116 => 
            array (
                'id' => 92,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            117 => 
            array (
                'id' => 93,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            118 => 
            array (
                'id' => 94,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            119 => 
            array (
                'id' => 95,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            120 => 
            array (
                'id' => 96,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            121 => 
            array (
                'id' => 97,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            122 => 
            array (
                'id' => 98,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            123 => 
            array (
                'id' => 99,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            124 => 
            array (
                'id' => 100,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            125 => 
            array (
                'id' => 101,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            126 => 
            array (
                'id' => 102,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            127 => 
            array (
                'id' => 103,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            128 => 
            array (
                'id' => 104,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            129 => 
            array (
                'id' => 105,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            130 => 
            array (
                'id' => 106,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            131 => 
            array (
                'id' => 107,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            132 => 
            array (
                'id' => 108,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            133 => 
            array (
                'id' => 109,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            134 => 
            array (
                'id' => 110,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            135 => 
            array (
                'id' => 111,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            136 => 
            array (
                'id' => 112,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            137 => 
            array (
                'id' => 113,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            138 => 
            array (
                'id' => 114,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            139 => 
            array (
                'id' => 115,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            140 => 
            array (
                'id' => 116,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            141 => 
            array (
                'id' => 117,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            142 => 
            array (
                'id' => 118,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            143 => 
            array (
                'id' => 119,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            144 => 
            array (
                'id' => 120,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            145 => 
            array (
                'id' => 121,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            146 => 
            array (
                'id' => 122,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            147 => 
            array (
                'id' => 123,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            148 => 
            array (
                'id' => 124,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            149 => 
            array (
                'id' => 125,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            150 => 
            array (
                'id' => 126,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            151 => 
            array (
                'id' => 127,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            152 => 
            array (
                'id' => 128,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            153 => 
            array (
                'id' => 129,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            154 => 
            array (
                'id' => 130,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            155 => 
            array (
                'id' => 131,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            156 => 
            array (
                'id' => 132,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            157 => 
            array (
                'id' => 133,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            158 => 
            array (
                'id' => 134,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            159 => 
            array (
                'id' => 135,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            160 => 
            array (
                'id' => 136,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            161 => 
            array (
                'id' => 137,
                'consignment_id' => 170336,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdf.com","item_sku":"23434","no_of_items":"2","item_value":"2","weight":"2","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            162 => 
            array (
                'id' => 138,
                'consignment_id' => 170337,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            163 => 
            array (
                'id' => 139,
                'consignment_id' => 170338,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"","item_sku":"324234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            164 => 
            array (
                'id' => 140,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            165 => 
            array (
                'id' => 141,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            166 => 
            array (
                'id' => 142,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            167 => 
            array (
                'id' => 143,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            168 => 
            array (
                'id' => 144,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            169 => 
            array (
                'id' => 145,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            170 => 
            array (
                'id' => 146,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            171 => 
            array (
                'id' => 147,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            172 => 
            array (
                'id' => 148,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            173 => 
            array (
                'id' => 149,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            174 => 
            array (
                'id' => 150,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            175 => 
            array (
                'id' => 151,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            176 => 
            array (
                'id' => 152,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            177 => 
            array (
                'id' => 153,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            178 => 
            array (
                'id' => 154,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            179 => 
            array (
                'id' => 155,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            180 => 
            array (
                'id' => 156,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            181 => 
            array (
                'id' => 157,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            182 => 
            array (
                'id' => 158,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            183 => 
            array (
                'id' => 159,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            184 => 
            array (
                'id' => 160,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            185 => 
            array (
                'id' => 161,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            186 => 
            array (
                'id' => 162,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            187 => 
            array (
                'id' => 163,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            188 => 
            array (
                'id' => 164,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            189 => 
            array (
                'id' => 165,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            190 => 
            array (
                'id' => 166,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            191 => 
            array (
                'id' => 167,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            192 => 
            array (
                'id' => 168,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            193 => 
            array (
                'id' => 169,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            194 => 
            array (
                'id' => 170,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            195 => 
            array (
                'id' => 171,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            196 => 
            array (
                'id' => 172,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            197 => 
            array (
                'id' => 173,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            198 => 
            array (
                'id' => 174,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            199 => 
            array (
                'id' => 175,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            200 => 
            array (
                'id' => 176,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            201 => 
            array (
                'id' => 177,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            202 => 
            array (
                'id' => 178,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            203 => 
            array (
                'id' => 179,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            204 => 
            array (
                'id' => 180,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            205 => 
            array (
                'id' => 181,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            206 => 
            array (
                'id' => 182,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            207 => 
            array (
                'id' => 183,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            208 => 
            array (
                'id' => 184,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            209 => 
            array (
                'id' => 185,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            210 => 
            array (
                'id' => 186,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            211 => 
            array (
                'id' => 187,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            212 => 
            array (
                'id' => 188,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            213 => 
            array (
                'id' => 189,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            214 => 
            array (
                'id' => 190,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            215 => 
            array (
                'id' => 191,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            216 => 
            array (
                'id' => 192,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            217 => 
            array (
                'id' => 193,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            218 => 
            array (
                'id' => 194,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            219 => 
            array (
                'id' => 195,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            220 => 
            array (
                'id' => 196,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            221 => 
            array (
                'id' => 197,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            222 => 
            array (
                'id' => 198,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            223 => 
            array (
                'id' => 199,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            224 => 
            array (
                'id' => 200,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            225 => 
            array (
                'id' => 201,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            226 => 
            array (
                'id' => 202,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            227 => 
            array (
                'id' => 203,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            228 => 
            array (
                'id' => 204,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            229 => 
            array (
                'id' => 205,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            230 => 
            array (
                'id' => 206,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            231 => 
            array (
                'id' => 207,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            232 => 
            array (
                'id' => 208,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            233 => 
            array (
                'id' => 209,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            234 => 
            array (
                'id' => 210,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            235 => 
            array (
                'id' => 211,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            236 => 
            array (
                'id' => 212,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            237 => 
            array (
                'id' => 213,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            238 => 
            array (
                'id' => 214,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            239 => 
            array (
                'id' => 215,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            240 => 
            array (
                'id' => 216,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            241 => 
            array (
                'id' => 217,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            242 => 
            array (
                'id' => 218,
                'consignment_id' => 170378,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            243 => 
            array (
                'id' => 219,
                'consignment_id' => 170379,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            244 => 
            array (
                'id' => 220,
                'consignment_id' => 170380,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            245 => 
            array (
                'id' => 221,
                'consignment_id' => 170381,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            246 => 
            array (
                'id' => 222,
                'consignment_id' => 170382,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            247 => 
            array (
                'id' => 223,
                'consignment_id' => 170383,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            248 => 
            array (
                'id' => 224,
                'consignment_id' => 170384,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            249 => 
            array (
                'id' => 225,
                'consignment_id' => 170385,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            250 => 
            array (
                'id' => 226,
                'consignment_id' => 170386,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            251 => 
            array (
                'id' => 227,
                'consignment_id' => 170387,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            252 => 
            array (
                'id' => 228,
                'consignment_id' => 170388,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            253 => 
            array (
                'id' => 229,
                'consignment_id' => 170389,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            254 => 
            array (
                'id' => 230,
                'consignment_id' => 170390,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            255 => 
            array (
                'id' => 231,
                'consignment_id' => 170391,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            256 => 
            array (
                'id' => 232,
                'consignment_id' => 170392,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            257 => 
            array (
                'id' => 233,
                'consignment_id' => 170393,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            258 => 
            array (
                'id' => 234,
                'consignment_id' => 170394,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            259 => 
            array (
                'id' => 235,
                'consignment_id' => 170395,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            260 => 
            array (
                'id' => 236,
                'consignment_id' => 170396,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            261 => 
            array (
                'id' => 237,
                'consignment_id' => 170397,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            262 => 
            array (
                'id' => 238,
                'consignment_id' => 170398,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            263 => 
            array (
                'id' => 239,
                'consignment_id' => 170399,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            264 => 
            array (
                'id' => 240,
                'consignment_id' => 170400,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            265 => 
            array (
                'id' => 241,
                'consignment_id' => 170401,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            266 => 
            array (
                'id' => 242,
                'consignment_id' => 170402,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            267 => 
            array (
                'id' => 243,
                'consignment_id' => 170403,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            268 => 
            array (
                'id' => 244,
                'consignment_id' => 170404,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            269 => 
            array (
                'id' => 245,
                'consignment_id' => 170405,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            270 => 
            array (
                'id' => 246,
                'consignment_id' => 170406,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            271 => 
            array (
                'id' => 247,
                'consignment_id' => 170407,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            272 => 
            array (
                'id' => 248,
                'consignment_id' => 170408,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            273 => 
            array (
                'id' => 249,
                'consignment_id' => 170409,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"dfads455","no_of_items":"4","item_value":"1","weight":"1","tariff_no":"","hscode":"32434drre","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            274 => 
            array (
                'id' => 250,
                'consignment_id' => 170410,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            275 => 
            array (
                'id' => 251,
                'consignment_id' => 170411,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"fdasf435","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123asd432","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            276 => 
            array (
                'id' => 252,
                'consignment_id' => 170412,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"tet","item_url":"","item_sku":"adsfat43r","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"erwdsaf","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            277 => 
            array (
                'id' => 253,
                'consignment_id' => 170413,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            278 => 
            array (
                'id' => 254,
                'consignment_id' => 170414,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            279 => 
            array (
                'id' => 255,
                'consignment_id' => 170415,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            280 => 
            array (
                'id' => 256,
                'consignment_id' => 170416,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            281 => 
            array (
                'id' => 257,
                'consignment_id' => 170417,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            282 => 
            array (
                'id' => 258,
                'consignment_id' => 170418,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            283 => 
            array (
                'id' => 259,
                'consignment_id' => 170419,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            284 => 
            array (
                'id' => 260,
                'consignment_id' => 170420,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            285 => 
            array (
                'id' => 261,
                'consignment_id' => 170421,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            286 => 
            array (
                'id' => 262,
                'consignment_id' => 170422,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test tesat","item_url":"","item_sku":"adsff435hadi","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"dfasf324","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            287 => 
            array (
                'id' => 264,
                'consignment_id' => 170423,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            288 => 
            array (
                'id' => 265,
                'consignment_id' => 170424,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"etst","item_url":"","item_sku":"teasdf324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"adsf424","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            289 => 
            array (
                'id' => 266,
                'consignment_id' => 170430,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            290 => 
            array (
                'id' => 267,
                'consignment_id' => 170432,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            291 => 
            array (
                'id' => 268,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            292 => 
            array (
                'id' => 269,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.sd.com","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123242","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            293 => 
            array (
                'id' => 270,
                'consignment_id' => 170433,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            294 => 
            array (
                'id' => 271,
                'consignment_id' => 170434,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            295 => 
            array (
                'id' => 272,
                'consignment_id' => 170435,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            296 => 
            array (
                'id' => 273,
                'consignment_id' => 170436,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            297 => 
            array (
                'id' => 274,
                'consignment_id' => 170437,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            298 => 
            array (
                'id' => 275,
                'consignment_id' => 170438,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            299 => 
            array (
                'id' => 276,
                'consignment_id' => 170439,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            300 => 
            array (
                'id' => 277,
                'consignment_id' => 170440,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            301 => 
            array (
                'id' => 278,
                'consignment_id' => 170441,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"books","item_url":"http:\\/\\/www.dad.com","item_sku":"24234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            302 => 
            array (
                'id' => 279,
                'consignment_id' => 170442,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            303 => 
            array (
                'id' => 280,
                'consignment_id' => 170443,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            304 => 
            array (
                'id' => 281,
                'consignment_id' => 170444,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            305 => 
            array (
                'id' => 282,
                'consignment_id' => 170445,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            306 => 
            array (
                'id' => 283,
                'consignment_id' => 170446,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Official Star Wars Storm Trooper Holdall Cabin Travel Gym Shoulder Duffel Bag","item_url":"ebay.com","weight":"1.00","item_sku":"3.04972E+11","no_of_items":"1","item_value":"9.99","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            307 => 
            array (
                'id' => 284,
                'consignment_id' => 170447,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Vivid Arts Zoo Pet Pals REALSTIC Baby Gorilla Home  Garden Decor Cute Gift","item_url":"ebay.com","weight":"1.00","item_sku":"3.0524E+11","no_of_items":"1","item_value":"15.99","hscode":"hs12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            308 => 
            array (
                'id' => 285,
                'consignment_id' => 170448,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2.4M EXTENDABLE PROP LINE HEAVY DUTY CLOTHES WASHING POLE OUTDOOR SUPPORT","item_url":"ebay.com","weight":"1.00","item_sku":"3.05015E+11","no_of_items":"2","item_value":"6.42","hscode":"hs12346","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            309 => 
            array (
                'id' => 286,
                'consignment_id' => 170449,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Clear Plastic Storage Box Stackable Boxes with Lids Office File use Home Kitchen[45 L,5]","item_url":"ebay.com","weight":"1.00","item_sku":"3.05014E+11","no_of_items":"2","item_value":"44.15","hscode":"hs12347","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            310 => 
            array (
                'id' => 287,
                'consignment_id' => 170450,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"12 Muffin Cupcake Tin Tray Non Stick Carbon Steel Baking Pan Yorkshire Pudding","item_url":"ebay.com","weight":"1.00","item_sku":"3.0495E+11","no_of_items":"1","item_value":"6.99","hscode":"hs12348","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            311 => 
            array (
                'id' => 288,
                'consignment_id' => 170451,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Woodland LED Aroma Tree Cylinder Electric Lamp Wax Melt Oil Burner GIFT BOXED[Colour Changing Grey]","item_url":"ebay.com","weight":"1.00","item_sku":"3.03498E+11","no_of_items":"1","item_value":"19.99","hscode":"hs12349","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            312 => 
            array (
                'id' => 289,
                'consignment_id' => 170452,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Luxury 2 Pack Hungarian 5* Goose Feather and Down Non-allergenic Pillows Cushion[4 Pack]","item_url":"ebay.com","weight":"1.00","item_sku":"3.04831E+11","no_of_items":"2","item_value":"47.52","hscode":"hs12350","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            313 => 
            array (
                'id' => 290,
                'consignment_id' => 170453,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2X Unibos Dolly Trolley Platform 200kg Wheeled Wooden Board Transporter- 59X29CM","item_url":"ebay.com","weight":"1.00","item_sku":"3.04923E+11","no_of_items":"2","item_value":"26.59","hscode":"hs12351","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            314 => 
            array (
                'id' => 291,
                'consignment_id' => 170454,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            315 => 
            array (
                'id' => 292,
                'consignment_id' => 170455,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"DSF","item_url":"HTTP:\\/\\/ww.dcom","item_sku":"4234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            316 => 
            array (
                'id' => 293,
                'consignment_id' => 170456,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            317 => 
            array (
                'id' => 294,
                'consignment_id' => 170457,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            318 => 
            array (
                'id' => 295,
                'consignment_id' => 170458,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            319 => 
            array (
                'id' => 296,
                'consignment_id' => 170459,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            320 => 
            array (
                'id' => 297,
                'consignment_id' => 170460,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            321 => 
            array (
                'id' => 298,
                'consignment_id' => 170461,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            322 => 
            array (
                'id' => 299,
                'consignment_id' => 170462,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            323 => 
            array (
                'id' => 300,
                'consignment_id' => 170463,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            324 => 
            array (
                'id' => 301,
                'consignment_id' => 170464,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/asfa.com","item_sku":"32424","no_of_items":"1","item_value":"1","weight":"11","tariff_no":"","hscode":"122333","manufacture_country_iso":""}]',
                'user_id' => 2341,
            ),
            325 => 
            array (
                'id' => 302,
                'consignment_id' => 170465,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"4455","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            326 => 
            array (
                'id' => 307,
                'consignment_id' => 170466,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/we.com","item_sku":"234324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            327 => 
            array (
                'id' => 308,
                'consignment_id' => 170467,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.com.co","item_sku":"234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            328 => 
            array (
                'id' => 12,
                'consignment_id' => 170278,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"13232","manufacture_country_iso":"AU"}]',
                'user_id' => 58,
            ),
            329 => 
            array (
                'id' => 14,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.ecom","item_sku":"23423","no_of_items":"11","item_value":"1","weight":"1","tariff_no":"","hscode":"132345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            330 => 
            array (
                'id' => 15,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            331 => 
            array (
                'id' => 17,
                'consignment_id' => 170275,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            332 => 
            array (
                'id' => 18,
                'consignment_id' => 170274,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            333 => 
            array (
                'id' => 19,
                'consignment_id' => 170273,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            334 => 
            array (
                'id' => 20,
                'consignment_id' => 170272,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            335 => 
            array (
                'id' => 21,
                'consignment_id' => 170271,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            336 => 
            array (
                'id' => 23,
                'consignment_id' => 170268,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            337 => 
            array (
                'id' => 24,
                'consignment_id' => 170270,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            338 => 
            array (
                'id' => 25,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            339 => 
            array (
                'id' => 26,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            340 => 
            array (
                'id' => 27,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            341 => 
            array (
                'id' => 28,
                'consignment_id' => 170280,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            342 => 
            array (
                'id' => 29,
                'consignment_id' => 170281,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            343 => 
            array (
                'id' => 30,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            344 => 
            array (
                'id' => 31,
                'consignment_id' => 170283,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            345 => 
            array (
                'id' => 32,
                'consignment_id' => 170284,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            346 => 
            array (
                'id' => 33,
                'consignment_id' => 170212,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            347 => 
            array (
                'id' => 34,
                'consignment_id' => 170213,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            348 => 
            array (
                'id' => 35,
                'consignment_id' => 170287,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            349 => 
            array (
                'id' => 36,
                'consignment_id' => 170288,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            350 => 
            array (
                'id' => 37,
                'consignment_id' => 170289,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            351 => 
            array (
                'id' => 38,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            352 => 
            array (
                'id' => 39,
                'consignment_id' => 170291,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            353 => 
            array (
                'id' => 40,
                'consignment_id' => 170292,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            354 => 
            array (
                'id' => 41,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            355 => 
            array (
                'id' => 42,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            356 => 
            array (
                'id' => 43,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            357 => 
            array (
                'id' => 44,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            358 => 
            array (
                'id' => 45,
                'consignment_id' => 170296,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            359 => 
            array (
                'id' => 50,
                'consignment_id' => 0,
                'session_id' => '9b478d41c9eef15d449460b0d1705c83',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"343","no_of_items":"33","item_value":"3","weight":"3","tariff_no":"","hscode":"333","manufacture_country_iso":"AI"}]',
                'user_id' => 58,
            ),
            360 => 
            array (
                'id' => 52,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.go.com","item_sku":"324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            361 => 
            array (
                'id' => 61,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdfa.com","item_sku":"2423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            362 => 
            array (
                'id' => 62,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2334,
            ),
            363 => 
            array (
                'id' => 63,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"http:\\/\\/www.asdfa.com","item_sku":"123213","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            364 => 
            array (
                'id' => 64,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            365 => 
            array (
                'id' => 65,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            366 => 
            array (
                'id' => 66,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            367 => 
            array (
                'id' => 67,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            368 => 
            array (
                'id' => 68,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            369 => 
            array (
                'id' => 69,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            370 => 
            array (
                'id' => 70,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            371 => 
            array (
                'id' => 71,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            372 => 
            array (
                'id' => 72,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            373 => 
            array (
                'id' => 73,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            374 => 
            array (
                'id' => 74,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            375 => 
            array (
                'id' => 75,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            376 => 
            array (
                'id' => 76,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            377 => 
            array (
                'id' => 77,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            378 => 
            array (
                'id' => 78,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            379 => 
            array (
                'id' => 79,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            380 => 
            array (
                'id' => 80,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            381 => 
            array (
                'id' => 81,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            382 => 
            array (
                'id' => 82,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            383 => 
            array (
                'id' => 83,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            384 => 
            array (
                'id' => 84,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            385 => 
            array (
                'id' => 85,
                'consignment_id' => 170308,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            386 => 
            array (
                'id' => 86,
                'consignment_id' => 170309,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            387 => 
            array (
                'id' => 87,
                'consignment_id' => 170310,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            388 => 
            array (
                'id' => 88,
                'consignment_id' => 170311,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.adf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            389 => 
            array (
                'id' => 89,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            390 => 
            array (
                'id' => 90,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            391 => 
            array (
                'id' => 91,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            392 => 
            array (
                'id' => 92,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            393 => 
            array (
                'id' => 93,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            394 => 
            array (
                'id' => 94,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            395 => 
            array (
                'id' => 95,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            396 => 
            array (
                'id' => 96,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            397 => 
            array (
                'id' => 97,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            398 => 
            array (
                'id' => 98,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            399 => 
            array (
                'id' => 99,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            400 => 
            array (
                'id' => 100,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            401 => 
            array (
                'id' => 101,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            402 => 
            array (
                'id' => 102,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            403 => 
            array (
                'id' => 103,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            404 => 
            array (
                'id' => 104,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            405 => 
            array (
                'id' => 105,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            406 => 
            array (
                'id' => 106,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            407 => 
            array (
                'id' => 107,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            408 => 
            array (
                'id' => 108,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            409 => 
            array (
                'id' => 109,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            410 => 
            array (
                'id' => 110,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            411 => 
            array (
                'id' => 111,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            412 => 
            array (
                'id' => 112,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            413 => 
            array (
                'id' => 113,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            414 => 
            array (
                'id' => 114,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            415 => 
            array (
                'id' => 115,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            416 => 
            array (
                'id' => 116,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            417 => 
            array (
                'id' => 117,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            418 => 
            array (
                'id' => 118,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            419 => 
            array (
                'id' => 119,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            420 => 
            array (
                'id' => 120,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            421 => 
            array (
                'id' => 121,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            422 => 
            array (
                'id' => 122,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            423 => 
            array (
                'id' => 123,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            424 => 
            array (
                'id' => 124,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            425 => 
            array (
                'id' => 125,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            426 => 
            array (
                'id' => 126,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            427 => 
            array (
                'id' => 127,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            428 => 
            array (
                'id' => 128,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            429 => 
            array (
                'id' => 129,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            430 => 
            array (
                'id' => 130,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            431 => 
            array (
                'id' => 131,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            432 => 
            array (
                'id' => 132,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            433 => 
            array (
                'id' => 133,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            434 => 
            array (
                'id' => 134,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            435 => 
            array (
                'id' => 135,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            436 => 
            array (
                'id' => 136,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            437 => 
            array (
                'id' => 137,
                'consignment_id' => 170336,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdf.com","item_sku":"23434","no_of_items":"2","item_value":"2","weight":"2","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            438 => 
            array (
                'id' => 138,
                'consignment_id' => 170337,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            439 => 
            array (
                'id' => 139,
                'consignment_id' => 170338,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"","item_sku":"324234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            440 => 
            array (
                'id' => 140,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            441 => 
            array (
                'id' => 141,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            442 => 
            array (
                'id' => 142,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            443 => 
            array (
                'id' => 143,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            444 => 
            array (
                'id' => 144,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            445 => 
            array (
                'id' => 145,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            446 => 
            array (
                'id' => 146,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            447 => 
            array (
                'id' => 147,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            448 => 
            array (
                'id' => 148,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            449 => 
            array (
                'id' => 149,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            450 => 
            array (
                'id' => 150,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            451 => 
            array (
                'id' => 151,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            452 => 
            array (
                'id' => 152,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            453 => 
            array (
                'id' => 153,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            454 => 
            array (
                'id' => 154,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            455 => 
            array (
                'id' => 155,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            456 => 
            array (
                'id' => 156,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            457 => 
            array (
                'id' => 157,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            458 => 
            array (
                'id' => 158,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            459 => 
            array (
                'id' => 159,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            460 => 
            array (
                'id' => 160,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            461 => 
            array (
                'id' => 161,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            462 => 
            array (
                'id' => 162,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            463 => 
            array (
                'id' => 163,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            464 => 
            array (
                'id' => 164,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            465 => 
            array (
                'id' => 165,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            466 => 
            array (
                'id' => 166,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            467 => 
            array (
                'id' => 167,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            468 => 
            array (
                'id' => 168,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            469 => 
            array (
                'id' => 169,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            470 => 
            array (
                'id' => 170,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            471 => 
            array (
                'id' => 171,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            472 => 
            array (
                'id' => 172,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            473 => 
            array (
                'id' => 173,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            474 => 
            array (
                'id' => 174,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            475 => 
            array (
                'id' => 175,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            476 => 
            array (
                'id' => 176,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            477 => 
            array (
                'id' => 177,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            478 => 
            array (
                'id' => 178,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            479 => 
            array (
                'id' => 179,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            480 => 
            array (
                'id' => 180,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            481 => 
            array (
                'id' => 181,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            482 => 
            array (
                'id' => 182,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            483 => 
            array (
                'id' => 183,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            484 => 
            array (
                'id' => 184,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            485 => 
            array (
                'id' => 185,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            486 => 
            array (
                'id' => 186,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            487 => 
            array (
                'id' => 187,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            488 => 
            array (
                'id' => 188,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            489 => 
            array (
                'id' => 189,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            490 => 
            array (
                'id' => 190,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            491 => 
            array (
                'id' => 191,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            492 => 
            array (
                'id' => 192,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            493 => 
            array (
                'id' => 193,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            494 => 
            array (
                'id' => 194,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            495 => 
            array (
                'id' => 195,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            496 => 
            array (
                'id' => 196,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            497 => 
            array (
                'id' => 197,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            498 => 
            array (
                'id' => 198,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            499 => 
            array (
                'id' => 199,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
        ));
        \DB::table('item_details')->insert(array (
            0 => 
            array (
                'id' => 200,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            1 => 
            array (
                'id' => 201,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            2 => 
            array (
                'id' => 202,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            3 => 
            array (
                'id' => 203,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            4 => 
            array (
                'id' => 204,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            5 => 
            array (
                'id' => 205,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            6 => 
            array (
                'id' => 206,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            7 => 
            array (
                'id' => 207,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            8 => 
            array (
                'id' => 208,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            9 => 
            array (
                'id' => 209,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            10 => 
            array (
                'id' => 210,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            11 => 
            array (
                'id' => 211,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            12 => 
            array (
                'id' => 212,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            13 => 
            array (
                'id' => 213,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            14 => 
            array (
                'id' => 214,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            15 => 
            array (
                'id' => 215,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            16 => 
            array (
                'id' => 216,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            17 => 
            array (
                'id' => 217,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            18 => 
            array (
                'id' => 218,
                'consignment_id' => 170378,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            19 => 
            array (
                'id' => 219,
                'consignment_id' => 170379,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            20 => 
            array (
                'id' => 220,
                'consignment_id' => 170380,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            21 => 
            array (
                'id' => 221,
                'consignment_id' => 170381,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            22 => 
            array (
                'id' => 222,
                'consignment_id' => 170382,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            23 => 
            array (
                'id' => 223,
                'consignment_id' => 170383,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            24 => 
            array (
                'id' => 224,
                'consignment_id' => 170384,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            25 => 
            array (
                'id' => 225,
                'consignment_id' => 170385,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            26 => 
            array (
                'id' => 226,
                'consignment_id' => 170386,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            27 => 
            array (
                'id' => 227,
                'consignment_id' => 170387,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            28 => 
            array (
                'id' => 228,
                'consignment_id' => 170388,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            29 => 
            array (
                'id' => 229,
                'consignment_id' => 170389,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            30 => 
            array (
                'id' => 230,
                'consignment_id' => 170390,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            31 => 
            array (
                'id' => 231,
                'consignment_id' => 170391,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            32 => 
            array (
                'id' => 232,
                'consignment_id' => 170392,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            33 => 
            array (
                'id' => 233,
                'consignment_id' => 170393,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            34 => 
            array (
                'id' => 234,
                'consignment_id' => 170394,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            35 => 
            array (
                'id' => 235,
                'consignment_id' => 170395,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            36 => 
            array (
                'id' => 236,
                'consignment_id' => 170396,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            37 => 
            array (
                'id' => 237,
                'consignment_id' => 170397,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            38 => 
            array (
                'id' => 238,
                'consignment_id' => 170398,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            39 => 
            array (
                'id' => 239,
                'consignment_id' => 170399,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            40 => 
            array (
                'id' => 240,
                'consignment_id' => 170400,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            41 => 
            array (
                'id' => 241,
                'consignment_id' => 170401,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            42 => 
            array (
                'id' => 242,
                'consignment_id' => 170402,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            43 => 
            array (
                'id' => 243,
                'consignment_id' => 170403,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            44 => 
            array (
                'id' => 244,
                'consignment_id' => 170404,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            45 => 
            array (
                'id' => 245,
                'consignment_id' => 170405,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            46 => 
            array (
                'id' => 246,
                'consignment_id' => 170406,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            47 => 
            array (
                'id' => 247,
                'consignment_id' => 170407,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            48 => 
            array (
                'id' => 248,
                'consignment_id' => 170408,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            49 => 
            array (
                'id' => 249,
                'consignment_id' => 170409,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"dfads455","no_of_items":"4","item_value":"1","weight":"1","tariff_no":"","hscode":"32434drre","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            50 => 
            array (
                'id' => 250,
                'consignment_id' => 170410,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            51 => 
            array (
                'id' => 251,
                'consignment_id' => 170411,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"fdasf435","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123asd432","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            52 => 
            array (
                'id' => 252,
                'consignment_id' => 170412,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"tet","item_url":"","item_sku":"adsfat43r","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"erwdsaf","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            53 => 
            array (
                'id' => 253,
                'consignment_id' => 170413,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            54 => 
            array (
                'id' => 254,
                'consignment_id' => 170414,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            55 => 
            array (
                'id' => 255,
                'consignment_id' => 170415,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            56 => 
            array (
                'id' => 256,
                'consignment_id' => 170416,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            57 => 
            array (
                'id' => 257,
                'consignment_id' => 170417,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            58 => 
            array (
                'id' => 258,
                'consignment_id' => 170418,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            59 => 
            array (
                'id' => 259,
                'consignment_id' => 170419,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            60 => 
            array (
                'id' => 260,
                'consignment_id' => 170420,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            61 => 
            array (
                'id' => 261,
                'consignment_id' => 170421,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            62 => 
            array (
                'id' => 262,
                'consignment_id' => 170422,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test tesat","item_url":"","item_sku":"adsff435hadi","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"dfasf324","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            63 => 
            array (
                'id' => 264,
                'consignment_id' => 170423,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            64 => 
            array (
                'id' => 265,
                'consignment_id' => 170424,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"etst","item_url":"","item_sku":"teasdf324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"adsf424","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            65 => 
            array (
                'id' => 266,
                'consignment_id' => 170430,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            66 => 
            array (
                'id' => 267,
                'consignment_id' => 170432,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            67 => 
            array (
                'id' => 268,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            68 => 
            array (
                'id' => 269,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.sd.com","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123242","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            69 => 
            array (
                'id' => 270,
                'consignment_id' => 170433,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            70 => 
            array (
                'id' => 271,
                'consignment_id' => 170434,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            71 => 
            array (
                'id' => 272,
                'consignment_id' => 170435,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            72 => 
            array (
                'id' => 273,
                'consignment_id' => 170436,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            73 => 
            array (
                'id' => 274,
                'consignment_id' => 170437,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            74 => 
            array (
                'id' => 275,
                'consignment_id' => 170438,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            75 => 
            array (
                'id' => 276,
                'consignment_id' => 170439,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            76 => 
            array (
                'id' => 277,
                'consignment_id' => 170440,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            77 => 
            array (
                'id' => 278,
                'consignment_id' => 170441,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"books","item_url":"http:\\/\\/www.dad.com","item_sku":"24234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            78 => 
            array (
                'id' => 279,
                'consignment_id' => 170442,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            79 => 
            array (
                'id' => 280,
                'consignment_id' => 170443,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            80 => 
            array (
                'id' => 281,
                'consignment_id' => 170444,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            81 => 
            array (
                'id' => 282,
                'consignment_id' => 170445,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            82 => 
            array (
                'id' => 283,
                'consignment_id' => 170446,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Official Star Wars Storm Trooper Holdall Cabin Travel Gym Shoulder Duffel Bag","item_url":"ebay.com","weight":"1.00","item_sku":"3.04972E+11","no_of_items":"1","item_value":"9.99","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            83 => 
            array (
                'id' => 284,
                'consignment_id' => 170447,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Vivid Arts Zoo Pet Pals REALSTIC Baby Gorilla Home  Garden Decor Cute Gift","item_url":"ebay.com","weight":"1.00","item_sku":"3.0524E+11","no_of_items":"1","item_value":"15.99","hscode":"hs12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            84 => 
            array (
                'id' => 285,
                'consignment_id' => 170448,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2.4M EXTENDABLE PROP LINE HEAVY DUTY CLOTHES WASHING POLE OUTDOOR SUPPORT","item_url":"ebay.com","weight":"1.00","item_sku":"3.05015E+11","no_of_items":"2","item_value":"6.42","hscode":"hs12346","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            85 => 
            array (
                'id' => 286,
                'consignment_id' => 170449,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Clear Plastic Storage Box Stackable Boxes with Lids Office File use Home Kitchen[45 L,5]","item_url":"ebay.com","weight":"1.00","item_sku":"3.05014E+11","no_of_items":"2","item_value":"44.15","hscode":"hs12347","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            86 => 
            array (
                'id' => 287,
                'consignment_id' => 170450,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"12 Muffin Cupcake Tin Tray Non Stick Carbon Steel Baking Pan Yorkshire Pudding","item_url":"ebay.com","weight":"1.00","item_sku":"3.0495E+11","no_of_items":"1","item_value":"6.99","hscode":"hs12348","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            87 => 
            array (
                'id' => 288,
                'consignment_id' => 170451,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Woodland LED Aroma Tree Cylinder Electric Lamp Wax Melt Oil Burner GIFT BOXED[Colour Changing Grey]","item_url":"ebay.com","weight":"1.00","item_sku":"3.03498E+11","no_of_items":"1","item_value":"19.99","hscode":"hs12349","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            88 => 
            array (
                'id' => 289,
                'consignment_id' => 170452,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Luxury 2 Pack Hungarian 5* Goose Feather and Down Non-allergenic Pillows Cushion[4 Pack]","item_url":"ebay.com","weight":"1.00","item_sku":"3.04831E+11","no_of_items":"2","item_value":"47.52","hscode":"hs12350","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            89 => 
            array (
                'id' => 290,
                'consignment_id' => 170453,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2X Unibos Dolly Trolley Platform 200kg Wheeled Wooden Board Transporter- 59X29CM","item_url":"ebay.com","weight":"1.00","item_sku":"3.04923E+11","no_of_items":"2","item_value":"26.59","hscode":"hs12351","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            90 => 
            array (
                'id' => 291,
                'consignment_id' => 170454,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            91 => 
            array (
                'id' => 292,
                'consignment_id' => 170455,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"DSF","item_url":"HTTP:\\/\\/ww.dcom","item_sku":"4234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            92 => 
            array (
                'id' => 293,
                'consignment_id' => 170456,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            93 => 
            array (
                'id' => 294,
                'consignment_id' => 170457,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            94 => 
            array (
                'id' => 295,
                'consignment_id' => 170458,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            95 => 
            array (
                'id' => 296,
                'consignment_id' => 170459,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            96 => 
            array (
                'id' => 297,
                'consignment_id' => 170460,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            97 => 
            array (
                'id' => 298,
                'consignment_id' => 170461,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            98 => 
            array (
                'id' => 299,
                'consignment_id' => 170462,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            99 => 
            array (
                'id' => 300,
                'consignment_id' => 170463,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            100 => 
            array (
                'id' => 301,
                'consignment_id' => 170464,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/asfa.com","item_sku":"32424","no_of_items":"1","item_value":"1","weight":"11","tariff_no":"","hscode":"122333","manufacture_country_iso":""}]',
                'user_id' => 2341,
            ),
            101 => 
            array (
                'id' => 302,
                'consignment_id' => 170465,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"4455","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            102 => 
            array (
                'id' => 307,
                'consignment_id' => 170466,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/we.com","item_sku":"234324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            103 => 
            array (
                'id' => 308,
                'consignment_id' => 170467,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.com.co","item_sku":"234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            104 => 
            array (
                'id' => 12,
                'consignment_id' => 170278,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"13232","manufacture_country_iso":"AU"}]',
                'user_id' => 58,
            ),
            105 => 
            array (
                'id' => 14,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.ecom","item_sku":"23423","no_of_items":"11","item_value":"1","weight":"1","tariff_no":"","hscode":"132345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            106 => 
            array (
                'id' => 15,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            107 => 
            array (
                'id' => 17,
                'consignment_id' => 170275,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            108 => 
            array (
                'id' => 18,
                'consignment_id' => 170274,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            109 => 
            array (
                'id' => 19,
                'consignment_id' => 170273,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            110 => 
            array (
                'id' => 20,
                'consignment_id' => 170272,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            111 => 
            array (
                'id' => 21,
                'consignment_id' => 170271,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            112 => 
            array (
                'id' => 23,
                'consignment_id' => 170268,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            113 => 
            array (
                'id' => 24,
                'consignment_id' => 170270,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            114 => 
            array (
                'id' => 25,
                'consignment_id' => 170276,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            115 => 
            array (
                'id' => 26,
                'consignment_id' => 170277,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            116 => 
            array (
                'id' => 27,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            117 => 
            array (
                'id' => 28,
                'consignment_id' => 170280,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            118 => 
            array (
                'id' => 29,
                'consignment_id' => 170281,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            119 => 
            array (
                'id' => 30,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            120 => 
            array (
                'id' => 31,
                'consignment_id' => 170283,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            121 => 
            array (
                'id' => 32,
                'consignment_id' => 170284,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            122 => 
            array (
                'id' => 33,
                'consignment_id' => 170212,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            123 => 
            array (
                'id' => 34,
                'consignment_id' => 170213,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            124 => 
            array (
                'id' => 35,
                'consignment_id' => 170287,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            125 => 
            array (
                'id' => 36,
                'consignment_id' => 170288,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            126 => 
            array (
                'id' => 37,
                'consignment_id' => 170289,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            127 => 
            array (
                'id' => 38,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            128 => 
            array (
                'id' => 39,
                'consignment_id' => 170291,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            129 => 
            array (
                'id' => 40,
                'consignment_id' => 170292,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"1111","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1111","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            130 => 
            array (
                'id' => 41,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"","item_url":"","item_sku":"45545","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3444234","manufacture_country_iso":"AE"}]',
                'user_id' => 58,
            ),
            131 => 
            array (
                'id' => 42,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPLE","item_url":"http:\\/\\/www.goog.co.cuk","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"3242334","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            132 => 
            array (
                'id' => 43,
                'consignment_id' => 170279,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"QWERQ","item_url":"http:\\/\\/www.goog.co","item_sku":"QQWERQR","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234345","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            133 => 
            array (
                'id' => 44,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.goog.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            134 => 
            array (
                'id' => 45,
                'consignment_id' => 170296,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"SAMPL","item_url":"http:\\/\\/www.asdf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"23434","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            135 => 
            array (
                'id' => 50,
                'consignment_id' => 0,
                'session_id' => '9b478d41c9eef15d449460b0d1705c83',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"343","no_of_items":"33","item_value":"3","weight":"3","tariff_no":"","hscode":"333","manufacture_country_iso":"AI"}]',
                'user_id' => 58,
            ),
            136 => 
            array (
                'id' => 52,
                'consignment_id' => 170290,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.go.com","item_sku":"324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            137 => 
            array (
                'id' => 61,
                'consignment_id' => 170293,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdfa.com","item_sku":"2423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"1234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            138 => 
            array (
                'id' => 62,
                'consignment_id' => 170294,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2334,
            ),
            139 => 
            array (
                'id' => 63,
                'consignment_id' => 170295,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"http:\\/\\/www.asdfa.com","item_sku":"123213","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            140 => 
            array (
                'id' => 64,
                'consignment_id' => 170297,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            141 => 
            array (
                'id' => 65,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            142 => 
            array (
                'id' => 66,
                'consignment_id' => 170298,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            143 => 
            array (
                'id' => 67,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            144 => 
            array (
                'id' => 68,
                'consignment_id' => 170299,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            145 => 
            array (
                'id' => 69,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            146 => 
            array (
                'id' => 70,
                'consignment_id' => 170300,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            147 => 
            array (
                'id' => 71,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            148 => 
            array (
                'id' => 72,
                'consignment_id' => 170301,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            149 => 
            array (
                'id' => 73,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            150 => 
            array (
                'id' => 74,
                'consignment_id' => 170302,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2337,
            ),
            151 => 
            array (
                'id' => 75,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            152 => 
            array (
                'id' => 76,
                'consignment_id' => 170303,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            153 => 
            array (
                'id' => 77,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            154 => 
            array (
                'id' => 78,
                'consignment_id' => 170304,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            155 => 
            array (
                'id' => 79,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            156 => 
            array (
                'id' => 80,
                'consignment_id' => 170305,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            157 => 
            array (
                'id' => 81,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            158 => 
            array (
                'id' => 82,
                'consignment_id' => 170306,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            159 => 
            array (
                'id' => 83,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            160 => 
            array (
                'id' => 84,
                'consignment_id' => 170307,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[]',
                'user_id' => 2335,
            ),
            161 => 
            array (
                'id' => 85,
                'consignment_id' => 170308,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            162 => 
            array (
                'id' => 86,
                'consignment_id' => 170309,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            163 => 
            array (
                'id' => 87,
                'consignment_id' => 170310,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            164 => 
            array (
                'id' => 88,
                'consignment_id' => 170311,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.adf.com","item_sku":"234234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            165 => 
            array (
                'id' => 89,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            166 => 
            array (
                'id' => 90,
                'consignment_id' => 170312,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            167 => 
            array (
                'id' => 91,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            168 => 
            array (
                'id' => 92,
                'consignment_id' => 170313,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            169 => 
            array (
                'id' => 93,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            170 => 
            array (
                'id' => 94,
                'consignment_id' => 170314,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            171 => 
            array (
                'id' => 95,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            172 => 
            array (
                'id' => 96,
                'consignment_id' => 170315,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            173 => 
            array (
                'id' => 97,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            174 => 
            array (
                'id' => 98,
                'consignment_id' => 170316,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            175 => 
            array (
                'id' => 99,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            176 => 
            array (
                'id' => 100,
                'consignment_id' => 170317,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            177 => 
            array (
                'id' => 101,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            178 => 
            array (
                'id' => 102,
                'consignment_id' => 170318,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            179 => 
            array (
                'id' => 103,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            180 => 
            array (
                'id' => 104,
                'consignment_id' => 170319,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            181 => 
            array (
                'id' => 105,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            182 => 
            array (
                'id' => 106,
                'consignment_id' => 170320,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            183 => 
            array (
                'id' => 107,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            184 => 
            array (
                'id' => 108,
                'consignment_id' => 170321,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            185 => 
            array (
                'id' => 109,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            186 => 
            array (
                'id' => 110,
                'consignment_id' => 170322,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            187 => 
            array (
                'id' => 111,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            188 => 
            array (
                'id' => 112,
                'consignment_id' => 170323,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            189 => 
            array (
                'id' => 113,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            190 => 
            array (
                'id' => 114,
                'consignment_id' => 170324,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            191 => 
            array (
                'id' => 115,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            192 => 
            array (
                'id' => 116,
                'consignment_id' => 170325,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            193 => 
            array (
                'id' => 117,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            194 => 
            array (
                'id' => 118,
                'consignment_id' => 170326,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            195 => 
            array (
                'id' => 119,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            196 => 
            array (
                'id' => 120,
                'consignment_id' => 170327,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            197 => 
            array (
                'id' => 121,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            198 => 
            array (
                'id' => 122,
                'consignment_id' => 170328,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            199 => 
            array (
                'id' => 123,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            200 => 
            array (
                'id' => 124,
                'consignment_id' => 170329,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            201 => 
            array (
                'id' => 125,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            202 => 
            array (
                'id' => 126,
                'consignment_id' => 170330,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            203 => 
            array (
                'id' => 127,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            204 => 
            array (
                'id' => 128,
                'consignment_id' => 170331,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            205 => 
            array (
                'id' => 129,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            206 => 
            array (
                'id' => 130,
                'consignment_id' => 170332,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            207 => 
            array (
                'id' => 131,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            208 => 
            array (
                'id' => 132,
                'consignment_id' => 170333,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            209 => 
            array (
                'id' => 133,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            210 => 
            array (
                'id' => 134,
                'consignment_id' => 170334,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            211 => 
            array (
                'id' => 135,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","weight":"4.00","item_sku":"parcelsky","no_of_items":"4","item_value":"3.00","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            212 => 
            array (
                'id' => 136,
                'consignment_id' => 170335,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"parcel desc","item_url":"google.com","item_sku":"parcelsky","hscode":"hscode","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            213 => 
            array (
                'id' => 137,
                'consignment_id' => 170336,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.asdf.com","item_sku":"23434","no_of_items":"2","item_value":"2","weight":"2","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            214 => 
            array (
                'id' => 138,
                'consignment_id' => 170337,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2337,
            ),
            215 => 
            array (
                'id' => 139,
                'consignment_id' => 170338,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"asfaf","item_url":"","item_sku":"324234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            216 => 
            array (
                'id' => 140,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            217 => 
            array (
                'id' => 141,
                'consignment_id' => 170339,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            218 => 
            array (
                'id' => 142,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            219 => 
            array (
                'id' => 143,
                'consignment_id' => 170340,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            220 => 
            array (
                'id' => 144,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            221 => 
            array (
                'id' => 145,
                'consignment_id' => 170341,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            222 => 
            array (
                'id' => 146,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            223 => 
            array (
                'id' => 147,
                'consignment_id' => 170342,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2337,
            ),
            224 => 
            array (
                'id' => 148,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            225 => 
            array (
                'id' => 149,
                'consignment_id' => 170343,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            226 => 
            array (
                'id' => 150,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            227 => 
            array (
                'id' => 151,
                'consignment_id' => 170344,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            228 => 
            array (
                'id' => 152,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            229 => 
            array (
                'id' => 153,
                'consignment_id' => 170345,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            230 => 
            array (
                'id' => 154,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            231 => 
            array (
                'id' => 155,
                'consignment_id' => 170346,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            232 => 
            array (
                'id' => 156,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            233 => 
            array (
                'id' => 157,
                'consignment_id' => 170347,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            234 => 
            array (
                'id' => 158,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            235 => 
            array (
                'id' => 159,
                'consignment_id' => 170348,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"255"}]',
                'user_id' => 2335,
            ),
            236 => 
            array (
                'id' => 160,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            237 => 
            array (
                'id' => 161,
                'consignment_id' => 170349,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            238 => 
            array (
                'id' => 162,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            239 => 
            array (
                'id' => 163,
                'consignment_id' => 170350,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            240 => 
            array (
                'id' => 164,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            241 => 
            array (
                'id' => 165,
                'consignment_id' => 170351,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            242 => 
            array (
                'id' => 166,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            243 => 
            array (
                'id' => 167,
                'consignment_id' => 170352,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            244 => 
            array (
                'id' => 168,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            245 => 
            array (
                'id' => 169,
                'consignment_id' => 170353,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            246 => 
            array (
                'id' => 170,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"4","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            247 => 
            array (
                'id' => 171,
                'consignment_id' => 170354,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            248 => 
            array (
                'id' => 172,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            249 => 
            array (
                'id' => 173,
                'consignment_id' => 170355,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            250 => 
            array (
                'id' => 174,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            251 => 
            array (
                'id' => 175,
                'consignment_id' => 170356,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            252 => 
            array (
                'id' => 176,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            253 => 
            array (
                'id' => 177,
                'consignment_id' => 170357,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            254 => 
            array (
                'id' => 178,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            255 => 
            array (
                'id' => 179,
                'consignment_id' => 170358,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            256 => 
            array (
                'id' => 180,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            257 => 
            array (
                'id' => 181,
                'consignment_id' => 170359,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            258 => 
            array (
                'id' => 182,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            259 => 
            array (
                'id' => 183,
                'consignment_id' => 170360,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            260 => 
            array (
                'id' => 184,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            261 => 
            array (
                'id' => 185,
                'consignment_id' => 170361,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            262 => 
            array (
                'id' => 186,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            263 => 
            array (
                'id' => 187,
                'consignment_id' => 170362,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            264 => 
            array (
                'id' => 188,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            265 => 
            array (
                'id' => 189,
                'consignment_id' => 170363,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            266 => 
            array (
                'id' => 190,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            267 => 
            array (
                'id' => 191,
                'consignment_id' => 170364,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            268 => 
            array (
                'id' => 192,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            269 => 
            array (
                'id' => 193,
                'consignment_id' => 170365,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            270 => 
            array (
                'id' => 194,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            271 => 
            array (
                'id' => 195,
                'consignment_id' => 170366,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            272 => 
            array (
                'id' => 196,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            273 => 
            array (
                'id' => 197,
                'consignment_id' => 170367,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            274 => 
            array (
                'id' => 198,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            275 => 
            array (
                'id' => 199,
                'consignment_id' => 170368,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            276 => 
            array (
                'id' => 200,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            277 => 
            array (
                'id' => 201,
                'consignment_id' => 170369,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            278 => 
            array (
                'id' => 202,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            279 => 
            array (
                'id' => 203,
                'consignment_id' => 170370,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            280 => 
            array (
                'id' => 204,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            281 => 
            array (
                'id' => 205,
                'consignment_id' => 170371,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            282 => 
            array (
                'id' => 206,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            283 => 
            array (
                'id' => 207,
                'consignment_id' => 170372,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            284 => 
            array (
                'id' => 208,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            285 => 
            array (
                'id' => 209,
                'consignment_id' => 170373,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            286 => 
            array (
                'id' => 210,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            287 => 
            array (
                'id' => 211,
                'consignment_id' => 170374,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            288 => 
            array (
                'id' => 212,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            289 => 
            array (
                'id' => 213,
                'consignment_id' => 170375,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            290 => 
            array (
                'id' => 214,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            291 => 
            array (
                'id' => 215,
                'consignment_id' => 170376,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            292 => 
            array (
                'id' => 216,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","weight":"4.00","item_sku":"sample1","no_of_items":"2","item_value":"3.00","hscode":"12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            293 => 
            array (
                'id' => 217,
                'consignment_id' => 170377,
                'session_id' => '',
                'parcel_count' => 1,
                'item_detail' => '[{"item_description":"sample1","item_url":"sample1","item_sku":"sample1","hscode":"12342","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            294 => 
            array (
                'id' => 218,
                'consignment_id' => 170378,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            295 => 
            array (
                'id' => 219,
                'consignment_id' => 170379,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            296 => 
            array (
                'id' => 220,
                'consignment_id' => 170380,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            297 => 
            array (
                'id' => 221,
                'consignment_id' => 170381,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            298 => 
            array (
                'id' => 222,
                'consignment_id' => 170382,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            299 => 
            array (
                'id' => 223,
                'consignment_id' => 170383,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            300 => 
            array (
                'id' => 224,
                'consignment_id' => 170384,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2335,
            ),
            301 => 
            array (
                'id' => 225,
                'consignment_id' => 170385,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            302 => 
            array (
                'id' => 226,
                'consignment_id' => 170386,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            303 => 
            array (
                'id' => 227,
                'consignment_id' => 170387,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            304 => 
            array (
                'id' => 228,
                'consignment_id' => 170388,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            305 => 
            array (
                'id' => 229,
                'consignment_id' => 170389,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            306 => 
            array (
                'id' => 230,
                'consignment_id' => 170390,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            307 => 
            array (
                'id' => 231,
                'consignment_id' => 170391,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            308 => 
            array (
                'id' => 232,
                'consignment_id' => 170392,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            309 => 
            array (
                'id' => 233,
                'consignment_id' => 170393,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            310 => 
            array (
                'id' => 234,
                'consignment_id' => 170394,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            311 => 
            array (
                'id' => 235,
                'consignment_id' => 170395,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            312 => 
            array (
                'id' => 236,
                'consignment_id' => 170396,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            313 => 
            array (
                'id' => 237,
                'consignment_id' => 170397,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            314 => 
            array (
                'id' => 238,
                'consignment_id' => 170398,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            315 => 
            array (
                'id' => 239,
                'consignment_id' => 170399,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            316 => 
            array (
                'id' => 240,
                'consignment_id' => 170400,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            317 => 
            array (
                'id' => 241,
                'consignment_id' => 170401,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            318 => 
            array (
                'id' => 242,
                'consignment_id' => 170402,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            319 => 
            array (
                'id' => 243,
                'consignment_id' => 170403,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            320 => 
            array (
                'id' => 244,
                'consignment_id' => 170404,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2337,
            ),
            321 => 
            array (
                'id' => 245,
                'consignment_id' => 170405,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            322 => 
            array (
                'id' => 246,
                'consignment_id' => 170406,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            323 => 
            array (
                'id' => 247,
                'consignment_id' => 170407,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            324 => 
            array (
                'id' => 248,
                'consignment_id' => 170408,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2339,
            ),
            325 => 
            array (
                'id' => 249,
                'consignment_id' => 170409,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"dfads455","no_of_items":"4","item_value":"1","weight":"1","tariff_no":"","hscode":"32434drre","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            326 => 
            array (
                'id' => 250,
                'consignment_id' => 170410,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            327 => 
            array (
                'id' => 251,
                'consignment_id' => 170411,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"fdasf435","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123asd432","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            328 => 
            array (
                'id' => 252,
                'consignment_id' => 170412,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"tet","item_url":"","item_sku":"adsfat43r","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"erwdsaf","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            329 => 
            array (
                'id' => 253,
                'consignment_id' => 170413,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            330 => 
            array (
                'id' => 254,
                'consignment_id' => 170414,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            331 => 
            array (
                'id' => 255,
                'consignment_id' => 170415,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            332 => 
            array (
                'id' => 256,
                'consignment_id' => 170416,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            333 => 
            array (
                'id' => 257,
                'consignment_id' => 170417,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            334 => 
            array (
                'id' => 258,
                'consignment_id' => 170418,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            335 => 
            array (
                'id' => 259,
                'consignment_id' => 170419,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            336 => 
            array (
                'id' => 260,
                'consignment_id' => 170420,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            337 => 
            array (
                'id' => 261,
                'consignment_id' => 170421,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            338 => 
            array (
                'id' => 262,
                'consignment_id' => 170422,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test tesat","item_url":"","item_sku":"adsff435hadi","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"dfasf324","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            339 => 
            array (
                'id' => 264,
                'consignment_id' => 170423,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            340 => 
            array (
                'id' => 265,
                'consignment_id' => 170424,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"etst","item_url":"","item_sku":"teasdf324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"adsf424","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            341 => 
            array (
                'id' => 266,
                'consignment_id' => 170430,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            342 => 
            array (
                'id' => 267,
                'consignment_id' => 170432,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            343 => 
            array (
                'id' => 268,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"easdfa","item_url":"","item_sku":"rwedsaf","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"asddf31","manufacture_country_iso":"BS"}]',
                'user_id' => 58,
            ),
            344 => 
            array (
                'id' => 269,
                'consignment_id' => 170431,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.sd.com","item_sku":"23423","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123242","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            345 => 
            array (
                'id' => 270,
                'consignment_id' => 170433,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            346 => 
            array (
                'id' => 271,
                'consignment_id' => 170434,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            347 => 
            array (
                'id' => 272,
                'consignment_id' => 170435,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            348 => 
            array (
                'id' => 273,
                'consignment_id' => 170436,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 58,
            ),
            349 => 
            array (
                'id' => 274,
                'consignment_id' => 170437,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            350 => 
            array (
                'id' => 275,
                'consignment_id' => 170438,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            351 => 
            array (
                'id' => 276,
                'consignment_id' => 170439,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            352 => 
            array (
                'id' => 277,
                'consignment_id' => 170440,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2340,
            ),
            353 => 
            array (
                'id' => 278,
                'consignment_id' => 170441,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"books","item_url":"http:\\/\\/www.dad.com","item_sku":"24234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"134345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            354 => 
            array (
                'id' => 279,
                'consignment_id' => 170442,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            355 => 
            array (
                'id' => 280,
                'consignment_id' => 170443,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            356 => 
            array (
                'id' => 281,
                'consignment_id' => 170444,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            357 => 
            array (
                'id' => 282,
                'consignment_id' => 170445,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample1","item_url":"fb.com","weight":"4.00","item_sku":"sku123","no_of_items":"1","item_value":"3.00","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            358 => 
            array (
                'id' => 283,
                'consignment_id' => 170446,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Official Star Wars Storm Trooper Holdall Cabin Travel Gym Shoulder Duffel Bag","item_url":"ebay.com","weight":"1.00","item_sku":"3.04972E+11","no_of_items":"1","item_value":"9.99","hscode":"hs12344","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            359 => 
            array (
                'id' => 284,
                'consignment_id' => 170447,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Vivid Arts Zoo Pet Pals REALSTIC Baby Gorilla Home  Garden Decor Cute Gift","item_url":"ebay.com","weight":"1.00","item_sku":"3.0524E+11","no_of_items":"1","item_value":"15.99","hscode":"hs12345","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            360 => 
            array (
                'id' => 285,
                'consignment_id' => 170448,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2.4M EXTENDABLE PROP LINE HEAVY DUTY CLOTHES WASHING POLE OUTDOOR SUPPORT","item_url":"ebay.com","weight":"1.00","item_sku":"3.05015E+11","no_of_items":"2","item_value":"6.42","hscode":"hs12346","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            361 => 
            array (
                'id' => 286,
                'consignment_id' => 170449,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Clear Plastic Storage Box Stackable Boxes with Lids Office File use Home Kitchen[45 L,5]","item_url":"ebay.com","weight":"1.00","item_sku":"3.05014E+11","no_of_items":"2","item_value":"44.15","hscode":"hs12347","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            362 => 
            array (
                'id' => 287,
                'consignment_id' => 170450,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"12 Muffin Cupcake Tin Tray Non Stick Carbon Steel Baking Pan Yorkshire Pudding","item_url":"ebay.com","weight":"1.00","item_sku":"3.0495E+11","no_of_items":"1","item_value":"6.99","hscode":"hs12348","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            363 => 
            array (
                'id' => 288,
                'consignment_id' => 170451,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Woodland LED Aroma Tree Cylinder Electric Lamp Wax Melt Oil Burner GIFT BOXED[Colour Changing Grey]","item_url":"ebay.com","weight":"1.00","item_sku":"3.03498E+11","no_of_items":"1","item_value":"19.99","hscode":"hs12349","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            364 => 
            array (
                'id' => 289,
                'consignment_id' => 170452,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"Luxury 2 Pack Hungarian 5* Goose Feather and Down Non-allergenic Pillows Cushion[4 Pack]","item_url":"ebay.com","weight":"1.00","item_sku":"3.04831E+11","no_of_items":"2","item_value":"47.52","hscode":"hs12350","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            365 => 
            array (
                'id' => 290,
                'consignment_id' => 170453,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"2X Unibos Dolly Trolley Platform 200kg Wheeled Wooden Board Transporter- 59X29CM","item_url":"ebay.com","weight":"1.00","item_sku":"3.04923E+11","no_of_items":"2","item_value":"26.59","hscode":"hs12351","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            366 => 
            array (
                'id' => 291,
                'consignment_id' => 170454,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            367 => 
            array (
                'id' => 292,
                'consignment_id' => 170455,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"DSF","item_url":"HTTP:\\/\\/ww.dcom","item_sku":"4234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            368 => 
            array (
                'id' => 293,
                'consignment_id' => 170456,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            369 => 
            array (
                'id' => 294,
                'consignment_id' => 170457,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            370 => 
            array (
                'id' => 295,
                'consignment_id' => 170458,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            371 => 
            array (
                'id' => 296,
                'consignment_id' => 170459,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            372 => 
            array (
                'id' => 297,
                'consignment_id' => 170460,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            373 => 
            array (
                'id' => 298,
                'consignment_id' => 170461,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            374 => 
            array (
                'id' => 299,
                'consignment_id' => 170462,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            375 => 
            array (
                'id' => 300,
                'consignment_id' => 170463,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => 'null',
                'user_id' => 2341,
            ),
            376 => 
            array (
                'id' => 301,
                'consignment_id' => 170464,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/asfa.com","item_sku":"32424","no_of_items":"1","item_value":"1","weight":"11","tariff_no":"","hscode":"122333","manufacture_country_iso":""}]',
                'user_id' => 2341,
            ),
            377 => 
            array (
                'id' => 302,
                'consignment_id' => 170465,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"test","item_url":"","item_sku":"3434","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"4455","manufacture_country_iso":"GB"}]',
                'user_id' => 58,
            ),
            378 => 
            array (
                'id' => 307,
                'consignment_id' => 170466,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/we.com","item_sku":"234324","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"234234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
            379 => 
            array (
                'id' => 308,
                'consignment_id' => 170467,
                'session_id' => '',
                'parcel_count' => 0,
                'item_detail' => '[{"item_description":"sample","item_url":"http:\\/\\/www.com.co","item_sku":"234","no_of_items":"1","item_value":"1","weight":"1","tariff_no":"","hscode":"123234","manufacture_country_iso":"GB"}]',
                'user_id' => 2341,
            ),
        ));
        
        
    }
}