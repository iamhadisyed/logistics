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
        Schema::create('currencies', function (Blueprint $table) {
            $table->integer('id');
            $table->string('currencyname', 50);
            $table->string('leftsymbol', 10);
            $table->string('rightsymbol', 10);
            $table->boolean('isdefault');
            $table->decimal('currencyexchangerate', 20, 6);
            $table->boolean('isactive')->default(false);
            $table->boolean('clientdisplay')->default(false);
            $table->string('currencyid', 45)->nullable()->default('null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
