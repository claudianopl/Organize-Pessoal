<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

// Iniciando a sessão
session_start();

class AppController extends Action {
	public function checkSession() {
		if($_SESSION['authenticate'] == 'YES') {
			return true;
		}
		return false;
	}
	public function Index() {
		if($this->checkSession()) {
			$this->render('index');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Receive() {
		if($this->checkSession()) {
			$this->render('receive');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Expense() {
		if($this->checkSession()) {
			$this->render('expense');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Tasks() {
		if($this->checkSession()) {
			$this->render('tasks');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Fixed() {
		if($this->checkSession()) {
			$this->render('fixed');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Wallet() {
		if($this->checkSession()) {
			$this->render('wallet');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Profile() {
		if($this->checkSession()) {
		$this->render('profile');
		} else {
			header("Location: /entrar?e=0");
		}
	}
	
}

?>