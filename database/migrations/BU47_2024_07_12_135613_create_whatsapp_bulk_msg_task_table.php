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
        Schema::create('whatsapp_bulk_msg_task', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('campaign_name')->nullable();
            $table->text('lead_ids')->nullable();
            $table->text('numbers')->nullable();
            $table->text('img')->nullable();
            $table->text('msg')->nullable();
            $table->enum('status', ['0', '1'])->comment('0 for in progress, 1 for completed');
            $table->string('template_name')->nullable();
            $table->integer('is_rm_name')->nullable();
            $table->string('team_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('lead_id_type')->nullable()->comment('1=>venue, 2=>nv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_bulk_msg_task');
    }
};
