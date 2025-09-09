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
        Schema::create('a', function (Blueprint $table) {
            $table->integer('sn')->default(0);
            $table->integer('Property_ID');
            $table->string('BookingDocid', 35);
            $table->string('BookNo', 10);
            $table->string('GuestName', 50)->nullable();
            $table->string('GuestProf', 10)->nullable();
            $table->integer('Sno');
            $table->float('RoomDet', null, 0);
            $table->float('Adults', null, 0);
            $table->float('Childs', null, 0);
            $table->decimal('Tarrif', 10);
            $table->string('IncTax', 3);
            $table->dateTime('U_EntDt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('U_AE', 1);
            $table->string('RoomCat', 9);
            $table->string('ccode');
            $table->string('pcode')->default('');
            $table->string('Plan_Code', 20);
            $table->string('ServiceChrg', 3);
            $table->string('RoomNo', 9);
            $table->string('RateCode', 1);
            $table->date('ArrDate');
            $table->time('ArrTime');
            $table->smallInteger('NoDays');
            $table->date('DepDate');
            $table->string('Cancel', 1);
            $table->date('CancelDate')->nullable();
            $table->string('U_Name', 15);
            $table->time('DepTime');
            $table->integer('status')->default(1);
            $table->string('RoomTaxStru', 9);
            $table->string('CancelUName', 15);
            $table->string('ContraDocId', 25)->nullable();
            $table->integer('ContraSno')->nullable();
            $table->string('chkoutyn', 1)->default('N');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a');
    }
};
