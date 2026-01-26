<?php
$data = json_decode(file_get_contents('db_full_schema.json'), true);
foreach ($data as $t) {
    if (stripos($t['name'], 'countr') !== false) {
        echo "Found: " . $t['name'] . "\n";
    }
}
