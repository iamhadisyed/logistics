<?php
$json = file_get_contents('db_full_schema.json');
$schema = json_decode($json, true);

$modules = [
    'Accounts & Users' => ['user', 'account', 'group', 'permission', 'department', 'role'],
    'Carriers & Services' => ['carrier', 'service', 'agent', 'routing', 'mapping', 'agent_mappings'],
    'Consignments & Tracking' => ['consignment', 'parcel', 'item', 'tracking', 'manifest', 'bagging', 'pod', 'label'],
    'Postcodes & Geography' => ['country', 'city', 'state', 'postcode', 'zone', 'county', 'district'],
    'Rates & Pricing' => ['tariff', 'charge', 'price', 'cost', 'invoice', 'billing', 'rate', 'surcharge', 'markup'],
    'Settings & Logs' => ['setting', 'log', 'constant', 'email_template', 'api_log', 'system_log'],
];

$categorized = [];
$uncategorized = [];

foreach ($schema as $table) {
    $found = false;
    foreach ($modules as $moduleName => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($table['name'], $keyword) !== false) {
                $categorized[$moduleName][] = $table;
                $found = true;
                break 2;
            }
        }
    }
    if (!$found) {
        $uncategorized[] = $table;
    }
}

// Map relationships (Simple heuristic: column name ending in _id or serviceid etc)
$allTableNames = array_column($schema, 'name');

function getRel($colName, $allTables) {
    $clean = preg_replace('/(_id|id|_ID|ID)$/', '', $colName);
    // Try plural and singular
    $options = [$clean, $clean . 's', rtrim($clean, 's')];
    foreach ($options as $opt) {
        if (in_array($opt, $allTables)) return $opt;
    }
    // Hardcoded overrides for common misalignments
    $overrides = [
        'agentid' => 'carriers', // Sometimes agents refer to carriers/agents
        'serviceid' => 'services',
        'account_id' => 'user_accounts',
        'perm_id' => 'permissions',
        'department_id' => 'groups',
    ];
    if (isset($overrides[$colName])) return $overrides[$colName];
    return null;
}

$output = "# Database Analysis Summary\n\n";

foreach ($categorized as $moduleName => $tables) {
    $output .= "## Module: $moduleName\n\n";
    foreach ($tables as $table) {
        $output .= "### Table: `{$table['name']}`\n";
        
        // Check for manual ID
        $pk = array_values(array_filter($table['columns'], fn($c) => $c['pk']));
        $manualId = false;
        if (!empty($pk)) {
            $isAi = strpos($pk[0]['extra'], 'auto_increment') !== false;
            if (!$isAi) $manualId = true;
        }

        $output .= "- **Primary Key**: " . (!empty($pk) ? "`{$pk[0]['name']}`" : "NONE");
        if ($manualId) $output .= " (MANUAL - NO AUTO-INCREMENT)";
        $output .= "\n";

        // Status Columns
        $statusCols = array_filter($table['columns'], fn($c) => in_array($c['name'], ['active_flag', 'is_deleted', 'status', 'is_active', 'deleted', 'deleted_at']));
        if (!empty($statusCols)) {
            $output .= "- **Status/Soft Delete**: " . implode(', ', array_map(fn($c) => "`{$c['name']}`", $statusCols)) . "\n";
        }

        // Relationships
        $rels = [];
        foreach ($table['columns'] as $col) {
            if ($col['name'] != $table['name'] . '_id' && $col['name'] != 'id' && (strpos($col['name'], 'id') !== false || strpos($col['name'], 'ID') !== false)) {
                $target = getRel($col['name'], $allTableNames);
                if ($target) {
                    $rels[] = "`{$col['name']}` -> `{$target}`";
                }
            }
        }
        if (!empty($rels)) {
            $output .= "- **Relationships**: " . implode(', ', $rels) . "\n";
        }

        $output .= "\n| Column | Type | Null | Default | Notes |\n";
        $output .= "|---|---|---|---|---|\n";
        foreach ($table['columns'] as $col) {
            $notes = [];
            if ($col['pk']) $notes[] = "PK";
            if (!$col['nullable']) $notes[] = "REQUIRED";
            if (strpos($col['extra'], 'auto_increment') !== false) $notes[] = "AI";
            $output .= "| `{$col['name']}` | `{$col['type']}` | " . ($col['nullable'] ? "YES" : "NO") . " | `{$col['default']}` | " . implode(', ', $notes) . " |\n";
        }
        $output .= "\n---\n";
    }
}

file_put_contents('db_summary_raw.md', $output);
echo "Analysis complete. Generated db_summary_raw.md\n";
