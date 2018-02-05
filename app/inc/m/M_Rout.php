<?php
	// Модуль роутинга
	class M_Rout 
	{	
		private $controller;
		private $action;
		private $params;
		
		public function __construct($url) 
		{
			$info = explode('/', $url);		
			$this->params = array();

			foreach ($info as $v) {
				if ($v != '')
					$this->params[] = $v;
			}

			$this->action = 'action_';
			$this->action .= ( isset($this->params[1]) ) ? $this->params[1] : 'index';

			// если $params[0] не инициализирована, присваиваем null 
			// $params[0] = (isset($params[0])) ? $params[0] : null ;
			// $params[0] = (isset($params[0])) ?: null;
			$this->params[0] = $this->params[0] ?? null;
		
			switch ($this->params[0]) {	
				case 'tasks':   $this->controller = 'C_Tasks'; break;
				// добавление задачи	
				case 'add':  	   $this->controller = 'C_Tasks'; $this->action = 'action_add'; break;	
				case 'edit':  	   $this->controller = 'C_Tasks'; $this->action = 'action_edit'; break;	
				case 'login':      $this->controller = 'C_Auth'; $this->action = 'action_login'; break;			
					    	
				// null - заходим на главную страницу сайта
				case null: $this->controller = 'C_Tasks';
						   $this->action = 'action_index';  
						   break;

				default: $this->controller = 'C_Tasks';
				$this->action = 'action_404';	
			}		
		}
		
		public function Request() 
		{
			$c = new $this->controller();
			$c->Go($this->action, $this->params);
		}
	}