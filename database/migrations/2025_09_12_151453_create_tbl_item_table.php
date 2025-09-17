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
        Schema::create('tbl_item', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('property_id');
            $table->integer('code');
            $table->string('name', 30);
            $table->string('type', 15);
            $table->string('activeyn', 10);
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt');
            $table->string('u_ae', 1);
            $table->string('restcode', 25);
            $table->string('cattype', 15);

            $table->primary(['property_id', 'code', 'restcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_item');
    }
};
