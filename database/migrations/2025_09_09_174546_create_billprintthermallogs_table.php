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
        Schema::create('billprintthermallogs', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->string('docid')->default('');
            $table->json('billdata');
            $table->dateTime('u_entdt')->nullable()->useCurrent();
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_name')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billprintthermallogs');
    }
};
