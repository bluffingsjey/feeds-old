<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingDeliveries extends Model
{
     // Table Used	
	protected $table = "feeds_deliveries_pending";
	
	// Disable timestamps
	public $timestamps = false;
		
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'delivery_id';
	
	// Mass Assignment
	protected $fillable = [	
		'delivery_date',
		'truck_id',
		'farm_id',
		'feeds_type_id',
		'medication_id',
		'amount',
		'bin_id'
	];
}
