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
   * Retornar a despesa localizada pelo id da despesa.
   * @access public
   * @return array com os dados da despesa.
   */
  public function filterId()
  {
    $query = 'select * from tb_expenses where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * A função retorna todas as despesas.
   * @access public
   * @return array com todas as despesas registradas no banco de dados.
   */
  public function filterAll()
  {
    $query = '
    select 
      * 
    from 
      tb_expenses 
    where 
      id_wallet = :id_wallet
    order by date asc';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * A função retorna as despesas que possuem o mesmo id_parcel.
   * @access public
   * @return array com todas as despesas que contém o mesmo id_parcel.
   */
  public function filterIdParcel()
  {
    $query = 'select * from tb_expenses where id_parcel = :id_parcel';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_parcel', $this->__get('id_parcel'));
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
      id, status, category, description, value, date, enrollment, n_parcel, 
      n_parcel_pay, status_parcel_fixed
    from 
      tb_expenses 
    where
      id_wallet = :id_wallet and date between :date and :lastDate and 
      category like :category and status like :status and 
      enrollment like :enrollment
    order by date asc";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->bindValue(':category', '%'.$this->__get('category').'%');
    $stmt->bindValue(':status', '%'.$this->__get('status').'%');
    $stmt->bindValue(':enrollment', '%'.$this->__get('enrollment').'%');
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
      id, description, date, value 
    from 
      tb_expenses 
    where 
      id_wallet = :id_wallet and date <= :date and status = 0';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Função para retornamos as despesas do mês.
   * Usada quando o usuário entra na layout de despesas.
   * @access public
   * @return array $stmt com todas as despesas do mês.
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
    order by date asc';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Função para efetuarmos a soma das despesas pagas e a pagar.
   * @access public
   * @return array $paymentData com as depesas pagas e as que faltam pagar
   */
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
   * A função retornas todos os desespas pagas e não pagas.
   * @access public
   * @return array com dois elementos, conta pagas e contas não pagas.
   */
  public function sumAll()
  {
    $paymentData = array();

    $query = "
    select 
      sum(value)
    from 
      tb_expenses 
    where 
      id_wallet = :id_wallet and status = 1";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();
    
    $paymentData['ExpensesPayme'] = number_format($stmt->fetchColumn(),2,',','.');

    $query = "
    select
      sum(value)
    from 
      tb_expenses 
    where 
      id_wallet = :id_wallet and status = 0";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();
    $paymentData['ExpensesNotPayme'] = number_format($stmt->fetchColumn(),2,',','.');

    return $paymentData;
  }

  /**
   * Função para retornar as despesas pagas para o gráfico.
   * @access public
   * @return array
   */
  public function sumGraphicDashboard()
  {
    $query = '
    WITH days AS (
    SELECT DATE_ADD(:date, INTERVAL rnk DAY) day 
      FROM (SELECT row_number() over() -1 rnk 
      FROM information_schema.columns LIMIT 31) gerado)   
    
    SELECT 
      days.day, 
      IFNULL(sum(expenses.value), 0) as amount
    FROM 
      days
      LEFT JOIN tb_expenses as expenses ON expenses.date = days.day 
      and expenses.id_wallet = :id_wallet
      and expenses.status = :status
    WHERE days.day BETWEEN :date AND :lastDate
    group by days.day
    ORDER BY days.day';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':status', $this->__get('status'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Função para efetuar a soma das despesas para o gráfico detalhado.
   * @access public
   * @return array
   */
  public function sumGraphicDetailed()
  {
    $query = '
    select 
      expenses.category,
      sum(expenses.value) as sumGraphic
    from 
      tb_expenses as expenses
    where 
      date between :date and :lastDate 
      and id_wallet = :id_wallet 
      and status = :status
    group by expenses.category
    ORDER BY expenses.category';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':status', $this->__get('status'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Função para remover as despesas.
   * @access public
   * @return boolean
   */
  public function remove()
  {
    $query = 'delete from tb_expenses where id = :id or id_parcel = :id';
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
   * A função faz duas edições, uma edição para a despesa pai e outra para as 
   * despesas filhos, ou seja, os filhos são as parcelas fixas ou parcelado.
   * @access public
   * @return boolean
   */
  public function update()
  {
    $query = '
    update 
      tb_expenses
    set 
      date = :date, description = :description, value = :value, id_wallet = :id_wallet, 
      category = :category, id_parcel = :id_parcel, status = :status
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
    $stmt->bindValue(':status', $this->__get('status'));
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
    if($stmt->execute())
    {
      return true;
    }
    return false;
  }

  /**
   * Função para concluar as despesas.
   * @access public
   * @return boolean
   */
  public function conclude()
  {
    $query = 'update tb_expenses set status = 1 where id = :id';
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