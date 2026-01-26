<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        
        return response()->json([
            'userData' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'fullName' => $user->name,
                    'username' => $user->email,
                    'email' => $user->email,
                    'role' => 'admin',
                    'status' => 'active',
                    'avatar' => null,
                    'billing' => 'auto-debit'
                ];
            })
        ]);
    }

    public function permissions()
    {
        return response()->json([
            'permissionsData' => [
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'assignedTo' => ['admin'],
                    'createdDate' => '2024-01-01',
                    'permissions' => ['read', 'write', 'delete']
                ]
            ]
        ]);
    }

    public function roles()
    {
        return response()->json([
            'userData' => [
                [
                    'id' => 1,
                    'fullName' => 'Admin User',
                    'username' => 'admin',
                    'email' => 'admin@example.com',
                    'role' => 'admin',
                    'status' => 'active',
                    'avatar' => null,
                    'billing' => 'auto-debit'
                ]
            ]
        ]);
    }
} 