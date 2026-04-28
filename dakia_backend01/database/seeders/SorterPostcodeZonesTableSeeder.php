<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SorterPostcodeZonesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sorter_postcode_zones')->delete();
        
        \DB::table('sorter_postcode_zones')->insert(array (
            0 => 
            array (
                'id' => 1,
                'area' => 'Aberdeen',
                'postcode' => 'AB',
                'zone' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'area' => 'St Albans',
                'postcode' => 'AL',
                'zone' => '9',
            ),
            2 => 
            array (
                'id' => 3,
                'area' => 'Birmingham',
                'postcode' => 'B',
                'zone' => '6',
            ),
            3 => 
            array (
                'id' => 4,
                'area' => 'Bath',
                'postcode' => 'BA',
                'zone' => '8',
            ),
            4 => 
            array (
                'id' => 5,
                'area' => 'Blackburn',
                'postcode' => 'BB',
                'zone' => '4',
            ),
            5 => 
            array (
                'id' => 6,
                'area' => 'Bradford',
                'postcode' => 'BD',
                'zone' => '4',
            ),
            6 => 
            array (
                'id' => 7,
                'area' => 'Bournemouth',
                'postcode' => 'BH',
                'zone' => '8',
            ),
            7 => 
            array (
                'id' => 8,
                'area' => 'Bolton',
                'postcode' => 'BL',
                'zone' => '4',
            ),
            8 => 
            array (
                'id' => 9,
                'area' => 'Brighton',
                'postcode' => 'BN',
                'zone' => '9',
            ),
            9 => 
            array (
                'id' => 10,
                'area' => 'Bromley',
                'postcode' => 'BR',
                'zone' => '10',
            ),
            10 => 
            array (
                'id' => 11,
                'area' => 'Bristol',
                'postcode' => 'BS',
                'zone' => '8',
            ),
            11 => 
            array (
                'id' => 12,
                'area' => 'Northern Ireland',
                'postcode' => 'BT',
                'zone' => '2',
            ),
            12 => 
            array (
                'id' => 13,
                'area' => 'Carlisle',
                'postcode' => 'CA',
                'zone' => '4',
            ),
            13 => 
            array (
                'id' => 14,
                'area' => 'Cambridge',
                'postcode' => 'CB',
                'zone' => '5',
            ),
            14 => 
            array (
                'id' => 15,
                'area' => 'Cardiff',
                'postcode' => 'CF',
                'zone' => '7',
            ),
            15 => 
            array (
                'id' => 16,
                'area' => 'Chester',
                'postcode' => 'CH',
                'zone' => '4',
            ),
            16 => 
            array (
                'id' => 17,
                'area' => 'Chelmsford',
                'postcode' => 'CM',
                'zone' => '9',
            ),
            17 => 
            array (
                'id' => 18,
                'area' => 'Colchester',
                'postcode' => 'CO',
                'zone' => '5',
            ),
            18 => 
            array (
                'id' => 19,
                'area' => 'Croydon',
                'postcode' => 'CR',
                'zone' => '10',
            ),
            19 => 
            array (
                'id' => 20,
                'area' => 'Canterbury',
                'postcode' => 'CT',
                'zone' => '9',
            ),
            20 => 
            array (
                'id' => 21,
                'area' => 'Coventry',
                'postcode' => 'CV',
                'zone' => '6',
            ),
            21 => 
            array (
                'id' => 22,
                'area' => 'Crewe',
                'postcode' => 'CW',
                'zone' => '4',
            ),
            22 => 
            array (
                'id' => 23,
                'area' => 'Dartford',
                'postcode' => 'DA',
                'zone' => '10',
            ),
            23 => 
            array (
                'id' => 24,
                'area' => 'Dundee',
                'postcode' => 'DD',
                'zone' => '1',
            ),
            24 => 
            array (
                'id' => 25,
                'area' => 'Derby',
                'postcode' => 'DE',
                'zone' => '5',
            ),
            25 => 
            array (
                'id' => 26,
                'area' => 'Dumfries',
                'postcode' => 'DG',
                'zone' => '1',
            ),
            26 => 
            array (
                'id' => 27,
                'area' => 'Durham',
                'postcode' => 'DH',
                'zone' => '3',
            ),
            27 => 
            array (
                'id' => 28,
                'area' => 'Darlington',
                'postcode' => 'DL',
                'zone' => '3',
            ),
            28 => 
            array (
                'id' => 29,
                'area' => 'Doncaster',
                'postcode' => 'DN',
                'zone' => '5',
            ),
            29 => 
            array (
                'id' => 30,
                'area' => 'Dorchester',
                'postcode' => 'DT',
                'zone' => '8',
            ),
            30 => 
            array (
                'id' => 31,
                'area' => 'Dudley',
                'postcode' => 'DY',
                'zone' => '6',
            ),
            31 => 
            array (
                'id' => 32,
                'area' => 'East London',
                'postcode' => 'E',
                'zone' => '10',
            ),
            32 => 
            array (
                'id' => 33,
                'area' => 'Central London',
                'postcode' => 'EC',
                'zone' => '10',
            ),
            33 => 
            array (
                'id' => 34,
                'area' => 'Edinburgh',
                'postcode' => 'EH',
                'zone' => '1',
            ),
            34 => 
            array (
                'id' => 35,
                'area' => 'Enfield',
                'postcode' => 'EN',
                'zone' => '10',
            ),
            35 => 
            array (
                'id' => 36,
                'area' => 'Exeter',
                'postcode' => 'EX',
                'zone' => '8',
            ),
            36 => 
            array (
                'id' => 37,
                'area' => 'Falkirk and Stirling',
                'postcode' => 'FK',
                'zone' => '1',
            ),
            37 => 
            array (
                'id' => 38,
                'area' => 'Blackpool',
                'postcode' => 'FY',
                'zone' => '4',
            ),
            38 => 
            array (
                'id' => 39,
                'area' => 'Glasgow',
                'postcode' => 'G',
                'zone' => '1',
            ),
            39 => 
            array (
                'id' => 40,
                'area' => 'Gloucester',
                'postcode' => 'GL',
                'zone' => '8',
            ),
            40 => 
            array (
                'id' => 41,
                'area' => 'Guildford',
                'postcode' => 'GU',
                'zone' => '9',
            ),
            41 => 
            array (
                'id' => 42,
                'area' => 'Harrow',
                'postcode' => 'HA',
                'zone' => '10',
            ),
            42 => 
            array (
                'id' => 43,
                'area' => 'Huddersfield',
                'postcode' => 'HD',
                'zone' => '4',
            ),
            43 => 
            array (
                'id' => 44,
                'area' => 'Harrogate',
                'postcode' => 'HG',
                'zone' => '3',
            ),
            44 => 
            array (
                'id' => 45,
                'area' => 'Hemel Hempstead',
                'postcode' => 'HP',
                'zone' => '9',
            ),
            45 => 
            array (
                'id' => 46,
                'area' => 'Hereford',
                'postcode' => 'HR',
                'zone' => '6',
            ),
            46 => 
            array (
                'id' => 47,
                'area' => 'Hull',
                'postcode' => 'HU',
                'zone' => '3',
            ),
            47 => 
            array (
                'id' => 48,
                'area' => 'Halifax',
                'postcode' => 'HX',
                'zone' => '4',
            ),
            48 => 
            array (
                'id' => 49,
                'area' => 'Ilford',
                'postcode' => 'IG',
                'zone' => '10',
            ),
            49 => 
            array (
                'id' => 50,
                'area' => 'Ipswich',
                'postcode' => 'IP',
                'zone' => '5',
            ),
            50 => 
            array (
                'id' => 51,
                'area' => 'Inverness',
                'postcode' => 'IV',
                'zone' => '1',
            ),
            51 => 
            array (
                'id' => 52,
                'area' => 'Kilmarnock',
                'postcode' => 'KA',
                'zone' => '1',
            ),
            52 => 
            array (
                'id' => 53,
                'area' => 'Kingston upon Thames',
                'postcode' => 'KT',
                'zone' => '10',
            ),
            53 => 
            array (
                'id' => 54,
                'area' => 'Kirkwall',
                'postcode' => 'KW',
                'zone' => '1',
            ),
            54 => 
            array (
                'id' => 55,
                'area' => 'Kirkcaldy',
                'postcode' => 'KY',
                'zone' => '1',
            ),
            55 => 
            array (
                'id' => 56,
                'area' => 'Liverpool',
                'postcode' => 'L',
                'zone' => '4',
            ),
            56 => 
            array (
                'id' => 57,
                'area' => 'Lancaster',
                'postcode' => 'LA',
                'zone' => '4',
            ),
            57 => 
            array (
                'id' => 58,
                'area' => 'Llandrindod Wells',
                'postcode' => 'LD',
                'zone' => '7',
            ),
            58 => 
            array (
                'id' => 59,
                'area' => 'Leicester',
                'postcode' => 'LE',
                'zone' => '5',
            ),
            59 => 
            array (
                'id' => 60,
                'area' => 'Llandudno',
                'postcode' => 'LL',
                'zone' => '7',
            ),
            60 => 
            array (
                'id' => 61,
                'area' => 'Lincoln',
                'postcode' => 'LN',
                'zone' => '5',
            ),
            61 => 
            array (
                'id' => 62,
                'area' => 'Leeds',
                'postcode' => 'LS',
                'zone' => '3',
            ),
            62 => 
            array (
                'id' => 63,
                'area' => 'Luton',
                'postcode' => 'LU',
                'zone' => '9',
            ),
            63 => 
            array (
                'id' => 64,
                'area' => 'Manchester',
                'postcode' => 'M',
                'zone' => '4',
            ),
            64 => 
            array (
                'id' => 65,
                'area' => 'Rochester',
                'postcode' => 'ME',
                'zone' => '9',
            ),
            65 => 
            array (
                'id' => 66,
                'area' => 'Milton Keynes',
                'postcode' => 'MK',
                'zone' => '9',
            ),
            66 => 
            array (
                'id' => 67,
                'area' => 'Motherwell',
                'postcode' => 'ML',
                'zone' => '1',
            ),
            67 => 
            array (
                'id' => 68,
                'area' => 'North London',
                'postcode' => 'N',
                'zone' => '10',
            ),
            68 => 
            array (
                'id' => 69,
                'area' => 'Newcastle upon Tyne',
                'postcode' => 'NE',
                'zone' => '3',
            ),
            69 => 
            array (
                'id' => 70,
                'area' => 'Nottingham',
                'postcode' => 'NG',
                'zone' => '5',
            ),
            70 => 
            array (
                'id' => 71,
                'area' => 'Northampton',
                'postcode' => 'NN',
                'zone' => '6',
            ),
            71 => 
            array (
                'id' => 72,
                'area' => 'Newport',
                'postcode' => 'NP',
                'zone' => '7',
            ),
            72 => 
            array (
                'id' => 73,
                'area' => 'Norwich',
                'postcode' => 'NR',
                'zone' => '5',
            ),
            73 => 
            array (
                'id' => 74,
                'area' => 'North West London',
                'postcode' => 'NW',
                'zone' => '10',
            ),
            74 => 
            array (
                'id' => 75,
                'area' => 'Oldham',
                'postcode' => 'OL',
                'zone' => '4',
            ),
            75 => 
            array (
                'id' => 76,
                'area' => 'Oxford',
                'postcode' => 'OX',
                'zone' => '9',
            ),
            76 => 
            array (
                'id' => 77,
                'area' => 'Paisley',
                'postcode' => 'PA',
                'zone' => '1',
            ),
            77 => 
            array (
                'id' => 78,
                'area' => 'Peterborough',
                'postcode' => 'PE',
                'zone' => '5',
            ),
            78 => 
            array (
                'id' => 79,
                'area' => 'Perth',
                'postcode' => 'PH',
                'zone' => '1',
            ),
            79 => 
            array (
                'id' => 80,
                'area' => 'Plymouth',
                'postcode' => 'PL',
                'zone' => '8',
            ),
            80 => 
            array (
                'id' => 81,
                'area' => 'Portsmouth',
                'postcode' => 'PO',
                'zone' => '9',
            ),
            81 => 
            array (
                'id' => 82,
                'area' => 'Preston',
                'postcode' => 'PR',
                'zone' => '4',
            ),
            82 => 
            array (
                'id' => 83,
                'area' => 'Reading',
                'postcode' => 'RG',
                'zone' => '9',
            ),
            83 => 
            array (
                'id' => 84,
                'area' => 'Redhill',
                'postcode' => 'RH',
                'zone' => '9',
            ),
            84 => 
            array (
                'id' => 85,
                'area' => 'Romford',
                'postcode' => 'RM',
                'zone' => '10',
            ),
            85 => 
            array (
                'id' => 86,
                'area' => 'Sheffield',
                'postcode' => 'S',
                'zone' => '5',
            ),
            86 => 
            array (
                'id' => 87,
                'area' => 'Swansea',
                'postcode' => 'SA',
                'zone' => '7',
            ),
            87 => 
            array (
                'id' => 88,
                'area' => 'South East London',
                'postcode' => 'SE',
                'zone' => '10',
            ),
            88 => 
            array (
                'id' => 89,
                'area' => 'Stevenage',
                'postcode' => 'SG',
                'zone' => '9',
            ),
            89 => 
            array (
                'id' => 90,
                'area' => 'Stockport',
                'postcode' => 'SK',
                'zone' => '4',
            ),
            90 => 
            array (
                'id' => 91,
                'area' => 'Slough',
                'postcode' => 'SL',
                'zone' => '9',
            ),
            91 => 
            array (
                'id' => 92,
                'area' => 'Sutton',
                'postcode' => 'SM',
                'zone' => '10',
            ),
            92 => 
            array (
                'id' => 93,
                'area' => 'Swindon',
                'postcode' => 'SN',
                'zone' => '8',
            ),
            93 => 
            array (
                'id' => 94,
                'area' => 'Southampton',
                'postcode' => 'SO',
                'zone' => '9',
            ),
            94 => 
            array (
                'id' => 95,
                'area' => 'Salisbury',
                'postcode' => 'SP',
                'zone' => '8',
            ),
            95 => 
            array (
                'id' => 96,
                'area' => 'Sunderland',
                'postcode' => 'SR',
                'zone' => '3',
            ),
            96 => 
            array (
                'id' => 97,
                'area' => 'Southend-on-Sea',
                'postcode' => 'SS',
                'zone' => '5',
            ),
            97 => 
            array (
                'id' => 98,
                'area' => 'Stoke-on-Trent',
                'postcode' => 'ST',
                'zone' => '6',
            ),
            98 => 
            array (
                'id' => 99,
                'area' => 'South West London',
                'postcode' => 'SW',
                'zone' => '10',
            ),
            99 => 
            array (
                'id' => 100,
                'area' => 'Shrewsbury',
                'postcode' => 'SY',
                'zone' => '7',
            ),
            100 => 
            array (
                'id' => 101,
                'area' => 'Taunton',
                'postcode' => 'TA',
                'zone' => '8',
            ),
            101 => 
            array (
                'id' => 102,
                'area' => 'Galashiels',
                'postcode' => 'TD',
                'zone' => '1',
            ),
            102 => 
            array (
                'id' => 103,
                'area' => 'Telford',
                'postcode' => 'TF',
                'zone' => '6',
            ),
            103 => 
            array (
                'id' => 104,
                'area' => 'Tonbridge',
                'postcode' => 'TN',
                'zone' => '9',
            ),
            104 => 
            array (
                'id' => 105,
                'area' => 'Torquay',
                'postcode' => 'TQ',
                'zone' => '8',
            ),
            105 => 
            array (
                'id' => 106,
                'area' => 'Truro',
                'postcode' => 'TR',
                'zone' => '8',
            ),
            106 => 
            array (
                'id' => 107,
                'area' => 'Cleveland',
                'postcode' => 'TS',
                'zone' => '3',
            ),
            107 => 
            array (
                'id' => 108,
                'area' => 'Twickenham',
                'postcode' => 'TW',
                'zone' => '10',
            ),
            108 => 
            array (
                'id' => 109,
                'area' => 'Southall',
                'postcode' => 'UB',
                'zone' => '10',
            ),
            109 => 
            array (
                'id' => 110,
                'area' => 'West London',
                'postcode' => 'W',
                'zone' => '10',
            ),
            110 => 
            array (
                'id' => 111,
                'area' => 'Warrington',
                'postcode' => 'WA',
                'zone' => '4',
            ),
            111 => 
            array (
                'id' => 112,
                'area' => 'Central London',
                'postcode' => 'WC',
                'zone' => '10',
            ),
            112 => 
            array (
                'id' => 113,
                'area' => 'Watford',
                'postcode' => 'WD',
                'zone' => '10',
            ),
            113 => 
            array (
                'id' => 114,
                'area' => 'Wakefield',
                'postcode' => 'WF',
                'zone' => '3',
            ),
            114 => 
            array (
                'id' => 115,
                'area' => 'Wigan',
                'postcode' => 'WN',
                'zone' => '4',
            ),
            115 => 
            array (
                'id' => 116,
                'area' => 'Worcester',
                'postcode' => 'WR',
                'zone' => '6',
            ),
            116 => 
            array (
                'id' => 117,
                'area' => 'Walsall',
                'postcode' => 'WS',
                'zone' => '6',
            ),
            117 => 
            array (
                'id' => 118,
                'area' => 'Wolverhampton',
                'postcode' => 'WV',
                'zone' => '6',
            ),
            118 => 
            array (
                'id' => 119,
                'area' => 'York',
                'postcode' => 'YO',
                'zone' => '3',
            ),
            119 => 
            array (
                'id' => 1,
                'area' => 'Aberdeen',
                'postcode' => 'AB',
                'zone' => '1',
            ),
            120 => 
            array (
                'id' => 2,
                'area' => 'St Albans',
                'postcode' => 'AL',
                'zone' => '9',
            ),
            121 => 
            array (
                'id' => 3,
                'area' => 'Birmingham',
                'postcode' => 'B',
                'zone' => '6',
            ),
            122 => 
            array (
                'id' => 4,
                'area' => 'Bath',
                'postcode' => 'BA',
                'zone' => '8',
            ),
            123 => 
            array (
                'id' => 5,
                'area' => 'Blackburn',
                'postcode' => 'BB',
                'zone' => '4',
            ),
            124 => 
            array (
                'id' => 6,
                'area' => 'Bradford',
                'postcode' => 'BD',
                'zone' => '4',
            ),
            125 => 
            array (
                'id' => 7,
                'area' => 'Bournemouth',
                'postcode' => 'BH',
                'zone' => '8',
            ),
            126 => 
            array (
                'id' => 8,
                'area' => 'Bolton',
                'postcode' => 'BL',
                'zone' => '4',
            ),
            127 => 
            array (
                'id' => 9,
                'area' => 'Brighton',
                'postcode' => 'BN',
                'zone' => '9',
            ),
            128 => 
            array (
                'id' => 10,
                'area' => 'Bromley',
                'postcode' => 'BR',
                'zone' => '10',
            ),
            129 => 
            array (
                'id' => 11,
                'area' => 'Bristol',
                'postcode' => 'BS',
                'zone' => '8',
            ),
            130 => 
            array (
                'id' => 12,
                'area' => 'Northern Ireland',
                'postcode' => 'BT',
                'zone' => '2',
            ),
            131 => 
            array (
                'id' => 13,
                'area' => 'Carlisle',
                'postcode' => 'CA',
                'zone' => '4',
            ),
            132 => 
            array (
                'id' => 14,
                'area' => 'Cambridge',
                'postcode' => 'CB',
                'zone' => '5',
            ),
            133 => 
            array (
                'id' => 15,
                'area' => 'Cardiff',
                'postcode' => 'CF',
                'zone' => '7',
            ),
            134 => 
            array (
                'id' => 16,
                'area' => 'Chester',
                'postcode' => 'CH',
                'zone' => '4',
            ),
            135 => 
            array (
                'id' => 17,
                'area' => 'Chelmsford',
                'postcode' => 'CM',
                'zone' => '9',
            ),
            136 => 
            array (
                'id' => 18,
                'area' => 'Colchester',
                'postcode' => 'CO',
                'zone' => '5',
            ),
            137 => 
            array (
                'id' => 19,
                'area' => 'Croydon',
                'postcode' => 'CR',
                'zone' => '10',
            ),
            138 => 
            array (
                'id' => 20,
                'area' => 'Canterbury',
                'postcode' => 'CT',
                'zone' => '9',
            ),
            139 => 
            array (
                'id' => 21,
                'area' => 'Coventry',
                'postcode' => 'CV',
                'zone' => '6',
            ),
            140 => 
            array (
                'id' => 22,
                'area' => 'Crewe',
                'postcode' => 'CW',
                'zone' => '4',
            ),
            141 => 
            array (
                'id' => 23,
                'area' => 'Dartford',
                'postcode' => 'DA',
                'zone' => '10',
            ),
            142 => 
            array (
                'id' => 24,
                'area' => 'Dundee',
                'postcode' => 'DD',
                'zone' => '1',
            ),
            143 => 
            array (
                'id' => 25,
                'area' => 'Derby',
                'postcode' => 'DE',
                'zone' => '5',
            ),
            144 => 
            array (
                'id' => 26,
                'area' => 'Dumfries',
                'postcode' => 'DG',
                'zone' => '1',
            ),
            145 => 
            array (
                'id' => 27,
                'area' => 'Durham',
                'postcode' => 'DH',
                'zone' => '3',
            ),
            146 => 
            array (
                'id' => 28,
                'area' => 'Darlington',
                'postcode' => 'DL',
                'zone' => '3',
            ),
            147 => 
            array (
                'id' => 29,
                'area' => 'Doncaster',
                'postcode' => 'DN',
                'zone' => '5',
            ),
            148 => 
            array (
                'id' => 30,
                'area' => 'Dorchester',
                'postcode' => 'DT',
                'zone' => '8',
            ),
            149 => 
            array (
                'id' => 31,
                'area' => 'Dudley',
                'postcode' => 'DY',
                'zone' => '6',
            ),
            150 => 
            array (
                'id' => 32,
                'area' => 'East London',
                'postcode' => 'E',
                'zone' => '10',
            ),
            151 => 
            array (
                'id' => 33,
                'area' => 'Central London',
                'postcode' => 'EC',
                'zone' => '10',
            ),
            152 => 
            array (
                'id' => 34,
                'area' => 'Edinburgh',
                'postcode' => 'EH',
                'zone' => '1',
            ),
            153 => 
            array (
                'id' => 35,
                'area' => 'Enfield',
                'postcode' => 'EN',
                'zone' => '10',
            ),
            154 => 
            array (
                'id' => 36,
                'area' => 'Exeter',
                'postcode' => 'EX',
                'zone' => '8',
            ),
            155 => 
            array (
                'id' => 37,
                'area' => 'Falkirk and Stirling',
                'postcode' => 'FK',
                'zone' => '1',
            ),
            156 => 
            array (
                'id' => 38,
                'area' => 'Blackpool',
                'postcode' => 'FY',
                'zone' => '4',
            ),
            157 => 
            array (
                'id' => 39,
                'area' => 'Glasgow',
                'postcode' => 'G',
                'zone' => '1',
            ),
            158 => 
            array (
                'id' => 40,
                'area' => 'Gloucester',
                'postcode' => 'GL',
                'zone' => '8',
            ),
            159 => 
            array (
                'id' => 41,
                'area' => 'Guildford',
                'postcode' => 'GU',
                'zone' => '9',
            ),
            160 => 
            array (
                'id' => 42,
                'area' => 'Harrow',
                'postcode' => 'HA',
                'zone' => '10',
            ),
            161 => 
            array (
                'id' => 43,
                'area' => 'Huddersfield',
                'postcode' => 'HD',
                'zone' => '4',
            ),
            162 => 
            array (
                'id' => 44,
                'area' => 'Harrogate',
                'postcode' => 'HG',
                'zone' => '3',
            ),
            163 => 
            array (
                'id' => 45,
                'area' => 'Hemel Hempstead',
                'postcode' => 'HP',
                'zone' => '9',
            ),
            164 => 
            array (
                'id' => 46,
                'area' => 'Hereford',
                'postcode' => 'HR',
                'zone' => '6',
            ),
            165 => 
            array (
                'id' => 47,
                'area' => 'Hull',
                'postcode' => 'HU',
                'zone' => '3',
            ),
            166 => 
            array (
                'id' => 48,
                'area' => 'Halifax',
                'postcode' => 'HX',
                'zone' => '4',
            ),
            167 => 
            array (
                'id' => 49,
                'area' => 'Ilford',
                'postcode' => 'IG',
                'zone' => '10',
            ),
            168 => 
            array (
                'id' => 50,
                'area' => 'Ipswich',
                'postcode' => 'IP',
                'zone' => '5',
            ),
            169 => 
            array (
                'id' => 51,
                'area' => 'Inverness',
                'postcode' => 'IV',
                'zone' => '1',
            ),
            170 => 
            array (
                'id' => 52,
                'area' => 'Kilmarnock',
                'postcode' => 'KA',
                'zone' => '1',
            ),
            171 => 
            array (
                'id' => 53,
                'area' => 'Kingston upon Thames',
                'postcode' => 'KT',
                'zone' => '10',
            ),
            172 => 
            array (
                'id' => 54,
                'area' => 'Kirkwall',
                'postcode' => 'KW',
                'zone' => '1',
            ),
            173 => 
            array (
                'id' => 55,
                'area' => 'Kirkcaldy',
                'postcode' => 'KY',
                'zone' => '1',
            ),
            174 => 
            array (
                'id' => 56,
                'area' => 'Liverpool',
                'postcode' => 'L',
                'zone' => '4',
            ),
            175 => 
            array (
                'id' => 57,
                'area' => 'Lancaster',
                'postcode' => 'LA',
                'zone' => '4',
            ),
            176 => 
            array (
                'id' => 58,
                'area' => 'Llandrindod Wells',
                'postcode' => 'LD',
                'zone' => '7',
            ),
            177 => 
            array (
                'id' => 59,
                'area' => 'Leicester',
                'postcode' => 'LE',
                'zone' => '5',
            ),
            178 => 
            array (
                'id' => 60,
                'area' => 'Llandudno',
                'postcode' => 'LL',
                'zone' => '7',
            ),
            179 => 
            array (
                'id' => 61,
                'area' => 'Lincoln',
                'postcode' => 'LN',
                'zone' => '5',
            ),
            180 => 
            array (
                'id' => 62,
                'area' => 'Leeds',
                'postcode' => 'LS',
                'zone' => '3',
            ),
            181 => 
            array (
                'id' => 63,
                'area' => 'Luton',
                'postcode' => 'LU',
                'zone' => '9',
            ),
            182 => 
            array (
                'id' => 64,
                'area' => 'Manchester',
                'postcode' => 'M',
                'zone' => '4',
            ),
            183 => 
            array (
                'id' => 65,
                'area' => 'Rochester',
                'postcode' => 'ME',
                'zone' => '9',
            ),
            184 => 
            array (
                'id' => 66,
                'area' => 'Milton Keynes',
                'postcode' => 'MK',
                'zone' => '9',
            ),
            185 => 
            array (
                'id' => 67,
                'area' => 'Motherwell',
                'postcode' => 'ML',
                'zone' => '1',
            ),
            186 => 
            array (
                'id' => 68,
                'area' => 'North London',
                'postcode' => 'N',
                'zone' => '10',
            ),
            187 => 
            array (
                'id' => 69,
                'area' => 'Newcastle upon Tyne',
                'postcode' => 'NE',
                'zone' => '3',
            ),
            188 => 
            array (
                'id' => 70,
                'area' => 'Nottingham',
                'postcode' => 'NG',
                'zone' => '5',
            ),
            189 => 
            array (
                'id' => 71,
                'area' => 'Northampton',
                'postcode' => 'NN',
                'zone' => '6',
            ),
            190 => 
            array (
                'id' => 72,
                'area' => 'Newport',
                'postcode' => 'NP',
                'zone' => '7',
            ),
            191 => 
            array (
                'id' => 73,
                'area' => 'Norwich',
                'postcode' => 'NR',
                'zone' => '5',
            ),
            192 => 
            array (
                'id' => 74,
                'area' => 'North West London',
                'postcode' => 'NW',
                'zone' => '10',
            ),
            193 => 
            array (
                'id' => 75,
                'area' => 'Oldham',
                'postcode' => 'OL',
                'zone' => '4',
            ),
            194 => 
            array (
                'id' => 76,
                'area' => 'Oxford',
                'postcode' => 'OX',
                'zone' => '9',
            ),
            195 => 
            array (
                'id' => 77,
                'area' => 'Paisley',
                'postcode' => 'PA',
                'zone' => '1',
            ),
            196 => 
            array (
                'id' => 78,
                'area' => 'Peterborough',
                'postcode' => 'PE',
                'zone' => '5',
            ),
            197 => 
            array (
                'id' => 79,
                'area' => 'Perth',
                'postcode' => 'PH',
                'zone' => '1',
            ),
            198 => 
            array (
                'id' => 80,
                'area' => 'Plymouth',
                'postcode' => 'PL',
                'zone' => '8',
            ),
            199 => 
            array (
                'id' => 81,
                'area' => 'Portsmouth',
                'postcode' => 'PO',
                'zone' => '9',
            ),
            200 => 
            array (
                'id' => 82,
                'area' => 'Preston',
                'postcode' => 'PR',
                'zone' => '4',
            ),
            201 => 
            array (
                'id' => 83,
                'area' => 'Reading',
                'postcode' => 'RG',
                'zone' => '9',
            ),
            202 => 
            array (
                'id' => 84,
                'area' => 'Redhill',
                'postcode' => 'RH',
                'zone' => '9',
            ),
            203 => 
            array (
                'id' => 85,
                'area' => 'Romford',
                'postcode' => 'RM',
                'zone' => '10',
            ),
            204 => 
            array (
                'id' => 86,
                'area' => 'Sheffield',
                'postcode' => 'S',
                'zone' => '5',
            ),
            205 => 
            array (
                'id' => 87,
                'area' => 'Swansea',
                'postcode' => 'SA',
                'zone' => '7',
            ),
            206 => 
            array (
                'id' => 88,
                'area' => 'South East London',
                'postcode' => 'SE',
                'zone' => '10',
            ),
            207 => 
            array (
                'id' => 89,
                'area' => 'Stevenage',
                'postcode' => 'SG',
                'zone' => '9',
            ),
            208 => 
            array (
                'id' => 90,
                'area' => 'Stockport',
                'postcode' => 'SK',
                'zone' => '4',
            ),
            209 => 
            array (
                'id' => 91,
                'area' => 'Slough',
                'postcode' => 'SL',
                'zone' => '9',
            ),
            210 => 
            array (
                'id' => 92,
                'area' => 'Sutton',
                'postcode' => 'SM',
                'zone' => '10',
            ),
            211 => 
            array (
                'id' => 93,
                'area' => 'Swindon',
                'postcode' => 'SN',
                'zone' => '8',
            ),
            212 => 
            array (
                'id' => 94,
                'area' => 'Southampton',
                'postcode' => 'SO',
                'zone' => '9',
            ),
            213 => 
            array (
                'id' => 95,
                'area' => 'Salisbury',
                'postcode' => 'SP',
                'zone' => '8',
            ),
            214 => 
            array (
                'id' => 96,
                'area' => 'Sunderland',
                'postcode' => 'SR',
                'zone' => '3',
            ),
            215 => 
            array (
                'id' => 97,
                'area' => 'Southend-on-Sea',
                'postcode' => 'SS',
                'zone' => '5',
            ),
            216 => 
            array (
                'id' => 98,
                'area' => 'Stoke-on-Trent',
                'postcode' => 'ST',
                'zone' => '6',
            ),
            217 => 
            array (
                'id' => 99,
                'area' => 'South West London',
                'postcode' => 'SW',
                'zone' => '10',
            ),
            218 => 
            array (
                'id' => 100,
                'area' => 'Shrewsbury',
                'postcode' => 'SY',
                'zone' => '7',
            ),
            219 => 
            array (
                'id' => 101,
                'area' => 'Taunton',
                'postcode' => 'TA',
                'zone' => '8',
            ),
            220 => 
            array (
                'id' => 102,
                'area' => 'Galashiels',
                'postcode' => 'TD',
                'zone' => '1',
            ),
            221 => 
            array (
                'id' => 103,
                'area' => 'Telford',
                'postcode' => 'TF',
                'zone' => '6',
            ),
            222 => 
            array (
                'id' => 104,
                'area' => 'Tonbridge',
                'postcode' => 'TN',
                'zone' => '9',
            ),
            223 => 
            array (
                'id' => 105,
                'area' => 'Torquay',
                'postcode' => 'TQ',
                'zone' => '8',
            ),
            224 => 
            array (
                'id' => 106,
                'area' => 'Truro',
                'postcode' => 'TR',
                'zone' => '8',
            ),
            225 => 
            array (
                'id' => 107,
                'area' => 'Cleveland',
                'postcode' => 'TS',
                'zone' => '3',
            ),
            226 => 
            array (
                'id' => 108,
                'area' => 'Twickenham',
                'postcode' => 'TW',
                'zone' => '10',
            ),
            227 => 
            array (
                'id' => 109,
                'area' => 'Southall',
                'postcode' => 'UB',
                'zone' => '10',
            ),
            228 => 
            array (
                'id' => 110,
                'area' => 'West London',
                'postcode' => 'W',
                'zone' => '10',
            ),
            229 => 
            array (
                'id' => 111,
                'area' => 'Warrington',
                'postcode' => 'WA',
                'zone' => '4',
            ),
            230 => 
            array (
                'id' => 112,
                'area' => 'Central London',
                'postcode' => 'WC',
                'zone' => '10',
            ),
            231 => 
            array (
                'id' => 113,
                'area' => 'Watford',
                'postcode' => 'WD',
                'zone' => '10',
            ),
            232 => 
            array (
                'id' => 114,
                'area' => 'Wakefield',
                'postcode' => 'WF',
                'zone' => '3',
            ),
            233 => 
            array (
                'id' => 115,
                'area' => 'Wigan',
                'postcode' => 'WN',
                'zone' => '4',
            ),
            234 => 
            array (
                'id' => 116,
                'area' => 'Worcester',
                'postcode' => 'WR',
                'zone' => '6',
            ),
            235 => 
            array (
                'id' => 117,
                'area' => 'Walsall',
                'postcode' => 'WS',
                'zone' => '6',
            ),
            236 => 
            array (
                'id' => 118,
                'area' => 'Wolverhampton',
                'postcode' => 'WV',
                'zone' => '6',
            ),
            237 => 
            array (
                'id' => 119,
                'area' => 'York',
                'postcode' => 'YO',
                'zone' => '3',
            ),
            238 => 
            array (
                'id' => 1,
                'area' => 'Aberdeen',
                'postcode' => 'AB',
                'zone' => '1',
            ),
            239 => 
            array (
                'id' => 2,
                'area' => 'St Albans',
                'postcode' => 'AL',
                'zone' => '9',
            ),
            240 => 
            array (
                'id' => 3,
                'area' => 'Birmingham',
                'postcode' => 'B',
                'zone' => '6',
            ),
            241 => 
            array (
                'id' => 4,
                'area' => 'Bath',
                'postcode' => 'BA',
                'zone' => '8',
            ),
            242 => 
            array (
                'id' => 5,
                'area' => 'Blackburn',
                'postcode' => 'BB',
                'zone' => '4',
            ),
            243 => 
            array (
                'id' => 6,
                'area' => 'Bradford',
                'postcode' => 'BD',
                'zone' => '4',
            ),
            244 => 
            array (
                'id' => 7,
                'area' => 'Bournemouth',
                'postcode' => 'BH',
                'zone' => '8',
            ),
            245 => 
            array (
                'id' => 8,
                'area' => 'Bolton',
                'postcode' => 'BL',
                'zone' => '4',
            ),
            246 => 
            array (
                'id' => 9,
                'area' => 'Brighton',
                'postcode' => 'BN',
                'zone' => '9',
            ),
            247 => 
            array (
                'id' => 10,
                'area' => 'Bromley',
                'postcode' => 'BR',
                'zone' => '10',
            ),
            248 => 
            array (
                'id' => 11,
                'area' => 'Bristol',
                'postcode' => 'BS',
                'zone' => '8',
            ),
            249 => 
            array (
                'id' => 12,
                'area' => 'Northern Ireland',
                'postcode' => 'BT',
                'zone' => '2',
            ),
            250 => 
            array (
                'id' => 13,
                'area' => 'Carlisle',
                'postcode' => 'CA',
                'zone' => '4',
            ),
            251 => 
            array (
                'id' => 14,
                'area' => 'Cambridge',
                'postcode' => 'CB',
                'zone' => '5',
            ),
            252 => 
            array (
                'id' => 15,
                'area' => 'Cardiff',
                'postcode' => 'CF',
                'zone' => '7',
            ),
            253 => 
            array (
                'id' => 16,
                'area' => 'Chester',
                'postcode' => 'CH',
                'zone' => '4',
            ),
            254 => 
            array (
                'id' => 17,
                'area' => 'Chelmsford',
                'postcode' => 'CM',
                'zone' => '9',
            ),
            255 => 
            array (
                'id' => 18,
                'area' => 'Colchester',
                'postcode' => 'CO',
                'zone' => '5',
            ),
            256 => 
            array (
                'id' => 19,
                'area' => 'Croydon',
                'postcode' => 'CR',
                'zone' => '10',
            ),
            257 => 
            array (
                'id' => 20,
                'area' => 'Canterbury',
                'postcode' => 'CT',
                'zone' => '9',
            ),
            258 => 
            array (
                'id' => 21,
                'area' => 'Coventry',
                'postcode' => 'CV',
                'zone' => '6',
            ),
            259 => 
            array (
                'id' => 22,
                'area' => 'Crewe',
                'postcode' => 'CW',
                'zone' => '4',
            ),
            260 => 
            array (
                'id' => 23,
                'area' => 'Dartford',
                'postcode' => 'DA',
                'zone' => '10',
            ),
            261 => 
            array (
                'id' => 24,
                'area' => 'Dundee',
                'postcode' => 'DD',
                'zone' => '1',
            ),
            262 => 
            array (
                'id' => 25,
                'area' => 'Derby',
                'postcode' => 'DE',
                'zone' => '5',
            ),
            263 => 
            array (
                'id' => 26,
                'area' => 'Dumfries',
                'postcode' => 'DG',
                'zone' => '1',
            ),
            264 => 
            array (
                'id' => 27,
                'area' => 'Durham',
                'postcode' => 'DH',
                'zone' => '3',
            ),
            265 => 
            array (
                'id' => 28,
                'area' => 'Darlington',
                'postcode' => 'DL',
                'zone' => '3',
            ),
            266 => 
            array (
                'id' => 29,
                'area' => 'Doncaster',
                'postcode' => 'DN',
                'zone' => '5',
            ),
            267 => 
            array (
                'id' => 30,
                'area' => 'Dorchester',
                'postcode' => 'DT',
                'zone' => '8',
            ),
            268 => 
            array (
                'id' => 31,
                'area' => 'Dudley',
                'postcode' => 'DY',
                'zone' => '6',
            ),
            269 => 
            array (
                'id' => 32,
                'area' => 'East London',
                'postcode' => 'E',
                'zone' => '10',
            ),
            270 => 
            array (
                'id' => 33,
                'area' => 'Central London',
                'postcode' => 'EC',
                'zone' => '10',
            ),
            271 => 
            array (
                'id' => 34,
                'area' => 'Edinburgh',
                'postcode' => 'EH',
                'zone' => '1',
            ),
            272 => 
            array (
                'id' => 35,
                'area' => 'Enfield',
                'postcode' => 'EN',
                'zone' => '10',
            ),
            273 => 
            array (
                'id' => 36,
                'area' => 'Exeter',
                'postcode' => 'EX',
                'zone' => '8',
            ),
            274 => 
            array (
                'id' => 37,
                'area' => 'Falkirk and Stirling',
                'postcode' => 'FK',
                'zone' => '1',
            ),
            275 => 
            array (
                'id' => 38,
                'area' => 'Blackpool',
                'postcode' => 'FY',
                'zone' => '4',
            ),
            276 => 
            array (
                'id' => 39,
                'area' => 'Glasgow',
                'postcode' => 'G',
                'zone' => '1',
            ),
            277 => 
            array (
                'id' => 40,
                'area' => 'Gloucester',
                'postcode' => 'GL',
                'zone' => '8',
            ),
            278 => 
            array (
                'id' => 41,
                'area' => 'Guildford',
                'postcode' => 'GU',
                'zone' => '9',
            ),
            279 => 
            array (
                'id' => 42,
                'area' => 'Harrow',
                'postcode' => 'HA',
                'zone' => '10',
            ),
            280 => 
            array (
                'id' => 43,
                'area' => 'Huddersfield',
                'postcode' => 'HD',
                'zone' => '4',
            ),
            281 => 
            array (
                'id' => 44,
                'area' => 'Harrogate',
                'postcode' => 'HG',
                'zone' => '3',
            ),
            282 => 
            array (
                'id' => 45,
                'area' => 'Hemel Hempstead',
                'postcode' => 'HP',
                'zone' => '9',
            ),
            283 => 
            array (
                'id' => 46,
                'area' => 'Hereford',
                'postcode' => 'HR',
                'zone' => '6',
            ),
            284 => 
            array (
                'id' => 47,
                'area' => 'Hull',
                'postcode' => 'HU',
                'zone' => '3',
            ),
            285 => 
            array (
                'id' => 48,
                'area' => 'Halifax',
                'postcode' => 'HX',
                'zone' => '4',
            ),
            286 => 
            array (
                'id' => 49,
                'area' => 'Ilford',
                'postcode' => 'IG',
                'zone' => '10',
            ),
            287 => 
            array (
                'id' => 50,
                'area' => 'Ipswich',
                'postcode' => 'IP',
                'zone' => '5',
            ),
            288 => 
            array (
                'id' => 51,
                'area' => 'Inverness',
                'postcode' => 'IV',
                'zone' => '1',
            ),
            289 => 
            array (
                'id' => 52,
                'area' => 'Kilmarnock',
                'postcode' => 'KA',
                'zone' => '1',
            ),
            290 => 
            array (
                'id' => 53,
                'area' => 'Kingston upon Thames',
                'postcode' => 'KT',
                'zone' => '10',
            ),
            291 => 
            array (
                'id' => 54,
                'area' => 'Kirkwall',
                'postcode' => 'KW',
                'zone' => '1',
            ),
            292 => 
            array (
                'id' => 55,
                'area' => 'Kirkcaldy',
                'postcode' => 'KY',
                'zone' => '1',
            ),
            293 => 
            array (
                'id' => 56,
                'area' => 'Liverpool',
                'postcode' => 'L',
                'zone' => '4',
            ),
            294 => 
            array (
                'id' => 57,
                'area' => 'Lancaster',
                'postcode' => 'LA',
                'zone' => '4',
            ),
            295 => 
            array (
                'id' => 58,
                'area' => 'Llandrindod Wells',
                'postcode' => 'LD',
                'zone' => '7',
            ),
            296 => 
            array (
                'id' => 59,
                'area' => 'Leicester',
                'postcode' => 'LE',
                'zone' => '5',
            ),
            297 => 
            array (
                'id' => 60,
                'area' => 'Llandudno',
                'postcode' => 'LL',
                'zone' => '7',
            ),
            298 => 
            array (
                'id' => 61,
                'area' => 'Lincoln',
                'postcode' => 'LN',
                'zone' => '5',
            ),
            299 => 
            array (
                'id' => 62,
                'area' => 'Leeds',
                'postcode' => 'LS',
                'zone' => '3',
            ),
            300 => 
            array (
                'id' => 63,
                'area' => 'Luton',
                'postcode' => 'LU',
                'zone' => '9',
            ),
            301 => 
            array (
                'id' => 64,
                'area' => 'Manchester',
                'postcode' => 'M',
                'zone' => '4',
            ),
            302 => 
            array (
                'id' => 65,
                'area' => 'Rochester',
                'postcode' => 'ME',
                'zone' => '9',
            ),
            303 => 
            array (
                'id' => 66,
                'area' => 'Milton Keynes',
                'postcode' => 'MK',
                'zone' => '9',
            ),
            304 => 
            array (
                'id' => 67,
                'area' => 'Motherwell',
                'postcode' => 'ML',
                'zone' => '1',
            ),
            305 => 
            array (
                'id' => 68,
                'area' => 'North London',
                'postcode' => 'N',
                'zone' => '10',
            ),
            306 => 
            array (
                'id' => 69,
                'area' => 'Newcastle upon Tyne',
                'postcode' => 'NE',
                'zone' => '3',
            ),
            307 => 
            array (
                'id' => 70,
                'area' => 'Nottingham',
                'postcode' => 'NG',
                'zone' => '5',
            ),
            308 => 
            array (
                'id' => 71,
                'area' => 'Northampton',
                'postcode' => 'NN',
                'zone' => '6',
            ),
            309 => 
            array (
                'id' => 72,
                'area' => 'Newport',
                'postcode' => 'NP',
                'zone' => '7',
            ),
            310 => 
            array (
                'id' => 73,
                'area' => 'Norwich',
                'postcode' => 'NR',
                'zone' => '5',
            ),
            311 => 
            array (
                'id' => 74,
                'area' => 'North West London',
                'postcode' => 'NW',
                'zone' => '10',
            ),
            312 => 
            array (
                'id' => 75,
                'area' => 'Oldham',
                'postcode' => 'OL',
                'zone' => '4',
            ),
            313 => 
            array (
                'id' => 76,
                'area' => 'Oxford',
                'postcode' => 'OX',
                'zone' => '9',
            ),
            314 => 
            array (
                'id' => 77,
                'area' => 'Paisley',
                'postcode' => 'PA',
                'zone' => '1',
            ),
            315 => 
            array (
                'id' => 78,
                'area' => 'Peterborough',
                'postcode' => 'PE',
                'zone' => '5',
            ),
            316 => 
            array (
                'id' => 79,
                'area' => 'Perth',
                'postcode' => 'PH',
                'zone' => '1',
            ),
            317 => 
            array (
                'id' => 80,
                'area' => 'Plymouth',
                'postcode' => 'PL',
                'zone' => '8',
            ),
            318 => 
            array (
                'id' => 81,
                'area' => 'Portsmouth',
                'postcode' => 'PO',
                'zone' => '9',
            ),
            319 => 
            array (
                'id' => 82,
                'area' => 'Preston',
                'postcode' => 'PR',
                'zone' => '4',
            ),
            320 => 
            array (
                'id' => 83,
                'area' => 'Reading',
                'postcode' => 'RG',
                'zone' => '9',
            ),
            321 => 
            array (
                'id' => 84,
                'area' => 'Redhill',
                'postcode' => 'RH',
                'zone' => '9',
            ),
            322 => 
            array (
                'id' => 85,
                'area' => 'Romford',
                'postcode' => 'RM',
                'zone' => '10',
            ),
            323 => 
            array (
                'id' => 86,
                'area' => 'Sheffield',
                'postcode' => 'S',
                'zone' => '5',
            ),
            324 => 
            array (
                'id' => 87,
                'area' => 'Swansea',
                'postcode' => 'SA',
                'zone' => '7',
            ),
            325 => 
            array (
                'id' => 88,
                'area' => 'South East London',
                'postcode' => 'SE',
                'zone' => '10',
            ),
            326 => 
            array (
                'id' => 89,
                'area' => 'Stevenage',
                'postcode' => 'SG',
                'zone' => '9',
            ),
            327 => 
            array (
                'id' => 90,
                'area' => 'Stockport',
                'postcode' => 'SK',
                'zone' => '4',
            ),
            328 => 
            array (
                'id' => 91,
                'area' => 'Slough',
                'postcode' => 'SL',
                'zone' => '9',
            ),
            329 => 
            array (
                'id' => 92,
                'area' => 'Sutton',
                'postcode' => 'SM',
                'zone' => '10',
            ),
            330 => 
            array (
                'id' => 93,
                'area' => 'Swindon',
                'postcode' => 'SN',
                'zone' => '8',
            ),
            331 => 
            array (
                'id' => 94,
                'area' => 'Southampton',
                'postcode' => 'SO',
                'zone' => '9',
            ),
            332 => 
            array (
                'id' => 95,
                'area' => 'Salisbury',
                'postcode' => 'SP',
                'zone' => '8',
            ),
            333 => 
            array (
                'id' => 96,
                'area' => 'Sunderland',
                'postcode' => 'SR',
                'zone' => '3',
            ),
            334 => 
            array (
                'id' => 97,
                'area' => 'Southend-on-Sea',
                'postcode' => 'SS',
                'zone' => '5',
            ),
            335 => 
            array (
                'id' => 98,
                'area' => 'Stoke-on-Trent',
                'postcode' => 'ST',
                'zone' => '6',
            ),
            336 => 
            array (
                'id' => 99,
                'area' => 'South West London',
                'postcode' => 'SW',
                'zone' => '10',
            ),
            337 => 
            array (
                'id' => 100,
                'area' => 'Shrewsbury',
                'postcode' => 'SY',
                'zone' => '7',
            ),
            338 => 
            array (
                'id' => 101,
                'area' => 'Taunton',
                'postcode' => 'TA',
                'zone' => '8',
            ),
            339 => 
            array (
                'id' => 102,
                'area' => 'Galashiels',
                'postcode' => 'TD',
                'zone' => '1',
            ),
            340 => 
            array (
                'id' => 103,
                'area' => 'Telford',
                'postcode' => 'TF',
                'zone' => '6',
            ),
            341 => 
            array (
                'id' => 104,
                'area' => 'Tonbridge',
                'postcode' => 'TN',
                'zone' => '9',
            ),
            342 => 
            array (
                'id' => 105,
                'area' => 'Torquay',
                'postcode' => 'TQ',
                'zone' => '8',
            ),
            343 => 
            array (
                'id' => 106,
                'area' => 'Truro',
                'postcode' => 'TR',
                'zone' => '8',
            ),
            344 => 
            array (
                'id' => 107,
                'area' => 'Cleveland',
                'postcode' => 'TS',
                'zone' => '3',
            ),
            345 => 
            array (
                'id' => 108,
                'area' => 'Twickenham',
                'postcode' => 'TW',
                'zone' => '10',
            ),
            346 => 
            array (
                'id' => 109,
                'area' => 'Southall',
                'postcode' => 'UB',
                'zone' => '10',
            ),
            347 => 
            array (
                'id' => 110,
                'area' => 'West London',
                'postcode' => 'W',
                'zone' => '10',
            ),
            348 => 
            array (
                'id' => 111,
                'area' => 'Warrington',
                'postcode' => 'WA',
                'zone' => '4',
            ),
            349 => 
            array (
                'id' => 112,
                'area' => 'Central London',
                'postcode' => 'WC',
                'zone' => '10',
            ),
            350 => 
            array (
                'id' => 113,
                'area' => 'Watford',
                'postcode' => 'WD',
                'zone' => '10',
            ),
            351 => 
            array (
                'id' => 114,
                'area' => 'Wakefield',
                'postcode' => 'WF',
                'zone' => '3',
            ),
            352 => 
            array (
                'id' => 115,
                'area' => 'Wigan',
                'postcode' => 'WN',
                'zone' => '4',
            ),
            353 => 
            array (
                'id' => 116,
                'area' => 'Worcester',
                'postcode' => 'WR',
                'zone' => '6',
            ),
            354 => 
            array (
                'id' => 117,
                'area' => 'Walsall',
                'postcode' => 'WS',
                'zone' => '6',
            ),
            355 => 
            array (
                'id' => 118,
                'area' => 'Wolverhampton',
                'postcode' => 'WV',
                'zone' => '6',
            ),
            356 => 
            array (
                'id' => 119,
                'area' => 'York',
                'postcode' => 'YO',
                'zone' => '3',
            ),
            357 => 
            array (
                'id' => 1,
                'area' => 'Aberdeen',
                'postcode' => 'AB',
                'zone' => '1',
            ),
            358 => 
            array (
                'id' => 2,
                'area' => 'St Albans',
                'postcode' => 'AL',
                'zone' => '9',
            ),
            359 => 
            array (
                'id' => 3,
                'area' => 'Birmingham',
                'postcode' => 'B',
                'zone' => '6',
            ),
            360 => 
            array (
                'id' => 4,
                'area' => 'Bath',
                'postcode' => 'BA',
                'zone' => '8',
            ),
            361 => 
            array (
                'id' => 5,
                'area' => 'Blackburn',
                'postcode' => 'BB',
                'zone' => '4',
            ),
            362 => 
            array (
                'id' => 6,
                'area' => 'Bradford',
                'postcode' => 'BD',
                'zone' => '4',
            ),
            363 => 
            array (
                'id' => 7,
                'area' => 'Bournemouth',
                'postcode' => 'BH',
                'zone' => '8',
            ),
            364 => 
            array (
                'id' => 8,
                'area' => 'Bolton',
                'postcode' => 'BL',
                'zone' => '4',
            ),
            365 => 
            array (
                'id' => 9,
                'area' => 'Brighton',
                'postcode' => 'BN',
                'zone' => '9',
            ),
            366 => 
            array (
                'id' => 10,
                'area' => 'Bromley',
                'postcode' => 'BR',
                'zone' => '10',
            ),
            367 => 
            array (
                'id' => 11,
                'area' => 'Bristol',
                'postcode' => 'BS',
                'zone' => '8',
            ),
            368 => 
            array (
                'id' => 12,
                'area' => 'Northern Ireland',
                'postcode' => 'BT',
                'zone' => '2',
            ),
            369 => 
            array (
                'id' => 13,
                'area' => 'Carlisle',
                'postcode' => 'CA',
                'zone' => '4',
            ),
            370 => 
            array (
                'id' => 14,
                'area' => 'Cambridge',
                'postcode' => 'CB',
                'zone' => '5',
            ),
            371 => 
            array (
                'id' => 15,
                'area' => 'Cardiff',
                'postcode' => 'CF',
                'zone' => '7',
            ),
            372 => 
            array (
                'id' => 16,
                'area' => 'Chester',
                'postcode' => 'CH',
                'zone' => '4',
            ),
            373 => 
            array (
                'id' => 17,
                'area' => 'Chelmsford',
                'postcode' => 'CM',
                'zone' => '9',
            ),
            374 => 
            array (
                'id' => 18,
                'area' => 'Colchester',
                'postcode' => 'CO',
                'zone' => '5',
            ),
            375 => 
            array (
                'id' => 19,
                'area' => 'Croydon',
                'postcode' => 'CR',
                'zone' => '10',
            ),
            376 => 
            array (
                'id' => 20,
                'area' => 'Canterbury',
                'postcode' => 'CT',
                'zone' => '9',
            ),
            377 => 
            array (
                'id' => 21,
                'area' => 'Coventry',
                'postcode' => 'CV',
                'zone' => '6',
            ),
            378 => 
            array (
                'id' => 22,
                'area' => 'Crewe',
                'postcode' => 'CW',
                'zone' => '4',
            ),
            379 => 
            array (
                'id' => 23,
                'area' => 'Dartford',
                'postcode' => 'DA',
                'zone' => '10',
            ),
            380 => 
            array (
                'id' => 24,
                'area' => 'Dundee',
                'postcode' => 'DD',
                'zone' => '1',
            ),
            381 => 
            array (
                'id' => 25,
                'area' => 'Derby',
                'postcode' => 'DE',
                'zone' => '5',
            ),
            382 => 
            array (
                'id' => 26,
                'area' => 'Dumfries',
                'postcode' => 'DG',
                'zone' => '1',
            ),
            383 => 
            array (
                'id' => 27,
                'area' => 'Durham',
                'postcode' => 'DH',
                'zone' => '3',
            ),
            384 => 
            array (
                'id' => 28,
                'area' => 'Darlington',
                'postcode' => 'DL',
                'zone' => '3',
            ),
            385 => 
            array (
                'id' => 29,
                'area' => 'Doncaster',
                'postcode' => 'DN',
                'zone' => '5',
            ),
            386 => 
            array (
                'id' => 30,
                'area' => 'Dorchester',
                'postcode' => 'DT',
                'zone' => '8',
            ),
            387 => 
            array (
                'id' => 31,
                'area' => 'Dudley',
                'postcode' => 'DY',
                'zone' => '6',
            ),
            388 => 
            array (
                'id' => 32,
                'area' => 'East London',
                'postcode' => 'E',
                'zone' => '10',
            ),
            389 => 
            array (
                'id' => 33,
                'area' => 'Central London',
                'postcode' => 'EC',
                'zone' => '10',
            ),
            390 => 
            array (
                'id' => 34,
                'area' => 'Edinburgh',
                'postcode' => 'EH',
                'zone' => '1',
            ),
            391 => 
            array (
                'id' => 35,
                'area' => 'Enfield',
                'postcode' => 'EN',
                'zone' => '10',
            ),
            392 => 
            array (
                'id' => 36,
                'area' => 'Exeter',
                'postcode' => 'EX',
                'zone' => '8',
            ),
            393 => 
            array (
                'id' => 37,
                'area' => 'Falkirk and Stirling',
                'postcode' => 'FK',
                'zone' => '1',
            ),
            394 => 
            array (
                'id' => 38,
                'area' => 'Blackpool',
                'postcode' => 'FY',
                'zone' => '4',
            ),
            395 => 
            array (
                'id' => 39,
                'area' => 'Glasgow',
                'postcode' => 'G',
                'zone' => '1',
            ),
            396 => 
            array (
                'id' => 40,
                'area' => 'Gloucester',
                'postcode' => 'GL',
                'zone' => '8',
            ),
            397 => 
            array (
                'id' => 41,
                'area' => 'Guildford',
                'postcode' => 'GU',
                'zone' => '9',
            ),
            398 => 
            array (
                'id' => 42,
                'area' => 'Harrow',
                'postcode' => 'HA',
                'zone' => '10',
            ),
            399 => 
            array (
                'id' => 43,
                'area' => 'Huddersfield',
                'postcode' => 'HD',
                'zone' => '4',
            ),
            400 => 
            array (
                'id' => 44,
                'area' => 'Harrogate',
                'postcode' => 'HG',
                'zone' => '3',
            ),
            401 => 
            array (
                'id' => 45,
                'area' => 'Hemel Hempstead',
                'postcode' => 'HP',
                'zone' => '9',
            ),
            402 => 
            array (
                'id' => 46,
                'area' => 'Hereford',
                'postcode' => 'HR',
                'zone' => '6',
            ),
            403 => 
            array (
                'id' => 47,
                'area' => 'Hull',
                'postcode' => 'HU',
                'zone' => '3',
            ),
            404 => 
            array (
                'id' => 48,
                'area' => 'Halifax',
                'postcode' => 'HX',
                'zone' => '4',
            ),
            405 => 
            array (
                'id' => 49,
                'area' => 'Ilford',
                'postcode' => 'IG',
                'zone' => '10',
            ),
            406 => 
            array (
                'id' => 50,
                'area' => 'Ipswich',
                'postcode' => 'IP',
                'zone' => '5',
            ),
            407 => 
            array (
                'id' => 51,
                'area' => 'Inverness',
                'postcode' => 'IV',
                'zone' => '1',
            ),
            408 => 
            array (
                'id' => 52,
                'area' => 'Kilmarnock',
                'postcode' => 'KA',
                'zone' => '1',
            ),
            409 => 
            array (
                'id' => 53,
                'area' => 'Kingston upon Thames',
                'postcode' => 'KT',
                'zone' => '10',
            ),
            410 => 
            array (
                'id' => 54,
                'area' => 'Kirkwall',
                'postcode' => 'KW',
                'zone' => '1',
            ),
            411 => 
            array (
                'id' => 55,
                'area' => 'Kirkcaldy',
                'postcode' => 'KY',
                'zone' => '1',
            ),
            412 => 
            array (
                'id' => 56,
                'area' => 'Liverpool',
                'postcode' => 'L',
                'zone' => '4',
            ),
            413 => 
            array (
                'id' => 57,
                'area' => 'Lancaster',
                'postcode' => 'LA',
                'zone' => '4',
            ),
            414 => 
            array (
                'id' => 58,
                'area' => 'Llandrindod Wells',
                'postcode' => 'LD',
                'zone' => '7',
            ),
            415 => 
            array (
                'id' => 59,
                'area' => 'Leicester',
                'postcode' => 'LE',
                'zone' => '5',
            ),
            416 => 
            array (
                'id' => 60,
                'area' => 'Llandudno',
                'postcode' => 'LL',
                'zone' => '7',
            ),
            417 => 
            array (
                'id' => 61,
                'area' => 'Lincoln',
                'postcode' => 'LN',
                'zone' => '5',
            ),
            418 => 
            array (
                'id' => 62,
                'area' => 'Leeds',
                'postcode' => 'LS',
                'zone' => '3',
            ),
            419 => 
            array (
                'id' => 63,
                'area' => 'Luton',
                'postcode' => 'LU',
                'zone' => '9',
            ),
            420 => 
            array (
                'id' => 64,
                'area' => 'Manchester',
                'postcode' => 'M',
                'zone' => '4',
            ),
            421 => 
            array (
                'id' => 65,
                'area' => 'Rochester',
                'postcode' => 'ME',
                'zone' => '9',
            ),
            422 => 
            array (
                'id' => 66,
                'area' => 'Milton Keynes',
                'postcode' => 'MK',
                'zone' => '9',
            ),
            423 => 
            array (
                'id' => 67,
                'area' => 'Motherwell',
                'postcode' => 'ML',
                'zone' => '1',
            ),
            424 => 
            array (
                'id' => 68,
                'area' => 'North London',
                'postcode' => 'N',
                'zone' => '10',
            ),
            425 => 
            array (
                'id' => 69,
                'area' => 'Newcastle upon Tyne',
                'postcode' => 'NE',
                'zone' => '3',
            ),
            426 => 
            array (
                'id' => 70,
                'area' => 'Nottingham',
                'postcode' => 'NG',
                'zone' => '5',
            ),
            427 => 
            array (
                'id' => 71,
                'area' => 'Northampton',
                'postcode' => 'NN',
                'zone' => '6',
            ),
            428 => 
            array (
                'id' => 72,
                'area' => 'Newport',
                'postcode' => 'NP',
                'zone' => '7',
            ),
            429 => 
            array (
                'id' => 73,
                'area' => 'Norwich',
                'postcode' => 'NR',
                'zone' => '5',
            ),
            430 => 
            array (
                'id' => 74,
                'area' => 'North West London',
                'postcode' => 'NW',
                'zone' => '10',
            ),
            431 => 
            array (
                'id' => 75,
                'area' => 'Oldham',
                'postcode' => 'OL',
                'zone' => '4',
            ),
            432 => 
            array (
                'id' => 76,
                'area' => 'Oxford',
                'postcode' => 'OX',
                'zone' => '9',
            ),
            433 => 
            array (
                'id' => 77,
                'area' => 'Paisley',
                'postcode' => 'PA',
                'zone' => '1',
            ),
            434 => 
            array (
                'id' => 78,
                'area' => 'Peterborough',
                'postcode' => 'PE',
                'zone' => '5',
            ),
            435 => 
            array (
                'id' => 79,
                'area' => 'Perth',
                'postcode' => 'PH',
                'zone' => '1',
            ),
            436 => 
            array (
                'id' => 80,
                'area' => 'Plymouth',
                'postcode' => 'PL',
                'zone' => '8',
            ),
            437 => 
            array (
                'id' => 81,
                'area' => 'Portsmouth',
                'postcode' => 'PO',
                'zone' => '9',
            ),
            438 => 
            array (
                'id' => 82,
                'area' => 'Preston',
                'postcode' => 'PR',
                'zone' => '4',
            ),
            439 => 
            array (
                'id' => 83,
                'area' => 'Reading',
                'postcode' => 'RG',
                'zone' => '9',
            ),
            440 => 
            array (
                'id' => 84,
                'area' => 'Redhill',
                'postcode' => 'RH',
                'zone' => '9',
            ),
            441 => 
            array (
                'id' => 85,
                'area' => 'Romford',
                'postcode' => 'RM',
                'zone' => '10',
            ),
            442 => 
            array (
                'id' => 86,
                'area' => 'Sheffield',
                'postcode' => 'S',
                'zone' => '5',
            ),
            443 => 
            array (
                'id' => 87,
                'area' => 'Swansea',
                'postcode' => 'SA',
                'zone' => '7',
            ),
            444 => 
            array (
                'id' => 88,
                'area' => 'South East London',
                'postcode' => 'SE',
                'zone' => '10',
            ),
            445 => 
            array (
                'id' => 89,
                'area' => 'Stevenage',
                'postcode' => 'SG',
                'zone' => '9',
            ),
            446 => 
            array (
                'id' => 90,
                'area' => 'Stockport',
                'postcode' => 'SK',
                'zone' => '4',
            ),
            447 => 
            array (
                'id' => 91,
                'area' => 'Slough',
                'postcode' => 'SL',
                'zone' => '9',
            ),
            448 => 
            array (
                'id' => 92,
                'area' => 'Sutton',
                'postcode' => 'SM',
                'zone' => '10',
            ),
            449 => 
            array (
                'id' => 93,
                'area' => 'Swindon',
                'postcode' => 'SN',
                'zone' => '8',
            ),
            450 => 
            array (
                'id' => 94,
                'area' => 'Southampton',
                'postcode' => 'SO',
                'zone' => '9',
            ),
            451 => 
            array (
                'id' => 95,
                'area' => 'Salisbury',
                'postcode' => 'SP',
                'zone' => '8',
            ),
            452 => 
            array (
                'id' => 96,
                'area' => 'Sunderland',
                'postcode' => 'SR',
                'zone' => '3',
            ),
            453 => 
            array (
                'id' => 97,
                'area' => 'Southend-on-Sea',
                'postcode' => 'SS',
                'zone' => '5',
            ),
            454 => 
            array (
                'id' => 98,
                'area' => 'Stoke-on-Trent',
                'postcode' => 'ST',
                'zone' => '6',
            ),
            455 => 
            array (
                'id' => 99,
                'area' => 'South West London',
                'postcode' => 'SW',
                'zone' => '10',
            ),
            456 => 
            array (
                'id' => 100,
                'area' => 'Shrewsbury',
                'postcode' => 'SY',
                'zone' => '7',
            ),
            457 => 
            array (
                'id' => 101,
                'area' => 'Taunton',
                'postcode' => 'TA',
                'zone' => '8',
            ),
            458 => 
            array (
                'id' => 102,
                'area' => 'Galashiels',
                'postcode' => 'TD',
                'zone' => '1',
            ),
            459 => 
            array (
                'id' => 103,
                'area' => 'Telford',
                'postcode' => 'TF',
                'zone' => '6',
            ),
            460 => 
            array (
                'id' => 104,
                'area' => 'Tonbridge',
                'postcode' => 'TN',
                'zone' => '9',
            ),
            461 => 
            array (
                'id' => 105,
                'area' => 'Torquay',
                'postcode' => 'TQ',
                'zone' => '8',
            ),
            462 => 
            array (
                'id' => 106,
                'area' => 'Truro',
                'postcode' => 'TR',
                'zone' => '8',
            ),
            463 => 
            array (
                'id' => 107,
                'area' => 'Cleveland',
                'postcode' => 'TS',
                'zone' => '3',
            ),
            464 => 
            array (
                'id' => 108,
                'area' => 'Twickenham',
                'postcode' => 'TW',
                'zone' => '10',
            ),
            465 => 
            array (
                'id' => 109,
                'area' => 'Southall',
                'postcode' => 'UB',
                'zone' => '10',
            ),
            466 => 
            array (
                'id' => 110,
                'area' => 'West London',
                'postcode' => 'W',
                'zone' => '10',
            ),
            467 => 
            array (
                'id' => 111,
                'area' => 'Warrington',
                'postcode' => 'WA',
                'zone' => '4',
            ),
            468 => 
            array (
                'id' => 112,
                'area' => 'Central London',
                'postcode' => 'WC',
                'zone' => '10',
            ),
            469 => 
            array (
                'id' => 113,
                'area' => 'Watford',
                'postcode' => 'WD',
                'zone' => '10',
            ),
            470 => 
            array (
                'id' => 114,
                'area' => 'Wakefield',
                'postcode' => 'WF',
                'zone' => '3',
            ),
            471 => 
            array (
                'id' => 115,
                'area' => 'Wigan',
                'postcode' => 'WN',
                'zone' => '4',
            ),
            472 => 
            array (
                'id' => 116,
                'area' => 'Worcester',
                'postcode' => 'WR',
                'zone' => '6',
            ),
            473 => 
            array (
                'id' => 117,
                'area' => 'Walsall',
                'postcode' => 'WS',
                'zone' => '6',
            ),
            474 => 
            array (
                'id' => 118,
                'area' => 'Wolverhampton',
                'postcode' => 'WV',
                'zone' => '6',
            ),
            475 => 
            array (
                'id' => 119,
                'area' => 'York',
                'postcode' => 'YO',
                'zone' => '3',
            ),
            476 => 
            array (
                'id' => 1,
                'area' => 'Aberdeen',
                'postcode' => 'AB',
                'zone' => '1',
            ),
            477 => 
            array (
                'id' => 2,
                'area' => 'St Albans',
                'postcode' => 'AL',
                'zone' => '9',
            ),
            478 => 
            array (
                'id' => 3,
                'area' => 'Birmingham',
                'postcode' => 'B',
                'zone' => '6',
            ),
            479 => 
            array (
                'id' => 4,
                'area' => 'Bath',
                'postcode' => 'BA',
                'zone' => '8',
            ),
            480 => 
            array (
                'id' => 5,
                'area' => 'Blackburn',
                'postcode' => 'BB',
                'zone' => '4',
            ),
            481 => 
            array (
                'id' => 6,
                'area' => 'Bradford',
                'postcode' => 'BD',
                'zone' => '4',
            ),
            482 => 
            array (
                'id' => 7,
                'area' => 'Bournemouth',
                'postcode' => 'BH',
                'zone' => '8',
            ),
            483 => 
            array (
                'id' => 8,
                'area' => 'Bolton',
                'postcode' => 'BL',
                'zone' => '4',
            ),
            484 => 
            array (
                'id' => 9,
                'area' => 'Brighton',
                'postcode' => 'BN',
                'zone' => '9',
            ),
            485 => 
            array (
                'id' => 10,
                'area' => 'Bromley',
                'postcode' => 'BR',
                'zone' => '10',
            ),
            486 => 
            array (
                'id' => 11,
                'area' => 'Bristol',
                'postcode' => 'BS',
                'zone' => '8',
            ),
            487 => 
            array (
                'id' => 12,
                'area' => 'Northern Ireland',
                'postcode' => 'BT',
                'zone' => '2',
            ),
            488 => 
            array (
                'id' => 13,
                'area' => 'Carlisle',
                'postcode' => 'CA',
                'zone' => '4',
            ),
            489 => 
            array (
                'id' => 14,
                'area' => 'Cambridge',
                'postcode' => 'CB',
                'zone' => '5',
            ),
            490 => 
            array (
                'id' => 15,
                'area' => 'Cardiff',
                'postcode' => 'CF',
                'zone' => '7',
            ),
            491 => 
            array (
                'id' => 16,
                'area' => 'Chester',
                'postcode' => 'CH',
                'zone' => '4',
            ),
            492 => 
            array (
                'id' => 17,
                'area' => 'Chelmsford',
                'postcode' => 'CM',
                'zone' => '9',
            ),
            493 => 
            array (
                'id' => 18,
                'area' => 'Colchester',
                'postcode' => 'CO',
                'zone' => '5',
            ),
            494 => 
            array (
                'id' => 19,
                'area' => 'Croydon',
                'postcode' => 'CR',
                'zone' => '10',
            ),
            495 => 
            array (
                'id' => 20,
                'area' => 'Canterbury',
                'postcode' => 'CT',
                'zone' => '9',
            ),
            496 => 
            array (
                'id' => 21,
                'area' => 'Coventry',
                'postcode' => 'CV',
                'zone' => '6',
            ),
            497 => 
            array (
                'id' => 22,
                'area' => 'Crewe',
                'postcode' => 'CW',
                'zone' => '4',
            ),
            498 => 
            array (
                'id' => 23,
                'area' => 'Dartford',
                'postcode' => 'DA',
                'zone' => '10',
            ),
            499 => 
            array (
                'id' => 24,
                'area' => 'Dundee',
                'postcode' => 'DD',
                'zone' => '1',
            ),
        ));
        \DB::table('sorter_postcode_zones')->insert(array (
            0 => 
            array (
                'id' => 25,
                'area' => 'Derby',
                'postcode' => 'DE',
                'zone' => '5',
            ),
            1 => 
            array (
                'id' => 26,
                'area' => 'Dumfries',
                'postcode' => 'DG',
                'zone' => '1',
            ),
            2 => 
            array (
                'id' => 27,
                'area' => 'Durham',
                'postcode' => 'DH',
                'zone' => '3',
            ),
            3 => 
            array (
                'id' => 28,
                'area' => 'Darlington',
                'postcode' => 'DL',
                'zone' => '3',
            ),
            4 => 
            array (
                'id' => 29,
                'area' => 'Doncaster',
                'postcode' => 'DN',
                'zone' => '5',
            ),
            5 => 
            array (
                'id' => 30,
                'area' => 'Dorchester',
                'postcode' => 'DT',
                'zone' => '8',
            ),
            6 => 
            array (
                'id' => 31,
                'area' => 'Dudley',
                'postcode' => 'DY',
                'zone' => '6',
            ),
            7 => 
            array (
                'id' => 32,
                'area' => 'East London',
                'postcode' => 'E',
                'zone' => '10',
            ),
            8 => 
            array (
                'id' => 33,
                'area' => 'Central London',
                'postcode' => 'EC',
                'zone' => '10',
            ),
            9 => 
            array (
                'id' => 34,
                'area' => 'Edinburgh',
                'postcode' => 'EH',
                'zone' => '1',
            ),
            10 => 
            array (
                'id' => 35,
                'area' => 'Enfield',
                'postcode' => 'EN',
                'zone' => '10',
            ),
            11 => 
            array (
                'id' => 36,
                'area' => 'Exeter',
                'postcode' => 'EX',
                'zone' => '8',
            ),
            12 => 
            array (
                'id' => 37,
                'area' => 'Falkirk and Stirling',
                'postcode' => 'FK',
                'zone' => '1',
            ),
            13 => 
            array (
                'id' => 38,
                'area' => 'Blackpool',
                'postcode' => 'FY',
                'zone' => '4',
            ),
            14 => 
            array (
                'id' => 39,
                'area' => 'Glasgow',
                'postcode' => 'G',
                'zone' => '1',
            ),
            15 => 
            array (
                'id' => 40,
                'area' => 'Gloucester',
                'postcode' => 'GL',
                'zone' => '8',
            ),
            16 => 
            array (
                'id' => 41,
                'area' => 'Guildford',
                'postcode' => 'GU',
                'zone' => '9',
            ),
            17 => 
            array (
                'id' => 42,
                'area' => 'Harrow',
                'postcode' => 'HA',
                'zone' => '10',
            ),
            18 => 
            array (
                'id' => 43,
                'area' => 'Huddersfield',
                'postcode' => 'HD',
                'zone' => '4',
            ),
            19 => 
            array (
                'id' => 44,
                'area' => 'Harrogate',
                'postcode' => 'HG',
                'zone' => '3',
            ),
            20 => 
            array (
                'id' => 45,
                'area' => 'Hemel Hempstead',
                'postcode' => 'HP',
                'zone' => '9',
            ),
            21 => 
            array (
                'id' => 46,
                'area' => 'Hereford',
                'postcode' => 'HR',
                'zone' => '6',
            ),
            22 => 
            array (
                'id' => 47,
                'area' => 'Hull',
                'postcode' => 'HU',
                'zone' => '3',
            ),
            23 => 
            array (
                'id' => 48,
                'area' => 'Halifax',
                'postcode' => 'HX',
                'zone' => '4',
            ),
            24 => 
            array (
                'id' => 49,
                'area' => 'Ilford',
                'postcode' => 'IG',
                'zone' => '10',
            ),
            25 => 
            array (
                'id' => 50,
                'area' => 'Ipswich',
                'postcode' => 'IP',
                'zone' => '5',
            ),
            26 => 
            array (
                'id' => 51,
                'area' => 'Inverness',
                'postcode' => 'IV',
                'zone' => '1',
            ),
            27 => 
            array (
                'id' => 52,
                'area' => 'Kilmarnock',
                'postcode' => 'KA',
                'zone' => '1',
            ),
            28 => 
            array (
                'id' => 53,
                'area' => 'Kingston upon Thames',
                'postcode' => 'KT',
                'zone' => '10',
            ),
            29 => 
            array (
                'id' => 54,
                'area' => 'Kirkwall',
                'postcode' => 'KW',
                'zone' => '1',
            ),
            30 => 
            array (
                'id' => 55,
                'area' => 'Kirkcaldy',
                'postcode' => 'KY',
                'zone' => '1',
            ),
            31 => 
            array (
                'id' => 56,
                'area' => 'Liverpool',
                'postcode' => 'L',
                'zone' => '4',
            ),
            32 => 
            array (
                'id' => 57,
                'area' => 'Lancaster',
                'postcode' => 'LA',
                'zone' => '4',
            ),
            33 => 
            array (
                'id' => 58,
                'area' => 'Llandrindod Wells',
                'postcode' => 'LD',
                'zone' => '7',
            ),
            34 => 
            array (
                'id' => 59,
                'area' => 'Leicester',
                'postcode' => 'LE',
                'zone' => '5',
            ),
            35 => 
            array (
                'id' => 60,
                'area' => 'Llandudno',
                'postcode' => 'LL',
                'zone' => '7',
            ),
            36 => 
            array (
                'id' => 61,
                'area' => 'Lincoln',
                'postcode' => 'LN',
                'zone' => '5',
            ),
            37 => 
            array (
                'id' => 62,
                'area' => 'Leeds',
                'postcode' => 'LS',
                'zone' => '3',
            ),
            38 => 
            array (
                'id' => 63,
                'area' => 'Luton',
                'postcode' => 'LU',
                'zone' => '9',
            ),
            39 => 
            array (
                'id' => 64,
                'area' => 'Manchester',
                'postcode' => 'M',
                'zone' => '4',
            ),
            40 => 
            array (
                'id' => 65,
                'area' => 'Rochester',
                'postcode' => 'ME',
                'zone' => '9',
            ),
            41 => 
            array (
                'id' => 66,
                'area' => 'Milton Keynes',
                'postcode' => 'MK',
                'zone' => '9',
            ),
            42 => 
            array (
                'id' => 67,
                'area' => 'Motherwell',
                'postcode' => 'ML',
                'zone' => '1',
            ),
            43 => 
            array (
                'id' => 68,
                'area' => 'North London',
                'postcode' => 'N',
                'zone' => '10',
            ),
            44 => 
            array (
                'id' => 69,
                'area' => 'Newcastle upon Tyne',
                'postcode' => 'NE',
                'zone' => '3',
            ),
            45 => 
            array (
                'id' => 70,
                'area' => 'Nottingham',
                'postcode' => 'NG',
                'zone' => '5',
            ),
            46 => 
            array (
                'id' => 71,
                'area' => 'Northampton',
                'postcode' => 'NN',
                'zone' => '6',
            ),
            47 => 
            array (
                'id' => 72,
                'area' => 'Newport',
                'postcode' => 'NP',
                'zone' => '7',
            ),
            48 => 
            array (
                'id' => 73,
                'area' => 'Norwich',
                'postcode' => 'NR',
                'zone' => '5',
            ),
            49 => 
            array (
                'id' => 74,
                'area' => 'North West London',
                'postcode' => 'NW',
                'zone' => '10',
            ),
            50 => 
            array (
                'id' => 75,
                'area' => 'Oldham',
                'postcode' => 'OL',
                'zone' => '4',
            ),
            51 => 
            array (
                'id' => 76,
                'area' => 'Oxford',
                'postcode' => 'OX',
                'zone' => '9',
            ),
            52 => 
            array (
                'id' => 77,
                'area' => 'Paisley',
                'postcode' => 'PA',
                'zone' => '1',
            ),
            53 => 
            array (
                'id' => 78,
                'area' => 'Peterborough',
                'postcode' => 'PE',
                'zone' => '5',
            ),
            54 => 
            array (
                'id' => 79,
                'area' => 'Perth',
                'postcode' => 'PH',
                'zone' => '1',
            ),
            55 => 
            array (
                'id' => 80,
                'area' => 'Plymouth',
                'postcode' => 'PL',
                'zone' => '8',
            ),
            56 => 
            array (
                'id' => 81,
                'area' => 'Portsmouth',
                'postcode' => 'PO',
                'zone' => '9',
            ),
            57 => 
            array (
                'id' => 82,
                'area' => 'Preston',
                'postcode' => 'PR',
                'zone' => '4',
            ),
            58 => 
            array (
                'id' => 83,
                'area' => 'Reading',
                'postcode' => 'RG',
                'zone' => '9',
            ),
            59 => 
            array (
                'id' => 84,
                'area' => 'Redhill',
                'postcode' => 'RH',
                'zone' => '9',
            ),
            60 => 
            array (
                'id' => 85,
                'area' => 'Romford',
                'postcode' => 'RM',
                'zone' => '10',
            ),
            61 => 
            array (
                'id' => 86,
                'area' => 'Sheffield',
                'postcode' => 'S',
                'zone' => '5',
            ),
            62 => 
            array (
                'id' => 87,
                'area' => 'Swansea',
                'postcode' => 'SA',
                'zone' => '7',
            ),
            63 => 
            array (
                'id' => 88,
                'area' => 'South East London',
                'postcode' => 'SE',
                'zone' => '10',
            ),
            64 => 
            array (
                'id' => 89,
                'area' => 'Stevenage',
                'postcode' => 'SG',
                'zone' => '9',
            ),
            65 => 
            array (
                'id' => 90,
                'area' => 'Stockport',
                'postcode' => 'SK',
                'zone' => '4',
            ),
            66 => 
            array (
                'id' => 91,
                'area' => 'Slough',
                'postcode' => 'SL',
                'zone' => '9',
            ),
            67 => 
            array (
                'id' => 92,
                'area' => 'Sutton',
                'postcode' => 'SM',
                'zone' => '10',
            ),
            68 => 
            array (
                'id' => 93,
                'area' => 'Swindon',
                'postcode' => 'SN',
                'zone' => '8',
            ),
            69 => 
            array (
                'id' => 94,
                'area' => 'Southampton',
                'postcode' => 'SO',
                'zone' => '9',
            ),
            70 => 
            array (
                'id' => 95,
                'area' => 'Salisbury',
                'postcode' => 'SP',
                'zone' => '8',
            ),
            71 => 
            array (
                'id' => 96,
                'area' => 'Sunderland',
                'postcode' => 'SR',
                'zone' => '3',
            ),
            72 => 
            array (
                'id' => 97,
                'area' => 'Southend-on-Sea',
                'postcode' => 'SS',
                'zone' => '5',
            ),
            73 => 
            array (
                'id' => 98,
                'area' => 'Stoke-on-Trent',
                'postcode' => 'ST',
                'zone' => '6',
            ),
            74 => 
            array (
                'id' => 99,
                'area' => 'South West London',
                'postcode' => 'SW',
                'zone' => '10',
            ),
            75 => 
            array (
                'id' => 100,
                'area' => 'Shrewsbury',
                'postcode' => 'SY',
                'zone' => '7',
            ),
            76 => 
            array (
                'id' => 101,
                'area' => 'Taunton',
                'postcode' => 'TA',
                'zone' => '8',
            ),
            77 => 
            array (
                'id' => 102,
                'area' => 'Galashiels',
                'postcode' => 'TD',
                'zone' => '1',
            ),
            78 => 
            array (
                'id' => 103,
                'area' => 'Telford',
                'postcode' => 'TF',
                'zone' => '6',
            ),
            79 => 
            array (
                'id' => 104,
                'area' => 'Tonbridge',
                'postcode' => 'TN',
                'zone' => '9',
            ),
            80 => 
            array (
                'id' => 105,
                'area' => 'Torquay',
                'postcode' => 'TQ',
                'zone' => '8',
            ),
            81 => 
            array (
                'id' => 106,
                'area' => 'Truro',
                'postcode' => 'TR',
                'zone' => '8',
            ),
            82 => 
            array (
                'id' => 107,
                'area' => 'Cleveland',
                'postcode' => 'TS',
                'zone' => '3',
            ),
            83 => 
            array (
                'id' => 108,
                'area' => 'Twickenham',
                'postcode' => 'TW',
                'zone' => '10',
            ),
            84 => 
            array (
                'id' => 109,
                'area' => 'Southall',
                'postcode' => 'UB',
                'zone' => '10',
            ),
            85 => 
            array (
                'id' => 110,
                'area' => 'West London',
                'postcode' => 'W',
                'zone' => '10',
            ),
            86 => 
            array (
                'id' => 111,
                'area' => 'Warrington',
                'postcode' => 'WA',
                'zone' => '4',
            ),
            87 => 
            array (
                'id' => 112,
                'area' => 'Central London',
                'postcode' => 'WC',
                'zone' => '10',
            ),
            88 => 
            array (
                'id' => 113,
                'area' => 'Watford',
                'postcode' => 'WD',
                'zone' => '10',
            ),
            89 => 
            array (
                'id' => 114,
                'area' => 'Wakefield',
                'postcode' => 'WF',
                'zone' => '3',
            ),
            90 => 
            array (
                'id' => 115,
                'area' => 'Wigan',
                'postcode' => 'WN',
                'zone' => '4',
            ),
            91 => 
            array (
                'id' => 116,
                'area' => 'Worcester',
                'postcode' => 'WR',
                'zone' => '6',
            ),
            92 => 
            array (
                'id' => 117,
                'area' => 'Walsall',
                'postcode' => 'WS',
                'zone' => '6',
            ),
            93 => 
            array (
                'id' => 118,
                'area' => 'Wolverhampton',
                'postcode' => 'WV',
                'zone' => '6',
            ),
            94 => 
            array (
                'id' => 119,
                'area' => 'York',
                'postcode' => 'YO',
                'zone' => '3',
            ),
        ));
        
        
    }
}