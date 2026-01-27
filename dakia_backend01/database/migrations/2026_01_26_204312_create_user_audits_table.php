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
        Schema::create('user_audits', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('table_name', 100)->nullable();
            $table->bigInteger('table_key')->nullable();
            $table->text('message')->nullable();
            $table->longText('old_data')->nullable();
            $table->longText('new_data')->nullable();
            $table->string('ip_address', 100)->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_audits');
    }
};
