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
        Schema::create('userpermission', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('username', 100);
            $table->decimal('posdiscountallowupto', 10)->default(0);
            $table->string('possettlementyn', 1)->default('Y');
            $table->string('editelementinkot', 1)->default('Y');
            $table->string('freeitemallow', 1)->default('N');
            $table->string('refundcashcardamt', 1)->default('Y');
            $table->decimal('fomdiscuntallowupto', 10)->default(0);
            $table->string('cancelguestbill', 1)->default('Y');
            $table->string('changeroomdetail', 1)->default('Y');
            $table->string('deleteguestcharges', 1)->default('Y');
            $table->string('changeguestcharges', 1)->default('Y');
            $table->string('changeguestprofile', 1)->default('Y');
            $table->string('cancelreservation', 1)->default('Y');
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userpermission');
    }
};
