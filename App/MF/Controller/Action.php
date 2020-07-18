<?php
namespace MF\Controller;

// phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Json Web Token
use \Firebase\JWT\JWT;

abstract class Action {
  protected $view;

  /*
  * o $this->view vai armazenar os dados do model dentro de uma array, e no render ele transforma os indices da array em variável
  */
  
  public function __construct() {
    $this->view = new \stdClass();
  }

  // Apresentando o view com layout
  public function render($view) {
    $this->view->page = $view;
    $this->content();
  }
  
  protected function importViews($header='', $info='') {
    if(file_exists("../App/Views/Global/".$header.".phtml")) {
      require_once "../App/Views/Global/".$header.".phtml";
    }
  }

  protected function footer($footer='') {
    if(file_exists("../App/Views/Global/".$footer.".phtml")) {
      require_once "../App/Views/Global/".$footer.".phtml";
    }
  }

  protected function sendMail($server=array(), $mailData=array()) {
    require "../vendor/phpmailer/phpmailer/src/Exception.php";
    require "../vendor/phpmailer/phpmailer/src/OAuth.php";
    require "../vendor/phpmailer/phpmailer/src/PHPMailer.php";
    require "../vendor/phpmailer/phpmailer/src/POP3.php";
    require "../vendor/phpmailer/phpmailer/src/SMTP.php";

    // usando PHPMailer 
    $mail = new PHPMailer(true);
    try {
      //Configurações do servidor
      $mail->SMTPDebug = false; // Ativar saída de depuração detalhada
      $mail->isSMTP(); // Enviar usando SMTP
      $mail->Host = $server['host']; // Definir o servidor SMTP para enviar
      $mail->SMTPAuth   = true; // Ativar autenticação SMTP
      $mail->Username   = $server['username']; // Nome de usuário SMTP
      $mail->Password   = $server['password']; // Senha de usuário SMTP
      $mail->SMTPSecure = 'PHPMailer::ENCRYPTION_STARTTLS';// Ativar criptografia TLS
      $mail->Port       = $server['port']; // Porta TCP à qual se conectar
      $mail->CharSet = "UTF-8";
      $mail->setLanguage('br');


      $mail->setFrom($mailData['from']);
      $mail->addAddress($mailData['to']); // Adicionando destinatário
      if(in_array('attachment', $mailData)) {
        $mail->addAttachment($mailData['attachment']['path'] ,$mailData['attachment']['name']);
      }
      
      // Conteúdo
      $mail->isHTML(true);  // Definir formato de email para HTML
      $mail->Subject = $mailData['subject'];  // Assunto da menssagem
      $mail->Body = $mailData['body']; // Corpo da menssagem
      $mail->send();
      
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  protected function passwordArgon2id($password) {
    $options = [
      'memory' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
      'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
      'time' => PASSWORD_ARGON2_DEFAULT_TIME_COST
    ];
    return password_hash($password, PASSWORD_ARGON2ID, $options);
  }

  protected function checkArgon2id($password, $hash) {
    if(password_verify($password, $hash)) {
      return true;
    }
    return false;
  }

  /** 
   * Para rodar as view com as informações futuras do model
   *  Não se preocupar tanto com o render, basta copiar e colar nas class controllers futuras
  */
  protected function content() {
    $classAtual = get_class($this);
    
    $classAtual = str_replace('App\\Controllers\\', '', $classAtual);

    $classAtual = strtolower(str_replace('Controller', '', $classAtual));

    echo getcwd();
    require_once "../App/Views/".$classAtual."/".$this->view->page.".phtml";
  }
  /**
   * Gera um JWT.
   * Função gera um jwt para ser colocado no cookie do usuário, para validarmos 
   * o login dele e permitir o acesso as páginas de clientes.
   * @access protected
   * @param array $data são os dados que vamos incluir no jwt.
   */
  protected function econdeJWT($data) {
    return JWT::encode($data, md5('OrganizePessoalC4658*'));
  }

  /**
   * Decodificar o JWT.
   * Função para retornar os dados da hash gerada pelo JWT.
   * @access protected
   * @param string $hash é a hash gerada pelo JWT.
   */
  protected function decodeJWT($hash) {
    /**
     * Retorna os dados da hash jwt.
     * Retorna esses dados em formato de objeto.
     */
    return JWT::decode($hash, md5('OrganizePessoalC4658*'), array('HS256'));
  }
}

?>