<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TariffsPricingRulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tariffs_pricing_rules')->delete();
        
        \DB::table('tariffs_pricing_rules')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tariff_id' => 240393,
                'name' => 'UKMELL TARIFF CUSTOMER',
                'date_added' => '2018-11-12 16:11:40',
                'added_by' => 58,
                'date_updated' => '2018-11-12 10:11:40',
                'updated_by' => 58,
            ),
            1 => 
            array (
                'id' => 2,
                'tariff_id' => 240397,
                'name' => 'kazy',
                'date_added' => '2018-11-26 02:26:26',
                'added_by' => 58,
                'date_updated' => '2018-11-25 20:26:26',
                'updated_by' => 58,
            ),
            2 => 
            array (
                'id' => 3,
                'tariff_id' => 240409,
                'name' => 'najam rule',
                'date_added' => '2018-12-03 17:37:39',
                'added_by' => 2170,
                'date_updated' => '2018-12-03 11:37:39',
                'updated_by' => 2170,
            ),
            3 => 
            array (
                'id' => 4,
                'tariff_id' => 240413,
                'name' => 'SUE',
                'date_added' => '2018-12-03 23:16:26',
                'added_by' => 2112,
                'date_updated' => '2018-12-03 17:16:26',
                'updated_by' => 2112,
            ),
            4 => 
            array (
                'id' => 5,
                'tariff_id' => 240421,
                'name' => 'BRTITAL_TEST rules',
                'date_added' => '2018-12-06 17:50:39',
                'added_by' => 58,
                'date_updated' => '2018-12-06 11:50:39',
                'updated_by' => 58,
            ),
            5 => 
            array (
                'id' => 6,
                'tariff_id' => 240443,
                'name' => 'test',
                'date_added' => '2018-12-18 19:30:57',
                'added_by' => 58,
                'date_updated' => '2018-12-18 13:30:57',
                'updated_by' => 58,
            ),
            6 => 
            array (
                'id' => 7,
                'tariff_id' => 240530,
                'name' => 'Yodel Tariff New Rule',
                'date_added' => '2019-05-30 17:18:54',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:18:54',
                'updated_by' => 2182,
            ),
            7 => 
            array (
                'id' => 8,
                'tariff_id' => 240532,
                'name' => 'Swe Cus 2 Tariff',
                'date_added' => '2019-05-30 17:34:34',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:34:34',
                'updated_by' => 2182,
            ),
            8 => 
            array (
                'id' => 9,
                'tariff_id' => 240537,
                'name' => 'sue rules',
                'date_added' => '2019-05-31 17:01:45',
                'added_by' => 58,
                'date_updated' => '2019-05-31 12:01:45',
                'updated_by' => 58,
            ),
            9 => 
            array (
                'id' => 10,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:45:10',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:45:10',
                'updated_by' => 58,
            ),
            10 => 
            array (
                'id' => 11,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:49:07',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:49:07',
                'updated_by' => 58,
            ),
            11 => 
            array (
                'id' => 12,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-03 21:17:18',
                'added_by' => 58,
                'date_updated' => '2019-07-03 16:17:18',
                'updated_by' => 58,
            ),
            12 => 
            array (
                'id' => 13,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-31 15:28:33',
                'added_by' => 58,
                'date_updated' => '2019-07-31 10:28:33',
                'updated_by' => 58,
            ),
            13 => 
            array (
                'id' => 14,
                'tariff_id' => 240561,
                'name' => 'PA POST Rule for Home 24',
                'date_added' => '2019-08-28 17:36:04',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:04',
                'updated_by' => 2252,
            ),
            14 => 
            array (
                'id' => 15,
                'tariff_id' => 240561,
                'name' => '24 PA Post  rules',
                'date_added' => '2019-08-28 17:36:10',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:10',
                'updated_by' => 2252,
            ),
            15 => 
            array (
                'id' => 16,
                'tariff_id' => 240566,
                'name' => 'shabbir_customer rules',
                'date_added' => '2019-08-30 22:00:09',
                'added_by' => 58,
                'date_updated' => '2019-08-30 17:00:09',
                'updated_by' => 58,
            ),
            16 => 
            array (
                'id' => 17,
                'tariff_id' => 240571,
                'name' => 'Increment 1',
                'date_added' => '2019-09-04 22:59:06',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:06',
                'updated_by' => 2258,
            ),
            17 => 
            array (
                'id' => 18,
                'tariff_id' => 240571,
                'name' => 'sdv',
                'date_added' => '2019-09-04 22:59:56',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:56',
                'updated_by' => 2258,
            ),
            18 => 
            array (
                'id' => 19,
                'tariff_id' => 240574,
                'name' => 'USD ACC T1 csv 0.5 Margin',
                'date_added' => '2019-09-05 17:05:17',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:05:17',
                'updated_by' => 2282,
            ),
            19 => 
            array (
                'id' => 20,
                'tariff_id' => 240574,
                'name' => 'USD Account T1 csv 1 Margin Fixed ',
                'date_added' => '2019-09-05 17:11:39',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:11:39',
                'updated_by' => 2282,
            ),
            20 => 
            array (
                'id' => 21,
                'tariff_id' => 240577,
                'name' => 'EURACC Tariff 1 Sup rules',
                'date_added' => '2019-09-06 18:23:35',
                'added_by' => 2282,
                'date_updated' => '2019-09-06 13:23:35',
                'updated_by' => 2282,
            ),
            21 => 
            array (
                'id' => 22,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD RUle 1',
                'date_added' => '2019-09-11 15:37:14',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:14',
                'updated_by' => 2300,
            ),
            22 => 
            array (
                'id' => 23,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD rules',
                'date_added' => '2019-09-11 15:37:34',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:34',
                'updated_by' => 2300,
            ),
            23 => 
            array (
                'id' => 24,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD Rule 2',
                'date_added' => '2019-09-11 15:38:54',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:38:54',
                'updated_by' => 2300,
            ),
            24 => 
            array (
                'id' => 25,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD	 rules',
                'date_added' => '2019-09-11 15:39:25',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:25',
                'updated_by' => 2300,
            ),
            25 => 
            array (
                'id' => 26,
                'tariff_id' => 240603,
                'name' => 'Yodel 72 MINI USD	 rules',
                'date_added' => '2019-09-11 15:39:53',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:53',
                'updated_by' => 2300,
            ),
            26 => 
            array (
                'id' => 27,
                'tariff_id' => 240607,
                'name' => 'BRT Italy RUle 1',
                'date_added' => '2019-09-11 15:42:41',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:42:41',
                'updated_by' => 2300,
            ),
            27 => 
            array (
                'id' => 28,
                'tariff_id' => 240608,
                'name' => 'OWE PRODUCT RULE 1',
                'date_added' => '2019-09-11 15:43:21',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:43:21',
                'updated_by' => 2300,
            ),
            28 => 
            array (
                'id' => 29,
                'tariff_id' => 240632,
                'name' => 'NaN Rule',
                'date_added' => '2019-11-06 23:50:07',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:50:07',
                'updated_by' => 58,
            ),
            29 => 
            array (
                'id' => 30,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:22',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:22',
                'updated_by' => 58,
            ),
            30 => 
            array (
                'id' => 31,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:55',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:55',
                'updated_by' => 58,
            ),
            31 => 
            array (
                'id' => 32,
                'tariff_id' => 240637,
                'name' => 'Sweden Reg Sup T rules',
                'date_added' => '2019-11-08 19:10:07',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:10:07',
                'updated_by' => 58,
            ),
            32 => 
            array (
                'id' => 33,
                'tariff_id' => 240639,
                'name' => 'Swe Tariff rules',
                'date_added' => '2019-11-08 19:29:33',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:29:33',
                'updated_by' => 58,
            ),
            33 => 
            array (
                'id' => 34,
                'tariff_id' => 240645,
                'name' => 'DE dpd rules',
                'date_added' => '2019-11-13 00:37:11',
                'added_by' => 58,
                'date_updated' => '2019-11-12 18:37:11',
                'updated_by' => 58,
            ),
            34 => 
            array (
                'id' => 35,
                'tariff_id' => 240656,
                'name' => 'yasmin',
                'date_added' => '2019-11-27 22:49:43',
                'added_by' => 2295,
                'date_updated' => '2019-11-27 16:49:43',
                'updated_by' => 2295,
            ),
            35 => 
            array (
                'id' => 36,
                'tariff_id' => 240644,
                'name' => 'OWE Pro rules',
                'date_added' => '2019-11-28 19:23:07',
                'added_by' => 58,
                'date_updated' => '2019-11-28 13:23:07',
                'updated_by' => 58,
            ),
            36 => 
            array (
                'id' => 37,
                'tariff_id' => 240642,
                'name' => 'test',
                'date_added' => '2020-02-28 18:41:04',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:41:04',
                'updated_by' => 58,
            ),
            37 => 
            array (
                'id' => 38,
                'tariff_id' => 240667,
                'name' => 'Supplier compare rules',
                'date_added' => '2020-02-28 18:45:23',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:45:23',
                'updated_by' => 58,
            ),
            38 => 
            array (
                'id' => 1,
                'tariff_id' => 240393,
                'name' => 'UKMELL TARIFF CUSTOMER',
                'date_added' => '2018-11-12 16:11:40',
                'added_by' => 58,
                'date_updated' => '2018-11-12 10:11:40',
                'updated_by' => 58,
            ),
            39 => 
            array (
                'id' => 2,
                'tariff_id' => 240397,
                'name' => 'kazy',
                'date_added' => '2018-11-26 02:26:26',
                'added_by' => 58,
                'date_updated' => '2018-11-25 20:26:26',
                'updated_by' => 58,
            ),
            40 => 
            array (
                'id' => 3,
                'tariff_id' => 240409,
                'name' => 'najam rule',
                'date_added' => '2018-12-03 17:37:39',
                'added_by' => 2170,
                'date_updated' => '2018-12-03 11:37:39',
                'updated_by' => 2170,
            ),
            41 => 
            array (
                'id' => 4,
                'tariff_id' => 240413,
                'name' => 'SUE',
                'date_added' => '2018-12-03 23:16:26',
                'added_by' => 2112,
                'date_updated' => '2018-12-03 17:16:26',
                'updated_by' => 2112,
            ),
            42 => 
            array (
                'id' => 5,
                'tariff_id' => 240421,
                'name' => 'BRTITAL_TEST rules',
                'date_added' => '2018-12-06 17:50:39',
                'added_by' => 58,
                'date_updated' => '2018-12-06 11:50:39',
                'updated_by' => 58,
            ),
            43 => 
            array (
                'id' => 6,
                'tariff_id' => 240443,
                'name' => 'test',
                'date_added' => '2018-12-18 19:30:57',
                'added_by' => 58,
                'date_updated' => '2018-12-18 13:30:57',
                'updated_by' => 58,
            ),
            44 => 
            array (
                'id' => 7,
                'tariff_id' => 240530,
                'name' => 'Yodel Tariff New Rule',
                'date_added' => '2019-05-30 17:18:54',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:18:54',
                'updated_by' => 2182,
            ),
            45 => 
            array (
                'id' => 8,
                'tariff_id' => 240532,
                'name' => 'Swe Cus 2 Tariff',
                'date_added' => '2019-05-30 17:34:34',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:34:34',
                'updated_by' => 2182,
            ),
            46 => 
            array (
                'id' => 9,
                'tariff_id' => 240537,
                'name' => 'sue rules',
                'date_added' => '2019-05-31 17:01:45',
                'added_by' => 58,
                'date_updated' => '2019-05-31 12:01:45',
                'updated_by' => 58,
            ),
            47 => 
            array (
                'id' => 10,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:45:10',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:45:10',
                'updated_by' => 58,
            ),
            48 => 
            array (
                'id' => 11,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:49:07',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:49:07',
                'updated_by' => 58,
            ),
            49 => 
            array (
                'id' => 12,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-03 21:17:18',
                'added_by' => 58,
                'date_updated' => '2019-07-03 16:17:18',
                'updated_by' => 58,
            ),
            50 => 
            array (
                'id' => 13,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-31 15:28:33',
                'added_by' => 58,
                'date_updated' => '2019-07-31 10:28:33',
                'updated_by' => 58,
            ),
            51 => 
            array (
                'id' => 14,
                'tariff_id' => 240561,
                'name' => 'PA POST Rule for Home 24',
                'date_added' => '2019-08-28 17:36:04',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:04',
                'updated_by' => 2252,
            ),
            52 => 
            array (
                'id' => 15,
                'tariff_id' => 240561,
                'name' => '24 PA Post  rules',
                'date_added' => '2019-08-28 17:36:10',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:10',
                'updated_by' => 2252,
            ),
            53 => 
            array (
                'id' => 16,
                'tariff_id' => 240566,
                'name' => 'shabbir_customer rules',
                'date_added' => '2019-08-30 22:00:09',
                'added_by' => 58,
                'date_updated' => '2019-08-30 17:00:09',
                'updated_by' => 58,
            ),
            54 => 
            array (
                'id' => 17,
                'tariff_id' => 240571,
                'name' => 'Increment 1',
                'date_added' => '2019-09-04 22:59:06',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:06',
                'updated_by' => 2258,
            ),
            55 => 
            array (
                'id' => 18,
                'tariff_id' => 240571,
                'name' => 'sdv',
                'date_added' => '2019-09-04 22:59:56',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:56',
                'updated_by' => 2258,
            ),
            56 => 
            array (
                'id' => 19,
                'tariff_id' => 240574,
                'name' => 'USD ACC T1 csv 0.5 Margin',
                'date_added' => '2019-09-05 17:05:17',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:05:17',
                'updated_by' => 2282,
            ),
            57 => 
            array (
                'id' => 20,
                'tariff_id' => 240574,
                'name' => 'USD Account T1 csv 1 Margin Fixed ',
                'date_added' => '2019-09-05 17:11:39',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:11:39',
                'updated_by' => 2282,
            ),
            58 => 
            array (
                'id' => 21,
                'tariff_id' => 240577,
                'name' => 'EURACC Tariff 1 Sup rules',
                'date_added' => '2019-09-06 18:23:35',
                'added_by' => 2282,
                'date_updated' => '2019-09-06 13:23:35',
                'updated_by' => 2282,
            ),
            59 => 
            array (
                'id' => 22,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD RUle 1',
                'date_added' => '2019-09-11 15:37:14',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:14',
                'updated_by' => 2300,
            ),
            60 => 
            array (
                'id' => 23,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD rules',
                'date_added' => '2019-09-11 15:37:34',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:34',
                'updated_by' => 2300,
            ),
            61 => 
            array (
                'id' => 24,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD Rule 2',
                'date_added' => '2019-09-11 15:38:54',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:38:54',
                'updated_by' => 2300,
            ),
            62 => 
            array (
                'id' => 25,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD	 rules',
                'date_added' => '2019-09-11 15:39:25',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:25',
                'updated_by' => 2300,
            ),
            63 => 
            array (
                'id' => 26,
                'tariff_id' => 240603,
                'name' => 'Yodel 72 MINI USD	 rules',
                'date_added' => '2019-09-11 15:39:53',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:53',
                'updated_by' => 2300,
            ),
            64 => 
            array (
                'id' => 27,
                'tariff_id' => 240607,
                'name' => 'BRT Italy RUle 1',
                'date_added' => '2019-09-11 15:42:41',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:42:41',
                'updated_by' => 2300,
            ),
            65 => 
            array (
                'id' => 28,
                'tariff_id' => 240608,
                'name' => 'OWE PRODUCT RULE 1',
                'date_added' => '2019-09-11 15:43:21',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:43:21',
                'updated_by' => 2300,
            ),
            66 => 
            array (
                'id' => 29,
                'tariff_id' => 240632,
                'name' => 'NaN Rule',
                'date_added' => '2019-11-06 23:50:07',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:50:07',
                'updated_by' => 58,
            ),
            67 => 
            array (
                'id' => 30,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:22',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:22',
                'updated_by' => 58,
            ),
            68 => 
            array (
                'id' => 31,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:55',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:55',
                'updated_by' => 58,
            ),
            69 => 
            array (
                'id' => 32,
                'tariff_id' => 240637,
                'name' => 'Sweden Reg Sup T rules',
                'date_added' => '2019-11-08 19:10:07',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:10:07',
                'updated_by' => 58,
            ),
            70 => 
            array (
                'id' => 33,
                'tariff_id' => 240639,
                'name' => 'Swe Tariff rules',
                'date_added' => '2019-11-08 19:29:33',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:29:33',
                'updated_by' => 58,
            ),
            71 => 
            array (
                'id' => 34,
                'tariff_id' => 240645,
                'name' => 'DE dpd rules',
                'date_added' => '2019-11-13 00:37:11',
                'added_by' => 58,
                'date_updated' => '2019-11-12 18:37:11',
                'updated_by' => 58,
            ),
            72 => 
            array (
                'id' => 35,
                'tariff_id' => 240656,
                'name' => 'yasmin',
                'date_added' => '2019-11-27 22:49:43',
                'added_by' => 2295,
                'date_updated' => '2019-11-27 16:49:43',
                'updated_by' => 2295,
            ),
            73 => 
            array (
                'id' => 36,
                'tariff_id' => 240644,
                'name' => 'OWE Pro rules',
                'date_added' => '2019-11-28 19:23:07',
                'added_by' => 58,
                'date_updated' => '2019-11-28 13:23:07',
                'updated_by' => 58,
            ),
            74 => 
            array (
                'id' => 37,
                'tariff_id' => 240642,
                'name' => 'test',
                'date_added' => '2020-02-28 18:41:04',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:41:04',
                'updated_by' => 58,
            ),
            75 => 
            array (
                'id' => 38,
                'tariff_id' => 240667,
                'name' => 'Supplier compare rules',
                'date_added' => '2020-02-28 18:45:23',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:45:23',
                'updated_by' => 58,
            ),
            76 => 
            array (
                'id' => 1,
                'tariff_id' => 240393,
                'name' => 'UKMELL TARIFF CUSTOMER',
                'date_added' => '2018-11-12 16:11:40',
                'added_by' => 58,
                'date_updated' => '2018-11-12 10:11:40',
                'updated_by' => 58,
            ),
            77 => 
            array (
                'id' => 2,
                'tariff_id' => 240397,
                'name' => 'kazy',
                'date_added' => '2018-11-26 02:26:26',
                'added_by' => 58,
                'date_updated' => '2018-11-25 20:26:26',
                'updated_by' => 58,
            ),
            78 => 
            array (
                'id' => 3,
                'tariff_id' => 240409,
                'name' => 'najam rule',
                'date_added' => '2018-12-03 17:37:39',
                'added_by' => 2170,
                'date_updated' => '2018-12-03 11:37:39',
                'updated_by' => 2170,
            ),
            79 => 
            array (
                'id' => 4,
                'tariff_id' => 240413,
                'name' => 'SUE',
                'date_added' => '2018-12-03 23:16:26',
                'added_by' => 2112,
                'date_updated' => '2018-12-03 17:16:26',
                'updated_by' => 2112,
            ),
            80 => 
            array (
                'id' => 5,
                'tariff_id' => 240421,
                'name' => 'BRTITAL_TEST rules',
                'date_added' => '2018-12-06 17:50:39',
                'added_by' => 58,
                'date_updated' => '2018-12-06 11:50:39',
                'updated_by' => 58,
            ),
            81 => 
            array (
                'id' => 6,
                'tariff_id' => 240443,
                'name' => 'test',
                'date_added' => '2018-12-18 19:30:57',
                'added_by' => 58,
                'date_updated' => '2018-12-18 13:30:57',
                'updated_by' => 58,
            ),
            82 => 
            array (
                'id' => 7,
                'tariff_id' => 240530,
                'name' => 'Yodel Tariff New Rule',
                'date_added' => '2019-05-30 17:18:54',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:18:54',
                'updated_by' => 2182,
            ),
            83 => 
            array (
                'id' => 8,
                'tariff_id' => 240532,
                'name' => 'Swe Cus 2 Tariff',
                'date_added' => '2019-05-30 17:34:34',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:34:34',
                'updated_by' => 2182,
            ),
            84 => 
            array (
                'id' => 9,
                'tariff_id' => 240537,
                'name' => 'sue rules',
                'date_added' => '2019-05-31 17:01:45',
                'added_by' => 58,
                'date_updated' => '2019-05-31 12:01:45',
                'updated_by' => 58,
            ),
            85 => 
            array (
                'id' => 10,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:45:10',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:45:10',
                'updated_by' => 58,
            ),
            86 => 
            array (
                'id' => 11,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:49:07',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:49:07',
                'updated_by' => 58,
            ),
            87 => 
            array (
                'id' => 12,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-03 21:17:18',
                'added_by' => 58,
                'date_updated' => '2019-07-03 16:17:18',
                'updated_by' => 58,
            ),
            88 => 
            array (
                'id' => 13,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-31 15:28:33',
                'added_by' => 58,
                'date_updated' => '2019-07-31 10:28:33',
                'updated_by' => 58,
            ),
            89 => 
            array (
                'id' => 14,
                'tariff_id' => 240561,
                'name' => 'PA POST Rule for Home 24',
                'date_added' => '2019-08-28 17:36:04',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:04',
                'updated_by' => 2252,
            ),
            90 => 
            array (
                'id' => 15,
                'tariff_id' => 240561,
                'name' => '24 PA Post  rules',
                'date_added' => '2019-08-28 17:36:10',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:10',
                'updated_by' => 2252,
            ),
            91 => 
            array (
                'id' => 16,
                'tariff_id' => 240566,
                'name' => 'shabbir_customer rules',
                'date_added' => '2019-08-30 22:00:09',
                'added_by' => 58,
                'date_updated' => '2019-08-30 17:00:09',
                'updated_by' => 58,
            ),
            92 => 
            array (
                'id' => 17,
                'tariff_id' => 240571,
                'name' => 'Increment 1',
                'date_added' => '2019-09-04 22:59:06',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:06',
                'updated_by' => 2258,
            ),
            93 => 
            array (
                'id' => 18,
                'tariff_id' => 240571,
                'name' => 'sdv',
                'date_added' => '2019-09-04 22:59:56',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:56',
                'updated_by' => 2258,
            ),
            94 => 
            array (
                'id' => 19,
                'tariff_id' => 240574,
                'name' => 'USD ACC T1 csv 0.5 Margin',
                'date_added' => '2019-09-05 17:05:17',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:05:17',
                'updated_by' => 2282,
            ),
            95 => 
            array (
                'id' => 20,
                'tariff_id' => 240574,
                'name' => 'USD Account T1 csv 1 Margin Fixed ',
                'date_added' => '2019-09-05 17:11:39',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:11:39',
                'updated_by' => 2282,
            ),
            96 => 
            array (
                'id' => 21,
                'tariff_id' => 240577,
                'name' => 'EURACC Tariff 1 Sup rules',
                'date_added' => '2019-09-06 18:23:35',
                'added_by' => 2282,
                'date_updated' => '2019-09-06 13:23:35',
                'updated_by' => 2282,
            ),
            97 => 
            array (
                'id' => 22,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD RUle 1',
                'date_added' => '2019-09-11 15:37:14',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:14',
                'updated_by' => 2300,
            ),
            98 => 
            array (
                'id' => 23,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD rules',
                'date_added' => '2019-09-11 15:37:34',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:34',
                'updated_by' => 2300,
            ),
            99 => 
            array (
                'id' => 24,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD Rule 2',
                'date_added' => '2019-09-11 15:38:54',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:38:54',
                'updated_by' => 2300,
            ),
            100 => 
            array (
                'id' => 25,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD	 rules',
                'date_added' => '2019-09-11 15:39:25',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:25',
                'updated_by' => 2300,
            ),
            101 => 
            array (
                'id' => 26,
                'tariff_id' => 240603,
                'name' => 'Yodel 72 MINI USD	 rules',
                'date_added' => '2019-09-11 15:39:53',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:53',
                'updated_by' => 2300,
            ),
            102 => 
            array (
                'id' => 27,
                'tariff_id' => 240607,
                'name' => 'BRT Italy RUle 1',
                'date_added' => '2019-09-11 15:42:41',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:42:41',
                'updated_by' => 2300,
            ),
            103 => 
            array (
                'id' => 28,
                'tariff_id' => 240608,
                'name' => 'OWE PRODUCT RULE 1',
                'date_added' => '2019-09-11 15:43:21',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:43:21',
                'updated_by' => 2300,
            ),
            104 => 
            array (
                'id' => 29,
                'tariff_id' => 240632,
                'name' => 'NaN Rule',
                'date_added' => '2019-11-06 23:50:07',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:50:07',
                'updated_by' => 58,
            ),
            105 => 
            array (
                'id' => 30,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:22',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:22',
                'updated_by' => 58,
            ),
            106 => 
            array (
                'id' => 31,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:55',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:55',
                'updated_by' => 58,
            ),
            107 => 
            array (
                'id' => 32,
                'tariff_id' => 240637,
                'name' => 'Sweden Reg Sup T rules',
                'date_added' => '2019-11-08 19:10:07',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:10:07',
                'updated_by' => 58,
            ),
            108 => 
            array (
                'id' => 33,
                'tariff_id' => 240639,
                'name' => 'Swe Tariff rules',
                'date_added' => '2019-11-08 19:29:33',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:29:33',
                'updated_by' => 58,
            ),
            109 => 
            array (
                'id' => 34,
                'tariff_id' => 240645,
                'name' => 'DE dpd rules',
                'date_added' => '2019-11-13 00:37:11',
                'added_by' => 58,
                'date_updated' => '2019-11-12 18:37:11',
                'updated_by' => 58,
            ),
            110 => 
            array (
                'id' => 35,
                'tariff_id' => 240656,
                'name' => 'yasmin',
                'date_added' => '2019-11-27 22:49:43',
                'added_by' => 2295,
                'date_updated' => '2019-11-27 16:49:43',
                'updated_by' => 2295,
            ),
            111 => 
            array (
                'id' => 36,
                'tariff_id' => 240644,
                'name' => 'OWE Pro rules',
                'date_added' => '2019-11-28 19:23:07',
                'added_by' => 58,
                'date_updated' => '2019-11-28 13:23:07',
                'updated_by' => 58,
            ),
            112 => 
            array (
                'id' => 37,
                'tariff_id' => 240642,
                'name' => 'test',
                'date_added' => '2020-02-28 18:41:04',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:41:04',
                'updated_by' => 58,
            ),
            113 => 
            array (
                'id' => 38,
                'tariff_id' => 240667,
                'name' => 'Supplier compare rules',
                'date_added' => '2020-02-28 18:45:23',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:45:23',
                'updated_by' => 58,
            ),
            114 => 
            array (
                'id' => 1,
                'tariff_id' => 240393,
                'name' => 'UKMELL TARIFF CUSTOMER',
                'date_added' => '2018-11-12 16:11:40',
                'added_by' => 58,
                'date_updated' => '2018-11-12 10:11:40',
                'updated_by' => 58,
            ),
            115 => 
            array (
                'id' => 2,
                'tariff_id' => 240397,
                'name' => 'kazy',
                'date_added' => '2018-11-26 02:26:26',
                'added_by' => 58,
                'date_updated' => '2018-11-25 20:26:26',
                'updated_by' => 58,
            ),
            116 => 
            array (
                'id' => 3,
                'tariff_id' => 240409,
                'name' => 'najam rule',
                'date_added' => '2018-12-03 17:37:39',
                'added_by' => 2170,
                'date_updated' => '2018-12-03 11:37:39',
                'updated_by' => 2170,
            ),
            117 => 
            array (
                'id' => 4,
                'tariff_id' => 240413,
                'name' => 'SUE',
                'date_added' => '2018-12-03 23:16:26',
                'added_by' => 2112,
                'date_updated' => '2018-12-03 17:16:26',
                'updated_by' => 2112,
            ),
            118 => 
            array (
                'id' => 5,
                'tariff_id' => 240421,
                'name' => 'BRTITAL_TEST rules',
                'date_added' => '2018-12-06 17:50:39',
                'added_by' => 58,
                'date_updated' => '2018-12-06 11:50:39',
                'updated_by' => 58,
            ),
            119 => 
            array (
                'id' => 6,
                'tariff_id' => 240443,
                'name' => 'test',
                'date_added' => '2018-12-18 19:30:57',
                'added_by' => 58,
                'date_updated' => '2018-12-18 13:30:57',
                'updated_by' => 58,
            ),
            120 => 
            array (
                'id' => 7,
                'tariff_id' => 240530,
                'name' => 'Yodel Tariff New Rule',
                'date_added' => '2019-05-30 17:18:54',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:18:54',
                'updated_by' => 2182,
            ),
            121 => 
            array (
                'id' => 8,
                'tariff_id' => 240532,
                'name' => 'Swe Cus 2 Tariff',
                'date_added' => '2019-05-30 17:34:34',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:34:34',
                'updated_by' => 2182,
            ),
            122 => 
            array (
                'id' => 9,
                'tariff_id' => 240537,
                'name' => 'sue rules',
                'date_added' => '2019-05-31 17:01:45',
                'added_by' => 58,
                'date_updated' => '2019-05-31 12:01:45',
                'updated_by' => 58,
            ),
            123 => 
            array (
                'id' => 10,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:45:10',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:45:10',
                'updated_by' => 58,
            ),
            124 => 
            array (
                'id' => 11,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:49:07',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:49:07',
                'updated_by' => 58,
            ),
            125 => 
            array (
                'id' => 12,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-03 21:17:18',
                'added_by' => 58,
                'date_updated' => '2019-07-03 16:17:18',
                'updated_by' => 58,
            ),
            126 => 
            array (
                'id' => 13,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-31 15:28:33',
                'added_by' => 58,
                'date_updated' => '2019-07-31 10:28:33',
                'updated_by' => 58,
            ),
            127 => 
            array (
                'id' => 14,
                'tariff_id' => 240561,
                'name' => 'PA POST Rule for Home 24',
                'date_added' => '2019-08-28 17:36:04',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:04',
                'updated_by' => 2252,
            ),
            128 => 
            array (
                'id' => 15,
                'tariff_id' => 240561,
                'name' => '24 PA Post  rules',
                'date_added' => '2019-08-28 17:36:10',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:10',
                'updated_by' => 2252,
            ),
            129 => 
            array (
                'id' => 16,
                'tariff_id' => 240566,
                'name' => 'shabbir_customer rules',
                'date_added' => '2019-08-30 22:00:09',
                'added_by' => 58,
                'date_updated' => '2019-08-30 17:00:09',
                'updated_by' => 58,
            ),
            130 => 
            array (
                'id' => 17,
                'tariff_id' => 240571,
                'name' => 'Increment 1',
                'date_added' => '2019-09-04 22:59:06',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:06',
                'updated_by' => 2258,
            ),
            131 => 
            array (
                'id' => 18,
                'tariff_id' => 240571,
                'name' => 'sdv',
                'date_added' => '2019-09-04 22:59:56',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:56',
                'updated_by' => 2258,
            ),
            132 => 
            array (
                'id' => 19,
                'tariff_id' => 240574,
                'name' => 'USD ACC T1 csv 0.5 Margin',
                'date_added' => '2019-09-05 17:05:17',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:05:17',
                'updated_by' => 2282,
            ),
            133 => 
            array (
                'id' => 20,
                'tariff_id' => 240574,
                'name' => 'USD Account T1 csv 1 Margin Fixed ',
                'date_added' => '2019-09-05 17:11:39',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:11:39',
                'updated_by' => 2282,
            ),
            134 => 
            array (
                'id' => 21,
                'tariff_id' => 240577,
                'name' => 'EURACC Tariff 1 Sup rules',
                'date_added' => '2019-09-06 18:23:35',
                'added_by' => 2282,
                'date_updated' => '2019-09-06 13:23:35',
                'updated_by' => 2282,
            ),
            135 => 
            array (
                'id' => 22,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD RUle 1',
                'date_added' => '2019-09-11 15:37:14',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:14',
                'updated_by' => 2300,
            ),
            136 => 
            array (
                'id' => 23,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD rules',
                'date_added' => '2019-09-11 15:37:34',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:34',
                'updated_by' => 2300,
            ),
            137 => 
            array (
                'id' => 24,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD Rule 2',
                'date_added' => '2019-09-11 15:38:54',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:38:54',
                'updated_by' => 2300,
            ),
            138 => 
            array (
                'id' => 25,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD	 rules',
                'date_added' => '2019-09-11 15:39:25',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:25',
                'updated_by' => 2300,
            ),
            139 => 
            array (
                'id' => 26,
                'tariff_id' => 240603,
                'name' => 'Yodel 72 MINI USD	 rules',
                'date_added' => '2019-09-11 15:39:53',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:53',
                'updated_by' => 2300,
            ),
            140 => 
            array (
                'id' => 27,
                'tariff_id' => 240607,
                'name' => 'BRT Italy RUle 1',
                'date_added' => '2019-09-11 15:42:41',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:42:41',
                'updated_by' => 2300,
            ),
            141 => 
            array (
                'id' => 28,
                'tariff_id' => 240608,
                'name' => 'OWE PRODUCT RULE 1',
                'date_added' => '2019-09-11 15:43:21',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:43:21',
                'updated_by' => 2300,
            ),
            142 => 
            array (
                'id' => 29,
                'tariff_id' => 240632,
                'name' => 'NaN Rule',
                'date_added' => '2019-11-06 23:50:07',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:50:07',
                'updated_by' => 58,
            ),
            143 => 
            array (
                'id' => 30,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:22',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:22',
                'updated_by' => 58,
            ),
            144 => 
            array (
                'id' => 31,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:55',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:55',
                'updated_by' => 58,
            ),
            145 => 
            array (
                'id' => 32,
                'tariff_id' => 240637,
                'name' => 'Sweden Reg Sup T rules',
                'date_added' => '2019-11-08 19:10:07',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:10:07',
                'updated_by' => 58,
            ),
            146 => 
            array (
                'id' => 33,
                'tariff_id' => 240639,
                'name' => 'Swe Tariff rules',
                'date_added' => '2019-11-08 19:29:33',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:29:33',
                'updated_by' => 58,
            ),
            147 => 
            array (
                'id' => 34,
                'tariff_id' => 240645,
                'name' => 'DE dpd rules',
                'date_added' => '2019-11-13 00:37:11',
                'added_by' => 58,
                'date_updated' => '2019-11-12 18:37:11',
                'updated_by' => 58,
            ),
            148 => 
            array (
                'id' => 35,
                'tariff_id' => 240656,
                'name' => 'yasmin',
                'date_added' => '2019-11-27 22:49:43',
                'added_by' => 2295,
                'date_updated' => '2019-11-27 16:49:43',
                'updated_by' => 2295,
            ),
            149 => 
            array (
                'id' => 36,
                'tariff_id' => 240644,
                'name' => 'OWE Pro rules',
                'date_added' => '2019-11-28 19:23:07',
                'added_by' => 58,
                'date_updated' => '2019-11-28 13:23:07',
                'updated_by' => 58,
            ),
            150 => 
            array (
                'id' => 37,
                'tariff_id' => 240642,
                'name' => 'test',
                'date_added' => '2020-02-28 18:41:04',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:41:04',
                'updated_by' => 58,
            ),
            151 => 
            array (
                'id' => 38,
                'tariff_id' => 240667,
                'name' => 'Supplier compare rules',
                'date_added' => '2020-02-28 18:45:23',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:45:23',
                'updated_by' => 58,
            ),
            152 => 
            array (
                'id' => 1,
                'tariff_id' => 240393,
                'name' => 'UKMELL TARIFF CUSTOMER',
                'date_added' => '2018-11-12 16:11:40',
                'added_by' => 58,
                'date_updated' => '2018-11-12 10:11:40',
                'updated_by' => 58,
            ),
            153 => 
            array (
                'id' => 2,
                'tariff_id' => 240397,
                'name' => 'kazy',
                'date_added' => '2018-11-26 02:26:26',
                'added_by' => 58,
                'date_updated' => '2018-11-25 20:26:26',
                'updated_by' => 58,
            ),
            154 => 
            array (
                'id' => 3,
                'tariff_id' => 240409,
                'name' => 'najam rule',
                'date_added' => '2018-12-03 17:37:39',
                'added_by' => 2170,
                'date_updated' => '2018-12-03 11:37:39',
                'updated_by' => 2170,
            ),
            155 => 
            array (
                'id' => 4,
                'tariff_id' => 240413,
                'name' => 'SUE',
                'date_added' => '2018-12-03 23:16:26',
                'added_by' => 2112,
                'date_updated' => '2018-12-03 17:16:26',
                'updated_by' => 2112,
            ),
            156 => 
            array (
                'id' => 5,
                'tariff_id' => 240421,
                'name' => 'BRTITAL_TEST rules',
                'date_added' => '2018-12-06 17:50:39',
                'added_by' => 58,
                'date_updated' => '2018-12-06 11:50:39',
                'updated_by' => 58,
            ),
            157 => 
            array (
                'id' => 6,
                'tariff_id' => 240443,
                'name' => 'test',
                'date_added' => '2018-12-18 19:30:57',
                'added_by' => 58,
                'date_updated' => '2018-12-18 13:30:57',
                'updated_by' => 58,
            ),
            158 => 
            array (
                'id' => 7,
                'tariff_id' => 240530,
                'name' => 'Yodel Tariff New Rule',
                'date_added' => '2019-05-30 17:18:54',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:18:54',
                'updated_by' => 2182,
            ),
            159 => 
            array (
                'id' => 8,
                'tariff_id' => 240532,
                'name' => 'Swe Cus 2 Tariff',
                'date_added' => '2019-05-30 17:34:34',
                'added_by' => 2182,
                'date_updated' => '2019-05-30 12:34:34',
                'updated_by' => 2182,
            ),
            160 => 
            array (
                'id' => 9,
                'tariff_id' => 240537,
                'name' => 'sue rules',
                'date_added' => '2019-05-31 17:01:45',
                'added_by' => 58,
                'date_updated' => '2019-05-31 12:01:45',
                'updated_by' => 58,
            ),
            161 => 
            array (
                'id' => 10,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:45:10',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:45:10',
                'updated_by' => 58,
            ),
            162 => 
            array (
                'id' => 11,
                'tariff_id' => 240544,
                'name' => 'UP001 Tariff rules',
                'date_added' => '2019-05-31 22:49:07',
                'added_by' => 58,
                'date_updated' => '2019-05-31 17:49:07',
                'updated_by' => 58,
            ),
            163 => 
            array (
                'id' => 12,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-03 21:17:18',
                'added_by' => 58,
                'date_updated' => '2019-07-03 16:17:18',
                'updated_by' => 58,
            ),
            164 => 
            array (
                'id' => 13,
                'tariff_id' => 240551,
                'name' => 'ASACC Sup Tariff rules',
                'date_added' => '2019-07-31 15:28:33',
                'added_by' => 58,
                'date_updated' => '2019-07-31 10:28:33',
                'updated_by' => 58,
            ),
            165 => 
            array (
                'id' => 14,
                'tariff_id' => 240561,
                'name' => 'PA POST Rule for Home 24',
                'date_added' => '2019-08-28 17:36:04',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:04',
                'updated_by' => 2252,
            ),
            166 => 
            array (
                'id' => 15,
                'tariff_id' => 240561,
                'name' => '24 PA Post  rules',
                'date_added' => '2019-08-28 17:36:10',
                'added_by' => 2252,
                'date_updated' => '2019-08-28 12:36:10',
                'updated_by' => 2252,
            ),
            167 => 
            array (
                'id' => 16,
                'tariff_id' => 240566,
                'name' => 'shabbir_customer rules',
                'date_added' => '2019-08-30 22:00:09',
                'added_by' => 58,
                'date_updated' => '2019-08-30 17:00:09',
                'updated_by' => 58,
            ),
            168 => 
            array (
                'id' => 17,
                'tariff_id' => 240571,
                'name' => 'Increment 1',
                'date_added' => '2019-09-04 22:59:06',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:06',
                'updated_by' => 2258,
            ),
            169 => 
            array (
                'id' => 18,
                'tariff_id' => 240571,
                'name' => 'sdv',
                'date_added' => '2019-09-04 22:59:56',
                'added_by' => 2258,
                'date_updated' => '2019-09-04 17:59:56',
                'updated_by' => 2258,
            ),
            170 => 
            array (
                'id' => 19,
                'tariff_id' => 240574,
                'name' => 'USD ACC T1 csv 0.5 Margin',
                'date_added' => '2019-09-05 17:05:17',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:05:17',
                'updated_by' => 2282,
            ),
            171 => 
            array (
                'id' => 20,
                'tariff_id' => 240574,
                'name' => 'USD Account T1 csv 1 Margin Fixed ',
                'date_added' => '2019-09-05 17:11:39',
                'added_by' => 2282,
                'date_updated' => '2019-09-05 12:11:39',
                'updated_by' => 2282,
            ),
            172 => 
            array (
                'id' => 21,
                'tariff_id' => 240577,
                'name' => 'EURACC Tariff 1 Sup rules',
                'date_added' => '2019-09-06 18:23:35',
                'added_by' => 2282,
                'date_updated' => '2019-09-06 13:23:35',
                'updated_by' => 2282,
            ),
            173 => 
            array (
                'id' => 22,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD RUle 1',
                'date_added' => '2019-09-11 15:37:14',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:14',
                'updated_by' => 2300,
            ),
            174 => 
            array (
                'id' => 23,
                'tariff_id' => 240601,
                'name' => 'Yodel 24 POD USD rules',
                'date_added' => '2019-09-11 15:37:34',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:37:34',
                'updated_by' => 2300,
            ),
            175 => 
            array (
                'id' => 24,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD Rule 2',
                'date_added' => '2019-09-11 15:38:54',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:38:54',
                'updated_by' => 2300,
            ),
            176 => 
            array (
                'id' => 25,
                'tariff_id' => 240602,
                'name' => 'Yodel 72 POD USD	 rules',
                'date_added' => '2019-09-11 15:39:25',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:25',
                'updated_by' => 2300,
            ),
            177 => 
            array (
                'id' => 26,
                'tariff_id' => 240603,
                'name' => 'Yodel 72 MINI USD	 rules',
                'date_added' => '2019-09-11 15:39:53',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:39:53',
                'updated_by' => 2300,
            ),
            178 => 
            array (
                'id' => 27,
                'tariff_id' => 240607,
                'name' => 'BRT Italy RUle 1',
                'date_added' => '2019-09-11 15:42:41',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:42:41',
                'updated_by' => 2300,
            ),
            179 => 
            array (
                'id' => 28,
                'tariff_id' => 240608,
                'name' => 'OWE PRODUCT RULE 1',
                'date_added' => '2019-09-11 15:43:21',
                'added_by' => 2300,
                'date_updated' => '2019-09-11 10:43:21',
                'updated_by' => 2300,
            ),
            180 => 
            array (
                'id' => 29,
                'tariff_id' => 240632,
                'name' => 'NaN Rule',
                'date_added' => '2019-11-06 23:50:07',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:50:07',
                'updated_by' => 58,
            ),
            181 => 
            array (
                'id' => 30,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:22',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:22',
                'updated_by' => 58,
            ),
            182 => 
            array (
                'id' => 31,
                'tariff_id' => 240632,
                'name' => 'test sup 72 rules',
                'date_added' => '2019-11-06 23:51:55',
                'added_by' => 58,
                'date_updated' => '2019-11-06 17:51:55',
                'updated_by' => 58,
            ),
            183 => 
            array (
                'id' => 32,
                'tariff_id' => 240637,
                'name' => 'Sweden Reg Sup T rules',
                'date_added' => '2019-11-08 19:10:07',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:10:07',
                'updated_by' => 58,
            ),
            184 => 
            array (
                'id' => 33,
                'tariff_id' => 240639,
                'name' => 'Swe Tariff rules',
                'date_added' => '2019-11-08 19:29:33',
                'added_by' => 58,
                'date_updated' => '2019-11-08 13:29:33',
                'updated_by' => 58,
            ),
            185 => 
            array (
                'id' => 34,
                'tariff_id' => 240645,
                'name' => 'DE dpd rules',
                'date_added' => '2019-11-13 00:37:11',
                'added_by' => 58,
                'date_updated' => '2019-11-12 18:37:11',
                'updated_by' => 58,
            ),
            186 => 
            array (
                'id' => 35,
                'tariff_id' => 240656,
                'name' => 'yasmin',
                'date_added' => '2019-11-27 22:49:43',
                'added_by' => 2295,
                'date_updated' => '2019-11-27 16:49:43',
                'updated_by' => 2295,
            ),
            187 => 
            array (
                'id' => 36,
                'tariff_id' => 240644,
                'name' => 'OWE Pro rules',
                'date_added' => '2019-11-28 19:23:07',
                'added_by' => 58,
                'date_updated' => '2019-11-28 13:23:07',
                'updated_by' => 58,
            ),
            188 => 
            array (
                'id' => 37,
                'tariff_id' => 240642,
                'name' => 'test',
                'date_added' => '2020-02-28 18:41:04',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:41:04',
                'updated_by' => 58,
            ),
            189 => 
            array (
                'id' => 38,
                'tariff_id' => 240667,
                'name' => 'Supplier compare rules',
                'date_added' => '2020-02-28 18:45:23',
                'added_by' => 58,
                'date_updated' => '2020-02-28 12:45:23',
                'updated_by' => 58,
            ),
        ));
        
        
    }
}