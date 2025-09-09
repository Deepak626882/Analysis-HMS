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
        Schema::create('permission', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->integer('m1')->nullable()->default(0);
            $table->integer('m1a')->nullable()->default(0);
            $table->integer('m1b')->nullable()->default(0);
            $table->integer('m1c')->nullable()->default(0);
            $table->integer('m1d')->nullable()->default(0);
            $table->integer('m1a1')->nullable()->default(0);
            $table->integer('m1a2')->nullable()->default(0);
            $table->integer('m1a3')->nullable()->default(0);
            $table->integer('m2')->default(0);
            $table->integer('m3')->default(0);
            $table->integer('m4')->default(0);
            $table->integer('m5')->default(0);
            $table->integer('m6')->default(0);
            $table->integer('m7')->default(0);
            $table->integer('m8')->default(0);
            $table->integer('m9')->default(0);
            $table->integer('m10')->default(0);
            $table->string('u_name', 25);
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
        Schema::dropIfExists('permission');
    }
};
