<?php
// namespace para serem importados no Controllers
namespace App\Models;

// namespace para importar a conexão ao banco de dados
use MF\Model\Model;

class User extends Model
{
  private $id;
  private $wallet_name;
  private $user_name;
  private $user_surname;
  private $user_gender;
  private $user_email;
  private $user_password;
  private $user_confirm;
  private $user_confirmed;
  private $user_changepassword;
  private $user_nasciment;

  public function __get($attribute) 
  {
    return $this->$attribute;
  }

  public function __set($attribute, $value) 
  {
    $this->$attribute = $value;
  }

  /**
   * Salva novo usuário.
   * A função salva no banco de dados um novo usuário que foi criado.
   * @access public
   * @return boolean
   */
  public function saveUser() 
  {
    $query = 'insert into tb_user set user_name=:name, user_surname=:surname,
    user_email=:email, user_password=:password, user_confirm=:confirm';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':name', $this->__get('user_name'));
    $stmt->bindValue(':surname', $this->__get('user_surname'));
    $stmt->bindValue(':email', $this->__get('user_email'));
    $stmt->bindValue(':password', $this->__get('user_password'));
    $stmt->bindValue(':confirm', $this->__get('user_confirm'));
    $stmt->execute();

    return true;
  }

  /**
   * Atualizando os dados do usuário.
   * @access public
   * @return boolean
   */
  public function updateUser()
  {
    $query = 'update tb_user set user_name=:name, user_surname=:surname,
    user_nasciment=:nasciment, user_gender=:gender';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':name', $this->__get('user_name'));
    $stmt->bindValue(':surname', $this->__get('user_surname'));
    $stmt->bindValue(':nasciment', $this->__get('user_nasciment'));
    $stmt->bindValue(':gender', $this->__get('user_gender'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Função para editar a senha do usuário.
   * @access public
   */
  public function updatePassword()
  {
    $query = 'update tb_user set user_password = :password where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':password', $this->__get('user_password'));
    $stmt->bindValue(':id', $this->__get('id'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Salvar carteira.
   * A função salva uma carteira automática para o usuário quando ele faz o seu 
   * cadastro.
   * @access public
   * @return boolean
   */
  public function saveWallet() 
  {
    $query = 'insert into tb_wallets set id_user = :id_user, wallet_name = :wallet';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_user', $this->__get('id'));
    $stmt->bindValue(':wallet', $this->__get('wallet_name'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Verifica o e-mail.
   * A função verifica se o e-mail informado pelo usuário é valido.
   * @access public
   * @return boolean
   */
  public function validateUser() 
  {
    if(!(filter_var($this->__get('user_email'), FILTER_VALIDATE_EMAIL))) 
    {
      return false;
    }

    return true;
  }

  /**
   * Função para capturar os dados do usuário pelo id.
   * @access public
   * @return array
   */
  public function getUserId()
  {
    $query = 'select * from tb_user where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * Validar usuário.
   * A função verifica se já existe um usuário cadastrado com o e-mail informado.
   * @access public
   * @return array
   */
  public function getUserEmail() 
  {
    $query = 'select * from tb_user where user_email = :email';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':email', $this->__get('user_email'));
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

   /**
   * Verifica se o usuário confirmou o e-mail.
   * @access public
   * @return array
   */
  public function getUserHashConfirm() 
  {
    $query = 'select id,user_confirmed from tb_user where user_confirm = :user_confirm';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':user_confirm', $this->__get('user_confirm'));
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * Confirmação do usuário.
   * Função faz a confirmação do usuário quando ele abrir o e-mail.
   * @access public
   * @return boolean
   */
  public function userUpdateConfirmed() 
  {
    $query = 'update tb_user set user_confirmed = 1 where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Modifica a hash do banco de dados.
   * A função modifica a hash do banco de dados para salvar a nova hash de 
   * solicitação para efetuar a modificação da senha.
   * @access public
   * @return boolean
   */
  public function changeTokenPassword() 
  {
    $query = 'update tb_user set user_changepassword = :user_changepassword where user_email = :email';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':user_changepassword', $this->__get('user_changepassword'));
    $stmt->bindValue(':email', $this->__get('user_email'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Modificar senha.
   * A função faz a modificação da senha solicitada pelo usuário.
   * @access public
   * @return boolean
   */
  public function changePassword() 
  {
    $query = "
    update 
      tb_user set user_changepassword = 0, user_password = :user_password 
    where 
      user_changepassword = :user_changepassword";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':user_password', $this->__get('user_password'));
    $stmt->bindValue(':user_changepassword', $this->__get('user_changepassword'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }
}

?>