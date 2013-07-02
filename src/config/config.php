<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Socket Default Port
	|--------------------------------------------------------------------------
	|
	| Default port on which the React Socket Server will listen for incoming
	| connections. You can also define a port in the artisan command,
	| if nothing is set there, we'll use this port.
	|
	*/

	'socketPort' => 1111,

	/*
	|--------------------------------------------------------------------------
	| Enable Push Option
	|--------------------------------------------------------------------------
	|
	| Latchet gives you the possibility to easily push messages to
	| subscribed Topics. To be able to push messages, we need the
	| ZeroMQ Library (libzmq). It can be a little tricky to install the
	| library and the PECL extension. A lot of hosters won't
	| even allow you to install something so it's optional and you
	| can enable it here.
	|
	*/

	'enablePush' => false,

	/*
	|--------------------------------------------------------------------------
	| ZeroMQ Socket Default Port
	|--------------------------------------------------------------------------
	|
	| Port for the ZeroMQ connection. This is used so we can connect
	| to all Socket connections and broadcast messages from e.g an
	| Ajax Request.
	|
	*/

	'zmqPort' => 5555,

	/*
	|--------------------------------------------------------------------------
	| Allow Flash
	|--------------------------------------------------------------------------
	|
	| Allow legacy browsers to connect with the websocket polyfill
	| https://github.com/gimite/web-socket-js
	|
	*/

	'allowFlash' => true,

	/*
	|--------------------------------------------------------------------------
	| Flash Port
	|--------------------------------------------------------------------------
	|
	| If Flash is allowed and Websockets are not supported by the client
	| browser, you have to provide a Flash socket policy file for the
	| web-socket-js fallback.
	|
	| This is  automatically done by latchet. However, you have to set a port on which
	| this policy is located. By default, flash always starts looking for this
	| policy at port 843. You are free to set your own port here, if you are
	| not allowed to bind something to some of the lower ports.
	|
	| This will cause a connection delay of 2-3 seconds, and don't forget to
	| tell the client where the policy is located. In JS:
	| WebSocket.loadFlashPolicyFile("xmlsocket://myhost.com:61011");
	*/

	'flashPort' => 843,

);
