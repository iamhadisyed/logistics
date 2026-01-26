<?php
$data = json_decode(file_get_contents('db_full_schema.json'), true);
foreach ($data as $t) {
    if (stripos($t['name'], 'country') !== false || stripos($t['name'], 'city') !== false || stripos($t['name'], 'postcode') !== false || stripos($t['name'], 'zone') !== false) {
        echo "Found: " . $t['name'] . "\n";
    }
}
