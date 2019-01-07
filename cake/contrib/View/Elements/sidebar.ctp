<?php $path = $this->request->query('url') ?>
<div class="accordion" id="accordion">
	<div class="accordion-group">
		<div id="collapseOne" class="accordion-body collapse in">
			<div class="accordion-inner">
				<div class="<?php echo (bool)stristr($path,'voices/index') || 
				    $path == 'voices'? 'active' : ''?>">
					<a href="/voices/index">解说列表</a>
				</div>
				<div class="<?php echo (bool)stristr($path,'voices/add') || 
				    (bool)stristr($path,'voices/edit') ? 'active' : ''?>">
					<a href="/voices/add">录制解说</a>
				</div>
			</div>
		</div>
	</div>
</div>
