<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FarmDelivery extends Model
{
    // Table Used	
	protected $table = "feeds_farm_deliveries";
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
	protected $primaryKey = 'delivery_id';
	
	// Mass Assignment
	protected $fillable = [	
		'batch_code',
		'driver_id',
		'truck_id',
		'farm_id',
		'compartment_number',
		'compartment_amount',
		'bin_one_color',
		'bin_two_color',
		'bin_one_number',
		'bin_two_number',
		'bins_amount',
		'status',
		'user_id',
		'date_of_delivery'	
	];
}
