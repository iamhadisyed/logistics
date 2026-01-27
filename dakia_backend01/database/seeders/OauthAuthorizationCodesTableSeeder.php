<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OauthAuthorizationCodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_authorization_codes')->delete();
        
        
        
    }
}