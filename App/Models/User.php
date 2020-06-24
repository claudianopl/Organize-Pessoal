<?php
// namespace para serem importados no Controllers
namespace App\Models;

// namespace para importar a conexão ao banco de dados
use MF\Model\Model;

class User extends Model{
  private $id;
  private $user_name;
  private $user_surname;
  private $user_gender;
  private $user_email;
  private $user_password;
  private $user_confirm;
  private $user_confirmed;

  public function __get($attribute) {
    return $this->$attribute;
  }

  public function __set($attribute, $value) {
    $this->$attribute = $value;
  }

  // Salvar usuário no banco de dados
  public function saveUser() {
    $query = 'insert into tb_user set user_name=:name, user_surname=:surname,
    user_email=:email, user_password=:password, user_confirm=:confirm';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':name', $this->__get('user_name'));
    $stmt->bindValue(':surname', $this->__get('user_surname'));
    $stmt->bindValue(':email', $this->__get('user_email'));
    $stmt->bindValue(':password', md5($this->__get('user_password')));
    $stmt->bindValue(':confirm', $this->__get('user_confirm'));
    $stmt->execute();

    return true;
  }

  // Validar usuário
  public function validateUser() {
    $validate = true;

    if(!(filter_var($this->__get('user_email'), FILTER_VALIDATE_EMAIL))) {
      $validate = false;
    }

    return $validate;
  }

  // Verificar se usuário já existe no banco de dados
  public function getUserEmail() {
    $query = 'select * from tb_user where user_email = :email';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':email', $this->__get('user_email'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}

?>