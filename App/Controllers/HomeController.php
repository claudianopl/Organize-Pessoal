<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

// Models


class HomeController extends Action {
	
	public function index() {
		$this->render('index');
	}
	
	public function sobre() {
		$this->render('sobre');
	}

	public function confirmRegister() {
		$this->render('confirmRegister');
	}

	public function registerConfirmed($dados=null) {
		$this->render('registerConfirmed');
	}

	public function login() {
		$this->render('login');
	}

	public function singup() {
		$this->render('singup');
	}

	
}

?>