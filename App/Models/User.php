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
  private $user_changepassword;

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
    $stmt->bindValue(':password', $this->__get('user_password'));
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

  public function getUserHashConfirm() {
    $query = 'select id,user_confirmed from tb_user where user_confirm = :user_confirm';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':user_confirm', $this->__get('user_confirm'));
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function userUpdateConfirmed() {
    $query = 'update tb_user set user_confirmed = 1 where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return true;
  }

  public function changeTokenPassword() {
    $query = 'update tb_user set user_changepassword = :user_changepassword where user_email = :email';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':user_changepassword', $this->__get('user_changepassword'));
    $stmt->bindValue(':email', $this->__get('user_email'));
    $stmt->execute();

    return true;
  }

  public function changePassword() {
    $query = "
    update 
      tb_user set user_changepassword = 0, user_password = :user_password 
    where 
      user_changepassword = :user_changepassword";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':user_password', $this->__get('user_password'));
    $stmt->bindValue(':user_changepassword', $this->__get('user_changepassword'));
    $stmt->execute();

    return true;
  }
}

?>