<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use App\Models\User;
use App\Models\Group;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Defensive check to ensure the user belongs to the master account (ID 148).
     * This is a hard-lock requirement.
     */
    private function checkMasterAccount(Request $request): void
    {
        if (!$request->user() || $request->user()->user_account_id != 148) {
            abort(response()->json([
                'message' => 'Unauthorized. Business logic check: Only master account users allowed.'
            ], 403));
        }
    }

    /**
     * Display a listing of user accounts.
     */
    public function index(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);
        $accounts = UserAccount::active()
            ->select('id', 'user_account', 'active_flag', 'company', 'email', 'phone')
            ->orderBy('id')
            ->get();
        return response()->json($accounts);
    }

    /**
     * Store a newly created user account.
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);
        $validator = Validator::make($request->all(), [
            'user_account' => 'required|string|max:30|unique:user_accounts',
            'company' => 'required|string|max:100',
            'full_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:500',
            'phone' => 'nullable|string|max:20',
            'user_service_type' => 'nullable|in:CHOICE,ROUTING,BOTH',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nextId = UserAccount::max('id') + 1;
        $account = UserAccount::create(array_merge($request->all(), ['id' => $nextId]));

        return response()->json([
            'id' => $account->id,
            'user_account' => $account->user_account,
            'active_flag' => $account->active_flag
        ], 201);
    }

    /**
     * Display the specified user account.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $this->checkMasterAccount($request);
        $account = UserAccount::with('users')->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        return response()->json($account);
    }

    /**
     * Update the specified user account.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkMasterAccount($request);
        $account = UserAccount::find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_account' => 'sometimes|required|string|max:30|unique:user_accounts,user_account,' . $id,
            'company' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $account->update($request->all());

        return response()->json([
            'message' => 'Account updated successfully',
            'account' => $account
        ]);
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $this->checkMasterAccount($request);
        $account = UserAccount::find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        // Soft delete: set active_flag to 0
        $account->update(['active_flag' => 0]);

        return response()->json(['message' => 'Account deactivated (soft deleted) successfully']);
    }

    /**
     * List all users across all accounts (master account only).
     */
    public function getAllUsers(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);
        
        // Fetch users and ensure uniqueness by ID
        $users = User::with('userAccount:id,user_account,company')
            ->active()
            ->select('id', 'name', 'email', 'user_type', 'active_flag', 'user_account_id', 'created_at')
            ->orderBy('id')
            ->get()
            ->unique('id')
            ->values(); // Re-index the collection after unique()

        return response()->json($users);
    }

    /**
     * List all users under a specific account.
     */
    public function getUsers(Request $request, $accountId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $account = UserAccount::find($accountId);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $users = $account->users()->active()->get();

        return response()->json($users);
    }

    /**
     * Store a newly created user for a specific account.
     */
    public function storeUser(Request $request, $accountId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $account = UserAccount::find($accountId);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'user_type' => 'nullable|in:corporate,client,admin,driver',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $account->users()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_type' => $request->user_type ?? 'client',
            'active_flag' => true,
        ]);

        return response()->json([
            'message' => 'User created and assigned successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_account_id' => $user->user_account_id,
                'active_flag' => $user->active_flag
            ]
        ], 201);
    }

    /**
     * Update a specific user.
     */
    public function updateUser(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $userId,
            'user_type' => 'nullable|in:corporate,client,admin,driver',
            'active_flag' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Delete (soft delete) a specific user.
     */
    public function destroyUser(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Soft delete: set active_flag to 0 and is_deleted to 1
        $user->update([
            'active_flag' => 0,
            'is_deleted' => 1
        ]);

        return response()->json(['message' => 'User deactivated (soft deleted) successfully']);
    }

    /**
     * Get available groups for assignment.
     */
    public function getGroups(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);
        $groups = Group::active()
            ->orderBy('group_id')
            ->get()
            ->unique('group_id')
            ->values(); // Re-index the collection after unique()
        
        return response()->json($groups);
    }

    /**
     * Get user permissions inherited via active groups.
     */
    public function getPermissions(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $user = \App\Models\User::active()->find($userId);

        if (!$user) {
            return response()->json(['message' => 'Active user not found'], 404);
        }

        return response()->json([
            'user' => $user,
            'groups' => $user->groups()->active()->get(),
            'permissions' => $user->getInheritedPermissions()
        ]);
    }

    /**
     * Assign group(s) to a user.
     */
    public function assignPermissions(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $user = \App\Models\User::active()->find($userId);

        if (!$user) {
            return response()->json(['message' => 'Active user not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,group_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Attach only groups that are active and not already assigned
        $groupIds = Group::active()->whereIn('group_id', $request->group_ids)->pluck('group_id');
        
        foreach ($groupIds as $groupId) {
            $exists = DB::table('user_departments')
                ->where('user_id', $user->id)
                ->where('department_id', $groupId)
                ->exists();

            if (!$exists) {
                $nextId = DB::table('user_departments')->max('id') + 1;
                DB::table('user_departments')->insert([
                    'id' => $nextId,
                    'user_id' => $user->id,
                    'department_id' => $groupId
                ]);
            }
        }

        return response()->json([
            'message' => 'Groups assigned successfully',
            'user' => $user->load('groups'),
            'permissions' => $user->getInheritedPermissions()
        ]);
    }

    /**
     * Remove a group from a user.
     */
    public function removePermission(Request $request, $userId, $groupId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        DB::table('user_departments')
            ->where('user_id', $userId)
            ->where('department_id', $groupId)
            ->delete();

        return response()->json(['message' => 'Group assignment removed successfully']);
    }

    /**
     * Sync user's groups (Update Role).
     */
    public function updateRole(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $user = \App\Models\User::active()->find($userId);

        if (!$user) {
            return response()->json(['message' => 'Active user not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,group_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $groupIds = Group::active()->whereIn('group_id', $request->group_ids)->pluck('group_id');
        
        // Manual Sync
        DB::table('user_departments')->where('user_id', $user->id)->delete();
        
        foreach ($groupIds as $groupId) {
            $nextId = DB::table('user_departments')->max('id') + 1;
            DB::table('user_departments')->insert([
                'id' => $nextId,
                'user_id' => $user->id,
                'department_id' => $groupId
            ]);
        }

        return response()->json([
            'message' => 'User roles (groups) updated successfully',
            'user' => $user->load('groups'),
            'permissions' => $user->getInheritedPermissions()
        ]);
    }

    /**
     * Legacy assignGroup helper (still useful for single assignments)
     */
    public function assignGroup(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,group_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exists = DB::table('user_departments')
            ->where('user_id', $user->id)
            ->where('department_id', $request->group_id)
            ->exists();

        if (!$exists) {
            $nextId = DB::table('user_departments')->max('id') + 1;
            DB::table('user_departments')->insert([
                'id' => $nextId,
                'user_id' => $user->id,
                'department_id' => $request->group_id
            ]);
        }

        return response()->json(['message' => 'Group assigned successfully']);
    }
}
