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
        Schema::create('lead_forwards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('forward_to');
            $table->dateTime('lead_datetime')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->string('source')->nullable();
            $table->string('locality')->nullable();
            $table->string('lead_status')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->tinyInteger('read_status')->default(0)->comment('0=Unread 1=Read');
            $table->tinyInteger('service_status')->default(0)->comment('0=NotContacted 1=Contacted');
            $table->string('done_title')->nullable();
            $table->text('done_message')->nullable();
            $table->unsignedBigInteger('task_id')->nullable()->comment('This is used only for the task list page');
            $table->unsignedBigInteger('visit_id')->nullable()->comment('This is used only for the visit list page');
            $table->unsignedBigInteger('booking_id')->nullable()->comment('This is used only for the booking list page');
            $table->integer('is_manager_forwarded')->nullable();
            $table->integer('manager_forwarded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('lead_id')->on('leads');
            $table->foreign('forward_to')->references('id')->on('team_members');
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('visit_id')->references('id')->on('visits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_forwards');
    }
};
