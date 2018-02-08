<?php if (!empty($task)): ?>
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
						<?php if (isset($task['img'])): ?>
							<img class="img_prev" src="<?=IMG_DIR_PREV . $task['img']?>" alt="">
						<?php else: ?>
							<img class="img_prev" src="img/tasks/no-image.png" alt="">
						<?php endif; ?>
					</div>
					<div class="card-text col-md-6 col-lg-7"><?=$task['content']?></div>
				</div>
			</div>
		</article>
</section>
<?php else: ?>
	<h3>Форма пуста</h3>
<?php endif; ?>


