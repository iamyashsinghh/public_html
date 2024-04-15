<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('nvrm_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('nv_leads');
            $table->unsignedBigInteger('vendor_category_id');
            $table->foreign('vendor_category_id')->references('id')->on('vendor_categories');
            $table->unsignedBigInteger('created_by')->comment("NVRM's ID");
            $table->foreign('created_by')->references('id')->on('team_members');
            $table->string('title');
            $table->text('message')->nullable();
            $table->integer('budget')->nullable();
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
        Schema::dropIfExists('nvrm_messages');
    }
};
