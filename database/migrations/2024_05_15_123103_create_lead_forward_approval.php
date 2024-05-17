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
        Schema::create('lead_forward_approval', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->unsignedBigInteger('forward_from');
            $table->foreign('forward_from')->references('id')->on('team_members');
            $table->unsignedBigInteger('forward_to');
            $table->foreign('forward_to')->references('id')->on('team_members');
            $table->unsignedBigInteger('forward_by');
            $table->foreign('forward_by')->references('id')->on('team_members');
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('lead_id')->on('leads');
            $table->string('is_approved')->nullable();
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
        Schema::dropIfExists('lead_forward_approval');
    }
};
