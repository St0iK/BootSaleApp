<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdBid extends Model
{
  
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['user_id', 'amount','status'];

    /**
     * 
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    /**
     * 
     */
    public function ad()
    {
        return $this->belongsTo('App\Ad');
    }
}
