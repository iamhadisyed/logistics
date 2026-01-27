<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

$tables = DB::select('SHOW TABLES');
$tableNames = array_map(fn($t) => array_values((array)$t)[0], $tables);

foreach ($tableNames as $table) {
    if ($table === 'migrations') continue;
    
    // Construct expected seeder filename
    $seederName = Str::studly($table) . 'TableSeeder';
    if (file_exists(__DIR__ . "/database/seeders/{$seederName}.php")) {
        echo "Skipping existing table: $table\n";
        continue;
    }

    echo "Seeding table: $table ... ";
    try {
        Artisan::call('iseed', ['tables' => $table, '--force' => true]);
        echo "Done.\n";
    } catch (\Exception $e) {
        echo "Failed: " . $e->getMessage() . "\n";
    }
}
