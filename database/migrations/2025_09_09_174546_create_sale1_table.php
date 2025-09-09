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
        Schema::create('sale1', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 50);
            $table->string('vtype', 6);
            $table->integer('vno')->default(0);
            $table->time('vtime');
            $table->string('vprefix', 5);
            $table->date('vdate');
            $table->string('restcode', 7);
            $table->string('roomcat', 7);
            $table->string('roomtype', 2);
            $table->string('roomno', 4);
            $table->integer('foliono')->default(0);
            $table->integer('sno1')->default(0);
            $table->string('party', 20);
            $table->decimal('total', 10)->default(0);
            $table->decimal('discper', 6)->default(0);
            $table->decimal('discamt', 10)->default(0);
            $table->decimal('nontaxable', 10)->default(0);
            $table->decimal('taxable', 10)->default(0);
            $table->decimal('servicecharge', 10)->default(0);
            $table->decimal('addamt', 10)->default(0);
            $table->decimal('dedamt', 10)->default(0);
            $table->decimal('roundoff', 10)->default(0);
            $table->decimal('netamt', 10)->default(0);
            $table->string('remark', 75);
            $table->string('waiter', 7);
            $table->string('kotno', 400);
            $table->integer('tokenno')->default(0);
            $table->integer('guaratt')->default(0);
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->string('delflag', 1)->default('N');
            $table->string('delremark')->nullable();
            $table->string('printed', 1);
            $table->string('deliveredyn', 1);
            $table->string('custname', 35);
            $table->string('phoneno', 10);
            $table->string('add', 50);
            $table->string('city', 35);
            $table->decimal('cashrecd', 10)->default(0);
            $table->string('folionodocid', 50);
            $table->string('au_name', 15);
            $table->dateTime('au_entdt')->nullable();
            $table->string('discremark', 35);
            $table->decimal('cgst', 10)->default(0);
            $table->decimal('sgst', 10)->default(0);
            $table->decimal('vat', 10)->default(0);
            $table->decimal('igst', 10)->default(0);
            $table->string('mergedwith', 50);

            $table->index(['vdate', 'restcode', 'delflag'], 'idx_sale1_date_rest');
            $table->index(['restcode', 'vdate', 'delflag', 'propertyid', 'vtype'], 'idx_sale1_optimization');
            $table->primary(['propertyid', 'docid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale1');
    }
};
