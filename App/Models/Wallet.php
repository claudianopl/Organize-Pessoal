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
   * Função para retornar as soams das despesas, receitas e tarefas.
   * @access public
   * @return array
   */
  public function balanceDiff()
  {
    $query = ' select sum(value) as receive from tb_received 
    where id_wallet = :id_wallet and status = 1;';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();
    $data['sumReceived'] = $stmt->fetchColumn();

    $query = ' select sum(value) as expense from tb_expenses 
    where id_wallet = :id_wallet and status = 1;';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();
    $data['sumExpenses'] = $stmt->fetchColumn();

    return $data;
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
   * A função para retornar os dados da carteira.
   * A função soma as receitas e despesas que estão com status pagas, separando 
   * por grupo formados com os ids das carteiras.
   * @access public
   * @return array $wallet com o ip, nome da carteira, soma das receitas e despesas
   */
  public function dataSumWallet()
  {
    $query = '
    select 
	    wallet.id as id, 
      wallet.wallet_name as walletName, 
      IFNULL(sum(received.value), 0) as sumReceive
    from 
	    tb_wallets as wallet
    left join 
      tb_received as received on(received.id_wallet = wallet.id) 
      and received.status = 1
    where id_user = :id_user
    group by wallet.id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_user', $this->__get('id_user'));
    $stmt->execute();
    $wallet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $query = '
    select 
      IFNULL(sum(expenses.value), 0) as sumExpenses
    from 
	    tb_wallets as wallet
    left join 
      tb_expenses as expenses on(expenses.id_wallet = wallet.id) 
      and expenses.status = 1
    where id_user = :id_user
    group by wallet.id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_user', $this->__get('id_user'));
    $stmt->execute();
    $expense = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    /**
     * For usado para extrairmos os valores da soma das depesas e adicionar nos 
     * dados das wallets.
     */
    for ($i=0; $i < count($wallet) ; $i++) { 
      $wallet[$i]['sumExpenses'] = $expense[$i]['sumExpenses'];
    }

    return $wallet;
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

  /**
   * Função para remover a carteira e seus dados de receitas, despesas e tarefas.
   * @access public
   * @return boolean
   */
  public function remove()
  {
    $query = '
    SET SQL_SAFE_UPDATES = 0;
    SET foreign_key_checks = 0;
    delete 
      wallet, received, expenses, tasks
    from
      tb_wallets as wallet
    left  join tb_received as received on(wallet.id = received.id_wallet)
    left  join tb_expenses as expenses on(wallet.id = expenses.id_wallet)
    left  join tb_tasks as tasks on(wallet.id = tasks.id_wallet)

    where wallet.id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id_wallet'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }
}
?>