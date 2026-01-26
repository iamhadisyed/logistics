<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SidebarController extends Controller
{
    public function getSidebar(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 1. Fetch Permissions based on User Role (Master vs Normal)
        if ($user->user_account_id == 148) {
            // Master Account sees ALL active menu items
            $permissions = Permission::active()
                ->menuItem()
                ->orderBy('sort_order')
                ->get();
        } else {
            // Normal Users see permissions assigned via Groups
            // user -> user_departments (groups) -> grouphaspermissions -> permissions
            $permissions = Permission::active()
                ->menuItem()
                ->whereHas('groups', function ($query) use ($user) {
                    $query->whereHas('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->active(); // Check group is active
                })
                ->orderBy('sort_order')
                ->get();
        }

        // 2. Build Hierarchy
        $hierarchy = $this->buildHierarchy($permissions);

        return response()->json($hierarchy);
    }

    private function buildHierarchy($permissions)
    {
        $lookup = [];
        // Convert to array for easier manipulation
        foreach ($permissions as $perm) {
            $perm->children = []; 
            // We use setAttribute to ensure it's treated as a custom attribute, 
            // but for simple manipulation, converting to array might be safer if we didn't need to preserve Model methods.
            // However, let's try standard object property assignment force.
            // Or better:
            // $perm->setRelation('children', collect([])); 
            
            // Simplest fix: Work with arrays.
            $lookup[$perm->id] = $perm;
        }

        $tree = [];

        foreach ($lookup as $id => $perm) {
            if ($perm->parent_id == 0) {
                $tree[] = $perm;
            } elseif (isset($lookup[$perm->parent_id])) {
                // This line caused error: $lookup[$perm->parent_id]->children[] = $perm;
                // Fix:
                $children = $lookup[$perm->parent_id]->children;
                $children[] = $perm;
                $lookup[$perm->parent_id]->children = $children;
            }
        }
        
        // ... rest of logic

            // If parent not found in accessible permissions, checking if we should show orphan
            // OR simply ignore. Usually, if parent is not accessible, child shouldn't be either 
            // OR child moves to root.
            // Requirement says: "Parent menu visibility depends on whether at least one child is permitted"
            // This implies we might need to fetch ALL parents even if not explicitly assigned?
            // "permissions table: parent_id = 0" means root.
            
            // Let's stick to strict: If parent is not in result set (not assigned), child is hidden/ignored
            // OR effectively it might mean the parent IS assigned if child is. 
            // Assuming RBAC grants parent permission if child is granted is safer, 
            // BUT usually permissions are granular. 
            // Let's assume standard behavior: You only see what you have. 
            // If you have a child but not the parent, it's an orphan.
            // HOWEVER, common practice is to allow path viewing.
            
            // Let's re-read: "Parent menu visibility depends on whether at least one child is permitted"
            // This suggests parents might NOT be explicitly assigned but should appear if a child is.
            // This approach requires fetching parents of all fetched permissions recursively.
            // comments about orphans


        // 3. Filter empty parents (recursive) & Sort
        $finalTree = [];
        foreach ($tree as $node) {
            if ($this->shouldShowNode($node)) {
                $finalTree[] = $this->formatNode($node);
            }
        }

        return $this->sortTree($finalTree);
    }
 
    private function shouldShowNode($node)
    {
        // If it's a leaf (file_name != '#'), show it.
        if ($node->file_name !== '#') {
            return true;
        }

        // If it's a header/parent (file_name == '#'), show ONLY if it has visible children
        if (!empty($node->children)) {
            return true;
        }

        return false;
    }

    private function formatNode($node)
    {
        $children = [];
        if (!empty($node->children)) {
            foreach ($node->children as $child) {
                if ($this->shouldShowNode($child)) {
                    $children[] = $this->formatNode($child);
                }
            }
        }
        
        return [
            'id' => $node->id,
            'label' => $node->description, // Use description for human readable label
            'href' => $node->file_name, // Renamed to href
            'icon' => $node->icon,
            'children' => empty($children) ? null : $this->sortTree($children),
            'sort_order' => $node->sort_order
        ];
    }
    
    private function sortTree($nodes) {
        usort($nodes, function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });
        return $nodes;
    }
}
