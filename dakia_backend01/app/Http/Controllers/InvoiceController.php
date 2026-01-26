<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json([
            [
                'id' => '1',
                'invoiceNumber' => 'INV-001',
                'clientName' => 'John Doe',
                'email' => 'john@example.com',
                'total' => 1250.00,
                'status' => 'paid',
                'date' => '2024-01-15'
            ],
            [
                'id' => '2',
                'invoiceNumber' => 'INV-002',
                'clientName' => 'Jane Smith',
                'email' => 'jane@example.com',
                'total' => 850.00,
                'status' => 'pending',
                'date' => '2024-01-16'
            ]
        ]);
    }
} 