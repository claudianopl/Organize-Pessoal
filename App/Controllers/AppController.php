<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

// Models


class AppController extends Action {
	
	public function Index() {
		$this->render('index');
	}

	public function Receive() {
		$this->render('receive');
	}

	public function Expense() {
		$this->render('expense');
	}

	public function Tasks() {
		$this->render('tasks');
	}
	
}

?>