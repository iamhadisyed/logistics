<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OauthPublicKeysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_public_keys')->delete();
        
        
        
    }
}