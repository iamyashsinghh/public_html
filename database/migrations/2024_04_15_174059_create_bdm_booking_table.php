<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bdm_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('lead_id')->on('bdm_leads');
            $table->unsignedBigInteger('created_by')->comment("Bdm ID");
            $table->string('booking_date');
            $table->string('package_name');
            $table->string('price');
            $table->string('payment_method');
            $table->string('payment_proof');
            $table->string('order_agreement_farm_image');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bdm_booking');
    }
};
