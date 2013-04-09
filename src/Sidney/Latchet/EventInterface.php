<?php namespace Sidney\Latchet;

interface EventInterface {

	public function run($event);

	public function setWsParameters($variables);

}