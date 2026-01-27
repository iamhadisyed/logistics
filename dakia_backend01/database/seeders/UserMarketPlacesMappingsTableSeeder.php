<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserMarketPlacesMappingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_market_places_mappings')->delete();
        
        \DB::table('user_market_places_mappings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'market_places_id' => 1,
                'user_account_id' => 2191,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'market_places_id' => 2,
                'user_account_id' => 2349,
                'auth_data' => '',
                'store_key' => NULL,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 17,
                'market_places_id' => 1,
                'user_account_id' => 2258,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 19,
                'market_places_id' => 1,
                'user_account_id' => 2349,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 20,
                'market_places_id' => 9,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 21,
                'market_places_id' => 53,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 22,
                'market_places_id' => 54,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 23,
                'market_places_id' => 55,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 24,
                'market_places_id' => 56,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 25,
                'market_places_id' => 57,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 26,
                'market_places_id' => 58,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 38,
                'market_places_id' => 88,
                'user_account_id' => 2327,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 49,
                'market_places_id' => 88,
                'user_account_id' => 2228,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/brands2you.co"}',
                'store_key' => '4e7f1cb38103c176c6c1afdb6799d2eb',
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 57,
                'market_places_id' => 1,
                'user_account_id' => 148,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 63,
                'market_places_id' => 1,
                'user_account_id' => 2326,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 64,
                'market_places_id' => 88,
                'user_account_id' => 2326,
                'auth_data' => '{"ftp_host":"https:\\/\\/www.brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '22a6e130f463b596e87af90fd982f324',
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 66,
                'market_places_id' => 1,
                'user_account_id' => 2297,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 67,
                'market_places_id' => 88,
                'user_account_id' => 2332,
                'auth_data' => '{"ftp_host":"","ftp_port":"","ftp_user":"","ftp_password":"","ftp_store_dir":""}',
                'store_key' => '',
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 73,
                'market_places_id' => 88,
                'user_account_id' => 2234,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '4769c94dbfc9a7db690df7ed79eb357e',
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 1,
                'market_places_id' => 1,
                'user_account_id' => 2191,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 2,
                'market_places_id' => 2,
                'user_account_id' => 2349,
                'auth_data' => '',
                'store_key' => NULL,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 17,
                'market_places_id' => 1,
                'user_account_id' => 2258,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 19,
                'market_places_id' => 1,
                'user_account_id' => 2349,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 20,
                'market_places_id' => 9,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 21,
                'market_places_id' => 53,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 22,
                'market_places_id' => 54,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            26 => 
            array (
                'id' => 23,
                'market_places_id' => 55,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            27 => 
            array (
                'id' => 24,
                'market_places_id' => 56,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            28 => 
            array (
                'id' => 25,
                'market_places_id' => 57,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            29 => 
            array (
                'id' => 26,
                'market_places_id' => 58,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            30 => 
            array (
                'id' => 38,
                'market_places_id' => 88,
                'user_account_id' => 2327,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            31 => 
            array (
                'id' => 49,
                'market_places_id' => 88,
                'user_account_id' => 2228,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/brands2you.co"}',
                'store_key' => '4e7f1cb38103c176c6c1afdb6799d2eb',
                'active' => 1,
            ),
            32 => 
            array (
                'id' => 57,
                'market_places_id' => 1,
                'user_account_id' => 148,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            33 => 
            array (
                'id' => 63,
                'market_places_id' => 1,
                'user_account_id' => 2326,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            34 => 
            array (
                'id' => 64,
                'market_places_id' => 88,
                'user_account_id' => 2326,
                'auth_data' => '{"ftp_host":"https:\\/\\/www.brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '22a6e130f463b596e87af90fd982f324',
                'active' => 1,
            ),
            35 => 
            array (
                'id' => 66,
                'market_places_id' => 1,
                'user_account_id' => 2297,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            36 => 
            array (
                'id' => 67,
                'market_places_id' => 88,
                'user_account_id' => 2332,
                'auth_data' => '{"ftp_host":"","ftp_port":"","ftp_user":"","ftp_password":"","ftp_store_dir":""}',
                'store_key' => '',
                'active' => 1,
            ),
            37 => 
            array (
                'id' => 73,
                'market_places_id' => 88,
                'user_account_id' => 2234,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '4769c94dbfc9a7db690df7ed79eb357e',
                'active' => 1,
            ),
            38 => 
            array (
                'id' => 1,
                'market_places_id' => 1,
                'user_account_id' => 2191,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            39 => 
            array (
                'id' => 2,
                'market_places_id' => 2,
                'user_account_id' => 2349,
                'auth_data' => '',
                'store_key' => NULL,
                'active' => 1,
            ),
            40 => 
            array (
                'id' => 17,
                'market_places_id' => 1,
                'user_account_id' => 2258,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            41 => 
            array (
                'id' => 19,
                'market_places_id' => 1,
                'user_account_id' => 2349,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            42 => 
            array (
                'id' => 20,
                'market_places_id' => 9,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            43 => 
            array (
                'id' => 21,
                'market_places_id' => 53,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            44 => 
            array (
                'id' => 22,
                'market_places_id' => 54,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            45 => 
            array (
                'id' => 23,
                'market_places_id' => 55,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            46 => 
            array (
                'id' => 24,
                'market_places_id' => 56,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            47 => 
            array (
                'id' => 25,
                'market_places_id' => 57,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            48 => 
            array (
                'id' => 26,
                'market_places_id' => 58,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            49 => 
            array (
                'id' => 38,
                'market_places_id' => 88,
                'user_account_id' => 2327,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            50 => 
            array (
                'id' => 49,
                'market_places_id' => 88,
                'user_account_id' => 2228,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/brands2you.co"}',
                'store_key' => '4e7f1cb38103c176c6c1afdb6799d2eb',
                'active' => 1,
            ),
            51 => 
            array (
                'id' => 57,
                'market_places_id' => 1,
                'user_account_id' => 148,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            52 => 
            array (
                'id' => 63,
                'market_places_id' => 1,
                'user_account_id' => 2326,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            53 => 
            array (
                'id' => 64,
                'market_places_id' => 88,
                'user_account_id' => 2326,
                'auth_data' => '{"ftp_host":"https:\\/\\/www.brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '22a6e130f463b596e87af90fd982f324',
                'active' => 1,
            ),
            54 => 
            array (
                'id' => 66,
                'market_places_id' => 1,
                'user_account_id' => 2297,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            55 => 
            array (
                'id' => 67,
                'market_places_id' => 88,
                'user_account_id' => 2332,
                'auth_data' => '{"ftp_host":"","ftp_port":"","ftp_user":"","ftp_password":"","ftp_store_dir":""}',
                'store_key' => '',
                'active' => 1,
            ),
            56 => 
            array (
                'id' => 73,
                'market_places_id' => 88,
                'user_account_id' => 2234,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '4769c94dbfc9a7db690df7ed79eb357e',
                'active' => 1,
            ),
            57 => 
            array (
                'id' => 1,
                'market_places_id' => 1,
                'user_account_id' => 2191,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            58 => 
            array (
                'id' => 2,
                'market_places_id' => 2,
                'user_account_id' => 2349,
                'auth_data' => '',
                'store_key' => NULL,
                'active' => 1,
            ),
            59 => 
            array (
                'id' => 17,
                'market_places_id' => 1,
                'user_account_id' => 2258,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            60 => 
            array (
                'id' => 19,
                'market_places_id' => 1,
                'user_account_id' => 2349,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            61 => 
            array (
                'id' => 20,
                'market_places_id' => 9,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            62 => 
            array (
                'id' => 21,
                'market_places_id' => 53,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            63 => 
            array (
                'id' => 22,
                'market_places_id' => 54,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            64 => 
            array (
                'id' => 23,
                'market_places_id' => 55,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            65 => 
            array (
                'id' => 24,
                'market_places_id' => 56,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            66 => 
            array (
                'id' => 25,
                'market_places_id' => 57,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            67 => 
            array (
                'id' => 26,
                'market_places_id' => 58,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            68 => 
            array (
                'id' => 38,
                'market_places_id' => 88,
                'user_account_id' => 2327,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            69 => 
            array (
                'id' => 49,
                'market_places_id' => 88,
                'user_account_id' => 2228,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/brands2you.co"}',
                'store_key' => '4e7f1cb38103c176c6c1afdb6799d2eb',
                'active' => 1,
            ),
            70 => 
            array (
                'id' => 57,
                'market_places_id' => 1,
                'user_account_id' => 148,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            71 => 
            array (
                'id' => 63,
                'market_places_id' => 1,
                'user_account_id' => 2326,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            72 => 
            array (
                'id' => 64,
                'market_places_id' => 88,
                'user_account_id' => 2326,
                'auth_data' => '{"ftp_host":"https:\\/\\/www.brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '22a6e130f463b596e87af90fd982f324',
                'active' => 1,
            ),
            73 => 
            array (
                'id' => 66,
                'market_places_id' => 1,
                'user_account_id' => 2297,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            74 => 
            array (
                'id' => 67,
                'market_places_id' => 88,
                'user_account_id' => 2332,
                'auth_data' => '{"ftp_host":"","ftp_port":"","ftp_user":"","ftp_password":"","ftp_store_dir":""}',
                'store_key' => '',
                'active' => 1,
            ),
            75 => 
            array (
                'id' => 73,
                'market_places_id' => 88,
                'user_account_id' => 2234,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '4769c94dbfc9a7db690df7ed79eb357e',
                'active' => 1,
            ),
            76 => 
            array (
                'id' => 1,
                'market_places_id' => 1,
                'user_account_id' => 2191,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            77 => 
            array (
                'id' => 2,
                'market_places_id' => 2,
                'user_account_id' => 2349,
                'auth_data' => '',
                'store_key' => NULL,
                'active' => 1,
            ),
            78 => 
            array (
                'id' => 17,
                'market_places_id' => 1,
                'user_account_id' => 2258,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            79 => 
            array (
                'id' => 19,
                'market_places_id' => 1,
                'user_account_id' => 2349,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            80 => 
            array (
                'id' => 20,
                'market_places_id' => 9,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            81 => 
            array (
                'id' => 21,
                'market_places_id' => 53,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            82 => 
            array (
                'id' => 22,
                'market_places_id' => 54,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            83 => 
            array (
                'id' => 23,
                'market_places_id' => 55,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            84 => 
            array (
                'id' => 24,
                'market_places_id' => 56,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            85 => 
            array (
                'id' => 25,
                'market_places_id' => 57,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            86 => 
            array (
                'id' => 26,
                'market_places_id' => 58,
                'user_account_id' => 2349,
                'auth_data' => NULL,
                'store_key' => NULL,
                'active' => 1,
            ),
            87 => 
            array (
                'id' => 38,
                'market_places_id' => 88,
                'user_account_id' => 2327,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => NULL,
                'active' => 1,
            ),
            88 => 
            array (
                'id' => 49,
                'market_places_id' => 88,
                'user_account_id' => 2228,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/brands2you.co"}',
                'store_key' => '4e7f1cb38103c176c6c1afdb6799d2eb',
                'active' => 1,
            ),
            89 => 
            array (
                'id' => 57,
                'market_places_id' => 1,
                'user_account_id' => 148,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            90 => 
            array (
                'id' => 63,
                'market_places_id' => 1,
                'user_account_id' => 2326,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            91 => 
            array (
                'id' => 64,
                'market_places_id' => 88,
                'user_account_id' => 2326,
                'auth_data' => '{"ftp_host":"https:\\/\\/www.brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '22a6e130f463b596e87af90fd982f324',
                'active' => 1,
            ),
            92 => 
            array (
                'id' => 66,
                'market_places_id' => 1,
                'user_account_id' => 2297,
                'auth_data' => '{"AWS_ACCESS_KEY_ID":"AKIAJBUWT3ZBRDV3QITA","AWS_SECRET_ACCESS_KEY":"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","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}',
                'store_key' => '',
                'active' => 1,
            ),
            93 => 
            array (
                'id' => 67,
                'market_places_id' => 88,
                'user_account_id' => 2332,
                'auth_data' => '{"ftp_host":"","ftp_port":"","ftp_user":"","ftp_password":"","ftp_store_dir":""}',
                'store_key' => '',
                'active' => 1,
            ),
            94 => 
            array (
                'id' => 73,
                'market_places_id' => 88,
                'user_account_id' => 2234,
                'auth_data' => '{"ftp_host":"brands2you.co","ftp_port":"21","ftp_user":"api2cart","ftp_password":"Kl27q$2s","ftp_store_dir":"httpdocs","store_url":"https:\\/\\/www.brands2you.co"}',
                'store_key' => '4769c94dbfc9a7db690df7ed79eb357e',
                'active' => 1,
            ),
        ));
        
        
    }
}