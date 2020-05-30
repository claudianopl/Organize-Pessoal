<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'HomeController',
			'action' => 'index'
		);

		$routes['sobre'] = array(
			'route' => '/sobre',
			'controller' => 'HomeController',
			'action' => 'sobre'
		);

		$routes['cadastre-se'] = array(
			'route' => '/cadastrar',
			'controller' => 'HomeController',
			'action' => 'cadastrar'
		);

		$routes['login'] = array(
			'route' => '/login',
			'controller' => 'HomeController',
			'action' => 'login'
		);

		$this->setRoutes($routes);
	}
}

?>