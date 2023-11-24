<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $table='sensors';
    use HasFactory;
    public function plant(){
        return $this->belongsToMany(Plant::class,'plant_sensor');
    }
}
