<?php 
	// Модель задач

class M_Tasks extends M_Model 
{
	// прием синглтон(одиночка)
	// таким методом будет создаваться только 1 объект
	// Позволяет не плодить экземпляры класса, а пользоваться одним
	private static $instance;
	private $tabName = TABLE_PREFIX . "tasks"; //имя таблицы articles с учетом префикса

	public function __construct() 
	{
		parent::__construct($this->tabName, 'id_task');
	}

	public static function Instance() 
	{
		if(self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get($id_task)
	{
		return $res = parent::get($id_task);
	}

		

	public function add($fields)
	{
		$file = $fields['file'];

		// если выбран файл
		if ($file['size'] > 0) {
			$file_name = $this->checkType($file);

			if (!$file_name)
				return false;

			$fields['img'] = $file_name;
		}

		// если пользователь авторизован, добавляем для записи в бд id_user
		if ($user = M_Users::Instance()->Get())
			$fields['id_user'] = $user['id_user'];

		// запись данных в бд
		$id_task = parent::add($fields);

		// если файл подходит и все поля корректно заполнены, копируем в нужную папку 
		if ( isset($file_name) && count($this->errors) == 0) {

			if(copy($file['tmp_name'], IMG_DIR . $file_name)){
				// определяем размер изображения
				$size = getimagesize(IMG_DIR . $file_name);
				if ($size[0] > 320 || $size[1] > 240) {
					// изменяем размер
					$this->resize(IMG_DIR . $file_name, IMG_DIR . $file_name, IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
				}

			} else {
				// если по какой-то причине не скопировался - удаляем запись из бд 
				$this->delete($id_task);
				die('Ошибка копирования файла в директорию. Проверить директорию с изображениями <b>tasks</b>!');	
			}		
		}

		return $id_task;
	}

	public function edit($pk, $fields)
	{
		$res = parent::edit($pk, $fields);			
		return $res;
	}

	public function delete($pk)
	{
		return parent::delete($pk);
	}
	
	public function intro($task) 
	{
		$res = $task['content'];
		if (strlen($res) > 170) {
			$res = mb_substr($task['content'], 0, 170);
			$temp = explode(" ", $res);
			unset($temp[count($temp)-1]);
			$res = implode(" ", $temp) . " ...";
		} 
		return $res;
	}

	public function randomStr($length = 10) 
	{
		$s = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	    return substr(str_shuffle(str_repeat($s, ceil($length/strlen($s)))), 1, $length);
	}

	

	public function resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 100)
    {
      if (!file_exists($src)) return false;

      $size = getimagesize($src);

      if ($size === false) return false;

      // Определяем исходный формат по MIME-информации, предоставленной
      // функцией getimagesize, и выбираем соответствующую формату
      // imagecreatefrom-функцию.
      $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
      $icfunc = "imagecreatefrom" . $format;
      if (!function_exists($icfunc)) return false;
		
      $x_ratio = $width / $size[0];
	  
	  if($height === null)
			$height = $size[1] * $x_ratio;
	  
      $y_ratio = $height / $size[1];

      $ratio       = min($x_ratio, $y_ratio);
      $use_x_ratio = ($x_ratio == $ratio);

      $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
      $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
      $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
      $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

      $isrc = $icfunc($src);
      $idest = imagecreatetruecolor($width, $height);

      imagefill($idest, 0, 0, $rgb);
      imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
        $new_width, $new_height, $size[0], $size[1]);

      imagejpeg($idest, $dest, $quality);

      imagedestroy($isrc);
      imagedestroy($idest);

      return true;
    }
	
	// метод проверяет тип файла и размер
	// возвращает новое сгенерированное false
	public function checkType($file)
	{
		$white_list = ['jpg', 'gif', 'png'];
		$tmp = explode('.', $file['name']);
		$ext = strtolower($tmp[count($tmp) - 1]);

		if (!in_array(strtolower($ext), $white_list)) {
			// записываем в массив ошибок
			$this->errors[] = 'Не верный тип файла (допустимые форматы jpg, png, gif)';
			return false;
		} elseif (($file['size'] > 5 * 1024 * 1024)) {
			// 'Файл превышает 5 МБ';
			$this->errors[] = 'Размер файла не должен превышать 5 Мб';
			return false;
		}

		return $name = $this->randomStr() . "." . $ext;
	}

	// удаляет изображения из папку preview
	public function checkSession($file) {
		// если сессия есть
		if (isset($_SESSION['file_name'])) {
			// и если в форме выбран новый файл (пока что проверка только по имени файла)
			if ( $_SESSION['file_name'] != $file['name']) {
				@unlink(IMG_DIR_PREV . $_SESSION['file_name']);
				// копируем новый файл в папку и присваем новое имя в сессию
				if ($this->uploadPrev($file)) {
					$_SESSION['file_name'] = $file['name'];
					$img = $file['name'];
				}
			} {
				$img = $_SESSION['file_name'];
			}
		} else {
			// если сессии нет, т.е. файл выбираем впервые - копируеv в папку, добавляем в сессию
			if ($this->uploadPrev($file)) {
				$_SESSION['file_name'] = $file['name'];
				$img = $file['name'];
			}
		}
		return $img;
	}

	// загрузка изображений для превью
	public function uploadPrev($file)
	{
		if (copy($file['tmp_name'], IMG_DIR_PREV . $file['name'])) {
			$size = getimagesize(IMG_DIR_PREV . $file['name']);
			if ($size[0] > 320 || $size[1] > 240) {
				M_Tasks::Instance()->resize(IMG_DIR_PREV . $file['name'], IMG_DIR_PREV . $file['name'], IMG_SMALL_WIDTH, IMG_SMALL_HEIGHT);
			}
		}	
	return true;										
	}	    
}