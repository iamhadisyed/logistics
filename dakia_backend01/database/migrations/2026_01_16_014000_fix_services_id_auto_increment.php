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
        // 0. Fix invalid zero dates which block ALTER TABLE in strict mode
        DB::statement("UPDATE services SET added_on = '2000-01-01 00:00:00' WHERE added_on < '1000-01-01 00:00:00'");
        DB::statement("UPDATE services SET changed_on = '2000-01-01 00:00:00' WHERE changed_on < '1000-01-01 00:00:00'");

        // 1. Deduplicate records sharing the same ID in services table
        $duplicates = DB::select('SELECT id, count(*) as c FROM services GROUP BY id HAVING c > 1');
        
        foreach ($duplicates as $dup) {
             $deleteCount = $dup->c - 1;
             // Naively delete the first N records, keeping the last one (or however MySQL orders them)
             // Since they are duplicates, we assume data is identical or we just save one.
             if ($deleteCount > 0) {
                 DB::statement("DELETE FROM services WHERE id = ? LIMIT {$deleteCount}", [$dup->id]);
             }
        }

        // 2. Fix missing auto-increment and primary key
        // Note: 'services' table has 'id' column. We modify it.
        DB::statement('ALTER TABLE services MODIFY id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily reverse deduping or date fixing.
        // We can remove auto increment but we can't restore duplicates.
        DB::statement('ALTER TABLE services MODIFY id INT(11) NOT NULL');
        DB::statement('ALTER TABLE services DROP PRIMARY KEY');
    }
};
