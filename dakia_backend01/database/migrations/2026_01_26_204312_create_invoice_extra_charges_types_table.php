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
        Schema::create('invoice_extra_charges_types', function (Blueprint $table) {
            $table->integer('id');
            $table->string('title')->nullable();
            $table->boolean('isactive')->default(false);
            $table->integer('added_by');
            $table->timestamp('added_date')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_extra_charges_types');
    }
};
