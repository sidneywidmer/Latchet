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

);
