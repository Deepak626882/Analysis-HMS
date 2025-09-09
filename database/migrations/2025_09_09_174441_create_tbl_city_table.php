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
        Schema::create('tbl_city', function (Blueprint $table) {
            $table->integer('propertyid');
            $table->integer('city_code', true);
            $table->integer('zipcode')->nullable();
            $table->string('country');
            $table->string('cityname')->nullable();
            $table->string('state', 10);
            $table->string('u_name');
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->default('a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_city');
    }
};
