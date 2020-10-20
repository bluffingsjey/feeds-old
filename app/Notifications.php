<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    // Table Used	
	protected $table = "feeds_notifications";
	
	// Disable timestamps
	public $timestamps = false;
	
	// Mass Assignment
	protected $fillable = [
		'type',
		'status',
		'posted',
		'unique_id'	
	];
	
}
