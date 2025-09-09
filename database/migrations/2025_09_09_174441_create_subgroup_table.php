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
        Schema::create('subgroup', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->string('sub_code', 100)->index('idx_subgroup');
            $table->integer('propertyid');
            $table->string('name', 100);
            $table->string('group_code', 100);
            $table->string('nature', 100);
            $table->string('comp_type', 100)->nullable();
            $table->string('allow_credit', 100)->nullable();
            $table->string('conprefix')->nullable();
            $table->string('conperson')->nullable();
            $table->string('address')->nullable();
            $table->string('citycode')->nullable();
            $table->string('pin')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('panno')->nullable();
            $table->string('tds_catg')->nullable();
            $table->char('activeyn', 1)->nullable()->default('Y');
            $table->char('allowcredit', 1)->nullable();
            $table->decimal('creditlimit', 10)->nullable();
            $table->integer('creditdays')->nullable();
            $table->string('remark')->nullable();
            $table->string('discounttype')->nullable();
            $table->decimal('discount', 5)->nullable();
            $table->string('religion')->nullable();
            $table->text('remarks')->nullable();
            $table->char('blacklisted', 1)->nullable();
            $table->string('reasonblacklist')->nullable();
            $table->string('blacklistedby')->nullable();
            $table->string('sysYN', 10)->nullable();
            $table->string('u_name');
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae')->default('a');
            $table->string('gstin')->nullable();
            $table->string('mapcode')->nullable();
            $table->string('dealertype')->nullable();
            $table->string('legalname')->nullable();
            $table->string('tradename')->nullable();
            $table->integer('subyn')->nullable();

            $table->index(['sub_code'], 'idx_subgroup_sub_code');
            $table->primary(['sub_code', 'propertyid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subgroup');
    }
};
