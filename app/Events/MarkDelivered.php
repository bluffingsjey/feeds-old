<?php

namespace App\Events;

use App\Deliveries;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MarkDelivered extends Event
{
    use SerializesModels;

    public $data = array();

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->data = $data;
    }

}
