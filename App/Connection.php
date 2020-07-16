<?php
namespace App;

class Connection {
  public static function conectar() {
    try {
      $host = 'localhost';
      $dbname = 'organize_pessoal';
      $user = 'root';
      $pass = 'root';

      $conexao = new \PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        "$user",
        "$pass"
      );
      return $conexao;
    } catch(\PDOException $e) {
      echo "Error: ".$e;
    }
  }
}


?>