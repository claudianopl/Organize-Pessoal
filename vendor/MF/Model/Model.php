<?php
namespace MF\Model;

class Model {
  protected $conexao;

  public function __construct(\PDO $conexao) {
    $this->conexao = $conexao;
  }
}
?>