<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['service_agent_mappings', 'user_services_routings'];

        foreach ($tables as $table) {
            // 1. Deduplicate records sharing the same ID
            $duplicates = DB::select("SELECT id, count(*) as c FROM {$table} GROUP BY id HAVING c > 1");
            
            foreach ($duplicates as $dup) {
                 $deleteCount = $dup->c - 1;
                 if ($deleteCount > 0) {
                     DB::statement("DELETE FROM {$table} WHERE id = ? LIMIT {$deleteCount}", [$dup->id]);
                 }
            }

            // 2. Fix missing auto-increment and primary key
            // Note: 'id' might be int(11) or int(10) unsigned. We standardize to INT AUTO_INCREMENT PRIMARY KEY.
            // We use CHANGE if column exists to modify definition.
            
            // Check if table has 'id' column (it does)
            // We modify it.
            DB::statement("ALTER TABLE {$table} MODIFY id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['service_agent_mappings', 'user_services_routings'];
        foreach ($tables as $table) {
             DB::statement("ALTER TABLE {$table} MODIFY id INT(11) NOT NULL");
             DB::statement("ALTER TABLE {$table} DROP PRIMARY KEY");
        }
    }
};
