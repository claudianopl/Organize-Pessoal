<?php
namespace MF\Model;

use App\Connection;

class Container {
	// O objetivo é retorna o modelo já instânciado e com a conexão já estabelecida
	public static function getModel($model) {
		// Instanciando o model
		$class = "\\App\\Models\\".ucfirst($model);

		// Conexão com o banco
		$conexao = Connection::conectar();

		return new $class($conexao);
	}
}

?>