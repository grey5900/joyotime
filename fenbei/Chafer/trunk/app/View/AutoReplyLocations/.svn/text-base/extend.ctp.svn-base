<div class="locations-content">
	<div class="boxer">
		<div class="top-banner">
		<?php if(!empty($location['ImageAttachment']['original_url'])): ?>
			<img src="<?php echo $location['ImageAttachment']['original_url']?>" />
		<?php endif; ?>
		</div>
		<div class="top-item">
			<div class="space">
				<h3><?php echo $location['AutoReplyLocation']['title']; ?></h3>
				<p class="address"><?php echo $location['AutoReplyLocation']['address']?></p>
				<p class="description"><?php echo $location['AutoReplyLocation']['description']?></p>
			</div>
		</div>
	</div>
	
	<div class="news-item">
	    <?php if(isset($location['AutoReplyMessage']) && !empty($location['AutoReplyMessage'])): ?>
		<div class="boxer">
			<ul class="media-list">
				<?php foreach($location['AutoReplyMessage'] as $item): ?>
				<li class="media">
					<a href='<?php echo $this->Link->newsDetail($item['AutoReplyMessageNews']['title'], $item['id'], array('only_href' => true)) ?>'>
					<div class="pull-right">
					    <img src="<?php echo empty($item['AutoReplyMessageNews']['ImageAttachment']['thumbnail_url']) ? Configure::read('AutoReplyMessage.default_message_thumbnail') : $item['AutoReplyMessageNews']['ImageAttachment']['thumbnail_url'] ?>" class="media-object" />
					</div>
					<div class="media-body">
						<?php // echo $this->Link->newsDetail($item['AutoReplyMessageNews']['title'], $item['id']); ?>
						<p><?php echo $item['AutoReplyMessageNews']['title'] ?></p>
					</div>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
	
</div>
