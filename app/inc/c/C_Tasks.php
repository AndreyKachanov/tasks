<?php 

	class C_Tasks extends C_Base 
	{
		public function __construct() 
		{
			parent::__construct();
		}

		protected function before() 
		{
			// Если needlogin = true - будет выполняться проверка для всего контроллера(т.е. для всех методов) 
			// авторизован пользователь или нет
			// $this->needlogin = true;

			parent::before();
		}	

		public function action_index() 
		{
			$this->action_page();	
		}

		public function action_page() 
		{
			$page_num = isset($this->params[2]) ? (int)$this->params[2] : 1;

			if (!$page_num)
				$this->p404();

			// создание объекта постраничной навигации (обязательные параметры)
			$mPagination = new M_Pagination(TABLE_PREFIX . 'tasks', '/page/');

			// задание свойств объекта
			$res = $mPagination->fields(TABLE_PREFIX  . 'tasks.*')
								->order_by('id_task DESC')							
								->on_page(3)->page_num($page_num)->page();
			if(!$res)
				$this->p404();							

			foreach ($res as $task) {
				$task['intro'] = M_Tasks::Instance()->intro($task);
				$tasks[] = $task;
			}
						
			$this->keywords = 'менеджер задач, todo-менеджер, task-менеджер, задачник, приложение-задачник';
			$this->description = 'менеджер задач';
			
			// генерация пагинации
			$navbar = $this->template('inc/v/v_navbar.php', ['navparams' => $mPagination->navparams()]);
			// генерация контента страницы
			$this->content = $this->template('inc/v/tasks/v_index.php', 
			[
				'tasks' => $tasks,
				'navbar' => $navbar,
				'navparams' => $mPagination->navparams(),
				'adminlink' => $this->adminlink
			]);			
		}

		public function action_add() 
		{		
			$mTasks = M_Tasks::Instance();
			$fields = [];
			$errors = [];
			
			if ($this->IsPost()) {

				if ($mTasks->add(array_merge($_POST, $_FILES))) 
					$this->Redirect('/');
	
				$errors = $mTasks->errors();
				$fields = $_POST;	
			}

			$this->title = " Менеджер задач | Добавить задачу";
			$this->content = $this->template('inc/v/tasks/v_add.php', 
			[	
			    'errors' => $errors, 
			    'fields' => $fields
			]);		
		}		

		public function action_get() 
		{
			$id_task = (isset($this->params[2])) ? (int)$this->params[2] : null;

			// если id не введен или params > 3
			if (!$id_task || count($this->params)>3)
				$this->p404();
			
			$mTasks = M_Tasks::Instance();

			$task = $mTasks->get($id_task);

			if (!$task)
				$this->p404();

			$this->title = 'Просмотр задачи';
			$this->content = $this->template('inc/v/tasks/v_task.php', [
				'task' => $task
			]); 		
		}

		public function action_edit()
		{
			// авторизован ли пользователь?
			if (!$this->user)
				$this->p404();

			$id_task = (isset($this->params[1])) ? (int)$this->params[1] : null;

			if ($id_task == null)
				$this->p404();

			$fields = [];
			$errors = [];				

			$mTasks = M_Tasks::Instance();

			if (!empty($_POST['delete'])) {
				$mTasks->delete($id_task);
				$this->Redirect('/');

			} elseif(!empty($_POST['save'])) {
				if($mTasks->edit($id_task, $_POST))
					$this->Redirect('/');

				$errors = $mTasks->errors();
				$fields = $_POST;			
			} else {

				$fields = $mTasks->get($id_task);
				if (!$fields)
					$this->p404();
			}

			$this->title = "Редактирование задачи";					
			$this->content = $this->template('inc/v/tasks/v_edit.php', 
			[	
			    'errors' => $errors, 
			    'fields' => $fields,
			    'status' => ['Не определено', 'Выполнено', 'Выполняется', 'Просрочено']
			]);
		}

		public function action_delete() 
		{
			if (!$this->user)
				$this->p404();
							
			if($this->isGet()) 
			{			
				M_Tasks::Instance()->delete($this->params[2]);
				$this->Redirect('/');
			}
		}				

		// превью перед сохранением формы - работает через AJAX
		public function action_preview()
		{
			$mTasks = M_Tasks::Instance();
			// если идет ajax запрос
			if( $this->IsAjax() ) {

				if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
					$file = $_FILES['file'];
					$img = M_Tasks::Instance()->checkSession($file);
				} 

			$task = [];
			$task['author'] = $_POST['author'];
			$task['email'] = $_POST['email'];
			$task['status'] = 'Не определено';
			$task['img'] = $img ?? null;
			$task['content'] = $_POST['content'];				
			echo $this->content = $this->template('inc/v/tasks/v_preview.php', ['task' => $task]);
			die;
			}
			
			$this->p404();						
		}

		public function action_404()
		{
			$this->title = '404 - Not Found';
			$this->content = $this->template('inc/v/v_404.php');
		}
		
	}