<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrenciesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('currencies')->delete();
        
        \DB::table('currencies')->insert(array (
            0 => 
            array (
                'id' => 1,
                'currencyname' => 'US Dollar',
                'leftsymbol' => '$',
                'rightsymbol' => 'USD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.250000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'currencyname' => 'Pound Sterling',
                'leftsymbol' => '£',
                'rightsymbol' => 'GBP',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '2',
            ),
            2 => 
            array (
                'id' => 3,
                'currencyname' => 'Euro',
                'leftsymbol' => '€',
                'rightsymbol' => 'EUR',
                'isdefault' => 0,
                'currencyexchangerate' => '1.130000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '3',
            ),
            3 => 
            array (
                'id' => 4,
                'currencyname' => 'UAE  Dirham  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'AED',
                'isdefault' => 0,
                'currencyexchangerate' => '4.500000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '4',
            ),
            4 => 
            array (
                'id' => 5,
                'currencyname' => 'Argentine Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'ARS',
                'isdefault' => 0,
                'currencyexchangerate' => '70.830000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '5',
            ),
            5 => 
            array (
                'id' => 6,
                'currencyname' => 'Australian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'AUD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.709780',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '6',
            ),
            6 => 
            array (
                'id' => 7,
                'currencyname' => 'Aruban Florin   
',
                'leftsymbol' => 'ƒ',
                'rightsymbol' => 'AWG',
                'isdefault' => 0,
                'currencyexchangerate' => '2.873290',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '7',
            ),
            7 => 
            array (
                'id' => 8,
                'currencyname' => 'Bosnia and Herzegovina convertible mark
',
                'leftsymbol' => 'KM',
                'rightsymbol' => 'BAM',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '8',
            ),
            8 => 
            array (
                'id' => 9,
                'currencyname' => 'Barbadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BBD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.530000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '9',
            ),
            9 => 
            array (
                'id' => 10,
                'currencyname' => 'Bangladeshi Taka   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BDT',
                'isdefault' => 0,
                'currencyexchangerate' => '124.771300',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '10',
            ),
            10 => 
            array (
                'id' => 11,
                'currencyname' => 'Bulgarian Lev   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BGN',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '11',
            ),
            11 => 
            array (
                'id' => 12,
                'currencyname' => 'Bahraini Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BHD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.603550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '12',
            ),
            12 => 
            array (
                'id' => 13,
                'currencyname' => 'Bermudian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BMD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '13',
            ),
            13 => 
            array (
                'id' => 14,
                'currencyname' => 'Bolivian Boliviano   
',
                'leftsymbol' => '$b',
                'rightsymbol' => 'BOB',
                'isdefault' => 0,
                'currencyexchangerate' => '11.091860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '14',
            ),
            14 => 
            array (
                'id' => 15,
                'currencyname' => 'Brazilian Real   
',
                'leftsymbol' => 'R$',
                'rightsymbol' => 'BRL',
                'isdefault' => 0,
                'currencyexchangerate' => '3.572820',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '15',
            ),
            15 => 
            array (
                'id' => 16,
                'currencyname' => 'Bahamian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BSD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '16',
            ),
            16 => 
            array (
                'id' => 17,
                'currencyname' => 'Canadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CAD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.654690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '17',
            ),
            17 => 
            array (
                'id' => 18,
                'currencyname' => 'Swiss Franc   
',
                'leftsymbol' => 'CHF',
                'rightsymbol' => 'CHF',
                'isdefault' => 0,
                'currencyexchangerate' => '1.462400',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '18',
            ),
            18 => 
            array (
                'id' => 19,
                'currencyname' => 'Chilean Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CLP',
                'isdefault' => 0,
                'currencyexchangerate' => '805.001780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '19',
            ),
            19 => 
            array (
                'id' => 20,
                'currencyname' => 'Chinese Yuan   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'CNY',
                'isdefault' => 0,
                'currencyexchangerate' => '9.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '20',
            ),
            20 => 
            array (
                'id' => 21,
                'currencyname' => 'Colombian Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'COP',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '21',
            ),
            21 => 
            array (
                'id' => 22,
                'currencyname' => 'Czech Koruna   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'CZK',
                'isdefault' => 0,
                'currencyexchangerate' => '30.758570',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '22',
            ),
            22 => 
            array (
                'id' => 23,
                'currencyname' => 'Danish Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'DKK',
                'isdefault' => 0,
                'currencyexchangerate' => '8.877180',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '23',
            ),
            23 => 
            array (
                'id' => 24,
                'currencyname' => 'Egyptian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'EGP',
                'isdefault' => 0,
                'currencyexchangerate' => '11.061760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '24',
            ),
            24 => 
            array (
                'id' => 25,
                'currencyname' => 'Fijian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'FJD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.991230',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '26',
            ),
            25 => 
            array (
                'id' => 26,
                'currencyname' => 'Ghana Cedi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GHS',
                'isdefault' => 0,
                'currencyexchangerate' => '3.217870',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '28',
            ),
            26 => 
            array (
                'id' => 27,
                'currencyname' => 'Gambian Dalasi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GMD',
                'isdefault' => 0,
                'currencyexchangerate' => '52.329130',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '29',
            ),
            27 => 
            array (
                'id' => 28,
                'currencyname' => 'Guatemalan Quetzal   
',
                'leftsymbol' => 'Q',
                'rightsymbol' => 'GTQ',
                'isdefault' => 0,
                'currencyexchangerate' => '12.737980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '30',
            ),
            28 => 
            array (
                'id' => 29,
                'currencyname' => 'Hong Kong Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'HKD',
                'isdefault' => 0,
                'currencyexchangerate' => '12.440210',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '31',
            ),
            29 => 
            array (
                'id' => 30,
                'currencyname' => 'Croatian Kuna   
',
                'leftsymbol' => 'kn',
                'rightsymbol' => 'HRK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.059020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '32',
            ),
            30 => 
            array (
                'id' => 31,
                'currencyname' => 'Hungarian Forint   
',
                'leftsymbol' => 'Ft',
                'rightsymbol' => 'HUF',
                'isdefault' => 0,
                'currencyexchangerate' => '356.720970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '33',
            ),
            31 => 
            array (
                'id' => 32,
                'currencyname' => 'Indonesian Rupiah   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'IDR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '34',
            ),
            32 => 
            array (
                'id' => 33,
                'currencyname' => 'Israeli Sheqel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'ILS',
                'isdefault' => 0,
                'currencyexchangerate' => '5.712760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '35',
            ),
            33 => 
            array (
                'id' => 34,
                'currencyname' => 'Indian Rupee   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'INR',
                'isdefault' => 0,
                'currencyexchangerate' => '99.798550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '36',
            ),
            34 => 
            array (
                'id' => 35,
                'currencyname' => 'Icelandic Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'ISK',
                'isdefault' => 0,
                'currencyexchangerate' => '194.918020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '37',
            ),
            35 => 
            array (
                'id' => 36,
                'currencyname' => 'Jamaican Dollar   
',
                'leftsymbol' => 'J$',
                'rightsymbol' => 'JMD',
                'isdefault' => 0,
                'currencyexchangerate' => '164.210730',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '38',
            ),
            36 => 
            array (
                'id' => 37,
                'currencyname' => 'Jordanian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'JOD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.138080',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '39',
            ),
            37 => 
            array (
                'id' => 38,
                'currencyname' => 'Japanese Yen   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'JPY',
                'isdefault' => 0,
                'currencyexchangerate' => '158.367800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '40',
            ),
            38 => 
            array (
                'id' => 39,
                'currencyname' => 'Kenyan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KES',
                'isdefault' => 0,
                'currencyexchangerate' => '139.651360',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '41',
            ),
            39 => 
            array (
                'id' => 40,
                'currencyname' => 'Cambodian Riel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KHR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '42',
            ),
            40 => 
            array (
                'id' => 41,
                'currencyname' => 'South Korean Won  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KRW',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '43',
            ),
            41 => 
            array (
                'id' => 42,
                'currencyname' => 'Kuwaiti Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KWD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.454780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '44',
            ),
            42 => 
            array (
                'id' => 43,
                'currencyname' => 'Lao Kip   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LAK',
                'isdefault' => 0,
                'currencyexchangerate' => '12.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '45',
            ),
            43 => 
            array (
                'id' => 44,
                'currencyname' => 'Lebanese Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'LBP',
                'isdefault' => 0,
                'currencyexchangerate' => '2.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '46',
            ),
            44 => 
            array (
                'id' => 45,
                'currencyname' => 'Sri Lankan Rupee  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LKR',
                'isdefault' => 0,
                'currencyexchangerate' => '211.844720',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '47',
            ),
            45 => 
            array (
                'id' => 46,
                'currencyname' => 'Lithuanian Litas   
',
                'leftsymbol' => 'Lt',
                'rightsymbol' => 'LTL',
                'isdefault' => 0,
                'currencyexchangerate' => '4.108520',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '48',
            ),
            46 => 
            array (
                'id' => 47,
                'currencyname' => 'Latvian Lats   
',
                'leftsymbol' => 'Ls',
                'rightsymbol' => 'LVL',
                'isdefault' => 0,
                'currencyexchangerate' => '0.836270',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '49',
            ),
            47 => 
            array (
                'id' => 48,
                'currencyname' => 'Moroccan Dirham   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MAD',
                'isdefault' => 0,
                'currencyexchangerate' => '13.330910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '50',
            ),
            48 => 
            array (
                'id' => 49,
                'currencyname' => 'Moldovan Leu   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MDL',
                'isdefault' => 0,
                'currencyexchangerate' => '20.714900',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '51',
            ),
            49 => 
            array (
                'id' => 50,
                'currencyname' => 'Malagasy Ariary   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MGA',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '52',
            ),
            50 => 
            array (
                'id' => 51,
                'currencyname' => 'Macedonian Denar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MKD',
                'isdefault' => 0,
                'currencyexchangerate' => '73.116250',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '53',
            ),
            51 => 
            array (
                'id' => 52,
                'currencyname' => 'Mauritian Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MUR',
                'isdefault' => 0,
                'currencyexchangerate' => '49.439790',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '54',
            ),
            52 => 
            array (
                'id' => 53,
                'currencyname' => 'Maldivian Rufiyaa   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MVR',
                'isdefault' => 0,
                'currencyexchangerate' => '24.735960',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '55',
            ),
            53 => 
            array (
                'id' => 54,
                'currencyname' => 'Mexican Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'MXN',
                'isdefault' => 0,
                'currencyexchangerate' => '20.862090',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '56',
            ),
            54 => 
            array (
                'id' => 55,
                'currencyname' => 'Malaysian Ringgit   
',
                'leftsymbol' => 'RM',
                'rightsymbol' => 'MYR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.165040',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '57',
            ),
            55 => 
            array (
                'id' => 56,
                'currencyname' => 'Namibian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NAD',
                'isdefault' => 0,
                'currencyexchangerate' => '16.022370',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '58',
            ),
            56 => 
            array (
                'id' => 57,
                'currencyname' => 'Nigerian Naira   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NGN',
                'isdefault' => 0,
                'currencyexchangerate' => '257.472160',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '59',
            ),
            57 => 
            array (
                'id' => 58,
                'currencyname' => 'Norwegian Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'NOK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.633510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '60',
            ),
            58 => 
            array (
                'id' => 59,
                'currencyname' => 'Nepalese Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NPR',
                'isdefault' => 0,
                'currencyexchangerate' => '159.677680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '61',
            ),
            59 => 
            array (
                'id' => 60,
                'currencyname' => 'New Zealand Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NZD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.935980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '62',
            ),
            60 => 
            array (
                'id' => 61,
                'currencyname' => 'Omani Rial   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'OMR',
                'isdefault' => 0,
                'currencyexchangerate' => '0.617190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '63',
            ),
            61 => 
            array (
                'id' => 62,
                'currencyname' => 'Panamanian Balboa   
',
                'leftsymbol' => 'B/.',
                'rightsymbol' => 'PAB',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '64',
            ),
            62 => 
            array (
                'id' => 63,
                'currencyname' => 'Peruvian Sol   
',
                'leftsymbol' => 'S/.',
                'rightsymbol' => 'PEN',
                'isdefault' => 0,
                'currencyexchangerate' => '4.439910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '65',
            ),
            63 => 
            array (
                'id' => 64,
                'currencyname' => 'Philippine Peso   
',
                'leftsymbol' => 'Php',
                'rightsymbol' => 'PHP',
                'isdefault' => 0,
                'currencyexchangerate' => '69.464540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '66',
            ),
            64 => 
            array (
                'id' => 65,
                'currencyname' => 'Pakistani Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PKR',
                'isdefault' => 0,
                'currencyexchangerate' => '176.600000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '67',
            ),
            65 => 
            array (
                'id' => 66,
                'currencyname' => 'Polish Zloty   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PLN',
                'isdefault' => 0,
                'currencyexchangerate' => '5.026420',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '68',
            ),
            66 => 
            array (
                'id' => 67,
                'currencyname' => 'Paraguayan Guaran   
',
                'leftsymbol' => 'Gs',
                'rightsymbol' => 'PYG',
                'isdefault' => 0,
                'currencyexchangerate' => '7.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '69',
            ),
            67 => 
            array (
                'id' => 68,
                'currencyname' => 'Qatari Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'QAR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '70',
            ),
            68 => 
            array (
                'id' => 69,
                'currencyname' => 'Romanian Leu   
',
                'leftsymbol' => 'lei',
                'rightsymbol' => 'RON',
                'isdefault' => 0,
                'currencyexchangerate' => '5.312350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '71',
            ),
            69 => 
            array (
                'id' => 70,
                'currencyname' => 'Serbian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RSD',
                'isdefault' => 0,
                'currencyexchangerate' => '123.341860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '72',
            ),
            70 => 
            array (
                'id' => 71,
                'currencyname' => 'Russian Rouble   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RUB',
                'isdefault' => 0,
                'currencyexchangerate' => '79.820000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '73',
            ),
            71 => 
            array (
                'id' => 72,
                'currencyname' => 'Saudi Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SAR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.019460',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '74',
            ),
            72 => 
            array (
                'id' => 73,
                'currencyname' => 'Seychellois Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SCR',
                'isdefault' => 0,
                'currencyexchangerate' => '19.351020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '75',
            ),
            73 => 
            array (
                'id' => 74,
                'currencyname' => 'Swedish Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'SEK',
                'isdefault' => 0,
                'currencyexchangerate' => '10.307350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '76',
            ),
            74 => 
            array (
                'id' => 75,
                'currencyname' => 'Singapore Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'SGD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.012970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '77',
            ),
            75 => 
            array (
                'id' => 76,
                'currencyname' => 'Syrian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'SYP',
                'isdefault' => 0,
                'currencyexchangerate' => '217.743690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '78',
            ),
            76 => 
            array (
                'id' => 77,
                'currencyname' => 'Thai Baht   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'THB',
                'isdefault' => 0,
                'currencyexchangerate' => '50.065800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '79',
            ),
            77 => 
            array (
                'id' => 78,
                'currencyname' => 'Tunisian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'TND',
                'isdefault' => 0,
                'currencyexchangerate' => '2.645410',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '80',
            ),
            78 => 
            array (
                'id' => 79,
                'currencyname' => 'Turkish Lira   
',
                'leftsymbol' => 'TL',
                'rightsymbol' => 'TRY',
                'isdefault' => 0,
                'currencyexchangerate' => '3.227630',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '81',
            ),
            79 => 
            array (
                'id' => 80,
                'currencyname' => 'Taiwanese Dollar   
',
                'leftsymbol' => 'NT$',
                'rightsymbol' => 'TWD',
                'isdefault' => 0,
                'currencyexchangerate' => '47.473470',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '82',
            ),
            80 => 
            array (
                'id' => 81,
                'currencyname' => 'Ukraine Hryvnia   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UAH',
                'isdefault' => 0,
                'currencyexchangerate' => '13.127200',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '83',
            ),
            81 => 
            array (
                'id' => 82,
                'currencyname' => 'Ugandan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UGX',
                'isdefault' => 0,
                'currencyexchangerate' => '4.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '84',
            ),
            82 => 
            array (
                'id' => 83,
                'currencyname' => 'Uruguayan Peso   
',
                'leftsymbol' => '$U',
                'rightsymbol' => 'UYU',
                'isdefault' => 0,
                'currencyexchangerate' => '34.511540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '86',
            ),
            83 => 
            array (
                'id' => 84,
                'currencyname' => 'Venezuelan Bolvar   
',
                'leftsymbol' => 'Bs',
                'rightsymbol' => 'VEF',
                'isdefault' => 0,
                'currencyexchangerate' => '10.112680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '87',
            ),
            84 => 
            array (
                'id' => 85,
                'currencyname' => 'Vietnamese Dong   ',
                'leftsymbol' => '-',
                'rightsymbol' => 'VND',
                'isdefault' => 0,
                'currencyexchangerate' => '33.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '88',
            ),
            85 => 
            array (
                'id' => 86,
                'currencyname' => 'Central African Franc  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'XAF',
                'isdefault' => 0,
                'currencyexchangerate' => '780.529510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '89',
            ),
            86 => 
            array (
                'id' => 87,
                'currencyname' => 'East Caribbean Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'XCD',
                'isdefault' => 0,
                'currencyexchangerate' => '4.334010',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '90',
            ),
            87 => 
            array (
                'id' => 88,
                'currencyname' => 'West African Franc  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'XOF',
                'isdefault' => 0,
                'currencyexchangerate' => '743.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '91',
            ),
            88 => 
            array (
                'id' => 90,
                'currencyname' => 'South African Rand  
',
                'leftsymbol' => 'R',
                'rightsymbol' => 'ZAR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '93',
            ),
            89 => 
            array (
                'id' => 91,
                'currencyname' => ' IRANIAN RIAL',
                'leftsymbol' => '--',
                'rightsymbol' => 'IRR',
                'isdefault' => 0,
                'currencyexchangerate' => '125.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '94',
            ),
            90 => 
            array (
                'id' => 1,
                'currencyname' => 'US Dollar',
                'leftsymbol' => '$',
                'rightsymbol' => 'USD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.250000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '1',
            ),
            91 => 
            array (
                'id' => 2,
                'currencyname' => 'Pound Sterling',
                'leftsymbol' => '£',
                'rightsymbol' => 'GBP',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '2',
            ),
            92 => 
            array (
                'id' => 3,
                'currencyname' => 'Euro',
                'leftsymbol' => '€',
                'rightsymbol' => 'EUR',
                'isdefault' => 0,
                'currencyexchangerate' => '1.130000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '3',
            ),
            93 => 
            array (
                'id' => 4,
                'currencyname' => 'UAE  Dirham  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'AED',
                'isdefault' => 0,
                'currencyexchangerate' => '4.500000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '4',
            ),
            94 => 
            array (
                'id' => 5,
                'currencyname' => 'Argentine Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'ARS',
                'isdefault' => 0,
                'currencyexchangerate' => '70.830000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '5',
            ),
            95 => 
            array (
                'id' => 6,
                'currencyname' => 'Australian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'AUD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.709780',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '6',
            ),
            96 => 
            array (
                'id' => 7,
                'currencyname' => 'Aruban Florin   
',
                'leftsymbol' => 'ƒ',
                'rightsymbol' => 'AWG',
                'isdefault' => 0,
                'currencyexchangerate' => '2.873290',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '7',
            ),
            97 => 
            array (
                'id' => 8,
                'currencyname' => 'Bosnia and Herzegovina convertible mark
',
                'leftsymbol' => 'KM',
                'rightsymbol' => 'BAM',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '8',
            ),
            98 => 
            array (
                'id' => 9,
                'currencyname' => 'Barbadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BBD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.530000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '9',
            ),
            99 => 
            array (
                'id' => 10,
                'currencyname' => 'Bangladeshi Taka   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BDT',
                'isdefault' => 0,
                'currencyexchangerate' => '124.771300',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '10',
            ),
            100 => 
            array (
                'id' => 11,
                'currencyname' => 'Bulgarian Lev   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BGN',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '11',
            ),
            101 => 
            array (
                'id' => 12,
                'currencyname' => 'Bahraini Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BHD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.603550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '12',
            ),
            102 => 
            array (
                'id' => 13,
                'currencyname' => 'Bermudian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BMD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '13',
            ),
            103 => 
            array (
                'id' => 14,
                'currencyname' => 'Bolivian Boliviano   
',
                'leftsymbol' => '$b',
                'rightsymbol' => 'BOB',
                'isdefault' => 0,
                'currencyexchangerate' => '11.091860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '14',
            ),
            104 => 
            array (
                'id' => 15,
                'currencyname' => 'Brazilian Real   
',
                'leftsymbol' => 'R$',
                'rightsymbol' => 'BRL',
                'isdefault' => 0,
                'currencyexchangerate' => '3.572820',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '15',
            ),
            105 => 
            array (
                'id' => 16,
                'currencyname' => 'Bahamian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BSD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '16',
            ),
            106 => 
            array (
                'id' => 17,
                'currencyname' => 'Canadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CAD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.654690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '17',
            ),
            107 => 
            array (
                'id' => 18,
                'currencyname' => 'Swiss Franc   
',
                'leftsymbol' => 'CHF',
                'rightsymbol' => 'CHF',
                'isdefault' => 0,
                'currencyexchangerate' => '1.462400',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '18',
            ),
            108 => 
            array (
                'id' => 19,
                'currencyname' => 'Chilean Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CLP',
                'isdefault' => 0,
                'currencyexchangerate' => '805.001780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '19',
            ),
            109 => 
            array (
                'id' => 20,
                'currencyname' => 'Chinese Yuan   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'CNY',
                'isdefault' => 0,
                'currencyexchangerate' => '9.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '20',
            ),
            110 => 
            array (
                'id' => 21,
                'currencyname' => 'Colombian Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'COP',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '21',
            ),
            111 => 
            array (
                'id' => 22,
                'currencyname' => 'Czech Koruna   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'CZK',
                'isdefault' => 0,
                'currencyexchangerate' => '30.758570',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '22',
            ),
            112 => 
            array (
                'id' => 23,
                'currencyname' => 'Danish Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'DKK',
                'isdefault' => 0,
                'currencyexchangerate' => '8.877180',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '23',
            ),
            113 => 
            array (
                'id' => 24,
                'currencyname' => 'Egyptian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'EGP',
                'isdefault' => 0,
                'currencyexchangerate' => '11.061760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '24',
            ),
            114 => 
            array (
                'id' => 25,
                'currencyname' => 'Fijian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'FJD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.991230',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '26',
            ),
            115 => 
            array (
                'id' => 26,
                'currencyname' => 'Ghana Cedi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GHS',
                'isdefault' => 0,
                'currencyexchangerate' => '3.217870',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '28',
            ),
            116 => 
            array (
                'id' => 27,
                'currencyname' => 'Gambian Dalasi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GMD',
                'isdefault' => 0,
                'currencyexchangerate' => '52.329130',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '29',
            ),
            117 => 
            array (
                'id' => 28,
                'currencyname' => 'Guatemalan Quetzal   
',
                'leftsymbol' => 'Q',
                'rightsymbol' => 'GTQ',
                'isdefault' => 0,
                'currencyexchangerate' => '12.737980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '30',
            ),
            118 => 
            array (
                'id' => 29,
                'currencyname' => 'Hong Kong Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'HKD',
                'isdefault' => 0,
                'currencyexchangerate' => '12.440210',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '31',
            ),
            119 => 
            array (
                'id' => 30,
                'currencyname' => 'Croatian Kuna   
',
                'leftsymbol' => 'kn',
                'rightsymbol' => 'HRK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.059020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '32',
            ),
            120 => 
            array (
                'id' => 31,
                'currencyname' => 'Hungarian Forint   
',
                'leftsymbol' => 'Ft',
                'rightsymbol' => 'HUF',
                'isdefault' => 0,
                'currencyexchangerate' => '356.720970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '33',
            ),
            121 => 
            array (
                'id' => 32,
                'currencyname' => 'Indonesian Rupiah   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'IDR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '34',
            ),
            122 => 
            array (
                'id' => 33,
                'currencyname' => 'Israeli Sheqel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'ILS',
                'isdefault' => 0,
                'currencyexchangerate' => '5.712760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '35',
            ),
            123 => 
            array (
                'id' => 34,
                'currencyname' => 'Indian Rupee   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'INR',
                'isdefault' => 0,
                'currencyexchangerate' => '99.798550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '36',
            ),
            124 => 
            array (
                'id' => 35,
                'currencyname' => 'Icelandic Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'ISK',
                'isdefault' => 0,
                'currencyexchangerate' => '194.918020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '37',
            ),
            125 => 
            array (
                'id' => 36,
                'currencyname' => 'Jamaican Dollar   
',
                'leftsymbol' => 'J$',
                'rightsymbol' => 'JMD',
                'isdefault' => 0,
                'currencyexchangerate' => '164.210730',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '38',
            ),
            126 => 
            array (
                'id' => 37,
                'currencyname' => 'Jordanian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'JOD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.138080',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '39',
            ),
            127 => 
            array (
                'id' => 38,
                'currencyname' => 'Japanese Yen   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'JPY',
                'isdefault' => 0,
                'currencyexchangerate' => '158.367800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '40',
            ),
            128 => 
            array (
                'id' => 39,
                'currencyname' => 'Kenyan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KES',
                'isdefault' => 0,
                'currencyexchangerate' => '139.651360',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '41',
            ),
            129 => 
            array (
                'id' => 40,
                'currencyname' => 'Cambodian Riel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KHR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '42',
            ),
            130 => 
            array (
                'id' => 41,
                'currencyname' => 'South Korean Won  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KRW',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '43',
            ),
            131 => 
            array (
                'id' => 42,
                'currencyname' => 'Kuwaiti Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KWD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.454780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '44',
            ),
            132 => 
            array (
                'id' => 43,
                'currencyname' => 'Lao Kip   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LAK',
                'isdefault' => 0,
                'currencyexchangerate' => '12.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '45',
            ),
            133 => 
            array (
                'id' => 44,
                'currencyname' => 'Lebanese Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'LBP',
                'isdefault' => 0,
                'currencyexchangerate' => '2.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '46',
            ),
            134 => 
            array (
                'id' => 45,
                'currencyname' => 'Sri Lankan Rupee  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LKR',
                'isdefault' => 0,
                'currencyexchangerate' => '211.844720',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '47',
            ),
            135 => 
            array (
                'id' => 46,
                'currencyname' => 'Lithuanian Litas   
',
                'leftsymbol' => 'Lt',
                'rightsymbol' => 'LTL',
                'isdefault' => 0,
                'currencyexchangerate' => '4.108520',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '48',
            ),
            136 => 
            array (
                'id' => 47,
                'currencyname' => 'Latvian Lats   
',
                'leftsymbol' => 'Ls',
                'rightsymbol' => 'LVL',
                'isdefault' => 0,
                'currencyexchangerate' => '0.836270',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '49',
            ),
            137 => 
            array (
                'id' => 48,
                'currencyname' => 'Moroccan Dirham   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MAD',
                'isdefault' => 0,
                'currencyexchangerate' => '13.330910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '50',
            ),
            138 => 
            array (
                'id' => 49,
                'currencyname' => 'Moldovan Leu   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MDL',
                'isdefault' => 0,
                'currencyexchangerate' => '20.714900',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '51',
            ),
            139 => 
            array (
                'id' => 50,
                'currencyname' => 'Malagasy Ariary   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MGA',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '52',
            ),
            140 => 
            array (
                'id' => 51,
                'currencyname' => 'Macedonian Denar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MKD',
                'isdefault' => 0,
                'currencyexchangerate' => '73.116250',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '53',
            ),
            141 => 
            array (
                'id' => 52,
                'currencyname' => 'Mauritian Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MUR',
                'isdefault' => 0,
                'currencyexchangerate' => '49.439790',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '54',
            ),
            142 => 
            array (
                'id' => 53,
                'currencyname' => 'Maldivian Rufiyaa   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MVR',
                'isdefault' => 0,
                'currencyexchangerate' => '24.735960',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '55',
            ),
            143 => 
            array (
                'id' => 54,
                'currencyname' => 'Mexican Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'MXN',
                'isdefault' => 0,
                'currencyexchangerate' => '20.862090',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '56',
            ),
            144 => 
            array (
                'id' => 55,
                'currencyname' => 'Malaysian Ringgit   
',
                'leftsymbol' => 'RM',
                'rightsymbol' => 'MYR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.165040',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '57',
            ),
            145 => 
            array (
                'id' => 56,
                'currencyname' => 'Namibian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NAD',
                'isdefault' => 0,
                'currencyexchangerate' => '16.022370',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '58',
            ),
            146 => 
            array (
                'id' => 57,
                'currencyname' => 'Nigerian Naira   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NGN',
                'isdefault' => 0,
                'currencyexchangerate' => '257.472160',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '59',
            ),
            147 => 
            array (
                'id' => 58,
                'currencyname' => 'Norwegian Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'NOK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.633510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '60',
            ),
            148 => 
            array (
                'id' => 59,
                'currencyname' => 'Nepalese Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NPR',
                'isdefault' => 0,
                'currencyexchangerate' => '159.677680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '61',
            ),
            149 => 
            array (
                'id' => 60,
                'currencyname' => 'New Zealand Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NZD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.935980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '62',
            ),
            150 => 
            array (
                'id' => 61,
                'currencyname' => 'Omani Rial   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'OMR',
                'isdefault' => 0,
                'currencyexchangerate' => '0.617190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '63',
            ),
            151 => 
            array (
                'id' => 62,
                'currencyname' => 'Panamanian Balboa   
',
                'leftsymbol' => 'B/.',
                'rightsymbol' => 'PAB',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '64',
            ),
            152 => 
            array (
                'id' => 63,
                'currencyname' => 'Peruvian Sol   
',
                'leftsymbol' => 'S/.',
                'rightsymbol' => 'PEN',
                'isdefault' => 0,
                'currencyexchangerate' => '4.439910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '65',
            ),
            153 => 
            array (
                'id' => 64,
                'currencyname' => 'Philippine Peso   
',
                'leftsymbol' => 'Php',
                'rightsymbol' => 'PHP',
                'isdefault' => 0,
                'currencyexchangerate' => '69.464540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '66',
            ),
            154 => 
            array (
                'id' => 65,
                'currencyname' => 'Pakistani Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PKR',
                'isdefault' => 0,
                'currencyexchangerate' => '176.600000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '67',
            ),
            155 => 
            array (
                'id' => 66,
                'currencyname' => 'Polish Zloty   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PLN',
                'isdefault' => 0,
                'currencyexchangerate' => '5.026420',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '68',
            ),
            156 => 
            array (
                'id' => 67,
                'currencyname' => 'Paraguayan Guaran   
',
                'leftsymbol' => 'Gs',
                'rightsymbol' => 'PYG',
                'isdefault' => 0,
                'currencyexchangerate' => '7.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '69',
            ),
            157 => 
            array (
                'id' => 68,
                'currencyname' => 'Qatari Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'QAR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '70',
            ),
            158 => 
            array (
                'id' => 69,
                'currencyname' => 'Romanian Leu   
',
                'leftsymbol' => 'lei',
                'rightsymbol' => 'RON',
                'isdefault' => 0,
                'currencyexchangerate' => '5.312350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '71',
            ),
            159 => 
            array (
                'id' => 70,
                'currencyname' => 'Serbian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RSD',
                'isdefault' => 0,
                'currencyexchangerate' => '123.341860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '72',
            ),
            160 => 
            array (
                'id' => 71,
                'currencyname' => 'Russian Rouble   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RUB',
                'isdefault' => 0,
                'currencyexchangerate' => '79.820000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '73',
            ),
            161 => 
            array (
                'id' => 72,
                'currencyname' => 'Saudi Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SAR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.019460',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '74',
            ),
            162 => 
            array (
                'id' => 73,
                'currencyname' => 'Seychellois Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SCR',
                'isdefault' => 0,
                'currencyexchangerate' => '19.351020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '75',
            ),
            163 => 
            array (
                'id' => 74,
                'currencyname' => 'Swedish Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'SEK',
                'isdefault' => 0,
                'currencyexchangerate' => '10.307350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '76',
            ),
            164 => 
            array (
                'id' => 75,
                'currencyname' => 'Singapore Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'SGD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.012970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '77',
            ),
            165 => 
            array (
                'id' => 76,
                'currencyname' => 'Syrian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'SYP',
                'isdefault' => 0,
                'currencyexchangerate' => '217.743690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '78',
            ),
            166 => 
            array (
                'id' => 77,
                'currencyname' => 'Thai Baht   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'THB',
                'isdefault' => 0,
                'currencyexchangerate' => '50.065800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '79',
            ),
            167 => 
            array (
                'id' => 78,
                'currencyname' => 'Tunisian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'TND',
                'isdefault' => 0,
                'currencyexchangerate' => '2.645410',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '80',
            ),
            168 => 
            array (
                'id' => 79,
                'currencyname' => 'Turkish Lira   
',
                'leftsymbol' => 'TL',
                'rightsymbol' => 'TRY',
                'isdefault' => 0,
                'currencyexchangerate' => '3.227630',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '81',
            ),
            169 => 
            array (
                'id' => 80,
                'currencyname' => 'Taiwanese Dollar   
',
                'leftsymbol' => 'NT$',
                'rightsymbol' => 'TWD',
                'isdefault' => 0,
                'currencyexchangerate' => '47.473470',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '82',
            ),
            170 => 
            array (
                'id' => 81,
                'currencyname' => 'Ukraine Hryvnia   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UAH',
                'isdefault' => 0,
                'currencyexchangerate' => '13.127200',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '83',
            ),
            171 => 
            array (
                'id' => 82,
                'currencyname' => 'Ugandan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UGX',
                'isdefault' => 0,
                'currencyexchangerate' => '4.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '84',
            ),
            172 => 
            array (
                'id' => 83,
                'currencyname' => 'Uruguayan Peso   
',
                'leftsymbol' => '$U',
                'rightsymbol' => 'UYU',
                'isdefault' => 0,
                'currencyexchangerate' => '34.511540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '86',
            ),
            173 => 
            array (
                'id' => 84,
                'currencyname' => 'Venezuelan Bolvar   
',
                'leftsymbol' => 'Bs',
                'rightsymbol' => 'VEF',
                'isdefault' => 0,
                'currencyexchangerate' => '10.112680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '87',
            ),
            174 => 
            array (
                'id' => 85,
                'currencyname' => 'Vietnamese Dong   ',
                'leftsymbol' => '-',
                'rightsymbol' => 'VND',
                'isdefault' => 0,
                'currencyexchangerate' => '33.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '88',
            ),
            175 => 
            array (
                'id' => 86,
                'currencyname' => 'Central African Franc  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'XAF',
                'isdefault' => 0,
                'currencyexchangerate' => '780.529510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '89',
            ),
            176 => 
            array (
                'id' => 87,
                'currencyname' => 'East Caribbean Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'XCD',
                'isdefault' => 0,
                'currencyexchangerate' => '4.334010',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '90',
            ),
            177 => 
            array (
                'id' => 88,
                'currencyname' => 'West African Franc  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'XOF',
                'isdefault' => 0,
                'currencyexchangerate' => '743.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '91',
            ),
            178 => 
            array (
                'id' => 90,
                'currencyname' => 'South African Rand  
',
                'leftsymbol' => 'R',
                'rightsymbol' => 'ZAR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '93',
            ),
            179 => 
            array (
                'id' => 91,
                'currencyname' => ' IRANIAN RIAL',
                'leftsymbol' => '--',
                'rightsymbol' => 'IRR',
                'isdefault' => 0,
                'currencyexchangerate' => '125.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '94',
            ),
            180 => 
            array (
                'id' => 1,
                'currencyname' => 'US Dollar',
                'leftsymbol' => '$',
                'rightsymbol' => 'USD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.250000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '1',
            ),
            181 => 
            array (
                'id' => 2,
                'currencyname' => 'Pound Sterling',
                'leftsymbol' => '£',
                'rightsymbol' => 'GBP',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '2',
            ),
            182 => 
            array (
                'id' => 3,
                'currencyname' => 'Euro',
                'leftsymbol' => '€',
                'rightsymbol' => 'EUR',
                'isdefault' => 0,
                'currencyexchangerate' => '1.130000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '3',
            ),
            183 => 
            array (
                'id' => 4,
                'currencyname' => 'UAE  Dirham  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'AED',
                'isdefault' => 0,
                'currencyexchangerate' => '4.500000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '4',
            ),
            184 => 
            array (
                'id' => 5,
                'currencyname' => 'Argentine Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'ARS',
                'isdefault' => 0,
                'currencyexchangerate' => '70.830000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '5',
            ),
            185 => 
            array (
                'id' => 6,
                'currencyname' => 'Australian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'AUD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.709780',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '6',
            ),
            186 => 
            array (
                'id' => 7,
                'currencyname' => 'Aruban Florin   
',
                'leftsymbol' => 'ƒ',
                'rightsymbol' => 'AWG',
                'isdefault' => 0,
                'currencyexchangerate' => '2.873290',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '7',
            ),
            187 => 
            array (
                'id' => 8,
                'currencyname' => 'Bosnia and Herzegovina convertible mark
',
                'leftsymbol' => 'KM',
                'rightsymbol' => 'BAM',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '8',
            ),
            188 => 
            array (
                'id' => 9,
                'currencyname' => 'Barbadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BBD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.530000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '9',
            ),
            189 => 
            array (
                'id' => 10,
                'currencyname' => 'Bangladeshi Taka   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BDT',
                'isdefault' => 0,
                'currencyexchangerate' => '124.771300',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '10',
            ),
            190 => 
            array (
                'id' => 11,
                'currencyname' => 'Bulgarian Lev   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BGN',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '11',
            ),
            191 => 
            array (
                'id' => 12,
                'currencyname' => 'Bahraini Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BHD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.603550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '12',
            ),
            192 => 
            array (
                'id' => 13,
                'currencyname' => 'Bermudian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BMD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '13',
            ),
            193 => 
            array (
                'id' => 14,
                'currencyname' => 'Bolivian Boliviano   
',
                'leftsymbol' => '$b',
                'rightsymbol' => 'BOB',
                'isdefault' => 0,
                'currencyexchangerate' => '11.091860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '14',
            ),
            194 => 
            array (
                'id' => 15,
                'currencyname' => 'Brazilian Real   
',
                'leftsymbol' => 'R$',
                'rightsymbol' => 'BRL',
                'isdefault' => 0,
                'currencyexchangerate' => '3.572820',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '15',
            ),
            195 => 
            array (
                'id' => 16,
                'currencyname' => 'Bahamian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BSD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '16',
            ),
            196 => 
            array (
                'id' => 17,
                'currencyname' => 'Canadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CAD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.654690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '17',
            ),
            197 => 
            array (
                'id' => 18,
                'currencyname' => 'Swiss Franc   
',
                'leftsymbol' => 'CHF',
                'rightsymbol' => 'CHF',
                'isdefault' => 0,
                'currencyexchangerate' => '1.462400',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '18',
            ),
            198 => 
            array (
                'id' => 19,
                'currencyname' => 'Chilean Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CLP',
                'isdefault' => 0,
                'currencyexchangerate' => '805.001780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '19',
            ),
            199 => 
            array (
                'id' => 20,
                'currencyname' => 'Chinese Yuan   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'CNY',
                'isdefault' => 0,
                'currencyexchangerate' => '9.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '20',
            ),
            200 => 
            array (
                'id' => 21,
                'currencyname' => 'Colombian Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'COP',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '21',
            ),
            201 => 
            array (
                'id' => 22,
                'currencyname' => 'Czech Koruna   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'CZK',
                'isdefault' => 0,
                'currencyexchangerate' => '30.758570',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '22',
            ),
            202 => 
            array (
                'id' => 23,
                'currencyname' => 'Danish Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'DKK',
                'isdefault' => 0,
                'currencyexchangerate' => '8.877180',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '23',
            ),
            203 => 
            array (
                'id' => 24,
                'currencyname' => 'Egyptian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'EGP',
                'isdefault' => 0,
                'currencyexchangerate' => '11.061760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '24',
            ),
            204 => 
            array (
                'id' => 25,
                'currencyname' => 'Fijian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'FJD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.991230',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '26',
            ),
            205 => 
            array (
                'id' => 26,
                'currencyname' => 'Ghana Cedi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GHS',
                'isdefault' => 0,
                'currencyexchangerate' => '3.217870',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '28',
            ),
            206 => 
            array (
                'id' => 27,
                'currencyname' => 'Gambian Dalasi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GMD',
                'isdefault' => 0,
                'currencyexchangerate' => '52.329130',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '29',
            ),
            207 => 
            array (
                'id' => 28,
                'currencyname' => 'Guatemalan Quetzal   
',
                'leftsymbol' => 'Q',
                'rightsymbol' => 'GTQ',
                'isdefault' => 0,
                'currencyexchangerate' => '12.737980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '30',
            ),
            208 => 
            array (
                'id' => 29,
                'currencyname' => 'Hong Kong Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'HKD',
                'isdefault' => 0,
                'currencyexchangerate' => '12.440210',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '31',
            ),
            209 => 
            array (
                'id' => 30,
                'currencyname' => 'Croatian Kuna   
',
                'leftsymbol' => 'kn',
                'rightsymbol' => 'HRK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.059020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '32',
            ),
            210 => 
            array (
                'id' => 31,
                'currencyname' => 'Hungarian Forint   
',
                'leftsymbol' => 'Ft',
                'rightsymbol' => 'HUF',
                'isdefault' => 0,
                'currencyexchangerate' => '356.720970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '33',
            ),
            211 => 
            array (
                'id' => 32,
                'currencyname' => 'Indonesian Rupiah   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'IDR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '34',
            ),
            212 => 
            array (
                'id' => 33,
                'currencyname' => 'Israeli Sheqel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'ILS',
                'isdefault' => 0,
                'currencyexchangerate' => '5.712760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '35',
            ),
            213 => 
            array (
                'id' => 34,
                'currencyname' => 'Indian Rupee   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'INR',
                'isdefault' => 0,
                'currencyexchangerate' => '99.798550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '36',
            ),
            214 => 
            array (
                'id' => 35,
                'currencyname' => 'Icelandic Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'ISK',
                'isdefault' => 0,
                'currencyexchangerate' => '194.918020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '37',
            ),
            215 => 
            array (
                'id' => 36,
                'currencyname' => 'Jamaican Dollar   
',
                'leftsymbol' => 'J$',
                'rightsymbol' => 'JMD',
                'isdefault' => 0,
                'currencyexchangerate' => '164.210730',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '38',
            ),
            216 => 
            array (
                'id' => 37,
                'currencyname' => 'Jordanian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'JOD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.138080',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '39',
            ),
            217 => 
            array (
                'id' => 38,
                'currencyname' => 'Japanese Yen   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'JPY',
                'isdefault' => 0,
                'currencyexchangerate' => '158.367800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '40',
            ),
            218 => 
            array (
                'id' => 39,
                'currencyname' => 'Kenyan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KES',
                'isdefault' => 0,
                'currencyexchangerate' => '139.651360',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '41',
            ),
            219 => 
            array (
                'id' => 40,
                'currencyname' => 'Cambodian Riel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KHR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '42',
            ),
            220 => 
            array (
                'id' => 41,
                'currencyname' => 'South Korean Won  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KRW',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '43',
            ),
            221 => 
            array (
                'id' => 42,
                'currencyname' => 'Kuwaiti Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KWD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.454780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '44',
            ),
            222 => 
            array (
                'id' => 43,
                'currencyname' => 'Lao Kip   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LAK',
                'isdefault' => 0,
                'currencyexchangerate' => '12.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '45',
            ),
            223 => 
            array (
                'id' => 44,
                'currencyname' => 'Lebanese Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'LBP',
                'isdefault' => 0,
                'currencyexchangerate' => '2.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '46',
            ),
            224 => 
            array (
                'id' => 45,
                'currencyname' => 'Sri Lankan Rupee  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LKR',
                'isdefault' => 0,
                'currencyexchangerate' => '211.844720',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '47',
            ),
            225 => 
            array (
                'id' => 46,
                'currencyname' => 'Lithuanian Litas   
',
                'leftsymbol' => 'Lt',
                'rightsymbol' => 'LTL',
                'isdefault' => 0,
                'currencyexchangerate' => '4.108520',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '48',
            ),
            226 => 
            array (
                'id' => 47,
                'currencyname' => 'Latvian Lats   
',
                'leftsymbol' => 'Ls',
                'rightsymbol' => 'LVL',
                'isdefault' => 0,
                'currencyexchangerate' => '0.836270',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '49',
            ),
            227 => 
            array (
                'id' => 48,
                'currencyname' => 'Moroccan Dirham   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MAD',
                'isdefault' => 0,
                'currencyexchangerate' => '13.330910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '50',
            ),
            228 => 
            array (
                'id' => 49,
                'currencyname' => 'Moldovan Leu   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MDL',
                'isdefault' => 0,
                'currencyexchangerate' => '20.714900',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '51',
            ),
            229 => 
            array (
                'id' => 50,
                'currencyname' => 'Malagasy Ariary   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MGA',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '52',
            ),
            230 => 
            array (
                'id' => 51,
                'currencyname' => 'Macedonian Denar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MKD',
                'isdefault' => 0,
                'currencyexchangerate' => '73.116250',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '53',
            ),
            231 => 
            array (
                'id' => 52,
                'currencyname' => 'Mauritian Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MUR',
                'isdefault' => 0,
                'currencyexchangerate' => '49.439790',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '54',
            ),
            232 => 
            array (
                'id' => 53,
                'currencyname' => 'Maldivian Rufiyaa   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MVR',
                'isdefault' => 0,
                'currencyexchangerate' => '24.735960',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '55',
            ),
            233 => 
            array (
                'id' => 54,
                'currencyname' => 'Mexican Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'MXN',
                'isdefault' => 0,
                'currencyexchangerate' => '20.862090',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '56',
            ),
            234 => 
            array (
                'id' => 55,
                'currencyname' => 'Malaysian Ringgit   
',
                'leftsymbol' => 'RM',
                'rightsymbol' => 'MYR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.165040',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '57',
            ),
            235 => 
            array (
                'id' => 56,
                'currencyname' => 'Namibian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NAD',
                'isdefault' => 0,
                'currencyexchangerate' => '16.022370',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '58',
            ),
            236 => 
            array (
                'id' => 57,
                'currencyname' => 'Nigerian Naira   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NGN',
                'isdefault' => 0,
                'currencyexchangerate' => '257.472160',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '59',
            ),
            237 => 
            array (
                'id' => 58,
                'currencyname' => 'Norwegian Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'NOK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.633510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '60',
            ),
            238 => 
            array (
                'id' => 59,
                'currencyname' => 'Nepalese Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NPR',
                'isdefault' => 0,
                'currencyexchangerate' => '159.677680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '61',
            ),
            239 => 
            array (
                'id' => 60,
                'currencyname' => 'New Zealand Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NZD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.935980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '62',
            ),
            240 => 
            array (
                'id' => 61,
                'currencyname' => 'Omani Rial   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'OMR',
                'isdefault' => 0,
                'currencyexchangerate' => '0.617190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '63',
            ),
            241 => 
            array (
                'id' => 62,
                'currencyname' => 'Panamanian Balboa   
',
                'leftsymbol' => 'B/.',
                'rightsymbol' => 'PAB',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '64',
            ),
            242 => 
            array (
                'id' => 63,
                'currencyname' => 'Peruvian Sol   
',
                'leftsymbol' => 'S/.',
                'rightsymbol' => 'PEN',
                'isdefault' => 0,
                'currencyexchangerate' => '4.439910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '65',
            ),
            243 => 
            array (
                'id' => 64,
                'currencyname' => 'Philippine Peso   
',
                'leftsymbol' => 'Php',
                'rightsymbol' => 'PHP',
                'isdefault' => 0,
                'currencyexchangerate' => '69.464540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '66',
            ),
            244 => 
            array (
                'id' => 65,
                'currencyname' => 'Pakistani Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PKR',
                'isdefault' => 0,
                'currencyexchangerate' => '176.600000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '67',
            ),
            245 => 
            array (
                'id' => 66,
                'currencyname' => 'Polish Zloty   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PLN',
                'isdefault' => 0,
                'currencyexchangerate' => '5.026420',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '68',
            ),
            246 => 
            array (
                'id' => 67,
                'currencyname' => 'Paraguayan Guaran   
',
                'leftsymbol' => 'Gs',
                'rightsymbol' => 'PYG',
                'isdefault' => 0,
                'currencyexchangerate' => '7.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '69',
            ),
            247 => 
            array (
                'id' => 68,
                'currencyname' => 'Qatari Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'QAR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '70',
            ),
            248 => 
            array (
                'id' => 69,
                'currencyname' => 'Romanian Leu   
',
                'leftsymbol' => 'lei',
                'rightsymbol' => 'RON',
                'isdefault' => 0,
                'currencyexchangerate' => '5.312350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '71',
            ),
            249 => 
            array (
                'id' => 70,
                'currencyname' => 'Serbian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RSD',
                'isdefault' => 0,
                'currencyexchangerate' => '123.341860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '72',
            ),
            250 => 
            array (
                'id' => 71,
                'currencyname' => 'Russian Rouble   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RUB',
                'isdefault' => 0,
                'currencyexchangerate' => '79.820000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '73',
            ),
            251 => 
            array (
                'id' => 72,
                'currencyname' => 'Saudi Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SAR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.019460',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '74',
            ),
            252 => 
            array (
                'id' => 73,
                'currencyname' => 'Seychellois Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SCR',
                'isdefault' => 0,
                'currencyexchangerate' => '19.351020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '75',
            ),
            253 => 
            array (
                'id' => 74,
                'currencyname' => 'Swedish Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'SEK',
                'isdefault' => 0,
                'currencyexchangerate' => '10.307350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '76',
            ),
            254 => 
            array (
                'id' => 75,
                'currencyname' => 'Singapore Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'SGD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.012970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '77',
            ),
            255 => 
            array (
                'id' => 76,
                'currencyname' => 'Syrian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'SYP',
                'isdefault' => 0,
                'currencyexchangerate' => '217.743690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '78',
            ),
            256 => 
            array (
                'id' => 77,
                'currencyname' => 'Thai Baht   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'THB',
                'isdefault' => 0,
                'currencyexchangerate' => '50.065800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '79',
            ),
            257 => 
            array (
                'id' => 78,
                'currencyname' => 'Tunisian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'TND',
                'isdefault' => 0,
                'currencyexchangerate' => '2.645410',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '80',
            ),
            258 => 
            array (
                'id' => 79,
                'currencyname' => 'Turkish Lira   
',
                'leftsymbol' => 'TL',
                'rightsymbol' => 'TRY',
                'isdefault' => 0,
                'currencyexchangerate' => '3.227630',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '81',
            ),
            259 => 
            array (
                'id' => 80,
                'currencyname' => 'Taiwanese Dollar   
',
                'leftsymbol' => 'NT$',
                'rightsymbol' => 'TWD',
                'isdefault' => 0,
                'currencyexchangerate' => '47.473470',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '82',
            ),
            260 => 
            array (
                'id' => 81,
                'currencyname' => 'Ukraine Hryvnia   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UAH',
                'isdefault' => 0,
                'currencyexchangerate' => '13.127200',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '83',
            ),
            261 => 
            array (
                'id' => 82,
                'currencyname' => 'Ugandan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UGX',
                'isdefault' => 0,
                'currencyexchangerate' => '4.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '84',
            ),
            262 => 
            array (
                'id' => 83,
                'currencyname' => 'Uruguayan Peso   
',
                'leftsymbol' => '$U',
                'rightsymbol' => 'UYU',
                'isdefault' => 0,
                'currencyexchangerate' => '34.511540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '86',
            ),
            263 => 
            array (
                'id' => 84,
                'currencyname' => 'Venezuelan Bolvar   
',
                'leftsymbol' => 'Bs',
                'rightsymbol' => 'VEF',
                'isdefault' => 0,
                'currencyexchangerate' => '10.112680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '87',
            ),
            264 => 
            array (
                'id' => 85,
                'currencyname' => 'Vietnamese Dong   ',
                'leftsymbol' => '-',
                'rightsymbol' => 'VND',
                'isdefault' => 0,
                'currencyexchangerate' => '33.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '88',
            ),
            265 => 
            array (
                'id' => 86,
                'currencyname' => 'Central African Franc  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'XAF',
                'isdefault' => 0,
                'currencyexchangerate' => '780.529510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '89',
            ),
            266 => 
            array (
                'id' => 87,
                'currencyname' => 'East Caribbean Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'XCD',
                'isdefault' => 0,
                'currencyexchangerate' => '4.334010',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '90',
            ),
            267 => 
            array (
                'id' => 88,
                'currencyname' => 'West African Franc  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'XOF',
                'isdefault' => 0,
                'currencyexchangerate' => '743.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '91',
            ),
            268 => 
            array (
                'id' => 90,
                'currencyname' => 'South African Rand  
',
                'leftsymbol' => 'R',
                'rightsymbol' => 'ZAR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '93',
            ),
            269 => 
            array (
                'id' => 91,
                'currencyname' => ' IRANIAN RIAL',
                'leftsymbol' => '--',
                'rightsymbol' => 'IRR',
                'isdefault' => 0,
                'currencyexchangerate' => '125.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '94',
            ),
            270 => 
            array (
                'id' => 1,
                'currencyname' => 'US Dollar',
                'leftsymbol' => '$',
                'rightsymbol' => 'USD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.250000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '1',
            ),
            271 => 
            array (
                'id' => 2,
                'currencyname' => 'Pound Sterling',
                'leftsymbol' => '£',
                'rightsymbol' => 'GBP',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '2',
            ),
            272 => 
            array (
                'id' => 3,
                'currencyname' => 'Euro',
                'leftsymbol' => '€',
                'rightsymbol' => 'EUR',
                'isdefault' => 0,
                'currencyexchangerate' => '1.130000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '3',
            ),
            273 => 
            array (
                'id' => 4,
                'currencyname' => 'UAE  Dirham  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'AED',
                'isdefault' => 0,
                'currencyexchangerate' => '4.500000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '4',
            ),
            274 => 
            array (
                'id' => 5,
                'currencyname' => 'Argentine Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'ARS',
                'isdefault' => 0,
                'currencyexchangerate' => '70.830000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '5',
            ),
            275 => 
            array (
                'id' => 6,
                'currencyname' => 'Australian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'AUD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.709780',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '6',
            ),
            276 => 
            array (
                'id' => 7,
                'currencyname' => 'Aruban Florin   
',
                'leftsymbol' => 'ƒ',
                'rightsymbol' => 'AWG',
                'isdefault' => 0,
                'currencyexchangerate' => '2.873290',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '7',
            ),
            277 => 
            array (
                'id' => 8,
                'currencyname' => 'Bosnia and Herzegovina convertible mark
',
                'leftsymbol' => 'KM',
                'rightsymbol' => 'BAM',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '8',
            ),
            278 => 
            array (
                'id' => 9,
                'currencyname' => 'Barbadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BBD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.530000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '9',
            ),
            279 => 
            array (
                'id' => 10,
                'currencyname' => 'Bangladeshi Taka   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BDT',
                'isdefault' => 0,
                'currencyexchangerate' => '124.771300',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '10',
            ),
            280 => 
            array (
                'id' => 11,
                'currencyname' => 'Bulgarian Lev   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BGN',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '11',
            ),
            281 => 
            array (
                'id' => 12,
                'currencyname' => 'Bahraini Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BHD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.603550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '12',
            ),
            282 => 
            array (
                'id' => 13,
                'currencyname' => 'Bermudian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BMD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '13',
            ),
            283 => 
            array (
                'id' => 14,
                'currencyname' => 'Bolivian Boliviano   
',
                'leftsymbol' => '$b',
                'rightsymbol' => 'BOB',
                'isdefault' => 0,
                'currencyexchangerate' => '11.091860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '14',
            ),
            284 => 
            array (
                'id' => 15,
                'currencyname' => 'Brazilian Real   
',
                'leftsymbol' => 'R$',
                'rightsymbol' => 'BRL',
                'isdefault' => 0,
                'currencyexchangerate' => '3.572820',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '15',
            ),
            285 => 
            array (
                'id' => 16,
                'currencyname' => 'Bahamian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BSD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '16',
            ),
            286 => 
            array (
                'id' => 17,
                'currencyname' => 'Canadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CAD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.654690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '17',
            ),
            287 => 
            array (
                'id' => 18,
                'currencyname' => 'Swiss Franc   
',
                'leftsymbol' => 'CHF',
                'rightsymbol' => 'CHF',
                'isdefault' => 0,
                'currencyexchangerate' => '1.462400',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '18',
            ),
            288 => 
            array (
                'id' => 19,
                'currencyname' => 'Chilean Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CLP',
                'isdefault' => 0,
                'currencyexchangerate' => '805.001780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '19',
            ),
            289 => 
            array (
                'id' => 20,
                'currencyname' => 'Chinese Yuan   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'CNY',
                'isdefault' => 0,
                'currencyexchangerate' => '9.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '20',
            ),
            290 => 
            array (
                'id' => 21,
                'currencyname' => 'Colombian Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'COP',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '21',
            ),
            291 => 
            array (
                'id' => 22,
                'currencyname' => 'Czech Koruna   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'CZK',
                'isdefault' => 0,
                'currencyexchangerate' => '30.758570',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '22',
            ),
            292 => 
            array (
                'id' => 23,
                'currencyname' => 'Danish Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'DKK',
                'isdefault' => 0,
                'currencyexchangerate' => '8.877180',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '23',
            ),
            293 => 
            array (
                'id' => 24,
                'currencyname' => 'Egyptian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'EGP',
                'isdefault' => 0,
                'currencyexchangerate' => '11.061760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '24',
            ),
            294 => 
            array (
                'id' => 25,
                'currencyname' => 'Fijian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'FJD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.991230',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '26',
            ),
            295 => 
            array (
                'id' => 26,
                'currencyname' => 'Ghana Cedi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GHS',
                'isdefault' => 0,
                'currencyexchangerate' => '3.217870',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '28',
            ),
            296 => 
            array (
                'id' => 27,
                'currencyname' => 'Gambian Dalasi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GMD',
                'isdefault' => 0,
                'currencyexchangerate' => '52.329130',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '29',
            ),
            297 => 
            array (
                'id' => 28,
                'currencyname' => 'Guatemalan Quetzal   
',
                'leftsymbol' => 'Q',
                'rightsymbol' => 'GTQ',
                'isdefault' => 0,
                'currencyexchangerate' => '12.737980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '30',
            ),
            298 => 
            array (
                'id' => 29,
                'currencyname' => 'Hong Kong Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'HKD',
                'isdefault' => 0,
                'currencyexchangerate' => '12.440210',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '31',
            ),
            299 => 
            array (
                'id' => 30,
                'currencyname' => 'Croatian Kuna   
',
                'leftsymbol' => 'kn',
                'rightsymbol' => 'HRK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.059020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '32',
            ),
            300 => 
            array (
                'id' => 31,
                'currencyname' => 'Hungarian Forint   
',
                'leftsymbol' => 'Ft',
                'rightsymbol' => 'HUF',
                'isdefault' => 0,
                'currencyexchangerate' => '356.720970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '33',
            ),
            301 => 
            array (
                'id' => 32,
                'currencyname' => 'Indonesian Rupiah   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'IDR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '34',
            ),
            302 => 
            array (
                'id' => 33,
                'currencyname' => 'Israeli Sheqel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'ILS',
                'isdefault' => 0,
                'currencyexchangerate' => '5.712760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '35',
            ),
            303 => 
            array (
                'id' => 34,
                'currencyname' => 'Indian Rupee   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'INR',
                'isdefault' => 0,
                'currencyexchangerate' => '99.798550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '36',
            ),
            304 => 
            array (
                'id' => 35,
                'currencyname' => 'Icelandic Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'ISK',
                'isdefault' => 0,
                'currencyexchangerate' => '194.918020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '37',
            ),
            305 => 
            array (
                'id' => 36,
                'currencyname' => 'Jamaican Dollar   
',
                'leftsymbol' => 'J$',
                'rightsymbol' => 'JMD',
                'isdefault' => 0,
                'currencyexchangerate' => '164.210730',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '38',
            ),
            306 => 
            array (
                'id' => 37,
                'currencyname' => 'Jordanian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'JOD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.138080',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '39',
            ),
            307 => 
            array (
                'id' => 38,
                'currencyname' => 'Japanese Yen   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'JPY',
                'isdefault' => 0,
                'currencyexchangerate' => '158.367800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '40',
            ),
            308 => 
            array (
                'id' => 39,
                'currencyname' => 'Kenyan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KES',
                'isdefault' => 0,
                'currencyexchangerate' => '139.651360',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '41',
            ),
            309 => 
            array (
                'id' => 40,
                'currencyname' => 'Cambodian Riel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KHR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '42',
            ),
            310 => 
            array (
                'id' => 41,
                'currencyname' => 'South Korean Won  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KRW',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '43',
            ),
            311 => 
            array (
                'id' => 42,
                'currencyname' => 'Kuwaiti Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KWD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.454780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '44',
            ),
            312 => 
            array (
                'id' => 43,
                'currencyname' => 'Lao Kip   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LAK',
                'isdefault' => 0,
                'currencyexchangerate' => '12.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '45',
            ),
            313 => 
            array (
                'id' => 44,
                'currencyname' => 'Lebanese Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'LBP',
                'isdefault' => 0,
                'currencyexchangerate' => '2.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '46',
            ),
            314 => 
            array (
                'id' => 45,
                'currencyname' => 'Sri Lankan Rupee  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LKR',
                'isdefault' => 0,
                'currencyexchangerate' => '211.844720',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '47',
            ),
            315 => 
            array (
                'id' => 46,
                'currencyname' => 'Lithuanian Litas   
',
                'leftsymbol' => 'Lt',
                'rightsymbol' => 'LTL',
                'isdefault' => 0,
                'currencyexchangerate' => '4.108520',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '48',
            ),
            316 => 
            array (
                'id' => 47,
                'currencyname' => 'Latvian Lats   
',
                'leftsymbol' => 'Ls',
                'rightsymbol' => 'LVL',
                'isdefault' => 0,
                'currencyexchangerate' => '0.836270',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '49',
            ),
            317 => 
            array (
                'id' => 48,
                'currencyname' => 'Moroccan Dirham   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MAD',
                'isdefault' => 0,
                'currencyexchangerate' => '13.330910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '50',
            ),
            318 => 
            array (
                'id' => 49,
                'currencyname' => 'Moldovan Leu   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MDL',
                'isdefault' => 0,
                'currencyexchangerate' => '20.714900',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '51',
            ),
            319 => 
            array (
                'id' => 50,
                'currencyname' => 'Malagasy Ariary   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MGA',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '52',
            ),
            320 => 
            array (
                'id' => 51,
                'currencyname' => 'Macedonian Denar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MKD',
                'isdefault' => 0,
                'currencyexchangerate' => '73.116250',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '53',
            ),
            321 => 
            array (
                'id' => 52,
                'currencyname' => 'Mauritian Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MUR',
                'isdefault' => 0,
                'currencyexchangerate' => '49.439790',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '54',
            ),
            322 => 
            array (
                'id' => 53,
                'currencyname' => 'Maldivian Rufiyaa   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MVR',
                'isdefault' => 0,
                'currencyexchangerate' => '24.735960',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '55',
            ),
            323 => 
            array (
                'id' => 54,
                'currencyname' => 'Mexican Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'MXN',
                'isdefault' => 0,
                'currencyexchangerate' => '20.862090',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '56',
            ),
            324 => 
            array (
                'id' => 55,
                'currencyname' => 'Malaysian Ringgit   
',
                'leftsymbol' => 'RM',
                'rightsymbol' => 'MYR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.165040',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '57',
            ),
            325 => 
            array (
                'id' => 56,
                'currencyname' => 'Namibian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NAD',
                'isdefault' => 0,
                'currencyexchangerate' => '16.022370',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '58',
            ),
            326 => 
            array (
                'id' => 57,
                'currencyname' => 'Nigerian Naira   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NGN',
                'isdefault' => 0,
                'currencyexchangerate' => '257.472160',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '59',
            ),
            327 => 
            array (
                'id' => 58,
                'currencyname' => 'Norwegian Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'NOK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.633510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '60',
            ),
            328 => 
            array (
                'id' => 59,
                'currencyname' => 'Nepalese Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NPR',
                'isdefault' => 0,
                'currencyexchangerate' => '159.677680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '61',
            ),
            329 => 
            array (
                'id' => 60,
                'currencyname' => 'New Zealand Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NZD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.935980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '62',
            ),
            330 => 
            array (
                'id' => 61,
                'currencyname' => 'Omani Rial   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'OMR',
                'isdefault' => 0,
                'currencyexchangerate' => '0.617190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '63',
            ),
            331 => 
            array (
                'id' => 62,
                'currencyname' => 'Panamanian Balboa   
',
                'leftsymbol' => 'B/.',
                'rightsymbol' => 'PAB',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '64',
            ),
            332 => 
            array (
                'id' => 63,
                'currencyname' => 'Peruvian Sol   
',
                'leftsymbol' => 'S/.',
                'rightsymbol' => 'PEN',
                'isdefault' => 0,
                'currencyexchangerate' => '4.439910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '65',
            ),
            333 => 
            array (
                'id' => 64,
                'currencyname' => 'Philippine Peso   
',
                'leftsymbol' => 'Php',
                'rightsymbol' => 'PHP',
                'isdefault' => 0,
                'currencyexchangerate' => '69.464540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '66',
            ),
            334 => 
            array (
                'id' => 65,
                'currencyname' => 'Pakistani Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PKR',
                'isdefault' => 0,
                'currencyexchangerate' => '176.600000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '67',
            ),
            335 => 
            array (
                'id' => 66,
                'currencyname' => 'Polish Zloty   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PLN',
                'isdefault' => 0,
                'currencyexchangerate' => '5.026420',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '68',
            ),
            336 => 
            array (
                'id' => 67,
                'currencyname' => 'Paraguayan Guaran   
',
                'leftsymbol' => 'Gs',
                'rightsymbol' => 'PYG',
                'isdefault' => 0,
                'currencyexchangerate' => '7.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '69',
            ),
            337 => 
            array (
                'id' => 68,
                'currencyname' => 'Qatari Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'QAR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '70',
            ),
            338 => 
            array (
                'id' => 69,
                'currencyname' => 'Romanian Leu   
',
                'leftsymbol' => 'lei',
                'rightsymbol' => 'RON',
                'isdefault' => 0,
                'currencyexchangerate' => '5.312350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '71',
            ),
            339 => 
            array (
                'id' => 70,
                'currencyname' => 'Serbian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RSD',
                'isdefault' => 0,
                'currencyexchangerate' => '123.341860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '72',
            ),
            340 => 
            array (
                'id' => 71,
                'currencyname' => 'Russian Rouble   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RUB',
                'isdefault' => 0,
                'currencyexchangerate' => '79.820000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '73',
            ),
            341 => 
            array (
                'id' => 72,
                'currencyname' => 'Saudi Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SAR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.019460',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '74',
            ),
            342 => 
            array (
                'id' => 73,
                'currencyname' => 'Seychellois Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SCR',
                'isdefault' => 0,
                'currencyexchangerate' => '19.351020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '75',
            ),
            343 => 
            array (
                'id' => 74,
                'currencyname' => 'Swedish Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'SEK',
                'isdefault' => 0,
                'currencyexchangerate' => '10.307350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '76',
            ),
            344 => 
            array (
                'id' => 75,
                'currencyname' => 'Singapore Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'SGD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.012970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '77',
            ),
            345 => 
            array (
                'id' => 76,
                'currencyname' => 'Syrian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'SYP',
                'isdefault' => 0,
                'currencyexchangerate' => '217.743690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '78',
            ),
            346 => 
            array (
                'id' => 77,
                'currencyname' => 'Thai Baht   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'THB',
                'isdefault' => 0,
                'currencyexchangerate' => '50.065800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '79',
            ),
            347 => 
            array (
                'id' => 78,
                'currencyname' => 'Tunisian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'TND',
                'isdefault' => 0,
                'currencyexchangerate' => '2.645410',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '80',
            ),
            348 => 
            array (
                'id' => 79,
                'currencyname' => 'Turkish Lira   
',
                'leftsymbol' => 'TL',
                'rightsymbol' => 'TRY',
                'isdefault' => 0,
                'currencyexchangerate' => '3.227630',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '81',
            ),
            349 => 
            array (
                'id' => 80,
                'currencyname' => 'Taiwanese Dollar   
',
                'leftsymbol' => 'NT$',
                'rightsymbol' => 'TWD',
                'isdefault' => 0,
                'currencyexchangerate' => '47.473470',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '82',
            ),
            350 => 
            array (
                'id' => 81,
                'currencyname' => 'Ukraine Hryvnia   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UAH',
                'isdefault' => 0,
                'currencyexchangerate' => '13.127200',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '83',
            ),
            351 => 
            array (
                'id' => 82,
                'currencyname' => 'Ugandan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UGX',
                'isdefault' => 0,
                'currencyexchangerate' => '4.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '84',
            ),
            352 => 
            array (
                'id' => 83,
                'currencyname' => 'Uruguayan Peso   
',
                'leftsymbol' => '$U',
                'rightsymbol' => 'UYU',
                'isdefault' => 0,
                'currencyexchangerate' => '34.511540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '86',
            ),
            353 => 
            array (
                'id' => 84,
                'currencyname' => 'Venezuelan Bolvar   
',
                'leftsymbol' => 'Bs',
                'rightsymbol' => 'VEF',
                'isdefault' => 0,
                'currencyexchangerate' => '10.112680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '87',
            ),
            354 => 
            array (
                'id' => 85,
                'currencyname' => 'Vietnamese Dong   ',
                'leftsymbol' => '-',
                'rightsymbol' => 'VND',
                'isdefault' => 0,
                'currencyexchangerate' => '33.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '88',
            ),
            355 => 
            array (
                'id' => 86,
                'currencyname' => 'Central African Franc  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'XAF',
                'isdefault' => 0,
                'currencyexchangerate' => '780.529510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '89',
            ),
            356 => 
            array (
                'id' => 87,
                'currencyname' => 'East Caribbean Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'XCD',
                'isdefault' => 0,
                'currencyexchangerate' => '4.334010',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '90',
            ),
            357 => 
            array (
                'id' => 88,
                'currencyname' => 'West African Franc  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'XOF',
                'isdefault' => 0,
                'currencyexchangerate' => '743.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '91',
            ),
            358 => 
            array (
                'id' => 90,
                'currencyname' => 'South African Rand  
',
                'leftsymbol' => 'R',
                'rightsymbol' => 'ZAR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '93',
            ),
            359 => 
            array (
                'id' => 91,
                'currencyname' => ' IRANIAN RIAL',
                'leftsymbol' => '--',
                'rightsymbol' => 'IRR',
                'isdefault' => 0,
                'currencyexchangerate' => '125.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '94',
            ),
            360 => 
            array (
                'id' => 1,
                'currencyname' => 'US Dollar',
                'leftsymbol' => '$',
                'rightsymbol' => 'USD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.250000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '1',
            ),
            361 => 
            array (
                'id' => 2,
                'currencyname' => 'Pound Sterling',
                'leftsymbol' => '£',
                'rightsymbol' => 'GBP',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '2',
            ),
            362 => 
            array (
                'id' => 3,
                'currencyname' => 'Euro',
                'leftsymbol' => '€',
                'rightsymbol' => 'EUR',
                'isdefault' => 0,
                'currencyexchangerate' => '1.130000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '3',
            ),
            363 => 
            array (
                'id' => 4,
                'currencyname' => 'UAE  Dirham  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'AED',
                'isdefault' => 0,
                'currencyexchangerate' => '4.500000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '4',
            ),
            364 => 
            array (
                'id' => 5,
                'currencyname' => 'Argentine Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'ARS',
                'isdefault' => 0,
                'currencyexchangerate' => '70.830000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '5',
            ),
            365 => 
            array (
                'id' => 6,
                'currencyname' => 'Australian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'AUD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.709780',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '6',
            ),
            366 => 
            array (
                'id' => 7,
                'currencyname' => 'Aruban Florin   
',
                'leftsymbol' => 'ƒ',
                'rightsymbol' => 'AWG',
                'isdefault' => 0,
                'currencyexchangerate' => '2.873290',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '7',
            ),
            367 => 
            array (
                'id' => 8,
                'currencyname' => 'Bosnia and Herzegovina convertible mark
',
                'leftsymbol' => 'KM',
                'rightsymbol' => 'BAM',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '8',
            ),
            368 => 
            array (
                'id' => 9,
                'currencyname' => 'Barbadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BBD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.530000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '9',
            ),
            369 => 
            array (
                'id' => 10,
                'currencyname' => 'Bangladeshi Taka   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BDT',
                'isdefault' => 0,
                'currencyexchangerate' => '124.771300',
                'isactive' => 0,
                'clientdisplay' => 1,
                'currencyid' => '10',
            ),
            370 => 
            array (
                'id' => 11,
                'currencyname' => 'Bulgarian Lev   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BGN',
                'isdefault' => 0,
                'currencyexchangerate' => '2.327260',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '11',
            ),
            371 => 
            array (
                'id' => 12,
                'currencyname' => 'Bahraini Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'BHD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.603550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '12',
            ),
            372 => 
            array (
                'id' => 13,
                'currencyname' => 'Bermudian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BMD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '13',
            ),
            373 => 
            array (
                'id' => 14,
                'currencyname' => 'Bolivian Boliviano   
',
                'leftsymbol' => '$b',
                'rightsymbol' => 'BOB',
                'isdefault' => 0,
                'currencyexchangerate' => '11.091860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '14',
            ),
            374 => 
            array (
                'id' => 15,
                'currencyname' => 'Brazilian Real   
',
                'leftsymbol' => 'R$',
                'rightsymbol' => 'BRL',
                'isdefault' => 0,
                'currencyexchangerate' => '3.572820',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '15',
            ),
            375 => 
            array (
                'id' => 16,
                'currencyname' => 'Bahamian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'BSD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '16',
            ),
            376 => 
            array (
                'id' => 17,
                'currencyname' => 'Canadian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CAD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.654690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '17',
            ),
            377 => 
            array (
                'id' => 18,
                'currencyname' => 'Swiss Franc   
',
                'leftsymbol' => 'CHF',
                'rightsymbol' => 'CHF',
                'isdefault' => 0,
                'currencyexchangerate' => '1.462400',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '18',
            ),
            378 => 
            array (
                'id' => 19,
                'currencyname' => 'Chilean Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'CLP',
                'isdefault' => 0,
                'currencyexchangerate' => '805.001780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '19',
            ),
            379 => 
            array (
                'id' => 20,
                'currencyname' => 'Chinese Yuan   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'CNY',
                'isdefault' => 0,
                'currencyexchangerate' => '9.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '20',
            ),
            380 => 
            array (
                'id' => 21,
                'currencyname' => 'Colombian Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'COP',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '21',
            ),
            381 => 
            array (
                'id' => 22,
                'currencyname' => 'Czech Koruna   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'CZK',
                'isdefault' => 0,
                'currencyexchangerate' => '30.758570',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '22',
            ),
            382 => 
            array (
                'id' => 23,
                'currencyname' => 'Danish Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'DKK',
                'isdefault' => 0,
                'currencyexchangerate' => '8.877180',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '23',
            ),
            383 => 
            array (
                'id' => 24,
                'currencyname' => 'Egyptian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'EGP',
                'isdefault' => 0,
                'currencyexchangerate' => '11.061760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '24',
            ),
            384 => 
            array (
                'id' => 25,
                'currencyname' => 'Fijian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'FJD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.991230',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '26',
            ),
            385 => 
            array (
                'id' => 26,
                'currencyname' => 'Ghana Cedi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GHS',
                'isdefault' => 0,
                'currencyexchangerate' => '3.217870',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '28',
            ),
            386 => 
            array (
                'id' => 27,
                'currencyname' => 'Gambian Dalasi   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'GMD',
                'isdefault' => 0,
                'currencyexchangerate' => '52.329130',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '29',
            ),
            387 => 
            array (
                'id' => 28,
                'currencyname' => 'Guatemalan Quetzal   
',
                'leftsymbol' => 'Q',
                'rightsymbol' => 'GTQ',
                'isdefault' => 0,
                'currencyexchangerate' => '12.737980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '30',
            ),
            388 => 
            array (
                'id' => 29,
                'currencyname' => 'Hong Kong Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'HKD',
                'isdefault' => 0,
                'currencyexchangerate' => '12.440210',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '31',
            ),
            389 => 
            array (
                'id' => 30,
                'currencyname' => 'Croatian Kuna   
',
                'leftsymbol' => 'kn',
                'rightsymbol' => 'HRK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.059020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '32',
            ),
            390 => 
            array (
                'id' => 31,
                'currencyname' => 'Hungarian Forint   
',
                'leftsymbol' => 'Ft',
                'rightsymbol' => 'HUF',
                'isdefault' => 0,
                'currencyexchangerate' => '356.720970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '33',
            ),
            391 => 
            array (
                'id' => 32,
                'currencyname' => 'Indonesian Rupiah   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'IDR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '34',
            ),
            392 => 
            array (
                'id' => 33,
                'currencyname' => 'Israeli Sheqel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'ILS',
                'isdefault' => 0,
                'currencyexchangerate' => '5.712760',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '35',
            ),
            393 => 
            array (
                'id' => 34,
                'currencyname' => 'Indian Rupee   
',
                'leftsymbol' => 'Rp',
                'rightsymbol' => 'INR',
                'isdefault' => 0,
                'currencyexchangerate' => '99.798550',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '36',
            ),
            394 => 
            array (
                'id' => 35,
                'currencyname' => 'Icelandic Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'ISK',
                'isdefault' => 0,
                'currencyexchangerate' => '194.918020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '37',
            ),
            395 => 
            array (
                'id' => 36,
                'currencyname' => 'Jamaican Dollar   
',
                'leftsymbol' => 'J$',
                'rightsymbol' => 'JMD',
                'isdefault' => 0,
                'currencyexchangerate' => '164.210730',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '38',
            ),
            396 => 
            array (
                'id' => 37,
                'currencyname' => 'Jordanian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'JOD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.138080',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '39',
            ),
            397 => 
            array (
                'id' => 38,
                'currencyname' => 'Japanese Yen   
',
                'leftsymbol' => '¥',
                'rightsymbol' => 'JPY',
                'isdefault' => 0,
                'currencyexchangerate' => '158.367800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '40',
            ),
            398 => 
            array (
                'id' => 39,
                'currencyname' => 'Kenyan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KES',
                'isdefault' => 0,
                'currencyexchangerate' => '139.651360',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '41',
            ),
            399 => 
            array (
                'id' => 40,
                'currencyname' => 'Cambodian Riel   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KHR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '42',
            ),
            400 => 
            array (
                'id' => 41,
                'currencyname' => 'South Korean Won  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KRW',
                'isdefault' => 0,
                'currencyexchangerate' => '1.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '43',
            ),
            401 => 
            array (
                'id' => 42,
                'currencyname' => 'Kuwaiti Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'KWD',
                'isdefault' => 0,
                'currencyexchangerate' => '0.454780',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '44',
            ),
            402 => 
            array (
                'id' => 43,
                'currencyname' => 'Lao Kip   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LAK',
                'isdefault' => 0,
                'currencyexchangerate' => '12.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '45',
            ),
            403 => 
            array (
                'id' => 44,
                'currencyname' => 'Lebanese Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'LBP',
                'isdefault' => 0,
                'currencyexchangerate' => '2.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '46',
            ),
            404 => 
            array (
                'id' => 45,
                'currencyname' => 'Sri Lankan Rupee  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'LKR',
                'isdefault' => 0,
                'currencyexchangerate' => '211.844720',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '47',
            ),
            405 => 
            array (
                'id' => 46,
                'currencyname' => 'Lithuanian Litas   
',
                'leftsymbol' => 'Lt',
                'rightsymbol' => 'LTL',
                'isdefault' => 0,
                'currencyexchangerate' => '4.108520',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '48',
            ),
            406 => 
            array (
                'id' => 47,
                'currencyname' => 'Latvian Lats   
',
                'leftsymbol' => 'Ls',
                'rightsymbol' => 'LVL',
                'isdefault' => 0,
                'currencyexchangerate' => '0.836270',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '49',
            ),
            407 => 
            array (
                'id' => 48,
                'currencyname' => 'Moroccan Dirham   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MAD',
                'isdefault' => 0,
                'currencyexchangerate' => '13.330910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '50',
            ),
            408 => 
            array (
                'id' => 49,
                'currencyname' => 'Moldovan Leu   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MDL',
                'isdefault' => 0,
                'currencyexchangerate' => '20.714900',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '51',
            ),
            409 => 
            array (
                'id' => 50,
                'currencyname' => 'Malagasy Ariary   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MGA',
                'isdefault' => 0,
                'currencyexchangerate' => '3.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '52',
            ),
            410 => 
            array (
                'id' => 51,
                'currencyname' => 'Macedonian Denar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MKD',
                'isdefault' => 0,
                'currencyexchangerate' => '73.116250',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '53',
            ),
            411 => 
            array (
                'id' => 52,
                'currencyname' => 'Mauritian Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MUR',
                'isdefault' => 0,
                'currencyexchangerate' => '49.439790',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '54',
            ),
            412 => 
            array (
                'id' => 53,
                'currencyname' => 'Maldivian Rufiyaa   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'MVR',
                'isdefault' => 0,
                'currencyexchangerate' => '24.735960',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '55',
            ),
            413 => 
            array (
                'id' => 54,
                'currencyname' => 'Mexican Peso   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'MXN',
                'isdefault' => 0,
                'currencyexchangerate' => '20.862090',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '56',
            ),
            414 => 
            array (
                'id' => 55,
                'currencyname' => 'Malaysian Ringgit   
',
                'leftsymbol' => 'RM',
                'rightsymbol' => 'MYR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.165040',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '57',
            ),
            415 => 
            array (
                'id' => 56,
                'currencyname' => 'Namibian Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NAD',
                'isdefault' => 0,
                'currencyexchangerate' => '16.022370',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '58',
            ),
            416 => 
            array (
                'id' => 57,
                'currencyname' => 'Nigerian Naira   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NGN',
                'isdefault' => 0,
                'currencyexchangerate' => '257.472160',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '59',
            ),
            417 => 
            array (
                'id' => 58,
                'currencyname' => 'Norwegian Krone   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'NOK',
                'isdefault' => 0,
                'currencyexchangerate' => '9.633510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '60',
            ),
            418 => 
            array (
                'id' => 59,
                'currencyname' => 'Nepalese Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'NPR',
                'isdefault' => 0,
                'currencyexchangerate' => '159.677680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '61',
            ),
            419 => 
            array (
                'id' => 60,
                'currencyname' => 'New Zealand Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'NZD',
                'isdefault' => 0,
                'currencyexchangerate' => '1.935980',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '62',
            ),
            420 => 
            array (
                'id' => 61,
                'currencyname' => 'Omani Rial   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'OMR',
                'isdefault' => 0,
                'currencyexchangerate' => '0.617190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '63',
            ),
            421 => 
            array (
                'id' => 62,
                'currencyname' => 'Panamanian Balboa   
',
                'leftsymbol' => 'B/.',
                'rightsymbol' => 'PAB',
                'isdefault' => 0,
                'currencyexchangerate' => '1.605190',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '64',
            ),
            422 => 
            array (
                'id' => 63,
                'currencyname' => 'Peruvian Sol   
',
                'leftsymbol' => 'S/.',
                'rightsymbol' => 'PEN',
                'isdefault' => 0,
                'currencyexchangerate' => '4.439910',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '65',
            ),
            423 => 
            array (
                'id' => 64,
                'currencyname' => 'Philippine Peso   
',
                'leftsymbol' => 'Php',
                'rightsymbol' => 'PHP',
                'isdefault' => 0,
                'currencyexchangerate' => '69.464540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '66',
            ),
            424 => 
            array (
                'id' => 65,
                'currencyname' => 'Pakistani Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PKR',
                'isdefault' => 0,
                'currencyexchangerate' => '176.600000',
                'isactive' => 1,
                'clientdisplay' => 0,
                'currencyid' => '67',
            ),
            425 => 
            array (
                'id' => 66,
                'currencyname' => 'Polish Zloty   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'PLN',
                'isdefault' => 0,
                'currencyexchangerate' => '5.026420',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '68',
            ),
            426 => 
            array (
                'id' => 67,
                'currencyname' => 'Paraguayan Guaran   
',
                'leftsymbol' => 'Gs',
                'rightsymbol' => 'PYG',
                'isdefault' => 0,
                'currencyexchangerate' => '7.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '69',
            ),
            427 => 
            array (
                'id' => 68,
                'currencyname' => 'Qatari Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'QAR',
                'isdefault' => 0,
                'currencyexchangerate' => '5.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '70',
            ),
            428 => 
            array (
                'id' => 69,
                'currencyname' => 'Romanian Leu   
',
                'leftsymbol' => 'lei',
                'rightsymbol' => 'RON',
                'isdefault' => 0,
                'currencyexchangerate' => '5.312350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '71',
            ),
            429 => 
            array (
                'id' => 70,
                'currencyname' => 'Serbian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RSD',
                'isdefault' => 0,
                'currencyexchangerate' => '123.341860',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '72',
            ),
            430 => 
            array (
                'id' => 71,
                'currencyname' => 'Russian Rouble   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'RUB',
                'isdefault' => 0,
                'currencyexchangerate' => '79.820000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '73',
            ),
            431 => 
            array (
                'id' => 72,
                'currencyname' => 'Saudi Riyal   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SAR',
                'isdefault' => 0,
                'currencyexchangerate' => '6.019460',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '74',
            ),
            432 => 
            array (
                'id' => 73,
                'currencyname' => 'Seychellois Rupee   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'SCR',
                'isdefault' => 0,
                'currencyexchangerate' => '19.351020',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '75',
            ),
            433 => 
            array (
                'id' => 74,
                'currencyname' => 'Swedish Krona   
',
                'leftsymbol' => 'kr',
                'rightsymbol' => 'SEK',
                'isdefault' => 0,
                'currencyexchangerate' => '10.307350',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '76',
            ),
            434 => 
            array (
                'id' => 75,
                'currencyname' => 'Singapore Dollar   
',
                'leftsymbol' => '$',
                'rightsymbol' => 'SGD',
                'isdefault' => 0,
                'currencyexchangerate' => '2.012970',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '77',
            ),
            435 => 
            array (
                'id' => 76,
                'currencyname' => 'Syrian Pound   
',
                'leftsymbol' => '£',
                'rightsymbol' => 'SYP',
                'isdefault' => 0,
                'currencyexchangerate' => '217.743690',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '78',
            ),
            436 => 
            array (
                'id' => 77,
                'currencyname' => 'Thai Baht   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'THB',
                'isdefault' => 0,
                'currencyexchangerate' => '50.065800',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '79',
            ),
            437 => 
            array (
                'id' => 78,
                'currencyname' => 'Tunisian Dinar   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'TND',
                'isdefault' => 0,
                'currencyexchangerate' => '2.645410',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '80',
            ),
            438 => 
            array (
                'id' => 79,
                'currencyname' => 'Turkish Lira   
',
                'leftsymbol' => 'TL',
                'rightsymbol' => 'TRY',
                'isdefault' => 0,
                'currencyexchangerate' => '3.227630',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '81',
            ),
            439 => 
            array (
                'id' => 80,
                'currencyname' => 'Taiwanese Dollar   
',
                'leftsymbol' => 'NT$',
                'rightsymbol' => 'TWD',
                'isdefault' => 0,
                'currencyexchangerate' => '47.473470',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '82',
            ),
            440 => 
            array (
                'id' => 81,
                'currencyname' => 'Ukraine Hryvnia   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UAH',
                'isdefault' => 0,
                'currencyexchangerate' => '13.127200',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '83',
            ),
            441 => 
            array (
                'id' => 82,
                'currencyname' => 'Ugandan Shilling   
',
                'leftsymbol' => '-',
                'rightsymbol' => 'UGX',
                'isdefault' => 0,
                'currencyexchangerate' => '4.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '84',
            ),
            442 => 
            array (
                'id' => 83,
                'currencyname' => 'Uruguayan Peso   
',
                'leftsymbol' => '$U',
                'rightsymbol' => 'UYU',
                'isdefault' => 0,
                'currencyexchangerate' => '34.511540',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '86',
            ),
            443 => 
            array (
                'id' => 84,
                'currencyname' => 'Venezuelan Bolvar   
',
                'leftsymbol' => 'Bs',
                'rightsymbol' => 'VEF',
                'isdefault' => 0,
                'currencyexchangerate' => '10.112680',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '87',
            ),
            444 => 
            array (
                'id' => 85,
                'currencyname' => 'Vietnamese Dong   ',
                'leftsymbol' => '-',
                'rightsymbol' => 'VND',
                'isdefault' => 0,
                'currencyexchangerate' => '33.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '88',
            ),
            445 => 
            array (
                'id' => 86,
                'currencyname' => 'Central African Franc  
',
                'leftsymbol' => '-',
                'rightsymbol' => 'XAF',
                'isdefault' => 0,
                'currencyexchangerate' => '780.529510',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '89',
            ),
            446 => 
            array (
                'id' => 87,
                'currencyname' => 'East Caribbean Dollar  
',
                'leftsymbol' => '$',
                'rightsymbol' => 'XCD',
                'isdefault' => 0,
                'currencyexchangerate' => '4.334010',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '90',
            ),
            447 => 
            array (
                'id' => 88,
                'currencyname' => 'West African Franc  ',
                'leftsymbol' => '-',
                'rightsymbol' => 'XOF',
                'isdefault' => 0,
                'currencyexchangerate' => '743.000000',
                'isactive' => 1,
                'clientdisplay' => 1,
                'currencyid' => '91',
            ),
            448 => 
            array (
                'id' => 90,
                'currencyname' => 'South African Rand  
',
                'leftsymbol' => 'R',
                'rightsymbol' => 'ZAR',
                'isdefault' => 0,
                'currencyexchangerate' => '18.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '93',
            ),
            449 => 
            array (
                'id' => 91,
                'currencyname' => ' IRANIAN RIAL',
                'leftsymbol' => '--',
                'rightsymbol' => 'IRR',
                'isdefault' => 0,
                'currencyexchangerate' => '125.000000',
                'isactive' => 0,
                'clientdisplay' => 0,
                'currencyid' => '94',
            ),
        ));
        
        
    }
}