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
        Schema::create('bookingfollowup', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->integer('inqno');
            $table->integer('sno');
            $table->date('date');
            $table->time('time');
            $table->dateTime('nextfollowupdate');
            $table->string('remark', 50);
            $table->string('status', 9);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->string('u_ae', 1);

            $table->primary(['propertyid', 'inqno', 'sno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookingfollowup');
    }
};
