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
        Schema::create('itemcatmast', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('Code', 10);
            $table->string('Name', 25);
            $table->string('RoundOff', 3);
            $table->string('AcCode', 10);
            $table->string('U_Name', 25);
            $table->dateTime('U_EntDt');
            $table->dateTime('U_updatedt')->nullable();
            $table->string('U_AE', 1)->default('a');
            $table->string('OutletYN', 1);
            $table->string('Flag', 10);
            $table->string('TaxStru', 10);
            $table->string('CatType', 15);
            $table->string('cattyper', 50)->default('');
            $table->string('RestCode', 20);
            $table->string('DrCr', 2);
            $table->string('RevCode', 10);
            $table->string('ActiveYN', 1);

            $table->primary(['propertyid', 'Code', 'RestCode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itemcatmast');
    }
};
