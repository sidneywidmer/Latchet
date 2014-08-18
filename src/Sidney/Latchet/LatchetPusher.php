<?php namespace Sidney\Latchet;

class LatchetPusher {
	/**
	 * A lookup of all the topics clients have subscribed to
	 *
	 * @var array
	 */
	protected $subscribers = array();
	
	function __get($name)
	{
		if ($name == 'subscribedTopics') {
			return $this->subscribers;
		}
	}

	function __set($name, $value)
	{
		if ($name == 'subscribedTopics') {
			$this->subscribers = $value;
		}
	}

	/**
	 * add a new topic (subscriber) to our lookup array
	 *
	 * @param Ratchet\Wamp\Connection
	 * @param Ratchet\Wamp\Topic
	 * @return void
	 */
	public function addSubscriber($conneciton, $topic)
	{
		if (!array_key_exists($topic->getId(), $this->subscribers)) {
			$this->subscribers[$topic->getId()] = ['topic' => $topic, 'connections' => []];
		}
		$this->subscribers[$topic->getId()]['connections'][] = $conneciton;
	}
	
	/**
	 * Reset subscriber after the connection is closed
	 */
	public function removeSubscriber($conneciton)
	{
		foreach ($this->subscribers as $topicId => &$subscribe) {
			// remove connection from connections
			if (($key = array_search($conneciton, $subscribe['connections'])) !== false) {
				unset($subscribe['connections'][$key]);
			}
			// remove topic if connections is empty
			if (!$subscribe['connections']) {
				unset($this->subscribers[$topicId]);
			}
		}
	}

	/**
	 * json we recieve from ZerMQ
	 *
	 * @param string $message
	 * @return void
	 */
	public function serverPublish($message)
	{
		$message = json_decode($message, true);
		// If the lookup topic object isn't set there is no one to publish to
		if (!array_key_exists($message['topic'], $this->subscribers)) {
			return;
		}
		$topicId    = $message['topic'];
		$subscriber = $this->subscribers[$topicId];
		$subscriber['topic']->broadcast($message);
	}

}
