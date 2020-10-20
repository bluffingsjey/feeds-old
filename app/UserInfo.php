<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'feeds_user_info';
	
	// Disable timestamps
	public $timestamps = false;
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'info_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'middle_name', 'last_name','contact_number','user_id'];
	
	/**
	*	A user information is owned by a user
	*
	*	@param
	*	@return \Illuminate\Database\Eloquent\Relations\BelongsTo
	*/
	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
