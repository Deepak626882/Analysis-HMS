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
        Schema::create('itemrate', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('Property_ID');
            $table->string('ItemCode', 9);
            $table->string('itemcodeold', 10)->default('');
            $table->string('RestCode', 20);
            $table->decimal('Rate', 10);
            $table->date('AppDate');
            $table->string('Party', 9);
            $table->string('U_Name', 15);
            $table->dateTime('U_EntDt');
            $table->dateTime('U_updatedt')->nullable();
            $table->string('U_AE', 1);

            $table->primary(['Property_ID', 'ItemCode', 'RestCode', 'AppDate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itemrate');
    }
};
