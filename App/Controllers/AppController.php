<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

date_default_timezone_set("America/Sao_Paulo");

class AppController extends Action 
{
	/**
	 * Função para validar acesso.
	 * A função checa se o cookie user que está contida a hash jwt, se tiver ele 
	 * retorna os dados do jwt para dentro da $data e verificamos se não aconteceu
	 * um roubo de cookie.
	 * @access public
	 * @return boolean
	 */
	public function checkJWT() 
	{
		if(isset($_COOKIE['user'])) {
			$data = $this->decodeJWT($_COOKIE['user']);
			if($data->authenticate === md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) 
			{
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
	public function dataJWT() 
	{
		if(isset($_COOKIE['user'])) 
		{
			return $this->decodeJWT($_COOKIE['user']);
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}



	public function Index() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();

			$this->render('index');
		} else 
		{
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
	public function Receive() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();
			$this->view->receives = $this->receivedMonth();
			$this->view->payments = $this->sumMonthReceivedAndExpense(date('Y-m-01'), 'Received');

			$this->render('receive');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	/**
	 * Renderizar layout despesas.
	 * Responável por verifricar se o usuário está conectado e renderizar a 
	 * página de despesas e adicionar os dados: carteiras, despesas, pagamentos 
	 * das despesas.
	 * @access public
	 */
	public function Expense() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();
			$this->view->expenses = $this->expensesMonth();
			$this->view->payments = $this->sumMonthReceivedAndExpense(date('Y-m-01'), 'Expense');
			print_r($this->view->payments);

			$this->render('expense');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	public function Tasks() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();

			$this->render('tasks');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	public function Fixed() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();

			$this->render('fixed');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	public function Wallet() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();

			$this->render('wallet');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	public function Profile() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();

			$this->render('profile');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	/**
	 * Seleciona uma carteira automáticamente e retorna todas a carteiras do usuário.
	 * @access public
	 * @return array
	 */
	public function userGetWallet() 
	{
		$id = $this->dataJWT()->id;

		$wallet = Container::getModel('Wallet');
		$wallet->__set('id_user', $id);
		$wallets = $wallet->getUserWallet();
		return $wallets;
	}

	public function userSelectWallet() 
	{
		if(isset($_POST)) 
		{
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
	 * @return array
	 */
	public function dataApp() 
	{
		$info = array();
		$user = $this->dataJWT();

		$info['userName'] = $user->name.' '.$user->surname;
		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}




	/**
	 * A função insere novas receitas e despesas.
	 * @access public
	 * @param array $data que contém as informações do $_POST.
	 * @param string $model que contém qual o model a ser executado.
	 */
	public function insertReceiveAndExpenses($data, $model)
	{
		$wallet = $data['Wallet'];
		$desc = $data['Desc'];
		$value = $data['Value'];
		$date = $data['Date'];
		$category = $data['Category'];
		$enrollment = $data['Repetition'];
		if($enrollment == 'Fixa') 
		{
			$fixed = $data['RepetitionFixed'];
		}
		if($enrollment == 'Parcelada') 
		{
			$parcel = $data['RepetitionParcel'];
		}

		$dataInsert = Container::getModel($model);
		$dataInsert->__set('id_wallet', $wallet);
		$dataInsert->__set('status', 0);
		$dataInsert->__set('description', $desc);
		$dataInsert->__set('value', $value);
		$dataInsert->__set('date', $date);
		$dataInsert->__set('category', $category);
		$dataInsert->__set('enrollment', $enrollment);

		if(isset($fixed))
		{
			$dataInsert->__set('statusParcelFixed', $fixed);
			$lastDate = date("Y-m-d");

			$dateVerification = new \DateTime($date);
			$lastDateVerification = new \DateTime($lastDate);
			$diff = $dateVerification->diff($lastDateVerification);
			$diff = $diff->m + ($diff->y * 12);

			/**
			 * Verificamos a diferença de ano.
			 * Fazemos isso para que o usuário não insira um monte de linha no 
			 * nosso banco e fique ocupando espaço desnecessário.
			 */
			if($diff <= 12)
			{
				strtotime($date) <= strtotime(date('Y/m/d')) ? $dataInsert->__set('status', 1) : $dataInsert->__set('status', 0);
				/**
				 * Se for anual.
				 * Se a parcela for anual, vamos inserir a parcela atual no status de 
				 * paga ou pendente e vamos adicionar mais uma parcela a frente 
				 * pendente com o id da parcela pai.
				 */
				if($fixed == 'Anual') 
				{
					$idParcel = $dataInsert->insert();
					$dataInsert->__set('id_parcel', $idParcel['id']);
					$dataInsert->__set('id', $idParcel['id']);
					$dataInsert->update();
					

					$date = date('Y/m/d', strtotime("+12 month",strtotime($date)));
					$dataInsert->__set('status', 0);
					$dataInsert->__set('date', $date);
					$dataInsert->insert();
				}

				/**
				 * Se for mensal.
				 * Se a parcela for mensal vamos adicionar as parcelas pagas até a 
				 * data atual e mais três parcelas pendentes com o id da parcela pai.
				 */
				else
				{
					$idParcel = $dataInsert->insert();
					$dataInsert->__set('id_parcel', $idParcel['id']);
					$dataInsert->__set('id', $idParcel['id']);
					$dataInsert->update();
					
					/**
					 * Insere a quantidade de parcelas fixas até a data atual, mais 2, 
					 * verificando o status da parcela, se é pendente ou concluída.
					 */
					for($i = 2; $i <= $diff+3; $i++) 
					{
						$date = date('Y/m/d', strtotime("+1 month",strtotime($date)));
						$dataInsert->__set('date', $date);
						strtotime($date) <= strtotime(date('Y/m/d')) ? $dataInsert->__set('status', 1) : $dataInsert->__set('status', 0);
						
						$dataInsert->insert();
					}
				}

				$info['messege'] = 'success';
			}
			else
			{
				$info['messege'] = 'Sua fixa tem mais de 1 ano de diferença, por favor, altere sua fixa.';
			}
		}

		/**
		 * Se for parcelado.
		 * Quando for parcelado vamos verificar se o status já está pago ou 
		 * pendente e vamos setar o status da parcela, quantidade de parcelas e o
		 * número de parcelas geradas, só geramos no máximo 420 parcelas.
		 */
		else if(isset($parcel)) 
		{
			if($parcel > 1 && $parcel <= 420) 
			{
				strtotime($date) <= strtotime(date('Y/m/d')) ? $dataInsert->__set('status', 1) : $dataInsert->__set('status', 0);	
				$dataInsert->__set('parcel', $parcel);
				$dataInsert->__set('parcelPay', 1);

				$idParcel = $dataInsert->insert();
				$dataInsert->__set('id_parcel', $idParcel['id']);
				$dataInsert->__set('id', $idParcel['id']);
				$dataInsert->update();

				for($i=2; $i <= $parcel; $i++) 
				{
					$date = date('Y/m/d', strtotime("+1 month",strtotime($date)));
					strtotime($date) <= strtotime(date('Y/m/d')) ? $dataInsert->__set('status', 1) : $dataInsert->__set('status', 0);
					$dataInsert->__set('parcelPay', $i);
					$dataInsert->__set('date', $date);

					$dataInsert->insert();
				}
			}
			/**
			 * Se a parcela o usuário informa que é de 1 parcela, então cadastramos 
			 * no banco de dados como uma parcela única.
			 */
			else 
			{
				$dataInsert->__set('enrollment', 'Única');

				$dataInsert->insert();
			}
			$info['messege'] = 'success';
		}
		/**
		 * Se não for nem fixa, nem parcelada, então só vamos verificar se é pago 
		 * ou pendente e efetuar o cadastro no bacno de dados.
		 */
		else 
		{
			strtotime($date) <= strtotime(date('Y/m/d')) ? $dataInsert->__set('status', 1) : $dataInsert->__set('status', 0);
			$dataInsert->insert();
			$info['messege'] = 'success';
		}

		return $info;
	}

	/**
	 * Soma as receitas e as despesas.
	 * A função faz a solicitação da somas das receitas e despesas a receber e 
	 * recebido ao banco de dados.
	 * @access public
	 * @param string $date com a data do mês.
	 * @param string $model o model que será executado.
	 * @return array
	 */
	public function sumMonthReceivedAndExpense($date, $model)
	{
		$dateExplode = explode('-', $date);
		$year = $dateExplode[0];
		$month = $dateExplode[1];
		$lastDay = cal_days_in_month(CAL_GREGORIAN, $month , $year);
		$lastDate = "$year-$month-$lastDay";

		$received = Container::getModel($model);
		$received->__set('date', $date);
		$received -> __set('lastDate', $lastDate);
		$received->__set('id_wallet', $_COOKIE['userWallet']);

		return $received->sumMonth();
	}

	/**
	 * Filtra as receitas e despesas mensais.
	 * A função filtra os dados das receitas da layout receita e os dados das 
	 * despesas da layout despesa.
	 * @access public
	 * @param string $model contém a informação de qual model será executado.
	 * @return array com os dados das receitas ou despesas.
	 */
	public function filterMonthReceivedAndExpenses($model) 
	{
		$date = date("Y-m-01");
		$lastDate = date("Y-m-t");
		$filterMonth = Container::getModel($model);
		$filterMonth->__set('id_wallet', $_COOKIE['userWallet']);
		$filterMonth->__set('date', $date);
		$filterMonth->__set('lastDate', $lastDate);
		return $filterMonth->filterMonth();
	}

	/**
	 * Função para remover despesas e receitas.
	 * @access public
	 * @param string $id da despesa ou receita a ser removida.
	 * @param string $model o model que será executado
	 * @return array $info com os dados se foi removido ou não.
	 */
	public function removeReceivedAndExpenses($id, $model)
	{
		$remove = Container::getModel($model);
		$remove->__set('id', $id);

		if($remove->remove()) 
		{
			$info['messege'] = 'success';
		}
		else 
		{
			$info['messege'] = 'error';
		}
		return $info;
	}

	/**
	 * Função para concluir as receitas e despesas.
	 * @access public
	 * @param string $model para saber qual model será executado.
	 * @param string $id para ser concluída.
	 */
	public function concludeReceivedAndExpenses($model, $id)
	{
		$received = Container::getModel($model);
		$received->__set('id', $id);

		if($received->conclude())
		{
			$info['messege'] = 'success';
		}
		else 
		{
			$info['messege'] = 'error';
		}
		return $info;
	}




	/**
	 * Inserir receitas no banco.
	 * A função insere os dados das receitas.
	 * @access public
	 * @return array
	 */
	public function insertReceive() 
	{
		if(isset($_POST)) 
		{
			$info = $this->insertReceiveAndExpenses($_POST, 'Received');
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Retorna todas as receitas da carteira.
	 * A função retorna todas as receitas daquela carteira que foram criadas e 
	 * salvadas no banco de dados
	 * @access public
	 * @return array
	 */
	public function receivedMonth() 
	{
		return $this->filterMonthReceivedAndExpenses('Received');
	}

	/**
	 * Filtra as receitas do usuário.
	 * A função é solicitada através do Ajax e faz a filtragem no banco de dados, 
	 * retornando apenas os dados requisitados pelo usuário.
	 * @access public
	 * @return array
	 */
	public function filterReceive() 
	{
		if(isset($_POST)) 
		{

			/**
			 * Se todos os dados forem vazios, vamos retornar todos os dados que 
			 * estão pendentes até a atual data.
			 */
			if($_POST['date'] == '' && $_POST['status'] == '' && $_POST['category'] == '') 
			{
				$received = Container::getModel('Received');
				$received->__set('id_wallet', $_COOKIE['userWallet']);
				$dataReceived['received'] = $received->filterReceiveAll();
				$dataReceived['sumReceived'] = $received->sumReceivedAll();
			}

			/**
			 * Caso contrário, vamos retornar os dados que foram filtrados no banco 
			 * de dados.
			 */
			else 
			{
				$received = Container::getModel('Received');
				$date = $_POST['date'];
				if($date != '') 
				{
					$date = explode('/', $date);
					$month = $date[0];
					$year = $date[1];
					$lastDay = cal_days_in_month(CAL_GREGORIAN, $month , $year);

					$startDate = "$year-$month-01";
					$lastDate = "$year-$month-$lastDay";

					$received->__set('date', $startDate);
					$received->__set('lastDate', $lastDate);
				}
				else 
				{
					$received->__set('lastDate', '9999-00-00');
					$received->__set('date', '0000-00-00');
				}

				$status = $_POST['status'];
				if($status == '') 
				{
					$received->__set('status', '');
				} else 
				{
					$received->__set('status', $status);
				}

				$category = $_POST['category'];
				if($category == '') 
				{
					$received->__set('category', '');
				} else {
					$received->__set('category', $category);
				}
				
				$received->__set('id_wallet',$_COOKIE['userWallet']);
				
				$dataReceived['received'] = $received->filterReceive();
				$dataReceived['sumReceived'] = $received->sumMonth();
			}

			print_r(json_encode($dataReceived, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Função para solicitamos a remoção da receita no banco de dados.
	 * @access public
	 */
	public function removeReceived() 
	{
		if(isset($_POST)) 
		{
			$id = $_POST['id'];
			$info = $this->removeReceivedAndExpenses($id, 'received');
		 
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));

		}
	}

	/**
	 * Função para atualizar os dados das receitas do usuário.
	 * @access public
	 * @return array
	 */
	public function updateReceived()
	{
		if(isset($_POST))
		{
			$type = $_POST['type'];
			$id = $_POST['id'];

			if($type == 'filter')
			{
				$received = Container::getModel('Received');
				$received->__set('id', $id);
				$dataReceived = $received->filterReceivedId();
				print_r(json_encode($dataReceived, JSON_UNESCAPED_UNICODE));
			}
			else if($type == 'update')
			{
				$form = $_POST['form'];
				$description = $form[0]['value'];
				$value = $form[1]['value'];
				$date = $form[2]['value'];
				$wallet = $form[3]['value'];
				$category = $form[4]['value'];

				$received = Container::getModel('Received');
				$received->__set('id', $id);
				$received->__set('description', $description);
				$received->__set('value', $value);
				$received->__set('date', $date);
				$received->__set('id_wallet', $wallet);
				$received->__set('category', $category);

				$dataReceived = $received->filterReceivedId();
				$enrollment = $dataReceived['enrollment'];

				if($enrollment == 'Fixa' || $enrollment == 'Parcelada') 
				{
					$idParcel = $dataReceived['id_parcel'];
					$received->__set('id_parcel', $idParcel);
				}

				if($received->update()) 
				{
					$info['messege'] = 'success';
				}
				else
				{
					$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
				}

				print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
			}
		}
	}
	
	/**
	 * Função para conclusão da receita ao banco de dados.
	 * @access public
	 */
	public function concludeReceived() 
	{
		if(isset($_POST)) 
		{
			$id = $_POST['id'];
			$info = $this->concludeReceivedAndExpenses('Received', $id);
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}




	/**
	 * Função para inserir novas despesas.
	 * A função insere as depesas únicas, fixas e parceladas.
	 * @access public
	 * @return array
	 */
	public function insertExpenses() {
		if(isset($_POST)) {
			$info = $this->insertReceiveAndExpenses($_POST, 'Expense');
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Retorna todas as despesas da carteira.
	 * A função retorna todas as despesas daquela carteira que foram criadas e 
	 * salvadas no banco de dados
	 * @access public
	 * @return array
	 */
	public function expensesMonth()
	{
		return $this->filterMonthReceivedAndExpenses('Expense');
	}

	/**
	 * Função para remover uma despesa.
	 * @access public
	 */
	public function expensesRemove()
	{
		if(isset($_POST))
		{
			$id = $_POST['id'];
			$info = $this->removeReceivedAndExpenses($id, 'Expense');
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Função para concluir despesas.
	 * @access public
	 */
	public function expensesConclude()
	{
		if(isset($_POST))
		{
			$id = $_POST['id'];
			$info = $this->concludeReceivedAndExpenses('Expense', $id);
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}




	/**
	 * Efetuar o logoff do usuário
	 * Função que faz a "destruição" do cookie responsável por manter o usuário 
	 * conectado na página do cliente.
	 * @access public
	 */
	public function Logoff() 
	{
		unset($_COOKIE['userWallet']);
		setcookie('userWallet', null, -1, '/');
		unset($_COOKIE['user']);
		setcookie('user', null, -1, '/');
		header('Location: /');
	}
}

?>