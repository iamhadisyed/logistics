<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PartnerservicesroutingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('partnerservicesroutings')->delete();
        
        \DB::table('partnerservicesroutings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'country_id' => 225,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            1 => 
            array (
                'id' => 2,
                'country_id' => 225,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 0,
                'product_id' => 1,
                'service_id' => 120,
            ),
            2 => 
            array (
                'id' => 3,
                'country_id' => 225,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 69,
            ),
            3 => 
            array (
                'id' => 4,
                'country_id' => 225,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            4 => 
            array (
                'id' => 5,
                'country_id' => 225,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            5 => 
            array (
                'id' => 6,
                'country_id' => 225,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            6 => 
            array (
                'id' => 7,
                'country_id' => 225,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            7 => 
            array (
                'id' => 8,
                'country_id' => 225,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            8 => 
            array (
                'id' => 9,
                'country_id' => 225,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            9 => 
            array (
                'id' => 10,
                'country_id' => 225,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            10 => 
            array (
                'id' => 11,
                'country_id' => 225,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            11 => 
            array (
                'id' => 12,
                'country_id' => 225,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            12 => 
            array (
                'id' => 13,
                'country_id' => 225,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            13 => 
            array (
                'id' => 14,
                'country_id' => 225,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            14 => 
            array (
                'id' => 15,
                'country_id' => 225,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            15 => 
            array (
                'id' => 16,
                'country_id' => 225,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            16 => 
            array (
                'id' => 17,
                'country_id' => 225,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            17 => 
            array (
                'id' => 18,
                'country_id' => 225,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            18 => 
            array (
                'id' => 19,
                'country_id' => 225,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            19 => 
            array (
                'id' => 20,
                'country_id' => 225,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            20 => 
            array (
                'id' => 21,
                'country_id' => 225,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            21 => 
            array (
                'id' => 22,
                'country_id' => 225,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            22 => 
            array (
                'id' => 23,
                'country_id' => 225,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            23 => 
            array (
                'id' => 24,
                'country_id' => 225,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            24 => 
            array (
                'id' => 25,
                'country_id' => 225,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            25 => 
            array (
                'id' => 26,
                'country_id' => 225,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            26 => 
            array (
                'id' => 27,
                'country_id' => 225,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            27 => 
            array (
                'id' => 28,
                'country_id' => 225,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            28 => 
            array (
                'id' => 29,
                'country_id' => 225,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            29 => 
            array (
                'id' => 30,
                'country_id' => 225,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            30 => 
            array (
                'id' => 31,
                'country_id' => 225,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            31 => 
            array (
                'id' => 32,
                'country_id' => 225,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            32 => 
            array (
                'id' => 33,
                'country_id' => 225,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            33 => 
            array (
                'id' => 34,
                'country_id' => 225,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            34 => 
            array (
                'id' => 35,
                'country_id' => 225,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            35 => 
            array (
                'id' => 36,
                'country_id' => 225,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            36 => 
            array (
                'id' => 37,
                'country_id' => 225,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            37 => 
            array (
                'id' => 38,
                'country_id' => 225,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            38 => 
            array (
                'id' => 39,
                'country_id' => 225,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            39 => 
            array (
                'id' => 40,
                'country_id' => 225,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            40 => 
            array (
                'id' => 41,
                'country_id' => 225,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            41 => 
            array (
                'id' => 42,
                'country_id' => 225,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            42 => 
            array (
                'id' => 43,
                'country_id' => 225,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            43 => 
            array (
                'id' => 44,
                'country_id' => 225,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            44 => 
            array (
                'id' => 45,
                'country_id' => 225,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            45 => 
            array (
                'id' => 46,
                'country_id' => 225,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            46 => 
            array (
                'id' => 47,
                'country_id' => 225,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            47 => 
            array (
                'id' => 48,
                'country_id' => 225,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            48 => 
            array (
                'id' => 49,
                'country_id' => 225,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            49 => 
            array (
                'id' => 50,
                'country_id' => 225,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            50 => 
            array (
                'id' => 51,
                'country_id' => 225,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            51 => 
            array (
                'id' => 52,
                'country_id' => 225,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            52 => 
            array (
                'id' => 53,
                'country_id' => 225,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            53 => 
            array (
                'id' => 54,
                'country_id' => 225,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            54 => 
            array (
                'id' => 55,
                'country_id' => 225,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            55 => 
            array (
                'id' => 56,
                'country_id' => 225,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            56 => 
            array (
                'id' => 57,
                'country_id' => 225,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            57 => 
            array (
                'id' => 58,
                'country_id' => 225,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            58 => 
            array (
                'id' => 59,
                'country_id' => 225,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            59 => 
            array (
                'id' => 60,
                'country_id' => 225,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            60 => 
            array (
                'id' => 61,
                'country_id' => 225,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            61 => 
            array (
                'id' => 62,
                'country_id' => 225,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            62 => 
            array (
                'id' => 63,
                'country_id' => 225,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            63 => 
            array (
                'id' => 64,
                'country_id' => 225,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            64 => 
            array (
                'id' => 65,
                'country_id' => 162,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            65 => 
            array (
                'id' => 66,
                'country_id' => 162,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            66 => 
            array (
                'id' => 67,
                'country_id' => 162,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            67 => 
            array (
                'id' => 68,
                'country_id' => 162,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            68 => 
            array (
                'id' => 69,
                'country_id' => 162,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            69 => 
            array (
                'id' => 70,
                'country_id' => 162,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            70 => 
            array (
                'id' => 71,
                'country_id' => 162,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            71 => 
            array (
                'id' => 72,
                'country_id' => 162,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            72 => 
            array (
                'id' => 73,
                'country_id' => 162,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            73 => 
            array (
                'id' => 74,
                'country_id' => 162,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            74 => 
            array (
                'id' => 75,
                'country_id' => 162,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            75 => 
            array (
                'id' => 76,
                'country_id' => 162,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            76 => 
            array (
                'id' => 77,
                'country_id' => 162,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            77 => 
            array (
                'id' => 78,
                'country_id' => 162,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            78 => 
            array (
                'id' => 79,
                'country_id' => 162,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            79 => 
            array (
                'id' => 80,
                'country_id' => 162,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            80 => 
            array (
                'id' => 81,
                'country_id' => 162,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            81 => 
            array (
                'id' => 82,
                'country_id' => 162,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            82 => 
            array (
                'id' => 83,
                'country_id' => 162,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            83 => 
            array (
                'id' => 84,
                'country_id' => 162,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            84 => 
            array (
                'id' => 85,
                'country_id' => 162,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            85 => 
            array (
                'id' => 86,
                'country_id' => 162,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            86 => 
            array (
                'id' => 87,
                'country_id' => 162,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            87 => 
            array (
                'id' => 88,
                'country_id' => 162,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            88 => 
            array (
                'id' => 89,
                'country_id' => 162,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            89 => 
            array (
                'id' => 90,
                'country_id' => 162,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            90 => 
            array (
                'id' => 91,
                'country_id' => 162,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            91 => 
            array (
                'id' => 92,
                'country_id' => 162,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            92 => 
            array (
                'id' => 93,
                'country_id' => 162,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            93 => 
            array (
                'id' => 94,
                'country_id' => 162,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            94 => 
            array (
                'id' => 95,
                'country_id' => 162,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            95 => 
            array (
                'id' => 96,
                'country_id' => 162,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            96 => 
            array (
                'id' => 97,
                'country_id' => 162,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            97 => 
            array (
                'id' => 98,
                'country_id' => 162,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            98 => 
            array (
                'id' => 99,
                'country_id' => 162,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            99 => 
            array (
                'id' => 100,
                'country_id' => 162,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            100 => 
            array (
                'id' => 101,
                'country_id' => 162,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            101 => 
            array (
                'id' => 102,
                'country_id' => 162,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            102 => 
            array (
                'id' => 103,
                'country_id' => 162,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            103 => 
            array (
                'id' => 104,
                'country_id' => 162,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            104 => 
            array (
                'id' => 105,
                'country_id' => 162,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            105 => 
            array (
                'id' => 106,
                'country_id' => 162,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            106 => 
            array (
                'id' => 107,
                'country_id' => 162,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            107 => 
            array (
                'id' => 108,
                'country_id' => 162,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            108 => 
            array (
                'id' => 109,
                'country_id' => 162,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            109 => 
            array (
                'id' => 110,
                'country_id' => 162,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            110 => 
            array (
                'id' => 111,
                'country_id' => 162,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            111 => 
            array (
                'id' => 112,
                'country_id' => 162,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            112 => 
            array (
                'id' => 113,
                'country_id' => 162,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            113 => 
            array (
                'id' => 114,
                'country_id' => 162,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            114 => 
            array (
                'id' => 115,
                'country_id' => 162,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            115 => 
            array (
                'id' => 116,
                'country_id' => 162,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            116 => 
            array (
                'id' => 117,
                'country_id' => 162,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            117 => 
            array (
                'id' => 118,
                'country_id' => 162,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            118 => 
            array (
                'id' => 119,
                'country_id' => 162,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            119 => 
            array (
                'id' => 120,
                'country_id' => 162,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            120 => 
            array (
                'id' => 121,
                'country_id' => 162,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            121 => 
            array (
                'id' => 122,
                'country_id' => 162,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            122 => 
            array (
                'id' => 123,
                'country_id' => 162,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            123 => 
            array (
                'id' => 124,
                'country_id' => 162,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            124 => 
            array (
                'id' => 125,
                'country_id' => 162,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            125 => 
            array (
                'id' => 126,
                'country_id' => 162,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            126 => 
            array (
                'id' => 127,
                'country_id' => 162,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            127 => 
            array (
                'id' => 128,
                'country_id' => 162,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            128 => 
            array (
                'id' => 1,
                'country_id' => 225,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            129 => 
            array (
                'id' => 2,
                'country_id' => 225,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 0,
                'product_id' => 1,
                'service_id' => 120,
            ),
            130 => 
            array (
                'id' => 3,
                'country_id' => 225,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 69,
            ),
            131 => 
            array (
                'id' => 4,
                'country_id' => 225,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            132 => 
            array (
                'id' => 5,
                'country_id' => 225,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            133 => 
            array (
                'id' => 6,
                'country_id' => 225,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            134 => 
            array (
                'id' => 7,
                'country_id' => 225,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            135 => 
            array (
                'id' => 8,
                'country_id' => 225,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            136 => 
            array (
                'id' => 9,
                'country_id' => 225,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            137 => 
            array (
                'id' => 10,
                'country_id' => 225,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            138 => 
            array (
                'id' => 11,
                'country_id' => 225,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            139 => 
            array (
                'id' => 12,
                'country_id' => 225,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            140 => 
            array (
                'id' => 13,
                'country_id' => 225,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            141 => 
            array (
                'id' => 14,
                'country_id' => 225,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            142 => 
            array (
                'id' => 15,
                'country_id' => 225,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            143 => 
            array (
                'id' => 16,
                'country_id' => 225,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            144 => 
            array (
                'id' => 17,
                'country_id' => 225,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            145 => 
            array (
                'id' => 18,
                'country_id' => 225,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            146 => 
            array (
                'id' => 19,
                'country_id' => 225,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            147 => 
            array (
                'id' => 20,
                'country_id' => 225,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            148 => 
            array (
                'id' => 21,
                'country_id' => 225,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            149 => 
            array (
                'id' => 22,
                'country_id' => 225,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            150 => 
            array (
                'id' => 23,
                'country_id' => 225,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            151 => 
            array (
                'id' => 24,
                'country_id' => 225,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            152 => 
            array (
                'id' => 25,
                'country_id' => 225,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            153 => 
            array (
                'id' => 26,
                'country_id' => 225,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            154 => 
            array (
                'id' => 27,
                'country_id' => 225,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            155 => 
            array (
                'id' => 28,
                'country_id' => 225,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            156 => 
            array (
                'id' => 29,
                'country_id' => 225,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            157 => 
            array (
                'id' => 30,
                'country_id' => 225,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            158 => 
            array (
                'id' => 31,
                'country_id' => 225,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            159 => 
            array (
                'id' => 32,
                'country_id' => 225,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            160 => 
            array (
                'id' => 33,
                'country_id' => 225,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            161 => 
            array (
                'id' => 34,
                'country_id' => 225,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            162 => 
            array (
                'id' => 35,
                'country_id' => 225,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            163 => 
            array (
                'id' => 36,
                'country_id' => 225,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            164 => 
            array (
                'id' => 37,
                'country_id' => 225,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            165 => 
            array (
                'id' => 38,
                'country_id' => 225,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            166 => 
            array (
                'id' => 39,
                'country_id' => 225,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            167 => 
            array (
                'id' => 40,
                'country_id' => 225,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            168 => 
            array (
                'id' => 41,
                'country_id' => 225,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            169 => 
            array (
                'id' => 42,
                'country_id' => 225,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            170 => 
            array (
                'id' => 43,
                'country_id' => 225,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            171 => 
            array (
                'id' => 44,
                'country_id' => 225,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            172 => 
            array (
                'id' => 45,
                'country_id' => 225,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            173 => 
            array (
                'id' => 46,
                'country_id' => 225,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            174 => 
            array (
                'id' => 47,
                'country_id' => 225,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            175 => 
            array (
                'id' => 48,
                'country_id' => 225,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            176 => 
            array (
                'id' => 49,
                'country_id' => 225,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            177 => 
            array (
                'id' => 50,
                'country_id' => 225,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            178 => 
            array (
                'id' => 51,
                'country_id' => 225,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            179 => 
            array (
                'id' => 52,
                'country_id' => 225,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            180 => 
            array (
                'id' => 53,
                'country_id' => 225,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            181 => 
            array (
                'id' => 54,
                'country_id' => 225,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            182 => 
            array (
                'id' => 55,
                'country_id' => 225,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            183 => 
            array (
                'id' => 56,
                'country_id' => 225,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            184 => 
            array (
                'id' => 57,
                'country_id' => 225,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            185 => 
            array (
                'id' => 58,
                'country_id' => 225,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            186 => 
            array (
                'id' => 59,
                'country_id' => 225,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            187 => 
            array (
                'id' => 60,
                'country_id' => 225,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            188 => 
            array (
                'id' => 61,
                'country_id' => 225,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            189 => 
            array (
                'id' => 62,
                'country_id' => 225,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            190 => 
            array (
                'id' => 63,
                'country_id' => 225,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            191 => 
            array (
                'id' => 64,
                'country_id' => 225,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            192 => 
            array (
                'id' => 65,
                'country_id' => 162,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            193 => 
            array (
                'id' => 66,
                'country_id' => 162,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            194 => 
            array (
                'id' => 67,
                'country_id' => 162,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            195 => 
            array (
                'id' => 68,
                'country_id' => 162,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            196 => 
            array (
                'id' => 69,
                'country_id' => 162,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            197 => 
            array (
                'id' => 70,
                'country_id' => 162,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            198 => 
            array (
                'id' => 71,
                'country_id' => 162,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            199 => 
            array (
                'id' => 72,
                'country_id' => 162,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            200 => 
            array (
                'id' => 73,
                'country_id' => 162,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            201 => 
            array (
                'id' => 74,
                'country_id' => 162,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            202 => 
            array (
                'id' => 75,
                'country_id' => 162,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            203 => 
            array (
                'id' => 76,
                'country_id' => 162,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            204 => 
            array (
                'id' => 77,
                'country_id' => 162,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            205 => 
            array (
                'id' => 78,
                'country_id' => 162,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            206 => 
            array (
                'id' => 79,
                'country_id' => 162,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            207 => 
            array (
                'id' => 80,
                'country_id' => 162,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            208 => 
            array (
                'id' => 81,
                'country_id' => 162,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            209 => 
            array (
                'id' => 82,
                'country_id' => 162,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            210 => 
            array (
                'id' => 83,
                'country_id' => 162,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            211 => 
            array (
                'id' => 84,
                'country_id' => 162,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            212 => 
            array (
                'id' => 85,
                'country_id' => 162,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            213 => 
            array (
                'id' => 86,
                'country_id' => 162,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            214 => 
            array (
                'id' => 87,
                'country_id' => 162,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            215 => 
            array (
                'id' => 88,
                'country_id' => 162,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            216 => 
            array (
                'id' => 89,
                'country_id' => 162,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            217 => 
            array (
                'id' => 90,
                'country_id' => 162,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            218 => 
            array (
                'id' => 91,
                'country_id' => 162,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            219 => 
            array (
                'id' => 92,
                'country_id' => 162,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            220 => 
            array (
                'id' => 93,
                'country_id' => 162,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            221 => 
            array (
                'id' => 94,
                'country_id' => 162,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            222 => 
            array (
                'id' => 95,
                'country_id' => 162,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            223 => 
            array (
                'id' => 96,
                'country_id' => 162,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            224 => 
            array (
                'id' => 97,
                'country_id' => 162,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            225 => 
            array (
                'id' => 98,
                'country_id' => 162,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            226 => 
            array (
                'id' => 99,
                'country_id' => 162,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            227 => 
            array (
                'id' => 100,
                'country_id' => 162,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            228 => 
            array (
                'id' => 101,
                'country_id' => 162,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            229 => 
            array (
                'id' => 102,
                'country_id' => 162,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            230 => 
            array (
                'id' => 103,
                'country_id' => 162,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            231 => 
            array (
                'id' => 104,
                'country_id' => 162,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            232 => 
            array (
                'id' => 105,
                'country_id' => 162,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            233 => 
            array (
                'id' => 106,
                'country_id' => 162,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            234 => 
            array (
                'id' => 107,
                'country_id' => 162,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            235 => 
            array (
                'id' => 108,
                'country_id' => 162,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            236 => 
            array (
                'id' => 109,
                'country_id' => 162,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            237 => 
            array (
                'id' => 110,
                'country_id' => 162,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            238 => 
            array (
                'id' => 111,
                'country_id' => 162,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            239 => 
            array (
                'id' => 112,
                'country_id' => 162,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            240 => 
            array (
                'id' => 113,
                'country_id' => 162,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            241 => 
            array (
                'id' => 114,
                'country_id' => 162,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            242 => 
            array (
                'id' => 115,
                'country_id' => 162,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            243 => 
            array (
                'id' => 116,
                'country_id' => 162,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            244 => 
            array (
                'id' => 117,
                'country_id' => 162,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            245 => 
            array (
                'id' => 118,
                'country_id' => 162,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            246 => 
            array (
                'id' => 119,
                'country_id' => 162,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            247 => 
            array (
                'id' => 120,
                'country_id' => 162,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            248 => 
            array (
                'id' => 121,
                'country_id' => 162,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            249 => 
            array (
                'id' => 122,
                'country_id' => 162,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            250 => 
            array (
                'id' => 123,
                'country_id' => 162,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            251 => 
            array (
                'id' => 124,
                'country_id' => 162,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            252 => 
            array (
                'id' => 125,
                'country_id' => 162,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            253 => 
            array (
                'id' => 126,
                'country_id' => 162,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            254 => 
            array (
                'id' => 127,
                'country_id' => 162,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            255 => 
            array (
                'id' => 128,
                'country_id' => 162,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            256 => 
            array (
                'id' => 1,
                'country_id' => 225,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            257 => 
            array (
                'id' => 2,
                'country_id' => 225,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 0,
                'product_id' => 1,
                'service_id' => 120,
            ),
            258 => 
            array (
                'id' => 3,
                'country_id' => 225,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 69,
            ),
            259 => 
            array (
                'id' => 4,
                'country_id' => 225,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            260 => 
            array (
                'id' => 5,
                'country_id' => 225,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            261 => 
            array (
                'id' => 6,
                'country_id' => 225,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            262 => 
            array (
                'id' => 7,
                'country_id' => 225,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            263 => 
            array (
                'id' => 8,
                'country_id' => 225,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            264 => 
            array (
                'id' => 9,
                'country_id' => 225,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            265 => 
            array (
                'id' => 10,
                'country_id' => 225,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            266 => 
            array (
                'id' => 11,
                'country_id' => 225,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            267 => 
            array (
                'id' => 12,
                'country_id' => 225,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            268 => 
            array (
                'id' => 13,
                'country_id' => 225,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            269 => 
            array (
                'id' => 14,
                'country_id' => 225,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            270 => 
            array (
                'id' => 15,
                'country_id' => 225,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            271 => 
            array (
                'id' => 16,
                'country_id' => 225,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            272 => 
            array (
                'id' => 17,
                'country_id' => 225,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            273 => 
            array (
                'id' => 18,
                'country_id' => 225,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            274 => 
            array (
                'id' => 19,
                'country_id' => 225,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            275 => 
            array (
                'id' => 20,
                'country_id' => 225,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            276 => 
            array (
                'id' => 21,
                'country_id' => 225,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            277 => 
            array (
                'id' => 22,
                'country_id' => 225,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            278 => 
            array (
                'id' => 23,
                'country_id' => 225,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            279 => 
            array (
                'id' => 24,
                'country_id' => 225,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            280 => 
            array (
                'id' => 25,
                'country_id' => 225,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            281 => 
            array (
                'id' => 26,
                'country_id' => 225,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            282 => 
            array (
                'id' => 27,
                'country_id' => 225,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            283 => 
            array (
                'id' => 28,
                'country_id' => 225,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            284 => 
            array (
                'id' => 29,
                'country_id' => 225,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            285 => 
            array (
                'id' => 30,
                'country_id' => 225,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            286 => 
            array (
                'id' => 31,
                'country_id' => 225,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            287 => 
            array (
                'id' => 32,
                'country_id' => 225,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            288 => 
            array (
                'id' => 33,
                'country_id' => 225,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            289 => 
            array (
                'id' => 34,
                'country_id' => 225,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            290 => 
            array (
                'id' => 35,
                'country_id' => 225,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            291 => 
            array (
                'id' => 36,
                'country_id' => 225,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            292 => 
            array (
                'id' => 37,
                'country_id' => 225,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            293 => 
            array (
                'id' => 38,
                'country_id' => 225,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            294 => 
            array (
                'id' => 39,
                'country_id' => 225,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            295 => 
            array (
                'id' => 40,
                'country_id' => 225,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            296 => 
            array (
                'id' => 41,
                'country_id' => 225,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            297 => 
            array (
                'id' => 42,
                'country_id' => 225,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            298 => 
            array (
                'id' => 43,
                'country_id' => 225,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            299 => 
            array (
                'id' => 44,
                'country_id' => 225,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            300 => 
            array (
                'id' => 45,
                'country_id' => 225,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            301 => 
            array (
                'id' => 46,
                'country_id' => 225,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            302 => 
            array (
                'id' => 47,
                'country_id' => 225,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            303 => 
            array (
                'id' => 48,
                'country_id' => 225,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            304 => 
            array (
                'id' => 49,
                'country_id' => 225,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            305 => 
            array (
                'id' => 50,
                'country_id' => 225,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            306 => 
            array (
                'id' => 51,
                'country_id' => 225,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            307 => 
            array (
                'id' => 52,
                'country_id' => 225,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            308 => 
            array (
                'id' => 53,
                'country_id' => 225,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            309 => 
            array (
                'id' => 54,
                'country_id' => 225,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            310 => 
            array (
                'id' => 55,
                'country_id' => 225,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            311 => 
            array (
                'id' => 56,
                'country_id' => 225,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            312 => 
            array (
                'id' => 57,
                'country_id' => 225,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            313 => 
            array (
                'id' => 58,
                'country_id' => 225,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            314 => 
            array (
                'id' => 59,
                'country_id' => 225,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            315 => 
            array (
                'id' => 60,
                'country_id' => 225,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            316 => 
            array (
                'id' => 61,
                'country_id' => 225,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            317 => 
            array (
                'id' => 62,
                'country_id' => 225,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            318 => 
            array (
                'id' => 63,
                'country_id' => 225,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            319 => 
            array (
                'id' => 64,
                'country_id' => 225,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            320 => 
            array (
                'id' => 65,
                'country_id' => 162,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            321 => 
            array (
                'id' => 66,
                'country_id' => 162,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            322 => 
            array (
                'id' => 67,
                'country_id' => 162,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            323 => 
            array (
                'id' => 68,
                'country_id' => 162,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            324 => 
            array (
                'id' => 69,
                'country_id' => 162,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            325 => 
            array (
                'id' => 70,
                'country_id' => 162,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            326 => 
            array (
                'id' => 71,
                'country_id' => 162,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            327 => 
            array (
                'id' => 72,
                'country_id' => 162,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            328 => 
            array (
                'id' => 73,
                'country_id' => 162,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            329 => 
            array (
                'id' => 74,
                'country_id' => 162,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            330 => 
            array (
                'id' => 75,
                'country_id' => 162,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            331 => 
            array (
                'id' => 76,
                'country_id' => 162,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            332 => 
            array (
                'id' => 77,
                'country_id' => 162,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            333 => 
            array (
                'id' => 78,
                'country_id' => 162,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            334 => 
            array (
                'id' => 79,
                'country_id' => 162,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            335 => 
            array (
                'id' => 80,
                'country_id' => 162,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            336 => 
            array (
                'id' => 81,
                'country_id' => 162,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            337 => 
            array (
                'id' => 82,
                'country_id' => 162,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            338 => 
            array (
                'id' => 83,
                'country_id' => 162,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            339 => 
            array (
                'id' => 84,
                'country_id' => 162,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            340 => 
            array (
                'id' => 85,
                'country_id' => 162,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            341 => 
            array (
                'id' => 86,
                'country_id' => 162,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            342 => 
            array (
                'id' => 87,
                'country_id' => 162,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            343 => 
            array (
                'id' => 88,
                'country_id' => 162,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            344 => 
            array (
                'id' => 89,
                'country_id' => 162,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            345 => 
            array (
                'id' => 90,
                'country_id' => 162,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            346 => 
            array (
                'id' => 91,
                'country_id' => 162,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            347 => 
            array (
                'id' => 92,
                'country_id' => 162,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            348 => 
            array (
                'id' => 93,
                'country_id' => 162,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            349 => 
            array (
                'id' => 94,
                'country_id' => 162,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            350 => 
            array (
                'id' => 95,
                'country_id' => 162,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            351 => 
            array (
                'id' => 96,
                'country_id' => 162,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            352 => 
            array (
                'id' => 97,
                'country_id' => 162,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            353 => 
            array (
                'id' => 98,
                'country_id' => 162,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            354 => 
            array (
                'id' => 99,
                'country_id' => 162,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            355 => 
            array (
                'id' => 100,
                'country_id' => 162,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            356 => 
            array (
                'id' => 101,
                'country_id' => 162,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            357 => 
            array (
                'id' => 102,
                'country_id' => 162,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            358 => 
            array (
                'id' => 103,
                'country_id' => 162,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            359 => 
            array (
                'id' => 104,
                'country_id' => 162,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            360 => 
            array (
                'id' => 105,
                'country_id' => 162,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            361 => 
            array (
                'id' => 106,
                'country_id' => 162,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            362 => 
            array (
                'id' => 107,
                'country_id' => 162,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            363 => 
            array (
                'id' => 108,
                'country_id' => 162,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            364 => 
            array (
                'id' => 109,
                'country_id' => 162,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            365 => 
            array (
                'id' => 110,
                'country_id' => 162,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            366 => 
            array (
                'id' => 111,
                'country_id' => 162,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            367 => 
            array (
                'id' => 112,
                'country_id' => 162,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            368 => 
            array (
                'id' => 113,
                'country_id' => 162,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            369 => 
            array (
                'id' => 114,
                'country_id' => 162,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            370 => 
            array (
                'id' => 115,
                'country_id' => 162,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            371 => 
            array (
                'id' => 116,
                'country_id' => 162,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            372 => 
            array (
                'id' => 117,
                'country_id' => 162,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            373 => 
            array (
                'id' => 118,
                'country_id' => 162,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            374 => 
            array (
                'id' => 119,
                'country_id' => 162,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            375 => 
            array (
                'id' => 120,
                'country_id' => 162,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            376 => 
            array (
                'id' => 121,
                'country_id' => 162,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            377 => 
            array (
                'id' => 122,
                'country_id' => 162,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            378 => 
            array (
                'id' => 123,
                'country_id' => 162,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            379 => 
            array (
                'id' => 124,
                'country_id' => 162,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            380 => 
            array (
                'id' => 125,
                'country_id' => 162,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            381 => 
            array (
                'id' => 126,
                'country_id' => 162,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            382 => 
            array (
                'id' => 127,
                'country_id' => 162,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            383 => 
            array (
                'id' => 128,
                'country_id' => 162,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            384 => 
            array (
                'id' => 1,
                'country_id' => 225,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            385 => 
            array (
                'id' => 2,
                'country_id' => 225,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 0,
                'product_id' => 1,
                'service_id' => 120,
            ),
            386 => 
            array (
                'id' => 3,
                'country_id' => 225,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 69,
            ),
            387 => 
            array (
                'id' => 4,
                'country_id' => 225,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            388 => 
            array (
                'id' => 5,
                'country_id' => 225,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            389 => 
            array (
                'id' => 6,
                'country_id' => 225,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            390 => 
            array (
                'id' => 7,
                'country_id' => 225,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            391 => 
            array (
                'id' => 8,
                'country_id' => 225,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            392 => 
            array (
                'id' => 9,
                'country_id' => 225,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            393 => 
            array (
                'id' => 10,
                'country_id' => 225,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            394 => 
            array (
                'id' => 11,
                'country_id' => 225,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            395 => 
            array (
                'id' => 12,
                'country_id' => 225,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            396 => 
            array (
                'id' => 13,
                'country_id' => 225,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            397 => 
            array (
                'id' => 14,
                'country_id' => 225,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            398 => 
            array (
                'id' => 15,
                'country_id' => 225,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            399 => 
            array (
                'id' => 16,
                'country_id' => 225,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            400 => 
            array (
                'id' => 17,
                'country_id' => 225,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            401 => 
            array (
                'id' => 18,
                'country_id' => 225,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            402 => 
            array (
                'id' => 19,
                'country_id' => 225,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            403 => 
            array (
                'id' => 20,
                'country_id' => 225,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            404 => 
            array (
                'id' => 21,
                'country_id' => 225,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            405 => 
            array (
                'id' => 22,
                'country_id' => 225,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            406 => 
            array (
                'id' => 23,
                'country_id' => 225,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            407 => 
            array (
                'id' => 24,
                'country_id' => 225,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            408 => 
            array (
                'id' => 25,
                'country_id' => 225,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            409 => 
            array (
                'id' => 26,
                'country_id' => 225,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            410 => 
            array (
                'id' => 27,
                'country_id' => 225,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            411 => 
            array (
                'id' => 28,
                'country_id' => 225,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            412 => 
            array (
                'id' => 29,
                'country_id' => 225,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            413 => 
            array (
                'id' => 30,
                'country_id' => 225,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            414 => 
            array (
                'id' => 31,
                'country_id' => 225,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            415 => 
            array (
                'id' => 32,
                'country_id' => 225,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            416 => 
            array (
                'id' => 33,
                'country_id' => 225,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            417 => 
            array (
                'id' => 34,
                'country_id' => 225,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            418 => 
            array (
                'id' => 35,
                'country_id' => 225,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            419 => 
            array (
                'id' => 36,
                'country_id' => 225,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            420 => 
            array (
                'id' => 37,
                'country_id' => 225,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            421 => 
            array (
                'id' => 38,
                'country_id' => 225,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            422 => 
            array (
                'id' => 39,
                'country_id' => 225,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            423 => 
            array (
                'id' => 40,
                'country_id' => 225,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            424 => 
            array (
                'id' => 41,
                'country_id' => 225,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            425 => 
            array (
                'id' => 42,
                'country_id' => 225,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            426 => 
            array (
                'id' => 43,
                'country_id' => 225,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            427 => 
            array (
                'id' => 44,
                'country_id' => 225,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            428 => 
            array (
                'id' => 45,
                'country_id' => 225,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            429 => 
            array (
                'id' => 46,
                'country_id' => 225,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            430 => 
            array (
                'id' => 47,
                'country_id' => 225,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            431 => 
            array (
                'id' => 48,
                'country_id' => 225,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            432 => 
            array (
                'id' => 49,
                'country_id' => 225,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            433 => 
            array (
                'id' => 50,
                'country_id' => 225,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            434 => 
            array (
                'id' => 51,
                'country_id' => 225,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            435 => 
            array (
                'id' => 52,
                'country_id' => 225,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            436 => 
            array (
                'id' => 53,
                'country_id' => 225,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            437 => 
            array (
                'id' => 54,
                'country_id' => 225,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            438 => 
            array (
                'id' => 55,
                'country_id' => 225,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            439 => 
            array (
                'id' => 56,
                'country_id' => 225,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            440 => 
            array (
                'id' => 57,
                'country_id' => 225,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            441 => 
            array (
                'id' => 58,
                'country_id' => 225,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            442 => 
            array (
                'id' => 59,
                'country_id' => 225,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            443 => 
            array (
                'id' => 60,
                'country_id' => 225,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            444 => 
            array (
                'id' => 61,
                'country_id' => 225,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            445 => 
            array (
                'id' => 62,
                'country_id' => 225,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            446 => 
            array (
                'id' => 63,
                'country_id' => 225,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            447 => 
            array (
                'id' => 64,
                'country_id' => 225,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            448 => 
            array (
                'id' => 65,
                'country_id' => 162,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            449 => 
            array (
                'id' => 66,
                'country_id' => 162,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            450 => 
            array (
                'id' => 67,
                'country_id' => 162,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            451 => 
            array (
                'id' => 68,
                'country_id' => 162,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            452 => 
            array (
                'id' => 69,
                'country_id' => 162,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            453 => 
            array (
                'id' => 70,
                'country_id' => 162,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            454 => 
            array (
                'id' => 71,
                'country_id' => 162,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            455 => 
            array (
                'id' => 72,
                'country_id' => 162,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            456 => 
            array (
                'id' => 73,
                'country_id' => 162,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            457 => 
            array (
                'id' => 74,
                'country_id' => 162,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            458 => 
            array (
                'id' => 75,
                'country_id' => 162,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            459 => 
            array (
                'id' => 76,
                'country_id' => 162,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            460 => 
            array (
                'id' => 77,
                'country_id' => 162,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            461 => 
            array (
                'id' => 78,
                'country_id' => 162,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            462 => 
            array (
                'id' => 79,
                'country_id' => 162,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            463 => 
            array (
                'id' => 80,
                'country_id' => 162,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            464 => 
            array (
                'id' => 81,
                'country_id' => 162,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            465 => 
            array (
                'id' => 82,
                'country_id' => 162,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            466 => 
            array (
                'id' => 83,
                'country_id' => 162,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            467 => 
            array (
                'id' => 84,
                'country_id' => 162,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            468 => 
            array (
                'id' => 85,
                'country_id' => 162,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            469 => 
            array (
                'id' => 86,
                'country_id' => 162,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            470 => 
            array (
                'id' => 87,
                'country_id' => 162,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            471 => 
            array (
                'id' => 88,
                'country_id' => 162,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            472 => 
            array (
                'id' => 89,
                'country_id' => 162,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            473 => 
            array (
                'id' => 90,
                'country_id' => 162,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            474 => 
            array (
                'id' => 91,
                'country_id' => 162,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            475 => 
            array (
                'id' => 92,
                'country_id' => 162,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            476 => 
            array (
                'id' => 93,
                'country_id' => 162,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            477 => 
            array (
                'id' => 94,
                'country_id' => 162,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            478 => 
            array (
                'id' => 95,
                'country_id' => 162,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            479 => 
            array (
                'id' => 96,
                'country_id' => 162,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            480 => 
            array (
                'id' => 97,
                'country_id' => 162,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            481 => 
            array (
                'id' => 98,
                'country_id' => 162,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            482 => 
            array (
                'id' => 99,
                'country_id' => 162,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            483 => 
            array (
                'id' => 100,
                'country_id' => 162,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            484 => 
            array (
                'id' => 101,
                'country_id' => 162,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            485 => 
            array (
                'id' => 102,
                'country_id' => 162,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            486 => 
            array (
                'id' => 103,
                'country_id' => 162,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            487 => 
            array (
                'id' => 104,
                'country_id' => 162,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            488 => 
            array (
                'id' => 105,
                'country_id' => 162,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            489 => 
            array (
                'id' => 106,
                'country_id' => 162,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            490 => 
            array (
                'id' => 107,
                'country_id' => 162,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            491 => 
            array (
                'id' => 108,
                'country_id' => 162,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            492 => 
            array (
                'id' => 109,
                'country_id' => 162,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            493 => 
            array (
                'id' => 110,
                'country_id' => 162,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            494 => 
            array (
                'id' => 111,
                'country_id' => 162,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            495 => 
            array (
                'id' => 112,
                'country_id' => 162,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            496 => 
            array (
                'id' => 113,
                'country_id' => 162,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            497 => 
            array (
                'id' => 114,
                'country_id' => 162,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            498 => 
            array (
                'id' => 115,
                'country_id' => 162,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            499 => 
            array (
                'id' => 116,
                'country_id' => 162,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
        ));
        \DB::table('partnerservicesroutings')->insert(array (
            0 => 
            array (
                'id' => 117,
                'country_id' => 162,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            1 => 
            array (
                'id' => 118,
                'country_id' => 162,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            2 => 
            array (
                'id' => 119,
                'country_id' => 162,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            3 => 
            array (
                'id' => 120,
                'country_id' => 162,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            4 => 
            array (
                'id' => 121,
                'country_id' => 162,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            5 => 
            array (
                'id' => 122,
                'country_id' => 162,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            6 => 
            array (
                'id' => 123,
                'country_id' => 162,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            7 => 
            array (
                'id' => 124,
                'country_id' => 162,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            8 => 
            array (
                'id' => 125,
                'country_id' => 162,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            9 => 
            array (
                'id' => 126,
                'country_id' => 162,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            10 => 
            array (
                'id' => 127,
                'country_id' => 162,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            11 => 
            array (
                'id' => 128,
                'country_id' => 162,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            12 => 
            array (
                'id' => 1,
                'country_id' => 225,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            13 => 
            array (
                'id' => 2,
                'country_id' => 225,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 0,
                'product_id' => 1,
                'service_id' => 120,
            ),
            14 => 
            array (
                'id' => 3,
                'country_id' => 225,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 69,
            ),
            15 => 
            array (
                'id' => 4,
                'country_id' => 225,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            16 => 
            array (
                'id' => 5,
                'country_id' => 225,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            17 => 
            array (
                'id' => 6,
                'country_id' => 225,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            18 => 
            array (
                'id' => 7,
                'country_id' => 225,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            19 => 
            array (
                'id' => 8,
                'country_id' => 225,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            20 => 
            array (
                'id' => 9,
                'country_id' => 225,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            21 => 
            array (
                'id' => 10,
                'country_id' => 225,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            22 => 
            array (
                'id' => 11,
                'country_id' => 225,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            23 => 
            array (
                'id' => 12,
                'country_id' => 225,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            24 => 
            array (
                'id' => 13,
                'country_id' => 225,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            25 => 
            array (
                'id' => 14,
                'country_id' => 225,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            26 => 
            array (
                'id' => 15,
                'country_id' => 225,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            27 => 
            array (
                'id' => 16,
                'country_id' => 225,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            28 => 
            array (
                'id' => 17,
                'country_id' => 225,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            29 => 
            array (
                'id' => 18,
                'country_id' => 225,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            30 => 
            array (
                'id' => 19,
                'country_id' => 225,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            31 => 
            array (
                'id' => 20,
                'country_id' => 225,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            32 => 
            array (
                'id' => 21,
                'country_id' => 225,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            33 => 
            array (
                'id' => 22,
                'country_id' => 225,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            34 => 
            array (
                'id' => 23,
                'country_id' => 225,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            35 => 
            array (
                'id' => 24,
                'country_id' => 225,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            36 => 
            array (
                'id' => 25,
                'country_id' => 225,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            37 => 
            array (
                'id' => 26,
                'country_id' => 225,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            38 => 
            array (
                'id' => 27,
                'country_id' => 225,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            39 => 
            array (
                'id' => 28,
                'country_id' => 225,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            40 => 
            array (
                'id' => 29,
                'country_id' => 225,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            41 => 
            array (
                'id' => 30,
                'country_id' => 225,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            42 => 
            array (
                'id' => 31,
                'country_id' => 225,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            43 => 
            array (
                'id' => 32,
                'country_id' => 225,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            44 => 
            array (
                'id' => 33,
                'country_id' => 225,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            45 => 
            array (
                'id' => 34,
                'country_id' => 225,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            46 => 
            array (
                'id' => 35,
                'country_id' => 225,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            47 => 
            array (
                'id' => 36,
                'country_id' => 225,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            48 => 
            array (
                'id' => 37,
                'country_id' => 225,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            49 => 
            array (
                'id' => 38,
                'country_id' => 225,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            50 => 
            array (
                'id' => 39,
                'country_id' => 225,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            51 => 
            array (
                'id' => 40,
                'country_id' => 225,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            52 => 
            array (
                'id' => 41,
                'country_id' => 225,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            53 => 
            array (
                'id' => 42,
                'country_id' => 225,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            54 => 
            array (
                'id' => 43,
                'country_id' => 225,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            55 => 
            array (
                'id' => 44,
                'country_id' => 225,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            56 => 
            array (
                'id' => 45,
                'country_id' => 225,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            57 => 
            array (
                'id' => 46,
                'country_id' => 225,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            58 => 
            array (
                'id' => 47,
                'country_id' => 225,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            59 => 
            array (
                'id' => 48,
                'country_id' => 225,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            60 => 
            array (
                'id' => 49,
                'country_id' => 225,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            61 => 
            array (
                'id' => 50,
                'country_id' => 225,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            62 => 
            array (
                'id' => 51,
                'country_id' => 225,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            63 => 
            array (
                'id' => 52,
                'country_id' => 225,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            64 => 
            array (
                'id' => 53,
                'country_id' => 225,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            65 => 
            array (
                'id' => 54,
                'country_id' => 225,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            66 => 
            array (
                'id' => 55,
                'country_id' => 225,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            67 => 
            array (
                'id' => 56,
                'country_id' => 225,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            68 => 
            array (
                'id' => 57,
                'country_id' => 225,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            69 => 
            array (
                'id' => 58,
                'country_id' => 225,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            70 => 
            array (
                'id' => 59,
                'country_id' => 225,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            71 => 
            array (
                'id' => 60,
                'country_id' => 225,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            72 => 
            array (
                'id' => 61,
                'country_id' => 225,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            73 => 
            array (
                'id' => 62,
                'country_id' => 225,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            74 => 
            array (
                'id' => 63,
                'country_id' => 225,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            75 => 
            array (
                'id' => 64,
                'country_id' => 225,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 2,
            ),
            76 => 
            array (
                'id' => 65,
                'country_id' => 162,
                'from_weight' => '0.00',
                'to_weight' => '0.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            77 => 
            array (
                'id' => 66,
                'country_id' => 162,
                'from_weight' => '0.25',
                'to_weight' => '0.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            78 => 
            array (
                'id' => 67,
                'country_id' => 162,
                'from_weight' => '0.50',
                'to_weight' => '0.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            79 => 
            array (
                'id' => 68,
                'country_id' => 162,
                'from_weight' => '0.75',
                'to_weight' => '1.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            80 => 
            array (
                'id' => 69,
                'country_id' => 162,
                'from_weight' => '1.00',
                'to_weight' => '1.25',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            81 => 
            array (
                'id' => 70,
                'country_id' => 162,
                'from_weight' => '1.25',
                'to_weight' => '1.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            82 => 
            array (
                'id' => 71,
                'country_id' => 162,
                'from_weight' => '1.50',
                'to_weight' => '1.75',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            83 => 
            array (
                'id' => 72,
                'country_id' => 162,
                'from_weight' => '1.75',
                'to_weight' => '2.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            84 => 
            array (
                'id' => 73,
                'country_id' => 162,
                'from_weight' => '2.00',
                'to_weight' => '2.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            85 => 
            array (
                'id' => 74,
                'country_id' => 162,
                'from_weight' => '2.50',
                'to_weight' => '3.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            86 => 
            array (
                'id' => 75,
                'country_id' => 162,
                'from_weight' => '3.00',
                'to_weight' => '3.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            87 => 
            array (
                'id' => 76,
                'country_id' => 162,
                'from_weight' => '3.50',
                'to_weight' => '4.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            88 => 
            array (
                'id' => 77,
                'country_id' => 162,
                'from_weight' => '4.00',
                'to_weight' => '4.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            89 => 
            array (
                'id' => 78,
                'country_id' => 162,
                'from_weight' => '4.50',
                'to_weight' => '5.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            90 => 
            array (
                'id' => 79,
                'country_id' => 162,
                'from_weight' => '5.00',
                'to_weight' => '5.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            91 => 
            array (
                'id' => 80,
                'country_id' => 162,
                'from_weight' => '5.50',
                'to_weight' => '6.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            92 => 
            array (
                'id' => 81,
                'country_id' => 162,
                'from_weight' => '6.00',
                'to_weight' => '6.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            93 => 
            array (
                'id' => 82,
                'country_id' => 162,
                'from_weight' => '6.50',
                'to_weight' => '7.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            94 => 
            array (
                'id' => 83,
                'country_id' => 162,
                'from_weight' => '7.00',
                'to_weight' => '7.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            95 => 
            array (
                'id' => 84,
                'country_id' => 162,
                'from_weight' => '7.50',
                'to_weight' => '8.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            96 => 
            array (
                'id' => 85,
                'country_id' => 162,
                'from_weight' => '8.00',
                'to_weight' => '8.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            97 => 
            array (
                'id' => 86,
                'country_id' => 162,
                'from_weight' => '8.50',
                'to_weight' => '9.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            98 => 
            array (
                'id' => 87,
                'country_id' => 162,
                'from_weight' => '9.00',
                'to_weight' => '9.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            99 => 
            array (
                'id' => 88,
                'country_id' => 162,
                'from_weight' => '9.50',
                'to_weight' => '10.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            100 => 
            array (
                'id' => 89,
                'country_id' => 162,
                'from_weight' => '10.00',
                'to_weight' => '10.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            101 => 
            array (
                'id' => 90,
                'country_id' => 162,
                'from_weight' => '10.50',
                'to_weight' => '11.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            102 => 
            array (
                'id' => 91,
                'country_id' => 162,
                'from_weight' => '11.00',
                'to_weight' => '11.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            103 => 
            array (
                'id' => 92,
                'country_id' => 162,
                'from_weight' => '11.50',
                'to_weight' => '12.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            104 => 
            array (
                'id' => 93,
                'country_id' => 162,
                'from_weight' => '12.00',
                'to_weight' => '12.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            105 => 
            array (
                'id' => 94,
                'country_id' => 162,
                'from_weight' => '12.50',
                'to_weight' => '13.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            106 => 
            array (
                'id' => 95,
                'country_id' => 162,
                'from_weight' => '13.00',
                'to_weight' => '13.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            107 => 
            array (
                'id' => 96,
                'country_id' => 162,
                'from_weight' => '13.50',
                'to_weight' => '14.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            108 => 
            array (
                'id' => 97,
                'country_id' => 162,
                'from_weight' => '14.00',
                'to_weight' => '14.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            109 => 
            array (
                'id' => 98,
                'country_id' => 162,
                'from_weight' => '14.50',
                'to_weight' => '15.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            110 => 
            array (
                'id' => 99,
                'country_id' => 162,
                'from_weight' => '15.00',
                'to_weight' => '15.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            111 => 
            array (
                'id' => 100,
                'country_id' => 162,
                'from_weight' => '15.50',
                'to_weight' => '16.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            112 => 
            array (
                'id' => 101,
                'country_id' => 162,
                'from_weight' => '16.00',
                'to_weight' => '16.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            113 => 
            array (
                'id' => 102,
                'country_id' => 162,
                'from_weight' => '16.50',
                'to_weight' => '17.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            114 => 
            array (
                'id' => 103,
                'country_id' => 162,
                'from_weight' => '17.00',
                'to_weight' => '17.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            115 => 
            array (
                'id' => 104,
                'country_id' => 162,
                'from_weight' => '17.50',
                'to_weight' => '18.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            116 => 
            array (
                'id' => 105,
                'country_id' => 162,
                'from_weight' => '18.00',
                'to_weight' => '18.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            117 => 
            array (
                'id' => 106,
                'country_id' => 162,
                'from_weight' => '18.50',
                'to_weight' => '19.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            118 => 
            array (
                'id' => 107,
                'country_id' => 162,
                'from_weight' => '19.00',
                'to_weight' => '19.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            119 => 
            array (
                'id' => 108,
                'country_id' => 162,
                'from_weight' => '19.50',
                'to_weight' => '20.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            120 => 
            array (
                'id' => 109,
                'country_id' => 162,
                'from_weight' => '20.00',
                'to_weight' => '20.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            121 => 
            array (
                'id' => 110,
                'country_id' => 162,
                'from_weight' => '20.50',
                'to_weight' => '21.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            122 => 
            array (
                'id' => 111,
                'country_id' => 162,
                'from_weight' => '21.00',
                'to_weight' => '21.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            123 => 
            array (
                'id' => 112,
                'country_id' => 162,
                'from_weight' => '21.50',
                'to_weight' => '22.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            124 => 
            array (
                'id' => 113,
                'country_id' => 162,
                'from_weight' => '22.00',
                'to_weight' => '22.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            125 => 
            array (
                'id' => 114,
                'country_id' => 162,
                'from_weight' => '22.50',
                'to_weight' => '23.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            126 => 
            array (
                'id' => 115,
                'country_id' => 162,
                'from_weight' => '23.00',
                'to_weight' => '23.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            127 => 
            array (
                'id' => 116,
                'country_id' => 162,
                'from_weight' => '23.50',
                'to_weight' => '24.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            128 => 
            array (
                'id' => 117,
                'country_id' => 162,
                'from_weight' => '24.00',
                'to_weight' => '24.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            129 => 
            array (
                'id' => 118,
                'country_id' => 162,
                'from_weight' => '24.50',
                'to_weight' => '25.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            130 => 
            array (
                'id' => 119,
                'country_id' => 162,
                'from_weight' => '25.00',
                'to_weight' => '25.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            131 => 
            array (
                'id' => 120,
                'country_id' => 162,
                'from_weight' => '25.50',
                'to_weight' => '26.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            132 => 
            array (
                'id' => 121,
                'country_id' => 162,
                'from_weight' => '26.00',
                'to_weight' => '26.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            133 => 
            array (
                'id' => 122,
                'country_id' => 162,
                'from_weight' => '26.50',
                'to_weight' => '27.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            134 => 
            array (
                'id' => 123,
                'country_id' => 162,
                'from_weight' => '27.00',
                'to_weight' => '27.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            135 => 
            array (
                'id' => 124,
                'country_id' => 162,
                'from_weight' => '27.50',
                'to_weight' => '28.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            136 => 
            array (
                'id' => 125,
                'country_id' => 162,
                'from_weight' => '28.00',
                'to_weight' => '28.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            137 => 
            array (
                'id' => 126,
                'country_id' => 162,
                'from_weight' => '28.50',
                'to_weight' => '29.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            138 => 
            array (
                'id' => 127,
                'country_id' => 162,
                'from_weight' => '29.00',
                'to_weight' => '29.50',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
            139 => 
            array (
                'id' => 128,
                'country_id' => 162,
                'from_weight' => '29.50',
                'to_weight' => '30.00',
                'status' => 1,
                'product_id' => 1,
                'service_id' => 4,
            ),
        ));
        
        
    }
}