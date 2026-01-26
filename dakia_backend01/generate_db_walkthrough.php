<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Increase memory limit for large schema analysis
ini_set('memory_limit', '512M');

$tables = Schema::getConnection()->getSchemaBuilder()->getTableListing();
sort($tables);

$output = "# Full Legacy Database Walkthrough & Logic Analysis\n\n";
$output .= "This document provides a detailed analysis of all " . count($tables) . " tables in the legacy database, explaining their structure, business logic, and relationships.\n\n";

foreach ($tables as $table) {
    // Skip internal Laravel tables to focus on legacy logic
    if (in_array($table, ['migrations', 'jobs', 'failed_jobs', 'sessions', 'cache', 'cache_locks', 'job_batches'])) {
        continue;
    }

    $columns = Schema::getColumnListing($table);
    $count = DB::table($table)->count();
    
    $output .= "## Table: `$table`\n";
    $output .= "- **Record Count:** $count\n";
    $output .= "- **Inferred Module:** " . inferModule($table) . "\n";
    
    // Analyze Columns
    $output .= "### Columns\n";
    $output .= "| Column | Type | Nullable | Key | Guess Logic |\n";
    $output .= "|--------|------|----------|-----|-------------|\n";
    
    $relationships = [];

    foreach ($columns as $column) {
        $type = Schema::getColumnType($table, $column);
        $nullable = 'No'; // Doctrine/Schema doesn't easily give nullable in simple list, skipping for speed or would need complex query. 
        // Let's use simple logic for now.
        
        $logic = "";
        
        // Infer relationships
        if (str_ends_with($column, '_id')) {
            $relatedTable = substr($column, 0, -3);
            // Pluralize check
            if (in_array($relatedTable . 's', $tables)) {
                 $relationships[] = "Belongs to `{$relatedTable}s` via `$column`";
                 $logic = "Foreign Key to {$relatedTable}s";
            } elseif (in_array($relatedTable, $tables)) {
                 $relationships[] = "Belongs to `$relatedTable` via `$column`";
                 $logic = "Foreign Key to $relatedTable";
            }
        }
        
        $output .= "| $column | $type | - | - | $logic |\n";
    }
    
    // Logic & Relationships Section
    $output .= "\n### Logic & Relationships\n";
    if (!empty($relationships)) {
        foreach ($relationships as $rel) {
            $output .= "- $rel\n";
        }
    } else {
        $output .= "- No obvious foreign keys detected by naming convention.\n";
    }
    
    // Sample Data (First row) to understand context
    $firstRow = DB::table($table)->first();
    if ($firstRow) {
        $output .= "\n### Sample Data (First Record)\n";
        $output .= "```json\n";
        // Limit sample data fields to first 5 specific ones to avoid noise
        $sampleArray = (array)$firstRow;
        $output .= json_encode(array_slice($sampleArray, 0, 5), JSON_PRETTY_PRINT);
        $output .= "\n... (more columns)\n```\n";
    }
    
    $output .= "\n---\n\n";
}

function inferModule($tableName) {
    if (str_contains($tableName, 'carrier')) return 'Carrier Management';
    if (str_contains($tableName, 'service')) return 'Service Management';
    if (str_contains($tableName, 'consignment')) return 'Consignments & Operations';
    if (str_contains($tableName, 'user')) return 'User & Access';
    if (str_contains($tableName, 'finance') || str_contains($tableName, 'cost') || str_contains($tableName, 'tariff')) return 'Finance & Pricing';
    if (str_contains($tableName, 'log')) return 'System Logging';
    return 'General / Uncategorized';
}

file_put_contents(base_path('../DATABASE_WALKTHROUGH.md'), $output);

echo "Analysis generated at ../DATABASE_WALKTHROUGH.md";
