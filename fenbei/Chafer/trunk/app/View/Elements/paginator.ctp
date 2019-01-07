<?php 
// $this->Paginator->options(array(
// 	'update' => 'tbody',
// 	'evalScripts' => true,
// ));
if($this->Paginator->hasPage(2)) {
	echo '<div class="pager"><ul>';
	echo $this->Paginator->prev('上一页', array(
		'tag' => 'li', 
// 		'disabledTag' => 'a'
		), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	echo $this->Paginator->numbers(array(
		'tag' => 'li',
		'separator' => NULL,
		'currentClass' => 'activing',
		'currentTag' => 'a'
	));
	echo $this->Paginator->next('下一页', array(
			'tag' => 'li',
// 			'disabledTag' => 'a'
	), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	echo '</ul></div>';
}
?>