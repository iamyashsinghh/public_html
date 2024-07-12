<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('party_area_id');
            $table->string('food_type')->nullable();
            $table->smallInteger('pax');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('team_members');
            $table->foreign('party_area_id')->references('id')->on('party_areas');
        });
    }

    public function down()
    {
        Schema::dropIfExists('availabilities');
    }
};
