<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InvoiceExtraChargesTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('invoice_extra_charges_types')->delete();
        
        
        
    }
}