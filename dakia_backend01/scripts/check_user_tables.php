<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Table Row Counts ===\n\n";
echo "user table count: " . DB::table('user')->count() . "\n";
echo "users table count: " . DB::table('users')->count() . "\n";
