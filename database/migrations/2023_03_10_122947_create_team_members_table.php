<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     * This table is using as users. team member login by otp which we will be send on their whatsapp number and on email. 
     */
    public function up() {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('team_members');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->string("name");
            $table->string("mobile");
            $table->string("email");
            $table->string("venue_name")->nullable();
            $table->boolean("status")->default(true)->comment("0=deactive, 1=active");
            $table->string('profile_image')->nullable();
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
        Schema::dropIfExists('team_members');
    }
};
