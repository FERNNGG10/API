<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\Plant;
use GuzzleHttp\Psr7\Request;
use App\Models\Alert;

class User extends Authenticatable implements JWTSubject
{
    protected $table='users';
    use Notifiable;

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function plant(){
        return $this->hasMany(Plant::class,'user_id');
    }

    public function alerts(){
        return $this->hasMany(Alert::class,'user_id');
    }


}
