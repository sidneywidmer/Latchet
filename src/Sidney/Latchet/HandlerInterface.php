<?php namespace Sidney\Latchet;

interface HandlerInterface {

	public function run($event);

	public function setWsParameters($variables);

}