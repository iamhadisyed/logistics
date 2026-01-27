<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 494,
                'lang_key' => 'LEFT_MENU_SERVICES',
                'parent_id' => 0,
                'file_name' => '#',
                'description' => 'Services',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-service-line',
                'sort_order' => 30,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            1 => 
            array (
                'id' => 495,
                'lang_key' => 'LEFT_MENU_SERVICES_LIST',
                'parent_id' => 494,
                'file_name' => 'services',
                'description' => 'List Services',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 1,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            2 => 
            array (
                'id' => 496,
                'lang_key' => 'LEFT_MENU_ACCOUNTS_LIST',
                'parent_id' => 503,
                'file_name' => 'accounts',
                'description' => 'List Accounts',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 1,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            3 => 
            array (
                'id' => 497,
                'lang_key' => 'LEFT_MENU_USERS',
                'parent_id' => 0,
                'file_name' => '#',
                'description' => 'Users',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-user-line',
                'sort_order' => 50,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            4 => 
            array (
                'id' => 498,
                'lang_key' => 'LEFT_MENU_USERS_LIST',
                'parent_id' => 497,
                'file_name' => 'users',
                'description' => 'List Users',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 1,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            5 => 
            array (
                'id' => 499,
                'lang_key' => 'LEFT_MENU_SHIPMENTS',
                'parent_id' => 0,
                'file_name' => '#',
                'description' => 'Shipments',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-truck-line',
                'sort_order' => 10,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            6 => 
            array (
                'id' => 500,
                'lang_key' => 'LEFT_MENU_SHIPMENTS_LIST',
                'parent_id' => 499,
                'file_name' => 'shipments',
                'description' => 'List Shipments',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 1,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            7 => 
            array (
                'id' => 501,
                'lang_key' => 'LEFT_MENU_SHIPMENTS_CREATE',
                'parent_id' => 499,
                'file_name' => 'shipments/create',
                'description' => 'Create Shipment',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 2,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            8 => 
            array (
                'id' => 502,
                'lang_key' => 'LEFT_MENU_CARRIERS',
                'parent_id' => 0,
                'file_name' => '#',
                'description' => 'Carriers',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-bus-wifi-line',
                'sort_order' => 20,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            9 => 
            array (
                'id' => 503,
                'lang_key' => 'LEFT_MENU_ACCOUNTS',
                'parent_id' => 0,
                'file_name' => '#',
                'description' => 'Accounts',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-user-settings-line',
                'sort_order' => 40,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            10 => 
            array (
                'id' => 504,
                'lang_key' => 'LEFT_MENU_CARRIERS_LIST',
                'parent_id' => 502,
                'file_name' => 'carriers',
                'description' => 'List Carriers',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 1,
                'is_menu_item' => 1,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            11 => 
            array (
                'id' => 505,
                'lang_key' => 'LEFT_MENU_CARRIERS_ADD',
                'parent_id' => 502,
                'file_name' => 'carriers/add',
                'description' => 'Add Carrier',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 2,
                'is_menu_item' => 0,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            12 => 
            array (
                'id' => 506,
                'lang_key' => 'LEFT_MENU_CARRIERS_EDIT',
                'parent_id' => 502,
                'file_name' => 'carriers/edit',
                'description' => 'Edit Carrier',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 3,
                'is_menu_item' => 0,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            13 => 
            array (
                'id' => 507,
                'lang_key' => 'LEFT_MENU_CARRIERS_VIEW',
                'parent_id' => 502,
                'file_name' => 'carriers/view',
                'description' => 'View Carrier',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 4,
                'is_menu_item' => 0,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
            14 => 
            array (
                'id' => 508,
                'lang_key' => 'LEFT_MENU_CARRIERS_DELETE',
                'parent_id' => 502,
                'file_name' => 'carriers/delete',
                'description' => 'Delete Carrier',
                'added_by' => NULL,
                'added_date' => NULL,
                'query_string' => NULL,
                'icon' => 'ri-circle-fill',
                'sort_order' => 5,
                'is_menu_item' => 0,
                'is_active' => 1,
                'is_deleted' => 0,
            ),
        ));
        
        
    }
}