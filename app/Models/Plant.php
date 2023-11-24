<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Sensor;
class Plant extends Model
{
    use HasFactory;
    protected $table='plants';

    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function sensor(){
        return $this->belongsToMany(Sensor::class,'plant_sensor');
    }
}
