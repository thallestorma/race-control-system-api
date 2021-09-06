<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaceRunnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_runner', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_race');
            $table->unsignedInteger('id_runner');
            $table->time('start_time')->nullable();
            $table->time('finish_time')->nullable();
            $table->timestamps();

            $table->unique(['id_race', 'id_runner']);
            $table->foreign('id_race')->references('id')->on('race');
            $table->foreign('id_runner')->references('id')->on('runner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_runner');
    }
}
