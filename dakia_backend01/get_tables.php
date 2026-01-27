<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$tableNames = array_map(fn($t) => array_values((array)$t)[0], $tables);
echo implode(',', $tableNames);
