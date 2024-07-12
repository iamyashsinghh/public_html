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
        Schema::create('nv_lead_forward_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('forward_from')->comment("Team member's ID");
            $table->unsignedBigInteger('forward_to')->comment("Vendor's ID");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('nv_leads');
            $table->foreign('forward_from')->references('id')->on('team_members');
            $table->foreign('forward_to')->references('id')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nv_lead_forward_infos');
    }
};
