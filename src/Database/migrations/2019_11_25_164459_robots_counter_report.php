<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RobotsCounterReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robots_counter_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('agent');
            $table->string('url');
            $table->string('ip');
            $table->dateTime('time_request');
            $table->string('time_exec');
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
        Schema::dropIfExists('robots_counter_report');
    }
}
