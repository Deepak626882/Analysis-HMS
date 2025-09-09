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
        Schema::create('itemmast', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('Property_ID');
            $table->integer('Code');
            $table->string('codeold', 10)->default('');
            $table->string('Name', 40);
            $table->string('itemcodeold', 10)->default('');
            $table->string('Unit', 6);
            $table->string('Type', 20);
            $table->integer('dishtype')->nullable();
            $table->integer('favourite')->nullable()->default(0);
            $table->integer('ItemGroup');
            $table->string('RestCode', 20);
            $table->decimal('PurchRate', 10);
            $table->decimal('MinStock', 10, 3);
            $table->decimal('MaxStock', 10, 3);
            $table->decimal('ReStock', 10, 3);
            $table->string('DiscApp', 1);
            $table->string('RateEdit', 1);
            $table->float('LPurRate', null, 0)->nullable();
            $table->date('LPurDate')->nullable();
            $table->string('U_Name', 15);
            $table->dateTime('U_EntDt');
            $table->dateTime('u_updaedt')->nullable();
            $table->string('U_AE', 1);
            $table->string('ItemCatCode', 9);
            $table->smallInteger('DispCode');
            $table->string('Kitchen', 9);
            $table->decimal('ConvRatio', 10, 3);
            $table->string('IssueUnit', 60);
            $table->string('SChrgApp', 1);
            $table->string('Specification', 35);
            $table->string('RateIncTax', 1);
            $table->string('ActiveYN', 3);
            $table->string('NType', 20);
            $table->string('ItemType', 9);
            $table->string('BarCode', 25);
            $table->string('HSNCode', 7);
            $table->string('LabelName', 50);
            $table->string('LabelQty', 50);
            $table->string('LabelRemark1', 50);
            $table->string('LabelRemark2', 50);
            $table->string('LabelRemark3', 50);
            $table->string('LabelRemark4', 50);
            $table->string('iempic', 100);

            $table->index(['Code', 'RestCode'], 'idx_itemmast_code_rest');
            $table->primary(['Property_ID', 'Code', 'RestCode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itemmast');
    }
};
