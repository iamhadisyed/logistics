<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\Logistics\ConsignmentController;
use App\Http\Controllers\Logistics\CarrierController;
use App\Http\Controllers\Logistics\ServiceController;
use App\Http\Controllers\Logistics\AddressController;
use App\Http\Controllers\Logistics\CountryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// User Management
Route::get('/apps/user-list', [UserController::class, 'index']);
Route::get('/apps/permissions', [UserController::class, 'permissions']);
Route::get('/apps/roles', [UserController::class, 'roles']);

// Ecommerce Routes
Route::get('/apps/ecommerce', [EcommerceController::class, 'index']);

// Invoice Routes
Route::get('/apps/invoice', [InvoiceController::class, 'index']);

// Academy Routes
Route::get('/apps/academy', [AcademyController::class, 'index']);

// Pages Routes
Route::get('/pages/faq', [PagesController::class, 'faq']);
Route::get('/pages/pricing', [PagesController::class, 'pricing']);
Route::get('/pages/profile', [PagesController::class, 'profile']);
Route::get('/pages/widget-examples', [PagesController::class, 'statistics']);

// Logistics API Routes
Route::prefix('logistics')->group(function () {
    // Consignments
    Route::get('/consignments', [ConsignmentController::class, 'index']);
    Route::post('/consignments', [ConsignmentController::class, 'store']);
    Route::get('/consignments/{id}', [ConsignmentController::class, 'show']);
    Route::put('/consignments/{id}', [ConsignmentController::class, 'update']);
    Route::delete('/consignments/{id}', [ConsignmentController::class, 'destroy']);
    Route::get('/consignments/statistics', [ConsignmentController::class, 'statistics']);
    
    // Carriers
    Route::get('/carriers', [CarrierController::class, 'index']);
    Route::post('/carriers', [CarrierController::class, 'store']);
    Route::get('/carriers/{id}', [CarrierController::class, 'show']);
    Route::put('/carriers/{id}', [CarrierController::class, 'update']);
    Route::delete('/carriers/{id}', [CarrierController::class, 'destroy']);
    
    // Services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    
    // Addresses
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::get('/addresses/{id}', [AddressController::class, 'show']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    
    // Countries
    Route::get('/countries', [CountryController::class, 'index']);
    Route::post('/countries', [CountryController::class, 'store']);
    Route::get('/countries/{id}', [CountryController::class, 'show']);
    Route::put('/countries/{id}', [CountryController::class, 'update']);
    Route::delete('/countries/{id}', [CountryController::class, 'destroy']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
}); 