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
        Schema::create('readings',function(Blueprint $table){
            
            $table->id();
            $table->unsignedBigInteger('plant_sensor_id')->onDelete('cascade')->onUpdate('cascade');
            $table->float('value');
            $table->dateTimeTz('created_at');
            $table->foreign('plant_sensor_id')->references('id')->on('plant_sensors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('readings');
    }
};
