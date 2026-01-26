<?php
$data = json_decode(file_get_contents('db_full_schema.json'), true);
foreach ($data as $t) {
    if ($t['name'] === 'item_details') {
        echo "Table: item_details\n";
        foreach ($t['columns'] as $c) {
            printf("  %-25s %-20s %s\n", $c['name'], $c['type'], $c['nullable'] ? "NULL" : "REQUIRED");
        }
    }
}
