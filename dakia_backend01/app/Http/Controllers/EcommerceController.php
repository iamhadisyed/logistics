<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EcommerceController extends Controller
{
    public function index()
    {
        return response()->json([
            'customerData' => [
                [
                    'customerId' => '1',
                    'fullName' => 'John Doe',
                    'email' => 'john@example.com',
                    'status' => 'active',
                    'totalSpent' => 1250.00,
                    'avatar' => null
                ]
            ],
            'orderData' => [
                [
                    'order' => '1',
                    'customerName' => 'John Doe',
                    'email' => 'john@example.com',
                    'status' => 'completed',
                    'total' => 1250.00,
                    'date' => '2024-01-15'
                ]
            ],
            'products' => [
                [
                    'id' => '1',
                    'name' => 'Product 1',
                    'category' => 'Electronics',
                    'price' => 299.99,
                    'stock' => 50,
                    'status' => 'in-stock'
                ]
            ]
        ]);
    }
} 