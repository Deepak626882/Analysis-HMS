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
        Schema::create('venueocc', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('fpdocid');
            $table->string('venucode', 8);
            $table->integer('sno')->default(0);
            $table->date('fromdate');
            $table->time('dromtime');
            $table->date('todate');
            $table->string('totime', 20);
            $table->string('u_name');
            $table->dateTime('u_entdt');
            $table->string('u_ae', 1);
            $table->dateTime('u_updatedt')->nullable();

            $table->primary(['propertyid', 'fpdocid', 'venucode', 'fromdate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venueocc');
    }
};
