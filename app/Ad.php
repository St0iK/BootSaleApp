<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{

	/**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'title', 'description', 'user_id',
      'category_id', 'price', 'latitude',
      'longitude','currency_code',
  ];
  
	protected $hidden = ['countBids','countComments'];
	
	/**
	 * Add fields to the Model
	 */
	protected $appends = ['total_comments','total_bids'];

	/**
	 * 
	 */
	public function categories()
	{
	    return $this->belongsToMany('App\AdCategory');
	}

	/**
	 * 
	 */
	public function photos()
	{
	    return $this->hasMany('App\AdPhoto');
	}

	/**
	 * 
	 */
	public function bids()
	{
	    return $this->hasMany('App\AdBid');
	}

	/**
	 * 
	 */
	public function comments()
	{
	    return $this->hasMany('App\AdComment');
	}

    /**
     * 
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

  /**
   * an Eloquent relation that contains the total comments
   */
  public function countComments()
  {
    return $this->hasOne('App\AdComment')
      ->selectRaw('ad_id, count(*) as count')
      ->groupBy('ad_id');
  }

  /**
   * an Eloquent relation that contains the total bids
   */
  public function countBids()
  {
    return $this->hasOne('App\AdBid')
      ->selectRaw('ad_id, count(*) as count')
      ->groupBy('ad_id');
  } 

  /**
   * Total Comments attribute
   */
  public function getTotalCommentsAttribute()
  {
  	// if relation is not loaded already, let's do it first
  	if ( ! array_key_exists('countComments', $this->relations))
  	{
  		$this->load('countComments');
  	}

  	$related = $this->getRelation('countComments');
  	return ($related) ? (int)$related['attributes']['count'] : 0;
  }

  /**
   * Total Bids attribute
   */
  public function getTotalBidsAttribute()
  {
  	// if relation is not loaded already, let's do it first
  	if ( ! array_key_exists('countBids', $this->relations))
  	{
  		$this->load('countBids');
  	}
  	$related = $this->getRelation('countBids');
  	return ($related) ? (int)$related['attributes']['count'] : 0;
  }

  public function ScopeWithinDistance($query,$from_latitude,$from_longitude)
  {
    $raw = \DB::raw('ROUND ( ( 6371 * acos( cos( radians('.$from_latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$from_longitude.') ) + sin( radians('.$from_latitude.') ) * sin( radians( latitude ) ) ) ) ) AS distance');
    return $query->select('*')->addSelect($raw)->orderBy( 'distance', 'ASC' );
  }
  
}
