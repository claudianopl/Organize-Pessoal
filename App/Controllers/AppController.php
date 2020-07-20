<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;


session_start();


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
	public function getJWT() 
	{
		if(isset($_COOKIE['user'])) 
		{
			return $this->decodeJWT($_COOKIE['user']);
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}


	/**
	 * Renderizar layout index.
	 * Responável por verifricar se o usuário está conectado e renderizar a 
	 * página home e adicionar os dados: carteiras, receitas, despesas e tarefas.
	 * @access public
	 */
	public function Index() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();
			$this->view->balanceDiff = $this->balanceDiffDashboard();
			$this->view->pending = $this->pendingDashboard();

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

			$this->render('expense');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	/**
	 * Renderizar layout tarefas.
	 * Responável por verifricar se o usuário está conectado e renderizar a 
	 * página de tarefas e adicionar os dados: carteiras e as tarefas.
	 * @access public
	 */
	public function Tasks() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();
			$this->view->tasks = $this->tasksMonth();
			$this->render('tasks');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	/**
	 * Funão para renderizar a layout wallet, aprezentar as carteiras e dados das
	 * carteiras como id, soma das receitas e despesas.
	 */
	public function Wallet() 
	{
		if($this->checkJWT()) 
		{
			$wallets = Container::getModel('Wallet');
			$userData = $this->getJWT();
			$userId = $userData->id;
			$wallets->__set('id_user', $userId);

			$this->view->wallets = $this->userGetWallet();
			$this->view->dataSumWallets = $wallets->dataSumWallet();

			$this->render('wallet');
		} else 
		{
			header("Location: /entrar?e=0");
		}
	}

	/**
	 * Renderizar layout perfil.
	 * Responável por verifricar se o usuário está conectado e renderizar a 
	 * página de perfil e adicionar os dados do perfil do usuário.
	 * @access public
	 */
	public function Profile() 
	{
		if($this->checkJWT()) 
		{
			$this->view->wallets = $this->userGetWallet();
			$this->view->user = $this->getJWT();

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
		$id = $this->getJWT()->id;

		$wallet = Container::getModel('Wallet');
		$wallet->__set('id_user', $id);
		$wallets = $wallet->getUserWallet();
		return $wallets;
	}

	/**
	 * Seleciona a carteira que o usuário desejou.
	 * @access public
	 */
	public function userSelectWallet() 
	{
		if(isset($_POST)) 
		{
			$wallet = $_POST['wallet'];
			$_SESSION['userWallet'] = $wallet;
		}
	}



	/**
	 * Função para retornar os dados via ajax.
	 * Alimentar os gráficos e a soma das receitas e despesas mensais.
	 * @access public
	 */
	public function dashboardCalendar()
	{
		if(isset($_POST))
		{
			$date = explode('-', $_POST['date']);
			$month = $date[0];
			$month < 10 ? $month = '0'.$month : $month = $month;
			$year = $date[1];

			$lastDay = date('t', mktime(0, 0, 0, $month, 1, $year)); 
			$date = date("$year-$month-01");
			$lastDate = date("$year-$month-$lastDay");

			/**
			 * Puxando a soma das receitas recebidas do mês selecionado.
			 */
			$userReceived = $this->sumMonthReceivedAndExpense($date, 'Received');
			$sumData['sumReceived'] = $userReceived['paymentReceived'];
			
			/**
			 * Puxando a soma das despesas concluídas do mês atual
			 */
			$userExpense = $this->sumMonthReceivedAndExpense($date, 'Expense');
			$sumData['sumExpenses'] = $userExpense['ExpensesPayme'];

			/**
			 * Puxando as receitas recebidas, para alimentar o gráfico de receitas x 
			 * despesas.
			 */
			
			$graphicReceived = Container::getModel('Received');
			$graphicReceived->__set('date', $date);
			$graphicReceived->__set('lastDate', $lastDate);
			$graphicReceived->__set('status', 1);
			$graphicReceived->__set('id_wallet', $_SESSION['userWallet']);

			$graphic['received'] = $graphicReceived->sumGraphicDashboard();
			
			/**
			 * Puxando as despesas pagas, para alimentar o gráfico de receitas x 
			 * despesas.
			 */
			
			$graphicExpenses = Container::getModel('Expense');
			$graphicExpenses->__set('date', $date);
			$graphicExpenses->__set('lastDate', $lastDate);
			$graphicExpenses->__set('status', 1);
			$graphicExpenses->__set('id_wallet', $_SESSION['userWallet']);
			$graphic['expenses'] = $graphicExpenses->sumGraphicDashboard();

			/**
			 * Puxando as despesas detalhadas para alimentar o gráfico.
			 */
			
			$graphicExpensesDetailed = Container::getModel('Expense');
			$graphicExpensesDetailed->__set('date', $date);
			$graphicExpensesDetailed->__set('lastDate', $lastDate);
			$graphicExpensesDetailed->__set('status', 1);
			$graphicExpensesDetailed->__set('id_wallet', $_SESSION['userWallet']);
			$graphic['detailed'] = $graphicExpensesDetailed->sumGraphicDetailed();
			
			$data = ['sum' => $sumData, 'graphic' => $graphic];

			print_r(json_encode($data, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Retornar o nome do usuário para o header das layouts.
	 * @access public
	 */
	public function headerUserName() 
	{
		$info = array();
		$user = $this->getJWT();

		$info['userName'] = $user->name.' '.$user->surname;
		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Função para apresentarmos as receitas, despesas e tarefas pendentes.
	 * @access public
	 * @return array com a soma das receitas e despesas pagas.
	 */
	public function balanceDiffDashboard()
	{
		$userDashboard = Container::getModel('Wallet');
		$userDashboard->__set('id_wallet', $_SESSION['userWallet']);
		$sumData['diff'] = $userDashboard->balanceDiff();

		/**
		 * Puxando a soma das receitas concluídas do mês atual
		 */
		$userReceived = $this->sumMonthReceivedAndExpense(date('Y-m-01'), 'Received');
		$sumData['sumReceived'] = $userReceived['paymentReceived'];

		/**
		 * Puxando a soma das despesas concluídas do mês atual
		 */
		$userExpense = $this->sumMonthReceivedAndExpense(date('Y-m-01'), 'Expense');
		$sumData['sumExpenses'] = $userExpense['ExpensesPayme'];
		
		return $sumData;
	}

	/**
	 * Função para filtrar as receitas, despesas e tarefas pendentes até o mês 
	 * atual.
	 * @access public
	 * @return array
	 */
	public function pendingDashboard()
	{
		$pendingReceived = Container::getModel('Received');
		$pendingReceived->__set('id_wallet', $_SESSION['userWallet']);
		$pendingReceived->__set('date' ,date('Y-m-t'));
		$pendingReceived = $pendingReceived->filterDashboard();
		$pending['received'] = $pendingReceived;

		$pendingExpenses = Container::getModel('Expense');
		$pendingExpenses->__set('id_wallet', $_SESSION['userWallet']);
		$pendingExpenses->__set('date' ,date('Y-m-t'));
		$pendingExpenses = $pendingExpenses->filterDashboard();
		$pending['expenses'] = $pendingExpenses;

		$pendingTasks = Container::getModel('Tasks');
		$pendingTasks->__set('id_wallet', $_SESSION['userWallet']);
		$pendingTasks->__set('date' ,date('Y-m-t'));
		$pendingTasks = $pendingTasks->filterDashboard();
		$pending['tasks'] = $pendingTasks;

		return($pending);
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
	 * @param string $date com o mês e o ano.
	 * @param string $model o model que será executado.
	 * @return array
	 */
	public function sumMonthReceivedAndExpense($date, $model)
	{
		$dateExplode = explode('-', $date);
		$year = $dateExplode[0];
		$month = $dateExplode[1];
		$lastDay = date('t', mktime(0, 0, 0, $month, 1, $year)); 
		$lastDate = "$year-$month-$lastDay";

		$received = Container::getModel($model);
		$received->__set('date', $date);
		$received -> __set('lastDate', $lastDate);
		$received->__set('id_wallet', $_SESSION['userWallet']);

		return $received->sumMonth();
	}

	/**
	 * Filtra as receitas e as despesas do usuário.
	 * @access public
	 * @param string $data contendo os dados do $_POST.
	 * @param string $model contendo qual o model a ser executado.
	 */
	public function filterReceivedAndExpenses($data, $model)
	{
		/**
		 * Se todos os dados forem vazios, vamos retornar todos os dados registrados 
		 * no banco de dados.
		 */
		if($_POST['date'] == '' && $_POST['status'] == '' && $_POST['category'] == '')
		{
			$filterAll = Container::getModel($model);
			$filterAll->__set('id_wallet', $_SESSION['userWallet']);
			$dataFilter['data'] = $filterAll->filterAll();
			$dataFilter['sum'] = $filterAll->sumAll();
		}

		/**
		 * Caso contrário, vamos retornar os dados que foram filtrados no banco 
		 * de dados.
		 */
		else
		{
			$filter = Container::getModel($model);
			$date = $_POST['date'];
			if($date != '')
			{
				$date = explode('/', $date);
				$month = $date[0];
				$year = $date[1];
				$lastDay = date('t', mktime(0, 0, 0, $month, 1, $year)); 

				$startDate = "$year-$month-01";
				$lastDate = "$year-$month-$lastDay";

				$filter->__set('date', $startDate);
				$filter->__set('lastDate', $lastDate);
			}
			else
			{
				$filter->__set('lastDate', '9999-00-00');
				$filter->__set('date', '0000-00-00');
			}

			$status = $_POST['status'];
			if($status != '') 
			{
				if($status == 'Fixa' || $status == 'Parcelada')
				{
					$filter->__set('enrollment', $status);
				}
				else 
				{
					$filter->__set('status', $status);
				}
			}

			$category = $_POST['category'];
			if($category != '')
			{	
				$filter->__set('category', $category);
			}

			$filter->__set('id_wallet',$_SESSION['userWallet']);
			$dataFilter['data'] = $filter->filter();
			$dataFilter['sum'] = $filter->sumMonth();
		}
		
		return $dataFilter;
	}

	/**
	 * Filtra as receitas e despesas mensais.
	 * A função filtra os dados do mes atual das receitas, despesas e tarefas.
	 * @access public
	 * @param string $model contém a informação de qual model será executado.
	 * @return array com os dados das receitas ou despesas ou tarefas.
	 */
	public function filterMonthReceivedAndExpensesAndTasks($model) 
	{
		$date = date("Y-m-01");
		$lastDate = date("Y-m-t");
		$filterMonth = Container::getModel($model);
		$filterMonth->__set('id_wallet', $_SESSION['userWallet']);
		$filterMonth->__set('date', $date);
		$filterMonth->__set('lastDate', $lastDate);
		return $filterMonth->filterMonth();
	}

	/**
	 * Função para atualizar os dados das receitas e despesas do usuário.
	 * @access public
	 * @return array $dataFilterId contém os dados da receita ou despesa a ser 
	 * alterada|| $info cotém a informação se foi ou não alterada.
	 */
	public function updateReceivedAndExpenses($data, $model)
	{
		$type = $data['type'];
		$id = $data['id'];

		if($type == 'filter')
		{
			$filterId = Container::getModel($model);
			$filterId->__set('id', $id);
			$dataFilterId = $filterId->filterId();
			return $dataFilterId;
		}
		else if($type == 'update')
		{
			$form = $data['form'];
			$description = $form[0]['value'];
			$value = $form[1]['value'];
			$date = $form[2]['value'];
			$wallet = $form[3]['value'];
			$category = $form[4]['value'];
			$status = $form[5]['value'];

			$updateId = Container::getModel($model);
			$updateId->__set('id', $id);
			$updateId->__set('description', $description);
			$updateId->__set('value', $value);
			$updateId->__set('date', $date);
			$updateId->__set('id_wallet', $wallet);
			$updateId->__set('category', $category);
			$updateId->__set('status', $status);

			$dataFilterId = $updateId->filterId();
			$enrollment = $dataFilterId['enrollment'];

			if($enrollment == 'Fixa' || $enrollment == 'Parcelada') 
			{
				$idParcel = $dataFilterId['id_parcel'];
				$updateId->__set('id_parcel', $idParcel);
			}
			if($updateId->update())
			{
				$info['messege'] = 'success';
			}
			else
			{
				$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
			}
			return $info;
		}
	}

	/**
	 * Função para remover despesas e receitas.
	 * @access public
	 * @param string $id da despesa ou receita a ser removida.
	 * @param string $model o model que será executado
	 * @return array $info com os dados se foi removido ou não.
	 */
	public function removeReceivedAndExpensesAndTasks($id, $model)
	{
		$remove = Container::getModel($model);
		$remove->__set('id', $id);

		if($remove->remove()) 
		{
			$info['messege'] = 'success';
		}
		else 
		{
			$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
		}
		return $info;
	}

	/**
	 * Função para concluir as receitas e despesas.
	 * @access public
	 * @param string $model para saber qual model será executado.
	 * @param string $id para ser concluída.
	 * @return array
	 */
	public function concludeReceivedAndExpenses($model, $id)
	{
		
		$conclude = Container::getModel($model);
		$conclude->__set('id', $id);
		$data = $conclude->filterId();
		$enrollment = $data['enrollment'];
		if($enrollment == 'Fixa')
		{
			$conclude->__set('id_parcel', $data['id_parcel']);

			$dataParcel = $conclude->filterIdParcel();
			$dataParcel = $dataParcel[count($dataParcel) - 1];
			if($data['status_parcel_fixed'] == 'Mensal')
			{
				$date = date('Y-m-d', strtotime("+1 month",strtotime($dataParcel['date'])));
			}
			else if($data['status_parcel_fixed'] == 'Anual')
			{
				$date = date('Y-m-d', strtotime("+12 month",strtotime($dataParcel['date'])));
			}
	
			$conclude->__set('id_wallet', $data['id_wallet']);
			$conclude->__set('status', 0);
			$conclude->__set('description', $data['description']);
			$conclude->__set('value', $data['value']);
			$conclude->__set('date', $date);
			$conclude->__set('category', $data['category']);
			$conclude->__set('enrollment', $data['enrollment']);
			$conclude->__set('statusParcelFixed', $data['status_parcel_fixed']);
			$conclude->__set('id_parcel', $data['id_parcel']);
			$conclude->insert();
			
		}
		
		if($conclude->conclude())
		{
			$info['messege'] = 'success';
		}
		else 
		{
			$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
		}

		return $info;
	}




	/**
	 * Inserir receitas no banco.
	 * A função insere os dados das receitas.
	 * @access public
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
		return $this->filterMonthReceivedAndExpensesAndTasks('Received');
	}

	/**
	 * Filtra as receitas do usuário.
	 * A função é solicitada através do Ajax e faz a filtragem no banco de dados, 
	 * retornando apenas os dados requisitados pelo usuário.
	 * @access public
	 */
	public function filterReceive() 
	{
		if(isset($_POST)) 
		{
			$data = $this->filterReceivedAndExpenses($_POST, 'Received');
			print_r(json_encode($data, JSON_UNESCAPED_UNICODE));
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
			$info = $this->removeReceivedAndExpensesAndTasks($id, 'received');
		 
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));

		}
	}

	/**
	 * Função para atualizar os dados das receitas do usuário.
	 * @access public
	 */
	public function updateReceived()
	{
		if(isset($_POST))
		{
			$data = $this->updateReceivedAndExpenses($_POST, 'Received');
			print_r(json_encode($data, JSON_UNESCAPED_UNICODE));
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
	 */
	public function insertExpenses() 
	{
		if(isset($_POST)) 
		{
			$info = $this->insertReceiveAndExpenses($_POST, 'Expense');
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Filtra as despesas do usuário.
	 * A função é solicitada através do Ajax e faz a filtragem no banco de dados, 
	 * retornando apenas os dados requisitados pelo usuário.
	 * @access public
	 */
	public function filterExpenses()
	{
		if(isset($_POST))
		{
			$data = $this->filterReceivedAndExpenses($_POST, 'Expense');
			print_r(json_encode($data, JSON_UNESCAPED_UNICODE));
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
		return $this->filterMonthReceivedAndExpensesAndTasks('Expense');
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
			$info = $this->removeReceivedAndExpensesAndTasks($id, 'Expense');
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Função para atualizar os dados das despesas do usuário.
	 * @access public
	 */
  public function updateExpenses()
  {
    if(isset($_POST))
    {
			$data = $this->updateReceivedAndExpenses($_POST, 'Expense');
			print_r(json_encode($data, JSON_UNESCAPED_UNICODE));
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
	 * Função para solicitar a inserção de uma nova tarefa.
	 * A função verifica a data para saber se o status da tarefa vai ser pendente 
	 * ou concluída.
	 * @access public
	 */
	public function insertTasks()
	{
		if(isset($_POST))
		{
			$description = $_POST['Desc'];
			$date = $_POST['Date'];
			$wallet = $_POST['Wallet'];
			$insertTasks = Container::getModel('Tasks');
			$insertTasks->__set('id_wallet', $_SESSION['userWallet']);
			if(strtotime($date) <= strtotime(date('Y-m-d H:i:s')))
			{
				$insertTasks->__set('status', 1);
			} 
			else
			{
				$insertTasks->__set('status', 0);
			}
			$insertTasks->__set('date', $date);
			$insertTasks->__set('description', $description);
			if($insertTasks->insert())
			{
				$info['messege'] = 'success';
			}
			else
			{
				$info['messege'] = 'Um erro inesperado aconteceu na inserção da tarefa, tente novamente mais tarde!';
			}

			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Filtra as tarefas do usuário.
	 * @access public
	 */
	public function filterTasks()
	{
		if(isset($_POST))
		{
			/**
			 * Se todos os dados forem vazios, vamos retornar todos os dados registrados 
			 * no banco de dados.
			 */
			if($_POST['date'] == '' && $_POST['status'] == '')
			{
				$filterAll = Container::getModel('Tasks');
				$filterAll->__set('id_wallet', $_SESSION['userWallet']);
				$dataFilter['data'] = $filterAll->filterAll();
			}

			/**
			 * Caso contrário, vamos retornar os dados que foram filtrados no banco 
			 * de dados.
			 */
			else
			{
				$filter = Container::getModel('Tasks');
				$date = $_POST['date'];
				if($date != '')
				{
					$date = explode('/', $date);
					$month = $date[0];
					$year = $date[1];
					$lastDay = date('t', mktime(0, 0, 0, $month, 1, $year)); 

					$startDate = "$year-$month-01";
					$lastDate = "$year-$month-$lastDay";

					$filter->__set('date', $startDate);
					$filter->__set('lastDate', $lastDate);
				}
				else
				{
					$filter->__set('lastDate', '9999-00-00');
					$filter->__set('date', '0000-00-00');
				}

				$status = $_POST['status'];
				if($status != '') 
				{
					$filter->__set('status', $status);
				}
				$filter->__set('id_wallet',$_SESSION['userWallet']);
				$dataFilter['data'] = $filter->filter();
			}
		}
		print_r(json_encode($dataFilter, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Atualizar tarefas
	 * @access public
	 */
	public function updateTasks()
	{
		if(isset($_POST))
		{
			$type = $_POST['type'];
			$id = $_POST['id'];
		}
		/**
		 * se for filter, vamos formatar a data antes de enviar o dado ao usuário.
		 */
		if($type == 'filter')
		{
			$filterId = Container::getModel('Tasks');
			$filterId->__set('id', $id);
			$dataFilterId = $filterId->filterId();
			$dateFormat = date_format(new \DateTime($dataFilterId['date']), 'd/m/Y H:i');
			$dataFilterId['date'] = $dateFormat;
			print_r(json_encode($dataFilterId, JSON_UNESCAPED_UNICODE));
		}
		else if($type == 'update')
		{
			$form = $_POST['form'];
			$description = $form[0]['value'];
			$date = date_format(new \DateTime($form[1]['value']), 'Y-d-m H:i');
			$wallet = $form[2]['value'];
			$status = $form[3]['value'];
			
			$updateId = Container::getModel('Tasks');
			$updateId->__set('id', $id);
			$updateId->__set('description', $description);
			$updateId->__set('date', $date);
			$updateId->__set('id_wallet', $wallet);
			$updateId->__set('status', $status);
			if($updateId->update())
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

	/**
	 * Retorna todas as taferas da carteira.
	 * A função retorna todas as taferas daquela carteira que foram criadas e 
	 * salvadas no banco de dados
	 * @access public
	 * @return array
	 */
	public function tasksMonth()
	{
		return $this->filterMonthReceivedAndExpensesAndTasks('Tasks');
	}

	/**
	 * Função para remover uma tarefa.
	 * @access public
	 */
	public function removeTasks()
	{
		if(isset($_POST))
		{
			$id = $_POST['id'];
			$info = $this->removeReceivedAndExpensesAndTasks($id, 'Tasks');
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Função para concluir tarefas.
	 * @access public
	 */
	public function concludeTasks()
	{
		if(isset($_POST))
		{
			$id = $_POST['id'];
			$conclude = Container::getModel('Tasks');
			$conclude->__set('id', $id);
			if($conclude->conclude())
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

	


	/**
	 * Função para inserir novas carteiras.
	 * @access public
	 */
	public function insertWallet()
	{
		if(isset($_POST))
		{
			$wallet = $_POST['newWallet'];
			$userData = $this->getJWT();
			$userId = $userData->id;

			$insert = Container::getModel('Wallet');
			$insert->__set('id_user', $userId);
			if(count($insert->getUserWallet()) < 3)
			{
				$insert->__set('wallet_name', $wallet);
				if($insert->insert())
				{
					$info['messege'] = 'success';
				}
				else 
				{
					$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
				}
			}
			else
			{
				$info['messege'] = 'Ops… Parece que você atingiu um número limite de carteiras.';
			}
			
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}

	/**
	 * Função para trazer os dados que vão conter no gráfico.
	 * @access public
	 */
	public function graphicWallet()
	{
		$graphic = Container::getModel('Wallet');
		$graphic->__set('date', date('Y-01-01'));
		$graphic->__set('id_wallet', $_SESSION['userWallet']);
		$graphic->__set('year', date('Y'));

		$data = $graphic->annualSum();
		
		print_r(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Função para remover carteiras.
	 * @access public
	 */
	public function removeWallet()
	{
		if(isset($_POST))
		{
			$wallet = $this->userGetWallet();
	
			$idWallet = $_POST['id'];
			$remove = Container::getModel('Wallet');
			$remove->__set('id_wallet', $idWallet);

			if(count($wallet) > 1)
			{
				if($remove->remove())
				{
					$info['messege'] = 'success';
					$_SESSION['userWallet'] = $wallet[0]['id'];
				}
				else {
					$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
				}
			}
			else {
				$info['messege'] = 'Você precisa criar uma carteira para remover outra.';
			}
			print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
		}
	}




	/**
	 * Função para atualziar os dados do usuário.
	 * @access public
	 */
	public function userProfile()
	{
		if(isset($_POST))
		{
			$name = $_POST['name'];
			$surname = $_POST['surname'];
			$gender = $_POST['gender'];
			$nasciment = $_POST['nasciment'];
			$password = $_POST['password'];

			$userId = $this->getJWT()->id;
			

			$user = Container::getModel('User');
			$user->__set('id', $userId);

			$userData = $user->getUserId();
			if(count($userData) > 0)
			{
				if($this->checkArgon2id($password, $userData['user_password']))
				{
					$user->__set('user_name', $name);
					$user->__set('user_surname', $surname);
					$user->__set('user_gender', $gender);
					$user->__set('user_nasciment', $nasciment);
					if($user->updateUser())
					{
						$userData = $user->getUserId();
						$userData = [
							'authenticate' => md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']),
							'id' => $userData['id'],
							'name' => $userData['user_name'],
							'surname' => $userData['user_surname'],
							'email' => $userData['user_email'],
							'gender' => $userData['user_gender'],
							'nasciment' => $userData['user_nasciment']
						];
						$name = 'user';
						$jwt = $this->econdeJWT($userData);
						setcookie($name, $jwt);

						$info['messege'] = 'success';
					}
					else
					{
						$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
					}
				}
				else
				{
					$info['messege'] = 'Senha inválida, por favor, verifique sua senha.';
				}
				print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
			}
		}
	}

	/**
	 * Função para atualizar senha do usuário.
	 * A função verifica a senha atual, se estiver correta, a função efetua a 
	 * troca da senha.
	 * @access public
	 */
	public function updatePassword()
	{
		if(isset($_POST))
		{
			$password = $_POST['password'];
			$newPassword = $_POST['newPassword'];
			$confirmPassword = $_POST['confirmPassword'];

			$userId = $this->getJWT()->id;
			$user = Container::getModel('User');
			$user->__set('id', $userId);

			$userData = $user->getUserId();
			if(count($userData) > 0)
			{
				if($this->checkArgon2id($password, $userData['user_password']))
				{
					$password = $this->passwordArgon2id($newPassword);
					$user->__set('user_password', $password);
					if($user->updatePassword())
					{
						$info['messege'] = 'success';
					}
					else 
					{
						$info['messege'] = 'Um erro inesperado aconteceu, tente novamente mais tarde.';
					}
				}
				else
				{
					$info['messege'] = 'Sua senha é inválida, por favor, verifique sua senha.';
				}
				print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
			}
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
		session_destroy();
		unset($_COOKIE['user']);
		setcookie('user', null, -1, '/');
		header('Location: /');
	}
}

?>