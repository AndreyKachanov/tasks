<div class="container">
	<section class="tasks">
		<article class="task">
			<div class="card">
				<div class="card-header">
					<p><span>Имя:</span> <?=$task['author']?></p>
					<p><span>Email:</span> <?=$task['email']?></p>
					<p><span>Статус:</span> <?=$task['status']?></p>
				</div>
				<div class="card-body text-secondary row flex-column flex-md-row">
					<div class="img-block col-md-6 col-lg-5 d-flex justify-content-center justify-content-center">
						<img src="<?=IMG_DIR . $task['img']?>" alt="">
					</div>
					<div class="card-text col-md-6 col-lg-7"><?=$task['content']?></div>
				</div>
			</div>
		</article>
	</section>
</div>
