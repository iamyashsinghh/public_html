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
        Schema::create('dashboard_statistics', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('type', 191); // Type can be 'vendor', 'rm', 'nvrm', 'vm', 'chart', etc.
            $table->json('data')->nullable(); // JSON field for vendor, RM, NVRM, VM statistics
            $table->json('chart_data')->nullable(); // JSON field specifically for storing chart data
            $table->timestamps(); // created_at and updated_at fields
            $table->unique('type'); // Ensure each type is unique
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dashboard_statistics');
    }
};
