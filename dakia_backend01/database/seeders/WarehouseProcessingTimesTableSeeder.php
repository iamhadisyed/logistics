<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WarehouseProcessingTimesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('warehouse_processing_times')->delete();
        
        \DB::table('warehouse_processing_times')->insert(array (
            0 => 
            array (
                'id' => 3,
                'warehouse_id' => 13,
                'parcel_processing_time' => 39,
                'service_id' => 162,
            ),
            1 => 
            array (
                'id' => 5,
                'warehouse_id' => 11,
                'parcel_processing_time' => 985,
                'service_id' => 215,
            ),
            2 => 
            array (
                'id' => 9,
                'warehouse_id' => 9,
                'parcel_processing_time' => 101,
                'service_id' => 79,
            ),
            3 => 
            array (
                'id' => 10,
                'warehouse_id' => 13,
                'parcel_processing_time' => 55,
                'service_id' => 162,
            ),
            4 => 
            array (
                'id' => 12,
                'warehouse_id' => 9,
                'parcel_processing_time' => 55,
                'service_id' => 79,
            ),
            5 => 
            array (
                'id' => 13,
                'warehouse_id' => 9,
                'parcel_processing_time' => 88,
                'service_id' => 109,
            ),
            6 => 
            array (
                'id' => 14,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 214,
            ),
            7 => 
            array (
                'id' => 15,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 62,
            ),
            8 => 
            array (
                'id' => 16,
                'warehouse_id' => 28,
                'parcel_processing_time' => 55,
                'service_id' => 148,
            ),
            9 => 
            array (
                'id' => 17,
                'warehouse_id' => 9,
                'parcel_processing_time' => 8,
                'service_id' => 93,
            ),
            10 => 
            array (
                'id' => 18,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 17,
            ),
            11 => 
            array (
                'id' => 19,
                'warehouse_id' => 9,
                'parcel_processing_time' => 5,
                'service_id' => 102,
            ),
            12 => 
            array (
                'id' => 20,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 113,
            ),
            13 => 
            array (
                'id' => 21,
                'warehouse_id' => 27,
                'parcel_processing_time' => 5,
                'service_id' => 116,
            ),
            14 => 
            array (
                'id' => 22,
                'warehouse_id' => 29,
                'parcel_processing_time' => 5,
                'service_id' => 129,
            ),
            15 => 
            array (
                'id' => 23,
                'warehouse_id' => 12,
                'parcel_processing_time' => 55,
                'service_id' => 220,
            ),
            16 => 
            array (
                'id' => 24,
                'warehouse_id' => 32,
                'parcel_processing_time' => 55,
                'service_id' => 164,
            ),
            17 => 
            array (
                'id' => 25,
                'warehouse_id' => 11,
                'parcel_processing_time' => 4,
                'service_id' => 182,
            ),
            18 => 
            array (
                'id' => 26,
                'warehouse_id' => 31,
                'parcel_processing_time' => 3,
                'service_id' => 182,
            ),
            19 => 
            array (
                'id' => 27,
                'warehouse_id' => 32,
                'parcel_processing_time' => 3,
                'service_id' => 219,
            ),
            20 => 
            array (
                'id' => 28,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            21 => 
            array (
                'id' => 29,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 97,
            ),
            22 => 
            array (
                'id' => 30,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 152,
            ),
            23 => 
            array (
                'id' => 31,
                'warehouse_id' => 28,
                'parcel_processing_time' => 2,
                'service_id' => 153,
            ),
            24 => 
            array (
                'id' => 32,
                'warehouse_id' => 10,
                'parcel_processing_time' => 7,
                'service_id' => 215,
            ),
            25 => 
            array (
                'id' => 33,
                'warehouse_id' => 28,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            26 => 
            array (
                'id' => 34,
                'warehouse_id' => 31,
                'parcel_processing_time' => 99,
                'service_id' => 109,
            ),
            27 => 
            array (
                'id' => 35,
                'warehouse_id' => 32,
                'parcel_processing_time' => 8,
                'service_id' => 61,
            ),
            28 => 
            array (
                'id' => 36,
                'warehouse_id' => 28,
                'parcel_processing_time' => 22,
                'service_id' => 223,
            ),
            29 => 
            array (
                'id' => 37,
                'warehouse_id' => 29,
                'parcel_processing_time' => 22,
                'service_id' => 39,
            ),
            30 => 
            array (
                'id' => 38,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 102,
            ),
            31 => 
            array (
                'id' => 39,
                'warehouse_id' => 28,
                'parcel_processing_time' => 33,
                'service_id' => 79,
            ),
            32 => 
            array (
                'id' => 40,
                'warehouse_id' => 11,
                'parcel_processing_time' => 7,
                'service_id' => 152,
            ),
            33 => 
            array (
                'id' => 41,
                'warehouse_id' => 31,
                'parcel_processing_time' => 87,
                'service_id' => 113,
            ),
            34 => 
            array (
                'id' => 42,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 218,
            ),
            35 => 
            array (
                'id' => 43,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 100,
            ),
            36 => 
            array (
                'id' => 44,
                'warehouse_id' => 10,
                'parcel_processing_time' => 8,
                'service_id' => 109,
            ),
            37 => 
            array (
                'id' => 45,
                'warehouse_id' => 28,
                'parcel_processing_time' => 56,
                'service_id' => 143,
            ),
            38 => 
            array (
                'id' => 46,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 220,
            ),
            39 => 
            array (
                'id' => 47,
                'warehouse_id' => 28,
                'parcel_processing_time' => 66,
                'service_id' => 61,
            ),
            40 => 
            array (
                'id' => 48,
                'warehouse_id' => 26,
                'parcel_processing_time' => 6,
                'service_id' => 69,
            ),
            41 => 
            array (
                'id' => 49,
                'warehouse_id' => 31,
                'parcel_processing_time' => 44,
                'service_id' => 186,
            ),
            42 => 
            array (
                'id' => 50,
                'warehouse_id' => 28,
                'parcel_processing_time' => 6,
                'service_id' => 111,
            ),
            43 => 
            array (
                'id' => 51,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 221,
            ),
            44 => 
            array (
                'id' => 52,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 58,
            ),
            45 => 
            array (
                'id' => 53,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 187,
            ),
            46 => 
            array (
                'id' => 54,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 207,
            ),
            47 => 
            array (
                'id' => 55,
                'warehouse_id' => 27,
                'parcel_processing_time' => 99,
                'service_id' => 76,
            ),
            48 => 
            array (
                'id' => 3,
                'warehouse_id' => 13,
                'parcel_processing_time' => 39,
                'service_id' => 162,
            ),
            49 => 
            array (
                'id' => 5,
                'warehouse_id' => 11,
                'parcel_processing_time' => 985,
                'service_id' => 215,
            ),
            50 => 
            array (
                'id' => 9,
                'warehouse_id' => 9,
                'parcel_processing_time' => 101,
                'service_id' => 79,
            ),
            51 => 
            array (
                'id' => 10,
                'warehouse_id' => 13,
                'parcel_processing_time' => 55,
                'service_id' => 162,
            ),
            52 => 
            array (
                'id' => 12,
                'warehouse_id' => 9,
                'parcel_processing_time' => 55,
                'service_id' => 79,
            ),
            53 => 
            array (
                'id' => 13,
                'warehouse_id' => 9,
                'parcel_processing_time' => 88,
                'service_id' => 109,
            ),
            54 => 
            array (
                'id' => 14,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 214,
            ),
            55 => 
            array (
                'id' => 15,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 62,
            ),
            56 => 
            array (
                'id' => 16,
                'warehouse_id' => 28,
                'parcel_processing_time' => 55,
                'service_id' => 148,
            ),
            57 => 
            array (
                'id' => 17,
                'warehouse_id' => 9,
                'parcel_processing_time' => 8,
                'service_id' => 93,
            ),
            58 => 
            array (
                'id' => 18,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 17,
            ),
            59 => 
            array (
                'id' => 19,
                'warehouse_id' => 9,
                'parcel_processing_time' => 5,
                'service_id' => 102,
            ),
            60 => 
            array (
                'id' => 20,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 113,
            ),
            61 => 
            array (
                'id' => 21,
                'warehouse_id' => 27,
                'parcel_processing_time' => 5,
                'service_id' => 116,
            ),
            62 => 
            array (
                'id' => 22,
                'warehouse_id' => 29,
                'parcel_processing_time' => 5,
                'service_id' => 129,
            ),
            63 => 
            array (
                'id' => 23,
                'warehouse_id' => 12,
                'parcel_processing_time' => 55,
                'service_id' => 220,
            ),
            64 => 
            array (
                'id' => 24,
                'warehouse_id' => 32,
                'parcel_processing_time' => 55,
                'service_id' => 164,
            ),
            65 => 
            array (
                'id' => 25,
                'warehouse_id' => 11,
                'parcel_processing_time' => 4,
                'service_id' => 182,
            ),
            66 => 
            array (
                'id' => 26,
                'warehouse_id' => 31,
                'parcel_processing_time' => 3,
                'service_id' => 182,
            ),
            67 => 
            array (
                'id' => 27,
                'warehouse_id' => 32,
                'parcel_processing_time' => 3,
                'service_id' => 219,
            ),
            68 => 
            array (
                'id' => 28,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            69 => 
            array (
                'id' => 29,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 97,
            ),
            70 => 
            array (
                'id' => 30,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 152,
            ),
            71 => 
            array (
                'id' => 31,
                'warehouse_id' => 28,
                'parcel_processing_time' => 2,
                'service_id' => 153,
            ),
            72 => 
            array (
                'id' => 32,
                'warehouse_id' => 10,
                'parcel_processing_time' => 7,
                'service_id' => 215,
            ),
            73 => 
            array (
                'id' => 33,
                'warehouse_id' => 28,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            74 => 
            array (
                'id' => 34,
                'warehouse_id' => 31,
                'parcel_processing_time' => 99,
                'service_id' => 109,
            ),
            75 => 
            array (
                'id' => 35,
                'warehouse_id' => 32,
                'parcel_processing_time' => 8,
                'service_id' => 61,
            ),
            76 => 
            array (
                'id' => 36,
                'warehouse_id' => 28,
                'parcel_processing_time' => 22,
                'service_id' => 223,
            ),
            77 => 
            array (
                'id' => 37,
                'warehouse_id' => 29,
                'parcel_processing_time' => 22,
                'service_id' => 39,
            ),
            78 => 
            array (
                'id' => 38,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 102,
            ),
            79 => 
            array (
                'id' => 39,
                'warehouse_id' => 28,
                'parcel_processing_time' => 33,
                'service_id' => 79,
            ),
            80 => 
            array (
                'id' => 40,
                'warehouse_id' => 11,
                'parcel_processing_time' => 7,
                'service_id' => 152,
            ),
            81 => 
            array (
                'id' => 41,
                'warehouse_id' => 31,
                'parcel_processing_time' => 87,
                'service_id' => 113,
            ),
            82 => 
            array (
                'id' => 42,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 218,
            ),
            83 => 
            array (
                'id' => 43,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 100,
            ),
            84 => 
            array (
                'id' => 44,
                'warehouse_id' => 10,
                'parcel_processing_time' => 8,
                'service_id' => 109,
            ),
            85 => 
            array (
                'id' => 45,
                'warehouse_id' => 28,
                'parcel_processing_time' => 56,
                'service_id' => 143,
            ),
            86 => 
            array (
                'id' => 46,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 220,
            ),
            87 => 
            array (
                'id' => 47,
                'warehouse_id' => 28,
                'parcel_processing_time' => 66,
                'service_id' => 61,
            ),
            88 => 
            array (
                'id' => 48,
                'warehouse_id' => 26,
                'parcel_processing_time' => 6,
                'service_id' => 69,
            ),
            89 => 
            array (
                'id' => 49,
                'warehouse_id' => 31,
                'parcel_processing_time' => 44,
                'service_id' => 186,
            ),
            90 => 
            array (
                'id' => 50,
                'warehouse_id' => 28,
                'parcel_processing_time' => 6,
                'service_id' => 111,
            ),
            91 => 
            array (
                'id' => 51,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 221,
            ),
            92 => 
            array (
                'id' => 52,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 58,
            ),
            93 => 
            array (
                'id' => 53,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 187,
            ),
            94 => 
            array (
                'id' => 54,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 207,
            ),
            95 => 
            array (
                'id' => 55,
                'warehouse_id' => 27,
                'parcel_processing_time' => 99,
                'service_id' => 76,
            ),
            96 => 
            array (
                'id' => 3,
                'warehouse_id' => 13,
                'parcel_processing_time' => 39,
                'service_id' => 162,
            ),
            97 => 
            array (
                'id' => 5,
                'warehouse_id' => 11,
                'parcel_processing_time' => 985,
                'service_id' => 215,
            ),
            98 => 
            array (
                'id' => 9,
                'warehouse_id' => 9,
                'parcel_processing_time' => 101,
                'service_id' => 79,
            ),
            99 => 
            array (
                'id' => 10,
                'warehouse_id' => 13,
                'parcel_processing_time' => 55,
                'service_id' => 162,
            ),
            100 => 
            array (
                'id' => 12,
                'warehouse_id' => 9,
                'parcel_processing_time' => 55,
                'service_id' => 79,
            ),
            101 => 
            array (
                'id' => 13,
                'warehouse_id' => 9,
                'parcel_processing_time' => 88,
                'service_id' => 109,
            ),
            102 => 
            array (
                'id' => 14,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 214,
            ),
            103 => 
            array (
                'id' => 15,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 62,
            ),
            104 => 
            array (
                'id' => 16,
                'warehouse_id' => 28,
                'parcel_processing_time' => 55,
                'service_id' => 148,
            ),
            105 => 
            array (
                'id' => 17,
                'warehouse_id' => 9,
                'parcel_processing_time' => 8,
                'service_id' => 93,
            ),
            106 => 
            array (
                'id' => 18,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 17,
            ),
            107 => 
            array (
                'id' => 19,
                'warehouse_id' => 9,
                'parcel_processing_time' => 5,
                'service_id' => 102,
            ),
            108 => 
            array (
                'id' => 20,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 113,
            ),
            109 => 
            array (
                'id' => 21,
                'warehouse_id' => 27,
                'parcel_processing_time' => 5,
                'service_id' => 116,
            ),
            110 => 
            array (
                'id' => 22,
                'warehouse_id' => 29,
                'parcel_processing_time' => 5,
                'service_id' => 129,
            ),
            111 => 
            array (
                'id' => 23,
                'warehouse_id' => 12,
                'parcel_processing_time' => 55,
                'service_id' => 220,
            ),
            112 => 
            array (
                'id' => 24,
                'warehouse_id' => 32,
                'parcel_processing_time' => 55,
                'service_id' => 164,
            ),
            113 => 
            array (
                'id' => 25,
                'warehouse_id' => 11,
                'parcel_processing_time' => 4,
                'service_id' => 182,
            ),
            114 => 
            array (
                'id' => 26,
                'warehouse_id' => 31,
                'parcel_processing_time' => 3,
                'service_id' => 182,
            ),
            115 => 
            array (
                'id' => 27,
                'warehouse_id' => 32,
                'parcel_processing_time' => 3,
                'service_id' => 219,
            ),
            116 => 
            array (
                'id' => 28,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            117 => 
            array (
                'id' => 29,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 97,
            ),
            118 => 
            array (
                'id' => 30,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 152,
            ),
            119 => 
            array (
                'id' => 31,
                'warehouse_id' => 28,
                'parcel_processing_time' => 2,
                'service_id' => 153,
            ),
            120 => 
            array (
                'id' => 32,
                'warehouse_id' => 10,
                'parcel_processing_time' => 7,
                'service_id' => 215,
            ),
            121 => 
            array (
                'id' => 33,
                'warehouse_id' => 28,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            122 => 
            array (
                'id' => 34,
                'warehouse_id' => 31,
                'parcel_processing_time' => 99,
                'service_id' => 109,
            ),
            123 => 
            array (
                'id' => 35,
                'warehouse_id' => 32,
                'parcel_processing_time' => 8,
                'service_id' => 61,
            ),
            124 => 
            array (
                'id' => 36,
                'warehouse_id' => 28,
                'parcel_processing_time' => 22,
                'service_id' => 223,
            ),
            125 => 
            array (
                'id' => 37,
                'warehouse_id' => 29,
                'parcel_processing_time' => 22,
                'service_id' => 39,
            ),
            126 => 
            array (
                'id' => 38,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 102,
            ),
            127 => 
            array (
                'id' => 39,
                'warehouse_id' => 28,
                'parcel_processing_time' => 33,
                'service_id' => 79,
            ),
            128 => 
            array (
                'id' => 40,
                'warehouse_id' => 11,
                'parcel_processing_time' => 7,
                'service_id' => 152,
            ),
            129 => 
            array (
                'id' => 41,
                'warehouse_id' => 31,
                'parcel_processing_time' => 87,
                'service_id' => 113,
            ),
            130 => 
            array (
                'id' => 42,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 218,
            ),
            131 => 
            array (
                'id' => 43,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 100,
            ),
            132 => 
            array (
                'id' => 44,
                'warehouse_id' => 10,
                'parcel_processing_time' => 8,
                'service_id' => 109,
            ),
            133 => 
            array (
                'id' => 45,
                'warehouse_id' => 28,
                'parcel_processing_time' => 56,
                'service_id' => 143,
            ),
            134 => 
            array (
                'id' => 46,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 220,
            ),
            135 => 
            array (
                'id' => 47,
                'warehouse_id' => 28,
                'parcel_processing_time' => 66,
                'service_id' => 61,
            ),
            136 => 
            array (
                'id' => 48,
                'warehouse_id' => 26,
                'parcel_processing_time' => 6,
                'service_id' => 69,
            ),
            137 => 
            array (
                'id' => 49,
                'warehouse_id' => 31,
                'parcel_processing_time' => 44,
                'service_id' => 186,
            ),
            138 => 
            array (
                'id' => 50,
                'warehouse_id' => 28,
                'parcel_processing_time' => 6,
                'service_id' => 111,
            ),
            139 => 
            array (
                'id' => 51,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 221,
            ),
            140 => 
            array (
                'id' => 52,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 58,
            ),
            141 => 
            array (
                'id' => 53,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 187,
            ),
            142 => 
            array (
                'id' => 54,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 207,
            ),
            143 => 
            array (
                'id' => 55,
                'warehouse_id' => 27,
                'parcel_processing_time' => 99,
                'service_id' => 76,
            ),
            144 => 
            array (
                'id' => 3,
                'warehouse_id' => 13,
                'parcel_processing_time' => 39,
                'service_id' => 162,
            ),
            145 => 
            array (
                'id' => 5,
                'warehouse_id' => 11,
                'parcel_processing_time' => 985,
                'service_id' => 215,
            ),
            146 => 
            array (
                'id' => 9,
                'warehouse_id' => 9,
                'parcel_processing_time' => 101,
                'service_id' => 79,
            ),
            147 => 
            array (
                'id' => 10,
                'warehouse_id' => 13,
                'parcel_processing_time' => 55,
                'service_id' => 162,
            ),
            148 => 
            array (
                'id' => 12,
                'warehouse_id' => 9,
                'parcel_processing_time' => 55,
                'service_id' => 79,
            ),
            149 => 
            array (
                'id' => 13,
                'warehouse_id' => 9,
                'parcel_processing_time' => 88,
                'service_id' => 109,
            ),
            150 => 
            array (
                'id' => 14,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 214,
            ),
            151 => 
            array (
                'id' => 15,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 62,
            ),
            152 => 
            array (
                'id' => 16,
                'warehouse_id' => 28,
                'parcel_processing_time' => 55,
                'service_id' => 148,
            ),
            153 => 
            array (
                'id' => 17,
                'warehouse_id' => 9,
                'parcel_processing_time' => 8,
                'service_id' => 93,
            ),
            154 => 
            array (
                'id' => 18,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 17,
            ),
            155 => 
            array (
                'id' => 19,
                'warehouse_id' => 9,
                'parcel_processing_time' => 5,
                'service_id' => 102,
            ),
            156 => 
            array (
                'id' => 20,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 113,
            ),
            157 => 
            array (
                'id' => 21,
                'warehouse_id' => 27,
                'parcel_processing_time' => 5,
                'service_id' => 116,
            ),
            158 => 
            array (
                'id' => 22,
                'warehouse_id' => 29,
                'parcel_processing_time' => 5,
                'service_id' => 129,
            ),
            159 => 
            array (
                'id' => 23,
                'warehouse_id' => 12,
                'parcel_processing_time' => 55,
                'service_id' => 220,
            ),
            160 => 
            array (
                'id' => 24,
                'warehouse_id' => 32,
                'parcel_processing_time' => 55,
                'service_id' => 164,
            ),
            161 => 
            array (
                'id' => 25,
                'warehouse_id' => 11,
                'parcel_processing_time' => 4,
                'service_id' => 182,
            ),
            162 => 
            array (
                'id' => 26,
                'warehouse_id' => 31,
                'parcel_processing_time' => 3,
                'service_id' => 182,
            ),
            163 => 
            array (
                'id' => 27,
                'warehouse_id' => 32,
                'parcel_processing_time' => 3,
                'service_id' => 219,
            ),
            164 => 
            array (
                'id' => 28,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            165 => 
            array (
                'id' => 29,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 97,
            ),
            166 => 
            array (
                'id' => 30,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 152,
            ),
            167 => 
            array (
                'id' => 31,
                'warehouse_id' => 28,
                'parcel_processing_time' => 2,
                'service_id' => 153,
            ),
            168 => 
            array (
                'id' => 32,
                'warehouse_id' => 10,
                'parcel_processing_time' => 7,
                'service_id' => 215,
            ),
            169 => 
            array (
                'id' => 33,
                'warehouse_id' => 28,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            170 => 
            array (
                'id' => 34,
                'warehouse_id' => 31,
                'parcel_processing_time' => 99,
                'service_id' => 109,
            ),
            171 => 
            array (
                'id' => 35,
                'warehouse_id' => 32,
                'parcel_processing_time' => 8,
                'service_id' => 61,
            ),
            172 => 
            array (
                'id' => 36,
                'warehouse_id' => 28,
                'parcel_processing_time' => 22,
                'service_id' => 223,
            ),
            173 => 
            array (
                'id' => 37,
                'warehouse_id' => 29,
                'parcel_processing_time' => 22,
                'service_id' => 39,
            ),
            174 => 
            array (
                'id' => 38,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 102,
            ),
            175 => 
            array (
                'id' => 39,
                'warehouse_id' => 28,
                'parcel_processing_time' => 33,
                'service_id' => 79,
            ),
            176 => 
            array (
                'id' => 40,
                'warehouse_id' => 11,
                'parcel_processing_time' => 7,
                'service_id' => 152,
            ),
            177 => 
            array (
                'id' => 41,
                'warehouse_id' => 31,
                'parcel_processing_time' => 87,
                'service_id' => 113,
            ),
            178 => 
            array (
                'id' => 42,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 218,
            ),
            179 => 
            array (
                'id' => 43,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 100,
            ),
            180 => 
            array (
                'id' => 44,
                'warehouse_id' => 10,
                'parcel_processing_time' => 8,
                'service_id' => 109,
            ),
            181 => 
            array (
                'id' => 45,
                'warehouse_id' => 28,
                'parcel_processing_time' => 56,
                'service_id' => 143,
            ),
            182 => 
            array (
                'id' => 46,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 220,
            ),
            183 => 
            array (
                'id' => 47,
                'warehouse_id' => 28,
                'parcel_processing_time' => 66,
                'service_id' => 61,
            ),
            184 => 
            array (
                'id' => 48,
                'warehouse_id' => 26,
                'parcel_processing_time' => 6,
                'service_id' => 69,
            ),
            185 => 
            array (
                'id' => 49,
                'warehouse_id' => 31,
                'parcel_processing_time' => 44,
                'service_id' => 186,
            ),
            186 => 
            array (
                'id' => 50,
                'warehouse_id' => 28,
                'parcel_processing_time' => 6,
                'service_id' => 111,
            ),
            187 => 
            array (
                'id' => 51,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 221,
            ),
            188 => 
            array (
                'id' => 52,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 58,
            ),
            189 => 
            array (
                'id' => 53,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 187,
            ),
            190 => 
            array (
                'id' => 54,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 207,
            ),
            191 => 
            array (
                'id' => 55,
                'warehouse_id' => 27,
                'parcel_processing_time' => 99,
                'service_id' => 76,
            ),
            192 => 
            array (
                'id' => 3,
                'warehouse_id' => 13,
                'parcel_processing_time' => 39,
                'service_id' => 162,
            ),
            193 => 
            array (
                'id' => 5,
                'warehouse_id' => 11,
                'parcel_processing_time' => 985,
                'service_id' => 215,
            ),
            194 => 
            array (
                'id' => 9,
                'warehouse_id' => 9,
                'parcel_processing_time' => 101,
                'service_id' => 79,
            ),
            195 => 
            array (
                'id' => 10,
                'warehouse_id' => 13,
                'parcel_processing_time' => 55,
                'service_id' => 162,
            ),
            196 => 
            array (
                'id' => 12,
                'warehouse_id' => 9,
                'parcel_processing_time' => 55,
                'service_id' => 79,
            ),
            197 => 
            array (
                'id' => 13,
                'warehouse_id' => 9,
                'parcel_processing_time' => 88,
                'service_id' => 109,
            ),
            198 => 
            array (
                'id' => 14,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 214,
            ),
            199 => 
            array (
                'id' => 15,
                'warehouse_id' => 9,
                'parcel_processing_time' => 99,
                'service_id' => 62,
            ),
            200 => 
            array (
                'id' => 16,
                'warehouse_id' => 28,
                'parcel_processing_time' => 55,
                'service_id' => 148,
            ),
            201 => 
            array (
                'id' => 17,
                'warehouse_id' => 9,
                'parcel_processing_time' => 8,
                'service_id' => 93,
            ),
            202 => 
            array (
                'id' => 18,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 17,
            ),
            203 => 
            array (
                'id' => 19,
                'warehouse_id' => 9,
                'parcel_processing_time' => 5,
                'service_id' => 102,
            ),
            204 => 
            array (
                'id' => 20,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 113,
            ),
            205 => 
            array (
                'id' => 21,
                'warehouse_id' => 27,
                'parcel_processing_time' => 5,
                'service_id' => 116,
            ),
            206 => 
            array (
                'id' => 22,
                'warehouse_id' => 29,
                'parcel_processing_time' => 5,
                'service_id' => 129,
            ),
            207 => 
            array (
                'id' => 23,
                'warehouse_id' => 12,
                'parcel_processing_time' => 55,
                'service_id' => 220,
            ),
            208 => 
            array (
                'id' => 24,
                'warehouse_id' => 32,
                'parcel_processing_time' => 55,
                'service_id' => 164,
            ),
            209 => 
            array (
                'id' => 25,
                'warehouse_id' => 11,
                'parcel_processing_time' => 4,
                'service_id' => 182,
            ),
            210 => 
            array (
                'id' => 26,
                'warehouse_id' => 31,
                'parcel_processing_time' => 3,
                'service_id' => 182,
            ),
            211 => 
            array (
                'id' => 27,
                'warehouse_id' => 32,
                'parcel_processing_time' => 3,
                'service_id' => 219,
            ),
            212 => 
            array (
                'id' => 28,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            213 => 
            array (
                'id' => 29,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 97,
            ),
            214 => 
            array (
                'id' => 30,
                'warehouse_id' => 29,
                'parcel_processing_time' => 8,
                'service_id' => 152,
            ),
            215 => 
            array (
                'id' => 31,
                'warehouse_id' => 28,
                'parcel_processing_time' => 2,
                'service_id' => 153,
            ),
            216 => 
            array (
                'id' => 32,
                'warehouse_id' => 10,
                'parcel_processing_time' => 7,
                'service_id' => 215,
            ),
            217 => 
            array (
                'id' => 33,
                'warehouse_id' => 28,
                'parcel_processing_time' => 9,
                'service_id' => 164,
            ),
            218 => 
            array (
                'id' => 34,
                'warehouse_id' => 31,
                'parcel_processing_time' => 99,
                'service_id' => 109,
            ),
            219 => 
            array (
                'id' => 35,
                'warehouse_id' => 32,
                'parcel_processing_time' => 8,
                'service_id' => 61,
            ),
            220 => 
            array (
                'id' => 36,
                'warehouse_id' => 28,
                'parcel_processing_time' => 22,
                'service_id' => 223,
            ),
            221 => 
            array (
                'id' => 37,
                'warehouse_id' => 29,
                'parcel_processing_time' => 22,
                'service_id' => 39,
            ),
            222 => 
            array (
                'id' => 38,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 102,
            ),
            223 => 
            array (
                'id' => 39,
                'warehouse_id' => 28,
                'parcel_processing_time' => 33,
                'service_id' => 79,
            ),
            224 => 
            array (
                'id' => 40,
                'warehouse_id' => 11,
                'parcel_processing_time' => 7,
                'service_id' => 152,
            ),
            225 => 
            array (
                'id' => 41,
                'warehouse_id' => 31,
                'parcel_processing_time' => 87,
                'service_id' => 113,
            ),
            226 => 
            array (
                'id' => 42,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 218,
            ),
            227 => 
            array (
                'id' => 43,
                'warehouse_id' => 28,
                'parcel_processing_time' => 5,
                'service_id' => 100,
            ),
            228 => 
            array (
                'id' => 44,
                'warehouse_id' => 10,
                'parcel_processing_time' => 8,
                'service_id' => 109,
            ),
            229 => 
            array (
                'id' => 45,
                'warehouse_id' => 28,
                'parcel_processing_time' => 56,
                'service_id' => 143,
            ),
            230 => 
            array (
                'id' => 46,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 220,
            ),
            231 => 
            array (
                'id' => 47,
                'warehouse_id' => 28,
                'parcel_processing_time' => 66,
                'service_id' => 61,
            ),
            232 => 
            array (
                'id' => 48,
                'warehouse_id' => 26,
                'parcel_processing_time' => 6,
                'service_id' => 69,
            ),
            233 => 
            array (
                'id' => 49,
                'warehouse_id' => 31,
                'parcel_processing_time' => 44,
                'service_id' => 186,
            ),
            234 => 
            array (
                'id' => 50,
                'warehouse_id' => 28,
                'parcel_processing_time' => 6,
                'service_id' => 111,
            ),
            235 => 
            array (
                'id' => 51,
                'warehouse_id' => 10,
                'parcel_processing_time' => 3,
                'service_id' => 221,
            ),
            236 => 
            array (
                'id' => 52,
                'warehouse_id' => 31,
                'parcel_processing_time' => 5,
                'service_id' => 58,
            ),
            237 => 
            array (
                'id' => 53,
                'warehouse_id' => 29,
                'parcel_processing_time' => 6,
                'service_id' => 187,
            ),
            238 => 
            array (
                'id' => 54,
                'warehouse_id' => 29,
                'parcel_processing_time' => 9,
                'service_id' => 207,
            ),
            239 => 
            array (
                'id' => 55,
                'warehouse_id' => 27,
                'parcel_processing_time' => 99,
                'service_id' => 76,
            ),
        ));
        
        
    }
}