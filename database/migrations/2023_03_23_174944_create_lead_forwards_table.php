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
        Schema::create('lead_forwards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads');
            // $table->unsignedBigInteger('forward_from')->nullable(); we need to remove this column;
            // $table->foreign('forward_from')->references('id')->on('team_members');
            $table->unsignedBigInteger('forward_to');
            $table->foreign('forward_to')->references('id')->on('team_members');
            $table->dateTime('lead_datetime')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->string('source')->nullable();
            $table->string('locality')->nullable();
            $table->string('lead_status')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->boolean('read_status')->default(false)->comment("0=Unread 1=Read");
            $table->boolean('service_status')->default(false)->comment("0=NotContacted 1=Contacted");
            $table->string('done_title')->nullable();
            $table->text('done_message')->nullable();
            $table->unsignedBigInteger('task_id')->nullable()->comment("This is used only for the task list page");
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->unsignedBigInteger('visit_id')->nullable()->comment("This is used only for the visit list page");
            $table->foreign('visit_id')->references('id')->on('visits');
            $table->unsignedBigInteger('booking_id')->nullable()->comment("This is used only for the booking list page");
            $table->foreign('booking_id')->references('id')->on('bookings');
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
        Schema::dropIfExists('lead_forwards');
    }
};
