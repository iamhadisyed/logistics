<?php

$file = __DIR__ . '/../database/seeders/LegacyDataSeeder.php';
$temp = __DIR__ . '/../database/seeders/LegacyDataSeeder.tmp';

$handle = fopen($file, "r");
$tempHandle = fopen($temp, "w");

$count = 0;
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, "'0000-00-00'") !== false) {
            $line = str_replace("'0000-00-00'", "NULL", $line);
            $count++;
        }
        fwrite($tempHandle, $line);
    }
    fclose($handle);
    fclose($tempHandle);
    
    rename($temp, $file);
    echo "Fixed $count occurrences of invalid date.\n";
} else {
    echo "Error opening file.\n";
}
