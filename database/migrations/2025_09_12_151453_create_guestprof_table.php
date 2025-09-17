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
        Schema::create('guestprof', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 100)->nullable()->index('idx_docid');
            $table->integer('folio_no')->nullable();
            $table->string('guestcode', 10)->index('idx_guestprof_guestcode');
            $table->string('name')->nullable();
            $table->string('add1', 50)->nullable();
            $table->string('add2', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state_code', 50)->nullable();
            $table->string('country_code', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('mobile_no', 10)->nullable();
            $table->string('email_id', 35)->nullable();
            $table->string('nationality', 35)->nullable()->index('idx_guestprof_nationality');
            $table->date('anniversary')->nullable();
            $table->string('guest_status', 10)->nullable()->index('idx_guestprof_guest_status');
            $table->string('spl_instr', 100)->nullable();
            $table->string('comments1', 100)->nullable();
            $table->string('comments2', 100)->nullable();
            $table->string('comments3', 100)->nullable();
            $table->string('city_name', 25)->nullable();
            $table->string('state_name', 25)->nullable();
            $table->string('country_name', 25)->nullable();
            $table->string('gender', 100)->nullable();
            $table->string('marital_status', 10)->nullable();
            $table->string('zip_code', 6)->nullable();
            $table->string('panno', 10)->nullable();
            $table->string('con_prefix', 8)->nullable();
            $table->date('dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('guestsign')->default('');
            $table->string('pic_path', 100)->nullable();
            $table->string('id_proof', 100)->nullable();
            $table->string('m_prof', 10)->nullable();
            $table->string('father_name', 35)->nullable();
            $table->string('idproof_no', 15)->nullable();
            $table->string('idpic_path', 100)->nullable();
            $table->string('issuingcitycode', 20)->nullable();
            $table->string('issuingcityname', 50)->nullable();
            $table->string('issuingcountrycode', 20)->nullable();
            $table->string('issuingcountryname', 50)->nullable();
            $table->date('expiryDate')->nullable();
            $table->string('paymentMethod', 50)->nullable();
            $table->string('billingAccount', 50)->nullable();
            $table->string('complimentry', 1)->nullable();
            $table->string('vipStatus', 50)->nullable();
            $table->string('likes', 100)->nullable();
            $table->string('dislikes', 100)->nullable();
            $table->integer('fom')->nullable();
            $table->string('pos', 3)->nullable();
            $table->string('webYN', 1)->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');

            $table->index(['guestcode', 'propertyid'], 'idx_guestprof_code_property');
            $table->index(['docid', 'guestcode'], 'idx_guestprof_doc_guest');
            $table->index(['city', 'country_code', 'state_code'], 'idx_location');
            $table->primary(['sn', 'propertyid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guestprof');
    }
};
