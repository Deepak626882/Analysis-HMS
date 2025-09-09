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
        Schema::create('kot', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->string('propertyid', 30)->index('idx_kot_propertyid');
            $table->string('docid', 100);
            $table->integer('sno');
            $table->string('vtype', 5);
            $table->time('vtime');
            $table->integer('vno');
            $table->string('vprefix', 5);
            $table->date('vdate');
            $table->string('restcode', 7);
            $table->string('roomcat', 8);
            $table->string('roomtype', 5);
            $table->string('roomno', 20);
            $table->string('item', 9);
            $table->decimal('qty', 5);
            $table->decimal('rate', 10);
            $table->decimal('amount', 10);
            $table->string('voidyn', 1);
            $table->string('waiter', 7);
            $table->string('pending', 1);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->string('delflag', 1);
            $table->string('contradocid', 50);
            $table->integer('contrsno');
            $table->string('reasons', 35);
            $table->string('ncreason', 100)->default('');
            $table->string('remarks', 50);
            $table->string('nckot', 1);
            $table->string('nctype', 25);
            $table->integer('freesno');
            $table->string('printed', 1);
            $table->string('description', 50);
            $table->string('schemecode', 7);
            $table->string('itemrestcode', 7);
            $table->smallInteger('pax');
            $table->integer('tokenno');
            $table->string('printflag', 1);
            $table->string('mergedwith')->default('');

            $table->index(['vdate', 'restcode', 'voidyn', 'delflag'], 'idx_kot_date_rest');
            $table->primary(['propertyid', 'docid', 'sno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kot');
    }
};
