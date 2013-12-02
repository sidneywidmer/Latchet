<?php namespace Sidney\Latchet;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
		$loop   = \React\EventLoop\Factory::create();

		if(\Config::get('latchet::enablePush'))
		{
			$this->enablePush($loop);
		}

		// Set up our WebSocket server for clients wanting real-time updates
		$webSock = new \React\Socket\Server($loop);
		$webSock->listen($this->option('port'), '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
		$webServer = new \Ratchet\Server\IoServer(
			new \Ratchet\Http\HttpServer(
				new \Ratchet\WebSocket\WsServer(
					new \Ratchet\Wamp\WampServer(
						$this->latchet
					)
				)
			), $webSock
		);



		if(\Config::get('latchet::allowFlash'))
		{
			$this->allowFlash($loop);
		}

		$this->info('Listening on port ' . $this->option('port'));
		$loop->run();
	}

	/**
	 * Allow Flash sockets to connect to our server.
	 * For this we have to listen on port 843 and return
	 * the flashpolicy
	 *
	 * @param React\EventLoop\StreamSelectLoop $loop
	 * @return void
	 */
	protected function allowFlash($loop)
	{
		// Allow Flash sockets (Internet Explorer) to connect to our app
		$flashSock = new \React\Socket\Server($loop);
		$flashSock->listen(\Config::get('latchet::flashPort'), '0.0.0.0');
		$policy = new \Ratchet\Server\FlashPolicy;
		$policy->addAllowedAccess('*', $this->option('port'));
		$webServer = new \Ratchet\Server\IoServer($policy, $flashSock);

		$this->info('Flash connection allowed');
	}

	/**
	 * Enable the option to push messages from
	 * the Server to the client
	 *
	 * @param React\EventLoop\StreamSelectLoop $loop
	 * @return void
	 */
	protected function enablePush($loop)
	{
		// Listen for the web server to make a ZeroMQ push after an ajax request
		$context = new \React\ZMQ\Context($loop);
		$pull = $context->getSocket(\ZMQ::SOCKET_PULL);
		$pull->bind('tcp://127.0.0.1:'.\Config::get('latchet::zmqPort')); // Binding to 127.0.0.1 means the only client that can connect is itself
		$pull->on('message', array($this->latchet, 'serverPublish'));

		$this->info('Push enabled');
	}


	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('port', 'p', InputOption::VALUE_OPTIONAL, 'The Port on which we listen for new connections', \Config::get('latchet::socketPort')),
		);
	}

}