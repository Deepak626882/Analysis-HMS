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
        Schema::create('bookingdetail', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->integer('inqno');
            $table->integer('sno')->default(0);
            $table->string('venuecode', 9);
            $table->date('fromdate');
            $table->date('todate');
            $table->time('fromtime');
            $table->time('totime');
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'inqno', 'venuecode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookingdetail');
    }
};
