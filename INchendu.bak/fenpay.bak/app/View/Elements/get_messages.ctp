<ul class="media-list" rel="selectList">
	<?php foreach($messages as $item): ?>
	<li class="media" data-select="dbclick">
	<?php echo $this->element('select_message_item', compact('item'))?>
	</li>
	<?php endforeach; ?>
</ul>
<?php 
$this->Paginator->options(array(
	'update' => '.modal-select-box',
	'evalScripts' => true,
    'complete' => '$("[rel=selectList]").selectable();',
));

echo $this->element('paginator');
?>
<?php 
if(isset($isAjax) && $isAjax == true) {
    echo $this->Js->writeBuffer();
} else {
    $this->start('script');
    // output generated code used to requesst new page via ajax.
    echo $this->Js->writeBuffer();
    $this->end();
}
?>
