<?php

$file = __DIR__ . '/../database/seeders/LegacyDataSeeder.php';
$lines = file($file);

// Check lines around 44019 (0-indexed array so 44018)
$targetLineIndex = 44018; 

echo "Line " . ($targetLineIndex+1) . ": " . $lines[$targetLineIndex];
