<?php

$analysis = file_get_contents('c:/daakia/COMPLETE_TABLE_NAMING_ANALYSIS.md');
$lines = explode("\n", $analysis);

$ups = [];
$downs = [];
foreach ($lines as $line) {
    if (strpos($line, '⚠️ RENAME') !== false) {
        $parts = explode('|', $line);
        if (count($parts) >= 6) {
            $old = trim($parts[2]);
            $new = trim($parts[3]);
            if (in_array($old, ['address', 'carrier', 'consignment', 'country', 'parcel', 'user'])) continue;
            $ups[] = "        Schema::rename('$old', '$new');";
            $downs[] = "        Schema::rename('$new', '$old');";
        }
    }
}

$upString = implode("\n", $ups);
$downString = implode("\n", array_reverse($downs));

$migrationContent = "<?php

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
$upString
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
$downString
    }
};
";

file_put_contents('c:/daakia/dakia_backend01/database/migrations/2025_12_17_201604_rename_remaining_tables_to_plural.php', $migrationContent);
echo "Migration file updated with " . count($ups) . " renames.\n";
