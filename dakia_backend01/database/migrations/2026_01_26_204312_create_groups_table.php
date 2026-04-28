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
        Schema::create('groups', function (Blueprint $table) {
            $table->integer('group_id');
            $table->string('group_name', 150)->nullable();
            $table->string('group_slug')->nullable();
            $table->string('group_desc')->nullable();
            $table->enum('group_type', ['admin', 'corporate', 'client'])->nullable();
            $table->boolean('is_active')->nullable()->default(false);
            $table->integer('added_by')->nullable();
            $table->dateTime('added_date')->nullable();
            $table->boolean('is_deleted')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
