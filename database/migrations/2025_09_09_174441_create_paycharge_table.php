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
        Schema::create('paycharge', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 30)->index('idx_paycharge_docid');
            $table->integer('sno');
            $table->integer('sno1');
            $table->string('vtype', 5)->nullable();
            $table->integer('vno')->nullable();
            $table->string('vprefix', 4)->nullable();
            $table->date('vdate')->nullable();
            $table->time('vtime')->nullable();
            $table->string('guestprof', 10)->nullable();
            $table->string('comp_code', 10)->nullable();
            $table->string('travel_agent', 100)->nullable();
            $table->longText('comments')->nullable();
            $table->string('paycode', 10)->index('idx_paycharge_paycode');
            $table->string('paytype', 15)->nullable();
            $table->decimal('amtcr', 10)->nullable()->default(0);
            $table->decimal('amtdr', 10)->nullable()->default(0);
            $table->integer('tipamt')->nullable();
            $table->string('roomcat', 10)->nullable();
            $table->string('roomtype', 10)->nullable();
            $table->string('roomno', 10)->nullable();
            $table->integer('foliono')->nullable();
            $table->integer('msno1')->default(0);
            $table->string('cardno', 10)->nullable();
            $table->string('cardholder', 15)->nullable();
            $table->string('chqno', 10)->nullable();
            $table->date('chqdate')->nullable();
            $table->date('expdate')->nullable();
            $table->string('bookno', 15)->nullable();
            $table->string('booktype', 15)->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->string('restcode', 10)->nullable();
            $table->decimal('billamount', 20)->nullable();
            $table->string('contraid', 15)->nullable();
            $table->string('dbtchkin', 10)->nullable();
            $table->decimal('taxper', 10)->nullable()->default(0);
            $table->decimal('onamt', 20)->nullable();
            $table->integer('split')->nullable()->default(1);
            $table->integer('billno')->nullable()->default(0)->index('billno');
            $table->string('modeset', 1)->nullable();
            $table->date('settledate')->nullable()->index('settledate');
            $table->string('batchno', 4)->nullable();
            $table->string('plancharge', 10)->nullable();
            $table->string('fixedchargecode', 10)->nullable();
            $table->string('relatdfoliono', 10)->nullable();
            $table->string('folionodocid', 50)->nullable()->index('idx_paycharge_folionodocid');
            $table->string('refno', 100)->nullable();
            $table->string('plancode', 10)->nullable();
            $table->string('seqno', 2)->nullable();
            $table->string('relatedfolionodocid', 50)->nullable();
            $table->string('refdocid', 50)->nullable();
            $table->longText('remarks')->nullable();
            $table->string('au_name', 15)->nullable();
            $table->dateTime('au_entdt')->nullable();
            $table->dateTime('au_updatedt')->nullable();
            $table->decimal('taxcondamt', 20)->nullable();
            $table->string('taxstru', 10)->nullable();
            $table->string('agac', 10)->nullable();
            $table->string('txnno', 10)->nullable();

            $table->index(['amtdr', 'amtcr'], 'idx_paycharge_amounts');
            $table->index(['propertyid', 'settledate', 'roomtype', 'foliono', 'folionodocid', 'billno'], 'idx_paycharge_combined');
            $table->index(['settledate', 'propertyid', 'roomtype', 'foliono', 'folionodocid'], 'idx_paycharge_main');
            $table->index(['propertyid', 'sno', 'refdocid'], 'idx_property_sno_refdoc');
            $table->primary(['propertyid', 'docid', 'sno', 'sno1']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paycharge');
    }
};
