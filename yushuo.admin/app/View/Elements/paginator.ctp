<?php 
// $this->Paginator->options(array(
// 	'update' => 'tbody',
// 	'evalScripts' => true,
// ));
if($this->Paginator->hasPage(2)) {
	echo '<div class="pager"><ul>';

	echo $this->Paginator->first('<i class="icon-pager-left"></i>', array(
		'tag' => 'li',
		'escape' => false,
// 		'disabledTag' => 'a'
		), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	echo $this->Paginator->numbers(array(
		'tag' => 'li',
		'separator' => NULL,
		'currentClass' => 'activing',
		'currentTag' => 'a',
		'escape' => false
	));    
	echo $this->Paginator->last('<i class="icon-pager-right"></i>', array(
			'tag' => 'li',
			'escape' => false,
// 			'disabledTag' => 'a'
	), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	echo '</ul></div>';
}
?>