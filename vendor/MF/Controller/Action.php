<?php
namespace MF\Controller;

abstract class Action {
	protected $view;

	/*
	* o $this->view vai armazenar os dados do model dentro de uma array, e no render ele transforma os indices da array em variável
	*/
	
	public function __construct() {
		$this->view = new \stdClass();
	}

	// Apresentando o view com layout
	public function render($view, $layout = '') {
		$this->view->page = $view;
		if(file_exists("../App/Views/".$layout.".phtml")) {
			require_once "../App/Views/".$layout.".phtml";
		} else {
			$this->content();
		}
	}
	
	/*
	* Para rodar as view com as informações futuras do model
	* Não se preocupar tanto com o render, basta copiar e colar nas class controllers futuras
	*/
	
	protected function content() {
		if(isset($this->view->dados)) {
			extract($this->view->dados);
		}

		$classAtual = get_class($this);
		
		$classAtual = str_replace('App\\Controllers\\', '', $classAtual);

		$classAtual = strtolower(str_replace('Controller', '', $classAtual));
		
		require_once "../App/Views/".$classAtual."/".$this->view->page.".phtml";
	}
}

?>