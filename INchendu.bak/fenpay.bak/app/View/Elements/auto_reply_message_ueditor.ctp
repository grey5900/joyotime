<?php echo $this->Form->input('AutoReplyMessageCustom.custom_content', array(
	'label' => false,
	'type' => 'textarea',
	'rows' => 8,
	'class' => 'span6',
	'placeholder' => '允许部分HTML Markup',
	'error' => false
));?>
<?php
$this->start('script');
echo $this->Html->script('UEditor/editor_api.js');
echo $this->Html->script('UEditor/ueditor.config.js');
$this->end();
?>
<?php 
$this->start('script');
?>
 
<script type="text/javascript" charset="utf-8">
$(function(){
	var editor = UE.getEditor('AutoReplyMessageCustomCustomContent',{initialFrameHeight:250});
	editor.reset();
})
</script>
<?php
$this->end();
?>