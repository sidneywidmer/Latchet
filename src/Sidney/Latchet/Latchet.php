<?php namespace Sidney\Latchet;

use Ratchet\Wamp\WampServerInterface;
use Ratchet\Wamp\Topic;
use Ratchet\ConnectionInterface as Conn;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Latchet implements WampServerInterface {

	/**
	 * The route collection instance.
	 *
	 * @var Symfony\Component\Routing\RouteCollection
	 */
	protected $routes;

	public function __construct()
	{
		$this->routes = new RouteCollection;
	}

	public function channel($pattern, $action)
	{
		$this->createRoute($pattern, $action);
	}

	public function createRoute($pattern, $action)
	{
		$route = new Route($pattern, array('_controller' => $action));
		$this->routes->add($pattern, $route);
	}

	private function dispatch($action, $variables)
	{
		$parameters = $this->getUrlMatcher($this->getTopicName($variables['topic']))->match('/'.$this->getTopicName($variables['topic']));
		var_dump($parameters);
		//$route = $this->routes->get($parameters['_route']);

	}

	private function getTopicName(Topic $topic)
	{
		return $topic->getId();
	}

	/**
	 * Create a new URL matcher instance.
	 *
	 * @return Symfony\Component\Routing\Matcher\UrlMatcher
	 */
	protected function getUrlMatcher($topic)
	{
		$context = new RequestContext($topic);

		return new UrlMatcher($this->routes, $context);
	}

	// No need to anything, since WampServer adds and removes subscribers to channels automatically
	public function onSubscribe(Conn $conn, $topic)
	{
		//var_dump($conn->WebSocket);
		$variables = array(
			'connection' => $conn,
			'topic' => $topic
		);

		$this->dispatch('subscribe', $variables);
	}

	public function onOpen(Conn $conn){}

	public function onPublish(Conn $conn, $channel, $message, array $exclude, array $eligible)
	{
		echo "New message boradcasted in Channel " . $channel->getId();
		$channel->broadcast($message);
	}

	public function onCall(Conn $conn, $id, $channel, array $params) {
		//$conn->callError($id, $channel, 'RPC not allowed');
		//return $conn->callResult($id, array('id' => $roomId, 'display' => $topic));
	}

	public function onUnSubscribe(Conn $conn, $channel) {}

	public function onClose(Conn $conn) {}
	public function onError(Conn $conn, \Exception $e)
	{
		//var_dump($e);
		$conn->close();
	}

}