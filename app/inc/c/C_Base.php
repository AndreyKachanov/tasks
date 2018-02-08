<?php 

// Базовый контроллер
abstract class C_Base extends C_Controller 
{
	protected $title;	// заголовок страницы
	protected $content; // содержание страницы
	protected $needlogin; // нужна ли авторизация
	protected $user; //текущий пользователь
	protected $keywords;
	protected $description;
	protected $styles;
	protected $scripts;
	protected $template; //базовый шаблон
	protected $adminlink; // метка для скрытых разделов

	public function __construct() 
	{
		$this->needlogin = false;
		$this->user = M_Users::Instance()->Get();
		$this->adminlink = false;		
		$this->keywords = '';
		$this->description = '';
		$this->template = 'v_main.php';
		$this->styles = [
			'../libs/bootstrap-4.0.0/dist/css/bootstrap.min', 
			'main.min'
		];		
		$this->scripts = [
			'jquery.min',
			'jquery.tablesorter.min',
			'../libs/bootstrap-4.0.0/dist/js/bootstrap.min',
			'../libs/ckeditor/ckeditor',
			'scripts.min'
		];
	}	

	protected function before() 
	{
		if($this->needlogin && $this->user === null)
			$this->Redirect('/login');

		if (isset($this->user))
			$this->adminlink = true;

		$this->title = "Main | Менеджер задач";
		$this->content = '';
	}

	// Генерация базового шаблона
	public function render() 
	{
		$vars = [
					'title' => $this->title, 
					'content' => $this->content,
					'user' => $this->user['login'],
					'keywords' => $this->keywords,
					'description' => $this->description,
					'styles' => $this->styles,
					'scripts' => $this->scripts,
					'adminlink'=> $this->adminlink
		];

		$page = $this->template("inc/v/base_templates/{$this->template}", $vars);
		echo $page;
	}
}