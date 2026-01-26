<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function faq()
    {
        return response()->json([
            'faqData' => [
                [
                    'id' => '1',
                    'question' => 'How do I reset my password?',
                    'answer' => 'You can reset your password by clicking on the forgot password link on the login page.'
                ],
                [
                    'id' => '2',
                    'question' => 'How do I update my profile?',
                    'answer' => 'You can update your profile by going to the user profile section in your dashboard.'
                ]
            ]
        ]);
    }

    public function pricing()
    {
        return response()->json([
            'pricingData' => [
                [
                    'id' => '1',
                    'title' => 'Basic',
                    'price' => 29.99,
                    'features' => ['Feature 1', 'Feature 2', 'Feature 3']
                ],
                [
                    'id' => '2',
                    'title' => 'Pro',
                    'price' => 59.99,
                    'features' => ['All Basic features', 'Feature 4', 'Feature 5']
                ]
            ]
        ]);
    }

    public function profile()
    {
        return response()->json([
            'profileData' => [
                'user' => [
                    'id' => '1',
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'avatar' => null,
                    'role' => 'admin'
                ],
                'stats' => [
                    'projects' => 25,
                    'tasks' => 150,
                    'completed' => 120
                ]
            ]
        ]);
    }

    public function statistics()
    {
        return response()->json([
            'statisticsData' => [
                'customerStats' => [
                    'totalCustomers' => 1250,
                    'newCustomers' => 45,
                    'activeCustomers' => 1100
                ],
                'salesStats' => [
                    'totalSales' => 125000,
                    'monthlySales' => 15000,
                    'growth' => 12.5
                ]
            ]
        ]);
    }
} 