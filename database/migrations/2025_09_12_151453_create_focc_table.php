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
        Schema::create('focc', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->string('propertyid', 100)->default('');
            $table->date('vdate')->nullable();
            $table->decimal('interestamount', 10);
            $table->dateTime('u_entdt')->nullable()->useCurrent();
            $table->dateTime('u_updatedt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('focc');
    }
};
