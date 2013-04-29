<?php namespace Sidney\Latchet;

use Illuminate\Support\ServiceProvider;
use Sidney\Latchet\Generators\Generator;

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
		$this->app['latchet'] = $this->app->share(function($app)
		{
			$latchet = new Latchet($app);
			return $latchet;
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

		$this->app['command.latchet.generate'] = $this->app->share(function($app)
		{
			$path = app_path() . '/socket';

			$generator = new Generator($app['files']);

			return new GenerateCommand($generator, $path);
		});

		$this->commands(
			'command.latchet.listen',
			'command.latchet.generate'
		);
	}

}