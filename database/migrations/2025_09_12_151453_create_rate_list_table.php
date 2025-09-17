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
        Schema::create('rate_list', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('roomno', 100);
            $table->string('room_cat', 100);
            $table->string('occtype', 100);
            $table->decimal('rate1', 20)->nullable();
            $table->decimal('rate2', 20)->nullable();
            $table->decimal('rate3', 20)->nullable();
            $table->decimal('rate4', 20)->nullable();
            $table->decimal('rate5', 20)->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->string('sysYN', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_list');
    }
};
