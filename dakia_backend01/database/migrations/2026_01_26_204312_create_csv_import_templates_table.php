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
        Schema::create('csv_import_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->bigInteger('user_id');
            $table->bigInteger('user_account_id');
            $table->string('template_name');
            $table->text('template');
            $table->bigInteger('added_by');
            $table->dateTime('added_date');
            $table->bigInteger('update_by')->nullable();
            $table->dateTime('update_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_import_templates');
    }
};
