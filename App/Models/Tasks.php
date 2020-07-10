<?php
// namespace para serem importados no Controllers
namespace App\Models;

// namespace para importar a conexão ao banco de dados
use MF\Model\Model;

class Tasks extends Model
{
  private $id;
  private $id_wallet;
  private $status;
  private $date;
  private $lastDate;
  private $description;
  

  public function __get($attribute) 
  {
    return $this->$attribute;
  }
  
  public function __set($attribute, $value) 
  {
    $this->$attribute = $value;
  }

  /**
   * Função para inserir novas tarefas.
   * @access public
   * @return true
   */
  public function insert()
  {
    $query = '
    insert into 
      tb_tasks 
    set 
      id_wallet = :id_wallet, status = :status, date = :date, 
      description = :description';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':status', $this->__get('status'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':description', $this->__get('description'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Função para retornamos as tarefas do mês.
   * Usada quando o usuário entra na layout de tarefas.
   * @access public
   * @return array $stmt com todas as tarefas do mês.
   */
  public function filterMonth()
  {
    $query = '
    select 
      * 
    from 
      tb_tasks 
    where 
      id_wallet = :id_wallet and date between :date and :lastDate
    order by date asc';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Função para remover as tarefas.
   * @access public
   * @return true
   */
  public function remove()
  {
    $query = 'delete from tb_tasks where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Função para concluar as tarefas.
   * @access public
   * @return true
   */
  public function conclude()
  {
    $query = 'update tb_tasks set status = 1 where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }
}

?>