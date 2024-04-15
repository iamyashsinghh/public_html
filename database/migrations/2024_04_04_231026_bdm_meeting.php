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
        Schema::create('bdm_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('lead_id')->on('bdm_leads');
            $table->unsignedBigInteger('created_by')->comment("Bdm ID");
            $table->dateTime('meeting_schedule_datetime');
            $table->text('message')->nullable();
            $table->text('done_message')->nullable();
            $table->dateTime('done_datetime')->nullable();
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
        //
    }
};
