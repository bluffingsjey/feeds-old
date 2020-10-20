<?php

namespace App\Providers;

use App\Farms;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->composerNavigation();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
	
	/**
	* Compose the navigation bar.
	*
	* @return void
	*/
    private function composerNavigation()
    {
        view()->composer('partials.nav', function($view){
			$view->with('latest', Farms::latest()->first());
		});
    }
}
