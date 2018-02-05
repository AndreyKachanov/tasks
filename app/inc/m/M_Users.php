<?php

// Модель для работы с пользователями

class M_Users extends M_Model
{
	private static $instance;	// экземпляр класса
	private $sid;				// идентификатор текущей сессии
	private $uid;				// идентификатор текущего пользователя

	private $tab_name = TABLE_PREFIX . "users"; //имя таблицы users с учетом префикса		

	public static function Instance()
	{
		if (self::$instance == null)
			self::$instance = new self();
			
		return self::$instance;
	}

	//
	// Конструктор
	//
	public function __construct()
	{
		parent::__construct($this->tab_name, 'id_user');
		$this->sid = null;
		$this->uid = null;	
	}			

	//
	// Очистка неиспользуемых сессий
	// 
	public function ClearSessions()
	{
		$min = date('Y-m-d H:i:s', time() - 60 * 20); 			
		$t = "time_last < '%s'";
		$where = sprintf($t, $min);
		$this->db->Delete(TABLE_PREFIX . 'sessions', $where);
	}

	//
	// Авторизация
	// $login 		- логин
	// $password 	- пароль
	// $remember 	- нужно ли запомнить в куках
	// результат	- true или false
	//
	public function Login($login, $password, $remember = true)
	{
		// вытаскиваем пользователя из БД 
		$user = $this->GetByLogin($login);
		
		if ($user == null)
			return false;
		
		$id_user = $user['id_user'];
				
		// проверяем пароль и роль пользователя
		if ($user['password'] != $this->hash($password))
			return false;
				
		// запоминаем имя и md5(пароль)
		if ($remember)
		{
			// установить куки на 100 дней
			$expire = time() + 3600 * 24 * 100;
			setcookie('login', $login, $expire, BASE_URL);
			setcookie('password', $this->hash($password), $expire, BASE_URL);
		}		
				
		// открываем сессию и запоминаем SID
		$this->sid = $this->OpenSession($id_user);
		
		return true;
	}

	//
	// Выход
	//
	public function Logout()
	{
		setcookie('login', '', time() - 1, BASE_URL);
		setcookie('password', '', time() - 1, BASE_URL);
		unset($_COOKIE['login']);
		unset($_COOKIE['password']);
		unset($_SESSION['sid']);
		// ----------------		
		$this->sid = null;
		$this->uid = null;
	}	

	public function Get($id_user = null)
	{	
		// Если id_user не указан, берем его по текущей сессии.
		if ($id_user == null)
			$id_user = $this->GetUid();
			
		if ($id_user == null)
			return null;
			
		// А теперь просто возвращаем пользователя по id_user.
		$t = "SELECT * FROM $this->tab_name WHERE id_user = '%d'";
		$query = sprintf($t, $id_user);
		$result = $this->db->Select($query);


		return $result[0];		
	}	
						
	//
	// Получение id текущего пользователя
	// результат	- UID
	//
	public function GetUid()
	{	
		// Проверка кеша.
		if ($this->uid != null)
			return $this->uid;	

		// Берем по текущей сессии.
		$sid = $this->GetSid();
				
		if ($sid == null)
			return null;
			
		$result = $this->db->Select("SELECT id_user FROM " . TABLE_PREFIX . "sessions WHERE sid = '$sid'");
				
		// Если сессию не нашли - значит пользователь не авторизован.
		if (count($result) == 0)
			return null;
			
		// Если нашли - запоминм ее.
		$this->uid = $result[0]['id_user'];
		return $this->uid;
	}
	
	
	// Получает пользователя по логину
	
	public function GetByLogin($login)
	{	

		$result = $this->db->Select("SELECT * FROM $this->tab_name WHERE login = '$login'");

		if (isset($result[0])) 
			return $result[0];
		
	}
			
	public function hash($str) {
		return md5(md5($str . HASH_KEY));
	}

	//
	// Функция возвращает идентификатор текущей сессии
	// результат	- SID
	//
	private function GetSid()
	{
		// Проверка кеша.
		if ($this->sid != null)
			return $this->sid;
	
		// Ищем SID в сессии.

		if ( isset($_SESSION['sid']) ) {
			$sid = $_SESSION['sid'];
		} else {
			$sid = null;
		}
								
		// Если нашли, попробуем обновить time_last в базе. 
		// Заодно и проверим, есть ли сессия там.
		if ($sid != null)
		{
			$session = [];
			$session['time_last'] = date('Y-m-d H:i:s'); 			

			$where = "sid = '$sid'";			
			$affected_rows = $this->db->Update(TABLE_PREFIX . 'sessions', $session, $where);

			if ($affected_rows == 0)
			{	
				$query = "SELECT count(*) FROM " . TABLE_PREFIX . "sessions WHERE sid = '$sid'";
				$result = $this->db->Select($query);
				
				if ($result[0]['count(*)'] == 0)
					$sid = null;			
			}			
		}		
		
		// Нет сессии? Ищем логин и md5(пароль) в куках.
		// Т.е. пробуем переподключиться.
		if ($sid == null && isset($_COOKIE['login']))
		{
			$user = $this->GetByLogin($_COOKIE['login']);
			
			if ($user != null && $user['password'] == $_COOKIE['password'])
				$sid = $this->OpenSession($user['id_user']);
		}
		
		// Запоминаем в кеш.
		if ($sid != null)
			$this->sid = $sid;
		
		// Возвращаем, наконец, SID.
		return $sid;		
	}
	
	//
	// Открытие новой сессии
	// результат	- SID
	//
	private function OpenSession($id_user)
	{
		// генерируем SID
		$sid = $this->GenerateStr(10);
				
		// вставляем SID в БД
		$now = date('Y-m-d H:i:s'); 
		$session = [];
		$session['id_user'] = $id_user;
		$session['sid'] = $sid;
		$session['time_start'] = $now; 
		$session['time_last'] = $now;				
		$this->db->Insert(TABLE_PREFIX . 'sessions', $session); 
				
		// регистрируем сессию в PHP сессии
		$_SESSION['sid'] = $sid;				
				
		// возвращаем SID
		return $sid;	
	}

	//
	// Генерация случайной последовательности
	// $length 		- ее длина
	// результат	- случайная строка
	//
	private function GenerateStr($length = 10) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;  

		while (strlen($code) < $length) 
            $code .= $chars[mt_rand(0, $clen)];  

		return $code;
	}						
}	