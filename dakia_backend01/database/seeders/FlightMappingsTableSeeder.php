<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FlightMappingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('flight_mappings')->delete();
        
        \DB::table('flight_mappings')->insert(array (
            0 => 
            array (
                'id' => 3,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 7,
                'is_delete' => 0,
            ),
            1 => 
            array (
                'id' => 8,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            2 => 
            array (
                'id' => 9,
                'flight_info_id' => 2,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 23,
                'is_delete' => 0,
            ),
            3 => 
            array (
                'id' => 10,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 24,
                'is_delete' => 0,
            ),
            4 => 
            array (
                'id' => 11,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 26,
                'is_delete' => 0,
            ),
            5 => 
            array (
                'id' => 12,
                'flight_info_id' => 4,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            6 => 
            array (
                'id' => 13,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 30,
                'is_delete' => 0,
            ),
            7 => 
            array (
                'id' => 14,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 31,
                'is_delete' => 0,
            ),
            8 => 
            array (
                'id' => 15,
                'flight_info_id' => 6,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 32,
                'is_delete' => 0,
            ),
            9 => 
            array (
                'id' => 16,
                'flight_info_id' => 7,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 34,
                'is_delete' => 0,
            ),
            10 => 
            array (
                'id' => 17,
                'flight_info_id' => 8,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 35,
                'is_delete' => 0,
            ),
            11 => 
            array (
                'id' => 18,
                'flight_info_id' => 9,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 36,
                'is_delete' => 0,
            ),
            12 => 
            array (
                'id' => 19,
                'flight_info_id' => 10,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 37,
                'is_delete' => 0,
            ),
            13 => 
            array (
                'id' => 20,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 38,
                'is_delete' => 0,
            ),
            14 => 
            array (
                'id' => 21,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 39,
                'is_delete' => 0,
            ),
            15 => 
            array (
                'id' => 22,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 22,
                'is_delete' => 0,
            ),
            16 => 
            array (
                'id' => 23,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 21,
                'is_delete' => 0,
            ),
            17 => 
            array (
                'id' => 24,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            18 => 
            array (
                'id' => 25,
                'flight_info_id' => 13,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 40,
                'is_delete' => 0,
            ),
            19 => 
            array (
                'id' => 26,
                'flight_info_id' => 14,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 41,
                'is_delete' => 0,
            ),
            20 => 
            array (
                'id' => 27,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 42,
                'is_delete' => 0,
            ),
            21 => 
            array (
                'id' => 28,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 43,
                'is_delete' => 0,
            ),
            22 => 
            array (
                'id' => 29,
                'flight_info_id' => 16,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 44,
                'is_delete' => 0,
            ),
            23 => 
            array (
                'id' => 30,
                'flight_info_id' => 17,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            24 => 
            array (
                'id' => 31,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 47,
                'is_delete' => 0,
            ),
            25 => 
            array (
                'id' => 32,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 46,
                'is_delete' => 0,
            ),
            26 => 
            array (
                'id' => 33,
                'flight_info_id' => 19,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 48,
                'is_delete' => 0,
            ),
            27 => 
            array (
                'id' => 34,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 49,
                'is_delete' => 0,
            ),
            28 => 
            array (
                'id' => 35,
                'flight_info_id' => 20,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 50,
                'is_delete' => 0,
            ),
            29 => 
            array (
                'id' => 36,
                'flight_info_id' => 21,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 51,
                'is_delete' => 0,
            ),
            30 => 
            array (
                'id' => 37,
                'flight_info_id' => 22,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 52,
                'is_delete' => 0,
            ),
            31 => 
            array (
                'id' => 38,
                'flight_info_id' => 23,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 53,
                'is_delete' => 0,
            ),
            32 => 
            array (
                'id' => 39,
                'flight_info_id' => 24,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 54,
                'is_delete' => 0,
            ),
            33 => 
            array (
                'id' => 41,
                'flight_info_id' => 27,
                'flight_number' => NULL,
                'mawb' => '9090909',
                'mawb_id' => 0,
                'is_delete' => 0,
            ),
            34 => 
            array (
                'id' => 42,
                'flight_info_id' => 30,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 109,
                'is_delete' => 0,
            ),
            35 => 
            array (
                'id' => 43,
                'flight_info_id' => 33,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 110,
                'is_delete' => 0,
            ),
            36 => 
            array (
                'id' => 44,
                'flight_info_id' => 37,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 1,
                'is_delete' => 0,
            ),
            37 => 
            array (
                'id' => 45,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 111,
                'is_delete' => 0,
            ),
            38 => 
            array (
                'id' => 46,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 112,
                'is_delete' => 0,
            ),
            39 => 
            array (
                'id' => 47,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 113,
                'is_delete' => 0,
            ),
            40 => 
            array (
                'id' => 48,
                'flight_info_id' => 32,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 114,
                'is_delete' => 0,
            ),
            41 => 
            array (
                'id' => 3,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 7,
                'is_delete' => 0,
            ),
            42 => 
            array (
                'id' => 8,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            43 => 
            array (
                'id' => 9,
                'flight_info_id' => 2,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 23,
                'is_delete' => 0,
            ),
            44 => 
            array (
                'id' => 10,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 24,
                'is_delete' => 0,
            ),
            45 => 
            array (
                'id' => 11,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 26,
                'is_delete' => 0,
            ),
            46 => 
            array (
                'id' => 12,
                'flight_info_id' => 4,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            47 => 
            array (
                'id' => 13,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 30,
                'is_delete' => 0,
            ),
            48 => 
            array (
                'id' => 14,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 31,
                'is_delete' => 0,
            ),
            49 => 
            array (
                'id' => 15,
                'flight_info_id' => 6,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 32,
                'is_delete' => 0,
            ),
            50 => 
            array (
                'id' => 16,
                'flight_info_id' => 7,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 34,
                'is_delete' => 0,
            ),
            51 => 
            array (
                'id' => 17,
                'flight_info_id' => 8,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 35,
                'is_delete' => 0,
            ),
            52 => 
            array (
                'id' => 18,
                'flight_info_id' => 9,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 36,
                'is_delete' => 0,
            ),
            53 => 
            array (
                'id' => 19,
                'flight_info_id' => 10,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 37,
                'is_delete' => 0,
            ),
            54 => 
            array (
                'id' => 20,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 38,
                'is_delete' => 0,
            ),
            55 => 
            array (
                'id' => 21,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 39,
                'is_delete' => 0,
            ),
            56 => 
            array (
                'id' => 22,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 22,
                'is_delete' => 0,
            ),
            57 => 
            array (
                'id' => 23,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 21,
                'is_delete' => 0,
            ),
            58 => 
            array (
                'id' => 24,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            59 => 
            array (
                'id' => 25,
                'flight_info_id' => 13,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 40,
                'is_delete' => 0,
            ),
            60 => 
            array (
                'id' => 26,
                'flight_info_id' => 14,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 41,
                'is_delete' => 0,
            ),
            61 => 
            array (
                'id' => 27,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 42,
                'is_delete' => 0,
            ),
            62 => 
            array (
                'id' => 28,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 43,
                'is_delete' => 0,
            ),
            63 => 
            array (
                'id' => 29,
                'flight_info_id' => 16,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 44,
                'is_delete' => 0,
            ),
            64 => 
            array (
                'id' => 30,
                'flight_info_id' => 17,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            65 => 
            array (
                'id' => 31,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 47,
                'is_delete' => 0,
            ),
            66 => 
            array (
                'id' => 32,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 46,
                'is_delete' => 0,
            ),
            67 => 
            array (
                'id' => 33,
                'flight_info_id' => 19,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 48,
                'is_delete' => 0,
            ),
            68 => 
            array (
                'id' => 34,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 49,
                'is_delete' => 0,
            ),
            69 => 
            array (
                'id' => 35,
                'flight_info_id' => 20,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 50,
                'is_delete' => 0,
            ),
            70 => 
            array (
                'id' => 36,
                'flight_info_id' => 21,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 51,
                'is_delete' => 0,
            ),
            71 => 
            array (
                'id' => 37,
                'flight_info_id' => 22,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 52,
                'is_delete' => 0,
            ),
            72 => 
            array (
                'id' => 38,
                'flight_info_id' => 23,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 53,
                'is_delete' => 0,
            ),
            73 => 
            array (
                'id' => 39,
                'flight_info_id' => 24,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 54,
                'is_delete' => 0,
            ),
            74 => 
            array (
                'id' => 41,
                'flight_info_id' => 27,
                'flight_number' => NULL,
                'mawb' => '9090909',
                'mawb_id' => 0,
                'is_delete' => 0,
            ),
            75 => 
            array (
                'id' => 42,
                'flight_info_id' => 30,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 109,
                'is_delete' => 0,
            ),
            76 => 
            array (
                'id' => 43,
                'flight_info_id' => 33,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 110,
                'is_delete' => 0,
            ),
            77 => 
            array (
                'id' => 44,
                'flight_info_id' => 37,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 1,
                'is_delete' => 0,
            ),
            78 => 
            array (
                'id' => 45,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 111,
                'is_delete' => 0,
            ),
            79 => 
            array (
                'id' => 46,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 112,
                'is_delete' => 0,
            ),
            80 => 
            array (
                'id' => 47,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 113,
                'is_delete' => 0,
            ),
            81 => 
            array (
                'id' => 48,
                'flight_info_id' => 32,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 114,
                'is_delete' => 0,
            ),
            82 => 
            array (
                'id' => 3,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 7,
                'is_delete' => 0,
            ),
            83 => 
            array (
                'id' => 8,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            84 => 
            array (
                'id' => 9,
                'flight_info_id' => 2,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 23,
                'is_delete' => 0,
            ),
            85 => 
            array (
                'id' => 10,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 24,
                'is_delete' => 0,
            ),
            86 => 
            array (
                'id' => 11,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 26,
                'is_delete' => 0,
            ),
            87 => 
            array (
                'id' => 12,
                'flight_info_id' => 4,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            88 => 
            array (
                'id' => 13,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 30,
                'is_delete' => 0,
            ),
            89 => 
            array (
                'id' => 14,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 31,
                'is_delete' => 0,
            ),
            90 => 
            array (
                'id' => 15,
                'flight_info_id' => 6,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 32,
                'is_delete' => 0,
            ),
            91 => 
            array (
                'id' => 16,
                'flight_info_id' => 7,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 34,
                'is_delete' => 0,
            ),
            92 => 
            array (
                'id' => 17,
                'flight_info_id' => 8,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 35,
                'is_delete' => 0,
            ),
            93 => 
            array (
                'id' => 18,
                'flight_info_id' => 9,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 36,
                'is_delete' => 0,
            ),
            94 => 
            array (
                'id' => 19,
                'flight_info_id' => 10,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 37,
                'is_delete' => 0,
            ),
            95 => 
            array (
                'id' => 20,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 38,
                'is_delete' => 0,
            ),
            96 => 
            array (
                'id' => 21,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 39,
                'is_delete' => 0,
            ),
            97 => 
            array (
                'id' => 22,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 22,
                'is_delete' => 0,
            ),
            98 => 
            array (
                'id' => 23,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 21,
                'is_delete' => 0,
            ),
            99 => 
            array (
                'id' => 24,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            100 => 
            array (
                'id' => 25,
                'flight_info_id' => 13,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 40,
                'is_delete' => 0,
            ),
            101 => 
            array (
                'id' => 26,
                'flight_info_id' => 14,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 41,
                'is_delete' => 0,
            ),
            102 => 
            array (
                'id' => 27,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 42,
                'is_delete' => 0,
            ),
            103 => 
            array (
                'id' => 28,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 43,
                'is_delete' => 0,
            ),
            104 => 
            array (
                'id' => 29,
                'flight_info_id' => 16,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 44,
                'is_delete' => 0,
            ),
            105 => 
            array (
                'id' => 30,
                'flight_info_id' => 17,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            106 => 
            array (
                'id' => 31,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 47,
                'is_delete' => 0,
            ),
            107 => 
            array (
                'id' => 32,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 46,
                'is_delete' => 0,
            ),
            108 => 
            array (
                'id' => 33,
                'flight_info_id' => 19,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 48,
                'is_delete' => 0,
            ),
            109 => 
            array (
                'id' => 34,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 49,
                'is_delete' => 0,
            ),
            110 => 
            array (
                'id' => 35,
                'flight_info_id' => 20,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 50,
                'is_delete' => 0,
            ),
            111 => 
            array (
                'id' => 36,
                'flight_info_id' => 21,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 51,
                'is_delete' => 0,
            ),
            112 => 
            array (
                'id' => 37,
                'flight_info_id' => 22,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 52,
                'is_delete' => 0,
            ),
            113 => 
            array (
                'id' => 38,
                'flight_info_id' => 23,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 53,
                'is_delete' => 0,
            ),
            114 => 
            array (
                'id' => 39,
                'flight_info_id' => 24,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 54,
                'is_delete' => 0,
            ),
            115 => 
            array (
                'id' => 41,
                'flight_info_id' => 27,
                'flight_number' => NULL,
                'mawb' => '9090909',
                'mawb_id' => 0,
                'is_delete' => 0,
            ),
            116 => 
            array (
                'id' => 42,
                'flight_info_id' => 30,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 109,
                'is_delete' => 0,
            ),
            117 => 
            array (
                'id' => 43,
                'flight_info_id' => 33,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 110,
                'is_delete' => 0,
            ),
            118 => 
            array (
                'id' => 44,
                'flight_info_id' => 37,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 1,
                'is_delete' => 0,
            ),
            119 => 
            array (
                'id' => 45,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 111,
                'is_delete' => 0,
            ),
            120 => 
            array (
                'id' => 46,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 112,
                'is_delete' => 0,
            ),
            121 => 
            array (
                'id' => 47,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 113,
                'is_delete' => 0,
            ),
            122 => 
            array (
                'id' => 48,
                'flight_info_id' => 32,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 114,
                'is_delete' => 0,
            ),
            123 => 
            array (
                'id' => 3,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 7,
                'is_delete' => 0,
            ),
            124 => 
            array (
                'id' => 8,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            125 => 
            array (
                'id' => 9,
                'flight_info_id' => 2,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 23,
                'is_delete' => 0,
            ),
            126 => 
            array (
                'id' => 10,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 24,
                'is_delete' => 0,
            ),
            127 => 
            array (
                'id' => 11,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 26,
                'is_delete' => 0,
            ),
            128 => 
            array (
                'id' => 12,
                'flight_info_id' => 4,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            129 => 
            array (
                'id' => 13,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 30,
                'is_delete' => 0,
            ),
            130 => 
            array (
                'id' => 14,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 31,
                'is_delete' => 0,
            ),
            131 => 
            array (
                'id' => 15,
                'flight_info_id' => 6,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 32,
                'is_delete' => 0,
            ),
            132 => 
            array (
                'id' => 16,
                'flight_info_id' => 7,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 34,
                'is_delete' => 0,
            ),
            133 => 
            array (
                'id' => 17,
                'flight_info_id' => 8,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 35,
                'is_delete' => 0,
            ),
            134 => 
            array (
                'id' => 18,
                'flight_info_id' => 9,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 36,
                'is_delete' => 0,
            ),
            135 => 
            array (
                'id' => 19,
                'flight_info_id' => 10,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 37,
                'is_delete' => 0,
            ),
            136 => 
            array (
                'id' => 20,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 38,
                'is_delete' => 0,
            ),
            137 => 
            array (
                'id' => 21,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 39,
                'is_delete' => 0,
            ),
            138 => 
            array (
                'id' => 22,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 22,
                'is_delete' => 0,
            ),
            139 => 
            array (
                'id' => 23,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 21,
                'is_delete' => 0,
            ),
            140 => 
            array (
                'id' => 24,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            141 => 
            array (
                'id' => 25,
                'flight_info_id' => 13,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 40,
                'is_delete' => 0,
            ),
            142 => 
            array (
                'id' => 26,
                'flight_info_id' => 14,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 41,
                'is_delete' => 0,
            ),
            143 => 
            array (
                'id' => 27,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 42,
                'is_delete' => 0,
            ),
            144 => 
            array (
                'id' => 28,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 43,
                'is_delete' => 0,
            ),
            145 => 
            array (
                'id' => 29,
                'flight_info_id' => 16,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 44,
                'is_delete' => 0,
            ),
            146 => 
            array (
                'id' => 30,
                'flight_info_id' => 17,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            147 => 
            array (
                'id' => 31,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 47,
                'is_delete' => 0,
            ),
            148 => 
            array (
                'id' => 32,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 46,
                'is_delete' => 0,
            ),
            149 => 
            array (
                'id' => 33,
                'flight_info_id' => 19,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 48,
                'is_delete' => 0,
            ),
            150 => 
            array (
                'id' => 34,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 49,
                'is_delete' => 0,
            ),
            151 => 
            array (
                'id' => 35,
                'flight_info_id' => 20,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 50,
                'is_delete' => 0,
            ),
            152 => 
            array (
                'id' => 36,
                'flight_info_id' => 21,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 51,
                'is_delete' => 0,
            ),
            153 => 
            array (
                'id' => 37,
                'flight_info_id' => 22,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 52,
                'is_delete' => 0,
            ),
            154 => 
            array (
                'id' => 38,
                'flight_info_id' => 23,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 53,
                'is_delete' => 0,
            ),
            155 => 
            array (
                'id' => 39,
                'flight_info_id' => 24,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 54,
                'is_delete' => 0,
            ),
            156 => 
            array (
                'id' => 41,
                'flight_info_id' => 27,
                'flight_number' => NULL,
                'mawb' => '9090909',
                'mawb_id' => 0,
                'is_delete' => 0,
            ),
            157 => 
            array (
                'id' => 42,
                'flight_info_id' => 30,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 109,
                'is_delete' => 0,
            ),
            158 => 
            array (
                'id' => 43,
                'flight_info_id' => 33,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 110,
                'is_delete' => 0,
            ),
            159 => 
            array (
                'id' => 44,
                'flight_info_id' => 37,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 1,
                'is_delete' => 0,
            ),
            160 => 
            array (
                'id' => 45,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 111,
                'is_delete' => 0,
            ),
            161 => 
            array (
                'id' => 46,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 112,
                'is_delete' => 0,
            ),
            162 => 
            array (
                'id' => 47,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 113,
                'is_delete' => 0,
            ),
            163 => 
            array (
                'id' => 48,
                'flight_info_id' => 32,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 114,
                'is_delete' => 0,
            ),
            164 => 
            array (
                'id' => 3,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 7,
                'is_delete' => 0,
            ),
            165 => 
            array (
                'id' => 8,
                'flight_info_id' => 1,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            166 => 
            array (
                'id' => 9,
                'flight_info_id' => 2,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 23,
                'is_delete' => 0,
            ),
            167 => 
            array (
                'id' => 10,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 24,
                'is_delete' => 0,
            ),
            168 => 
            array (
                'id' => 11,
                'flight_info_id' => 3,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 26,
                'is_delete' => 0,
            ),
            169 => 
            array (
                'id' => 12,
                'flight_info_id' => 4,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            170 => 
            array (
                'id' => 13,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 30,
                'is_delete' => 0,
            ),
            171 => 
            array (
                'id' => 14,
                'flight_info_id' => 5,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 31,
                'is_delete' => 0,
            ),
            172 => 
            array (
                'id' => 15,
                'flight_info_id' => 6,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 32,
                'is_delete' => 0,
            ),
            173 => 
            array (
                'id' => 16,
                'flight_info_id' => 7,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 34,
                'is_delete' => 0,
            ),
            174 => 
            array (
                'id' => 17,
                'flight_info_id' => 8,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 35,
                'is_delete' => 0,
            ),
            175 => 
            array (
                'id' => 18,
                'flight_info_id' => 9,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 36,
                'is_delete' => 0,
            ),
            176 => 
            array (
                'id' => 19,
                'flight_info_id' => 10,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 37,
                'is_delete' => 0,
            ),
            177 => 
            array (
                'id' => 20,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 38,
                'is_delete' => 0,
            ),
            178 => 
            array (
                'id' => 21,
                'flight_info_id' => 11,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 39,
                'is_delete' => 0,
            ),
            179 => 
            array (
                'id' => 22,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 22,
                'is_delete' => 0,
            ),
            180 => 
            array (
                'id' => 23,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 21,
                'is_delete' => 0,
            ),
            181 => 
            array (
                'id' => 24,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 8,
                'is_delete' => 0,
            ),
            182 => 
            array (
                'id' => 25,
                'flight_info_id' => 13,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 40,
                'is_delete' => 0,
            ),
            183 => 
            array (
                'id' => 26,
                'flight_info_id' => 14,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 41,
                'is_delete' => 0,
            ),
            184 => 
            array (
                'id' => 27,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 42,
                'is_delete' => 0,
            ),
            185 => 
            array (
                'id' => 28,
                'flight_info_id' => 15,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 43,
                'is_delete' => 0,
            ),
            186 => 
            array (
                'id' => 29,
                'flight_info_id' => 16,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 44,
                'is_delete' => 0,
            ),
            187 => 
            array (
                'id' => 30,
                'flight_info_id' => 17,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 29,
                'is_delete' => 0,
            ),
            188 => 
            array (
                'id' => 31,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 47,
                'is_delete' => 0,
            ),
            189 => 
            array (
                'id' => 32,
                'flight_info_id' => 18,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 46,
                'is_delete' => 0,
            ),
            190 => 
            array (
                'id' => 33,
                'flight_info_id' => 19,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 48,
                'is_delete' => 0,
            ),
            191 => 
            array (
                'id' => 34,
                'flight_info_id' => 12,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 49,
                'is_delete' => 0,
            ),
            192 => 
            array (
                'id' => 35,
                'flight_info_id' => 20,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 50,
                'is_delete' => 0,
            ),
            193 => 
            array (
                'id' => 36,
                'flight_info_id' => 21,
                'flight_number' => '',
                'mawb' => '',
                'mawb_id' => 51,
                'is_delete' => 0,
            ),
            194 => 
            array (
                'id' => 37,
                'flight_info_id' => 22,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 52,
                'is_delete' => 0,
            ),
            195 => 
            array (
                'id' => 38,
                'flight_info_id' => 23,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 53,
                'is_delete' => 0,
            ),
            196 => 
            array (
                'id' => 39,
                'flight_info_id' => 24,
                'flight_number' => NULL,
                'mawb' => NULL,
                'mawb_id' => 54,
                'is_delete' => 0,
            ),
            197 => 
            array (
                'id' => 41,
                'flight_info_id' => 27,
                'flight_number' => NULL,
                'mawb' => '9090909',
                'mawb_id' => 0,
                'is_delete' => 0,
            ),
            198 => 
            array (
                'id' => 42,
                'flight_info_id' => 30,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 109,
                'is_delete' => 0,
            ),
            199 => 
            array (
                'id' => 43,
                'flight_info_id' => 33,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 110,
                'is_delete' => 0,
            ),
            200 => 
            array (
                'id' => 44,
                'flight_info_id' => 37,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 1,
                'is_delete' => 0,
            ),
            201 => 
            array (
                'id' => 45,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 111,
                'is_delete' => 0,
            ),
            202 => 
            array (
                'id' => 46,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 112,
                'is_delete' => 0,
            ),
            203 => 
            array (
                'id' => 47,
                'flight_info_id' => 20,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 113,
                'is_delete' => 0,
            ),
            204 => 
            array (
                'id' => 48,
                'flight_info_id' => 32,
                'flight_number' => NULL,
                'mawb' => '',
                'mawb_id' => 114,
                'is_delete' => 0,
            ),
        ));
        
        
    }
}