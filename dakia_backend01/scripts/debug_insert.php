<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Extract the INSERT statement from the file
$file = __DIR__ . '/../database/seeders/LegacyDataSeeder.php';
$lines = file($file);
$insertLineIndex = 44018; // 0-indexed
$insertLine = $lines[$insertLineIndex];

// It continues until line 44132 "SQL"
// Let's just grab the whole block programmatically
$content = file_get_contents($file);
$startPos = strpos($content, "INSERT INTO `users`");
$endPos = strpos($content, "SQL", $startPos);
$sql = substr($content, $startPos, $endPos - $startPos);

echo "Attempting to run extracted SQL...\n";
try {
    DB::connection()->getPdo()->exec($sql);
    echo "Success!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
