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

		$routes['about'] = array(
			'route' => '/sobre',
			'controller' => 'HomeController',
			'action' => 'about'
		);

		$routes['login'] = array(
			'route' => "/entrar",
			'controller' => 'HomeController',
			'action' => 'login',
		);

		$routes['resetPassword'] = array(
			'route' => "/redefinir",
			'controller' => 'HomeController',
			'action' => 'resetPassword',
		);

		$routes['singup'] = array(
			'route' => "/cadastre-se",
			'controller' => 'HomeController',
			'action' => 'singup',
		);

		$routes['confirm-register'] = array(
			'route' => '/confirmar-cadastro',
			'controller' => 'HomeController',
			'action' => 'confirmRegister'
		);
		
		$routes['register-confirmed'] = array(
			'route' => "/cadastro-confirmado",
			'controller' => 'HomeController',
			'action' => 'registerConfirmed',
		);

		/*
		* Routes user do controller App
		*/

		$routes['app'] = array(
			'route' => "/app",
			'controller' => 'AppController',
			'action' => 'index',
		);

		$routes['appReceive'] = array(
			'route' => "/app/receitas",
			'controller' => 'AppController',
			'action' => 'receive',
		);

		$routes['appExpense'] = array(
			'route' => "/app/despesas",
			'controller' => 'AppController',
			'action' => 'expense',
		);

		$routes['appTasks'] = array(
			'route' => "/app/tarefas",
			'controller' => 'AppController',
			'action' => 'tasks',
		);

		$routes['appFixed'] = array(
			'route' => "/app/fixas",
			'controller' => 'AppController',
			'action' => 'fixed',
		);

		$routes['appWallet'] = array(
			'route' => "/app/carteiras",
			'controller' => 'AppController',
			'action' => 'wallet',
		);

		$routes['appProfile'] = array(
			'route' => "/app/perfil",
			'controller' => 'AppController',
			'action' => 'profile',
		);

		/*
		* Routes back-end
		*/

		// Criar novo usuário
		$routes['newUser'] = array(
			'route' => "/newUser",
			'controller' => 'HomeController',
			'action' => 'newUser',
		);

		// Autenticação do usuário na hora de efeutar login
		$routes['authenticateUser'] = array(
			'route' => "/authenticateUser",
			'controller' => 'HomeController',
			'action' => 'authenticateUser',
		);

		// Envio de email para trocar senha
		$routes['changeTokenPassword'] = array(
			'route' => "/changeTokenPassword",
			'controller' => 'HomeController',
			'action' => 'changeTokenPassword',
		);

		// Trocar de senha
		$routes['changePassword'] = array(
			'route' => "/changePassword",
			'controller' => 'HomeController',
			'action' => 'changePassword',
		);

		// Execução das routes
		$this->setRoutes($routes);
	}
}

?>