<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'first_name', 'last_name', 'profile_pic',
        'username'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the ads for the user.
     */
    public function ads()
    {
        return $this->hasMany('App\Ad');
    }

    /**
     * Get the ads for the user.
     */
    public function comments()
    {
        return $this->hasMany('App\AdComment');
    }

    /**
     * 
     */
    public function bids()
    {
        return $this->hasMany('App\AdBids');
    }

    /**
     * 
     */
    public function locations()
    {
        return $this->hasMany('App\UserLocation');
    }

    /**
     * 
     */
    public function information()
    {
        return $this->hasMany('App\UserInformation');
    }
}
