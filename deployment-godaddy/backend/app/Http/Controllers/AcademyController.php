<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademyController extends Controller
{
    public function index()
    {
        return response()->json([
            'courses' => [
                [
                    'id' => '1',
                    'title' => 'Web Development Fundamentals',
                    'instructor' => 'John Smith',
                    'category' => 'Programming',
                    'rating' => 4.5,
                    'students' => 1250,
                    'price' => 99.99,
                    'status' => 'active'
                ],
                [
                    'id' => '2',
                    'title' => 'Advanced JavaScript',
                    'instructor' => 'Jane Doe',
                    'category' => 'Programming',
                    'rating' => 4.8,
                    'students' => 890,
                    'price' => 149.99,
                    'status' => 'active'
                ]
            ],
            'instructors' => [
                [
                    'id' => '1',
                    'name' => 'John Smith',
                    'specialization' => 'Web Development',
                    'rating' => 4.7,
                    'students' => 2500
                ]
            ]
        ]);
    }
} 