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
        Schema::create('login_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('login_type');
            $table->unsignedBigInteger('user_id');
            $table->mediumInteger('otp_code')->nullable();
            $table->dateTime('request_otp_at')->nullable();
            $table->smallInteger('request_otp_count')->nullable();
            $table->dateTime('login_at')->nullable();
            $table->smallInteger('login_count')->nullable();
            $table->dateTime('logout_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=offline, 1=online');
            $table->string('token')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('team_members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_infos');
    }
};
