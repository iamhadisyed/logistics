<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$entities = ['users', 'user_accounts', 'consignments', 'shipments', 'parcels', 'agent_data'];
foreach ($entities as $entity) {
    try {
        $count = DB::table($entity)->count();
        echo "$entity: $count\n";
    } catch (\Exception $e) {
        echo "$entity: TABLE MISSING\n";
    }
}
