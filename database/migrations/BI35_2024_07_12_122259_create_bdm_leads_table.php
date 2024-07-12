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
        Schema::create('bdm_leads', function (Blueprint $table) {
            $table->bigIncrements('lead_id');
            $table->unsignedBigInteger('created_by')->nullable()->comment('also used for done_by');
            $table->dateTime('lead_datetime')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_mobile')->nullable();
            $table->string('source')->nullable();
            $table->string('lead_status')->default('Active');
            $table->tinyInteger('read_status')->default(0)->comment('0=Unread 1=Read');
            $table->tinyInteger('service_status')->default(0)->comment('0=NotContacted 1=Contacted');
            $table->smallInteger('enquiry_count')->default(1);
            $table->string('done_title')->nullable();
            $table->text('done_message')->nullable();
            $table->string('assign_to')->nullable();
            $table->unsignedBigInteger('assign_id')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_cat')->nullable();
            $table->text('city')->nullable();
            $table->dateTime('whatsapp_msg_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('is_whatsapp_msg')->nullable();
            $table->string('lead_color')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bdm_leads');
    }
};
