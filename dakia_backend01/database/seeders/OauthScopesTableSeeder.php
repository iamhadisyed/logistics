<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OauthScopesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_scopes')->delete();
        
        
        
    }
}