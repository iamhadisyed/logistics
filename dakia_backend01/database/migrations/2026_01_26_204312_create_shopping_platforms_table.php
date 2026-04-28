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
        Schema::create('shopping_platforms', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('title')->nullable();
            $table->string('page_key', 30)->nullable();
            $table->longText('description')->nullable();
            $table->string('translation_key')->nullable();
            $table->string('plugin_key')->nullable();
            $table->string('integration_logo', 45)->nullable();
            $table->tinyInteger('display_option')->nullable()->default(0)->comment('0 - manual / connect
1 - manual
2 - connect
');
            $table->string('connect_url', 45)->nullable();
            $table->tinyInteger('active')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_platforms');
    }
};
