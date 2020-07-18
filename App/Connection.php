<?php
namespace App;

class Connection {
	public static function conectar() {
		try {
			$host = 'us-cdbr-east-02.cleardb.com';
			$dbname = 'heroku_9a7279bbaa6e8fb';
			$user = 'be1b64c43bd0f8';
			$pass = '7619a2a3';

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