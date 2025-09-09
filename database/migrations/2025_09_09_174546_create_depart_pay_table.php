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
        Schema::create('depart_pay', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->string('rest_code', 10);
            $table->string('pay_code', 10);
            $table->string('is_checked', 1)->nullable();
            $table->string('u_name', 20);
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
        Schema::dropIfExists('depart_pay');
    }
};
