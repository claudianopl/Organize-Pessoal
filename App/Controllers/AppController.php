<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {
	/**
	 * Função para validar acesso.
	 * A função checa se o cookie user que está contida a hash jwt, se tiver ele 
	 * retorna os dados do jwt para dentro da $data e verificamos se não aconteceu
	 * um roubo de cookie.
	 * @access public
	 * @return boolean
	 */
	public function checkJWT() {
		if(isset($_COOKIE['user'])) {
			$data = $this->decodeJWT($_COOKIE['user']);
			if($data->authenticate === md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) {
				return true;
			}
		}
		return false;
	}

	public function Index() {
		if($this->checkJWT()) {
			$this->render('index');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Receive() {
		if($this->checkJWT()) {
			$this->render('receive');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Expense() {
		if($this->checkJWT()) {
			$this->render('expense');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Tasks() {
		if($this->checkJWT()) {
			$this->render('tasks');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Fixed() {
		if($this->checkJWT()) {
			$this->render('fixed');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Wallet() {
		if($this->checkJWT()) {
			$this->render('wallet');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Profile() {
		if($this->checkJWT()) {
		$this->render('profile');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	/**
	 * Efetuar o logoff do usuário
	 * Função que faz a "destruição" do cookie responsável por manter o usuário 
	 * conectado na página do cliente.
	 * @access public
	 */
	public function Logoff() {
		unset($_COOKIE['user']);
		setcookie('user', null, -1);
		header('Location: /');
	}
	
}

?>