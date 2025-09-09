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
        Schema::create('ftemp', function (Blueprint $table) {
            $table->integer('propertyid');
            $table->string('guestname', 35);
            $table->integer('foliono');
            $table->string('folionodocid', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftemp');
    }
};
