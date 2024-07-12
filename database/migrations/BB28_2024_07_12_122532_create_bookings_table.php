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
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('event_id')->comment('vm_events table');
            $table->string('party_area');
            $table->string('menu_selected');
            $table->string('booking_source');
            $table->string('price_per_plate');
            $table->string('total_gmv')->comment('total booking amount');
            $table->string('advance_amount')->nullable();
            $table->tinyInteger('quarter_advance_collected')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('lead_id')->on('leads');
            $table->foreign('created_by')->references('id')->on('team_members');
            $table->foreign('event_id')->references('id')->on('vm_events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
