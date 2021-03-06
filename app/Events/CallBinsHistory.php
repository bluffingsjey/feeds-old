<?php

namespace App\Events;

use App\BinsHistory;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CallBinsHistory extends Event
{
    use SerializesModels;

    public $data = array();

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BinsHistory $data)
    {
        $this->data = $data;
    }

}
