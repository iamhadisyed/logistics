<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['address', 'carrier', 'consignment', 'country', 'parcel', 'service'];

foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "$table count: $count\n";
    } catch (\Exception $e) {
        echo "$table: Not found or error: " . $e->getMessage() . "\n";
    }
}
