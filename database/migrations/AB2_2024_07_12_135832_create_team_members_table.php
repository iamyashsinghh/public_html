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
        Schema::create('team_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->string('name');
            $table->string('mobile');
            $table->string('email');
            $table->string('venue_name')->nullable();
            $table->integer('venue_id')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=deactive, 1=active');
            $table->string('profile_image')->nullable();
            $table->integer('nvrm_id')->nullable();
            $table->text('device_token')->nullable();
            $table->integer('is_next')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->tinyInteger('can_add_device')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('team_members')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_members');
    }
};
