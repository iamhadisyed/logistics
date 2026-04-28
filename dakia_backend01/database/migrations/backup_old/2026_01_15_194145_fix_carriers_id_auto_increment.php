<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Deduplicate records sharing the same ID
        // Since we don't have a unique key yet, we use LIMIT to delete N-1 duplicates
        $duplicates = DB::select('SELECT id, count(*) as c FROM carriers GROUP BY id HAVING c > 1');
        
        foreach ($duplicates as $dup) {
             $deleteCount = $dup->c - 1;
             if ($deleteCount > 0) {
                 DB::statement("DELETE FROM carriers WHERE id = ? LIMIT {$deleteCount}", [$dup->id]);
             }
        }

        // 2. Fix missing auto-increment and primary key on carriers table
        // Use raw SQL to ensure modifications are applied correctly to legacy schema
        DB::statement('ALTER TABLE carriers MODIFY id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting the change (removing auto_increment)
        DB::statement('ALTER TABLE carriers MODIFY id INT(11) NOT NULL');
        DB::statement('ALTER TABLE carriers DROP PRIMARY KEY');
    }
};
