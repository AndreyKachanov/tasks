<?php

class M_Model
{
	protected $table;		// имя талицы
	protected $pk;			// первичный ключ
	protected $db;			// модуль для работы с бд
	protected $errors;		// список ошибок
	private $valid;			// модуль валидации

	
	public function __construct($table, $pk)
	{
		$this->table = $table;
		$this->pk = $pk;
		$this->errors = [];
		$this->valid = null;
		$this->db = M_MSQL::Instance();
	}

	public function get($id)
	{
		$id = (int)$id;
		$query = "SELECT {$this->table}.* FROM {$this->table} 
					  WHERE {$this->pk} = '$id'";

		$res = $this->db->Select($query);
		return ($res[0] ?? null);
	}

	public function add($fields)
	{
		$this->errors = [];  // обнуляем список ошибок
		$valid = $this->load_validation(); // подгружаем модуль валидации
		
		$valid->execute($fields); 
		if($valid->good()){
			$res = $valid->getObj();
			return $this->db->Insert($this->table, $res);
		}
		
		$this->errors = $valid->errors();
		return false;
	}

	public function edit($pk, $fields)
	{
		$this->errors = array();  		   // обнуляем список ошибок
		$valid = $this->load_validation(); // подгружаем модуль валидации
		
		$valid->execute($fields, $pk); 
		if($valid->good()){

			$this->db->Update($this->table, $valid->getObj(), "{$this->pk} = '$pk'");
			return true;
		}
		
		$this->errors = $valid->errors();
		return false;
	}

	public function delete($id)
	{
		$this->db->Delete($this->table, "{$this->pk} = '$id'");
		return true;
	}
	
	private function load_validation()
	{
		if($this->valid == null)
			$this->valid = new M_Validation($this->table);
			
		return $this->valid;
	}

	public function errors() 
	{
		return $this->errors;
	}			
}