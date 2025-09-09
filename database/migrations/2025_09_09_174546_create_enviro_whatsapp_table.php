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
        Schema::create('enviro_whatsapp', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->enum('checkyn', ['Y', 'N'])->default('N');
            $table->integer('propertyid')->primary();
            $table->string('managementmob')->default('');
            $table->string('whatsappcenterusername', 50)->default('');
            $table->string('whatsappcenterpassword', 20)->default('');
            $table->string('whatsappdisplayname', 50)->default('');
            $table->string('whatsappurl', 100)->default('');
            $table->string('bearercode')->default('');
            $table->integer('whatsapppurchase')->default(0);
            $table->integer('whatsappsend')->default(0);
            $table->integer('whatsappbal')->default(0);
            $table->string('whatsappuser', 20)->default('');
            $table->string('whatspassword', 20)->default('');
            $table->string('whatsapptfromsend', 20)->default('');
            $table->string('whatsmessage', 20)->default('');
            $table->string('whatsappextra', 20)->default('');
            $table->text('checkinmsg');
            $table->text('checkinmsgarray')->default('');
            $table->string('checkintemplate')->default('');
            $table->text('checkinmsgadmin');
            $table->text('checkinmsgadminarray')->default('');
            $table->string('checkinmsgadmintemplate')->default('');
            $table->text('checkoutmsgadmin');
            $table->text('checkoutmsgadminarray')->default('');
            $table->string('checkoutmsgadmintemplate')->default('');
            $table->text('checkoutmsg')->default('');
            $table->text('checkoutmsgarray')->default('');
            $table->string('checkouttemplate')->default('');
            $table->string('pphonenoprefix', 15)->default('');
            $table->string('tphNo', 20)->default('');
            $table->text('reservation');
            $table->text('reservationarray')->default('');
            $table->string('reservationtemplate')->default('');
            $table->string('adminreservation')->default('');
            $table->text('adminreservationarray')->default('');
            $table->string('adminreservationtemplate')->default('');
            $table->string('adminreservationcancel')->default('');
            $table->text('adminreservationcancelarray')->default('');
            $table->string('adminreservationcanceltemplate')->default('');
            $table->string('reservationcancel', 200)->default('');
            $table->text('reservationcancelarray')->default('');
            $table->string('reservationcanceltemplate')->default('');
            $table->text('kotmsgadmin')->default('');
            $table->text('kotmsgadminarray')->default('');
            $table->string('kotmsgadmintemplate')->default('');
            $table->text('billmsgguest')->default('');
            $table->text('billmsgguestarray')->default('');
            $table->string('billmsgguesttemplate')->default('');
            $table->text('billmsgadmin')->default('');
            $table->text('billmsgadminarray')->default('');
            $table->string('billmsgadmintemplate')->default('');
            $table->text('assigndelmsg')->default('');
            $table->text('assigndelmsgarray')->default('');
            $table->string('assigndelmsgtemplate')->default('');
            $table->string('banqbooking', 200)->default('');
            $table->string('banqbookingcancel', 200)->default('');
            $table->string('posbill', 200)->default('');
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->default('a');
            $table->string('u_name')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enviro_whatsapp');
    }
};
