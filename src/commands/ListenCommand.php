<?php namespace Sidney\Latchet;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Ratchet\Wamp\WampServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class ListenCommand extends Command {

	/**
	 * Latchet Instance
	 */
	protected $latchet;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'latchet:listen';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Start listening on specified port for incomming connections';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($app)
	{
		$this->latchet = $app->make('latchet');
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$server = IoServer::factory(
		    new WsServer(
		        new WampServer($this->latchet)
		    ), $this->option('port')
		);


		$this->info('Listening on port ' . $this->option('port'));
		$server->run();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('port', 'p', InputOption::VALUE_OPTIONAL, 'The Port on which we listen for new connections', 1111),
		);
	}

}