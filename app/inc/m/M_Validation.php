<?php
	// Модуль валицаии полей
class M_Validation
{
	private $table;			// имя талицы
	private $rules;			// правила, подгружаемые из rules_map.php
	private $errors;		// массив с константами ошибк валидации
	private $final_object;	// массив, сформированный из переданного, но с очищенными данными
	private $messages_path; //путь к файлу с сообщениями
	//
	// params:
	// 		$table - имя талицы для поиска в rules_map.php
	//
	
	public function __construct($table)
	{
		$this->table = $table;
		$this->rules_path = RULES_PATH;
		$this->messages_path = MESSAGES_PATH;
		$this->rules = $this->load_rules();
		$this->errors = array();
		$this->final_object = array();
		
	}
	
	//
	// params:
	// 		$obj - массив с формы. Ключи - поля в базе, значения - значения :)
	// 		$pk - первичный ключ при редактировании записи. При добавлении равен null.
	//
	public function execute($obj, $pk = null)
	{
		
		$query_tmp = array();
		$pair_for_unique = array();
		$clean_obj = array();
		// 1) выкидываем ненужные поля, используя $this->rules['fileds'] -> формируем первичный $final_object
		
		// 3) trim для $final_object + проверяем пустоту, используя $this->rules['not_empty']
		
		foreach ($obj as $key => $value) {
				
			if (in_array($key, $this->rules['fields'])) { //если dt входит в массив полей
				if (!is_array($value)) { //если не массив
					trim($value);
					$count = iconv_strlen($value, 'UTF-8'); //кол-во символов в строке
				}
				//очищаем данные для последующей передачи
				if(array_key_exists('html_allowed', $this->rules) && !in_array($key, $this->rules['html_allowed'])){

                    $value = htmlspecialchars($value);
                    // clean_obj - массив с обработанными htmlspecialchars значениями. Эти значения не входят в массив html_allowed
					$clean_obj[] = array($key => $value);
				}

				//все представленные здесь правила описаны в файле messages.php
				if(array_key_exists('not_empty', $this->rules) && in_array($key, $this->rules['not_empty'])){ //если dt есть в массиве not_empty
				
					if($value == ''){
						$this->errors[] = ['not_empty', $key];
					}

					else{

						if(array_key_exists('special_rules', $this->rules)){
							
							if(isset($this->rules['special_rules']['illegal_entry']) && $this->rules['special_rules']['illegal_entry'][0] == $key && $value == $pk)
								$this->errors[] = array('illegal_entry', $key);
							/* //раскомментировать для добавления условия нового специального правила
							elseif(условие){
								//запись ошибки в скобках необязательные параметры 
								//учитывать вложенность скобок, те параметр_2 не может быть вызван без вызова параметра_1
								//можно добавлять параметры дописав функцию errors()
								$this->errors[] = array('название правила', (проверяемое поле, (параметр_1|проверочное поле, (параметр_2))));
							} */
						}
						if(array_key_exists('range', $this->rules) && array_key_exists($key, $this->rules['range']) && ($count < $this->rules['range'][$key][0] || $count > $this->rules['range'][$key][1])){
							$this->errors[] = array('range', $key, $this->rules['range'][$key][0], $this->rules['range'][$key][1]);
						
						}
						elseif(array_key_exists('exact_length', $this->rules) && array_key_exists($key, $this->rules['exact_length']) && $count != $this->rules['exact_length'][$key]){
							$this->errors[] = array('exact_length', $key, $this->rules['exact_length'][$key]);
						
						}
						elseif(array_key_exists('min_length', $this->rules) && array_key_exists($key, $this->rules['min_length']) && $count < $this->rules['min_length'][$key]){
							$this->errors[] = array('min_length', $key, $this->rules['min_length'][$key]);
						
						}
						elseif(array_key_exists('max_length', $this->rules) && array_key_exists($key, $this->rules['max_length']) && $count > $this->rules['max_length'][$key]){
							
							$this->errors[] = array('max_length', $key, $this->rules['max_length'][$key]);
							
						}
						elseif(array_key_exists('authorized', $this->rules) && in_array($key, $this->rules['authorized']) && M_Users::Instance()->Get() == null){
						
								$this->errors[] = array('authorized');
						}
						elseif(array_key_exists('date', $this->rules) && in_array($key, $this->rules['date']) && strtotime($value) == FALSE){
							
							$this->errors[] = array('date', $key);
						}
						elseif(array_key_exists('equals', $this->rules) && array_key_exists($key, $this->rules['equals'])){
							
								$required = $obj[$key];
								$val = $obj[$this->rules['equals'][$key]];
								
								if(!$this->equals($val, $required))
									$this->errors[] = array('equals', $this->rules['equals'][$key], $key);
							
						}
						elseif(array_key_exists('phone', $this->rules) && in_array($key, $this->rules['phone']) && !$this->phone($value)){
							
							$this->errors[] = array('phone', $key);
						}
						elseif(array_key_exists('only_latin_letters', $this->rules) && in_array($key, $this->rules['only_latin_letters']) && !preg_match("/^[a-zA-Z0-9]+$/", $value)){
							
							$this->errors[] = array('only_latin_letters', $key);
						}
						elseif(array_key_exists('valid_captcha', $this->rules) && in_array($key, $this->rules['valid_captcha']) && (strtoupper($value) != strtoupper($_SESSION['keycaptcha']))){
							
							$this->errors[] = array('valid_captcha', $key);
						}
						elseif(array_key_exists('email', $this->rules) && in_array($key, $this->rules['email']) && !$this->email($value)){
							
							$this->errors[] = array('email', $key);
						}
						elseif(array_key_exists('email_domain', $this->rules) && in_array($key, $this->rules['email_domain']) && !$this->email_domain($value)){
							
							$this->errors[] = array('email_domain', $key);
						}
						elseif(array_key_exists('unique', $this->rules) && in_array($key, $this->rules['unique'])){

							$query_tmp[] = "$key = '$value'";
							$pair_for_unique[] = array($key => $value);
						}
						/* //раскомментировать для добавления условия нового правила
						elseif(условие){
								//запись ошибки в скобках необязательные параметры 
								//учитывать вложенность скобок, те параметр_2 не может быть вызван без вызова параметра_1
								//можно добавлять параметры дописав функцию errors()
								$this->errors[] = array('название правила', (проверяемое поле, (параметр_1|проверочное поле, (параметр_2))));
						} */
					}
				}

				$clean_obj[] = array($key => $value);

			}
				//						
		}				
		
		// проверка для правила unique		
		if(!empty($query_tmp)){
			$query = implode(' OR ', $query_tmp); 
			$list = $this->unique($query, $pk);

			foreach($pair_for_unique as $one ){
			
				foreach($one as $key => $value ){
				
					foreach($list as $list_one){
					
						foreach($list_one as $list_key => $list_val){

							if($key == $list_key && $value == $list_val)
								$this->errors[] = array('unique', $key);
						}
					}
				}
			}
		} 
				
		foreach($clean_obj as $key){
			foreach($key as $one_key => $value)
				$this->final_object[$one_key] = $value;

		}	
	}

