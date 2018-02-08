<?php
	//если в массиве только одно значения запись должна быть в таком виде 'role' => ['field']
	return [

		TABLE_PREFIX . 'tasks' => [
			'fields' => ['id_task', 'id_user', 'author', 'email', 'content', 'img', 'status'], 
			'not_empty' => ['id_task', 'author', 'email', 'content', 'status'],
			//массив 'html_allowed' нужно объявлять обязательно, даже если он пустой
			'html_allowed' => ['content'],
			'email' => ['email'],
			'range' => [
						'author' => ['3', '20'],
						],
			'labels' => [
				'author' => '"Автор задачи"',
				'email' => '"Email"',
				'content' => '"Текст задачи"'
			],
			'pk' => 'id_task'
		]
	];
