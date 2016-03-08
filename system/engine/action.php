<?php

use \Newcart\System\Modification\System\Engine\Action as NewcartAction;

class Action {
	private $file;
	private $class;
	private $method;
	private $args = array();
    private $action;

	public function __construct($route, $args = array()) {

		$this->action = new NewcartAction($route, $args);

		$this->file = $this->action->getFile();
		$this->class = $this->action->getClass();
		$this->method = $this->action->getMethod();
		$this->args = $this->action->getArgs();

	}

	public function execute($registry) {
		return $this->action->execute($registry);
	}
}
