<!DOCTYPE html>
<html>
	<head>
		<base href="<?=DOMEN . BASE_URL?>">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta content="text/html; charset=utf-8" http-equiv="content-type">
		<meta name="keywords" content="<?=$keywords?>">
		<meta name="description" content="<?=$description?>">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">	
	</head>
	<body>
		<div class="wrapper">
			<header>
				<div class="top-menu">

					<nav class="navbar navbar-expand-md"> 
						<div class="container">							
						  <button class="navbar-toggler mr-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						    <span class="navbar-toggler-icon"></span>
						  </button>

			  			  <div class="collapse navbar-collapse" id="navbarSupportedContent">
			  			  	
			  			   	<div class="navbar-nav mr-auto">
			  			   		<a class="nav-item nav-link active" href="/">Главная</a>
			  			   		<a class="nav-item nav-link" href="/add">Создать задачу</a>
			  			   	</div>

			  			   	<div class="auth navbar-nav">

								<? if (!$adminlink) : ?> 
			  			   			<a class="nav-item nav-link" href="/login">Войти</a>
			  			   		<? else: ?>
			  			   			<a class="nav-item nav-link" href="/my">Мои задачи</a>
			  			   			<a class="nav-item nav-link" href="/login">Выход (<b><?=$user?></b>)</a>
			  			   		<? endif ?>

			  			   	</div>		  			   		
			  			  </div>					  

						</div>
					</nav>
				</div>				

			</header>

			<div class="content">		
				<?=$content?>	
			</div>
			

			<footer>
				<div class="container">
					<p>Copyright <?=date('Y');?> <a href="http://andreiikachanov.com">andreiikachanov.com.</a> Все права защищены.</p>
					<p>Development - <a href="https://vk.com/id10398369">Andrey Kachanov</a></p>
				</div>
			</footer>
		</div>
		<? foreach($styles as $style): ?> <link rel="stylesheet" href="/<?=CSS_DIR . $style?>.css" /> <? endforeach; ?>
		<? foreach($scripts as $script): ?> <script src="/<?=JS_DIR . $script?>.js"></script> <? endforeach; ?>

		<!-- Modal -->
		<div  class="modal fade" id="cart" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body" id="modal-body">
		      
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		      </div>
		    </div>
		  </div>
		</div>
	</body>
</html>