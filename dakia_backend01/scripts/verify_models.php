<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Address;
use App\Models\Carrier;
use App\Models\Consignment;
use App\Models\Country;
use App\Models\Parcel;
use App\Models\Service;
use App\Models\User;

$models = [
    'Address' => Address::class,
    'Carrier' => Carrier::class,
    'Consignment' => Consignment::class,
    'Country' => Country::class,
    'Parcel' => Parcel::class,
    'Service' => Service::class,
    'User' => User::class,
];

foreach ($models as $name => $class) {
    try {
        $count = $class::count();
        $table = (new $class)->getTable();
        echo "$name (table: $table) count: $count\n";
    } catch (\Exception $e) {
        echo "$name ERROR: " . $e->getMessage() . "\n";
    }
}
