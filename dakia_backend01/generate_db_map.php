<?php
$json = file_get_contents('db_full_schema.json');
$schema = json_decode($json, true);

$modules = [
    'Accounts & Users' => ['user_accounts', 'users', 'groups', 'permissions', 'grouphaspermissions', 'user_departments', 'user_account_settings'],
    'Carriers & Services' => ['agents', 'carriers', 'services', 'user_services_routings', 'carrier_service_customize_rules', 'carrier_service_default_rules', 'carrier_service_groups', 'carrier_service_zone_mappings'],
    'Consignments' => ['consignments', 'parcels', 'items', 'consignment_status_logs', 'consignment_tracking_logs', 'manifests', 'consignment_bagging_mappings', 'consignment_relabels'],
    'Label Generation' => ['carrier_labels', 'label_templates', 'label_configs'], // Heuristic categories
    'Rates & Pricing' => ['tariffs', 'tariff_additional_charges', 'consignment_charges_types', 'sp_tariff_logs', 'invoices', 'billing_logs'],
];

$allTables = array_column($schema, 'name');
sort($allTables);

$output = "# Complete Database Map & Analysis\n\n";
$output .= "## Overview\n";
$output .= "- **Total Tables**: " . count($schema) . "\n";
$output .= "- **Total Modules**: " . count($modules) . " Core Modules mapped.\n\n";

function getRelationship($colName, $allTables) {
    $clean = preg_replace('/(_id|id|_ID|ID)$/', '', $colName);
    $options = [$clean, $clean . 's', rtrim($clean, 's')];
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
            
            // Analyze PK
            $pks = array_filter($table['columns'], fn($c) => $c['pk']);
            $pkName = !empty($pks) ? reset($pks)['name'] : "NONE";
            $isAi = !empty($pks) && strpos(reset($pks)['extra'], 'auto_increment') !== false;
            
            $output .= "- **Primary Key**: `{$pkName}` " . ($isAi ? "(Auto-Increment)" : "**(MANUAL HANDLING REQUIRED)**") . "\n";

            // Status Columns (Soft Delete)
            $statusCols = array_filter($table['columns'], fn($c) => in_array($c['name'], ['active_flag', 'is_deleted', 'status', 'is_active', 'deleted', 'deleted_at', 'active']));
            if (!empty($statusCols)) {
                $output .= "- **Status/Soft Delete**: " . implode(', ', array_map(fn($c) => "`{$c['name']}`", $statusCols)) . "\n";
            }

            // Relationships
            $rels = [];
            foreach ($table['columns'] as $col) {
                if ($col['name'] !== $pkName && (strpos($col['name'], 'id') !== false || strpos($col['name'], 'ID') !== false)) {
                    $target = getRelationship($col['name'], $allTables);
                    if ($target) $rels[] = "`{$col['name']}` -> `{$target}`";
                }
            }
            if (!empty($rels)) $output .= "- **Relationships**: " . implode(', ', array_unique($rels)) . "\n";

            $output .= "\n| Column | Type | Null | Default | Notes |\n";
            $output .= "|---|---|---|---|---|\n";
            foreach ($table['columns'] as $col) {
                $notes = [];
                if ($col['pk']) $notes[] = "PK";
                if (!$col['nullable']) $notes[] = "REQUIRED";
                if (strpos($col['extra'], 'auto_increment') !== false) $notes[] = "AI";
                $output .= "| `{$col['name']}` | `{$col['type']}` | " . ($col['nullable'] ? "YES" : "NO") . " | " . ($col['default'] ?? "NULL") . " | " . implode(', ', $notes) . " |\n";
            }
            $output .= "\n---\n\n";
        }
    }
}

$output .= "## Remaining Tables (Grouped Alphabetically)\n\n";
foreach ($schema as $table) {
    $isMapped = false;
    foreach ($modules as $m) if (in_array($table['name'], $m)) $isMapped = true;
    if (!$isMapped) {
        $output .= "### Table: `{$table['name']}`\n";
        // Brief summary for others to avoid massive bloat
        $pks = array_filter($table['columns'], fn($c) => $c['pk']);
        $pkName = !empty($pks) ? reset($pks)['name'] : "NONE";
        $output .= "- **PK**: `{$pkName}`\n";
        $output .= "- **Columns**: " . count($table['columns']) . " total.\n\n";
    }
}

file_put_contents('COMPLETE_DATABASE_MAP.md', $output);
echo "Generated COMPLETE_DATABASE_MAP.md\n";
