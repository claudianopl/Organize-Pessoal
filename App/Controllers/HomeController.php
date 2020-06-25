<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

// Iniciando a sessão
session_start();

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
	
	/**
	 * Função para confirmar o cadastro de usuário.
	 * @access public
	 * @param String $tokenEmail
	 */
	public function registerConfirmed($tokenEmail=null) {
		$this->render('registerConfirmed');
	}

	public function login() {
		$this->render('login');
	}

	public function singup() {
		$this->render('singup');
	}

	/**
	 * Função que recebe o token de confirmação de usário para ser enviado para o
	 * email com o link de confirmação.
	 * @access public
	 * @param String $tokenEmail
	 * @return string
	 */
	public function menssage($tokenEmail) {
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
	 * Função para cadastrar novos usuários e enviar e-mail de confirmação.
	 * @access public
	 */
	public function newUser() {
		$info = array();
		$name = $_POST['name'];
		$surname = $_POST['surname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
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
			/**
			 * Array para configurar o servidor do email
			 * @name $serverMail
			 * @param ElementArray host, username, password, port
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
			 * @param ElementArray from, to, attachment [path,name], subject, body
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
			 * @param Array ServerMail, mailData
			 */
			$email = $this->sendMail($serverMail, $mailData);
			
			if($email) {
				$newUser->saveUser();
				$info['messege'] = 'success';
			}
		}
		else {
			$info['messege'] = 'Esse email já se encontra cadastrado!';
		}
		echo(json_encode($info));
	}

	/**
	 * Função para autenticar a entrada do usuário no sistema
	 * @access public
	 */
	public function authenticateUser() {
		$info = array();
		$email = $_POST['email'];
		$password = md5($_POST['password']);

		// Criando conexão com o banco de dados e se comunicando com o modal.
		$user = Container::getModel('User');

		$user->__set('user_email', $email);
		$user->__set('user_password', $password);

		/**
		 * Verifica se o email do usuário existe no banco de dados, para depois
		 * veficar a senha do usuário. Se estiver valido, iniciamos a sessão.
		 * @name $date
		 */
		$date = $user->getUserEmail();
		if(count($date) > 0) {
			if($date[0]['user_password'] == $password) {
				if($date[0]['user_confirmed'] == 1) {
					$_SESSION['authenticate'] = 'YES';
					$_SESSION['id'] = $date[0]['id'];
					$_SESSION['name'] = $date[0]['user_name'];
					$_SESSION['surname'] = $date[0]['user_surname'];
					$_SESSION['user_email'] = $date[0]['user_name'];
					$_SESSION['user_gender'] = $date[0]['user_name'];
					// Informando ao ajax que o login foi efetuado com sucesso
					$info['messege'] = 'success';
				}
				else {
					// Informando ao ajax que o usuário falta confirmar seu acesso
					$info['messege'] = 'Você não confirmou o seu cadastro, por favor, verifique seu e-mail.';
				}
			}
		}
		else {
			// Informando o usuário não foi encontrado
			$info['messege'] = 'Ops… Usuário invalido!';
		}
	echo(json_encode($info, JSON_UNESCAPED_UNICODE));
	}

}

?>