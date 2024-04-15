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
        Schema::create('vm_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('team_members');
            $table->string('event_name')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->smallInteger('pax')->nullable()->comment("Number of guest");
            $table->integer('budget')->nullable();
            $table->string('food_preference')->nullable();
            $table->string('event_slot')->nullable();
            $table->unsignedBigInteger('event_id')->nullable()->comment("Relate to main 'events' table, use only for lead forward scenerio.");
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
        Schema::dropIfExists('vm_events');
    }
};
