<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'user_id', 'os', 'version',
	    'country_code', 'user_agent', 'ip_address',
	];
    /**
     * 
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
