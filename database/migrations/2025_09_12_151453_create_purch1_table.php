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
        Schema::create('purch1', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 100);
            $table->integer('vno');
            $table->date('vdate');
            $table->string('vtype', 6);
            $table->string('vprefix', 5);
            $table->string('restcode', 15);
            $table->string('Party', 7);
            $table->decimal('total', 10);
            $table->decimal('discper', 10);
            $table->decimal('discamt', 10);
            $table->decimal('nontaxable', 10);
            $table->decimal('taxable', 10);
            $table->decimal('tax', 10);
            $table->decimal('servicecharge', 10);
            $table->decimal('addamt', 10);
            $table->decimal('dedamt', 10);
            $table->decimal('roundoff', 10);
            $table->decimal('netamt', 10);
            $table->string('u_name', 20);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->string('delflag', 1);
            $table->string('partybillno', 25);
            $table->date('partybilldt');
            $table->string('cashparty', 45);
            $table->string('gstno', 12);
            $table->string('remark', 45);
            $table->string('invoicetype', 15);
            $table->integer('invoiceno');
            $table->decimal('cgst', 10);
            $table->decimal('sgst', 10);
            $table->decimal('igst', 10);
            $table->decimal('payable', 10);
            $table->string('billimagepath');

            $table->index(['vdate', 'propertyid', 'vtype'], 'idx_purch1');
            $table->primary(['propertyid', 'docid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purch1');
    }
};
