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
        Schema::create('service_country_ttimes', function (Blueprint $table) {
            $table->comment('This table is to get service with respect to country Transit Time. For Example WPX service is taking 3 days to India but 7 Days to Afghanistan.');
            $table->integer('id')->comment('Primary Key of the table');
            $table->integer('id_country')->nullable()->comment('This field is used as a foreign key from country Table in order to find the transit time of which country with respect to what service ');
            $table->integer('id_service')->nullable()->comment('This field is used as a foreign key from Service Table in order to find the transit time of which service with respect to what country ');
            $table->integer('transit_time')->nullable()->comment('It will tell you the service time in days to the country it is going to.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_country_ttimes');
    }
};