	private function email($email, $strict = FALSE)
	{
		if (strlen($email) > 254)
		{
			return FALSE;
		}

		if ($strict === TRUE)
		{
			$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
			$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
			$atom  = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
			$pair  = '\\x5c[\\x00-\\x7f]';

			$domain_literal = "\\x5b($dtext|$pair)*\\x5d";
			$quoted_string  = "\\x22($qtext|$pair)*\\x22";
			$sub_domain     = "($atom|$domain_literal)";
			$word           = "($atom|$quoted_string)";
			$domain         = "$sub_domain(\\x2e$sub_domain)*";
			$local_part     = "$word(\\x2e$word)*";

			$expression     = "/^$local_part\\x40$domain$/D";
		}
		else
		{
			$expression = '/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})$/iD';
		}

		return (bool) preg_match($expression, (string) $email);
	}
	
	private function email_domain($email)
	{
		return (bool) checkdnsrr(preg_replace('/^[^@]++@/', '', $email), 'MX');
	}
	
	private function unique($query, $pk = null){

		$primary_key = $this->rules['pk'];
		
		if($pk == null)
			$result = M_MSQL::Instance()->Select("SELECT * FROM $this->table WHERE $query");
		else{
			$result = M_MSQL::Instance()->Select("SELECT * FROM $this->table WHERE ($query) AND $primary_key<>$pk");
		}	

		return $result;
	}

	//
	// returns:
	//		true || false - всё хорошо или всё плохо
	//
	public function good()
	{
		return count($this->errors) == 0;
	}
	
	//
	// returns:
	//		массив с проверенными и отредактированными полями для базы
	//
	public function getObj()
	{
		if(count($this->errors) == 0)
			return $this->final_object;
	}
	
	public function replaceObj()
	{
			return $this->final_object;
	}
	
	private function searchRule($rules, $obj)
	{
		foreach($rules as $key)
			return !in_array($key, $obj);
	}
	
	private function get_errors()
	{
		$errors[] = $this->errors;
		return $errors[0];
		
	}
	private function phone($number, $lengths = NULL)
	{
		if ( ! is_array($lengths))
		{
			$lengths = array(7,10,11);
		}

		$number = preg_replace('/\D+/', '', $number);

		return in_array(strlen($number), $lengths);
	}
	
	private function equals($value, $required)
	{
		return ($value === $required);
	}
	//
	// returns:
	//		массив с ошибками
	//
	public function errors()
	{
		$errors_with_messages = array();
		$messages = include $this->messages_path;	
		$labels = $this->rules['labels'];
		$errors = $this->get_errors();

		foreach($errors as $i){

			if(isset($i[1]))
				$message = str_replace(':label_1', $labels[$i[1]],  $messages[$i[0]]);
			
			if(isset($i[2])){
				if(is_numeric($i[2]))
					$message = str_replace(':param_1', $i[2],  $message);
				else
					$message = str_replace(':label_2', $labels[$i[2]],  $message);
			}
			if(isset($i[3]))
				$message = str_replace(':param_2', $i[3],  $message);
			/* //раскомментировать для исспользования
			//здесь можно добавить еще параметр
			if(isset($i[4]))
				$message = str_replace(':param_3', $i[4],  $message); */	
			
			$errors_with_messages[] = $message;
		}
		
		return $errors_with_messages;
	}
	
	
	
	// подгрузить правила по имени таблицы из rules_map.php
	private function load_rules()
	{
		// запомнить в поле $rules 1 массив из общей карты
		
		$rules = include $this->rules_path;
		return $rules[$this->table];

	}
}