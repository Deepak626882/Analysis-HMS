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
        Schema::create('voucher_prefix', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->string('short_name', 100)->nullable();
            $table->integer('propertyid');
            $table->string('v_type');
            $table->date('date_from');
            $table->date('date_to');
            $table->integer('prefix');
            $table->integer('start_srl_no')->nullable();
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
        Schema::dropIfExists('voucher_prefix');
    }
};
