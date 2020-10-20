<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    // Table Used	
	protected $table = "feeds_truck";
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'truck_id';
	
	// Mass Assignment
	protected $fillable = [
							'driver_id',
							'name',
							'capacity',
							'compartment',
							'per_compartment',
							'user_id'
							];
	
	
	/**
	*	A farm is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function truck()
	{
		return $this->belongsTo('App\User');
	}
	
	
}
