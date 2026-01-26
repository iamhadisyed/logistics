<?php

use App\Models\AgConsignment;
use App\Models\AgParcel;
use App\Models\AgItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Starting AgConsignment Module Verification ---\n";

DB::beginTransaction();

try {
    // 1. Create a dummy user if none exists
    $user = User::first() ?? User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'active_flag' => 1
    ]);

    echo "Using User: {$user->email}\n";

    // 2. Prepare nested payload
    $payload = [
        'consignment' => [
            'customer_id' => $user->id,
            'service_type' => 'Express',
            'warehouse_id' => 1,
            'reference' => 'VERIFY-' . time(),
            'notes' => 'Bulk test consignment'
        ],
        'parcels' => [
            [
                'weight' => 5.5,
                'length' => 20,
                'width' => 15,
                'height' => 10,
                'notes' => 'Parcel 1 notes',
                'items' => [
                    ['description' => 'Laptop', 'quantity' => 1, 'weight' => 2.5, 'value' => 1200],
                    ['description' => 'Mouse', 'quantity' => 1, 'weight' => 0.2, 'value' => 50]
                ]
            ],
            [
                'weight' => 2.0,
                'length' => 10,
                'width' => 10,
                'height' => 5,
                'notes' => 'Parcel 2 notes',
                'items' => [
                    ['description' => 'Cables', 'quantity' => 2, 'weight' => 0.5, 'value' => 20]
                ]
            ]
        ]
    ];

    echo "Testing store logic...\n";
    $controller = new \App\Http\Controllers\Api\AgConsignmentController();
    $request = new \App\Http\Requests\StoreAgConsignmentRequest();
    $request->merge($payload);
    
    // Manually trigger transactional store logic (since we can't easily mock the Request validation in a script)
    $response = $controller->store($request);

    $responseData = json_decode($response->getContent(), true);
    $consignmentId = $responseData['data']['id'];
    echo "Consignment created with ID: {$consignmentId}\n";

    // 3. Verify Database
    echo "Verifying database counts...\n";
    $consignment = AgConsignment::with('parcels.items')->find($consignmentId);
    
    if ($consignment->parcels->count() !== 2) throw new \Exception("Parcel count mismatch");
    if ($consignment->parcels[0]->items->count() !== 2) throw new \Exception("Parcel 1 item count mismatch");
    if ($consignment->parcels[1]->items->count() !== 1) throw new \Exception("Parcel 2 item count mismatch");
    
    echo "Database verification: SUCCESS\n";

    // 4. Test Label Generation
    echo "Testing label generation...\n";
    $labelResponse = $controller->generateLabel($consignmentId);
    $labelData = json_decode($labelResponse->getContent(), true);
    
    if ($labelData['message'] !== 'Label generated successfully') throw new \Exception("Label generation failed");
    
    $consignment->refresh();
    if (!$consignment->label_generated) throw new \Exception("Label generated flag not set");
    if ($consignment->status !== 'label_generated') throw new \Exception("Consignment status not updated");

    echo "Label generation test: SUCCESS\n";

    // 5. Test Duplicate Label Prevention
    echo "Testing double label prevention...\n";
    $dupResponse = $controller->generateLabel($consignmentId);
    if ($dupResponse->getStatusCode() !== 422) throw new \Exception("Double label generation not prevented");
    
    echo "Double label prevention test: SUCCESS\n";

    echo "--- All Backend Verifications PASSED ---\n";
} catch (\Exception $e) {
    echo "Verification FAILED: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "Database cleaned (rollback).\n";
}
