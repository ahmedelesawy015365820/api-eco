<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable;


    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value){
        $this->attributes['password'] = bcrypt($value);
    }

    // public function getAuthAttribute($q)
    // {
    //     if($q == 1){
    //         return 'admin';
    //     }elseif($q == 2){
    //         return 'employee';
    //     }else{
    //         return 'customer';
    //     }
    // }

    public function getStatusAttribute($q)
    {
        return $q == 'active'? 'Active' : "InActive";
    }

    // start JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // start relation

    public function media()
    {
        return $this->morphOne(Media::class,'mediable');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class,'user_id');
    }

    // reset password
    public function sendPasswordResetNotification($token)
    {
        $url = 'http://localhost:8000/auth/reset-password?token='.$token;

        $this->notify(new ResetPasswordNotification($url));
    }

}