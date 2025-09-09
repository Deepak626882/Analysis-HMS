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
        Schema::create('enviro_pos', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid')->default(0)->primary();
            $table->string('cashpaytype', 10)->default('');
            $table->string('sundrypassword', 12)->default('');
            $table->string('kotprinting', 26)->default('');
            $table->decimal('nckot', 4)->default(0);
            $table->string('posbillatnightaudit', 1)->default('Y');
            $table->string('kotheader1', 50)->default('');
            $table->string('kotheader2', 50)->default('');
            $table->string('kotheader3', 50)->default('');
            $table->string('kotheader4', 50)->default('');
            $table->string('possalebillauditlog', 1)->default('Y');
            $table->string('creditcardinfomandatory', 1)->default('N');
            $table->string('modifyentryinbackdate', 1)->default('N');
            $table->string('kotatnightaudit', 1)->default('');
            $table->string('reportingonsalebill', 1)->default('N');
            $table->string('postposdiscseperately', 1)->default('Y');
            $table->string('kotoutletselection', 50)->default('Seperate For All Kitchen');
            $table->string('printkot', 100)->default('Seperate For All Kitchen');
            $table->string('printeditkot', 11)->default('All Items');
            $table->string('roundofftype', 9)->default('Upper');
            $table->string('bookingpartyac', 9)->default('');
            $table->string('slipfooter1', 50)->default('');
            $table->string('slipfooter2', 50)->default('');
            $table->string('u_name');
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae')->default('a');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enviro_pos');
    }
};
