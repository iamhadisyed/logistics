<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
$databaseName = DB::getDatabaseName();
$tableKey = "Tables_in_" . $databaseName;

$schemaMap = [];

foreach ($tables as $t) {
    $tableName = $t->$tableKey;
    $columns = DB::select("SHOW FULL COLUMNS FROM `$tableName` ");
    
    $tableData = [
        'name' => $tableName,
        'columns' => []
    ];

    foreach ($columns as $col) {
        $tableData['columns'][] = [
            'name' => $col->Field,
            'type' => $col->Type,
            'pk' => ($col->Key == 'PRI'),
            'fk' => ($col->Key == 'MUL' || $col->Key == 'UNI'), // Heuristic for FK/Index
            'nullable' => ($col->Null == 'YES'),
            'default' => $col->Default,
            'extra' => $col->Extra, // Auto-increment info here
            'comment' => $col->Comment
        ];
    }
    
    $schemaMap[] = $tableData;
}

file_put_contents('db_full_schema.json', json_encode($schemaMap, JSON_PRETTY_PRINT));
echo "Successfully mapped " . count($schemaMap) . " tables to db_full_schema.json\n";
