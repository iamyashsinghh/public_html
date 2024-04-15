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
        Schema::create('lead_forward_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->unsignedBigInteger('forward_from')->comment("Team member id");
            $table->foreign('forward_from')->references('id')->on('team_members');
            $table->unsignedBigInteger('forward_to')->comment("Team member id");
            $table->foreign('forward_to')->references('id')->on('team_members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('lead_forward_infos');
    }
};
