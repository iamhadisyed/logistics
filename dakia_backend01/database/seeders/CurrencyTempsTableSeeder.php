<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrencyTempsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('currency_temps')->delete();
        
        \DB::table('currency_temps')->insert(array (
            0 => 
            array (
                'country' => 'Albania',
                'currency' => 'Leke',
                'code' => 'ALL',
                'symbol' => 'Lek',
            ),
            1 => 
            array (
                'country' => 'America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            2 => 
            array (
                'country' => 'Argentina',
                'currency' => 'Pesos',
                'code' => 'ARS',
                'symbol' => '$',
            ),
            3 => 
            array (
                'country' => 'Aruba',
                'currency' => 'Guilders',
                'code' => 'AWG',
                'symbol' => 'ƒ',
            ),
            4 => 
            array (
                'country' => 'Australia',
                'currency' => 'Dollars',
                'code' => 'AUD',
                'symbol' => '$',
            ),
            5 => 
            array (
                'country' => 'Bahamas',
                'currency' => 'Dollars',
                'code' => 'BSD',
                'symbol' => '$',
            ),
            6 => 
            array (
                'country' => 'Barbados',
                'currency' => 'Dollars',
                'code' => 'BBD',
                'symbol' => '$',
            ),
            7 => 
            array (
                'country' => 'Belarus',
                'currency' => 'Rubles',
                'code' => 'BYR',
                'symbol' => 'p.',
            ),
            8 => 
            array (
                'country' => 'Belgium',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            9 => 
            array (
                'country' => 'Beliz',
                'currency' => 'Dollars',
                'code' => 'BZD',
                'symbol' => 'BZ$',
            ),
            10 => 
            array (
                'country' => 'Bermuda',
                'currency' => 'Dollars',
                'code' => 'BMD',
                'symbol' => '$',
            ),
            11 => 
            array (
                'country' => 'Bolivia',
                'currency' => 'Bolivianos',
                'code' => 'BOB',
                'symbol' => '$b',
            ),
            12 => 
            array (
                'country' => 'Bosnia and Herzegovina',
                'currency' => 'Convertible Marka',
                'code' => 'BAM',
                'symbol' => 'KM',
            ),
            13 => 
            array (
                'country' => 'Botswana',
                'currency' => 'Pula',
                'code' => 'BWP',
                'symbol' => 'P',
            ),
            14 => 
            array (
                'country' => 'Brazil',
                'currency' => 'Reais',
                'code' => 'BRL',
                'symbol' => 'R$',
            ),
            15 => 
            array (
            'country' => 'Britain (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            16 => 
            array (
                'country' => 'Brunei Darussalam',
                'currency' => 'Dollars',
                'code' => 'BND',
                'symbol' => '$',
            ),
            17 => 
            array (
                'country' => 'Canada',
                'currency' => 'Dollars',
                'code' => 'CAD',
                'symbol' => '$',
            ),
            18 => 
            array (
                'country' => 'Cayman Islands',
                'currency' => 'Dollars',
                'code' => 'KYD',
                'symbol' => '$',
            ),
            19 => 
            array (
                'country' => 'Chile',
                'currency' => 'Pesos',
                'code' => 'CLP',
                'symbol' => '$',
            ),
            20 => 
            array (
                'country' => 'China',
                'currency' => 'Yuan Renminbi',
                'code' => 'CNY',
                'symbol' => '¥',
            ),
            21 => 
            array (
                'country' => 'Colombia',
                'currency' => 'Pesos',
                'code' => 'COP',
                'symbol' => '$',
            ),
            22 => 
            array (
                'country' => 'Croatia',
                'currency' => 'Kuna',
                'code' => 'HRK',
                'symbol' => 'kn',
            ),
            23 => 
            array (
                'country' => 'Cyprus',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            24 => 
            array (
                'country' => 'Denmark',
                'currency' => 'Kroner',
                'code' => 'DKK',
                'symbol' => 'kr',
            ),
            25 => 
            array (
                'country' => 'Dominican Republic',
                'currency' => 'Pesos',
                'code' => 'DOP ',
                'symbol' => 'RD$',
            ),
            26 => 
            array (
                'country' => 'East Caribbean',
                'currency' => 'Dollars',
                'code' => 'XCD',
                'symbol' => '$',
            ),
            27 => 
            array (
                'country' => 'Egypt',
                'currency' => 'Pounds',
                'code' => 'EGP',
                'symbol' => '£',
            ),
            28 => 
            array (
                'country' => 'El Salvador',
                'currency' => 'Colones',
                'code' => 'SVC',
                'symbol' => '$',
            ),
            29 => 
            array (
            'country' => 'England (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            30 => 
            array (
                'country' => 'Euro',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            31 => 
            array (
                'country' => 'Falkland Islands',
                'currency' => 'Pounds',
                'code' => 'FKP',
                'symbol' => '£',
            ),
            32 => 
            array (
                'country' => 'Fiji',
                'currency' => 'Dollars',
                'code' => 'FJD',
                'symbol' => '$',
            ),
            33 => 
            array (
                'country' => 'France',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            34 => 
            array (
                'country' => 'Ghana',
                'currency' => 'Cedis',
                'code' => 'GHC',
                'symbol' => '¢',
            ),
            35 => 
            array (
                'country' => 'Gibraltar',
                'currency' => 'Pounds',
                'code' => 'GIP',
                'symbol' => '£',
            ),
            36 => 
            array (
                'country' => 'Greece',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            37 => 
            array (
                'country' => 'Guatemala',
                'currency' => 'Quetzales',
                'code' => 'GTQ',
                'symbol' => 'Q',
            ),
            38 => 
            array (
                'country' => 'Guernsey',
                'currency' => 'Pounds',
                'code' => 'GGP',
                'symbol' => '£',
            ),
            39 => 
            array (
                'country' => 'Guyana',
                'currency' => 'Dollars',
                'code' => 'GYD',
                'symbol' => '$',
            ),
            40 => 
            array (
            'country' => 'Holland (Netherlands)',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            41 => 
            array (
                'country' => 'Honduras',
                'currency' => 'Lempiras',
                'code' => 'HNL',
                'symbol' => 'L',
            ),
            42 => 
            array (
                'country' => 'Hong Kong',
                'currency' => 'Dollars',
                'code' => 'HKD',
                'symbol' => '$',
            ),
            43 => 
            array (
                'country' => 'Hungary',
                'currency' => 'Forint',
                'code' => 'HUF',
                'symbol' => 'Ft',
            ),
            44 => 
            array (
                'country' => 'Iceland',
                'currency' => 'Kronur',
                'code' => 'ISK',
                'symbol' => 'kr',
            ),
            45 => 
            array (
                'country' => 'India',
                'currency' => 'Rupees',
                'code' => 'INR',
                'symbol' => 'Rp',
            ),
            46 => 
            array (
                'country' => 'Indonesia',
                'currency' => 'Rupiahs',
                'code' => 'IDR',
                'symbol' => 'Rp',
            ),
            47 => 
            array (
                'country' => 'Ireland',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            48 => 
            array (
                'country' => 'Isle of Man',
                'currency' => 'Pounds',
                'code' => 'IMP',
                'symbol' => '£',
            ),
            49 => 
            array (
                'country' => 'Italy',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            50 => 
            array (
                'country' => 'Jamaica',
                'currency' => 'Dollars',
                'code' => 'JMD',
                'symbol' => 'J$',
            ),
            51 => 
            array (
                'country' => 'Japan',
                'currency' => 'Yen',
                'code' => 'JPY',
                'symbol' => '¥',
            ),
            52 => 
            array (
                'country' => 'Jersey',
                'currency' => 'Pounds',
                'code' => 'JEP',
                'symbol' => '£',
            ),
            53 => 
            array (
                'country' => 'Latvia',
                'currency' => 'Lati',
                'code' => 'LVL',
                'symbol' => 'Ls',
            ),
            54 => 
            array (
                'country' => 'Lebanon',
                'currency' => 'Pounds',
                'code' => 'LBP',
                'symbol' => '£',
            ),
            55 => 
            array (
                'country' => 'Liberia',
                'currency' => 'Dollars',
                'code' => 'LRD',
                'symbol' => '$',
            ),
            56 => 
            array (
                'country' => 'Liechtenstein',
                'currency' => 'Switzerland Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            57 => 
            array (
                'country' => 'Lithuania',
                'currency' => 'Litai',
                'code' => 'LTL',
                'symbol' => 'Lt',
            ),
            58 => 
            array (
                'country' => 'Luxembourg',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            59 => 
            array (
                'country' => 'Malaysia',
                'currency' => 'Ringgits',
                'code' => 'MYR',
                'symbol' => 'RM',
            ),
            60 => 
            array (
                'country' => 'Malta',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            61 => 
            array (
                'country' => 'Mexico',
                'currency' => 'Pesos',
                'code' => 'MXN',
                'symbol' => '$',
            ),
            62 => 
            array (
                'country' => 'Mozambique',
                'currency' => 'Meticais',
                'code' => 'MZN',
                'symbol' => 'MT',
            ),
            63 => 
            array (
                'country' => 'Namibia',
                'currency' => 'Dollars',
                'code' => 'NAD',
                'symbol' => '$',
            ),
            64 => 
            array (
                'country' => 'Netherlands Antilles',
                'currency' => 'Guilders',
                'code' => 'ANG',
                'symbol' => 'ƒ',
            ),
            65 => 
            array (
                'country' => 'Netherlands',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            66 => 
            array (
                'country' => 'New Zealand',
                'currency' => 'Dollars',
                'code' => 'NZD',
                'symbol' => '$',
            ),
            67 => 
            array (
                'country' => 'Nicaragua',
                'currency' => 'Cordobas',
                'code' => 'NIO',
                'symbol' => 'C$',
            ),
            68 => 
            array (
                'country' => 'Norway',
                'currency' => 'Krone',
                'code' => 'NOK',
                'symbol' => 'kr',
            ),
            69 => 
            array (
                'country' => 'Panama',
                'currency' => 'Balboa',
                'code' => 'PAB',
                'symbol' => 'B/.',
            ),
            70 => 
            array (
                'country' => 'Paraguay',
                'currency' => 'Guarani',
                'code' => 'PYG',
                'symbol' => 'Gs',
            ),
            71 => 
            array (
                'country' => 'Peru',
                'currency' => 'Nuevos Soles',
                'code' => 'PEN',
                'symbol' => 'S/.',
            ),
            72 => 
            array (
                'country' => 'Philippines',
                'currency' => 'Pesos',
                'code' => 'PHP',
                'symbol' => 'Php',
            ),
            73 => 
            array (
                'country' => 'Romania',
                'currency' => 'New Lei',
                'code' => 'RON',
                'symbol' => 'lei',
            ),
            74 => 
            array (
                'country' => 'Saint Helena',
                'currency' => 'Pounds',
                'code' => 'SHP',
                'symbol' => '£',
            ),
            75 => 
            array (
                'country' => 'Singapore',
                'currency' => 'Dollars',
                'code' => 'SGD',
                'symbol' => '$',
            ),
            76 => 
            array (
                'country' => 'Slovenia',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            77 => 
            array (
                'country' => 'Solomon Islands',
                'currency' => 'Dollars',
                'code' => 'SBD',
                'symbol' => '$',
            ),
            78 => 
            array (
                'country' => 'Somalia',
                'currency' => 'Shillings',
                'code' => 'SOS',
                'symbol' => 'S',
            ),
            79 => 
            array (
                'country' => 'South Africa',
                'currency' => 'Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
            ),
            80 => 
            array (
                'country' => 'Spain',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            81 => 
            array (
                'country' => 'Sweden',
                'currency' => 'Kronor',
                'code' => 'SEK',
                'symbol' => 'kr',
            ),
            82 => 
            array (
                'country' => 'Switzerland',
                'currency' => 'Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            83 => 
            array (
                'country' => 'Suriname',
                'currency' => 'Dollars',
                'code' => 'SRD',
                'symbol' => '$',
            ),
            84 => 
            array (
                'country' => 'Syria',
                'currency' => 'Pounds',
                'code' => 'SYP',
                'symbol' => '£',
            ),
            85 => 
            array (
                'country' => 'Taiwan',
                'currency' => 'New Dollars',
                'code' => 'TWD',
                'symbol' => 'NT$',
            ),
            86 => 
            array (
                'country' => 'Trinidad and Tobago',
                'currency' => 'Dollars',
                'code' => 'TTD',
                'symbol' => 'TT$',
            ),
            87 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Lira',
                'code' => 'TRY',
                'symbol' => 'TL',
            ),
            88 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Liras',
                'code' => 'TRL',
                'symbol' => '£',
            ),
            89 => 
            array (
                'country' => 'Tuvalu',
                'currency' => 'Dollars',
                'code' => 'TVD',
                'symbol' => '$',
            ),
            90 => 
            array (
                'country' => 'United Kingdom',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            91 => 
            array (
                'country' => 'United States of America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            92 => 
            array (
                'country' => 'Uruguay',
                'currency' => 'Pesos',
                'code' => 'UYU',
                'symbol' => '$U',
            ),
            93 => 
            array (
                'country' => 'Vatican City',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            94 => 
            array (
                'country' => 'Venezuela',
                'currency' => 'Bolivares Fuertes',
                'code' => 'VEF',
                'symbol' => 'Bs',
            ),
            95 => 
            array (
                'country' => 'Zimbabwe',
                'currency' => 'Zimbabwe Dollars',
                'code' => 'ZWD',
                'symbol' => 'Z$',
            ),
            96 => 
            array (
                'country' => 'Albania',
                'currency' => 'Leke',
                'code' => 'ALL',
                'symbol' => 'Lek',
            ),
            97 => 
            array (
                'country' => 'America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            98 => 
            array (
                'country' => 'Argentina',
                'currency' => 'Pesos',
                'code' => 'ARS',
                'symbol' => '$',
            ),
            99 => 
            array (
                'country' => 'Aruba',
                'currency' => 'Guilders',
                'code' => 'AWG',
                'symbol' => 'ƒ',
            ),
            100 => 
            array (
                'country' => 'Australia',
                'currency' => 'Dollars',
                'code' => 'AUD',
                'symbol' => '$',
            ),
            101 => 
            array (
                'country' => 'Bahamas',
                'currency' => 'Dollars',
                'code' => 'BSD',
                'symbol' => '$',
            ),
            102 => 
            array (
                'country' => 'Barbados',
                'currency' => 'Dollars',
                'code' => 'BBD',
                'symbol' => '$',
            ),
            103 => 
            array (
                'country' => 'Belarus',
                'currency' => 'Rubles',
                'code' => 'BYR',
                'symbol' => 'p.',
            ),
            104 => 
            array (
                'country' => 'Belgium',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            105 => 
            array (
                'country' => 'Beliz',
                'currency' => 'Dollars',
                'code' => 'BZD',
                'symbol' => 'BZ$',
            ),
            106 => 
            array (
                'country' => 'Bermuda',
                'currency' => 'Dollars',
                'code' => 'BMD',
                'symbol' => '$',
            ),
            107 => 
            array (
                'country' => 'Bolivia',
                'currency' => 'Bolivianos',
                'code' => 'BOB',
                'symbol' => '$b',
            ),
            108 => 
            array (
                'country' => 'Bosnia and Herzegovina',
                'currency' => 'Convertible Marka',
                'code' => 'BAM',
                'symbol' => 'KM',
            ),
            109 => 
            array (
                'country' => 'Botswana',
                'currency' => 'Pula',
                'code' => 'BWP',
                'symbol' => 'P',
            ),
            110 => 
            array (
                'country' => 'Brazil',
                'currency' => 'Reais',
                'code' => 'BRL',
                'symbol' => 'R$',
            ),
            111 => 
            array (
            'country' => 'Britain (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            112 => 
            array (
                'country' => 'Brunei Darussalam',
                'currency' => 'Dollars',
                'code' => 'BND',
                'symbol' => '$',
            ),
            113 => 
            array (
                'country' => 'Canada',
                'currency' => 'Dollars',
                'code' => 'CAD',
                'symbol' => '$',
            ),
            114 => 
            array (
                'country' => 'Cayman Islands',
                'currency' => 'Dollars',
                'code' => 'KYD',
                'symbol' => '$',
            ),
            115 => 
            array (
                'country' => 'Chile',
                'currency' => 'Pesos',
                'code' => 'CLP',
                'symbol' => '$',
            ),
            116 => 
            array (
                'country' => 'China',
                'currency' => 'Yuan Renminbi',
                'code' => 'CNY',
                'symbol' => '¥',
            ),
            117 => 
            array (
                'country' => 'Colombia',
                'currency' => 'Pesos',
                'code' => 'COP',
                'symbol' => '$',
            ),
            118 => 
            array (
                'country' => 'Croatia',
                'currency' => 'Kuna',
                'code' => 'HRK',
                'symbol' => 'kn',
            ),
            119 => 
            array (
                'country' => 'Cyprus',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            120 => 
            array (
                'country' => 'Denmark',
                'currency' => 'Kroner',
                'code' => 'DKK',
                'symbol' => 'kr',
            ),
            121 => 
            array (
                'country' => 'Dominican Republic',
                'currency' => 'Pesos',
                'code' => 'DOP ',
                'symbol' => 'RD$',
            ),
            122 => 
            array (
                'country' => 'East Caribbean',
                'currency' => 'Dollars',
                'code' => 'XCD',
                'symbol' => '$',
            ),
            123 => 
            array (
                'country' => 'Egypt',
                'currency' => 'Pounds',
                'code' => 'EGP',
                'symbol' => '£',
            ),
            124 => 
            array (
                'country' => 'El Salvador',
                'currency' => 'Colones',
                'code' => 'SVC',
                'symbol' => '$',
            ),
            125 => 
            array (
            'country' => 'England (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            126 => 
            array (
                'country' => 'Euro',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            127 => 
            array (
                'country' => 'Falkland Islands',
                'currency' => 'Pounds',
                'code' => 'FKP',
                'symbol' => '£',
            ),
            128 => 
            array (
                'country' => 'Fiji',
                'currency' => 'Dollars',
                'code' => 'FJD',
                'symbol' => '$',
            ),
            129 => 
            array (
                'country' => 'France',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            130 => 
            array (
                'country' => 'Ghana',
                'currency' => 'Cedis',
                'code' => 'GHC',
                'symbol' => '¢',
            ),
            131 => 
            array (
                'country' => 'Gibraltar',
                'currency' => 'Pounds',
                'code' => 'GIP',
                'symbol' => '£',
            ),
            132 => 
            array (
                'country' => 'Greece',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            133 => 
            array (
                'country' => 'Guatemala',
                'currency' => 'Quetzales',
                'code' => 'GTQ',
                'symbol' => 'Q',
            ),
            134 => 
            array (
                'country' => 'Guernsey',
                'currency' => 'Pounds',
                'code' => 'GGP',
                'symbol' => '£',
            ),
            135 => 
            array (
                'country' => 'Guyana',
                'currency' => 'Dollars',
                'code' => 'GYD',
                'symbol' => '$',
            ),
            136 => 
            array (
            'country' => 'Holland (Netherlands)',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            137 => 
            array (
                'country' => 'Honduras',
                'currency' => 'Lempiras',
                'code' => 'HNL',
                'symbol' => 'L',
            ),
            138 => 
            array (
                'country' => 'Hong Kong',
                'currency' => 'Dollars',
                'code' => 'HKD',
                'symbol' => '$',
            ),
            139 => 
            array (
                'country' => 'Hungary',
                'currency' => 'Forint',
                'code' => 'HUF',
                'symbol' => 'Ft',
            ),
            140 => 
            array (
                'country' => 'Iceland',
                'currency' => 'Kronur',
                'code' => 'ISK',
                'symbol' => 'kr',
            ),
            141 => 
            array (
                'country' => 'India',
                'currency' => 'Rupees',
                'code' => 'INR',
                'symbol' => 'Rp',
            ),
            142 => 
            array (
                'country' => 'Indonesia',
                'currency' => 'Rupiahs',
                'code' => 'IDR',
                'symbol' => 'Rp',
            ),
            143 => 
            array (
                'country' => 'Ireland',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            144 => 
            array (
                'country' => 'Isle of Man',
                'currency' => 'Pounds',
                'code' => 'IMP',
                'symbol' => '£',
            ),
            145 => 
            array (
                'country' => 'Italy',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            146 => 
            array (
                'country' => 'Jamaica',
                'currency' => 'Dollars',
                'code' => 'JMD',
                'symbol' => 'J$',
            ),
            147 => 
            array (
                'country' => 'Japan',
                'currency' => 'Yen',
                'code' => 'JPY',
                'symbol' => '¥',
            ),
            148 => 
            array (
                'country' => 'Jersey',
                'currency' => 'Pounds',
                'code' => 'JEP',
                'symbol' => '£',
            ),
            149 => 
            array (
                'country' => 'Latvia',
                'currency' => 'Lati',
                'code' => 'LVL',
                'symbol' => 'Ls',
            ),
            150 => 
            array (
                'country' => 'Lebanon',
                'currency' => 'Pounds',
                'code' => 'LBP',
                'symbol' => '£',
            ),
            151 => 
            array (
                'country' => 'Liberia',
                'currency' => 'Dollars',
                'code' => 'LRD',
                'symbol' => '$',
            ),
            152 => 
            array (
                'country' => 'Liechtenstein',
                'currency' => 'Switzerland Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            153 => 
            array (
                'country' => 'Lithuania',
                'currency' => 'Litai',
                'code' => 'LTL',
                'symbol' => 'Lt',
            ),
            154 => 
            array (
                'country' => 'Luxembourg',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            155 => 
            array (
                'country' => 'Malaysia',
                'currency' => 'Ringgits',
                'code' => 'MYR',
                'symbol' => 'RM',
            ),
            156 => 
            array (
                'country' => 'Malta',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            157 => 
            array (
                'country' => 'Mexico',
                'currency' => 'Pesos',
                'code' => 'MXN',
                'symbol' => '$',
            ),
            158 => 
            array (
                'country' => 'Mozambique',
                'currency' => 'Meticais',
                'code' => 'MZN',
                'symbol' => 'MT',
            ),
            159 => 
            array (
                'country' => 'Namibia',
                'currency' => 'Dollars',
                'code' => 'NAD',
                'symbol' => '$',
            ),
            160 => 
            array (
                'country' => 'Netherlands Antilles',
                'currency' => 'Guilders',
                'code' => 'ANG',
                'symbol' => 'ƒ',
            ),
            161 => 
            array (
                'country' => 'Netherlands',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            162 => 
            array (
                'country' => 'New Zealand',
                'currency' => 'Dollars',
                'code' => 'NZD',
                'symbol' => '$',
            ),
            163 => 
            array (
                'country' => 'Nicaragua',
                'currency' => 'Cordobas',
                'code' => 'NIO',
                'symbol' => 'C$',
            ),
            164 => 
            array (
                'country' => 'Norway',
                'currency' => 'Krone',
                'code' => 'NOK',
                'symbol' => 'kr',
            ),
            165 => 
            array (
                'country' => 'Panama',
                'currency' => 'Balboa',
                'code' => 'PAB',
                'symbol' => 'B/.',
            ),
            166 => 
            array (
                'country' => 'Paraguay',
                'currency' => 'Guarani',
                'code' => 'PYG',
                'symbol' => 'Gs',
            ),
            167 => 
            array (
                'country' => 'Peru',
                'currency' => 'Nuevos Soles',
                'code' => 'PEN',
                'symbol' => 'S/.',
            ),
            168 => 
            array (
                'country' => 'Philippines',
                'currency' => 'Pesos',
                'code' => 'PHP',
                'symbol' => 'Php',
            ),
            169 => 
            array (
                'country' => 'Romania',
                'currency' => 'New Lei',
                'code' => 'RON',
                'symbol' => 'lei',
            ),
            170 => 
            array (
                'country' => 'Saint Helena',
                'currency' => 'Pounds',
                'code' => 'SHP',
                'symbol' => '£',
            ),
            171 => 
            array (
                'country' => 'Singapore',
                'currency' => 'Dollars',
                'code' => 'SGD',
                'symbol' => '$',
            ),
            172 => 
            array (
                'country' => 'Slovenia',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            173 => 
            array (
                'country' => 'Solomon Islands',
                'currency' => 'Dollars',
                'code' => 'SBD',
                'symbol' => '$',
            ),
            174 => 
            array (
                'country' => 'Somalia',
                'currency' => 'Shillings',
                'code' => 'SOS',
                'symbol' => 'S',
            ),
            175 => 
            array (
                'country' => 'South Africa',
                'currency' => 'Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
            ),
            176 => 
            array (
                'country' => 'Spain',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            177 => 
            array (
                'country' => 'Sweden',
                'currency' => 'Kronor',
                'code' => 'SEK',
                'symbol' => 'kr',
            ),
            178 => 
            array (
                'country' => 'Switzerland',
                'currency' => 'Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            179 => 
            array (
                'country' => 'Suriname',
                'currency' => 'Dollars',
                'code' => 'SRD',
                'symbol' => '$',
            ),
            180 => 
            array (
                'country' => 'Syria',
                'currency' => 'Pounds',
                'code' => 'SYP',
                'symbol' => '£',
            ),
            181 => 
            array (
                'country' => 'Taiwan',
                'currency' => 'New Dollars',
                'code' => 'TWD',
                'symbol' => 'NT$',
            ),
            182 => 
            array (
                'country' => 'Trinidad and Tobago',
                'currency' => 'Dollars',
                'code' => 'TTD',
                'symbol' => 'TT$',
            ),
            183 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Lira',
                'code' => 'TRY',
                'symbol' => 'TL',
            ),
            184 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Liras',
                'code' => 'TRL',
                'symbol' => '£',
            ),
            185 => 
            array (
                'country' => 'Tuvalu',
                'currency' => 'Dollars',
                'code' => 'TVD',
                'symbol' => '$',
            ),
            186 => 
            array (
                'country' => 'United Kingdom',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            187 => 
            array (
                'country' => 'United States of America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            188 => 
            array (
                'country' => 'Uruguay',
                'currency' => 'Pesos',
                'code' => 'UYU',
                'symbol' => '$U',
            ),
            189 => 
            array (
                'country' => 'Vatican City',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            190 => 
            array (
                'country' => 'Venezuela',
                'currency' => 'Bolivares Fuertes',
                'code' => 'VEF',
                'symbol' => 'Bs',
            ),
            191 => 
            array (
                'country' => 'Zimbabwe',
                'currency' => 'Zimbabwe Dollars',
                'code' => 'ZWD',
                'symbol' => 'Z$',
            ),
            192 => 
            array (
                'country' => 'Albania',
                'currency' => 'Leke',
                'code' => 'ALL',
                'symbol' => 'Lek',
            ),
            193 => 
            array (
                'country' => 'America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            194 => 
            array (
                'country' => 'Argentina',
                'currency' => 'Pesos',
                'code' => 'ARS',
                'symbol' => '$',
            ),
            195 => 
            array (
                'country' => 'Aruba',
                'currency' => 'Guilders',
                'code' => 'AWG',
                'symbol' => 'ƒ',
            ),
            196 => 
            array (
                'country' => 'Australia',
                'currency' => 'Dollars',
                'code' => 'AUD',
                'symbol' => '$',
            ),
            197 => 
            array (
                'country' => 'Bahamas',
                'currency' => 'Dollars',
                'code' => 'BSD',
                'symbol' => '$',
            ),
            198 => 
            array (
                'country' => 'Barbados',
                'currency' => 'Dollars',
                'code' => 'BBD',
                'symbol' => '$',
            ),
            199 => 
            array (
                'country' => 'Belarus',
                'currency' => 'Rubles',
                'code' => 'BYR',
                'symbol' => 'p.',
            ),
            200 => 
            array (
                'country' => 'Belgium',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            201 => 
            array (
                'country' => 'Beliz',
                'currency' => 'Dollars',
                'code' => 'BZD',
                'symbol' => 'BZ$',
            ),
            202 => 
            array (
                'country' => 'Bermuda',
                'currency' => 'Dollars',
                'code' => 'BMD',
                'symbol' => '$',
            ),
            203 => 
            array (
                'country' => 'Bolivia',
                'currency' => 'Bolivianos',
                'code' => 'BOB',
                'symbol' => '$b',
            ),
            204 => 
            array (
                'country' => 'Bosnia and Herzegovina',
                'currency' => 'Convertible Marka',
                'code' => 'BAM',
                'symbol' => 'KM',
            ),
            205 => 
            array (
                'country' => 'Botswana',
                'currency' => 'Pula',
                'code' => 'BWP',
                'symbol' => 'P',
            ),
            206 => 
            array (
                'country' => 'Brazil',
                'currency' => 'Reais',
                'code' => 'BRL',
                'symbol' => 'R$',
            ),
            207 => 
            array (
            'country' => 'Britain (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            208 => 
            array (
                'country' => 'Brunei Darussalam',
                'currency' => 'Dollars',
                'code' => 'BND',
                'symbol' => '$',
            ),
            209 => 
            array (
                'country' => 'Canada',
                'currency' => 'Dollars',
                'code' => 'CAD',
                'symbol' => '$',
            ),
            210 => 
            array (
                'country' => 'Cayman Islands',
                'currency' => 'Dollars',
                'code' => 'KYD',
                'symbol' => '$',
            ),
            211 => 
            array (
                'country' => 'Chile',
                'currency' => 'Pesos',
                'code' => 'CLP',
                'symbol' => '$',
            ),
            212 => 
            array (
                'country' => 'China',
                'currency' => 'Yuan Renminbi',
                'code' => 'CNY',
                'symbol' => '¥',
            ),
            213 => 
            array (
                'country' => 'Colombia',
                'currency' => 'Pesos',
                'code' => 'COP',
                'symbol' => '$',
            ),
            214 => 
            array (
                'country' => 'Croatia',
                'currency' => 'Kuna',
                'code' => 'HRK',
                'symbol' => 'kn',
            ),
            215 => 
            array (
                'country' => 'Cyprus',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            216 => 
            array (
                'country' => 'Denmark',
                'currency' => 'Kroner',
                'code' => 'DKK',
                'symbol' => 'kr',
            ),
            217 => 
            array (
                'country' => 'Dominican Republic',
                'currency' => 'Pesos',
                'code' => 'DOP ',
                'symbol' => 'RD$',
            ),
            218 => 
            array (
                'country' => 'East Caribbean',
                'currency' => 'Dollars',
                'code' => 'XCD',
                'symbol' => '$',
            ),
            219 => 
            array (
                'country' => 'Egypt',
                'currency' => 'Pounds',
                'code' => 'EGP',
                'symbol' => '£',
            ),
            220 => 
            array (
                'country' => 'El Salvador',
                'currency' => 'Colones',
                'code' => 'SVC',
                'symbol' => '$',
            ),
            221 => 
            array (
            'country' => 'England (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            222 => 
            array (
                'country' => 'Euro',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            223 => 
            array (
                'country' => 'Falkland Islands',
                'currency' => 'Pounds',
                'code' => 'FKP',
                'symbol' => '£',
            ),
            224 => 
            array (
                'country' => 'Fiji',
                'currency' => 'Dollars',
                'code' => 'FJD',
                'symbol' => '$',
            ),
            225 => 
            array (
                'country' => 'France',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            226 => 
            array (
                'country' => 'Ghana',
                'currency' => 'Cedis',
                'code' => 'GHC',
                'symbol' => '¢',
            ),
            227 => 
            array (
                'country' => 'Gibraltar',
                'currency' => 'Pounds',
                'code' => 'GIP',
                'symbol' => '£',
            ),
            228 => 
            array (
                'country' => 'Greece',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            229 => 
            array (
                'country' => 'Guatemala',
                'currency' => 'Quetzales',
                'code' => 'GTQ',
                'symbol' => 'Q',
            ),
            230 => 
            array (
                'country' => 'Guernsey',
                'currency' => 'Pounds',
                'code' => 'GGP',
                'symbol' => '£',
            ),
            231 => 
            array (
                'country' => 'Guyana',
                'currency' => 'Dollars',
                'code' => 'GYD',
                'symbol' => '$',
            ),
            232 => 
            array (
            'country' => 'Holland (Netherlands)',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            233 => 
            array (
                'country' => 'Honduras',
                'currency' => 'Lempiras',
                'code' => 'HNL',
                'symbol' => 'L',
            ),
            234 => 
            array (
                'country' => 'Hong Kong',
                'currency' => 'Dollars',
                'code' => 'HKD',
                'symbol' => '$',
            ),
            235 => 
            array (
                'country' => 'Hungary',
                'currency' => 'Forint',
                'code' => 'HUF',
                'symbol' => 'Ft',
            ),
            236 => 
            array (
                'country' => 'Iceland',
                'currency' => 'Kronur',
                'code' => 'ISK',
                'symbol' => 'kr',
            ),
            237 => 
            array (
                'country' => 'India',
                'currency' => 'Rupees',
                'code' => 'INR',
                'symbol' => 'Rp',
            ),
            238 => 
            array (
                'country' => 'Indonesia',
                'currency' => 'Rupiahs',
                'code' => 'IDR',
                'symbol' => 'Rp',
            ),
            239 => 
            array (
                'country' => 'Ireland',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            240 => 
            array (
                'country' => 'Isle of Man',
                'currency' => 'Pounds',
                'code' => 'IMP',
                'symbol' => '£',
            ),
            241 => 
            array (
                'country' => 'Italy',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            242 => 
            array (
                'country' => 'Jamaica',
                'currency' => 'Dollars',
                'code' => 'JMD',
                'symbol' => 'J$',
            ),
            243 => 
            array (
                'country' => 'Japan',
                'currency' => 'Yen',
                'code' => 'JPY',
                'symbol' => '¥',
            ),
            244 => 
            array (
                'country' => 'Jersey',
                'currency' => 'Pounds',
                'code' => 'JEP',
                'symbol' => '£',
            ),
            245 => 
            array (
                'country' => 'Latvia',
                'currency' => 'Lati',
                'code' => 'LVL',
                'symbol' => 'Ls',
            ),
            246 => 
            array (
                'country' => 'Lebanon',
                'currency' => 'Pounds',
                'code' => 'LBP',
                'symbol' => '£',
            ),
            247 => 
            array (
                'country' => 'Liberia',
                'currency' => 'Dollars',
                'code' => 'LRD',
                'symbol' => '$',
            ),
            248 => 
            array (
                'country' => 'Liechtenstein',
                'currency' => 'Switzerland Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            249 => 
            array (
                'country' => 'Lithuania',
                'currency' => 'Litai',
                'code' => 'LTL',
                'symbol' => 'Lt',
            ),
            250 => 
            array (
                'country' => 'Luxembourg',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            251 => 
            array (
                'country' => 'Malaysia',
                'currency' => 'Ringgits',
                'code' => 'MYR',
                'symbol' => 'RM',
            ),
            252 => 
            array (
                'country' => 'Malta',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            253 => 
            array (
                'country' => 'Mexico',
                'currency' => 'Pesos',
                'code' => 'MXN',
                'symbol' => '$',
            ),
            254 => 
            array (
                'country' => 'Mozambique',
                'currency' => 'Meticais',
                'code' => 'MZN',
                'symbol' => 'MT',
            ),
            255 => 
            array (
                'country' => 'Namibia',
                'currency' => 'Dollars',
                'code' => 'NAD',
                'symbol' => '$',
            ),
            256 => 
            array (
                'country' => 'Netherlands Antilles',
                'currency' => 'Guilders',
                'code' => 'ANG',
                'symbol' => 'ƒ',
            ),
            257 => 
            array (
                'country' => 'Netherlands',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            258 => 
            array (
                'country' => 'New Zealand',
                'currency' => 'Dollars',
                'code' => 'NZD',
                'symbol' => '$',
            ),
            259 => 
            array (
                'country' => 'Nicaragua',
                'currency' => 'Cordobas',
                'code' => 'NIO',
                'symbol' => 'C$',
            ),
            260 => 
            array (
                'country' => 'Norway',
                'currency' => 'Krone',
                'code' => 'NOK',
                'symbol' => 'kr',
            ),
            261 => 
            array (
                'country' => 'Panama',
                'currency' => 'Balboa',
                'code' => 'PAB',
                'symbol' => 'B/.',
            ),
            262 => 
            array (
                'country' => 'Paraguay',
                'currency' => 'Guarani',
                'code' => 'PYG',
                'symbol' => 'Gs',
            ),
            263 => 
            array (
                'country' => 'Peru',
                'currency' => 'Nuevos Soles',
                'code' => 'PEN',
                'symbol' => 'S/.',
            ),
            264 => 
            array (
                'country' => 'Philippines',
                'currency' => 'Pesos',
                'code' => 'PHP',
                'symbol' => 'Php',
            ),
            265 => 
            array (
                'country' => 'Romania',
                'currency' => 'New Lei',
                'code' => 'RON',
                'symbol' => 'lei',
            ),
            266 => 
            array (
                'country' => 'Saint Helena',
                'currency' => 'Pounds',
                'code' => 'SHP',
                'symbol' => '£',
            ),
            267 => 
            array (
                'country' => 'Singapore',
                'currency' => 'Dollars',
                'code' => 'SGD',
                'symbol' => '$',
            ),
            268 => 
            array (
                'country' => 'Slovenia',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            269 => 
            array (
                'country' => 'Solomon Islands',
                'currency' => 'Dollars',
                'code' => 'SBD',
                'symbol' => '$',
            ),
            270 => 
            array (
                'country' => 'Somalia',
                'currency' => 'Shillings',
                'code' => 'SOS',
                'symbol' => 'S',
            ),
            271 => 
            array (
                'country' => 'South Africa',
                'currency' => 'Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
            ),
            272 => 
            array (
                'country' => 'Spain',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            273 => 
            array (
                'country' => 'Sweden',
                'currency' => 'Kronor',
                'code' => 'SEK',
                'symbol' => 'kr',
            ),
            274 => 
            array (
                'country' => 'Switzerland',
                'currency' => 'Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            275 => 
            array (
                'country' => 'Suriname',
                'currency' => 'Dollars',
                'code' => 'SRD',
                'symbol' => '$',
            ),
            276 => 
            array (
                'country' => 'Syria',
                'currency' => 'Pounds',
                'code' => 'SYP',
                'symbol' => '£',
            ),
            277 => 
            array (
                'country' => 'Taiwan',
                'currency' => 'New Dollars',
                'code' => 'TWD',
                'symbol' => 'NT$',
            ),
            278 => 
            array (
                'country' => 'Trinidad and Tobago',
                'currency' => 'Dollars',
                'code' => 'TTD',
                'symbol' => 'TT$',
            ),
            279 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Lira',
                'code' => 'TRY',
                'symbol' => 'TL',
            ),
            280 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Liras',
                'code' => 'TRL',
                'symbol' => '£',
            ),
            281 => 
            array (
                'country' => 'Tuvalu',
                'currency' => 'Dollars',
                'code' => 'TVD',
                'symbol' => '$',
            ),
            282 => 
            array (
                'country' => 'United Kingdom',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            283 => 
            array (
                'country' => 'United States of America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            284 => 
            array (
                'country' => 'Uruguay',
                'currency' => 'Pesos',
                'code' => 'UYU',
                'symbol' => '$U',
            ),
            285 => 
            array (
                'country' => 'Vatican City',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            286 => 
            array (
                'country' => 'Venezuela',
                'currency' => 'Bolivares Fuertes',
                'code' => 'VEF',
                'symbol' => 'Bs',
            ),
            287 => 
            array (
                'country' => 'Zimbabwe',
                'currency' => 'Zimbabwe Dollars',
                'code' => 'ZWD',
                'symbol' => 'Z$',
            ),
            288 => 
            array (
                'country' => 'Albania',
                'currency' => 'Leke',
                'code' => 'ALL',
                'symbol' => 'Lek',
            ),
            289 => 
            array (
                'country' => 'America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            290 => 
            array (
                'country' => 'Argentina',
                'currency' => 'Pesos',
                'code' => 'ARS',
                'symbol' => '$',
            ),
            291 => 
            array (
                'country' => 'Aruba',
                'currency' => 'Guilders',
                'code' => 'AWG',
                'symbol' => 'ƒ',
            ),
            292 => 
            array (
                'country' => 'Australia',
                'currency' => 'Dollars',
                'code' => 'AUD',
                'symbol' => '$',
            ),
            293 => 
            array (
                'country' => 'Bahamas',
                'currency' => 'Dollars',
                'code' => 'BSD',
                'symbol' => '$',
            ),
            294 => 
            array (
                'country' => 'Barbados',
                'currency' => 'Dollars',
                'code' => 'BBD',
                'symbol' => '$',
            ),
            295 => 
            array (
                'country' => 'Belarus',
                'currency' => 'Rubles',
                'code' => 'BYR',
                'symbol' => 'p.',
            ),
            296 => 
            array (
                'country' => 'Belgium',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            297 => 
            array (
                'country' => 'Beliz',
                'currency' => 'Dollars',
                'code' => 'BZD',
                'symbol' => 'BZ$',
            ),
            298 => 
            array (
                'country' => 'Bermuda',
                'currency' => 'Dollars',
                'code' => 'BMD',
                'symbol' => '$',
            ),
            299 => 
            array (
                'country' => 'Bolivia',
                'currency' => 'Bolivianos',
                'code' => 'BOB',
                'symbol' => '$b',
            ),
            300 => 
            array (
                'country' => 'Bosnia and Herzegovina',
                'currency' => 'Convertible Marka',
                'code' => 'BAM',
                'symbol' => 'KM',
            ),
            301 => 
            array (
                'country' => 'Botswana',
                'currency' => 'Pula',
                'code' => 'BWP',
                'symbol' => 'P',
            ),
            302 => 
            array (
                'country' => 'Brazil',
                'currency' => 'Reais',
                'code' => 'BRL',
                'symbol' => 'R$',
            ),
            303 => 
            array (
            'country' => 'Britain (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            304 => 
            array (
                'country' => 'Brunei Darussalam',
                'currency' => 'Dollars',
                'code' => 'BND',
                'symbol' => '$',
            ),
            305 => 
            array (
                'country' => 'Canada',
                'currency' => 'Dollars',
                'code' => 'CAD',
                'symbol' => '$',
            ),
            306 => 
            array (
                'country' => 'Cayman Islands',
                'currency' => 'Dollars',
                'code' => 'KYD',
                'symbol' => '$',
            ),
            307 => 
            array (
                'country' => 'Chile',
                'currency' => 'Pesos',
                'code' => 'CLP',
                'symbol' => '$',
            ),
            308 => 
            array (
                'country' => 'China',
                'currency' => 'Yuan Renminbi',
                'code' => 'CNY',
                'symbol' => '¥',
            ),
            309 => 
            array (
                'country' => 'Colombia',
                'currency' => 'Pesos',
                'code' => 'COP',
                'symbol' => '$',
            ),
            310 => 
            array (
                'country' => 'Croatia',
                'currency' => 'Kuna',
                'code' => 'HRK',
                'symbol' => 'kn',
            ),
            311 => 
            array (
                'country' => 'Cyprus',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            312 => 
            array (
                'country' => 'Denmark',
                'currency' => 'Kroner',
                'code' => 'DKK',
                'symbol' => 'kr',
            ),
            313 => 
            array (
                'country' => 'Dominican Republic',
                'currency' => 'Pesos',
                'code' => 'DOP ',
                'symbol' => 'RD$',
            ),
            314 => 
            array (
                'country' => 'East Caribbean',
                'currency' => 'Dollars',
                'code' => 'XCD',
                'symbol' => '$',
            ),
            315 => 
            array (
                'country' => 'Egypt',
                'currency' => 'Pounds',
                'code' => 'EGP',
                'symbol' => '£',
            ),
            316 => 
            array (
                'country' => 'El Salvador',
                'currency' => 'Colones',
                'code' => 'SVC',
                'symbol' => '$',
            ),
            317 => 
            array (
            'country' => 'England (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            318 => 
            array (
                'country' => 'Euro',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            319 => 
            array (
                'country' => 'Falkland Islands',
                'currency' => 'Pounds',
                'code' => 'FKP',
                'symbol' => '£',
            ),
            320 => 
            array (
                'country' => 'Fiji',
                'currency' => 'Dollars',
                'code' => 'FJD',
                'symbol' => '$',
            ),
            321 => 
            array (
                'country' => 'France',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            322 => 
            array (
                'country' => 'Ghana',
                'currency' => 'Cedis',
                'code' => 'GHC',
                'symbol' => '¢',
            ),
            323 => 
            array (
                'country' => 'Gibraltar',
                'currency' => 'Pounds',
                'code' => 'GIP',
                'symbol' => '£',
            ),
            324 => 
            array (
                'country' => 'Greece',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            325 => 
            array (
                'country' => 'Guatemala',
                'currency' => 'Quetzales',
                'code' => 'GTQ',
                'symbol' => 'Q',
            ),
            326 => 
            array (
                'country' => 'Guernsey',
                'currency' => 'Pounds',
                'code' => 'GGP',
                'symbol' => '£',
            ),
            327 => 
            array (
                'country' => 'Guyana',
                'currency' => 'Dollars',
                'code' => 'GYD',
                'symbol' => '$',
            ),
            328 => 
            array (
            'country' => 'Holland (Netherlands)',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            329 => 
            array (
                'country' => 'Honduras',
                'currency' => 'Lempiras',
                'code' => 'HNL',
                'symbol' => 'L',
            ),
            330 => 
            array (
                'country' => 'Hong Kong',
                'currency' => 'Dollars',
                'code' => 'HKD',
                'symbol' => '$',
            ),
            331 => 
            array (
                'country' => 'Hungary',
                'currency' => 'Forint',
                'code' => 'HUF',
                'symbol' => 'Ft',
            ),
            332 => 
            array (
                'country' => 'Iceland',
                'currency' => 'Kronur',
                'code' => 'ISK',
                'symbol' => 'kr',
            ),
            333 => 
            array (
                'country' => 'India',
                'currency' => 'Rupees',
                'code' => 'INR',
                'symbol' => 'Rp',
            ),
            334 => 
            array (
                'country' => 'Indonesia',
                'currency' => 'Rupiahs',
                'code' => 'IDR',
                'symbol' => 'Rp',
            ),
            335 => 
            array (
                'country' => 'Ireland',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            336 => 
            array (
                'country' => 'Isle of Man',
                'currency' => 'Pounds',
                'code' => 'IMP',
                'symbol' => '£',
            ),
            337 => 
            array (
                'country' => 'Italy',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            338 => 
            array (
                'country' => 'Jamaica',
                'currency' => 'Dollars',
                'code' => 'JMD',
                'symbol' => 'J$',
            ),
            339 => 
            array (
                'country' => 'Japan',
                'currency' => 'Yen',
                'code' => 'JPY',
                'symbol' => '¥',
            ),
            340 => 
            array (
                'country' => 'Jersey',
                'currency' => 'Pounds',
                'code' => 'JEP',
                'symbol' => '£',
            ),
            341 => 
            array (
                'country' => 'Latvia',
                'currency' => 'Lati',
                'code' => 'LVL',
                'symbol' => 'Ls',
            ),
            342 => 
            array (
                'country' => 'Lebanon',
                'currency' => 'Pounds',
                'code' => 'LBP',
                'symbol' => '£',
            ),
            343 => 
            array (
                'country' => 'Liberia',
                'currency' => 'Dollars',
                'code' => 'LRD',
                'symbol' => '$',
            ),
            344 => 
            array (
                'country' => 'Liechtenstein',
                'currency' => 'Switzerland Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            345 => 
            array (
                'country' => 'Lithuania',
                'currency' => 'Litai',
                'code' => 'LTL',
                'symbol' => 'Lt',
            ),
            346 => 
            array (
                'country' => 'Luxembourg',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            347 => 
            array (
                'country' => 'Malaysia',
                'currency' => 'Ringgits',
                'code' => 'MYR',
                'symbol' => 'RM',
            ),
            348 => 
            array (
                'country' => 'Malta',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            349 => 
            array (
                'country' => 'Mexico',
                'currency' => 'Pesos',
                'code' => 'MXN',
                'symbol' => '$',
            ),
            350 => 
            array (
                'country' => 'Mozambique',
                'currency' => 'Meticais',
                'code' => 'MZN',
                'symbol' => 'MT',
            ),
            351 => 
            array (
                'country' => 'Namibia',
                'currency' => 'Dollars',
                'code' => 'NAD',
                'symbol' => '$',
            ),
            352 => 
            array (
                'country' => 'Netherlands Antilles',
                'currency' => 'Guilders',
                'code' => 'ANG',
                'symbol' => 'ƒ',
            ),
            353 => 
            array (
                'country' => 'Netherlands',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            354 => 
            array (
                'country' => 'New Zealand',
                'currency' => 'Dollars',
                'code' => 'NZD',
                'symbol' => '$',
            ),
            355 => 
            array (
                'country' => 'Nicaragua',
                'currency' => 'Cordobas',
                'code' => 'NIO',
                'symbol' => 'C$',
            ),
            356 => 
            array (
                'country' => 'Norway',
                'currency' => 'Krone',
                'code' => 'NOK',
                'symbol' => 'kr',
            ),
            357 => 
            array (
                'country' => 'Panama',
                'currency' => 'Balboa',
                'code' => 'PAB',
                'symbol' => 'B/.',
            ),
            358 => 
            array (
                'country' => 'Paraguay',
                'currency' => 'Guarani',
                'code' => 'PYG',
                'symbol' => 'Gs',
            ),
            359 => 
            array (
                'country' => 'Peru',
                'currency' => 'Nuevos Soles',
                'code' => 'PEN',
                'symbol' => 'S/.',
            ),
            360 => 
            array (
                'country' => 'Philippines',
                'currency' => 'Pesos',
                'code' => 'PHP',
                'symbol' => 'Php',
            ),
            361 => 
            array (
                'country' => 'Romania',
                'currency' => 'New Lei',
                'code' => 'RON',
                'symbol' => 'lei',
            ),
            362 => 
            array (
                'country' => 'Saint Helena',
                'currency' => 'Pounds',
                'code' => 'SHP',
                'symbol' => '£',
            ),
            363 => 
            array (
                'country' => 'Singapore',
                'currency' => 'Dollars',
                'code' => 'SGD',
                'symbol' => '$',
            ),
            364 => 
            array (
                'country' => 'Slovenia',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            365 => 
            array (
                'country' => 'Solomon Islands',
                'currency' => 'Dollars',
                'code' => 'SBD',
                'symbol' => '$',
            ),
            366 => 
            array (
                'country' => 'Somalia',
                'currency' => 'Shillings',
                'code' => 'SOS',
                'symbol' => 'S',
            ),
            367 => 
            array (
                'country' => 'South Africa',
                'currency' => 'Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
            ),
            368 => 
            array (
                'country' => 'Spain',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            369 => 
            array (
                'country' => 'Sweden',
                'currency' => 'Kronor',
                'code' => 'SEK',
                'symbol' => 'kr',
            ),
            370 => 
            array (
                'country' => 'Switzerland',
                'currency' => 'Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            371 => 
            array (
                'country' => 'Suriname',
                'currency' => 'Dollars',
                'code' => 'SRD',
                'symbol' => '$',
            ),
            372 => 
            array (
                'country' => 'Syria',
                'currency' => 'Pounds',
                'code' => 'SYP',
                'symbol' => '£',
            ),
            373 => 
            array (
                'country' => 'Taiwan',
                'currency' => 'New Dollars',
                'code' => 'TWD',
                'symbol' => 'NT$',
            ),
            374 => 
            array (
                'country' => 'Trinidad and Tobago',
                'currency' => 'Dollars',
                'code' => 'TTD',
                'symbol' => 'TT$',
            ),
            375 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Lira',
                'code' => 'TRY',
                'symbol' => 'TL',
            ),
            376 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Liras',
                'code' => 'TRL',
                'symbol' => '£',
            ),
            377 => 
            array (
                'country' => 'Tuvalu',
                'currency' => 'Dollars',
                'code' => 'TVD',
                'symbol' => '$',
            ),
            378 => 
            array (
                'country' => 'United Kingdom',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            379 => 
            array (
                'country' => 'United States of America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            380 => 
            array (
                'country' => 'Uruguay',
                'currency' => 'Pesos',
                'code' => 'UYU',
                'symbol' => '$U',
            ),
            381 => 
            array (
                'country' => 'Vatican City',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            382 => 
            array (
                'country' => 'Venezuela',
                'currency' => 'Bolivares Fuertes',
                'code' => 'VEF',
                'symbol' => 'Bs',
            ),
            383 => 
            array (
                'country' => 'Zimbabwe',
                'currency' => 'Zimbabwe Dollars',
                'code' => 'ZWD',
                'symbol' => 'Z$',
            ),
            384 => 
            array (
                'country' => 'Albania',
                'currency' => 'Leke',
                'code' => 'ALL',
                'symbol' => 'Lek',
            ),
            385 => 
            array (
                'country' => 'America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            386 => 
            array (
                'country' => 'Argentina',
                'currency' => 'Pesos',
                'code' => 'ARS',
                'symbol' => '$',
            ),
            387 => 
            array (
                'country' => 'Aruba',
                'currency' => 'Guilders',
                'code' => 'AWG',
                'symbol' => 'ƒ',
            ),
            388 => 
            array (
                'country' => 'Australia',
                'currency' => 'Dollars',
                'code' => 'AUD',
                'symbol' => '$',
            ),
            389 => 
            array (
                'country' => 'Bahamas',
                'currency' => 'Dollars',
                'code' => 'BSD',
                'symbol' => '$',
            ),
            390 => 
            array (
                'country' => 'Barbados',
                'currency' => 'Dollars',
                'code' => 'BBD',
                'symbol' => '$',
            ),
            391 => 
            array (
                'country' => 'Belarus',
                'currency' => 'Rubles',
                'code' => 'BYR',
                'symbol' => 'p.',
            ),
            392 => 
            array (
                'country' => 'Belgium',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            393 => 
            array (
                'country' => 'Beliz',
                'currency' => 'Dollars',
                'code' => 'BZD',
                'symbol' => 'BZ$',
            ),
            394 => 
            array (
                'country' => 'Bermuda',
                'currency' => 'Dollars',
                'code' => 'BMD',
                'symbol' => '$',
            ),
            395 => 
            array (
                'country' => 'Bolivia',
                'currency' => 'Bolivianos',
                'code' => 'BOB',
                'symbol' => '$b',
            ),
            396 => 
            array (
                'country' => 'Bosnia and Herzegovina',
                'currency' => 'Convertible Marka',
                'code' => 'BAM',
                'symbol' => 'KM',
            ),
            397 => 
            array (
                'country' => 'Botswana',
                'currency' => 'Pula',
                'code' => 'BWP',
                'symbol' => 'P',
            ),
            398 => 
            array (
                'country' => 'Brazil',
                'currency' => 'Reais',
                'code' => 'BRL',
                'symbol' => 'R$',
            ),
            399 => 
            array (
            'country' => 'Britain (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            400 => 
            array (
                'country' => 'Brunei Darussalam',
                'currency' => 'Dollars',
                'code' => 'BND',
                'symbol' => '$',
            ),
            401 => 
            array (
                'country' => 'Canada',
                'currency' => 'Dollars',
                'code' => 'CAD',
                'symbol' => '$',
            ),
            402 => 
            array (
                'country' => 'Cayman Islands',
                'currency' => 'Dollars',
                'code' => 'KYD',
                'symbol' => '$',
            ),
            403 => 
            array (
                'country' => 'Chile',
                'currency' => 'Pesos',
                'code' => 'CLP',
                'symbol' => '$',
            ),
            404 => 
            array (
                'country' => 'China',
                'currency' => 'Yuan Renminbi',
                'code' => 'CNY',
                'symbol' => '¥',
            ),
            405 => 
            array (
                'country' => 'Colombia',
                'currency' => 'Pesos',
                'code' => 'COP',
                'symbol' => '$',
            ),
            406 => 
            array (
                'country' => 'Croatia',
                'currency' => 'Kuna',
                'code' => 'HRK',
                'symbol' => 'kn',
            ),
            407 => 
            array (
                'country' => 'Cyprus',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            408 => 
            array (
                'country' => 'Denmark',
                'currency' => 'Kroner',
                'code' => 'DKK',
                'symbol' => 'kr',
            ),
            409 => 
            array (
                'country' => 'Dominican Republic',
                'currency' => 'Pesos',
                'code' => 'DOP ',
                'symbol' => 'RD$',
            ),
            410 => 
            array (
                'country' => 'East Caribbean',
                'currency' => 'Dollars',
                'code' => 'XCD',
                'symbol' => '$',
            ),
            411 => 
            array (
                'country' => 'Egypt',
                'currency' => 'Pounds',
                'code' => 'EGP',
                'symbol' => '£',
            ),
            412 => 
            array (
                'country' => 'El Salvador',
                'currency' => 'Colones',
                'code' => 'SVC',
                'symbol' => '$',
            ),
            413 => 
            array (
            'country' => 'England (United Kingdom)',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            414 => 
            array (
                'country' => 'Euro',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            415 => 
            array (
                'country' => 'Falkland Islands',
                'currency' => 'Pounds',
                'code' => 'FKP',
                'symbol' => '£',
            ),
            416 => 
            array (
                'country' => 'Fiji',
                'currency' => 'Dollars',
                'code' => 'FJD',
                'symbol' => '$',
            ),
            417 => 
            array (
                'country' => 'France',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            418 => 
            array (
                'country' => 'Ghana',
                'currency' => 'Cedis',
                'code' => 'GHC',
                'symbol' => '¢',
            ),
            419 => 
            array (
                'country' => 'Gibraltar',
                'currency' => 'Pounds',
                'code' => 'GIP',
                'symbol' => '£',
            ),
            420 => 
            array (
                'country' => 'Greece',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            421 => 
            array (
                'country' => 'Guatemala',
                'currency' => 'Quetzales',
                'code' => 'GTQ',
                'symbol' => 'Q',
            ),
            422 => 
            array (
                'country' => 'Guernsey',
                'currency' => 'Pounds',
                'code' => 'GGP',
                'symbol' => '£',
            ),
            423 => 
            array (
                'country' => 'Guyana',
                'currency' => 'Dollars',
                'code' => 'GYD',
                'symbol' => '$',
            ),
            424 => 
            array (
            'country' => 'Holland (Netherlands)',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            425 => 
            array (
                'country' => 'Honduras',
                'currency' => 'Lempiras',
                'code' => 'HNL',
                'symbol' => 'L',
            ),
            426 => 
            array (
                'country' => 'Hong Kong',
                'currency' => 'Dollars',
                'code' => 'HKD',
                'symbol' => '$',
            ),
            427 => 
            array (
                'country' => 'Hungary',
                'currency' => 'Forint',
                'code' => 'HUF',
                'symbol' => 'Ft',
            ),
            428 => 
            array (
                'country' => 'Iceland',
                'currency' => 'Kronur',
                'code' => 'ISK',
                'symbol' => 'kr',
            ),
            429 => 
            array (
                'country' => 'India',
                'currency' => 'Rupees',
                'code' => 'INR',
                'symbol' => 'Rp',
            ),
            430 => 
            array (
                'country' => 'Indonesia',
                'currency' => 'Rupiahs',
                'code' => 'IDR',
                'symbol' => 'Rp',
            ),
            431 => 
            array (
                'country' => 'Ireland',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            432 => 
            array (
                'country' => 'Isle of Man',
                'currency' => 'Pounds',
                'code' => 'IMP',
                'symbol' => '£',
            ),
            433 => 
            array (
                'country' => 'Italy',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            434 => 
            array (
                'country' => 'Jamaica',
                'currency' => 'Dollars',
                'code' => 'JMD',
                'symbol' => 'J$',
            ),
            435 => 
            array (
                'country' => 'Japan',
                'currency' => 'Yen',
                'code' => 'JPY',
                'symbol' => '¥',
            ),
            436 => 
            array (
                'country' => 'Jersey',
                'currency' => 'Pounds',
                'code' => 'JEP',
                'symbol' => '£',
            ),
            437 => 
            array (
                'country' => 'Latvia',
                'currency' => 'Lati',
                'code' => 'LVL',
                'symbol' => 'Ls',
            ),
            438 => 
            array (
                'country' => 'Lebanon',
                'currency' => 'Pounds',
                'code' => 'LBP',
                'symbol' => '£',
            ),
            439 => 
            array (
                'country' => 'Liberia',
                'currency' => 'Dollars',
                'code' => 'LRD',
                'symbol' => '$',
            ),
            440 => 
            array (
                'country' => 'Liechtenstein',
                'currency' => 'Switzerland Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            441 => 
            array (
                'country' => 'Lithuania',
                'currency' => 'Litai',
                'code' => 'LTL',
                'symbol' => 'Lt',
            ),
            442 => 
            array (
                'country' => 'Luxembourg',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            443 => 
            array (
                'country' => 'Malaysia',
                'currency' => 'Ringgits',
                'code' => 'MYR',
                'symbol' => 'RM',
            ),
            444 => 
            array (
                'country' => 'Malta',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            445 => 
            array (
                'country' => 'Mexico',
                'currency' => 'Pesos',
                'code' => 'MXN',
                'symbol' => '$',
            ),
            446 => 
            array (
                'country' => 'Mozambique',
                'currency' => 'Meticais',
                'code' => 'MZN',
                'symbol' => 'MT',
            ),
            447 => 
            array (
                'country' => 'Namibia',
                'currency' => 'Dollars',
                'code' => 'NAD',
                'symbol' => '$',
            ),
            448 => 
            array (
                'country' => 'Netherlands Antilles',
                'currency' => 'Guilders',
                'code' => 'ANG',
                'symbol' => 'ƒ',
            ),
            449 => 
            array (
                'country' => 'Netherlands',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            450 => 
            array (
                'country' => 'New Zealand',
                'currency' => 'Dollars',
                'code' => 'NZD',
                'symbol' => '$',
            ),
            451 => 
            array (
                'country' => 'Nicaragua',
                'currency' => 'Cordobas',
                'code' => 'NIO',
                'symbol' => 'C$',
            ),
            452 => 
            array (
                'country' => 'Norway',
                'currency' => 'Krone',
                'code' => 'NOK',
                'symbol' => 'kr',
            ),
            453 => 
            array (
                'country' => 'Panama',
                'currency' => 'Balboa',
                'code' => 'PAB',
                'symbol' => 'B/.',
            ),
            454 => 
            array (
                'country' => 'Paraguay',
                'currency' => 'Guarani',
                'code' => 'PYG',
                'symbol' => 'Gs',
            ),
            455 => 
            array (
                'country' => 'Peru',
                'currency' => 'Nuevos Soles',
                'code' => 'PEN',
                'symbol' => 'S/.',
            ),
            456 => 
            array (
                'country' => 'Philippines',
                'currency' => 'Pesos',
                'code' => 'PHP',
                'symbol' => 'Php',
            ),
            457 => 
            array (
                'country' => 'Romania',
                'currency' => 'New Lei',
                'code' => 'RON',
                'symbol' => 'lei',
            ),
            458 => 
            array (
                'country' => 'Saint Helena',
                'currency' => 'Pounds',
                'code' => 'SHP',
                'symbol' => '£',
            ),
            459 => 
            array (
                'country' => 'Singapore',
                'currency' => 'Dollars',
                'code' => 'SGD',
                'symbol' => '$',
            ),
            460 => 
            array (
                'country' => 'Slovenia',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            461 => 
            array (
                'country' => 'Solomon Islands',
                'currency' => 'Dollars',
                'code' => 'SBD',
                'symbol' => '$',
            ),
            462 => 
            array (
                'country' => 'Somalia',
                'currency' => 'Shillings',
                'code' => 'SOS',
                'symbol' => 'S',
            ),
            463 => 
            array (
                'country' => 'South Africa',
                'currency' => 'Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
            ),
            464 => 
            array (
                'country' => 'Spain',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            465 => 
            array (
                'country' => 'Sweden',
                'currency' => 'Kronor',
                'code' => 'SEK',
                'symbol' => 'kr',
            ),
            466 => 
            array (
                'country' => 'Switzerland',
                'currency' => 'Francs',
                'code' => 'CHF',
                'symbol' => 'CHF',
            ),
            467 => 
            array (
                'country' => 'Suriname',
                'currency' => 'Dollars',
                'code' => 'SRD',
                'symbol' => '$',
            ),
            468 => 
            array (
                'country' => 'Syria',
                'currency' => 'Pounds',
                'code' => 'SYP',
                'symbol' => '£',
            ),
            469 => 
            array (
                'country' => 'Taiwan',
                'currency' => 'New Dollars',
                'code' => 'TWD',
                'symbol' => 'NT$',
            ),
            470 => 
            array (
                'country' => 'Trinidad and Tobago',
                'currency' => 'Dollars',
                'code' => 'TTD',
                'symbol' => 'TT$',
            ),
            471 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Lira',
                'code' => 'TRY',
                'symbol' => 'TL',
            ),
            472 => 
            array (
                'country' => 'Turkey',
                'currency' => 'Liras',
                'code' => 'TRL',
                'symbol' => '£',
            ),
            473 => 
            array (
                'country' => 'Tuvalu',
                'currency' => 'Dollars',
                'code' => 'TVD',
                'symbol' => '$',
            ),
            474 => 
            array (
                'country' => 'United Kingdom',
                'currency' => 'Pounds',
                'code' => 'GBP',
                'symbol' => '£',
            ),
            475 => 
            array (
                'country' => 'United States of America',
                'currency' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
            ),
            476 => 
            array (
                'country' => 'Uruguay',
                'currency' => 'Pesos',
                'code' => 'UYU',
                'symbol' => '$U',
            ),
            477 => 
            array (
                'country' => 'Vatican City',
                'currency' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
            ),
            478 => 
            array (
                'country' => 'Venezuela',
                'currency' => 'Bolivares Fuertes',
                'code' => 'VEF',
                'symbol' => 'Bs',
            ),
            479 => 
            array (
                'country' => 'Zimbabwe',
                'currency' => 'Zimbabwe Dollars',
                'code' => 'ZWD',
                'symbol' => 'Z$',
            ),
        ));
        
        
    }
}