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
        Schema::create('emailtemplates', function (Blueprint $table) {
            $table->integer('emailtemplateid');
            $table->string('title')->nullable();
            $table->string('shortkey', 50)->nullable();
            $table->text('content')->nullable();
            $table->boolean('isactive')->nullable();
            $table->timestamp('createdon')->useCurrentOnUpdate()->useCurrent();
            $table->boolean('isdeleted')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('pagetitle', 45)->nullable();
            $table->string('metatitle', 45)->nullable();
            $table->string('metadescription', 45)->nullable();
            $table->string('metakeywords', 45)->nullable();
            $table->boolean('sorder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emailtemplates');
    }
};
