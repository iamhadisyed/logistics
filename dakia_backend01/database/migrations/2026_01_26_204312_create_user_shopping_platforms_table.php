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
        Schema::create('user_shopping_platforms', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('shopping_platform_id')->nullable();
            $table->string('reference', 45)->nullable();
            $table->string('site_url', 45)->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('status')->nullable()->default(false)->comment('0 - for delete
1 - for active
2 - for inactive
');
            $table->timestamp('date_created')->nullable()->useCurrent();
            $table->string('api_key')->nullable();
            $table->string('api_secrete')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_shopping_platforms');
    }
};
