<?php

spl_autoload_register(
	function($classname) {
		switch ($classname[0]) {
			case 'C':
				require_once "inc/c/$classname.php";
				break;
			case 'M':
				require_once "inc/m/$classname.php";
				break;		
		}	
	}
);

define('BASE_URL', '/');
// нужно для IE 7 и ниже
define('DOMEN', 'http://tasks.loc');

define('MYSQL_SERVER', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', 'admin');
define('MYSQL_DB', 'tasks');
define('TABLE_PREFIX', 'lm77_');
define('HASH_KEY', '35445344dgfdg15d1gfdgdfgdf4545478%^&%^&%');
define('RULES_PATH', 'inc/m/maps/rules.php');
define('MESSAGES_PATH', 'inc/m/maps/messages.php');

define('IMG_SMALL_WIDTH', 320);
define('IMG_SMALL_HEIGHT', 240);
define('IMG_DIR', 'img/tasks/');
define('IMG_DIR_PREV', 'img/tasks/preview/');
define('CSS_DIR', 'css/');
define('JS_DIR', 'js/');