<?php

$file = __DIR__ . '/../database/seeders/LegacyDataSeeder.php';
$temp = __DIR__ . '/../database/seeders/LegacyDataSeeder.tmp';

$handle = fopen($file, "r");
$tempHandle = fopen($temp, "w");

$found = false;
$lineNum = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        // Check for the specific insert line
        if (strpos($line, "INSERT INTO `user`") !== false && strpos($line, "`user_name`") !== false) {
            echo "Found matching line at $lineNum\n";
            $line = str_replace("INSERT INTO `user`", "INSERT INTO `users`", $line);
            $line = str_replace("`user_name`", "`name`", $line);
            $line = str_replace("`user_pass`", "`password`", $line);
            $line = str_replace("`api_secert`", "`api_secret`", $line);
            $found = true;
        }
        fwrite($tempHandle, $line);
    }
    fclose($handle);
    fclose($tempHandle);
    
    if ($found) {
        rename($temp, $file);
        echo "File updated successfully.\n";
    } else {
        unlink($temp);
        echo "Target line not found.\n";
    }
} else {
    echo "Error opening file.\n";
}
