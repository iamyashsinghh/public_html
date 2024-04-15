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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('nvrm_lead_forwards');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('team_members');
            $table->dateTime('task_schedule_datetime');
            $table->string('follow_up')->nullable();
            $table->text('message')->nullable();
            $table->string('done_with')->nullable();
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
    public function down() {
        Schema::dropIfExists('tasks');
    }
};
