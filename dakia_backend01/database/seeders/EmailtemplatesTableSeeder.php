<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmailtemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('emailtemplates')->delete();
        
        
        
    }
}