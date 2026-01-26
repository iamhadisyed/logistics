$importFile = "c:\daakia\dakia_backend01\database\migrations\2025_12_09_000001_legacy_schema_import.php"
$dumpFile = "c:\daakia\logistics.sql"

$imported = @()
Get-Content $importFile | ForEach-Object {
    # Match singular quoted table name in migration
    if ($_ -match 'CREATE TABLE IF NOT EXISTS \`(\w+)\`') {
        $imported += $matches[1]
    }
}
$imported = $imported | Sort-Object -Unique

$dumped = @()
Get-Content $dumpFile | ForEach-Object {
    # Match CREATE TABLE statement in dump
    if ($_ -match 'CREATE TABLE\s+(IF\s+NOT\s+EXISTS\s+)?\`?(\w+)\`?') {
        $dumped += $matches[2]
    }
}
# Filter invalid captures
$dumped = $dumped | Where-Object { $_ -ne "IF" -and $_ -ne "NOT" -and $_ -ne "EXISTS" } | Sort-Object -Unique

Write-Output "# Database Table Audit"
Write-Output "Generated on $(Get-Date)"
Write-Output ""
Write-Output '| Table Name (Legacy) | Imported? | Expected Plural | Status |'
Write-Output '|---|---|---|---|'

$missingCount = 0

foreach ($table in $dumped) {
    $isImported = $imported -contains $table

    $plural = $table
    # Basic pluralization rules
    if ($table -match 'y$') {
        $plural = $table -replace 'y$', 'ies'
    } elseif ($table -notmatch 's$') {
        $plural = $table + 's'
    }
    # Already plural or specific exclusions could be added here
    # Assuming s$ is plural for now (not perfect but OK for audit)
    
    if ($isImported) {
         if ($table -ne $plural) {
             $status = "⚠️ Needs Rename"
         } else {
             $status = "✅ OK"
         }
         $impStr = "Yes"
    } else {
        $status = "❌ Missing"
        $impStr = "No"
    }

    Write-Output "| $table | $impStr | $plural | $status |"
    
    if (-not $isImported) { $missingCount++ }
}

Write-Output ""
Write-Output "Total Legacy Tables: $($dumped.Count)"
Write-Output "Total Imported: $($imported.Count)"
Write-Output "Missing: $missingCount"

Write-Output ""
Write-Output "## Extra Tables in Migration"
foreach ($table in $imported) {
    if ($dumped -notcontains $table) {
        Write-Output "- $table"
    }
}
