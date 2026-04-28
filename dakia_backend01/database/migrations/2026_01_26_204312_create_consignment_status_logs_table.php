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
        Schema::create('consignment_status_logs', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('parcel_id');
            $table->string('old_status', 100);
            $table->string('new_status', 100);
            $table->text('message')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->timestamp('date_added')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_status_logs');
    }
};
