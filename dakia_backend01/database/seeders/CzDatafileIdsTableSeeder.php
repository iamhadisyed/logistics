<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CzDatafileIdsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cz_datafile_ids')->delete();
        
        
        
    }
}