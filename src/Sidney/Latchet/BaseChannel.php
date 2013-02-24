<?php namespace Sidney\Latchet;

// use Ratchet\ConnectionInterface;
// use Ratchet\Wamp\WampServerInterface;

// class BaseChannel implements WampServerInterface {

// 	protected $name;

// 	public function setName($name)
// 	{
// 		$this->name = $name;
// 	}

// 	public function onSubscribe(ConnectionInterface $conn, $topic)
// 	{

// 	}

// 	public function onUnSubscribe(ConnectionInterface $conn, $topic)
// 	{

// 	}

// 	public function onOpen(ConnectionInterface $conn)
// 	{

// 	}

// 	public function onClose(ConnectionInterface $conn)
// 	{

// 	}

// 	public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
// 	{
// 		// In this application if clients send data it's because the user hacked around in console
// 		$conn->callError($id, $topic, 'You are not allowed to make calls')->close();
// 	}
// 	public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
// 	{
// 		// In this application if clients send data it's because the user hacked around in console
// 		$conn->close();
// 	}

// 	public function onError(ConnectionInterface $conn, \Exception $e)
// 	{

// 	}

// }