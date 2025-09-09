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
        Schema::create('guestreward', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 30);
            $table->string('custcode', 8);
            $table->date('vdate');
            $table->time('vtime');
            $table->string('departname', 25);
            $table->string('billno', 8);
            $table->float('billamt', null, 0);
            $table->float('rewardpoint', null, 0)->default(0);
            $table->float('redeempoint', null, 0)->default(0);
            $table->string('mobileno', 11);
            $table->float('total', null, 0)->default(0);
            $table->float('discamt', null, 0)->default(0);
            $table->string('restcode', 6);
            $table->string('schemecode', 6);
            $table->float('saleupto', null, 0)->default(0);
            $table->float('rppointonamt', null, 0)->default(0);
            $table->float('rewardvalue', null, 0)->default(0);
            $table->float('reedemvalue', null, 0)->default(0);
            $table->string('regid', 11);
            $table->float('discper', null, 0)->default(0);
            $table->string('u_name', 10);
            $table->string('u_ae', 1);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();

            $table->primary(['propertyid', 'docid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guestreward');
    }
};
