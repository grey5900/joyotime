<div class="pull-right">
    <?php if(isset($item['AutoReplyMessageNews']['ImageAttachment']['thumbnail_url'])): ?>
	  <img id="graphic_img" class="media-object" alt="80x80" src="<?php echo $this->Link->thumbnail($item['AutoReplyMessageNews']['ImageAttachment']['thumbnail_url']); ?>">
	<?php else: ?>
	  <img id="graphic_img" class="media-object" alt="80x80" src="<?php echo $this->Link->thumbnail('', true); ?>">  
	<?php endif; ?>
</div>
<div class="media-body">
	<h3 id="graphic_text"><?php echo $item['AutoReplyMessageNews']['title']?></h3>
	<div class="data hide"><?php echo json_encode($item['AutoReplyMessage'])?></div>
</div>