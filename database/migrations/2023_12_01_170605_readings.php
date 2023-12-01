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
            $table->foreignId('sensor_plant_id')
            ->constrained('sensor_plants')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->float('value');
            $table->dateTimeTz('created_at');
          
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
