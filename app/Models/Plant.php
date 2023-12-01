<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Sensor;

class Plant extends Model
{ 
    protected $table ='plants';
    use HasFactory;
   
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function sensors(){
        return $this->belongsToMany(Sensors::class,'sensor_plants');
    }
}
