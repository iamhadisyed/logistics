<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Address::with(['user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $addresses = $query->orderBy('company')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone_number' => 'nullable|string|max:45',
                'company' => 'nullable|string|max:45',
                'contact' => 'nullable|string|max:45',
                'email' => 'nullable|email|max:255',
                'address_line_1' => 'nullable|string|max:45',
                'address_line_2' => 'nullable|string|max:45',
                'address_line_3' => 'nullable|string|max:45',
                'city' => 'nullable|string|max:45',
                'country' => 'nullable|string|max:3',
                'postcode' => 'nullable|string|max:45',
                'user_id' => 'nullable|exists:users,id',
                'state' => 'nullable|string|max:45',
            ]);

            $address = Address::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Address created successfully',
                'data' => $address->load(['user'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $address = Address::with(['user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $address
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $address = Address::findOrFail($id);

            $validated = $request->validate([
                'phone_number' => 'nullable|string|max:45',
                'company' => 'nullable|string|max:45',
                'contact' => 'nullable|string|max:45',
                'email' => 'nullable|email|max:255',
                'address_line_1' => 'nullable|string|max:45',
                'address_line_2' => 'nullable|string|max:45',
                'address_line_3' => 'nullable|string|max:45',
                'city' => 'nullable|string|max:45',
                'country' => 'nullable|string|max:3',
                'postcode' => 'nullable|string|max:45',
                'user_id' => 'nullable|exists:users,id',
                'state' => 'nullable|string|max:45',
            ]);

            $address->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address->load(['user'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $address = Address::findOrFail($id);
            $address->delete();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete address',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
