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
        Schema::create('enviro_form', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid')->index('idx_enviro_form_propertyid');
            $table->string('arrdatetimeedit', 3)->nullable();
            $table->string('cancellationac', 10)->nullable();
            $table->string('pageopenwalkin')->default('roomstatus');
            $table->string('advanceroomrentac', 10)->nullable();
            $table->string('grcmandatory', 3)->nullable();
            $table->string('roomrateeditable', 3)->nullable();
            $table->string('roominctaxeditable', 3)->nullable();
            $table->string('rrinctaxdefault', 10)->nullable();
            $table->string('blockinvalidtarrifinctaxyn', 3)->nullable();
            $table->integer('fombillcopies')->nullable();
            $table->time('checkout')->nullable();
            $table->time('checkintime')->default('12:00:00');
            $table->string('plancalc', 3)->nullable();
            $table->string('autofillroomres', 1)->default('N');
            $table->string('emptyroomyn', 1)->nullable()->default('N');
            $table->string('noshowatnightaudit', 3)->nullable();
            $table->string('billprintingsummerised', 3)->nullable();
            $table->string('taxsummary', 3)->nullable();
            $table->time('variationbefore')->nullable();
            $table->time('variationafter')->nullable();
            $table->string('roomrentcharge', 1)->nullable();
            $table->string('roomrentbefchkout', 3)->nullable();
            $table->string('roomrentchkoutpost', 3)->nullable();
            $table->string('autosplityn', 3)->nullable();
            $table->string('roomcheckoutclearanceyn', 3)->nullable();
            $table->string('roomchrgdueac', 10)->nullable();
            $table->string('postroomdiscseparately', 3)->nullable();
            $table->string('plantariffnarration', 20)->nullable();
            $table->string('guestchargesdeletelog', 3)->nullable();
            $table->string('rate1', 15)->nullable()->default('High Rate');
            $table->string('rate2', 15)->nullable()->default('Rack Rate');
            $table->string('rate3', 15)->nullable()->default('Disk 1 Rate');
            $table->string('rate4', 15)->nullable()->default('Disk 2 Rate');
            $table->string('rate5', 15)->nullable()->default('Disk 3 Rate');
            $table->text('resinstruction1')->nullable();
            $table->string('resinstruction2', 100)->nullable();
            $table->string('resinstruction3', 100)->nullable();
            $table->string('resinstruction4', 100)->nullable();
            $table->string('resinstruction5', 100)->nullable();
            $table->string('resinstruction6', 100)->nullable();
            $table->string('resinstruction7', 100)->nullable();
            $table->string('resinstruction8', 100)->nullable();
            $table->string('resinstruction9', 100)->nullable();
            $table->string('resinstruction10', 100)->nullable();
            $table->string('resinstruction11', 100)->nullable();
            $table->string('resinstruction12', 100)->nullable();
            $table->string('roomcheckinclearanceyn', 3)->nullable();
            $table->string('logoyn', 1)->default('Y');
            $table->string('emailyn', 1)->default('Y');
            $table->string('websiteyn', 1)->default('Y');
            $table->string('seperatereservationletterasperstatusyn', 3)->nullable();
            $table->string('newtarrifforoldguest', 3)->nullable();
            $table->string('reservationexpondonsaveyn', 3)->nullable();
            $table->string('increservationinblankgrc', 3)->nullable();
            $table->string('displayrackrow', 10)->nullable();
            $table->string('displayrackcol', 10)->nullable();
            $table->string('displayrackfontsize', 10)->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->string('sysYN', 1)->nullable();
            $table->string('roundofftype', 10)->nullable();

            $table->primary(['propertyid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enviro_form');
    }
};
