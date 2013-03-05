<?php namespace Sidney\Latchet;

use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface as Conn;
use ReflectionClass;

class Latchet implements WampServerInterface {

	private $channels;

	/**
	 * Add a channel. Our websocket will listen on
	 * incomming requests on this channel.
	 *
	 * @param  string  $pattern
	 * @param  mixed   $action
	 * @return void
	 */
	public function addChannel($pattern, $controller)
	{
		$this->channels[$pattern]['controller'] = $controller;
	}

	/**
	 * Check if it's a valid channel
	 *
	 * @param obj $channel
	 * @return boolean
	 */
	private function validChannel($channel)
	{
		return array_key_exists($channel->getId(), $this->channels);
	}

	private function getController($channel)
	{
		$channel = $this->channels[$channel->getId()];
		if(array_key_exists('controller_obj', $channel) AND is_object($channel['controller_obj']))
		{
			return $channel['controller_obj'];
		}
		else
		{
			$classname = $channel['controller'];
			$channel['controller_obj'] = new $classname;
			return $channel['controller_obj'];
		}
	}

	public function onPublish(Conn $conn, $channel, $message, array $exclude, array $eligible)
	{
		echo "New message boradcasted in Channel " . $channel->getId();
		$channel->broadcast($message);
	}

	public function onCall(Conn $conn, $id, $channel, array $params) {
		$conn->callError($id, $channel, 'RPC not allowed');
	}

	// No need to anything, since WampServer adds and removes subscribers to channels automatically
	public function onSubscribe(Conn $conn, $channel)
	{
		if($this->validChannel($channel))
		{
			$controller = $this->getController($channel);
			$controller->onSubscribe($conn, $channel);
		}
	}
	public function onUnSubscribe(Conn $conn, $channel) {}

	public function onOpen(Conn $conn) {}
	public function onClose(Conn $conn) {}
	public function onError(Conn $conn, \Exception $e)
	{
		$conn->close();
	}

}