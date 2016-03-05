<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdPhoto extends Model
{
  
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'title', 'description', 'ad_id','path', 'thumb_path'
  ];

    /**
     * 
     */
    public function ad()
    {
        return $this->belongsTo('App\Ad');
    }
}
