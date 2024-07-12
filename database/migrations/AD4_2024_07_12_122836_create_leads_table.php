<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('lead_id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('also used for done_by');
            $table->dateTime('lead_datetime')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->string('lead_catagory')->nullable();
            $table->string('source')->nullable();
            $table->string('preference')->nullable();
            $table->string('locality')->nullable();
            $table->string('lead_status')->default('Active');
            $table->tinyInteger('read_status')->default(0);
            $table->tinyInteger('service_status')->default(0);
            $table->dateTime('event_datetime')->nullable();
            $table->smallInteger('pax')->nullable()->comment('This is the event pax and it is used for manager CRM.');
            $table->smallInteger('enquiry_count')->default(1);
            $table->string('virtual_number')->nullable()->comment('Call to wb api virtual number');
            $table->string('lead_color')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->string('done_title')->nullable();
            $table->text('done_message')->nullable();
            $table->string('last_forwarded_by')->nullable();
            $table->string('assign_to')->nullable()->comment('Contains rm name');
            $table->unsignedBigInteger('assign_id')->nullable();
            $table->integer('is_whatsapp_msg')->nullable();
            $table->dateTime('whatsapp_msg_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('recording_url')->nullable();
            $table->integer('is_ad')->nullable();
            $table->string('user_ip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('team_members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
