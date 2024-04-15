<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('lead_id')->on('leads');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('team_members');
            $table->unsignedBigInteger('event_id')->comment('vm_events table');
            $table->foreign('event_id')->references('id')->on('vm_events');
            $table->string('party_area');
            $table->string('menu_selected');
            $table->string('booking_source');
            $table->string('price_per_plate');
            $table->string('total_gmv')->comment('total booking amount');
            $table->string('advance_amount')->nullable();
            $table->boolean('quarter_advance_collected')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bookings');
    }
};
