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
        Schema::create('stock', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 30)->index('idx_stock_docid');
            $table->integer('sno');
            $table->string('vtype', 5);
            $table->integer('vno')->default(0);
            $table->string('vprefix', 4);
            $table->date('vdate');
            $table->string('partycode', 8)->default('');
            $table->string('restcode', 7)->default('');
            $table->string('roomcat', 7)->default('');
            $table->string('roomtype', 2)->default('');
            $table->string('roomno', 5)->default('');
            $table->string('contradocid', 30)->default('');
            $table->integer('contrasno')->default(0);
            $table->integer('item')->nullable();
            $table->decimal('qtyiss', 10)->default(0);
            $table->decimal('qtyrec', 10, 0)->default(0);
            $table->string('unit', 6);
            $table->decimal('rate', 10);
            $table->decimal('amount', 10);
            $table->decimal('taxper', 10)->default(0);
            $table->decimal('taxamt', 10)->default(0);
            $table->decimal('discper', 4)->default(0);
            $table->decimal('discamt', 10)->default(0);
            $table->string('description', 100)->default('');
            $table->string('voidyn', 1)->default('');
            $table->string('remarks', 75)->default('');
            $table->string('kotdocid', 50)->default('');
            $table->integer('kotsno')->default(0);
            $table->time('vtime')->nullable();
            $table->string('u_name', 15);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1);
            $table->decimal('total', 10)->default(0);
            $table->string('discapp', 1)->default('');
            $table->string('roundoff', 3)->default('');
            $table->string('departcode', 7);
            $table->string('godowncode', 7);
            $table->decimal('chalqty', 10)->default(0);
            $table->decimal('recdqty', 10)->default(0);
            $table->decimal('accqty', 10)->default(0);
            $table->decimal('rejqty', 10)->default(0);
            $table->string('recdunit', 6)->default('');
            $table->string('specification', 35)->default('');
            $table->decimal('itemrate', 10)->default(0);
            $table->string('delflag', 1)->default('');
            $table->string('delremark')->nullable();
            $table->decimal('landval', 10)->default(0);
            $table->decimal('convratio', 10)->default(0);
            $table->string('indentdocid', 50);
            $table->integer('indentsno')->default(0);
            $table->decimal('issqty', 10)->default(0);
            $table->string('issueunit', 7)->default('');
            $table->integer('freesno')->default(0);
            $table->string('schemecode', 7)->default('');
            $table->smallInteger('seqno')->default(0);
            $table->string('company', 8)->default('');
            $table->string('itemrestcode', 7)->default('');
            $table->string('schrgapp', 1)->default('');
            $table->decimal('schrgper', 10)->default(0);
            $table->decimal('schrgamt', 10)->default(0);
            $table->string('refdocid', 50)->default('');
            $table->dateTime('ExpDate')->nullable();
            $table->string('mergedwith')->default('');

            $table->index(['item', 'itemrestcode'], 'idx_stock_item_rest');
            $table->primary(['propertyid', 'docid', 'sno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
