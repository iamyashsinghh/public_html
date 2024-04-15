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
        Schema::create('leads', function (Blueprint $table) {
            $table->id('lead_id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('also used for done_by');
            $table->foreign('created_by')->references('id')->on('team_members');
            $table->dateTime('lead_datetime')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->string('source')->nullable();
            $table->string('preference')->nullable();
            $table->string('locality')->nullable();
            $table->string('lead_status')->default("Active");
            $table->boolean('read_status')->default(false)->comment("0=Unread 1=Read");
            $table->boolean('service_status')->default(false)->comment("0=NotContacted 1=Contacted");
            $table->dateTime('event_datetime')->nullable();
            $table->smallInteger('pax')->nullable()->comment('This is the event pax and it is used for manager CRM.');
            $table->smallInteger('enquiry_count')->default(1);
            $table->string('virtual_number')->nullable()->comment("Call to wb api virtual number");
            $table->string('lead_color')->nullable()->comment('This column is used only for lead color concept, and it contains dynamic color hex code');
            $table->unsignedBigInteger('task_id')->nullable()->comment("This is used only for the task list page");
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->unsignedBigInteger('visit_id')->nullable()->comment("This is used only for the visit list page");
            $table->foreign('visit_id')->references('id')->on('visits');
            $table->string('done_title')->nullable();
            $table->text('done_message')->nullable();
            $table->string('last_forwarded_by')->nullable()->comment('contains string');
            $table->string('assign_to')->nullable();
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
        Schema::dropIfExists('leads');
    }
};
