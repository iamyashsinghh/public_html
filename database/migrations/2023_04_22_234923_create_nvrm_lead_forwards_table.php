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
        Schema::create('nvrm_lead_forwards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('nv_leads');
            $table->unsignedBigInteger('forward_to')->comment("NVRM's ID.");
            $table->foreign('forward_to')->references('id')->on('team_members');
            $table->dateTime('lead_datetime')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->text('address')->nullable();
            $table->string('lead_status')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->boolean('read_status')->default(false)->comment("0=Unread 1=Read");
            $table->string('done_title')->nullable();
            $table->text('done_message')->nullable();
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
        Schema::dropIfExists('nvrm_lead_forwards');
    }
};
