<?php
// namespace para serem importados no Controllers
namespace App\Models;

// namespace para importar a conexão ao banco de dados
use MF\Model\Model;

class AppData extends Model  {
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

  public function __get($attribute) {
    return $this->$attribute;
  }
  
  public function __set($attribute, $value) {
    $this->$attribute = $value;
  }
  

  /**
   * A função salva os dados da nova receita no banco de dados.
   * @access public
   * @return true
   */
  public function saveReceive() {
    $query = '
    insert into 
      tb_received 
    set 
      id_wallet=:id_wallet, status=:status, description=:description,
      value=:value, date=:date, category=:category, enrollment=:enrollment,
      n_parcel=:parcel, status_parcel_fixed = :fixed
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
    $stmt->bindValue(':fixed', $this->__get('statusParcelFixed'));
    $stmt->execute();
    
    return true;
  }


  /**
   * A função salva os dados da nova receita parceladas no banco de dados.
   * @access public
   * @return true
   */
  public function saveParcelReceived() {
    $query = '
    insert into 
      tb_received 
    set 
      id_wallet=:id_wallet, status=:status, description=:description,
      value=:value, date=:date, category=:category, enrollment=:enrollment,
      n_parcel=:parcel, n_parcel_pay = :parcelPay, status_parcel_fixed = :fixed
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
    $stmt->execute();
    
    return true;
  }


  /**
   * A função retorna todas as receitas a receber do mês atual para trás.
   * @access public
   * @return array com todas as receitas a receber.
   */
  public function filterReceiveAll() {
    $query = '
    select 
      * 
    from 
      tb_received 
    where 
      id_wallet = :id_wallet and date <= :lastdate and status = 0 
    order by date asc
    ';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':lastdate', $this->__get('lastDate'));
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }


  /**
   * A função filtra os dados do banco que o usuário solicitou.
   * @access public
   * @return array com todos os dados solicitados pelo usuário.
   */
  public function filterReceive() {
    $query = "
    select 
      id, status, category, description, value, date, enrollment, n_parcel, 
      n_parcel_pay, status_parcel_fixed
    from 
      tb_received 
    where
      id_wallet = :id_wallet and date between :date and :lastDate and 
      category like :category and status like :status
    order by date asc
    ";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->bindValue(':date', $this->__get('date'));
    $stmt->bindValue(':lastDate', $this->__get('lastDate'));
    $stmt->bindValue(':category', '%'.$this->__get('category').'%');
    $stmt->bindValue(':status', '%'.$this->__get('status').'%');
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }


  /**
   * A função retornas os pagamentos recebidos e não recebidos.
   * @access public
   * @return array com dois elementos, conta recebidas e contas a receber.
   */
  public function sumReceived() {
    $paymentData = array();

    $query = "
    select 
      sum(value)
    from 
      tb_received 
    where 
      id_wallet = :id_wallet and status = 1";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();
    
    $paymentData['paymentReceived'] = number_format($stmt->fetchColumn(),2,',','.');

    $query = "
    select
      sum(value)
    from 
      tb_received 
    where 
      id_wallet = :id_wallet and status = 0";
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id_wallet', $this->__get('id_wallet'));
    $stmt->execute();
    $paymentData['paymentNotReceived'] = number_format($stmt->fetchColumn(),2,',','.');

    return $paymentData;
  }


  /**
   * Função para remover as receitas.
   * @access public
   * @return true
   */
  public function removeReceived() {
    $query = 'delete from tb_received where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return true;
  }

  /**
   * Função para concluar as receitas.
   * @access public
   * @return true
   */
  public function concludeReceived() {
    $query = 'update tb_received set status = 1 where id = :id';
    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':id', $this->__get('id'));
    $stmt->execute();

    return true;
  }
}
?>