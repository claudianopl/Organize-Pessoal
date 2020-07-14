<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

class HomeController extends Action 
{
	public function index() 
	{
		$this->render('index');
	}
	
	public function about() 
	{
		$this->render('about');
	}

	public function confirmRegister() 
	{
		$this->render('confirmRegister');
		echo(md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
	}
	
	/**
	 * Função para confirmar o cadastro de usuário.
	 * @access public
	 * @param String $tokenEmail
	 */
	public function registerConfirmed($tokenEmail=null) 
	{
		$this->render('registerConfirmed');

		$user = Container::getModel('User');
		$user->__set('user_confirm', $tokenEmail);

		$date = $user->getUserHashConfirm();
		if(count($date) > 0 && $date['user_confirmed'] == 0) 
		{	
			$user->__set('id', $date['id']);
			$user->userUpdateConfirmed();
		}
	}

	public function login() 
	{
		if(isset($_COOKIE['user'])) 
		{
			header('Location: /app');
		} else 
		{
			$this->render('login');
		}
	}

	public function resetPassword() 
	{
		$this->render('resetPassword');
	}

	public function singup() 
	{
		$this->render('singup');
	}

	/**
	 * Função para cadastrar novos usuários e enviar e-mail de confirmação.
	 * @access public
	 */
	public function newUser() 
	{
		$info = array();
		$name = $_POST['name'];
		$surname = $_POST['surname'];
		$email = $_POST['email'];
		$password = $this->passwordArgon2id($_POST['password']);
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
		if($newUser->validateUser() && count($newUser->getUserEmail()) == 0) 
		{
			/**
			 * Array para configurar o servidor do email
			 * @name $serverMail
			 * @param array host, username, password, port
			 */
			$serverMail = [
				'host' => 'smtp.mailtrap.io',
				'username' => '37dea9e6299bb1',
				'password' => '73b8c084a62e50',
				'port' => '587'
			];

			/**
			 * array do container email para ser enviado
			 * @name $mailData
			 * @param array from, to, attachment [path,name], subject, body
			 */
			$mailData = [
				'from' => 'equipeorganizepessoal@organizepessoal.com',
				'to' => $_POST['email'],
				'subject' => 'Ativar conta no Organize Pessoal',
				'body' => $this->menssage($tokenEmail)
			];

			/**
			 * Váriavel recebe o retorno do envio do email
			 * @name $email
			 * @param array ServerMail, mailData
			 */
			//$email = $this->sendMail($serverMail, $mailData);
			
			if($email) 
			{
				$newUser->saveUser();

				/**
				 * Criação da carteira.
				 * <?php 
				 * $dateUser = $newUser->getUserEmail();
				 * $userID = $dateUser['id'];
				 * $newUser->__set('id', $userID);
				 * $newUser->__set('wallet_name', 'Carteira Geral');
				 * $newUser->saveWallet();
				 * $info['messege'] = 'success';
				 * ?>
				 * Esse código cria uma carteira para o usuário.
				 */
				$dateUser = $newUser->getUserEmail();				
				$userID = $dateUser['id'];					
				$newUser->__set('id', $userID);
				$newUser->__set('wallet_name', 'Carteira Geral');
				$newUser->saveWallet();

				$info['messege'] = 'success';
			}
		}
		else 
		{
			$info['messege'] = 'Esse email já se encontra cadastrado!';
		}
		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Função para autenticar a entrada do usuário no sistema.
	 * A função valida o email, verifica no banco de dados se esse email está 
	 * cadastrado, caso tiver cadastrado verificamos a senha, se estiver tudo certo
	 * efetuamos o login e ligamos a sessão.
	 * @access public
	 */
	public function authenticateUser() 
	{
		$info = array();
		$email = $_POST['email'];
		$password = $_POST['password'];

		// Verifica se o email é válido.
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			// Criando conexão com o banco de dados e se comunicando com o modal.
			$user = Container::getModel('User');
			$user->__set('user_email', $email);
			$user->__set('user_password', $password);
			
			/**
			 * Verifica se o usuário existe.
			 * Verifica se o email do usuário existe no banco de dados, para depois 
			 * veficar a senha do usuário. Se estiver valido, iniciamos a sessão.
			 * @name $data
			 */
			$data = $user->getUserEmail();
			if(count($data) > 0) 
			{
				if($this->checkArgon2id($password, $data['user_password'])) 
				{
					if($data['user_confirmed'] == 1) 
					{
						$userData = [
							'authenticate' => md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']),
							'id' => $data['id'],
							'name' => $data['user_name'],
							'surname' => $data['user_surname'],
							'email' => $data['user_email'],
							'gender' => $data['user_gender'],
							'nasciment' => $data['user_nasciment']
						];
						$name = 'user';
						$jwt = $this->econdeJWT($userData);
						setcookie($name, $jwt);

						/**
						 * Iniciando o login com a primeira carteira do usuário.
						 */ 
						$wallet = Container::getModel('Wallet');
						$wallet->__set('id_user', $data['id']);
						$wallets = $wallet->getUserWallet();
						$walletId = $wallets[0]['id'];
						
						session_start();
						
						$_SESSION['userWallet'] = $walletId;
						// Informando ao ajax que o login foi efetuado com sucesso
						$info['messege'] = 'success';
					}
					else 
					{
						// Informando ao ajax que o usuário falta confirmar seu acesso
						$info['messege'] = 'Você não confirmou o seu cadastro, por favor, verifique seu e-mail.';
					}
				}
				else 
				{
					// Informando o usuário não foi encontrado
					$info['messege'] = 'Ops… Usuário invalido!';
				}
			}
			else 
			{
				// Informando o usuário não foi encontrado
				$info['messege'] = 'Ops… Usuário invalido!';
			}
		}

		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Função para enviar email de troca da senha.
	 * A função valida o email, verifica no banco de dados se esse email está 
	 * cadastrado, caso tiver cadastrado geramos um Token e enviamos para o usuário
	 * por email.
	 * @access public
	 */
	public function changeTokenPassword() 
	{
		$info = array();
		$email = $_POST['email'];
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			$user = Container::getModel('User');
			$user->__set('user_email', $email);

			$date = $user->getUserEmail();
			if(count($date) > 0) 
			{
				/**
				 * Gera o token para o usuário conseguir trocar a sua senha.
				 * Esse código gera um token e envia esse token para o banco de dados
				 * que deixa guardado até que seja usado.
				 * <?php 
				 * $tokenEmail = md5(time().rand(0,9999).rand(0,9999));
				 * $user->__set('user_changepassword', $tokenEmail);
				 * $user->changeTokenPassword();
				 * ?>
				 */
				$tokenEmail = md5(time().rand(0,9999).rand(0,9999));
				$user->__set('user_changepassword', $tokenEmail);
				if($user->changeTokenPassword())
				{
					$serverMail = [
						'host' => 'smtp.mailtrap.io',
						'username' => '37dea9e6299bb1',
						'password' => '73b8c084a62e50',
						'port' => '587'
					];
					$mailData = [
						'from' => 'equipeorganizepessoal@organizepessoal.com',
						'to' => $email,
						'subject' => 'Redefinir a senha do Organize Pessoal',
						'body' => $this->messegeChangePassword($tokenEmail)
					];
					$email = $this->sendMail($serverMail, $mailData);
	
					$info['message'] = 'success';
				}
				else
				{
					$info['message'] = 'Algum erro aconteceu, tente novamente mais tarde!';
				}
				
			} 
			else 
			{
				$info['message'] = 'O e-mail informado não está cadastrado!';
			}
		}
		else 
		{
			$info['message'] = 'E-mail invalido, por favor informe um e-mail válido.';
		}
		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

	public function changePassword() 
	{
		$newPassword = $this->passwordArgon2id($_POST['newPassword']);
		$hash = $_POST['hash'];
		$info = array();
		if($hash != '' || strlen($hash) == 32) 
		{
			$user = Container::getModel('User');
			$user->__set('user_password', $newPassword);
			$user->__set('user_changepassword', $hash);
			if($user->changePassword()) 
			{
				$info['message'] = 'success';
			} 
			else 
			{
				$info['message'] = 'Sua senha já foi alterada.';
			}
		
		} 
		else 
		{
			$info['message'] = 'Error: Informações inválidas, usuário não existe.';
		}
		print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

	

		/**
	 * Função que recebe o token de confirmação de usário para ser enviado para o
	 * email com o link de confirmação.
	 * @access public
	 * @param string $tokenEmail
	 * @return string
	 */
	public function menssage($tokenEmail) 
	{
		$message = "<html><head xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>";
		$message .= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
		$message .= "<meta name='viewport' content='width=device-width, initial-scale=1, max-scale=1'>\r\n";
		$message .= "<meta http-equiv='Content-Type' content='text/html' charset='utf-8'>";
		$message .= "<title>Bem vindo ao Organize Pessoal</title>";
		$message .= "<style type='text/css' body {margin: 0; padding: 0;}></style>";


		$message .= "</head><body>";
		$message .= "
		<center>
			<table cellspacing='0' cellpadding='0' bgcolor='#EBEBEB' width='600'>
				<tr>
					<td style='width: 100%; display: flex; justify-content: center; align-items: center;'>
						<img src=''>
					</td>
				</tr>
				<tr>
					<td style='height: 40px'></td>
				</tr>
				<tr>
					<td align='center' valign='top' style='padding: 0 20px !important;'>
						<table cellspacing='0' cellpadding='0'  width='540' style='background: #FFFFFF; box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.25);'>
							<tr>
								<td align='left' style='padding: 5% 4.5%;font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif!important;font-size:26px!important;line-height:30px!important;font-weight:500!important;color:#636D7C!important'>
									<h1 align='center' style='display:block;margin:0;padding:0;color:#636D7C;font-family:Helvetica;font-size:26px;font-style:normal;font-weight:bold;line-height:150%;letter-spacing:normal;text-align:center;'>
										<span style='font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif; color: #636D7C'>
											<strong>Bem-vindo(a) ao Organize Pessoal!</strong>
										</span>
									</h1>
									&nbsp;
									<p dir='ltr' style='margin-top: 20px; margin-bottom: 20px; padding: 0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;color:#636D7C;font-family:Helvetica;font-size:16px;line-height:150%;text-align:left; '>
										<span style='font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif; color: #636D7C'>
											Você acabou de dar o primeiro passo para melhorar sua vida financeira.
											<br>
											<br>
											Você já chegou no final do mês sem saber <strong>onde o seu dinheiro foi parar</strong> ou então tinha uma tarefa, ou algum compromisso super importante e esqueceu?
											<br>
											<br>
											É bastante provável que sim. <strong>Por isso desenvolvemos essa plataforma inteiramente para você</strong>, para conseguir controlar os seus gastos e gerenciar suas tarefas de forma inteligente, fácil e rápido.
											<br>
											<br>
											Tudo o que precisa fazer é <strong>se comprometer a anotar seus gastos e suas tarefas e deixar que a Organize Pessoal gerencia de forma inteligente tudo para você</strong>, único trabalho que vai ter é de anotar na plataforma.
											<br>
											<br>
											<mark>Chega de passar horas escrevendo seus gastos no papel, perder tempo fazendo cálculos chatos e não saber para onde o seu dinheiro está indo.</mark> Deixe que a Organize Pessoal faz todo o trabalho chato para você, ainda lhe entrega um relatório com gráficos mensais e detalhamento de todas suas despesas.
											<br>
											<br>
											<strong>Que tal começar se comprometendo a anotar todos seus gastos durante 7 dias seguidos?! Deixe o trabalho chato conosco!</strong>
											<br>
											<br>
											Basta confirmar o seu e-mail clicando no botão abaixo e se comprometer de anotar seus gastos.
										</span>
									</p>
								</td>
							</tr>
							<tr>
								<td align='center'>
									<a href='/cadastro-confirmado/$tokenEmail' style='padding: 10px 50px; background: #34F06F; border-radius: 35px; font-size: 24px; text-decoration: none; color: #fff; font-family: Roboto;'>
										Confirmar Cadastro
									</a>
								</td>
							</tr>
							<tr>
								<td style='height: 30px'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style='height: 40px'></td>
				</tr>
			</table>
		</center>";
		$message .= "</body></html>";

		return $message;
	}

	/**
	 * Função para gerar o email para trocar a senha.
	 * A função vai criar o corpo do email que vai enviar para o usuário poder 
	 * trocar sua senha com segurança.
	 * @access public
	 * @param string $tokenEmail é o token gerado para verificarmos quem está 
	 * solicitando a troca de senha.
	 * @return string $message retorna o corpo da menssagem para ser enviado no 
	 * email do usuário.
	 */
	public function messegeChangePassword($tokenEmail) 
	{
		$message = "<html><head xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>";
		$message .= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
		$message .= "<meta name='viewport' content='width=device-width, initial-scale=1, max-scale=1'>\r\n";
		$message .= "<meta http-equiv='Content-Type' content='text/html' charset='utf-8'>";
		$message .= "<title>Solicitação para trocar a senha.</title>";
		$message .= "<style type='text/css' body {margin: 0; padding: 0;}></style>";

		$message .= "</head><body>";
		$message .= "
		<center>
			<table cellspacing='0' cellpadding='0' bgcolor='#EBEBEB' width='600'>
				<tr>
					<td style='width: 100%; display: flex; justify-content: center; align-items: center;'>
						<img src=''>
					</td>
				</tr>
				<tr>
					<td style='height: 40px'></td>
				</tr>
				<tr>
					<td align='center' valign='top' style='padding: 0 20px !important;'>
						<table cellspacing='0' cellpadding='0'  width='540' style='background: #FFFFFF; box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.25);'>
							<tr>
								<td align='left' style='padding: 5% 4.5%;font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif!important;font-size:26px!important;line-height:30px!important;font-weight:500!important;color:#636D7C!important'>
									<h1 align='center' style='display:block;margin:0;padding:0;color:#636D7C;font-family:Helvetica;font-size:26px;font-style:normal;font-weight:bold;line-height:150%;letter-spacing:normal;text-align:center;'>
										<span style='font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif; color: #636D7C'>
											<strong>Redefinir a senha do Organize Pessoal.</strong>
										</span>
									</h1>
									&nbsp;
									<p dir='ltr' style='margin-top: 20px; margin-bottom: 20px; padding: 0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;color:#636D7C;font-family:Helvetica;font-size:16px;line-height:150%;text-align:justify; '>
										<span style='font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif; color: #636D7C'>
											Olá, você solicitou a redefinição da sua senha na sua conta da Organize Pessoal. Clique no botão para efetuar a troca da sua senha.
										</span>
									</p>
									<table align='center' style='padding: 20px 0;'>
										<tr>
											<td>
												<a href='/redefinir?verification=$tokenEmail' style='padding: 10px 90px; background: #34F06F; border-radius: 35px; font-size: 24px; text-decoration: none; color: #fff; font-family: Roboto;'>
													Trocar Senha
												</a>
											</td>
										</tr>
									</table>
									
									<p dir='ltr' style='margin-top: 20px; margin-bottom: 20px; padding: 0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;color:#636D7C;font-family:Helvetica;font-size:16px;line-height:150%;text-align:justify; '>
										<span style='font-family:trebuchet ms,lucida grande,lucida sans unicode,lucida sans,tahoma,sans-serif; color: #8F98A5'>
											Se você não solicitou a redefinição da sua senha, ignore este e-mail.
											<br>
											<br>
											Obrigado,
											<br>
											<br>
											Equipe da Organize Pessoal.
										</span>
									</p>
								</td>
							</tr>
							<tr>
								<td style='height: 30px'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style='height: 40px'></td>
				</tr>
			</table>
		</center>";
		$message .= "</body></html>";
		return $message;
	}
}

?>