<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreateLoadLoadoutBins extends Model
{
    // Table Used	
	protected $table = "feeds_create_load_loadout";
	
	// Disable Timestamp
	public $timestamps = false;	
	
	// Mass Assignment
	protected $fillable = [
		'sched_id',
		'value',
		'element_id',
		'unique_id'	
	];
	
	
	/**
	*	A compartment is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
