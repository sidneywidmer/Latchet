<?php namespace Sidney\Latchet;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Latchet implements WampServerInterface{

	protected $channels = array();

	// public function addChannel($name, $channel)
	// {
	// 	if (!array_key_exists($name, $this->channels)) {
	// 		$this->channels[$name] = with(new $channel)->setName($name);
	// 	}
	// }

	public function publish($args)
	{
		//update with list function
		$channel = $args['channel'];
		$msg = $args['message'];

		// If the lookup topic object isn't set there is no one to publish to
		if (!array_key_exists($channel, $this->channels)) {
			return;
		}

		$channel = $this->channels[$channel];

		// re-send the data to all the clients subscribed to that category
		$channel->broadcast($msg);
	}

	public function onSubscribe(ConnectionInterface $conn, $channel)
	{
		if (!array_key_exists($channel->getId(), $this->channels)) {
			$this->channels[$channel->getId()] = $channel;
		}
	}

	public function onUnSubscribe(ConnectionInterface $conn, $topic)
	{

	}

	public function onOpen(ConnectionInterface $conn)
	{

	}

	public function onClose(ConnectionInterface $conn)
	{

	}

	public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
	{
		// In this application if clients send data it's because the user hacked around in console
		$conn->callError($id, $topic, 'You are not allowed to make calls')->close();
	}
	public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
	{
		// In this application if clients send data it's because the user hacked around in console
		$conn->close();
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{

	}

}