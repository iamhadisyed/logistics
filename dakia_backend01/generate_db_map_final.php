<?php
$json = file_get_contents('db_full_schema.json');
$schema = json_decode($json, true);

$modules = [
    'Accounts & Users' => ['user_accounts', 'users', 'groups', 'permissions', 'grouphaspermissions', 'user_departments', 'user_account_settings'],
    'Carriers & Services' => ['agents', 'carriers', 'services', 'user_services_routings', 'carrier_service_customize_rules', 'carrier_service_default_rules', 'carrier_service_groups', 'carrier_service_zone_mappings', 'service_agent_mappings'],
    'Consignments & Parcels' => ['consignments', 'parcels', 'item_details', 'products', 'consignment_status_logs', 'consignment_tracking_logs', 'manifests', 'consignment_bagging_mappings', 'consignment_relabels'],
    'Label Generation' => ['label_files', 'consignment_relabels'],
    'Rates & Pricing' => ['tariffs', 'tariff_additional_charges', 'consignment_charges_types', 'sp_tariff_logs', 'invoices', 'billing_logs', 'ratebands', 'zonebands'],
    'Geography' => ['countries', 'carrier_zones', 'carrier_zones_countries', 'carrier_zones_postcodes'],
];

$allTables = array_column($schema, 'name');
sort($allTables);

$output = "# Complete Database Map & Analysis\n\n";
$output .= "## Overview\n";
$output .= "- **Total Tables**: " . count($schema) . "\n";
$output .= "- **Core Areas**: Accounts, Carriers, Consignments, Labels, Pricing, Geography.\n\n";

$output .= "## Relationship Hierarchy (Simplified)\n";
$output .= "```mermaid\ngraph TD\n";
$output .= "  UserAccount[\"UserAccount (ID)\"] --> User[\"User (id)\"]\n";
$output .= "  Carrier[\"Carrier (id)\"] --> Service[\"Service (id)\"]\n";
$output .= "  UserAccount --> UserServicesRouting[\"Routing Rules\"]\n";
$output .= "  Service --> UserServicesRouting\n";
$output .= "  Consignment[\"Consignment (id)\"] --> Parcel[\"Parcel (id)\"]\n";
$output .= "  Consignment --> User[\"Shared User context\"]\n";
$output .= "  Consignment --> Service[\"Linked Service\"]\n";
$output .= "  Parcel --> ItemDetails[\"ItemDetails (Linked via consignment_id)\"]\n";
$output .= "  Tariff[\"Tariff\"] --> UserAccount\n";
$output .= "  Tariff --> Service\n";
$output .= "```\n\n";

function getRelationship($colName, $allTables) {
    if (in_array($colName, $allTables)) return $colName;
    $clean = preg_replace('/(_id|id|_ID|ID|id)$/', '', $colName);
    $options = [$clean, $clean . 's', rtrim($clean, 's'), str_replace('userid', 'users', $colName)];
    if ($colName === 'user_account_id') return 'user_accounts';
    if ($colName === 'service_id' || $colName === 'serviceid') return 'services';
    if ($colName === 'agent_id' || $colName === 'agentid') return 'agents';
    if ($colName === 'carrier_id') return 'carriers';
    if ($colName === 'consignment_id' || $colName === 'consignmentid') return 'consignments';
    
    foreach ($options as $opt) {
        if (in_array($opt, $allTables)) return $opt;
    }
    return null;
}

foreach ($modules as $moduleName => $moduleTables) {
    $output .= "## Module: $moduleName\n\n";
    foreach ($schema as $table) {
        if (in_array($table['name'], $moduleTables)) {
            $output .= "### Table: `{$table['name']}`\n";
            
            $pks = array_filter($table['columns'], fn($c) => $c['pk']);
            $pkName = !empty($pks) ? reset($pks)['name'] : "NONE";
            $isAi = !empty($pks) && strpos(reset($pks)['extra'], 'auto_increment') !== false;
            
            $output .= "- **Primary Key**: `{$pkName}` " . ($isAi ? "(Auto-Increment)" : "**(MANUAL HANDLING REQUIRED)**") . "\n";

            $statusCols = array_filter($table['columns'], fn($c) => in_array($c['name'], ['active_flag', 'is_deleted', 'status', 'is_active', 'deleted', 'deleted_at', 'active']));
            if (!empty($statusCols)) {
                $output .= "- **Status/Soft Delete**: " . implode(', ', array_map(fn($c) => "`{$c['name']}`", $statusCols)) . "\n";
            }

            $rels = [];
            foreach ($table['columns'] as $col) {
                if ($col['name'] !== $pkName && (strpos($col['name'], 'id') !== false || strpos($col['name'], 'ID') !== false || in_array($col['name'], ['user_account_id', 'service_id']))) {
                    $target = getRelationship($col['name'], $allTables);
                    if ($target && $target !== $table['name']) $rels[] = "`{$col['name']}` -> `{$target}`";
                }
            }
            if (!empty($rels)) $output .= "- **Relationships**: " . implode(', ', array_unique($rels)) . "\n";

            $output .= "\n| Column | Type | Null | Default | Notes |\n";
            $output .= "|---|---|---|---|---|\n";
            foreach ($table['columns'] as $col) {
                $notes = [];
                if ($col['pk']) $notes[] = "PK";
                if (!$col['nullable'] && !strpos($col['extra'], 'auto_increment')) $notes[] = "**REQUIRED**";
                if (strpos($col['extra'], 'auto_increment') !== false) $notes[] = "AI";
                $output .= "| `{$col['name']}` | `{$col['type']}` | " . ($col['nullable'] ? "YES" : "NO") . " | " . ($col['default'] ?? "NULL") . " | " . implode(', ', $notes) . " |\n";
            }
            $output .= "\n---\n\n";
        }
    }
}

$output .= "## Summary of Critical Tables for Implementation\n\n";
$output .= "| Area | Primary Tables | Key Foreign Keys |\n";
$output .= "|---|---|---|\n";
$output .= "| **Consignment Creation** | `consignments`, `parcels`, `item_details` | `user_id`, `service_id`, `consignment_id` |\n";
$output .= "| **Service Selection** | `services`, `user_services_routings`, `carriers` | `carrier_id`, `user_account_id` |\n";
$output .= "| **Label Generation** | `label_files`, `consignments` (label_file col) | `account_number`, `hawb_list` |\n";
$output .= "| **Pricing** | `tariffs`, `tariff_additional_charges` | `user_account_id`, `service_id` |\n";

file_put_contents('COMPLETE_DATABASE_MAP.md', $output);
echo "Final COMPLETE_DATABASE_MAP.md generated.\n";
