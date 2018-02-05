<?php
	//
	// Модуль постраничной навигации
	//
	class M_Pagination
	{
		private $db;	         // ссылка на драйвер БД
		private $on_page;        // количество записей на странице
		private $url_self;       // url адрес от корня без номера с страницы (/pages/all/)
		private $page_num;       // текущая страница
		private $fields;         // какие поля тащить
		private $query = [
			'table' => '',       // имя таблицы
			'join' => '',        // с чем объединять
			'left_join' => '',   // с чем объединять
			'right_join' => '',  // с чем объединять
			'where' => '',       // что брать, что не брать
			'group_by' => '',    // с кем группировать (нужно крайне редко)
			'having' => '',      // что брать, что не брать после группировки (нужно крайне редко)
			'order_by' => ''     // как отсортировать
		];

		//
		// Инициализировать свойства объекта
		//
		public function __construct($table,	$url_self)
		{
			$this->db = M_MSQL::Instance();
			$this->query['table'] = $table;
			$this->fields = '*';
			$this->on_page = 2;
			$this->url_self = $url_self; 
			$this->page_num = 1;
		}

        //
        // Получить список записей из таблицы
        //
        public function page()
        {
            $shift = ($this->page_num - 1) * $this->on_page;
            
            if($shift < 0)
                $shift = 0;

        	$query = "SELECT {$this->fields} FROM {$this->concatenation()} 
                      LIMIT $shift, {$this->on_page}";
        

            return $this->db->Select($query);

        } 
		
		//
		// Получить количество записей в таблице
		//
		public function pagination_count()
		{
			return count($this->db->Select("SELECT * FROM {$this->concatenation()}"));
		}
		
		//
		// Конкатенация строки запроса к БД
		//
		private function concatenation()
		{
			return implode(' ', $this->query);  
		}
		
		//
		// Сформировать данные для Template (v_navbar.php)
		//
		public function navparams()
		{
			$count    = $this->pagination_count();
			$max_page = ceil ($count / $this->on_page); 
			$left     = $this->page_num - 2;
			$right    = $this->page_num + 2;

			while($left <= 0)
			{
				$left++;
				$right++;
			}
			while($right > $max_page)
			{
				$left--;
				$right--;
			}

			//запишем в сеесию текущую страницу чтоб знать куда вернуться при редиректе
			// M_Session::push($this->url_self, $this->page_num);

			return array('on_page'  => $this->on_page,
						 'count'    => $count,
						 'left'     => $left,
						 'right'    => $right,
						 'max_page' => $max_page,
						 'page_num' => $this->page_num,
						 'url_self' => $this->url_self);
		}
		
		//
		// Задать свойства объекта
		//
		
		public function __call($name, $args)
		{
			if (in_array($name, array_keys($this->query)))
				$this->query["$name"] .= strtoupper (str_replace ('_', ' ', $name)) . ' ' . $args[0]; 
			else 
				$this->$name = $args[0];
						
			return $this; 
		}
	}