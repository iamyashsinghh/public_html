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
        Schema::table('roles', function (Blueprint $table) {
            $table->time('login_start_time')->nullable();
            $table->time('login_end_time')->nullable();
            $table->tinyInteger('is_all_time_login')->default(1)->comment('0=deactive, 1=active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('login_start_time');
            $table->dropColumn('login_end_time');
            $table->dropColumn('is_all_time_login');
        });
    }
};
