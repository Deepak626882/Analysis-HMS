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
        Schema::create('tbl_country', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->string('name', 100)->unique('name');
            $table->string('country_code', 110)->unique('country_code');
            $table->string('nationality', 100);
            $table->string('u_name', 100);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_country');
    }
};
