<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedTypes extends Model
{
     // Table Used
	protected $table = "feeds_feed_types";

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
		'budgeted_amount',
		'total_days',
		'user_id'
	];


	/**
	*	A farm is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function feedType()
	{
		return $this->belongsTo('App\User');
	}
}
