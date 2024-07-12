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
        Schema::create('vendors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('parent_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=deactive, 1=active');
            $table->string('profile_image')->nullable();
            $table->integer('display_order')->nullable();
            $table->string('group_name')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('alt_mobile_number')->nullable();
            $table->tinyInteger('can_add_device')->default(1);
            $table->integer('is_whatsapp_msg')->nullable();
            $table->timestamp('whatsapp_msg_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('is_lead_forwarded')->nullable();
            $table->string('last_lead_forwarded_value')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('vendor_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};
