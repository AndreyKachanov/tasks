<div class="container container-bg">
	<section class="tasks">
		<article class="task">

			<h3 class="text-center">Создание новой задачи</h2>

			<? if(!empty($errors)): ?>
			<div class="alert alert-danger" role="alert">
				<? foreach($errors as $error): ?>
					<p><?=$error?></p>
				<? endforeach; ?>
			</div>
			<? endif; ?>
			<div id="panel-heading" class="alert-danger"></div>
			<form method="POST" enctype="multipart/form-data" id="form_task">
				<div class="row">
					<div class="left col-xs-12 col-md-6">
						
							<div class="form-group col">						
								<label for="author">Автор задачи:</label>
								<input name="author" data-author="<?=$fields['author'] ?? ''?>" type="text" class="form-control" id="author" value="<?=$fields['author'] ?? ''?>">
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group col">	
								<label for="email">Email:</label>
								<input name="email" type="text" class="form-control" id="email" value="<?=$fields['email'] ?? ''?>">
								<div class="invalid-feedback"></div>
							</div>	

							<div class="form-group col">
								<label for="replace">Текст задачи:</label>
								<textarea name="content" class="form-control" id="content" rows="10"><?=$fields['content'] ?? ''?></textarea>
								<div class="invalid-feedback"></div>
							</div>					
						
					</div>

					<div class="right col-xs-12 col-md-6">
						
						<div class="form-group">
							<div class="d-flex flex-column align-items-center">
								<label for="InputImg">Изображение:</label>

							      <div class="upload-img form-control">
							      	<img id="image" src="#" alt="" />
							      </div>
							    	
							    <input type="file" name="file" id="imgInput"> 
							</div>	

						    <div class="help-block">Допустимый формат jpg, png, gif. Допустимый размер - не более 320х240 пикселей, размер файла не больше 5 Мб.</div>
						 </div>

					 </div>				
				</div>

				<div class="d-flex justify-content-md-center flex-column flex-md-row">
						<!-- <button id="buttonPreview" type="button" class="btn btn-secondary col-xs-12">Предварительный просмотр</button> -->
					<button id="buttonPreview" type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-secondary col-xs-12">Предварительный просмотр</button>
					<button class="btn btn-warning col-xs-12" type="submit">Добавить задачу</button>
				</div>
				
			</form>
		</article>
	</section>
</div>			
