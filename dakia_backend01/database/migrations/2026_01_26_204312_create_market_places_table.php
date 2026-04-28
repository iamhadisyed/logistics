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
        Schema::create('market_places', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('title');
            $table->string('description');
            $table->string('translation_key');
            $table->boolean('is_active')->default(false);
            $table->string('page_link');
            $table->bigInteger('added_by');
            $table->dateTime('added_date');
            $table->bigInteger('updated_by')->nullable();
            $table->dateTime('updated_date')->nullable();
            $table->boolean('is_delete')->default(false);
            $table->string('integration_logo', 45)->nullable();
            $table->string('manual_link', 45)->nullable();
            $table->string('plugin_key')->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->integer('integration_type')->nullable()->default(0);
            $table->integer('channel_type')->nullable()->default(0);
            $table->integer('country_id')->nullable()->default(0);
            $table->dateTime('last_sync')->nullable();
            $table->boolean('is_featured')->nullable()->default(false);
            $table->boolean('is_api2cart')->nullable()->default(false);
            $table->string('help_doc', 100)->nullable();
            $table->string('class_name')->nullable();
            $table->string('documentation_title')->nullable();
            $table->string('documentation_cover_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_places');
    }
};
