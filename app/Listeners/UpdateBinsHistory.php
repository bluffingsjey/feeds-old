<?php

namespace App\Listeners;

use App\BinsHistory;
use App\Events\CallBinsHistory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateBinsHistory //implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  CallBinsHistory  $event
     * @return void
     */
    public function handle(CallBinsHistory $event)
    {

      $data = array(
        'update_date' => $event->data['update_date'],
				'amount' => $event->data['amount'],
				'budgeted_amount_tons' => $event->data['budgeted_amount_tons'],
				'actual_amount_tons' => $event->data['actual_amount_tons'],
				'bin_id' => $event->data['bin_id'],
				'farm_id' => $event->data['farm_id'],
				'num_of_pigs' => $event->data['num_of_pigs'],
				'user_id' => $event->data['user_id'],
				'update_type' => $event->data['update_type'],
				'admin' => $event->data['admin'],
				'created_at' => $event->data['created_at'],
				'updated_at' => $event->data['updated_at'],
				'budgeted_amount' => $event->data['budgeted_amount'],
				'remaining_amount' => $event->data['remaining_amount'],
				'sub_amount' => $event->data['sub_amount'],
				'variance' => $event->data['variance'],
				'consumption' => $event->data['consumption'],
				'medication' => $event->data['medication'],
				'feed_type' => $event->data['feed_type'],
				'unique_id' => $event->data['unique_id']
      );

      BinsHistory::where('history_id','=',$event->data['history_id'])
            ->update($data);
    }
}
