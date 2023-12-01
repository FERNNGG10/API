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
        Schema::create('sensor_plants',function(Blueprint $table){

            $table->id();
            $table->foreignId('plant_id')
            ->constrained('plants')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreignId('sensor_id')
            ->constrained('sensors')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_plants');
    }
};
