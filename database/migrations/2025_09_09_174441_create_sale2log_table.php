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
        Schema::create('sale2log', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 50);
            $table->integer('sno')->default(0);
            $table->integer('sno1')->default(0);
            $table->string('vtype', 6);
            $table->integer('vno')->default(0);
            $table->string('vprefix', 5);
            $table->time('vtime');
            $table->date('vdate');
            $table->string('restcode', 7);
            $table->string('taxcode', 8);
            $table->decimal('basevalue', 10)->default(0);
            $table->float('taxper', null, 0)->default(0);
            $table->decimal('taxamt', 10)->default(0);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->string('delflag', 1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale2log');
    }
};
