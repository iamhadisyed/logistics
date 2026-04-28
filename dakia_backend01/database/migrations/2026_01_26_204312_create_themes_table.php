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
        Schema::create('themes', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('name', 200);
            $table->string('slug', 200);
            $table->string('style_sheet', 100)->nullable();
            $table->string('dashboard_template', 80)->nullable();
            $table->boolean('is_active')->default(false);
            $table->bigInteger('created_by');
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
