<? extract($navparams); ?>
	<div class="container container-bg">
			<div class="table-responsive">
				<table class="table" id="myTable">
					<thead>
						<tr>
							<th></th>
							<th class="sortable">Имя</th>
							<th class="sortable">E-mail</th>
							<th>Задача</th>
							<th class="sortable">Статус</th>
						</tr>
					</thead>
					<tbody>
					<? foreach ($tasks as $task): ?>
						<tr>
							<td><a href="/task/<?=$task['id_task']?>"><img src="<?=IMG_DIR . $task['img']?>" alt=""></a></td>
							<td><?=$task['author']?></td>			
							<td><?=$task['email']?></td>
							<td class="task"><?=$task['intro'] ?? $task['content'];?></td>
							<td><?=$task['status']?></td>
							<td>
								<a class="continue btn btn-secondary btn-sm" href="/task/<?=$task['id_task']?>">Просмотр</a>
								<? if ($adminlink) : ?> 
									<a class="continue btn btn-warning btn-sm" href="/edit/<?=$task['id_task']?>">Изменить</a>
									<a onClick="javascript: return confirm('Вы действительно хотите удалить?')" class="continue btn btn-danger btn-sm" href="/tasks/delete/<?=$task['id_task']?>">Удалить</a>
								<? endif ?>
							</td>
						</tr>
					<? endforeach ?>
					</tbody>
				</table>
			</div>
			<!-- Постраничный вывод -->
			<?=$navbar ?>
	</div>