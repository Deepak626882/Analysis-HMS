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
        Schema::create('hallsale1', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docId', 25);
            $table->string('vtype', 3);
            $table->string('vprefix', 20)->default('');
            $table->integer('vno');
            $table->date('vdate');
            $table->string('restcode', 7);
            $table->string('party', 40);
            $table->string('comp_code')->default('');
            $table->decimal('total', 10);
            $table->decimal('discper', 10);
            $table->decimal('discamt', 10);
            $table->decimal('nontaxable', 10);
            $table->decimal('taxable', 10);
            $table->decimal('roundoff', 10)->default(0);
            $table->decimal('netamt', 10);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->string('remark')->default('');
            $table->float('noofpax', null, 0);
            $table->decimal('rateperpax', 10);
            $table->decimal('totalpercover', 10);
            $table->decimal('advance', 10);
            $table->integer('rectno');
            $table->dateTime('rectdate')->nullable();
            $table->string('bookdocid', 25);
            $table->string('narration', 25);
            $table->string('narration1', 25);
            $table->decimal('cgst', 10);
            $table->decimal('sgst', 10);

            $table->primary(['propertyid', 'docId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallsale1');
    }
};
