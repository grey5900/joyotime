<?php 
$id = isset($id) ? $id : 'keywords'; 
$label = isset($label) ? $label : '标 签：'; 
$tags = isset($tags) ? $tags : array();
?>
<div class="control-group">
	<label class="control-label" for="<?php echo $id ?>"><?php echo $label; ?></label>
	<div class="controls">
		<?php echo $this->Form->input("$id", array(
            'type' => 'hidden',
		    'id' => $id,
		    'value' => implode(',', $tags)
        ))?>
	</div>
</div>