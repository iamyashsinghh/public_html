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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('template_name')->nullable();
            $table->string('is_variable_template')->nullable();
            $table->text('img')->nullable();
            $table->text('msg')->nullable();
            $table->string('status')->nullable();
            $table->string('last_updated_time')->nullable();
            $table->text('head_val')->nullable();
            $table->text('body_val')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
