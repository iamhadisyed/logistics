<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class SyncPermissionsSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting Permission Sync...');

        // Source of Truth Definitions
        // Structure: Parent -> Children
        $modules = [
            [
                'key' => 'LEFT_MENU_SHIPMENTS',
                'label' => 'Shipments',
                'file_name' => '#',
                'icon' => 'ri-truck-line',
                'sort_order' => 10,
                'children' => [
                    [
                        'key' => 'LEFT_MENU_SHIPMENTS_LIST',
                        'label' => 'List Shipments',
                        'file_name' => 'shipments',
                        'sort_order' => 1,
                        'actions' => ['generate-label']
                    ],
                    [
                         'key' => 'LEFT_MENU_SHIPMENTS_CREATE',
                         'label' => 'Create Shipment',
                         'file_name' => 'shipments/create',
                         'sort_order' => 2
                    ]
                ]
            ],
            [
                'key' => 'LEFT_MENU_CARRIERS',
                'label' => 'Carriers',
                'file_name' => '#',
                'icon' => 'ri-bus-wifi-line',
                'sort_order' => 20,
                'children' => [
                     [
                        'key' => 'LEFT_MENU_CARRIERS_LIST',
                        'label' => 'List Carriers',
                        'file_name' => 'carriers',
                        'sort_order' => 1,
                        'is_menu_item' => 1
                     ],
                     [
                        'key' => 'LEFT_MENU_CARRIERS_ADD',
                        'label' => 'Add Carrier',
                        'file_name' => 'carriers/add',
                        'sort_order' => 2,
                        'is_menu_item' => 0
                     ],
                     [
                        'key' => 'LEFT_MENU_CARRIERS_EDIT',
                        'label' => 'Edit Carrier',
                        'file_name' => 'carriers/edit',
                        'sort_order' => 3,
                        'is_menu_item' => 0
                     ],
                     [
                        'key' => 'LEFT_MENU_CARRIERS_VIEW',
                        'label' => 'View Carrier',
                        'file_name' => 'carriers/view',
                        'sort_order' => 4,
                        'is_menu_item' => 0
                     ],
                     [
                        'key' => 'LEFT_MENU_CARRIERS_DELETE',
                        'label' => 'Delete Carrier',
                        'file_name' => 'carriers/delete',
                        'sort_order' => 5,
                        'is_menu_item' => 0
                     ]
                ]
            ],
            [
                'key' => 'LEFT_MENU_SERVICES',
                'label' => 'Services',
                'file_name' => '#',
                'icon' => 'ri-service-line',
                'sort_order' => 30,
                'children' => [
                     [
                        'key' => 'LEFT_MENU_SERVICES_LIST',
                        'label' => 'List Services',
                        'file_name' => 'services',
                        'sort_order' => 1
                     ]
                ]
            ],
            [
                'key' => 'LEFT_MENU_ACCOUNTS',
                'label' => 'Accounts',
                'file_name' => '#',
                'icon' => 'ri-user-settings-line',
                'sort_order' => 40,
                'children' => [
                     [
                        'key' => 'LEFT_MENU_ACCOUNTS_LIST',
                        'label' => 'List Accounts',
                        'file_name' => 'accounts',
                        'sort_order' => 1
                     ]
                ]
            ],
             [
                'key' => 'LEFT_MENU_USERS',
                'label' => 'Users',
                'file_name' => '#',
                'icon' => 'ri-user-line',
                'sort_order' => 50,
                'children' => [
                     [
                        'key' => 'LEFT_MENU_USERS_LIST',
                        'label' => 'List Users',
                        'file_name' => 'users',
                        'sort_order' => 1
                     ]
                ]
            ]
        ];

        DB::beginTransaction();

        try {
            foreach ($modules as $module) {
                // 1. Process Parent
                // Try to find by key first (to catch existing legacy ones with empty path)
                $parent = Permission::where('lang_key', $module['key'])->first();

                if (!$parent) {
                    $this->command->info("Creating Parent: {$module['label']}");
                    
                    $nextId = Permission::max('id') + 1;
                    
                    $parent = Permission::create([
                        'id' => $nextId,
                        'lang_key' => $module['key'],
                        'description' => $module['label'],
                        'file_name' => '#',
                        'parent_id' => 0,
                        'is_menu_item' => 1,
                        'is_active' => 1,
                        'is_deleted' => 0,
                        'sort_order' => $module['sort_order'],
                        'icon' => $module['icon'] ?? 'ri-circle-fill'
                    ]);
                } else {
                     $this->command->info("Updating Parent: {$module['label']}");
                     $parent->file_name = '#'; // Enforce hash for parent
                     $parent->is_menu_item = 1;
                     $parent->save();
                }

                // 2. Process Children
                if (!empty($module['children'])) {
                    foreach ($module['children'] as $child) {
                        $permission = Permission::where('file_name', $child['file_name'])->first();

                        if (!$permission) {
                            $this->command->info("  Creating Child: {$child['label']} ({$child['file_name']})");
                            
                            $nextId = Permission::max('id') + 1;
                            
                            $permission = Permission::create([
                                'id' => $nextId,
                                'lang_key' => $child['key'],
                                'description' => $child['label'],
                                'file_name' => $child['file_name'],
                                'parent_id' => $parent->id,
                                'is_menu_item' => $child['is_menu_item'] ?? 1, // Use defined value or default to 1
                                'is_active' => 1,
                                'is_deleted' => 0,
                                'sort_order' => $child['sort_order'],
                                'icon' => 'ri-circle-fill' // Default child icon
                            ]);
                        } else {
                            $this->command->info("  Child Exists: {$child['label']}");
                            // Ensure parent link is correct if it was orphaned or under different parent
                            if ($permission->parent_id != $parent->id) {
                                $permission->parent_id = $parent->id;
                                $permission->save();
                                $this->command->warn("    Re-parented to {$module['label']}");
                            }
                            // Update is_menu_item if specified in definition
                            if (isset($child['is_menu_item']) && $permission->is_menu_item != $child['is_menu_item']) {
                                $permission->is_menu_item = $child['is_menu_item'];
                                $permission->save();
                                $this->command->info("    Updated is_menu_item to {$child['is_menu_item']}");
                            }
                        }
                        
                        // 3. Process Hidden Actions (Children of the Child page, or same level hidden)
                        // Usually actions like 'generate-label' are POST routes, not sidebar items.
                        // But we might want permissions for them.
                        if (!empty($child['actions'])) {
                            foreach ($child['actions'] as $actionParams) {
                                // Allow string or object
                                $actionName = is_string($actionParams) ? $actionParams : $actionParams['name'];
                                // Construct unique file_name/key for action if not provided?
                                // Usually actions are attached to permissions via a different table (Ability?)
                                // But here 'permissions' table handles both menu and access.
                                // Let's simplify: Only focusing on MENU structure as requested.
                                // The prompt said: "If the module is an action-only feature..., set is_menu_item = 0"
                                
                                // Taking 'ag-consignments/generate-label' as example. 
                                // Actually, typically backend routes. But prompts implies frontend view logic maybe?
                                // Let's skip detailed action entries unless we have specific routes for them.
                            }
                        }
                    }
                }
            }
            
            DB::commit();
            $this->command->info('Sync Complete!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error($e->getMessage());
        }
    }
}
