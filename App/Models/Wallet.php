<?php
// namespace para serem importados no Controllers
namespace App\Models;

// namespace para importar a conexão ao banco de dados
use MF\Model\Model;

class Wallet extends Model 
{
  private $id;
  private $id_user;
  private $wallet_name;

  public function __get($attribute) 
  {
    return $this->$attribute;
  }
  
  public function __set($attribute, $value) 
  {
    $this->$attribute = $value;
  }

  /**
   * 
   */
  public function insert()
  {
    $query = 'insert into tb_wallets set id_user = :id_user, wallet_name = :wallet_name';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue('id_user', $this->__get('id_user'));
    $stmt->bindValue('wallet_name', $this->__get('wallet_name'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Retornando as carteiras existente no id do usuário.
   * @access public
   * @return array todas as carteiras que o usuário criou.
   */
  public function getUserWallet() 
  {
    $query = 'select * from tb_wallets where id_user = :id_user';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_user', $this->__get('id_user'));
    $stmt->execute();
    
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}
?>