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
        Schema::create('remotearea_user_mappings', function (Blueprint $table) {
            $table->integer('id');
            $table->string('user_account')->nullable();
            $table->string('postcode_name')->nullable()->comment('-');
            $table->string('service_code', 45)->nullable();
            $table->string('charges', 45)->nullable();
            $table->dateTime('remotearea_added_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remotearea_user_mappings');
    }
};
