<?php
// namespace para serem importados no Controllers
namespace App\Models;

// namespace para importar a conexão ao banco de dados
use MF\Model\Model;

class Expense extends Model
{
  private $id;
  private $id_wallet;
  private $status;
  private $description;
  private $value;
  private $date;
  private $lastDate;
  private $category;
  private $enrollment;
  private $parcel;
  private $parcelPay;
  private $statusParcelFixed;
  private $id_parcel;

  public function __get($attribute) 
  {
    return $this->$attribute;
  }
  
  public function __set($attribute, $value) 
  {
    $this->$attribute = $value;
  }


  /**
   * A função insere uma nova despesa no banco de dados.
   * @access public
   * @return array com a id da última inserção.
   */
  public function insert()
  {
    $query = '
    insert into 
      tb_expenses
    set 
      id_wallet=:id_wallet, status=:status, description=:description,
      value=:value, date=:date, category=:category, enrollment=:enrollment,
      n_parcel=:parcel, n_parcel_pay = :parcelPay, status_parcel_fixed = :fixed,
      id_parcel = :id_parcel
    ';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':status', $this->__get('status'));
    $stmt->bindValue(':description', $this->__get('description'));
    $stmt->bindValue(':value', $this->__get('value'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':category', $this->__get('category'));
    $stmt->bindValue(':enrollment', $this->__get('enrollment'));
    $stmt->bindValue(':parcel', $this->__get('parcel'));
    $stmt->bindValue(':parcelPay', $this->__get('parcelPay'));
    $stmt->bindValue(':fixed', $this->__get('statusParcelFixed'));
    $stmt->bindValue(':id_parcel', $this->__get('id_parcel'));
    $stmt->execute();

    $query = 'select LAST_INSERT_ID() as id';
    $stmt = $this->conexao->prepare($query);
    $stmt->execute();
    
    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }


  /**
   * 
   */
  public function filterMonth()
  {
    $query = '
    select 
      * 
    from 
      tb_expenses 
    where 
      id_wallet = :id_wallet and date between :date and :lastDate
    order by date asc
    ';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }


  public function sumMonth()
  {
    $paymentData = array();

    $query = "
    select 
      sum(value)
    from 
      tb_expenses 
    where 
      id_wallet = :id_wallet and status = 1 and date between :date and :lastDate";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->execute();
    
    $paymentData['ExpensesPayme'] = number_format($stmt->fetchColumn(),2,',','.');

    $query = "
    select
      sum(value)
    from 
      tb_expenses 
    where 
      id_wallet = :id_wallet and status = 0 and date between :date and :lastDate";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->execute();
    $paymentData['ExpensesNotPayme'] = number_format($stmt->fetchColumn(),2,',','.');

    return $paymentData;
  }

  /**
   * Função para remover as receitas.
   * @access public
   * @return true
   */
  public function remove()
  {
    $query = 'delete from tb_expenses where id = :id or id_parcel = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return true;
  }

  /**
   * Função para fazemos futuras edições nas despesas.
   * @access public
   * @return true
   */
  public function update()
  {
    $query = '
    update 
      tb_expenses
    set 
      date = :date, description = :description, value = :value, id_wallet = :id_wallet, 
      category = :category, id_parcel = :id_parcel
    where 
      id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':description', $this->__get('description'));
    $stmt->bindValue(':value', $this->__get('value'));
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':category', $this->__get('category'));
    $stmt->bindValue(':id_parcel', $this->__get('id_parcel'));
    $stmt->execute();

    $query = '
    update 
      tb_expenses 
    set 
      description = :description, value = :value, id_wallet = :id_wallet, 
      category = :category, id_parcel = :id_parcel
    where
      id_parcel = :id_parcel';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':description', $this->__get('description'));
    $stmt->bindValue(':value', $this->__get('value'));
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':category', $this->__get('category'));
    $stmt->bindValue(':id_parcel', $this->__get('id_parcel'));
    $stmt->execute();

    return true;
  }

  /**
   * Função para concluar as despesas.
   * @access public
   * @return true
   */
  public function conclude()
  {
    $query = 'update tb_expenses set status = 1 where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return true;
  }
}
?>