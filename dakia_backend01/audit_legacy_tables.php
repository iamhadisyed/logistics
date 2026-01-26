<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Increase memory limit
ini_set('memory_limit', '512M');

// Get all tables
$tables = Schema::getConnection()->getSchemaBuilder()->getTableListing();
sort($tables);

$report = "# Legacy Database Audit & Migration Checklist\n\n";
$report .= "This file tracks the migration status of all " . count($tables) . " tables found in the database. Use this to track progress chunk by chunk.\n\n";
$report .= "| Table Name | Row Count | Inferred Module | Status | Migrated? |\n";
$report .= "|------------|-----------|-----------------|--------|-----------|\n";

$modules = [];
$emptyTables = 0;
$populatedTables = 0;

foreach ($tables as $table) {
    if (in_array($table, ['migrations', 'jobs', 'failed_jobs', 'sessions', 'cache', 'cache_locks', 'job_batches', 'password_reset_tokens'])) {
        continue; // Skip Laravel internal tables
    }

    try {
        $count = DB::table($table)->count();
    } catch (\Exception $e) {
        $count = "ERR";
    }

    $module = inferModule($table);
    $status = ($count > 0) ? "✅ Has Data" : "⚠️ Empty";
    
    if ($count > 0) $populatedTables++;
    else $emptyTables++;
    
    // Count modules
    if (!isset($modules[$module])) $modules[$module] = 0;
    $modules[$module]++;

    $report .= "| `$table` | $count | **$module** | $status | ⬜ Pending |\n";
}

$report .= "\n\n## Summary Statistics\n";
$report .= "- **Total Legacy Tables:** " . ($populatedTables + $emptyTables) . "\n";
$report .= "- **Tables With Data:** $populatedTables\n";
$report .= "- **Empty Tables:** $emptyTables\n\n";

$report .= "## Module Breakdown (Estimated)\n";
foreach ($modules as $name => $count) {
    $report .= "- **$name**: $count tables\n";
}

function inferModule($t) {
    if (str_contains($t, 'carrier')) return 'Carriers';
    if (str_contains($t, 'service')) return 'Services';
    if (str_contains($t, 'consignment')) return 'Consignments';
    if (str_contains($t, 'manifest') || str_contains($t, 'bag')) return 'Operations (Manifest/Bag)';
    if (str_contains($t, 'warehouse') || str_contains($t, 'scan')) return 'Warehouse';
    if (str_contains($t, 'user') || str_contains($t, 'role') || str_contains($t, 'admin')) return 'Users & Auth';
    if (str_contains($t, 'tariff') || str_contains($t, 'cost') || str_contains($t, 'charge') || str_contains($t, 'invoice')) return 'Finance';
    if (str_contains($t, 'log') || str_contains($t, 'api_')) return 'Logs & API';
    if (str_contains($t, 'address') || str_contains($t, 'country') || str_contains($t, 'zone') || str_contains($t, 'postcode')) return 'Geographic Data';
    if (str_contains($t, 'label') || str_contains($t, 'print')) return 'Labels & Printing';
    
    return 'Other / Utils';
}

file_put_contents(base_path('../DATABASE_AUDIT_LOG.md'), $report);
echo "Audit report generated at ../DATABASE_AUDIT_LOG.md";
