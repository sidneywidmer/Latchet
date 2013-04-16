<?php namespace Sidney\Latchet;

class LatchetPusher {
	/**
	 * A lookup of all the topics clients have subscribed to
	 *
	 * @var array
	 */
	protected $subscribedTopics = array();

	/**
	 * add a new topic (subscriber) to our lookup array
	 *
	 * @param Ratchet\Wamp\Connection
	 * @param Ratchet\Wamp\Topic
	 * @return void
	 */
	public function addSubscriber($conneciton, $topic)
	{
		if (!array_key_exists($topic->getId(), $this->subscribedTopics)) {
			$this->subscribedTopics[$topic->getId()] = $topic;
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
		if (!array_key_exists($message['topic'], $this->subscribedTopics)) {
			return;
		}

		$topic = $this->subscribedTopics[$message['topic']];

		$topic->broadcast($message);
	}

}