<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BinsType extends Model
{
    // Table Used	
	//protected $table = "feeds_bin_types";
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'type_id';
	
	// Mass Assignment
	protected $fillable = [
		'name',	
		'description',
		'rings',
		'category_id',
		'max_density',
		'user_id'	
	];
	
	
	/**
	*	A farm is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function binType()
	{
		return $this->belongsTo('App\User');
	}
}
