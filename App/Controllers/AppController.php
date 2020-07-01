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

	/**
	 * Função para retornar os dados JWT.
	 * @access public
	 * @return object
	 */
	public function dataJWT() {
		if(isset($_COOKIE['user'])) {
			return $this->decodeJWT($_COOKIE['user']);
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Index() {
		if($this->checkJWT()) {
			$this->render('index');
		} else {
			header("Location: /entrar?e=0");
		}
	}
	/**
	 * Renderizar layout receitas.
	 * Responável por verifricar se o usuário está conectado e renderizar a 
	 * página de receitas e adicionar os dados da carteira no modal de inserção.
	 * @access public
	 */
	public function Receive() {
		if($this->checkJWT()) {
			$data = $this->dataJWT()->id;
			$wallet = Container::getModel('wallet');
			$wallet->__set('id_user', $data);
			$wallets = $wallet->getUserWallet();

			$this->view->wallets = $wallets;
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
	 * Retornar os dados ao usuário.
	 * A função se comunica por requisições ajax, vai ser responsável por levar os
	 * dados gerais, para a página /app.
	 * @access public
	 */
	public function dateApp() {
		$info = array();
		$user = $this->dataJWT();

		$info['userName'] = $user->name.' '.$user->surname;
		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Inserir dados no banco.
	 * A função insere os dados das receitas, despesas, tarefas no banco de dados, 
	 * fazendo a verificação do $_GET para saber em qual tabela inserir.
	 * @access public
	 */
	public function insertData() {
		$info = array();
		if(isset($_GET)) {
			$get = $_GET['location'];
		} 
		if(isset($_POST)) {
			$post = $_POST;
			if($get == 'receive') {
				if($this->insertReceive($post)) {
					$info['messege'] = 'success';
				}
			}
		}


		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}


	/**
	 * Inserir dados na tb_receive.
	 * A função insere os dados na tabela de receitas. 
	 */
	public function insertReceive($data) {
		$wallet = $data['receiveWallet'];
		$desc = $data['receiveDesc'];
		$value = $data['receiveValue'];
		$date = $data['receiveDate'];
		$category = $data['receiveCategory'];
		$enrollment = $data['receiveRepetition'];
		if($enrollment == 'Fixa') {
			$fixed = $data['receiveRepetitionFixed'];
		}
		if($enrollment == 'Parcelada') {
			$parcel = $data['receiveRepetitionParcel'];
		}

		$dataReceive = Container::getModel('AppData');
		$dataReceive->__set('id_wallet', $wallet);
		$dataReceive->__set('status', 0);
		$dataReceive->__set('description', $desc);
		$dataReceive->__set('value', $value);
		$dataReceive->__set('date', $date);
		$dataReceive->__set('category', $category);
		$dataReceive->__set('enrollment', $enrollment);
		if(isset($fixed)) {
			$dataReceive->__set('statusParcelFixed', $fixed);
		}
		if(isset($parcel)) {
			$dataReceive->__set('parcel', $parcel);
		}	

		return($dataReceive->saveReceive());

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