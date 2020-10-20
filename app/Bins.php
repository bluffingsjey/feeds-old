<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bins extends Model
{
    // Table Used	
	protected $table = "feeds_bins";
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'bin_id';
	
	// Mass Assignment
	protected $fillable = [
		'farm_id',
		'bin_number',
		'alias',
		'num_of_pigs',
		'hex_color',
		'bin_size',
		'created_at',
		'updated_at',
		'user_id'
	];
	
	
	/**
	*	A bin is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function user()
	{
		return $this->belongsTo('App\User');
	}
	
	/**
	*	A farm is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function farm()
	{
		return $this->belongsTo('App\Farms');
	} 
	
	/**
	*	Get a list of farm ids associated with the current bin.
	*
	*	@return array
	*/
	/*public function getFarmListAttribute()
	{
		return $this->farm->lists('id');
	}*/
}
