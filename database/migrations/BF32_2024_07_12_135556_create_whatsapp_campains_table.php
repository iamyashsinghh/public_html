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
        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('template_name')->nullable();
            $table->string('assign_to')->nullable();
            $table->string('name')->nullable();
            $table->string('team_name')->nullable();
            $table->integer('is_rm_name')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_campaigns');
    }
};
