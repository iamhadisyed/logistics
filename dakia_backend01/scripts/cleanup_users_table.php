<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Cleaning up users table columns...\n";

Schema::table('users', function (Blueprint $table) {
    $columns = [
        'user_type', 'user_name', 'active_flag', 'first_name', 'last_name',
        'address', 'phone', 'country_id', 'api_key', 'api_secret',
        'api_date', 'profile_image', 'is_employee', 'warehouse_id',
        'dashboard', 'invalid_login_count', 'user_account_id', 'last_login_date',
        'added_by', 'added_date', 'updated_by', 'updated_date',
        'is_deleted', 'archive_server', 'carrier_setup_agreement',
        'receive_email', 'tc_agreed_date', 'is_tc_agreed', 'address_2',
        'address_3', 'city', 'postcode', 'state',
        'commission_break_event_amount', 'is_sale_pot_eligible'
    ];

    foreach ($columns as $column) {
        if (Schema::hasColumn('users', $column)) {
            $table->dropColumn($column);
            echo "Dropped $column\n";
        }
    }
});

echo "Cleanup complete.\n";
