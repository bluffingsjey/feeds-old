<?php

namespace App\Console;


use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
		'App\Console\Commands\ConsumptionAutoUpdater',
		'App\Console\Commands\TurnOnFarms',
		'App\Console\Commands\BuildBinsCache',
		'App\Console\Commands\StartWebSocket',
		'App\Console\Commands\ForecastingCache',
    'App\Console\Commands\SchedulingCache',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
      $schedule->command('forecastingdatacache')->everyMinute();
      $schedule->command('schedulingcache')->everyMinute();
    	$schedule->command('consumption')->dailyAt('01:15');
    	$schedule->command('turnonfarms')->dailyAt('01:00');
    	$schedule->command('buildbinscache')->dailyAt('02:00');
    	$schedule->command('startwebsocket')->dailyAt('06:46');
    	/*$schedule->call('\App\Http\Controllers\HomeController@testCron')->dailyAt("05:27")
    			 ->sendOutputTo(storage_path().'/logs/cron.log');*/
    }


}
