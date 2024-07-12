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
        Schema::create('nvrm_lead_forwards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('forward_to')->comment("NVRM's ID.");
            $table->dateTime('lead_datetime')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->text('address')->nullable();
            $table->string('lead_status')->nullable();
            $table->dateTime('event_datetime')->nullable();
            $table->tinyInteger('read_status')->default(0)->comment('0=Unread 1=Read');
            $table->string('done_title')->nullable();
            $table->text('done_message')->nullable();
            $table->string('last_forwarded_by')->nullable();
            $table->string('created_by')->nullable();
            $table->string('service_status')->nullable();
            $table->dateTime('whatsapp_msg_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('is_whatsapp_msg')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('nv_leads');
            $table->foreign('forward_to')->references('id')->on('team_members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nvrm_lead_forwards');
    }
};
