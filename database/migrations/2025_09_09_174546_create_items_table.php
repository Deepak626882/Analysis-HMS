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
        Schema::create('items', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->integer('icode');
            $table->string('name', 35);
            $table->string('barcode', 25)->nullable();
            $table->string('hsncode', 7)->nullable();
            $table->string('itempic', 100)->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');

            $table->primary(['propertyid', 'icode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
