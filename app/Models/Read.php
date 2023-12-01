<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sensor_Plant;
class Read extends Model
{
    protected $table='reads';
    use HasFactory;

    public function sensor_plant()
    {
        return $this->hasMany(Sensor_Plant::class,'sensor_plant_id');
    }
    
}
