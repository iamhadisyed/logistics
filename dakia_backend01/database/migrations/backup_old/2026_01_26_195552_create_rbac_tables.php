<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Roles Table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Permissions Table (Legacy Exists)
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('lang_key')->unique()->nullable();
                $table->integer('parent_id')->default(0);
                $table->string('file_name')->nullable();
                $table->string('description')->nullable();
                $table->string('query_string')->nullable();
                $table->string('icon')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_menu_item')->default(false);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_deleted')->default(false);
            });
        }

        // 3. Groups / Departments Table (Legacy Support)
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('group_id');
                $table->string('group_name')->nullable();
                $table->string('group_slug')->nullable();
                $table->text('group_desc')->nullable();
                $table->string('group_type')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('added_by')->nullable();
                $table->dateTime('added_date')->nullable();
                $table->boolean('is_deleted')->default(false);
            });
        }

        // 4. Role User Pivot
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->primary(['role_id', 'user_id']);
            });
        }

        // 5. Permission Role Pivot
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                // permissions.id is int(11) in legacy, so use integer
                $table->integer('permission_id'); 
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                
                $table->index(['permission_id', 'role_id']);
                // Cannot easily add foreign key constraint to legacy permissions if types mismatch or MyISAM
                // But legacy permissions is InnoDB. Let's try to trust just the index for now to avoid FK errors.
            });
        }

        // 6. User Departments Pivot (Legacy User Groups)
        // Renamed from user_department -> user_departments in previous migration
        if (!Schema::hasTable('user_departments')) {
            Schema::create('user_departments', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->unsignedBigInteger('user_id');
                $table->unsignedInteger('department_id');

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                // groups.group_id is int(11). department_id is int(11).
                // $table->foreign('department_id')->references('group_id')->on('groups')->onDelete('cascade');
                
                $table->index(['user_id', 'department_id']);
            });
        }

        // 7. Group Has Permissions Pivot (Legacy)
        if (!Schema::hasTable('grouphaspermissions')) {
            Schema::create('grouphaspermissions', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->unsignedInteger('group_id');
                $table->unsignedBigInteger('perm_id');

                // $table->foreign('group_id')->references('group_id')->on('groups')->onDelete('cascade');
                // $table->foreign('perm_id')->references('id')->on('permissions')->onDelete('cascade');

                $table->index(['group_id', 'perm_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grouphaspermissions');
        Schema::dropIfExists('user_departments');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
