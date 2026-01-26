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
        // 0. Fix invalid zero dates which block ALTER TABLE in strict mode
        // Update any date < 1000-01-01 (including 0000-00-00) to a valid date
        DB::statement("UPDATE countries SET added_on = '2000-01-01 00:00:00' WHERE added_on < '1000-01-01 00:00:00'");
        DB::statement("UPDATE countries SET changed_on = '2000-01-01 00:00:00' WHERE changed_on < '1000-01-01 00:00:00'");

        // 1. Deduplicate records sharing the same ID in countries table
        $duplicates = DB::select('SELECT id, count(*) as c FROM countries GROUP BY id HAVING c > 1');
        
        foreach ($duplicates as $dup) {
             $deleteCount = $dup->c - 1;
             if ($deleteCount > 0) {
                 DB::statement("DELETE FROM countries WHERE id = ? LIMIT {$deleteCount}", [$dup->id]);
             }
        }

        // 2. Fix missing auto-increment and primary key
        // Use raw SQL to ensure modifications are applied correctly
        DB::statement('ALTER TABLE countries MODIFY id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting the change
        DB::statement('ALTER TABLE countries MODIFY id INT(11) NOT NULL');
        DB::statement('ALTER TABLE countries DROP PRIMARY KEY');
    }
};
