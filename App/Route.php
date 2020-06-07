<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		/*
		* Routes do controller principal Home
		*/
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

		$routes['entrar'] = array(
			'route' => "/entrar",
			'controller' => 'HomeController',
			'action' => 'login',
		);

		$routes['cadastrar'] = array(
			'route' => "/cadastre-se",
			'controller' => 'HomeController',
			'action' => 'singup',
		);

		/*
		* Routes user do controller App
		*/

		$routes['app'] = array(
			'route' => "/app",
			'controller' => 'AppController',
			'action' => 'index',
		);


		// Execução das routes
		$this->setRoutes($routes);
	}
}

?>