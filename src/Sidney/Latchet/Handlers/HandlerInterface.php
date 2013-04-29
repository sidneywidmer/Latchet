<?php namespace Sidney\Latchet\Handlers;

interface HandlerInterface {

	public function run($event);

	public function setWsParameters($variables);

}