<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Read;

class Sensor_Plant extends Model
{
    protected $table='sensor_plants';
    use HasFactory;

    public function read(){
        return $this->belongsTo(Read::class,'sensor_plant_id');
    }
}
