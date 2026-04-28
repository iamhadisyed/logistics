<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OauthJwtsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_jwts')->delete();
        
        
        
    }
}