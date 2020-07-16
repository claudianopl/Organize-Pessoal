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
   * @return boolean
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
   * 
   */
  public function filterId()
  {
    $query = 'select * from tb_tasks where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * A função retorna todas as tarefas.
   * @access public
   * @return array com todas as tarefas registradas no banco de dados.
   */
  public function filterAll()
  {
    
    $query = '
    select 
      * 
    from 
      tb_tasks 
    where 
      id_wallet = :id_wallet
    order by date asc';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * A função filtra os dados do banco que o usuário solicitou.
   * @access public
   * @return array com todos os dados solicitados pelo usuário.
   */
  public function filter()
  {
    $query = "
    select 
      *
    from 
      tb_tasks 
    where
      id_wallet = :id_wallet and date between :date and :lastDate and 
      status like :status
    order by date asc";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->bindValue(':status', '%'.$this->__get('status').'%');
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Filtra todas as despesas pendentes até o mês atual.
   * @access public
   * @return array
   */
  public function filterDashboard()
  {
    $query = '
    select 
      id, description, date
    from 
      tb_tasks
    where 
      id_wallet = :id_wallet and date <= :date and status = 0';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
   * @return boolean
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
   * Função para fazemos futuras edições nas despesas.
   * @access public
   * @return boolean
   */
  public function update()
  {
    $query = '
    update 
      tb_tasks
    set 
      date = :date, description = :description, id_wallet = :id_wallet, 
      status = :status
    where
      id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':description', $this->__get('description'));
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':status', $this->__get('status'));
    if($stmt->execute())
    {
      return true;
    }
    print_r($stmt->errorInfo());
    return false;
  }

  /**
   * Função para concluar as tarefas.
   * @access public
   * @return boolean
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