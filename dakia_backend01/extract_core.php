<?php
$json = file_get_contents('db_full_schema.json');
$schema = json_decode($json, true);

$targets = [
    'user_accounts', 'users', 'groups', 'permissions', 'grouphaspermissions', 'user_departments',
    'agents', 'carriers', 'services', 'user_services_routings', 'carrier_service_customize_rules',
    'consignments', 'parcels', 'items', 'tracking_logs', 'manifests'
];

$output = [];
foreach ($schema as $table) {
    if (in_array($table['name'], $targets)) {
        $output[] = $table;
    }
}

file_put_contents('db_core_tables.json', json_encode($output, JSON_PRETTY_PRINT));
echo "Extracted core tables to db_core_tables.json\n";
