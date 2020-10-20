<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	
	// Mass Assignment
	protected $fillable = [	
		'name'	
	];
	
	/**
	*	Get the farms associated with the given tag.
	*
	* 	@return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	*/
    public function farms()
	{
		return $this->belongsToMany('App\Farms');
	}
}
