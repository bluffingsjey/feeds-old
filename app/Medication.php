<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    // Table Used	
	protected $table = "feeds_medication";
	
	// Disable timestamps
	public $timestamps = false;
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'med_id';
	
	// Mass Assignment
	protected $fillable = [
		'med_name',	
		'med_description',
		'med_amount'
	];
	
	
	/**
	*	A medication is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function medication()
	{
		return $this->belongsTo('App\User');
	}
}
