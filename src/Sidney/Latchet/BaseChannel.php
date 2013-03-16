<?php namespace Sidney\Latchet;

use Ratchet\ConnectionInterface as Conn;

class BaseChannel extends \BaseController {

	private $router;

	public function __construct()
	{
		//$this->router = app()->make('router');
		$currentroute = \Route::getCurrentRoute();
		$params = $currentroute->getOption('_wsparams');
		var_dump($params);
		// $this->connection = $params['connection'];
	}

	public function onPublish(Conn $conn, $topic, $event, array $exclude = array(), array $eligible = array()) {
		$topic->broadcast($event);
	}

	public function onCall(Conn $conn, $id, $topic, array $params) {

	}

	public function onOpen(Conn $conn) {
	}

	public function onClose(Conn $conn) {
	}

	public function onSubscribe($channel) {
		//echo $this->connection->getid();
		echo"subsccr";
	}

	public function onUnSubscribe(Conn $conn, $topic) {
	}

	public function onError(Conn $conn, \Exception $e) {
	}

}