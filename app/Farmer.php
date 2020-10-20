<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    // Table Used	
	protected $table = "feeds_farm_users";
	
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
	
	// Mass Assignment
	protected $fillable = [	
		'farm_id',
		'user_id'	
	];
	
	
	/**
	*	A farm is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function user()
	{
		return $this->belongsTo('App\User');
	}

}
