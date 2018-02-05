<!--
	Шаблон вывода пагинации
	
	$count - общее количество записей
	$on_page - количество записей на странице
	$page_num - номер текущей страницы
	$url_self - url адрес от корня без номера с страницы. Например, /pages/all или /articles/editor/
-->
<? extract($navparams); ?>
<? if($max_page > 1):?>

  <ul class="pagination justify-content-center">
	<? if($page_num <= 1): ?>
    <li class='page-item disabled'><span class='page-link'>Начало</span></li>
	<li class='page-item disabled'><span class='page-link'>Пред.</span></li>
	<? else: ?>
	<li class='page-item'><a class='page-link' href="<?=$url_self?>">Начало</a></li>
	<li class='page-item'><a class='page-link' href="<?=$url_self . ($page_num - 1)?>">Пред.</a></li>
	<? endif; ?>
	<? for($i = $left; $i <= $right; $i++):?>
			<? if($i <1 || $i > $max_page) continue;?>
			<? if($i == $page_num): ?>
    <li class='page-item active'><span class='page-link'><?=$i?></span></li>
	<? else: ?>
	<li class='page-item'><a class='page-link' href="<?=$url_self . $i?>"><?=$i?></a></li>
	<? endif; ?>
	<? endfor; ?>
	
	<? if($page_num * $on_page >= $count): ?>
    <li class='page-item disabled'><span class='page-link'>След.</span></li>
	<li class='page-item disabled'><span class='page-link'>Конец</span></li>
	<? else: ?>
	<li class='page-item'><a class='page-link' href="<?=$url_self . ($page_num + 1)?>">След.</a></li>
	<li class='page-item'><a class='page-link' href="<?=$url_self . $max_page?>">Конец</a></li>
	<? endif; ?>
  </ul>

<? endif; ?>