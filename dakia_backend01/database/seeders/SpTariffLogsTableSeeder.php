<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SpTariffLogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sp_tariff_logs')->delete();
        
        \DB::table('sp_tariff_logs')->insert(array (
            0 => 
            array (
                'id' => 2763,
                'account_id' => 2288,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:06',
                'consignment_id' => 60126,
            ),
            1 => 
            array (
                'id' => 2764,
                'account_id' => 2287,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            2 => 
            array (
                'id' => 2765,
                'account_id' => 2287,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            3 => 
            array (
                'id' => 2766,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            4 => 
            array (
                'id' => 2767,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            5 => 
            array (
                'id' => 2768,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            6 => 
            array (
                'id' => 2769,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            7 => 
            array (
                'id' => 2770,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            8 => 
            array (
                'id' => 2771,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            9 => 
            array (
                'id' => 2772,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            10 => 
            array (
                'id' => 2773,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            11 => 
            array (
                'id' => 2774,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            12 => 
            array (
                'id' => 2775,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            13 => 
            array (
                'id' => 2776,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            14 => 
            array (
                'id' => 2777,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            15 => 
            array (
                'id' => 2778,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '4.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            16 => 
            array (
                'id' => 2779,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            17 => 
            array (
                'id' => 2780,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            18 => 
            array (
                'id' => 2781,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            19 => 
            array (
                'id' => 2782,
                'account_id' => 2285,
                'charges_type' => 'basic charges',
                'charges' => '100.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            20 => 
            array (
                'id' => 2783,
                'account_id' => 2285,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            21 => 
            array (
                'id' => 2763,
                'account_id' => 2288,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:06',
                'consignment_id' => 60126,
            ),
            22 => 
            array (
                'id' => 2764,
                'account_id' => 2287,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            23 => 
            array (
                'id' => 2765,
                'account_id' => 2287,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            24 => 
            array (
                'id' => 2766,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            25 => 
            array (
                'id' => 2767,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            26 => 
            array (
                'id' => 2768,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            27 => 
            array (
                'id' => 2769,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            28 => 
            array (
                'id' => 2770,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            29 => 
            array (
                'id' => 2771,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            30 => 
            array (
                'id' => 2772,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            31 => 
            array (
                'id' => 2773,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            32 => 
            array (
                'id' => 2774,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            33 => 
            array (
                'id' => 2775,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            34 => 
            array (
                'id' => 2776,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            35 => 
            array (
                'id' => 2777,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            36 => 
            array (
                'id' => 2778,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '4.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            37 => 
            array (
                'id' => 2779,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            38 => 
            array (
                'id' => 2780,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            39 => 
            array (
                'id' => 2781,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            40 => 
            array (
                'id' => 2782,
                'account_id' => 2285,
                'charges_type' => 'basic charges',
                'charges' => '100.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            41 => 
            array (
                'id' => 2783,
                'account_id' => 2285,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            42 => 
            array (
                'id' => 2763,
                'account_id' => 2288,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:06',
                'consignment_id' => 60126,
            ),
            43 => 
            array (
                'id' => 2764,
                'account_id' => 2287,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            44 => 
            array (
                'id' => 2765,
                'account_id' => 2287,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            45 => 
            array (
                'id' => 2766,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            46 => 
            array (
                'id' => 2767,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            47 => 
            array (
                'id' => 2768,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            48 => 
            array (
                'id' => 2769,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            49 => 
            array (
                'id' => 2770,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            50 => 
            array (
                'id' => 2771,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            51 => 
            array (
                'id' => 2772,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            52 => 
            array (
                'id' => 2773,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            53 => 
            array (
                'id' => 2774,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            54 => 
            array (
                'id' => 2775,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            55 => 
            array (
                'id' => 2776,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            56 => 
            array (
                'id' => 2777,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            57 => 
            array (
                'id' => 2778,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '4.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            58 => 
            array (
                'id' => 2779,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            59 => 
            array (
                'id' => 2780,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            60 => 
            array (
                'id' => 2781,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            61 => 
            array (
                'id' => 2782,
                'account_id' => 2285,
                'charges_type' => 'basic charges',
                'charges' => '100.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            62 => 
            array (
                'id' => 2783,
                'account_id' => 2285,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            63 => 
            array (
                'id' => 2763,
                'account_id' => 2288,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:06',
                'consignment_id' => 60126,
            ),
            64 => 
            array (
                'id' => 2764,
                'account_id' => 2287,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            65 => 
            array (
                'id' => 2765,
                'account_id' => 2287,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            66 => 
            array (
                'id' => 2766,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            67 => 
            array (
                'id' => 2767,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            68 => 
            array (
                'id' => 2768,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            69 => 
            array (
                'id' => 2769,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            70 => 
            array (
                'id' => 2770,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            71 => 
            array (
                'id' => 2771,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            72 => 
            array (
                'id' => 2772,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            73 => 
            array (
                'id' => 2773,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            74 => 
            array (
                'id' => 2774,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            75 => 
            array (
                'id' => 2775,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            76 => 
            array (
                'id' => 2776,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            77 => 
            array (
                'id' => 2777,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            78 => 
            array (
                'id' => 2778,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '4.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            79 => 
            array (
                'id' => 2779,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            80 => 
            array (
                'id' => 2780,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            81 => 
            array (
                'id' => 2781,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            82 => 
            array (
                'id' => 2782,
                'account_id' => 2285,
                'charges_type' => 'basic charges',
                'charges' => '100.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            83 => 
            array (
                'id' => 2783,
                'account_id' => 2285,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            84 => 
            array (
                'id' => 2763,
                'account_id' => 2288,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:06',
                'consignment_id' => 60126,
            ),
            85 => 
            array (
                'id' => 2764,
                'account_id' => 2287,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            86 => 
            array (
                'id' => 2765,
                'account_id' => 2287,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:29:58',
                'consignment_id' => 60127,
            ),
            87 => 
            array (
                'id' => 2766,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '25.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            88 => 
            array (
                'id' => 2767,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:30:45',
                'consignment_id' => 60128,
            ),
            89 => 
            array (
                'id' => 2768,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            90 => 
            array (
                'id' => 2769,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:33:59',
                'consignment_id' => 840,
            ),
            91 => 
            array (
                'id' => 2770,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            92 => 
            array (
                'id' => 2771,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 847,
            ),
            93 => 
            array (
                'id' => 2772,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            94 => 
            array (
                'id' => 2773,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:34:00',
                'consignment_id' => 848,
            ),
            95 => 
            array (
                'id' => 2774,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            96 => 
            array (
                'id' => 2775,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:18',
                'consignment_id' => 60174,
            ),
            97 => 
            array (
                'id' => 2776,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            98 => 
            array (
                'id' => 2777,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:38:21',
                'consignment_id' => 60173,
            ),
            99 => 
            array (
                'id' => 2778,
                'account_id' => 2286,
                'charges_type' => 'basic charges',
                'charges' => '4.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            100 => 
            array (
                'id' => 2779,
                'account_id' => 2286,
                'charges_type' => 'label charges',
                'charges' => '50.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:40:35',
                'consignment_id' => 60175,
            ),
            101 => 
            array (
                'id' => 2780,
                'account_id' => 2228,
                'charges_type' => 'basic charges',
                'charges' => '0.00',
                'formulla' => NULL,
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            102 => 
            array (
                'id' => 2781,
                'account_id' => 2228,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 22:50:11',
                'consignment_id' => 60165,
            ),
            103 => 
            array (
                'id' => 2782,
                'account_id' => 2285,
                'charges_type' => 'basic charges',
                'charges' => '100.00',
            'formulla' => ' (W*CHRG)+(Q*ITMCHR)',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
            104 => 
            array (
                'id' => 2783,
                'account_id' => 2285,
                'charges_type' => 'label charges',
                'charges' => '0.00',
            'formulla' => ',CURRENT_TIMESTAMP,@consignment_id_p),
(NULL, @f_user_ac',
                'date_added' => '2018-11-27 23:03:17',
                'consignment_id' => 60176,
            ),
        ));
        
        
    }
}