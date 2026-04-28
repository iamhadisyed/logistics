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
        Schema::create('remotearea_charges_carriers', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('remotearea_group_id')->nullable();
            $table->decimal('remotearea_charges', 10)->nullable();
            $table->enum('is_deleted', ['Y', 'N'])->nullable()->default('N');
            $table->integer('added_by')->nullable();
            $table->dateTime('added_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remotearea_charges_carriers');
    }
};
