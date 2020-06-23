<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

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

	public function newUser() {
		$name = $_POST['name'];
		$surname = $_POST['surname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		// Para confirmação de email.
		$tokenEmail = md5(time().rand(0,9999).rand(0,9999));

		// Criando conexão com o banco de dados e se comunicando com o modal.
		$newUser = Container::getModel('User');

		// Salvando os dados do novo usuário no modal user para serem verificados.
		$newUser->__set('user_name', $name);
		$newUser->__set('user_surname', $surname);
		$newUser->__set('user_email', $email);
		$newUser->__set('user_password', $password);
		$newUser->__set('user_confirm', $tokenEmail);
		
		
		// Verificando se podemos salvar no banco de dados
		if($newUser->validateUser() && count($newUser->getUserEmail()) == 0) {
			//$newUser->saveUser();
			echo ('success');
			//header('Location:/confirmar-cadastro');
			$mail = mail('claudianoplima@hotmail.com', 'temaTeste', 'DescriçãoTeste', 
			'From: organizepessoal@organizepessoal.com');
		}
		else {
			echo ('Esse email já se encontra cadastrado!');
		}
		
	}
	
}

?>