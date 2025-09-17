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
        Schema::create('enviro_inventory', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid')->primary();
            $table->string('cashpurchaseac', 8);
            $table->string('purchasegodown', 8)->default('');
            $table->string('modifyaccountfield', 1)->default('');
            $table->integer('blockdays')->default(0);
            $table->string('itemratemrbasedon', 100);
            $table->string('itemratepbillbasedon', 100);
            $table->enum('storeissuerequistion', ['Y', 'N'])->default('Y');
            $table->string('roundofftype')->default('Standard');
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
        Schema::dropIfExists('enviro_inventory');
    }
};
