<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BinsCat extends Model
{
    // Table Used	
	protected $table = "feeds_bin_category";
	
	// Mass Assignment
	protected $fillable = ['name','description'];
	
	
	/**
	*	A farm is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function binCat()
	{
		return $this->belongsTo('App\User');
	}
}
