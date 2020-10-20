<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Farms extends Model
{
    // Table Used
	protected $table = "feeds_farms";

	// Mass Assignment
	protected $fillable = [
		'id',
		'name',
		'packer',
		'farm_type',
		'address',
		'delivery_time',
		'lattitude',
		'longtitude',
		'contact',
		'notes',
		'owner',
		'update_notification'	
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

	/**
	*	Get the tags associated with the given farm.
	*
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	*/
	public function tags()
	{
		return $this->belongsToMany('App\Tag')->withTimestamps();
	}

	/**
	*	Get a list of tag ids associated with the current farm.
	*
	*	@return array
	*/
	public function getTagListAttribute()
	{
		return $this->tags->lists('id');
	}

}
