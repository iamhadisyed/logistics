<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\SidebarController;

// 1. Test as Master Admin
$email = 'admin@example.com';
$user = User::where('email', $email)->first();

if (!$user) {
    die("Admin user not found.\n");
}

echo "Testing Sidebar for User: {$user->email} (Account ID: {$user->user_account_id})\n";

// Mock Request
$request = Request::create('/api/sidebar', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

$controller = new SidebarController();
$response = $controller->getSidebar($request);

$content = $response->getContent();
$data = json_decode($content, true);

echo "Response Status: " . $response->getStatusCode() . "\n";
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Decode Error: " . json_last_error_msg() . "\n";
    echo "Raw Content: " . substr($content, 0, 500) . "...\n";
    exit;
}

echo "Root Items Count: " . count($data) . "\n";

// Display first level
foreach ($data as $item) {
    $childCount = isset($item['children']) ? count($item['children']) : 0;
    
    // Map label/href since controller was updated
    $title = $item['label'] ?? 'No Label';
    $path = $item['href'] ?? 'No Path';
    echo "- [{$item['id']}] {$title} ({$path}) - Children: {$childCount}\n";
    
    // Show one level deep
    if (!empty($item['children'])) {
        foreach ($item['children'] as $child) {
             $cTitle = $child['label'] ?? 'No Label';
             $cPath = $child['href'] ?? 'No Path';
            echo "  -- [{$child['id']}] {$cTitle} ({$cPath})\n";
        }
    }
}
