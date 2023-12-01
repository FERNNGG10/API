<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use APp\Models\User;

class Alert extends Model
{   protected $table ='alerts';
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
