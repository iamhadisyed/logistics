<?php
$json = file_get_contents('db_core_tables.json');
$tables = json_decode($json, true);

foreach($tables as $t) {
    echo "TABLE: " . $t['name'] . "\n";
    foreach($t['columns'] as $c) {
        $pk = $c['pk'] ? "[PK]" : "";
        $ai = strpos($c['extra'], 'auto_increment') !== false ? "[AI]" : "";
        $req = !$c['nullable'] ? "[REQ]" : "";
        echo "  - {$c['name']} ({$c['type']}) $pk $ai $req\n";
    }
    echo "\n";
}
