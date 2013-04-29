<?php namespace Sidney\Latchet\Handlers;

class ConnectionEventHandler implements HandlerInterface {

	/**
	 * different Parameters set by ratchet e.g connection, topic
	 *
	 * @var array
	 */
	protected $wsParameters;

	/**
	 * Instance of the Controller we registered
	 * which handles the Ratchet events
	 *
	 * @var object
	 */
	protected $controller;

	public function __construct($controller)
	{
		$this->controller = $controller;
	}

	/**
	 * Execute the handler
	 *
	 * @param string $event
	 * @return void
	 */
	public function run($event)
	{
		$this->callController($event);
	}

	/**
	 * Set the Ratchet variables
	 *
	 * @param  array  $parameters
	 * @return void
	 */
	public function setWsParameters($parameters)
	{
		$this->wsParameters = $parameters;
	}

	/**
	 * Call the registered controller
	 * with the right event
	 *
	 * @param string $event (either open or close)
	 */
	protected function callController($event)
	{
		$parameters = $this->wsParameters;

		return call_user_func_array(array($this->controller, $event), $parameters);
	}
}