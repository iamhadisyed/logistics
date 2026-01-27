<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CttDatafileIdsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('ctt_datafile_ids')->delete();
        
        
        
    }
}