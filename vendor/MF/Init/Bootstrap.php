<?php

namespace MF\Init;

// Abstract não pode ser instânceada, apenas herdada

// Método abstract quer dizer que quando herdado pela classe filha deverá ser implementado na classe filha

abstract class Bootstrap {
	private $routes;

	abstract protected function initRoutes();

	public function __construct() {
		$this->initRoutes();
		$this->run($this->getUrl());
	}

	public function getRoutes() {
		return $this->routes;
	}

	public function setRoutes(array $routes) {
		$this->routes = $routes;
	}

	protected function run($url) {
		$notFound = True;
		foreach ($this->getRoutes() as $key => $route) {
			if($url == $route['route']) {
				$class = "App\\Controllers\\".ucfirst($route['controller']);

				$controller = new $class;
				
				$action = $route['action'];

				$controller->$action();

				$notFound = False;
				break;
			}
		}
		if($notFound == True) {
			$class = "App\\Controllers\\"."NoutfoundController";

			$controller = new $class;

			$action = 'index';
			$controller->$action();
		}
	}

	protected function getUrl() {
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}
}
?>