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

		$routes['confirmar-cadastro'] = array(
			'route' => '/confirmar-cadastro',
			'controller' => 'HomeController',
			'action' => 'confirmarCadastro'
		);
		
		$routes['cadastro-confirmado'] = array(
			'route' => "/cadastro-confirmado",
			'controller' => 'HomeController',
			'action' => 'cadastroConfimado',
		);

		$routes['login'] = array(
			'route' => "/login",
			'controller' => 'HomeController',
			'action' => 'login',
		);

		$this->setRoutes($routes);
	}
}

?>