<?php 

class C_Auth extends C_Base 
{
	public function action_login() 
	{				
		$mUsers = M_Users::Instance();
		// Очистка старых сессий.
		$mUsers->ClearSessions();
		// Выход.
		$mUsers->Logout();

		// массив с ошибками, для распечатки в шаблоне
		$errors = [];

		$errors['login'] = '';
		$errors['password'] = '';
		$errors['auth'] = '';

		// если идет POST запрос
		if ($this->IsPost()) {
			$login = $_POST['login'];
			$password = $_POST['password'];

			// если 2 поля заполнены
			if ( !empty($login) && !empty($password) ) {
				// если метод Login вернул истину - авторизуем, иначе выводим блок incorrect_password  
				if ( $mUsers->Login($login, $password, isset($_POST['remember']))  ) {
					   // если идет ajax запрос
					   if( $this->IsAjax() ) {
							header('Content-type: application/json');
					   		echo json_encode(array('type' => 'good'));
					   		die();				   	
					   }
					   // если js отключен 
					   else {
							header('location: /');
					   }


				// если метод Login вернул ложь	   	
				} 
				else {
					 // если идет ajax запрос
					if( $this->IsAjax() ) {
						header('Content-type: application/json');						
						echo json_encode(array('type' => 'bad'));
						die();						
					} 
					// если js отключен	
					else {
						// подключем класс error-auth, который выводит ошибку
						$errors['auth'] = 'error-auth';		
					}
				}

			} 
			else {

				// если 2 поля пустые
				if ( empty($login) && empty($password)) {
					$errors['login'] = 'is-invalid';	
					$errors['password'] = 'is-invalid';
				// если логин пустой, пароль не пустой		
				} elseif( empty($login) && !empty($password) ) {
					$errors['login'] = 'is-invalid';
				// если логин не пустой, пароль пустой					
				} elseif ( !empty($login) && empty($password) ) {
					$errors['password'] = 'is-invalid';	
				}
					
			}			
		} 
		// если мы зашли первый раз
		else {
			$login = '';
			$password = '';	
		}		
		$this->title .= ' | Авторизация пользователя';
		$this->content = $this->template('inc/v/auth/v_login.php', 
			[
				'errors' => $errors, 
				'login' => $login, 
				'password' => $password
			]
		);	
	}
}