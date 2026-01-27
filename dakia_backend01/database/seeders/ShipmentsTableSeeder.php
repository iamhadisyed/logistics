<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShipmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shipments')->delete();
        
        \DB::table('shipments')->insert(array (
            0 => 
            array (
                'id' => 8,
                'uuid' => '9ac2f022-26d1-4441-9d0a-eb2e29f66ba7',
                'customer_id' => 148,
                'service_type' => 'Next Day Delivery',
                'warehouse_id' => 1,
                'reference' => '4577',
                'notes' => 'hhh',
                'company' => 'small company',
                'contact' => 'Syed Hadi Hussain Naqvi',
                'email' => 'iamhadisyed@gmail.com',
                'telephone' => '03333307449',
                'address_line_1' => '331 E BLOCK CANAL GARDEN NEAR SHAHKAM CHOWK LAHORE',
                'address_line_2' => '331 E BLOCK',
                'address_line_3' => 'Block E 1 Gulberg III',
                'city' => 'Lahore',
                'state' => 'Punjab',
                'postcode' => '54660',
                'country_id' => 1,
                'sender_company' => 'sendr',
                'sender_contact' => 'Syed Hadi Hussain Naqvi',
                'sender_email' => 'iamhadisyed@gmail.com',
                'sender_telephone' => '03333307449',
                'sender_address_line_1' => '331 E BLOCK CANAL GARDEN NEAR SHAHKAM CHOWK LAHORE',
                'sender_address_line_2' => NULL,
                'sender_address_line_3' => NULL,
                'sender_city' => 'Lahore',
                'sender_state' => NULL,
                'sender_postcode' => '54660',
                'sender_country_id' => 2,
                'status' => 'label_generated',
                'label_generated' => 1,
                'label_generated_at' => '2026-01-25 19:29:31',
                'created_at' => '2026-01-25 19:16:03',
                'updated_at' => '2026-01-25 19:29:31',
            ),
        ));
        
        
    }
}