<?php namespace Sidney\Latchet;

abstract class BaseConnection {

	abstract function open($connection);

	abstract function close($connection);

	abstract function error($connection, $exception);

}