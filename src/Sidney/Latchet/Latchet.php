<?php namespace Sidney\Latchet;

use Illuminate\Container\Container;

use Ratchet\Wamp\WampServerInterface;
use Ratchet\Wamp\Topic;
use Ratchet\ConnectionInterface as Conn;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Latchet implements WampServerInterface {

	/**
	 * The route collection instance.
	 * We're using the Symfony component to use custom parameters
	 * in a topicname
	 *
	 * @var Symfony\Component\Routing\RouteCollection
	 */
	protected $topicEventHandlers;

	/**
	 * Instance of the ConnectionEventHandler.
	 * We'll use it for the Ratchet open, close and error events
	 * @var ConnectionEventHandler instance
	 */
	protected $connectionEventHandler;

	/**
	 * The inversion of control container instance.
	 *
	 * @var Illuminate\Container
	 */
	protected $container;

	public function __construct(Container $container = null)
	{
		$this->container = $container;

		$this->topicEventHandlers = new RouteCollection;
	}

	/**
	 * Create and add a new handler to the
	 * RouteCollection a.k.a topicEventHandlers
	 *
	 * @param string $pattern
	 * @param string $controller
	 * @return void
	 */
	public function topic($pattern, $controller)
	{
		if(is_subclass_of($controller, '\Sidney\Latchet\BaseTopic'))
		{
			$topicEventHandler = new TopicEventHandler($pattern, array('_controller' => $this->getCallback($controller)));
			$this->topicEventHandlers->add($pattern, $topicEventHandler);
		}
		else
		{
			throw new LatchetException($controller . " has to extend BaseTopic");
		}
	}

	/**
	 * Create a new connection handler instance
	 *
	 * @param string $controller
	 * @return void
	 */
	public function connection($controller)
	{
		if(is_subclass_of($controller, '\Sidney\Latchet\BaseConnection'))
		{
			$this->connectionEventHandler = new ConnectionEventHandler($this->getCallback($controller));
		}
		else
		{
			throw new LatchetException($controller . " has to extend BaseConnection");
		}
	}

	/**
	 * Dispatch the 'request'
	 *
	 * @param string $event
	 * @param array $variables
	 */
	protected function dispatch($event, $variables = array())
	{
		$eventHandler = $this->findEventHandler($variables);
		$eventHandler->run($event);
	}

	/**
	 * Create instance of the given Controller
	 *
	 * @param  string $controller
	 * @return Object
	 */
	protected function getCallback($controller)
	{
		//TODO: check if instance of BaseChannel
		$ioc = $this->container;
		$instance = $ioc->make($controller);

		return $instance;
	}

	/**
	 * Find a eventHandler and set all the necessary parameters.
	 * This can either be a topic or a connection eventhandler
	 *
	 * @param array $variables
	 * @return mixed (EventInterface instance)
	 */
	protected function findEventHandler($variables)
	{
		if(array_key_exists('topic', $variables))
		{
			$eventHandler = $this->findTopicEvent($variables);
		}
		else
		{
			$eventHandler = $this->connectionEventHandler;
			$eventHandler->setWsParameters($variables);
		}

		return $eventHandler;
	}

	/**
	 * Find and return a TopicEvent in our Symfony RouteCollection
	 *
	 * @param array $variables
	 * @return Sidne\Latchet\TopicEventHandler
	 */
	protected function findTopicEvent($variables)
	{
		$topicName = $this->getTopicName($variables['topic']);

		try
		{
			$parameters = $this->getUrlMatcher($topicName)->match('/'.$topicName);
		}
		catch (ExceptionInterface $e)
		{
			if ($e instanceof ResourceNotFoundException)
			{
				throw new LatchetException("Requested Channel not found");
			}
		}

		$eventHandler = $this->topicEventHandlers->get($parameters['_route']);

		$eventHandler->setWsParameters($variables);
		$eventHandler->setRequestParameters($parameters);

		return $eventHandler;
	}

	/**
	 * Get the name of a topic/channel
	 * just for convenience
	 *
	 * @param Ratchet\Wamp\Topic $topic
	 * @return string
	 */
	protected function getTopicName(Topic $topic)
	{
		return $topic->getId();
	}

	/**
	 * Create a new URL matcher instance.
	 *
	 * @param string $topic
	 * @return Symfony\Component\Routing\Matcher\UrlMatcher
	 */
	protected function getUrlMatcher($topicName)
	{
		$context = new RequestContext($topicName);

		return new UrlMatcher($this->topicEventHandlers, $context);
	}

	//possible actions
	//array('subscribe', 'publish', 'call', 'unsubscribe', 'open', 'close', 'error')
	public function onSubscribe(Conn $connection, $topic)
	{
		$this->dispatch('subscribe', compact('connection', 'topic'));
	}

	public function onPublish(Conn $connection, $topic, $message, array $exclude, array $eligible)
	{
		$this->dispatch('publish', compact('connection', 'topic', 'message', 'exclude', 'eligible'));
	}

	public function onCall(Conn $connection, $id, $topic, array $params)
	{
		$this->dispatch('call', compact('connection', 'id', 'topic', 'params'));
	}

	public function onUnSubscribe(Conn $connection, $topic)
	{
		$this->dispatch('unsubscribe', compact('connection', 'topic'));
	}

	public function onOpen(Conn $connection)
	{
		$this->dispatch('open', compact('connection'));
	}

	public function onClose(Conn $connection)
	{
		$this->dispatch('close', compact('connection'));
	}

	public function onError(Conn $connection, \Exception $exception)
	{
		$this->dispatch('error', compact('connection', 'exception'));
	}

}