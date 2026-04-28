<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CarrierServiceDefaultRulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carrier_service_default_rules')->delete();
        
        \DB::table('carrier_service_default_rules')->insert(array (
            0 => 
            array (
                'id' => 98,
                'serviceid' => 1,
                'agentid' => 4,
                'from_weight' => '1.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            1 => 
            array (
                'id' => 99,
                'serviceid' => 1,
                'agentid' => 3,
                'from_weight' => '2.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            2 => 
            array (
                'id' => 123,
                'serviceid' => 63,
                'agentid' => 4,
                'from_weight' => '0.001',
                'to_weight' => '1.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            3 => 
            array (
                'id' => 228,
                'serviceid' => 229,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            4 => 
            array (
                'id' => 251,
                'serviceid' => 15,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            5 => 
            array (
                'id' => 253,
                'serviceid' => 17,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            6 => 
            array (
                'id' => 258,
                'serviceid' => 13,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            7 => 
            array (
                'id' => 260,
                'serviceid' => 60,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            8 => 
            array (
                'id' => 261,
                'serviceid' => 199,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            9 => 
            array (
                'id' => 262,
                'serviceid' => 200,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            10 => 
            array (
                'id' => 263,
                'serviceid' => 230,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            11 => 
            array (
                'id' => 272,
                'serviceid' => 128,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            12 => 
            array (
                'id' => 276,
                'serviceid' => 233,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            13 => 
            array (
                'id' => 296,
                'serviceid' => 251,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            14 => 
            array (
                'id' => 297,
                'serviceid' => 252,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            15 => 
            array (
                'id' => 299,
                'serviceid' => 254,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            16 => 
            array (
                'id' => 306,
                'serviceid' => 249,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            17 => 
            array (
                'id' => 311,
                'serviceid' => 257,
                'agentid' => 2,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            18 => 
            array (
                'id' => 319,
                'serviceid' => 258,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            19 => 
            array (
                'id' => 322,
                'serviceid' => 24,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            20 => 
            array (
                'id' => 346,
                'serviceid' => 227,
                'agentid' => 1,
                'from_weight' => '0.010',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            21 => 
            array (
                'id' => 347,
                'serviceid' => 227,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            22 => 
            array (
                'id' => 350,
                'serviceid' => 265,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            23 => 
            array (
                'id' => 351,
                'serviceid' => 265,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            24 => 
            array (
                'id' => 352,
                'serviceid' => 266,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            25 => 
            array (
                'id' => 353,
                'serviceid' => 266,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            26 => 
            array (
                'id' => 354,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            27 => 
            array (
                'id' => 355,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            28 => 
            array (
                'id' => 361,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            29 => 
            array (
                'id' => 362,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            30 => 
            array (
                'id' => 363,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            31 => 
            array (
                'id' => 364,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            32 => 
            array (
                'id' => 365,
                'serviceid' => 267,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            33 => 
            array (
                'id' => 366,
                'serviceid' => 267,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            34 => 
            array (
                'id' => 371,
                'serviceid' => 105,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            35 => 
            array (
                'id' => 372,
                'serviceid' => 105,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            36 => 
            array (
                'id' => 373,
                'serviceid' => 268,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            37 => 
            array (
                'id' => 374,
                'serviceid' => 268,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            38 => 
            array (
                'id' => 377,
                'serviceid' => 270,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            39 => 
            array (
                'id' => 378,
                'serviceid' => 270,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            40 => 
            array (
                'id' => 379,
                'serviceid' => 116,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            41 => 
            array (
                'id' => 380,
                'serviceid' => 116,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            42 => 
            array (
                'id' => 394,
                'serviceid' => 274,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            43 => 
            array (
                'id' => 395,
                'serviceid' => 274,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            44 => 
            array (
                'id' => 396,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            45 => 
            array (
                'id' => 397,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            46 => 
            array (
                'id' => 406,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            47 => 
            array (
                'id' => 407,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            48 => 
            array (
                'id' => 408,
                'serviceid' => 228,
                'agentid' => 43,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            49 => 
            array (
                'id' => 409,
                'serviceid' => 228,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            50 => 
            array (
                'id' => 414,
                'serviceid' => 161,
                'agentid' => 3,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            51 => 
            array (
                'id' => 415,
                'serviceid' => 161,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            52 => 
            array (
                'id' => 429,
                'serviceid' => 260,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '70.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            53 => 
            array (
                'id' => 430,
                'serviceid' => 260,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            54 => 
            array (
                'id' => 449,
                'serviceid' => 160,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            55 => 
            array (
                'id' => 450,
                'serviceid' => 160,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            56 => 
            array (
                'id' => 485,
                'serviceid' => 238,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            57 => 
            array (
                'id' => 486,
                'serviceid' => 238,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            58 => 
            array (
                'id' => 487,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            59 => 
            array (
                'id' => 488,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            60 => 
            array (
                'id' => 489,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            61 => 
            array (
                'id' => 490,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            62 => 
            array (
                'id' => 493,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            63 => 
            array (
                'id' => 494,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            64 => 
            array (
                'id' => 503,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '50.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            65 => 
            array (
                'id' => 504,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            66 => 
            array (
                'id' => 509,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            67 => 
            array (
                'id' => 510,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            68 => 
            array (
                'id' => 511,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            69 => 
            array (
                'id' => 512,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            70 => 
            array (
                'id' => 515,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            71 => 
            array (
                'id' => 516,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            72 => 
            array (
                'id' => 517,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            73 => 
            array (
                'id' => 518,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            74 => 
            array (
                'id' => 519,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            75 => 
            array (
                'id' => 520,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            76 => 
            array (
                'id' => 521,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            77 => 
            array (
                'id' => 522,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            78 => 
            array (
                'id' => 523,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            79 => 
            array (
                'id' => 524,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            80 => 
            array (
                'id' => 533,
                'serviceid' => 250,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            81 => 
            array (
                'id' => 534,
                'serviceid' => 250,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            82 => 
            array (
                'id' => 537,
                'serviceid' => 291,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            83 => 
            array (
                'id' => 538,
                'serviceid' => 291,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            84 => 
            array (
                'id' => 539,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            85 => 
            array (
                'id' => 540,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            86 => 
            array (
                'id' => 541,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            87 => 
            array (
                'id' => 542,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            88 => 
            array (
                'id' => 627,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            89 => 
            array (
                'id' => 628,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            90 => 
            array (
                'id' => 637,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            91 => 
            array (
                'id' => 638,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            92 => 
            array (
                'id' => 646,
                'serviceid' => 273,
                'agentid' => 46,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            93 => 
            array (
                'id' => 648,
                'serviceid' => 259,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            94 => 
            array (
                'id' => 690,
                'serviceid' => 39,
                'agentid' => 46,
                'from_weight' => '0.100',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            95 => 
            array (
                'id' => 702,
                'serviceid' => 276,
                'agentid' => 84,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            96 => 
            array (
                'id' => 763,
                'serviceid' => 114,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            97 => 
            array (
                'id' => 772,
                'serviceid' => 127,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            98 => 
            array (
                'id' => 790,
                'serviceid' => 4,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            99 => 
            array (
                'id' => 809,
                'serviceid' => 300,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            100 => 
            array (
                'id' => 810,
                'serviceid' => 301,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            101 => 
            array (
                'id' => 811,
                'serviceid' => 269,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            102 => 
            array (
                'id' => 812,
                'serviceid' => 302,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            103 => 
            array (
                'id' => 830,
                'serviceid' => 271,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            104 => 
            array (
                'id' => 832,
                'serviceid' => 119,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            105 => 
            array (
                'id' => 835,
                'serviceid' => 303,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            106 => 
            array (
                'id' => 853,
                'serviceid' => 279,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            107 => 
            array (
                'id' => 857,
                'serviceid' => 234,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            108 => 
            array (
                'id' => 862,
                'serviceid' => 263,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            109 => 
            array (
                'id' => 863,
                'serviceid' => 309,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            110 => 
            array (
                'id' => 864,
                'serviceid' => 310,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            111 => 
            array (
                'id' => 865,
                'serviceid' => 69,
                'agentid' => 46,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            112 => 
            array (
                'id' => 866,
                'serviceid' => 69,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            113 => 
            array (
                'id' => 867,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            114 => 
            array (
                'id' => 868,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            115 => 
            array (
                'id' => 869,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            116 => 
            array (
                'id' => 871,
                'serviceid' => 311,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            117 => 
            array (
                'id' => 872,
                'serviceid' => 312,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            118 => 
            array (
                'id' => 874,
                'serviceid' => 313,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            119 => 
            array (
                'id' => 875,
                'serviceid' => 22,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            120 => 
            array (
                'id' => 881,
                'serviceid' => 272,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            121 => 
            array (
                'id' => 882,
                'serviceid' => 280,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            122 => 
            array (
                'id' => 889,
                'serviceid' => 318,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            123 => 
            array (
                'id' => 910,
                'serviceid' => 305,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            124 => 
            array (
                'id' => 987,
                'serviceid' => 246,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            125 => 
            array (
                'id' => 992,
                'serviceid' => 3,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            126 => 
            array (
                'id' => 993,
                'serviceid' => 68,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            127 => 
            array (
                'id' => 995,
                'serviceid' => 16,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            128 => 
            array (
                'id' => 1002,
                'serviceid' => 248,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            129 => 
            array (
                'id' => 1003,
                'serviceid' => 262,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            130 => 
            array (
                'id' => 1006,
                'serviceid' => 329,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            131 => 
            array (
                'id' => 1010,
                'serviceid' => 253,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            132 => 
            array (
                'id' => 1012,
                'serviceid' => 14,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            133 => 
            array (
                'id' => 1026,
                'serviceid' => 239,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            134 => 
            array (
                'id' => 1027,
                'serviceid' => 226,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            135 => 
            array (
                'id' => 1034,
                'serviceid' => 23,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            136 => 
            array (
                'id' => 1035,
                'serviceid' => 23,
                'agentid' => 90,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            137 => 
            array (
                'id' => 1053,
                'serviceid' => 304,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            138 => 
            array (
                'id' => 1054,
                'serviceid' => 304,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '10.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            139 => 
            array (
                'id' => 1055,
                'serviceid' => 38,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            140 => 
            array (
                'id' => 1056,
                'serviceid' => 38,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            141 => 
            array (
                'id' => 1063,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            142 => 
            array (
                'id' => 1064,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            143 => 
            array (
                'id' => 1065,
                'serviceid' => 244,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            144 => 
            array (
                'id' => 1066,
                'serviceid' => 244,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            145 => 
            array (
                'id' => 1067,
                'serviceid' => 245,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            146 => 
            array (
                'id' => 1072,
                'serviceid' => 299,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            147 => 
            array (
                'id' => 1093,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '10.000',
                'to_weight' => '11.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            148 => 
            array (
                'id' => 1094,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '11.500',
                'to_weight' => '12.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            149 => 
            array (
                'id' => 1095,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            150 => 
            array (
                'id' => 1096,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '3.000',
                'to_weight' => '3.500',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            151 => 
            array (
                'id' => 98,
                'serviceid' => 1,
                'agentid' => 4,
                'from_weight' => '1.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            152 => 
            array (
                'id' => 99,
                'serviceid' => 1,
                'agentid' => 3,
                'from_weight' => '2.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            153 => 
            array (
                'id' => 123,
                'serviceid' => 63,
                'agentid' => 4,
                'from_weight' => '0.001',
                'to_weight' => '1.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            154 => 
            array (
                'id' => 228,
                'serviceid' => 229,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            155 => 
            array (
                'id' => 251,
                'serviceid' => 15,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            156 => 
            array (
                'id' => 253,
                'serviceid' => 17,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            157 => 
            array (
                'id' => 258,
                'serviceid' => 13,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            158 => 
            array (
                'id' => 260,
                'serviceid' => 60,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            159 => 
            array (
                'id' => 261,
                'serviceid' => 199,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            160 => 
            array (
                'id' => 262,
                'serviceid' => 200,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            161 => 
            array (
                'id' => 263,
                'serviceid' => 230,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            162 => 
            array (
                'id' => 272,
                'serviceid' => 128,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            163 => 
            array (
                'id' => 276,
                'serviceid' => 233,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            164 => 
            array (
                'id' => 296,
                'serviceid' => 251,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            165 => 
            array (
                'id' => 297,
                'serviceid' => 252,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            166 => 
            array (
                'id' => 299,
                'serviceid' => 254,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            167 => 
            array (
                'id' => 306,
                'serviceid' => 249,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            168 => 
            array (
                'id' => 311,
                'serviceid' => 257,
                'agentid' => 2,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            169 => 
            array (
                'id' => 319,
                'serviceid' => 258,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            170 => 
            array (
                'id' => 322,
                'serviceid' => 24,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            171 => 
            array (
                'id' => 346,
                'serviceid' => 227,
                'agentid' => 1,
                'from_weight' => '0.010',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            172 => 
            array (
                'id' => 347,
                'serviceid' => 227,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            173 => 
            array (
                'id' => 350,
                'serviceid' => 265,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            174 => 
            array (
                'id' => 351,
                'serviceid' => 265,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            175 => 
            array (
                'id' => 352,
                'serviceid' => 266,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            176 => 
            array (
                'id' => 353,
                'serviceid' => 266,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            177 => 
            array (
                'id' => 354,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            178 => 
            array (
                'id' => 355,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            179 => 
            array (
                'id' => 361,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            180 => 
            array (
                'id' => 362,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            181 => 
            array (
                'id' => 363,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            182 => 
            array (
                'id' => 364,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            183 => 
            array (
                'id' => 365,
                'serviceid' => 267,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            184 => 
            array (
                'id' => 366,
                'serviceid' => 267,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            185 => 
            array (
                'id' => 371,
                'serviceid' => 105,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            186 => 
            array (
                'id' => 372,
                'serviceid' => 105,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            187 => 
            array (
                'id' => 373,
                'serviceid' => 268,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            188 => 
            array (
                'id' => 374,
                'serviceid' => 268,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            189 => 
            array (
                'id' => 377,
                'serviceid' => 270,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            190 => 
            array (
                'id' => 378,
                'serviceid' => 270,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            191 => 
            array (
                'id' => 379,
                'serviceid' => 116,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            192 => 
            array (
                'id' => 380,
                'serviceid' => 116,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            193 => 
            array (
                'id' => 394,
                'serviceid' => 274,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            194 => 
            array (
                'id' => 395,
                'serviceid' => 274,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            195 => 
            array (
                'id' => 396,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            196 => 
            array (
                'id' => 397,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            197 => 
            array (
                'id' => 406,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            198 => 
            array (
                'id' => 407,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            199 => 
            array (
                'id' => 408,
                'serviceid' => 228,
                'agentid' => 43,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            200 => 
            array (
                'id' => 409,
                'serviceid' => 228,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            201 => 
            array (
                'id' => 414,
                'serviceid' => 161,
                'agentid' => 3,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            202 => 
            array (
                'id' => 415,
                'serviceid' => 161,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            203 => 
            array (
                'id' => 429,
                'serviceid' => 260,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '70.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            204 => 
            array (
                'id' => 430,
                'serviceid' => 260,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            205 => 
            array (
                'id' => 449,
                'serviceid' => 160,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            206 => 
            array (
                'id' => 450,
                'serviceid' => 160,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            207 => 
            array (
                'id' => 485,
                'serviceid' => 238,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            208 => 
            array (
                'id' => 486,
                'serviceid' => 238,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            209 => 
            array (
                'id' => 487,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            210 => 
            array (
                'id' => 488,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            211 => 
            array (
                'id' => 489,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            212 => 
            array (
                'id' => 490,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            213 => 
            array (
                'id' => 493,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            214 => 
            array (
                'id' => 494,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            215 => 
            array (
                'id' => 503,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '50.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            216 => 
            array (
                'id' => 504,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            217 => 
            array (
                'id' => 509,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            218 => 
            array (
                'id' => 510,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            219 => 
            array (
                'id' => 511,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            220 => 
            array (
                'id' => 512,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            221 => 
            array (
                'id' => 515,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            222 => 
            array (
                'id' => 516,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            223 => 
            array (
                'id' => 517,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            224 => 
            array (
                'id' => 518,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            225 => 
            array (
                'id' => 519,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            226 => 
            array (
                'id' => 520,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            227 => 
            array (
                'id' => 521,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            228 => 
            array (
                'id' => 522,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            229 => 
            array (
                'id' => 523,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            230 => 
            array (
                'id' => 524,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            231 => 
            array (
                'id' => 533,
                'serviceid' => 250,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            232 => 
            array (
                'id' => 534,
                'serviceid' => 250,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            233 => 
            array (
                'id' => 537,
                'serviceid' => 291,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            234 => 
            array (
                'id' => 538,
                'serviceid' => 291,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            235 => 
            array (
                'id' => 539,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            236 => 
            array (
                'id' => 540,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            237 => 
            array (
                'id' => 541,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            238 => 
            array (
                'id' => 542,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            239 => 
            array (
                'id' => 627,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            240 => 
            array (
                'id' => 628,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            241 => 
            array (
                'id' => 637,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            242 => 
            array (
                'id' => 638,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            243 => 
            array (
                'id' => 646,
                'serviceid' => 273,
                'agentid' => 46,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            244 => 
            array (
                'id' => 648,
                'serviceid' => 259,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            245 => 
            array (
                'id' => 690,
                'serviceid' => 39,
                'agentid' => 46,
                'from_weight' => '0.100',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            246 => 
            array (
                'id' => 702,
                'serviceid' => 276,
                'agentid' => 84,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            247 => 
            array (
                'id' => 763,
                'serviceid' => 114,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            248 => 
            array (
                'id' => 772,
                'serviceid' => 127,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            249 => 
            array (
                'id' => 790,
                'serviceid' => 4,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            250 => 
            array (
                'id' => 809,
                'serviceid' => 300,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            251 => 
            array (
                'id' => 810,
                'serviceid' => 301,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            252 => 
            array (
                'id' => 811,
                'serviceid' => 269,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            253 => 
            array (
                'id' => 812,
                'serviceid' => 302,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            254 => 
            array (
                'id' => 830,
                'serviceid' => 271,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            255 => 
            array (
                'id' => 832,
                'serviceid' => 119,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            256 => 
            array (
                'id' => 835,
                'serviceid' => 303,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            257 => 
            array (
                'id' => 853,
                'serviceid' => 279,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            258 => 
            array (
                'id' => 857,
                'serviceid' => 234,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            259 => 
            array (
                'id' => 862,
                'serviceid' => 263,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            260 => 
            array (
                'id' => 863,
                'serviceid' => 309,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            261 => 
            array (
                'id' => 864,
                'serviceid' => 310,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            262 => 
            array (
                'id' => 865,
                'serviceid' => 69,
                'agentid' => 46,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            263 => 
            array (
                'id' => 866,
                'serviceid' => 69,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            264 => 
            array (
                'id' => 867,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            265 => 
            array (
                'id' => 868,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            266 => 
            array (
                'id' => 869,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            267 => 
            array (
                'id' => 871,
                'serviceid' => 311,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            268 => 
            array (
                'id' => 872,
                'serviceid' => 312,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            269 => 
            array (
                'id' => 874,
                'serviceid' => 313,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            270 => 
            array (
                'id' => 875,
                'serviceid' => 22,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            271 => 
            array (
                'id' => 881,
                'serviceid' => 272,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            272 => 
            array (
                'id' => 882,
                'serviceid' => 280,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            273 => 
            array (
                'id' => 889,
                'serviceid' => 318,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            274 => 
            array (
                'id' => 910,
                'serviceid' => 305,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            275 => 
            array (
                'id' => 987,
                'serviceid' => 246,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            276 => 
            array (
                'id' => 992,
                'serviceid' => 3,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            277 => 
            array (
                'id' => 993,
                'serviceid' => 68,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            278 => 
            array (
                'id' => 995,
                'serviceid' => 16,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            279 => 
            array (
                'id' => 1002,
                'serviceid' => 248,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            280 => 
            array (
                'id' => 1003,
                'serviceid' => 262,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            281 => 
            array (
                'id' => 1006,
                'serviceid' => 329,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            282 => 
            array (
                'id' => 1010,
                'serviceid' => 253,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            283 => 
            array (
                'id' => 1012,
                'serviceid' => 14,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            284 => 
            array (
                'id' => 1026,
                'serviceid' => 239,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            285 => 
            array (
                'id' => 1027,
                'serviceid' => 226,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            286 => 
            array (
                'id' => 1034,
                'serviceid' => 23,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            287 => 
            array (
                'id' => 1035,
                'serviceid' => 23,
                'agentid' => 90,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            288 => 
            array (
                'id' => 1053,
                'serviceid' => 304,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            289 => 
            array (
                'id' => 1054,
                'serviceid' => 304,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '10.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            290 => 
            array (
                'id' => 1055,
                'serviceid' => 38,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            291 => 
            array (
                'id' => 1056,
                'serviceid' => 38,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            292 => 
            array (
                'id' => 1063,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            293 => 
            array (
                'id' => 1064,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            294 => 
            array (
                'id' => 1065,
                'serviceid' => 244,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            295 => 
            array (
                'id' => 1066,
                'serviceid' => 244,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            296 => 
            array (
                'id' => 1067,
                'serviceid' => 245,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            297 => 
            array (
                'id' => 1072,
                'serviceid' => 299,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            298 => 
            array (
                'id' => 1093,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '10.000',
                'to_weight' => '11.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            299 => 
            array (
                'id' => 1094,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '11.500',
                'to_weight' => '12.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            300 => 
            array (
                'id' => 1095,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            301 => 
            array (
                'id' => 1096,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '3.000',
                'to_weight' => '3.500',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            302 => 
            array (
                'id' => 98,
                'serviceid' => 1,
                'agentid' => 4,
                'from_weight' => '1.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            303 => 
            array (
                'id' => 99,
                'serviceid' => 1,
                'agentid' => 3,
                'from_weight' => '2.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            304 => 
            array (
                'id' => 123,
                'serviceid' => 63,
                'agentid' => 4,
                'from_weight' => '0.001',
                'to_weight' => '1.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            305 => 
            array (
                'id' => 228,
                'serviceid' => 229,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            306 => 
            array (
                'id' => 251,
                'serviceid' => 15,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            307 => 
            array (
                'id' => 253,
                'serviceid' => 17,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            308 => 
            array (
                'id' => 258,
                'serviceid' => 13,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            309 => 
            array (
                'id' => 260,
                'serviceid' => 60,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            310 => 
            array (
                'id' => 261,
                'serviceid' => 199,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            311 => 
            array (
                'id' => 262,
                'serviceid' => 200,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            312 => 
            array (
                'id' => 263,
                'serviceid' => 230,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            313 => 
            array (
                'id' => 272,
                'serviceid' => 128,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            314 => 
            array (
                'id' => 276,
                'serviceid' => 233,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            315 => 
            array (
                'id' => 296,
                'serviceid' => 251,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            316 => 
            array (
                'id' => 297,
                'serviceid' => 252,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            317 => 
            array (
                'id' => 299,
                'serviceid' => 254,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            318 => 
            array (
                'id' => 306,
                'serviceid' => 249,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            319 => 
            array (
                'id' => 311,
                'serviceid' => 257,
                'agentid' => 2,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            320 => 
            array (
                'id' => 319,
                'serviceid' => 258,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            321 => 
            array (
                'id' => 322,
                'serviceid' => 24,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            322 => 
            array (
                'id' => 346,
                'serviceid' => 227,
                'agentid' => 1,
                'from_weight' => '0.010',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            323 => 
            array (
                'id' => 347,
                'serviceid' => 227,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            324 => 
            array (
                'id' => 350,
                'serviceid' => 265,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            325 => 
            array (
                'id' => 351,
                'serviceid' => 265,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            326 => 
            array (
                'id' => 352,
                'serviceid' => 266,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            327 => 
            array (
                'id' => 353,
                'serviceid' => 266,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            328 => 
            array (
                'id' => 354,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            329 => 
            array (
                'id' => 355,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            330 => 
            array (
                'id' => 361,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            331 => 
            array (
                'id' => 362,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            332 => 
            array (
                'id' => 363,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            333 => 
            array (
                'id' => 364,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            334 => 
            array (
                'id' => 365,
                'serviceid' => 267,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            335 => 
            array (
                'id' => 366,
                'serviceid' => 267,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            336 => 
            array (
                'id' => 371,
                'serviceid' => 105,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            337 => 
            array (
                'id' => 372,
                'serviceid' => 105,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            338 => 
            array (
                'id' => 373,
                'serviceid' => 268,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            339 => 
            array (
                'id' => 374,
                'serviceid' => 268,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            340 => 
            array (
                'id' => 377,
                'serviceid' => 270,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            341 => 
            array (
                'id' => 378,
                'serviceid' => 270,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            342 => 
            array (
                'id' => 379,
                'serviceid' => 116,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            343 => 
            array (
                'id' => 380,
                'serviceid' => 116,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            344 => 
            array (
                'id' => 394,
                'serviceid' => 274,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            345 => 
            array (
                'id' => 395,
                'serviceid' => 274,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            346 => 
            array (
                'id' => 396,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            347 => 
            array (
                'id' => 397,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            348 => 
            array (
                'id' => 406,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            349 => 
            array (
                'id' => 407,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            350 => 
            array (
                'id' => 408,
                'serviceid' => 228,
                'agentid' => 43,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            351 => 
            array (
                'id' => 409,
                'serviceid' => 228,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            352 => 
            array (
                'id' => 414,
                'serviceid' => 161,
                'agentid' => 3,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            353 => 
            array (
                'id' => 415,
                'serviceid' => 161,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            354 => 
            array (
                'id' => 429,
                'serviceid' => 260,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '70.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            355 => 
            array (
                'id' => 430,
                'serviceid' => 260,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            356 => 
            array (
                'id' => 449,
                'serviceid' => 160,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            357 => 
            array (
                'id' => 450,
                'serviceid' => 160,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            358 => 
            array (
                'id' => 485,
                'serviceid' => 238,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            359 => 
            array (
                'id' => 486,
                'serviceid' => 238,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            360 => 
            array (
                'id' => 487,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            361 => 
            array (
                'id' => 488,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            362 => 
            array (
                'id' => 489,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            363 => 
            array (
                'id' => 490,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            364 => 
            array (
                'id' => 493,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            365 => 
            array (
                'id' => 494,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            366 => 
            array (
                'id' => 503,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '50.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            367 => 
            array (
                'id' => 504,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            368 => 
            array (
                'id' => 509,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            369 => 
            array (
                'id' => 510,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            370 => 
            array (
                'id' => 511,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            371 => 
            array (
                'id' => 512,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            372 => 
            array (
                'id' => 515,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            373 => 
            array (
                'id' => 516,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            374 => 
            array (
                'id' => 517,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            375 => 
            array (
                'id' => 518,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            376 => 
            array (
                'id' => 519,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            377 => 
            array (
                'id' => 520,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            378 => 
            array (
                'id' => 521,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            379 => 
            array (
                'id' => 522,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            380 => 
            array (
                'id' => 523,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            381 => 
            array (
                'id' => 524,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            382 => 
            array (
                'id' => 533,
                'serviceid' => 250,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            383 => 
            array (
                'id' => 534,
                'serviceid' => 250,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            384 => 
            array (
                'id' => 537,
                'serviceid' => 291,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            385 => 
            array (
                'id' => 538,
                'serviceid' => 291,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            386 => 
            array (
                'id' => 539,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            387 => 
            array (
                'id' => 540,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            388 => 
            array (
                'id' => 541,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            389 => 
            array (
                'id' => 542,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            390 => 
            array (
                'id' => 627,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            391 => 
            array (
                'id' => 628,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            392 => 
            array (
                'id' => 637,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            393 => 
            array (
                'id' => 638,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            394 => 
            array (
                'id' => 646,
                'serviceid' => 273,
                'agentid' => 46,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            395 => 
            array (
                'id' => 648,
                'serviceid' => 259,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            396 => 
            array (
                'id' => 690,
                'serviceid' => 39,
                'agentid' => 46,
                'from_weight' => '0.100',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            397 => 
            array (
                'id' => 702,
                'serviceid' => 276,
                'agentid' => 84,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            398 => 
            array (
                'id' => 763,
                'serviceid' => 114,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            399 => 
            array (
                'id' => 772,
                'serviceid' => 127,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            400 => 
            array (
                'id' => 790,
                'serviceid' => 4,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            401 => 
            array (
                'id' => 809,
                'serviceid' => 300,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            402 => 
            array (
                'id' => 810,
                'serviceid' => 301,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            403 => 
            array (
                'id' => 811,
                'serviceid' => 269,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            404 => 
            array (
                'id' => 812,
                'serviceid' => 302,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            405 => 
            array (
                'id' => 830,
                'serviceid' => 271,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            406 => 
            array (
                'id' => 832,
                'serviceid' => 119,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            407 => 
            array (
                'id' => 835,
                'serviceid' => 303,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            408 => 
            array (
                'id' => 853,
                'serviceid' => 279,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            409 => 
            array (
                'id' => 857,
                'serviceid' => 234,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            410 => 
            array (
                'id' => 862,
                'serviceid' => 263,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            411 => 
            array (
                'id' => 863,
                'serviceid' => 309,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            412 => 
            array (
                'id' => 864,
                'serviceid' => 310,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            413 => 
            array (
                'id' => 865,
                'serviceid' => 69,
                'agentid' => 46,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            414 => 
            array (
                'id' => 866,
                'serviceid' => 69,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            415 => 
            array (
                'id' => 867,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            416 => 
            array (
                'id' => 868,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            417 => 
            array (
                'id' => 869,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            418 => 
            array (
                'id' => 871,
                'serviceid' => 311,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            419 => 
            array (
                'id' => 872,
                'serviceid' => 312,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            420 => 
            array (
                'id' => 874,
                'serviceid' => 313,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            421 => 
            array (
                'id' => 875,
                'serviceid' => 22,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            422 => 
            array (
                'id' => 881,
                'serviceid' => 272,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            423 => 
            array (
                'id' => 882,
                'serviceid' => 280,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            424 => 
            array (
                'id' => 889,
                'serviceid' => 318,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            425 => 
            array (
                'id' => 910,
                'serviceid' => 305,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            426 => 
            array (
                'id' => 987,
                'serviceid' => 246,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            427 => 
            array (
                'id' => 992,
                'serviceid' => 3,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            428 => 
            array (
                'id' => 993,
                'serviceid' => 68,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            429 => 
            array (
                'id' => 995,
                'serviceid' => 16,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            430 => 
            array (
                'id' => 1002,
                'serviceid' => 248,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            431 => 
            array (
                'id' => 1003,
                'serviceid' => 262,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            432 => 
            array (
                'id' => 1006,
                'serviceid' => 329,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            433 => 
            array (
                'id' => 1010,
                'serviceid' => 253,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            434 => 
            array (
                'id' => 1012,
                'serviceid' => 14,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            435 => 
            array (
                'id' => 1026,
                'serviceid' => 239,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            436 => 
            array (
                'id' => 1027,
                'serviceid' => 226,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            437 => 
            array (
                'id' => 1034,
                'serviceid' => 23,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            438 => 
            array (
                'id' => 1035,
                'serviceid' => 23,
                'agentid' => 90,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            439 => 
            array (
                'id' => 1053,
                'serviceid' => 304,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            440 => 
            array (
                'id' => 1054,
                'serviceid' => 304,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '10.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            441 => 
            array (
                'id' => 1055,
                'serviceid' => 38,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            442 => 
            array (
                'id' => 1056,
                'serviceid' => 38,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            443 => 
            array (
                'id' => 1063,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            444 => 
            array (
                'id' => 1064,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            445 => 
            array (
                'id' => 1065,
                'serviceid' => 244,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            446 => 
            array (
                'id' => 1066,
                'serviceid' => 244,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            447 => 
            array (
                'id' => 1067,
                'serviceid' => 245,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            448 => 
            array (
                'id' => 1072,
                'serviceid' => 299,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            449 => 
            array (
                'id' => 1093,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '10.000',
                'to_weight' => '11.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            450 => 
            array (
                'id' => 1094,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '11.500',
                'to_weight' => '12.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            451 => 
            array (
                'id' => 1095,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            452 => 
            array (
                'id' => 1096,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '3.000',
                'to_weight' => '3.500',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            453 => 
            array (
                'id' => 98,
                'serviceid' => 1,
                'agentid' => 4,
                'from_weight' => '1.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            454 => 
            array (
                'id' => 99,
                'serviceid' => 1,
                'agentid' => 3,
                'from_weight' => '2.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            455 => 
            array (
                'id' => 123,
                'serviceid' => 63,
                'agentid' => 4,
                'from_weight' => '0.001',
                'to_weight' => '1.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            456 => 
            array (
                'id' => 228,
                'serviceid' => 229,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            457 => 
            array (
                'id' => 251,
                'serviceid' => 15,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            458 => 
            array (
                'id' => 253,
                'serviceid' => 17,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            459 => 
            array (
                'id' => 258,
                'serviceid' => 13,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            460 => 
            array (
                'id' => 260,
                'serviceid' => 60,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            461 => 
            array (
                'id' => 261,
                'serviceid' => 199,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            462 => 
            array (
                'id' => 262,
                'serviceid' => 200,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            463 => 
            array (
                'id' => 263,
                'serviceid' => 230,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            464 => 
            array (
                'id' => 272,
                'serviceid' => 128,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            465 => 
            array (
                'id' => 276,
                'serviceid' => 233,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            466 => 
            array (
                'id' => 296,
                'serviceid' => 251,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            467 => 
            array (
                'id' => 297,
                'serviceid' => 252,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            468 => 
            array (
                'id' => 299,
                'serviceid' => 254,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            469 => 
            array (
                'id' => 306,
                'serviceid' => 249,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            470 => 
            array (
                'id' => 311,
                'serviceid' => 257,
                'agentid' => 2,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            471 => 
            array (
                'id' => 319,
                'serviceid' => 258,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            472 => 
            array (
                'id' => 322,
                'serviceid' => 24,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            473 => 
            array (
                'id' => 346,
                'serviceid' => 227,
                'agentid' => 1,
                'from_weight' => '0.010',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            474 => 
            array (
                'id' => 347,
                'serviceid' => 227,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            475 => 
            array (
                'id' => 350,
                'serviceid' => 265,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            476 => 
            array (
                'id' => 351,
                'serviceid' => 265,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            477 => 
            array (
                'id' => 352,
                'serviceid' => 266,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            478 => 
            array (
                'id' => 353,
                'serviceid' => 266,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            479 => 
            array (
                'id' => 354,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            480 => 
            array (
                'id' => 355,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            481 => 
            array (
                'id' => 361,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            482 => 
            array (
                'id' => 362,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            483 => 
            array (
                'id' => 363,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            484 => 
            array (
                'id' => 364,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            485 => 
            array (
                'id' => 365,
                'serviceid' => 267,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            486 => 
            array (
                'id' => 366,
                'serviceid' => 267,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            487 => 
            array (
                'id' => 371,
                'serviceid' => 105,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            488 => 
            array (
                'id' => 372,
                'serviceid' => 105,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            489 => 
            array (
                'id' => 373,
                'serviceid' => 268,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            490 => 
            array (
                'id' => 374,
                'serviceid' => 268,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            491 => 
            array (
                'id' => 377,
                'serviceid' => 270,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            492 => 
            array (
                'id' => 378,
                'serviceid' => 270,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            493 => 
            array (
                'id' => 379,
                'serviceid' => 116,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            494 => 
            array (
                'id' => 380,
                'serviceid' => 116,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            495 => 
            array (
                'id' => 394,
                'serviceid' => 274,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            496 => 
            array (
                'id' => 395,
                'serviceid' => 274,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            497 => 
            array (
                'id' => 396,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            498 => 
            array (
                'id' => 397,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            499 => 
            array (
                'id' => 406,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
        ));
        \DB::table('carrier_service_default_rules')->insert(array (
            0 => 
            array (
                'id' => 407,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            1 => 
            array (
                'id' => 408,
                'serviceid' => 228,
                'agentid' => 43,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            2 => 
            array (
                'id' => 409,
                'serviceid' => 228,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            3 => 
            array (
                'id' => 414,
                'serviceid' => 161,
                'agentid' => 3,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            4 => 
            array (
                'id' => 415,
                'serviceid' => 161,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            5 => 
            array (
                'id' => 429,
                'serviceid' => 260,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '70.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            6 => 
            array (
                'id' => 430,
                'serviceid' => 260,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            7 => 
            array (
                'id' => 449,
                'serviceid' => 160,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            8 => 
            array (
                'id' => 450,
                'serviceid' => 160,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            9 => 
            array (
                'id' => 485,
                'serviceid' => 238,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            10 => 
            array (
                'id' => 486,
                'serviceid' => 238,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            11 => 
            array (
                'id' => 487,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            12 => 
            array (
                'id' => 488,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            13 => 
            array (
                'id' => 489,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            14 => 
            array (
                'id' => 490,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            15 => 
            array (
                'id' => 493,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            16 => 
            array (
                'id' => 494,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            17 => 
            array (
                'id' => 503,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '50.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            18 => 
            array (
                'id' => 504,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            19 => 
            array (
                'id' => 509,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            20 => 
            array (
                'id' => 510,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            21 => 
            array (
                'id' => 511,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            22 => 
            array (
                'id' => 512,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            23 => 
            array (
                'id' => 515,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            24 => 
            array (
                'id' => 516,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            25 => 
            array (
                'id' => 517,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            26 => 
            array (
                'id' => 518,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            27 => 
            array (
                'id' => 519,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            28 => 
            array (
                'id' => 520,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            29 => 
            array (
                'id' => 521,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            30 => 
            array (
                'id' => 522,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            31 => 
            array (
                'id' => 523,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            32 => 
            array (
                'id' => 524,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            33 => 
            array (
                'id' => 533,
                'serviceid' => 250,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            34 => 
            array (
                'id' => 534,
                'serviceid' => 250,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            35 => 
            array (
                'id' => 537,
                'serviceid' => 291,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            36 => 
            array (
                'id' => 538,
                'serviceid' => 291,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            37 => 
            array (
                'id' => 539,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            38 => 
            array (
                'id' => 540,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            39 => 
            array (
                'id' => 541,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            40 => 
            array (
                'id' => 542,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            41 => 
            array (
                'id' => 627,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            42 => 
            array (
                'id' => 628,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            43 => 
            array (
                'id' => 637,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            44 => 
            array (
                'id' => 638,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            45 => 
            array (
                'id' => 646,
                'serviceid' => 273,
                'agentid' => 46,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            46 => 
            array (
                'id' => 648,
                'serviceid' => 259,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            47 => 
            array (
                'id' => 690,
                'serviceid' => 39,
                'agentid' => 46,
                'from_weight' => '0.100',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            48 => 
            array (
                'id' => 702,
                'serviceid' => 276,
                'agentid' => 84,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            49 => 
            array (
                'id' => 763,
                'serviceid' => 114,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            50 => 
            array (
                'id' => 772,
                'serviceid' => 127,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            51 => 
            array (
                'id' => 790,
                'serviceid' => 4,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            52 => 
            array (
                'id' => 809,
                'serviceid' => 300,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            53 => 
            array (
                'id' => 810,
                'serviceid' => 301,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            54 => 
            array (
                'id' => 811,
                'serviceid' => 269,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            55 => 
            array (
                'id' => 812,
                'serviceid' => 302,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            56 => 
            array (
                'id' => 830,
                'serviceid' => 271,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            57 => 
            array (
                'id' => 832,
                'serviceid' => 119,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            58 => 
            array (
                'id' => 835,
                'serviceid' => 303,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            59 => 
            array (
                'id' => 853,
                'serviceid' => 279,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            60 => 
            array (
                'id' => 857,
                'serviceid' => 234,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            61 => 
            array (
                'id' => 862,
                'serviceid' => 263,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            62 => 
            array (
                'id' => 863,
                'serviceid' => 309,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            63 => 
            array (
                'id' => 864,
                'serviceid' => 310,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            64 => 
            array (
                'id' => 865,
                'serviceid' => 69,
                'agentid' => 46,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            65 => 
            array (
                'id' => 866,
                'serviceid' => 69,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            66 => 
            array (
                'id' => 867,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            67 => 
            array (
                'id' => 868,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            68 => 
            array (
                'id' => 869,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            69 => 
            array (
                'id' => 871,
                'serviceid' => 311,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            70 => 
            array (
                'id' => 872,
                'serviceid' => 312,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            71 => 
            array (
                'id' => 874,
                'serviceid' => 313,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            72 => 
            array (
                'id' => 875,
                'serviceid' => 22,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            73 => 
            array (
                'id' => 881,
                'serviceid' => 272,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            74 => 
            array (
                'id' => 882,
                'serviceid' => 280,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            75 => 
            array (
                'id' => 889,
                'serviceid' => 318,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            76 => 
            array (
                'id' => 910,
                'serviceid' => 305,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            77 => 
            array (
                'id' => 987,
                'serviceid' => 246,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            78 => 
            array (
                'id' => 992,
                'serviceid' => 3,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            79 => 
            array (
                'id' => 993,
                'serviceid' => 68,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            80 => 
            array (
                'id' => 995,
                'serviceid' => 16,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            81 => 
            array (
                'id' => 1002,
                'serviceid' => 248,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            82 => 
            array (
                'id' => 1003,
                'serviceid' => 262,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            83 => 
            array (
                'id' => 1006,
                'serviceid' => 329,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            84 => 
            array (
                'id' => 1010,
                'serviceid' => 253,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            85 => 
            array (
                'id' => 1012,
                'serviceid' => 14,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            86 => 
            array (
                'id' => 1026,
                'serviceid' => 239,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            87 => 
            array (
                'id' => 1027,
                'serviceid' => 226,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            88 => 
            array (
                'id' => 1034,
                'serviceid' => 23,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            89 => 
            array (
                'id' => 1035,
                'serviceid' => 23,
                'agentid' => 90,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            90 => 
            array (
                'id' => 1053,
                'serviceid' => 304,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            91 => 
            array (
                'id' => 1054,
                'serviceid' => 304,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '10.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            92 => 
            array (
                'id' => 1055,
                'serviceid' => 38,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            93 => 
            array (
                'id' => 1056,
                'serviceid' => 38,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            94 => 
            array (
                'id' => 1063,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            95 => 
            array (
                'id' => 1064,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            96 => 
            array (
                'id' => 1065,
                'serviceid' => 244,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            97 => 
            array (
                'id' => 1066,
                'serviceid' => 244,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            98 => 
            array (
                'id' => 1067,
                'serviceid' => 245,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            99 => 
            array (
                'id' => 1072,
                'serviceid' => 299,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            100 => 
            array (
                'id' => 1093,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '10.000',
                'to_weight' => '11.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            101 => 
            array (
                'id' => 1094,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '11.500',
                'to_weight' => '12.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            102 => 
            array (
                'id' => 1095,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            103 => 
            array (
                'id' => 1096,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '3.000',
                'to_weight' => '3.500',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            104 => 
            array (
                'id' => 98,
                'serviceid' => 1,
                'agentid' => 4,
                'from_weight' => '1.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            105 => 
            array (
                'id' => 99,
                'serviceid' => 1,
                'agentid' => 3,
                'from_weight' => '2.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            106 => 
            array (
                'id' => 123,
                'serviceid' => 63,
                'agentid' => 4,
                'from_weight' => '0.001',
                'to_weight' => '1.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            107 => 
            array (
                'id' => 228,
                'serviceid' => 229,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            108 => 
            array (
                'id' => 251,
                'serviceid' => 15,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            109 => 
            array (
                'id' => 253,
                'serviceid' => 17,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            110 => 
            array (
                'id' => 258,
                'serviceid' => 13,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            111 => 
            array (
                'id' => 260,
                'serviceid' => 60,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            112 => 
            array (
                'id' => 261,
                'serviceid' => 199,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            113 => 
            array (
                'id' => 262,
                'serviceid' => 200,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            114 => 
            array (
                'id' => 263,
                'serviceid' => 230,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            115 => 
            array (
                'id' => 272,
                'serviceid' => 128,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            116 => 
            array (
                'id' => 276,
                'serviceid' => 233,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            117 => 
            array (
                'id' => 296,
                'serviceid' => 251,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            118 => 
            array (
                'id' => 297,
                'serviceid' => 252,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            119 => 
            array (
                'id' => 299,
                'serviceid' => 254,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            120 => 
            array (
                'id' => 306,
                'serviceid' => 249,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            121 => 
            array (
                'id' => 311,
                'serviceid' => 257,
                'agentid' => 2,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            122 => 
            array (
                'id' => 319,
                'serviceid' => 258,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            123 => 
            array (
                'id' => 322,
                'serviceid' => 24,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            124 => 
            array (
                'id' => 346,
                'serviceid' => 227,
                'agentid' => 1,
                'from_weight' => '0.010',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            125 => 
            array (
                'id' => 347,
                'serviceid' => 227,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            126 => 
            array (
                'id' => 350,
                'serviceid' => 265,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            127 => 
            array (
                'id' => 351,
                'serviceid' => 265,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            128 => 
            array (
                'id' => 352,
                'serviceid' => 266,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            129 => 
            array (
                'id' => 353,
                'serviceid' => 266,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            130 => 
            array (
                'id' => 354,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            131 => 
            array (
                'id' => 355,
                'serviceid' => 12,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            132 => 
            array (
                'id' => 361,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            133 => 
            array (
                'id' => 362,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            134 => 
            array (
                'id' => 363,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            135 => 
            array (
                'id' => 364,
                'serviceid' => 58,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            136 => 
            array (
                'id' => 365,
                'serviceid' => 267,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            137 => 
            array (
                'id' => 366,
                'serviceid' => 267,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            138 => 
            array (
                'id' => 371,
                'serviceid' => 105,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            139 => 
            array (
                'id' => 372,
                'serviceid' => 105,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            140 => 
            array (
                'id' => 373,
                'serviceid' => 268,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            141 => 
            array (
                'id' => 374,
                'serviceid' => 268,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            142 => 
            array (
                'id' => 377,
                'serviceid' => 270,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            143 => 
            array (
                'id' => 378,
                'serviceid' => 270,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            144 => 
            array (
                'id' => 379,
                'serviceid' => 116,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            145 => 
            array (
                'id' => 380,
                'serviceid' => 116,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            146 => 
            array (
                'id' => 394,
                'serviceid' => 274,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            147 => 
            array (
                'id' => 395,
                'serviceid' => 274,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            148 => 
            array (
                'id' => 396,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            149 => 
            array (
                'id' => 397,
                'serviceid' => 275,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            150 => 
            array (
                'id' => 406,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            151 => 
            array (
                'id' => 407,
                'serviceid' => 281,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            152 => 
            array (
                'id' => 408,
                'serviceid' => 228,
                'agentid' => 43,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            153 => 
            array (
                'id' => 409,
                'serviceid' => 228,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            154 => 
            array (
                'id' => 414,
                'serviceid' => 161,
                'agentid' => 3,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            155 => 
            array (
                'id' => 415,
                'serviceid' => 161,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            156 => 
            array (
                'id' => 429,
                'serviceid' => 260,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '70.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            157 => 
            array (
                'id' => 430,
                'serviceid' => 260,
                'agentid' => 0,
                'from_weight' => '0.000',
                'to_weight' => '0.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            158 => 
            array (
                'id' => 449,
                'serviceid' => 160,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            159 => 
            array (
                'id' => 450,
                'serviceid' => 160,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            160 => 
            array (
                'id' => 485,
                'serviceid' => 238,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            161 => 
            array (
                'id' => 486,
                'serviceid' => 238,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            162 => 
            array (
                'id' => 487,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            163 => 
            array (
                'id' => 488,
                'serviceid' => 247,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            164 => 
            array (
                'id' => 489,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            165 => 
            array (
                'id' => 490,
                'serviceid' => 285,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            166 => 
            array (
                'id' => 493,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            167 => 
            array (
                'id' => 494,
                'serviceid' => 232,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            168 => 
            array (
                'id' => 503,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '50.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            169 => 
            array (
                'id' => 504,
                'serviceid' => 283,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            170 => 
            array (
                'id' => 509,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            171 => 
            array (
                'id' => 510,
                'serviceid' => 277,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            172 => 
            array (
                'id' => 511,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            173 => 
            array (
                'id' => 512,
                'serviceid' => 286,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            174 => 
            array (
                'id' => 515,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            175 => 
            array (
                'id' => 516,
                'serviceid' => 287,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            176 => 
            array (
                'id' => 517,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            177 => 
            array (
                'id' => 518,
                'serviceid' => 288,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            178 => 
            array (
                'id' => 519,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            179 => 
            array (
                'id' => 520,
                'serviceid' => 289,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            180 => 
            array (
                'id' => 521,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            181 => 
            array (
                'id' => 522,
                'serviceid' => 290,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            182 => 
            array (
                'id' => 523,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            183 => 
            array (
                'id' => 524,
                'serviceid' => 255,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            184 => 
            array (
                'id' => 533,
                'serviceid' => 250,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            185 => 
            array (
                'id' => 534,
                'serviceid' => 250,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            186 => 
            array (
                'id' => 537,
                'serviceid' => 291,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            187 => 
            array (
                'id' => 538,
                'serviceid' => 291,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            188 => 
            array (
                'id' => 539,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            189 => 
            array (
                'id' => 540,
                'serviceid' => 292,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            190 => 
            array (
                'id' => 541,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            191 => 
            array (
                'id' => 542,
                'serviceid' => 293,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            192 => 
            array (
                'id' => 627,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            193 => 
            array (
                'id' => 628,
                'serviceid' => 25,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            194 => 
            array (
                'id' => 637,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            195 => 
            array (
                'id' => 638,
                'serviceid' => 296,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            196 => 
            array (
                'id' => 646,
                'serviceid' => 273,
                'agentid' => 46,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            197 => 
            array (
                'id' => 648,
                'serviceid' => 259,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            198 => 
            array (
                'id' => 690,
                'serviceid' => 39,
                'agentid' => 46,
                'from_weight' => '0.100',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            199 => 
            array (
                'id' => 702,
                'serviceid' => 276,
                'agentid' => 84,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            200 => 
            array (
                'id' => 763,
                'serviceid' => 114,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            201 => 
            array (
                'id' => 772,
                'serviceid' => 127,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            202 => 
            array (
                'id' => 790,
                'serviceid' => 4,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            203 => 
            array (
                'id' => 809,
                'serviceid' => 300,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            204 => 
            array (
                'id' => 810,
                'serviceid' => 301,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            205 => 
            array (
                'id' => 811,
                'serviceid' => 269,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            206 => 
            array (
                'id' => 812,
                'serviceid' => 302,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            207 => 
            array (
                'id' => 830,
                'serviceid' => 271,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            208 => 
            array (
                'id' => 832,
                'serviceid' => 119,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            209 => 
            array (
                'id' => 835,
                'serviceid' => 303,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            210 => 
            array (
                'id' => 853,
                'serviceid' => 279,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            211 => 
            array (
                'id' => 857,
                'serviceid' => 234,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '15.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            212 => 
            array (
                'id' => 862,
                'serviceid' => 263,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            213 => 
            array (
                'id' => 863,
                'serviceid' => 309,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            214 => 
            array (
                'id' => 864,
                'serviceid' => 310,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            215 => 
            array (
                'id' => 865,
                'serviceid' => 69,
                'agentid' => 46,
                'from_weight' => '0.001',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            216 => 
            array (
                'id' => 866,
                'serviceid' => 69,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            217 => 
            array (
                'id' => 867,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            218 => 
            array (
                'id' => 868,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            219 => 
            array (
                'id' => 869,
                'serviceid' => 223,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            220 => 
            array (
                'id' => 871,
                'serviceid' => 311,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            221 => 
            array (
                'id' => 872,
                'serviceid' => 312,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            222 => 
            array (
                'id' => 874,
                'serviceid' => 313,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            223 => 
            array (
                'id' => 875,
                'serviceid' => 22,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            224 => 
            array (
                'id' => 881,
                'serviceid' => 272,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '20.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            225 => 
            array (
                'id' => 882,
                'serviceid' => 280,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            226 => 
            array (
                'id' => 889,
                'serviceid' => 318,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            227 => 
            array (
                'id' => 910,
                'serviceid' => 305,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            228 => 
            array (
                'id' => 987,
                'serviceid' => 246,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            229 => 
            array (
                'id' => 992,
                'serviceid' => 3,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            230 => 
            array (
                'id' => 993,
                'serviceid' => 68,
                'agentid' => 42,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            231 => 
            array (
                'id' => 995,
                'serviceid' => 16,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            232 => 
            array (
                'id' => 1002,
                'serviceid' => 248,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            233 => 
            array (
                'id' => 1003,
                'serviceid' => 262,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            234 => 
            array (
                'id' => 1006,
                'serviceid' => 329,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            235 => 
            array (
                'id' => 1010,
                'serviceid' => 253,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            236 => 
            array (
                'id' => 1012,
                'serviceid' => 14,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            237 => 
            array (
                'id' => 1026,
                'serviceid' => 239,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            238 => 
            array (
                'id' => 1027,
                'serviceid' => 226,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            239 => 
            array (
                'id' => 1034,
                'serviceid' => 23,
                'agentid' => 1,
                'from_weight' => '0.001',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            240 => 
            array (
                'id' => 1035,
                'serviceid' => 23,
                'agentid' => 90,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            241 => 
            array (
                'id' => 1053,
                'serviceid' => 304,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '30.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            242 => 
            array (
                'id' => 1054,
                'serviceid' => 304,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '10.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            243 => 
            array (
                'id' => 1055,
                'serviceid' => 38,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            244 => 
            array (
                'id' => 1056,
                'serviceid' => 38,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            245 => 
            array (
                'id' => 1063,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            246 => 
            array (
                'id' => 1064,
                'serviceid' => 235,
                'agentid' => 71,
                'from_weight' => '0.000',
                'to_weight' => '25.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            247 => 
            array (
                'id' => 1065,
                'serviceid' => 244,
                'agentid' => 2,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            248 => 
            array (
                'id' => 1066,
                'serviceid' => 244,
                'agentid' => 65,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'dispatch',
            ),
            249 => 
            array (
                'id' => 1067,
                'serviceid' => 245,
                'agentid' => 3,
                'from_weight' => '0.000',
                'to_weight' => '2.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            250 => 
            array (
                'id' => 1072,
                'serviceid' => 299,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '1000.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            251 => 
            array (
                'id' => 1093,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '10.000',
                'to_weight' => '11.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            252 => 
            array (
                'id' => 1094,
                'serviceid' => 2,
                'agentid' => 61,
                'from_weight' => '11.500',
                'to_weight' => '12.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            253 => 
            array (
                'id' => 1095,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '0.000',
                'to_weight' => '3.000',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
            254 => 
            array (
                'id' => 1096,
                'serviceid' => 2,
                'agentid' => 1,
                'from_weight' => '3.000',
                'to_weight' => '3.500',
                'is_default' => 1,
                'agent_type' => 'outbound',
            ),
        ));
        
        
    }
}