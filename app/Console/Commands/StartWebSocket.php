<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartWebSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'startwebsocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the service of websocket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        shell_exec("php /home/j2feeds/laravel/app/server.php");
		shell_exec("php /home/j2feeds/laravel/app/forecastingwebsocket.php");
		
    }
}
