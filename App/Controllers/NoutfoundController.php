<?php
namespace App\Controllers;

// Recursos
use MF\Controller\Action;

class NoutfoundController extends Action 
{
	public function index() 
	{
		$this->render('notFound404');
	}
}

?>