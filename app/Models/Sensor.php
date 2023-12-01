<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plant;

class Sensor extends Model
{
    protected $table='sensors';
    use HasFactory;

    public function plants(){
        return $this->belongsToMany(Plant::class,'sensor_plants');
    }
}
