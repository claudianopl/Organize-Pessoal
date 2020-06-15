<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

// Models


class AppController extends Action {
	
	public function index() {
		$this->render('index');
	}

	public function receive() {
		$this->render('receive');
		if(isset($_POST) && !(empty($_POST))) {
			$receive = $_POST['receive'];
			$category = $_POST['category'];
			$retorno = ['receita' => $receive, 'categoria' => $category];
			
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
		}
	}
	
}

?>