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
        Schema::create('paychargeh', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid')->index('idx_paychargeh_main');
            $table->string('docid', 30)->index('idx_paychargeh_docid');
            $table->integer('sno');
            $table->string('vtype', 5)->nullable();
            $table->integer('vno')->nullable();
            $table->string('vprefix', 4)->nullable();
            $table->integer('fpno');
            $table->date('vdate')->nullable();
            $table->time('vtime')->nullable();
            $table->string('comp_code', 10)->nullable()->default('');
            $table->longText('comments')->nullable();
            $table->string('paycode', 10)->index('idx_paychargeh_paycode');
            $table->string('paytype', 15)->nullable();
            $table->decimal('amtcr', 10)->nullable()->default(0);
            $table->decimal('amtdr', 10)->nullable()->default(0);
            $table->string('roomcat', 10)->nullable()->default('');
            $table->string('roomno', 10)->nullable()->default('');
            $table->string('cardno', 10)->nullable()->default('');
            $table->string('cardholder', 15)->nullable()->default('');
            $table->string('chqno', 10)->nullable()->default('');
            $table->date('chqdate')->nullable();
            $table->date('expdate')->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->string('restcode', 10)->nullable();
            $table->decimal('billamount', 20)->nullable()->default(0);
            $table->string('contradocid', 35)->nullable();
            $table->string('postdocId', 21);
            $table->integer('taxper')->nullable();
            $table->decimal('onamt', 20)->nullable()->default(0);
            $table->integer('split')->nullable()->default(1);
            $table->integer('billno')->nullable()->default(0)->index('billno');
            $table->string('batchno', 4)->nullable()->default('');
            $table->string('au_name', 15)->nullable()->default('');
            $table->dateTime('au_entdt')->nullable();
            $table->dateTime('au_updatedt')->nullable();
            $table->string('taxstru', 10)->nullable()->default('');

            $table->index(['amtdr', 'amtcr'], 'idx_paychargeh_amounts');
            $table->index(['propertyid', 'billno'], 'idx_paychargeh_combined');
            $table->index(['propertyid', 'sno'], 'idx_property_sno_refdoc');
            $table->primary(['propertyid', 'docid', 'sno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paychargeh');
    }
};
