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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('team_members');
            $table->dateTime('visit_schedule_datetime');
            $table->text('message')->nullable();
            $table->string('event_name')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->string('menu_selected')->nullable();
            $table->string('party_area')->nullable();
            $table->integer('price_quoted')->nullable();
            $table->text('done_message')->nullable();
            $table->dateTime('done_datetime')->nullable();
            $table->unsignedBigInteger('referred_by')->nullable()->comment("Contains only RM user id.");
            $table->foreign('referred_by')->references('id')->on('team_members');
            $table->string("vm_visits_id")->nullable()->comment("Contain visits id those who have shared with VM. in array format");
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
        Schema::dropIfExists('visits');
    }
};
