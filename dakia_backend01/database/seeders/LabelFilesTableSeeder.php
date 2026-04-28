<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LabelFilesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('label_files')->delete();
        
        
        
    }
}