<?php
namespace App\Controllers;
// Recursos
use MF\Controller\Action;
use MF\Model\Container;

// Models


class HomeController extends Action {
	
	public function index() {
		$this->render('index', 'layout');
	}
	
}

?>