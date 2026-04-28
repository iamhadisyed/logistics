<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PersonalAccessTokensTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('personal_access_tokens')->delete();
        
        \DB::table('personal_access_tokens')->insert(array (
            0 => 
            array (
                'id' => 2,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 2230,
                'name' => 'auth_token',
                'token' => '97bfb0965ed69d84de9c355b9bdc7d8a919a892b9989ca21afe6dd9acec46555',
                'abilities' => '["*"]',
                'last_used_at' => '2026-01-15 19:13:15',
                'expires_at' => NULL,
                'created_at' => '2026-01-14 17:54:19',
                'updated_at' => '2026-01-15 19:13:15',
            ),
            1 => 
            array (
                'id' => 3,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 2230,
                'name' => 'auth_token',
                'token' => '3f986a18d6349cc1a7bb97b3b356254c59f8f8cfbb00d555ed9bdf6303f2058e',
                'abilities' => '["*"]',
                'last_used_at' => '2026-01-15 19:23:44',
                'expires_at' => NULL,
                'created_at' => '2026-01-15 19:14:27',
                'updated_at' => '2026-01-15 19:23:44',
            ),
            2 => 
            array (
                'id' => 4,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 2230,
                'name' => 'auth_token',
                'token' => 'd1f78e2aa8fa23caddc9dd01e99c6251d8f89ffea93dc34c26c2270a2722858e',
                'abilities' => '["*"]',
                'last_used_at' => '2026-01-15 21:20:37',
                'expires_at' => NULL,
                'created_at' => '2026-01-15 19:25:23',
                'updated_at' => '2026-01-15 21:20:37',
            ),
            3 => 
            array (
                'id' => 5,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 2230,
                'name' => 'auth_token',
                'token' => 'a73377ccaea4337c5a44bb8a7c34f187ed5032ad374a449774bdb418151c78e3',
                'abilities' => '["*"]',
                'last_used_at' => '2026-01-25 19:39:38',
                'expires_at' => NULL,
                'created_at' => '2026-01-25 13:49:36',
                'updated_at' => '2026-01-25 19:39:38',
            ),
            4 => 
            array (
                'id' => 6,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 2230,
                'name' => 'auth_token',
                'token' => '5b9f905ec841d69b636c26daeed233983ee88aed47bfa9389696e21f639979fe',
                'abilities' => '["*"]',
                'last_used_at' => NULL,
                'expires_at' => NULL,
                'created_at' => '2026-01-26 20:36:03',
                'updated_at' => '2026-01-26 20:36:03',
            ),
            5 => 
            array (
                'id' => 7,
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => 2230,
                'name' => 'auth_token',
                'token' => 'db2835950d3fc0b373bf3d4e60b5ef8374de59aada2d392945a90c9561028031',
                'abilities' => '["*"]',
                'last_used_at' => '2026-01-26 20:50:57',
                'expires_at' => NULL,
                'created_at' => '2026-01-26 20:36:06',
                'updated_at' => '2026-01-26 20:50:57',
            ),
        ));
        
        
    }
}