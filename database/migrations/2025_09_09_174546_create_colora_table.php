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
        Schema::create('colora', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->string('rcode', 100)->nullable();
            $table->string('colorcode', 100)->nullable();
            $table->integer('cstatus')->nullable()->default(0);
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->string('sysYN', 1)->nullable();
            $table->string('activeYN', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colora');
    }
};
