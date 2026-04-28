<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AgentRestrictedPostcodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('agent_restricted_postcodes')->delete();
        
        \DB::table('agent_restricted_postcodes')->insert(array (
            0 => 
            array (
                'id' => 11,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => 'UB77RB',
                'is_city' => 0,
            ),
            1 => 
            array (
                'id' => 12,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' HA3  ',
                'is_city' => 0,
            ),
            2 => 
            array (
                'id' => 13,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' Northern Ireland  ',
                'is_city' => 1,
            ),
            3 => 
            array (
                'id' => 11,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => 'UB77RB',
                'is_city' => 0,
            ),
            4 => 
            array (
                'id' => 12,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' HA3  ',
                'is_city' => 0,
            ),
            5 => 
            array (
                'id' => 13,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' Northern Ireland  ',
                'is_city' => 1,
            ),
            6 => 
            array (
                'id' => 11,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => 'UB77RB',
                'is_city' => 0,
            ),
            7 => 
            array (
                'id' => 12,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' HA3  ',
                'is_city' => 0,
            ),
            8 => 
            array (
                'id' => 13,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' Northern Ireland  ',
                'is_city' => 1,
            ),
            9 => 
            array (
                'id' => 11,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => 'UB77RB',
                'is_city' => 0,
            ),
            10 => 
            array (
                'id' => 12,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' HA3  ',
                'is_city' => 0,
            ),
            11 => 
            array (
                'id' => 13,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' Northern Ireland  ',
                'is_city' => 1,
            ),
            12 => 
            array (
                'id' => 11,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => 'UB77RB',
                'is_city' => 0,
            ),
            13 => 
            array (
                'id' => 12,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' HA3  ',
                'is_city' => 0,
            ),
            14 => 
            array (
                'id' => 13,
                'agent_id' => 1,
                'service_id' => 226,
                'postcode_city' => ' Northern Ireland  ',
                'is_city' => 1,
            ),
        ));
        
        
    }
}