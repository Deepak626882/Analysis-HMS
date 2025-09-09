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
        Schema::create('guestfolio', function (Blueprint $table) {
            $table->integer('sn', true)->unique('sn');
            $table->integer('propertyid');
            $table->string('docid', 100)->index('idx_guestfolio_docid');
            $table->integer('folio_no')->nullable()->index('idx_guestfolio_folio_no');
            $table->string('vtype', 10)->nullable();
            $table->date('vdate')->nullable();
            $table->string('vprefix', 4)->nullable();
            $table->string('guestprof', 10)->nullable()->index('idx_guestfolio_guestprof');
            $table->string('name')->nullable();
            $table->string('add1', 50)->nullable();
            $table->string('add2', 50)->nullable();
            $table->string('city', 35)->nullable();
            $table->integer('nodays')->nullable();
            $table->string('remarks', 50)->nullable();
            $table->string('pickupdrop')->default('');
            $table->string('company', 10)->nullable()->index('idx_guestfolio_company');
            $table->string('purvisit', 10)->nullable();
            $table->string('arrfrom', 10)->nullable();
            $table->string('destination', 10)->nullable();
            $table->string('travelmode', 10)->nullable();
            $table->string('vehiclenum', 15)->nullable();
            $table->string('remark', 50)->nullable();
            $table->decimal('rodisc', 7)->nullable();
            $table->decimal('rsdisc', 7)->nullable();
            $table->string('bookingdocid', 50)->nullable();
            $table->string('u_name', 25);
            $table->dateTime('u_entdt');
            $table->dateTime('u_updatedt')->nullable();
            $table->string('u_ae', 1)->nullable()->default('a');
            $table->string('busssource', 100)->nullable()->index('idx_guestfolio_busssource');
            $table->string('booking_source', 20)->nullable();
            $table->date('depdate')->nullable();
            $table->string('travelagent', 10)->nullable()->index('idx_guestfolio_travelagent');
            $table->integer('roomcount')->nullable();
            $table->integer('mfoliono')->nullable();
            $table->string('comp', 10)->nullable();
            $table->string('mfolionodocid', 30)->nullable();
            $table->integer('refno')->nullable();
            $table->integer('bookingsno')->nullable();
            $table->string('refbookno', 15)->nullable();

            $table->index(['docid', 'guestprof'], 'idx_guestfolio_doc_guest');
            $table->index(['propertyid', 'docid'], 'idx_guestfolio_propertyid_docid');
            $table->index(['travelagent'], 'idx_guestfolio_travel_agent');
            $table->primary(['propertyid', 'docid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guestfolio');
    }
};
