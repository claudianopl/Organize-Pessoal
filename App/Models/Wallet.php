<?php
// namespace para serem importados no Controllers
namespace App\Models;

// namespace para importar a conexão ao banco de dados
use MF\Model\Model;

class Wallet extends Model 
{
  private $id_wallet;
  private $id_user;
  private $wallet_name;
  private $year;

  public function __get($attribute) 
  {
    return $this->$attribute;
  }
  
  public function __set($attribute, $value) 
  {
    $this->$attribute = $value;
  }

  /**
   * Função para inserir as novas carteiras do usuário.
   * @access public
   * @return boolean
   */
  public function insert()
  {
    $query = 'insert into tb_wallets set id_user = :id_user, wallet_name = :wallet_name';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_user', $this->__get('id_user'));
    $stmt->bindValue(':wallet_name', $this->__get('wallet_name'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Função para retornar a soma anual das receitas e despesas.
   */
  public function annualSum()
  {
    $query = '
    select 
      month.id as month,
      IFNULL(sum(received.value), 0) as amount
    from tb_month as month
    left join tb_received as received
      on(month(received.date) = month.id) 
      and received.id_wallet = :id_wallet
      and year(received.date) = :year
    group by month.id
    order by month asc;';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':year', $this->__get('year'));
    $stmt->execute();

    $report['received'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $query = '
    select 
      month.id as month,
      IFNULL(sum(expenses.value), 0) as amount
    from tb_month as month
    left join tb_expenses as expenses
      on(month(expenses.date) = month.id) 
      and expenses.id_wallet = :id_wallet
      and year(expenses.date) = :year
    group by month.id
    order by month asc;';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':year', $this->__get('year'));
    $stmt->execute();

    $report['expenses'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $report;

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