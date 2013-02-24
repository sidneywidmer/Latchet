<?php namespace Sidney\Latchet;

use Illuminate\Support\ServiceProvider;

class LatchetServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('sidney/latchet');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerLatchet();
	    $this->registerCommands();
	}

	/**
	 * Register the application bindings.
	 *
	 * @return void
	 */
	private function registerLatchet()
	{
		$this->app->bind('latchet', function($app)
		{
		    return new Latchet($app);
		});
	}

	/**
	 * Register the artisan commands.
	 *
	 * @return void
	 */
	private function registerCommands()
	{
		$this->app['command.latchet.listen'] = $this->app->share(function($app)
		{
			return new ListenCommand($app);
		});

		$this->commands(
			'command.latchet.listen'
		);
	}

}