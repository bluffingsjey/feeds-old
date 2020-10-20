<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compartments extends Model
{
    // Table Used	
	protected $table = "feeds_truck_compartment";
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'compartment_id';
	
	// Mass Assignment
	protected $fillable = [
		'compartment_number',
		'capacity',
		'truck_id',
		'user_id'	
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
	
	/**
	*	A truck is owned by a truck
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function truck()
	{
		return $this->belongsTo('App\Truck');
	} 
}
