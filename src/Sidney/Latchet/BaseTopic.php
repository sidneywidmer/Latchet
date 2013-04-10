<?php namespace Sidney\Latchet;

abstract class BaseTopic {

	abstract function subscribe($connection, $topic);

	abstract function publish($connection, $topic, $message, array $exclude, array $eligible);

	abstract function call($connection, $id, $topic, array $params);

	abstract function unsubscribe($connection, $topic);

	/**
	 * Broadcast message to clients
	 *
	 * @param Ratchet\Wamp\Topic $topic
	 * @param mixed $msg
	 * @param array $exclude
	 * @param array $eligible
	 * @return void
	 */
	protected function broadcast($topic, $msg, $exclude = array(), $eligible = array()) {
		if(count($exclude) > 0)
		{
			$this->broadcastExclude($topic, $msg, $exclude);
		}
		elseif (count($eligible) > 0)
		{
			$this->broadcastEligible($topic, $msg, $eligible);
		}
		else
		{
			$topic->broadcast($msg);
		}
	}

	/**
	 * Broadcast message only to clients which
	 * are not in the exclude array (blacklist)
	 *
	 * @param Ratchet\Wamp\Topic $topic
	 * @param mixed $msg
	 * @param array $exclude
	 * @return void
	 */
	protected function broadcastExclude($topic, $msg, $exclude)
	{
		foreach ($topic->getIterator() as $client)
		{
			if (!in_array($client->WAMP->sessionId, $exclude))
			{
				$client->event($topic, $msg);
			}
		}
	}

	/**
	 * Broadcast message only to clients which
	 * are in the eligible array (whitelist)
	 *
	 * @param Ratchet\Wamp\Topic $topic
	 * @param mixed $msg
	 * @param array $eligible
	 * @return void
	 */
	protected function broadcastEligible($topic, $msg, $eligible)
	{
		foreach ($topic->getIterator() as $client)
		{
			if (in_array($client->WAMP->sessionId, $eligible))
			{
				$client->event($topic, $msg);
			}
		}
	}

}