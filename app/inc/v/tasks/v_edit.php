<div class="container container-bg">
	<section class="tasks">
		<article class="task">

			<h3 class="text-center">Редактирование задачи</h2>

			<? if(!empty($errors)): ?>
			<div class="alert alert-danger" role="alert">
				<? foreach($errors as $error): ?>
					<p><?=$error?></p>
				<? endforeach; ?>
			</div>
			<? endif; ?>
			<form method="POST" class="row">
				<div class="left col-12 col-md-4">
					<div class="form-group">
						<label for="status"><b>Статус задачи:</b></label> 
						<select class="form-control" name="status" id="status">
							<?php foreach($status as $key => $st): ?>
				                <option value="<?=$st?>" <?php if($st == $fields['status']) echo 'selected';?>>
				                	<?=$st?>
				                </option>
		                	<?php endforeach; ?>
		        	  	</select>
		        	</div>

					<div class="form-group">
						<label for="author">Автор задачи:</label>
						<input name="author" type="text" class="form-control" id="author" value="<?=$fields['author'] ?? ''?>">
					</div>

					<div class="form-group">
						<label for="email">Email:</label>
						<input name="email" type="text" class="form-control" id="email" value="<?=$fields['email'] ?? ''?>">
					</div>					
				</div>

				<div class="right col-12 col-md-8">
						<div class="form-group">
						<label for="replace">Текст задачи:</label>
						<textarea name="content" class="form-control" id="replace" rows="10"><?=$fields['content'] ?? ''?></textarea>
					</div>
				</div>

				<div class="col-12 d-flex flex-column justify-content-center flex-md-row">
					<input class="btn btn-warning" type="submit" value="Сохранить" name="save">
					<input class="btn btn-danger" type="submit" value="Удалить" name="delete" onClick="javascript: return confirm('Вы действительно хотите удалить?')">					
				</div>
				
			</form>
		</article>
	</section>
</div>			
