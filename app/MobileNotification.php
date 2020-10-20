<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileNotification extends Model
{
     // Table Used	
	protected $table = "feeds_mobile_notification";
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'id';
	
	
	// Disable timestamps
	public $timestamps = false;
	
	// Mass Assignment
	protected $fillable = [	
		'farm_id',
		'driver_id',
		'user_id',
		'date_of_delivery',
		'is_readred'	
	];
}
