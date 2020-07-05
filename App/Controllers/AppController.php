<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

date_default_timezone_set("America/Sao_Paulo");

class AppController extends Action {
	
	public function Index() {
		if($this->checkJWT()) {
			$this->view->wallets = $this->userGetWallet();

			$this->render('index');
		} else {
			header("Location: /entrar?e=0");
		}
	}


	/**
	 * Renderizar layout receitas.
	 * Responável por verifricar se o usuário está conectado e renderizar a 
	 * página de receitas e adicionar os dados: carteiras, receitas, pagamentos 
	 * das receitas.
	 * @access public
	 */
	public function Receive() {
		if($this->checkJWT()) {
			$this->view->wallets = $this->userGetWallet();
			$this->view->receives = $this->receivedMonth();
			$this->view->payments = $this->sumReceived(date('Y-m-01'));

			$this->render('receive');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Expense() {
		if($this->checkJWT()) {
			$this->view->wallets = $this->userGetWallet();

			$this->render('expense');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Tasks() {
		if($this->checkJWT()) {
			$this->view->wallets = $this->userGetWallet();

			$this->render('tasks');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Fixed() {
		if($this->checkJWT()) {
			$this->view->wallets = $this->userGetWallet();

			$this->render('fixed');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Wallet() {
		if($this->checkJWT()) {
			$this->view->wallets = $this->userGetWallet();

			$this->render('wallet');
		} else {
			header("Location: /entrar?e=0");
		}
	}

	public function Profile() {
		if($this->checkJWT()) {
			$this->view->wallets = $this->userGetWallet();

			$this->render('profile');
		} else {
			header("Location: /entrar?e=0");
		}
	}


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


	/**
	 * Seleciona uma carteira automáticamente e retorna todas a carteiras do usuário.
	 * @access public
	 * @return array
	 */
	public function userGetWallet() {
		$id = $this->dataJWT()->id;

		$wallet = Container::getModel('Wallet');
		$wallet->__set('id_user', $id);
		$wallets = $wallet->getUserWallet();
		return $wallets;
	}

	public function userSelectWallet() {
		if(isset($_POST)) {
			$wallet = $_POST['wallet'];
			setcookie('userWallet', null, -1, '/');
			setcookie('userWallet', $wallet);
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
	 * Inserir receitas no banco.
	 * A função insere os dados das receitas.
	 * @access public
	 * @access public
	 * @return boolean
	 */
	public function insertReceive() {
		if(isset($_POST)) {
			$wallet = $_POST['receiveWallet'];
			$desc = $_POST['receiveDesc'];
			$value = $_POST['receiveValue'];
			$date = $_POST['receiveDate'];
			$category = $_POST['receiveCategory'];
			$enrollment = $_POST['receiveRepetition'];
			if($enrollment == 'Fixa') {
				$fixed = $_POST['receiveRepetitionFixed'];
			}
			if($enrollment == 'Parcelada') {
				$parcel = $_POST['receiveRepetitionParcel'];
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
				strtotime($date) <= strtotime(date('Y/m/d')) ? $dataReceive->__set('status', 1) : $dataReceive->__set('status', 0);
				$info['messege'] = 'success';
			}

			if(isset($parcel)) {
				if($parcel > 1 && $parcel <= 420) {
					strtotime($date) <= strtotime(date('Y/m/d')) ? $dataReceive->__set('status', 1) : $dataReceive->__set('status', 0);	
					$dataReceive->__set('parcel', $parcel);
					$dataReceive->__set('parcelPay', 1);

					$dataReceive->saveParcelReceived();
	
					for($i=2; $i <= $parcel; $i++) {
						$date = date('Y/m/d', strtotime("+1 month",strtotime($date)));
						strtotime($date) <= strtotime(date('Y/m/d')) ? $dataReceive->__set('status', 1) : $dataReceive->__set('status', 0);
						$dataReceive->__set('parcelPay', $i);
						$dataReceive->__set('date', $date);

						$dataReceive->saveParcelReceived();
					}
				}
				else {
					$dataReceive->__set('enrollment', 'Única');

					$dataReceive->saveParcelReceived();
				}
				$info['messege'] = 'success';
			}

			else {
				strtotime($date) <= strtotime(date('Y/m/d')) ? $dataReceive->__set('status', 1) : $dataReceive->__set('status', 0);
				$info['messege'] = 'success';
			}

			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}


	/**
	 * Retorna todas as receitas da carteira.
	 * A função retorna todas as receitas daquela carteira que foram criadas e 
	 * salvadas no banco de dados
	 * @access public
	 */
	public function receivedMonth() {
		$date = date("Y-m-01");
		$lastDate = date("Y-m-t");
		$received = Container::getModel('AppData');
		$received->__set('id_wallet', $_COOKIE['userWallet']);
		$received->__set('date', $date);
		$received->__set('lastDate', $lastDate);
		return $received->filterReceivedMonth();
	}


	/**
	 * A função faz a solicitação da somas das receitas a receber e recebido ao 
	 * banco de dados.
	 * @access public
	 * @return array
	 */
	public function sumReceived($date) {
		$dateExplode = explode('-', $date);
		$year = $dateExplode[0];
		$month = $dateExplode[1];

		$received = Container::getModel('AppData');
		$received->__set('date', $date);
		$received -> __set('lastDate', date("$year-$month-t"));
		$received->__set('id_wallet', $_COOKIE['userWallet']);

		$paymentData = $received->sumReceived();

		return $paymentData;
	}


	/**
	 * Filtra as receitas do usuário.
	 * A função é solicitada através do Ajax e faz a filtragem no banco de dados, 
	 * retornando apenas os dados requisitados pelo usuário.
	 * @access public
	 */
	public function filterReceive() {
		if(isset($_POST)) {

			/**
			 * Se todos os dados forem vazios, vamos retornar todos os dados que 
			 * estão pendentes até a atual data.
			 */
			if($_POST['date'] == '' && $_POST['status'] == '' && $_POST['category'] == '') {
				$received = Container::getModel('appData');
				$received->__set('id_wallet', $_COOKIE['userWallet']);
				$dataReceived['received'] = $received->filterReceiveAll();
				$dataReceived['sumReceived'] = $received->sumReceivedAll();
			}

			/**
			 * Caso contrário, vamos retornar os dados que foram filtrados no banco 
			 * de dados.
			 */
			else {
				$received = Container::getModel('AppData');
				$date = $_POST['date'];
				if($date != '') {
					$date = explode('/', $date);
					$startDate = $date[1].'-'.$date[0].'-01';
					$lastDate = date($date[1].'-'.$date[0].'-t');
					$received->__set('lastDate', $lastDate);
					$received->__set('date', $startDate);
				}
				else {
					$received->__set('lastDate', '9999-00-00');
					$received->__set('date', '0000-00-00');
				}

				$status = $_POST['status'];
				if($status == '') {
					$received->__set('status', '');
				} else {
					$received->__set('status', $status);
				}

				$category = $_POST['category'];
				if($category == '') {
					$received->__set('category', '');
				} else {
					$received->__set('category', $category);
				}
				
				$received->__set('id_wallet',$_COOKIE['userWallet']);
				
				$dataReceived['received'] = $received->filterReceive();
				$dataReceived['sumReceived'] = $this->sumReceived($startDate);

			}

			print_r(json_encode($dataReceived, JSON_UNESCAPED_UNICODE));
		}
	}


	/**
	 * Função para solicitamos a remoção da receita no banco de dados.
	 * @access public
	 */
	public function removeReceived() {
		if(isset($_POST)) {
			$id = $_POST['id'];
			$received = Container::getModel('AppData');
			$received->__set('id', $id);

			if($received->removeReceived()) {
				$info['messege'] = 'success';
			}
			else {
				$info['messege'] = 'error';
			}

			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));

		}
	}
	
	
	/**
	 * Função para solicitar a conclusão da receita ao banco de dados.
	 * @access public
	 */
	public function concludeReceived() {
		if(isset($_POST)) {
			$id = $_POST['id'];
			$received = Container::getModel('AppData');
			$received->__set('id', $id);

			if($received->concludeReceived()) {
				$info['messege'] = 'success';
			}
			else {
				$info['messege'] = 'error';
			}

			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}


	/**
	 * Efetuar o logoff do usuário
	 * Função que faz a "destruição" do cookie responsável por manter o usuário 
	 * conectado na página do cliente.
	 * @access public
	 */
	public function Logoff() {
		unset($_COOKIE['userWallet']);
		setcookie('userWallet', null, -1, '/');
		unset($_COOKIE['user']);
		setcookie('user', null, -1, '/');
		header('Location: /');
	}
}

?>