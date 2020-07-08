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

		/**
		 * Rota do back-end.
		 * Essas rotas são executadas no HomeController.
		 */

		/**
		 * Rota para criar novos usuários
		 */
		$routes['newUser'] = array(
			'route' => "/newUser",
			'controller' => 'HomeController',
			'action' => 'newUser',
		);

		
		/**
		 * Rota para efetuar login
		 */
		$routes['authenticateUser'] = array(
			'route' => "/authenticateUser",
			'controller' => 'HomeController',
			'action' => 'authenticateUser',
		);

		/**
		 * Rota para modificar senha
		 */
		$routes['changeTokenPassword'] = array(
			'route' => "/changeTokenPassword",
			'controller' => 'HomeController',
			'action' => 'changeTokenPassword',
		);



		/**
		 * Rota do back-end.
		 * Essas rotas são executadas no AppController.
		 */

		/**
		 * Rota para comunicação ajax da página /app
		 */
		$routes['dataApp'] = array(
			'route' => "/app/dataApp",
			'controller' => 'AppController',
			'action' => 'dataApp',
		);

		/**
		 * Rota para a seleção e apresentação das carteiras do usuário.
		 */
		$routes['userGetWallet'] = array(
			'route' => "/app/userSelectWallet",
			'controller' => 'AppController',
			'action' => 'userSelectWallet',
		);
		
		/**
		 * Rota para inserção de receitas.
		 */
		$routes['insertReceive'] = array(
			'route' => "/app/insertReceive",
			'controller' => 'AppController',
			'action' => 'insertReceive',
		);

		/**
		 * Rota para a filtrar as receitas do usuário.
		 */
		$routes['filterReceive'] = array(
			'route' => "/app/filterReceive",
			'controller' => 'AppController',
			'action' => 'filterReceive',
		);

		/**
		 * Rota para remover as receitas do usuário.
		 */
		$routes['removeReceived'] = array(
			'route' => "/app/removeReceived",
			'controller' => 'AppController',
			'action' => 'removeReceived',
		);

		/**
		 * Rota para atualizar as receitas do usuário.
		 */
		$routes['updateReceived'] = array(
			'route' => "/app/updateReceived",
			'controller' => 'AppController',
			'action' => 'updateReceived',
		);

		/**
		 * Rota para concluir as receitas do usuário.
		 */
		$routes['concludeReceived'] = array(
			'route' => "/app/concludeReceived",
			'controller' => 'AppController',
			'action' => 'concludeReceived',
		);

		/**
		 * Rota para inserir novas despesas do usuário.
		 */
		$routes['insertExpenses'] = array(
			'route' => "/app/insertExpenses",
			'controller' => 'AppController',
			'action' => 'insertExpenses',
		);
		
		/**
		 * Rota para remover despesas do usuário.
		 */
		$routes['expensesRemove'] = array(
			'route' => "/app/expensesRemove",
			'controller' => 'AppController',
			'action' => 'expensesRemove',
		);

		/**
		 * Rota para concluir despesas do usuário.
		 */
		$routes['expensesConclude'] = array(
			'route' => "/app/expensesConclude",
			'controller' => 'AppController',
			'action' => 'expensesConclude',
		);

		/**
		 * Rota para efetuar o logoff
		 */
		$routes['userLogoff'] = array(
			'route' => "/logoff",
			'controller' => 'AppController',
			'action' => 'logoff',
		);

		// Execução das routes
		$this->setRoutes($routes);
	}
}

?>