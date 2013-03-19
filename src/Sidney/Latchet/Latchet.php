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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Latchet implements WampServerInterface {

	/**
	 * The route collection instance.
	 *
	 * @var Symfony\Component\Routing\RouteCollection
	 */
	protected $routes;

	/**
	 * The inversion of control container instance.
	 *
	 * @var Illuminate\Container
	 */
	protected $container;

	public function __construct(Container $container = null)
	{
		$this->container = $container;

		$this->routes = new RouteCollection;
	}

	/**
	 * Allias for the createRoute function
	 *
	 * @param string $pattern
	 * @param string $controller
	 * @return void
	 */
	public function channel($pattern, $controller)
	{
		$this->createRoute($pattern, $controller);
	}

	/**
	 * Create and add a new route to the
	 * RouteCollection
	 *
	 * @param string $pattern
	 * @param string $controller
	 * @return void
	 */
	public function createRoute($pattern, $controller)
	{
		$route = new Route($pattern, array('_controller' => $this->getCallback($controller)));
		$this->routes->add($pattern, $route);
	}

	/**
	 * Dispatch the 'request'
	 *
	 * @param string $event
	 * @param array $variables
	 */
	protected function dispatch($event, $variables = array())
	{
		$route = $this->findRoute($variables);
		$route->run($event);
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
	 * Find a route in our RouteCollection
	 * and set all the necessary parameters
	 *
	 * @param array $variables
	 * @return Sidney\Latchet\Route
	 */
	protected function findRoute($variables)
	{
		if(array_key_exists('topic', $variables))
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
					throw new NotFoundHttpException("Requested Route not found");
				}
			}

			$route = $this->routes->get($parameters['_route']);

			$route->setWsParameters($variables);
			$route->setRequestParameters($parameters);

			return $route;
		}
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

		return new UrlMatcher($this->routes, $context);
	}

	public function onSubscribe(Conn $connection, $topic)
	{
		$this->dispatch('subscribe', compact('connection', 'topic'));
	}

	public function onPublish(Conn $connection, $topic, $message, array $exclude, array $eligible)
	{
		//$topic->broadcast($message);
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
		//$this->dispatch('subscribe', compact('connection'));
	}
	public function onClose(Conn $connection) {}
	public function onError(Conn $connection, \Exception $e)
	{
		//TODO: delegate events to laravel so we can
		//use the framework error handler
		var_dump($e->getMessage());
		$connection->close();
	}

}