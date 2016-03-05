<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdCategory extends Model
{
    /**
     * 
     */
    public function ad()
    {
        return $this->belongsToMany('App\Ad');
    }
}
