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
        Schema::create('printdelay', function (Blueprint $table) {
            $table->integer('sn', true);
            $table->integer('propertyid');
            $table->string('docid', 50);
            $table->string('duplicate', 1)->default('N');
            $table->string('restaurentname', 100);
            $table->string('restcode', 50);
            $table->string('kitchen', 50);
            $table->string('printerpath', 100);
            $table->integer('itemsn');
            $table->integer('psno')->default(0);
            $table->string('itemname', 100);
            $table->decimal('itemprice', 10);
            $table->decimal('quantity', 10);
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printdelay');
    }
};
