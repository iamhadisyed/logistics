<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Restoring email unique index...\n";

try {
    Schema::table('users', function (Blueprint $table) {
        $table->unique('email');
    });
    echo "Index restored.\n";
} catch (\Exception $e) {
    echo "Notice: " . $e->getMessage() . "\n";
}
