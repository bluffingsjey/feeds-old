<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    // Table Used	
	protected $table = "feeds_messages";
	
	// Disable timestamps
	public $timestamps = false;
	
	// Mass Assignment
	protected $fillable = [
		'admin',
		'user',
		'message',
		'posted',
		'unique_id'	
	];
}
