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
        Schema::create('permissions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('lang_key', 100)->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('file_name', 150)->nullable();
            $table->string('description', 250)->nullable();
            $table->integer('added_by')->nullable();
            $table->dateTime('added_date')->nullable();
            $table->string('query_string', 100)->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('sort_order')->nullable();
            $table->boolean('is_menu_item')->default(true);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
