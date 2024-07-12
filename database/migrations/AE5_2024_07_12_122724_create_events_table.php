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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('created_by');
            $table->string('event_name')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->smallInteger('pax')->nullable()->comment('Number of guests');
            $table->string('budget')->nullable();
            $table->string('food_preference')->nullable();
            $table->string('event_slot')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('lead_id')->on('leads');
            $table->foreign('created_by')->references('id')->on('team_members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
