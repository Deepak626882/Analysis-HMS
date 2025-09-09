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
        Schema::create('bookingplandetails', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->integer('foliono');
            $table->string('docid', 30);
            $table->integer('sno');
            $table->integer('sno1');
            $table->string('roomno', 6);
            $table->string('rev_code', 8)->index('idx_rev_code');
            $table->string('taxinc', 3);
            $table->string('taxstru', 8);
            $table->string('fixrate', 1);
            $table->integer('noofdays')->default(0);
            $table->decimal('planper', 10);
            $table->decimal('amount', 10);
            $table->decimal('room_rate_before_tax', 10);
            $table->decimal('total_rate', 10);
            $table->string('pcode', 8);
            $table->decimal('netplanamt', 10);
            $table->string('u_name', 8);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);

            $table->index(['docid', 'sno1'], 'idx_booking_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookingplandetails');
    }
};
