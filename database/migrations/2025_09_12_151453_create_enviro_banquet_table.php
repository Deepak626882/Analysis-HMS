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
        Schema::create('enviro_banquet', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid')->primary();
            $table->string('outdoorcatering', 3);
            $table->string('cataloglimit', 3);
            $table->string('roundoffac', 8);
            $table->string('discountac', 8);
            $table->string('indoorsaleac', 8);
            $table->string('indoorpartyac', 8);
            $table->string('panrequiredyn', 1)->default('N');
            $table->string('roundofftype')->default('Standard');
            $table->string('u_name', 15);
            $table->string('u_ae', 1);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enviro_banquet');
    }
};
