<?php 

// Базовый класс контроллера

abstract class C_Controller 
{
	// массив с параметрами - аналог с $_GET
	protected $params;
	// Генерация внешнего шаблона
	protected abstract function render();

	// Функция отрабатывающая до основного метода
	protected abstract function before();

	public function Go($action, $params) 
	{
		$this->params = $params;
		$this->before();
		$this->$action();
		$this->render();
	}

	// Запрос произведен методом GET?
	protected function IsGet() 
	{
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}

	// Запрос произведен методом POST?
	protected function IsPost() 
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	// Запрос произведен с помощью AJAX?
	protected function IsAjax() 
	{
		return ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
					strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	// в случае запроса на несуществующую страницу отправляем заголовок 404 ошибки
	protected function Redirect($url) 
	{
		if ($url == '404') {
			header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
			require_once "inc/v/v_404.php";
			die;	
		} 

		header("Location: $url");
		die;		
	}

	// Генерация HTML шаблона в строку

	protected function template($fileName, $vars = []) 
	{
		
		// Перегонка ключей массива в переменные
		foreach ($vars as $key => $value) {
			$$key = $value;
		}

		// Генерация HTML в строку
		ob_start();
		require_once "$fileName";
		return ob_get_clean();
	}

	// Если вызван метод, которого нет
	public function __call($name, $params) 
	{
		$this->p404();
	}

	public function p404() 
	{
		$c = new C_Tasks();
		$c->Go('action_404', []);
		die;
	}	
}