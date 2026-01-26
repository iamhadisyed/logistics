<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarrierController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Public Auth Routes
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Carrier routes
    Route::apiResource('carriers', CarrierController::class);
    Route::post('carriers/{id}/status', [CarrierController::class, 'updateStatus']);

    // Service routes
    Route::apiResource('services', ServiceController::class);
    Route::get('services/available', [ServiceController::class, 'available']);

    // Shipment routes
    Route::apiResource('shipments', \App\Http\Controllers\Api\ShipmentController::class);
    Route::post('shipments/{id}/generate-label', [\App\Http\Controllers\Api\ShipmentController::class, 'generateLabel']);
    
    // Dynamic Sidebar
    Route::get('sidebar', [\App\Http\Controllers\Api\SidebarController::class, 'getSidebar']);

    // Country routes
    Route::apiResource('countries', \App\Http\Controllers\Logistics\CountryController::class);


    // Account routes
    Route::middleware(['master_account'])->group(function () {
        // Account CRUD routes
        Route::get('accounts', [AccountController::class, 'index']);
        Route::post('accounts', [AccountController::class, 'store']);
        Route::get('accounts/{id}', [AccountController::class, 'show']);
        Route::put('accounts/{id}', [AccountController::class, 'update']);
        Route::delete('accounts/{id}', [AccountController::class, 'destroy']);
        
        // User management under account context
        Route::get('accounts/{account}/users', [AccountController::class, 'getUsers']);
        Route::post('accounts/{account}/users', [AccountController::class, 'storeUser']);
        
        // Direct user management (still protected by master_account)
        Route::get('users', [AccountController::class, 'getAllUsers']);
        Route::put('users/{user}', [AccountController::class, 'updateUser']);
        Route::delete('users/{user}', [AccountController::class, 'destroyUser']);
        
        // RBAC / Permission routes
        Route::get('users/{user}/permissions', [AccountController::class, 'getPermissions']);
        Route::post('users/{user}/assign-permissions', [AccountController::class, 'assignPermissions']);
        Route::delete('users/{user}/permissions/{group}', [AccountController::class, 'removePermission']);
        Route::put('users/{user}/update-role', [AccountController::class, 'updateRole']);
        
        // Group/Role routes
        Route::get('groups', [AccountController::class, 'getGroups']);
        Route::post('users/{user}/groups', [AccountController::class, 'assignGroup']);

        // Account Services routes
        Route::get('accounts/{account}/services', [\App\Http\Controllers\Api\ServiceController::class, 'getAccountServices']);
        Route::post('accounts/{account}/services', [\App\Http\Controllers\Api\ServiceController::class, 'assignAccountServices']);
        Route::delete('accounts/{account}/services/{service}', [\App\Http\Controllers\Api\ServiceController::class, 'removeAccountService']);

        // User Services routes
        Route::get('users/{user}/services', [\App\Http\Controllers\Api\ServiceController::class, 'getUserServices']);
        Route::post('users/{user}/assign-services', [\App\Http\Controllers\Api\ServiceController::class, 'assignUserServices']);

        // Service Routing routes
        Route::get('services/{service}/routing', [\App\Http\Controllers\Api\ServiceController::class, 'getServiceRouting']);
        Route::post('services/{service}/routing', [\App\Http\Controllers\Api\ServiceController::class, 'updateServiceRouting']);
        Route::delete('services/{service}/routing/{rule}', [\App\Http\Controllers\Api\ServiceController::class, 'removeServiceRouting']);

        // Utility routes
        Route::get('all-services', [\App\Http\Controllers\Api\ServiceController::class, 'getAllServices']);
        Route::get('all-carriers', [\App\Http\Controllers\Api\ServiceController::class, 'getAllCarriers']);
    });
});
