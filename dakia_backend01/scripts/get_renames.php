<?php

$content = file_get_contents('c:/daakia/COMPLETE_TABLE_NAMING_ANALYSIS.md');
$lines = explode("\n", $content);

$ups = [];
$downs = [];
foreach ($lines as $line) {
    if (strpos($line, '⚠️ RENAME') !== false) {
        $parts = explode('|', $line);
        if (count($parts) >= 6) {
            $old = trim($parts[2]);
            $new = trim($parts[3]);
            
            // Skip already processed
            if (in_array($old, ['address', 'carrier', 'consignment', 'country', 'parcel', 'user'])) continue;
            
            $ups[] = "        Schema::rename('$old', '$new');";
            $downs[] = "        Schema::rename('$new', '$old');";
        }
    }
}

echo "UP:\n" . implode("\n", $ups) . "\n\nDOWN:\n" . implode("\n", array_reverse($downs));
