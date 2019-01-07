<?php 
$this->Location->initialize($location); 
$mayor = $this->Location->mayor();
$this->Tip->initialize($tip);
$tips = $this->Tip;
$properties = $this->Location->properties();
?>
<!-- 地点信息 -->
<div class="adv-top">
	<a class="img-cut" href="javascript:;"><img class="media-object-full" src="<?php echo $this->Location->banner(); ?>" /></a>
    <?php //echo $this->Location->repayPoint(); ?>
    <!-- //挣积分 -->
    <div class="cover-vertical"></div>
	<div class="media">
	  <a class="pull-left" href="javascript:;">
	    <?php echo $this->Location->icon(array('class' => 'media-object40')); ?>
	  </a>
	  <div class="media-body">
	    <h4 class="media-heading"><?php echo $this->Location->placename(); ?></h4>
	    <div class="media-more">
	    	<span><?php echo $this->Location->address(); ?></span>
	    	<p><i class="<?php echo $this->Location->stars(); ?>"></i></p>
	    </div>
	  </div>
	</div>
</div>

<div class="lump">
	
	<!-- 扩展模型 -->
	<?php if($properties->has()): ?>
	<div class="adv-box">
	    <?php 
    		foreach($properties->getAll() as $idx => $pro): 
		    $item = $properties->item($pro);
		?>
		
		
		<?php if($item->link()):?>
		  <a class="arrow media" href="<?php echo $item->link(); ?>">
		      <?php echo $item->output() ?>
		  </a>
		<?php else: ?>
		  <div class="media">
		  <?php echo $item->output() ?>
		  </div>
		<?php endif; ?>
		
		<?php if($properties->count() != $idx + 1): ?>
		<div class="dotted"></div>
		<?php endif; ?>
		
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<!-- 点评 -->
	<?php if($tips->has()): ?>
	<div class="adv-title">
		<i class="icon-post"></i><span>点评</span><div class="round"></div>
	</div>
	<?php 
	foreach($tips->getAll() as $tip): 
	    $item = $tips->item($tip);
	?>
	<div class="adv-box gap-control">
	    <?php echo $item->hot(); ?>
	    <!-- //精华 -->
		<div class="media-opt"> 
			<img class="media-object40 radius" src="<?php echo $item->avatar() ?>"  />
			<h4 class="media-heading"><?php echo $item->nickname() ?></h4>
			<?php echo $item->cover(array('class' => 'media-object124')); ?>
			<p><?php echo $item->content() ?></p>
			<div class="active"></div>
		</div>
	</div>
	<?php endforeach; ?>
	
	<?php if($this->Location->more($this->Location->tipCount())):?>
	<div class="media-unclick">
		<a data-toggle="dl" href="javascript:;">共<?php echo $this->Location->tipCount() ?>条点评</a>
	</div>
	<?php endif; ?>
	<?php endif; ?>
	
	<!-- 电话，其他 -->
	<?php if($this->Location->phone()): ?>
	<div class="adv-box">
		<div class="media">
		  <div class="media-body">
		    <div class="media-more">
		    	<p>电话：<?php echo $this->Location->phone() ?></p>
		    </div>
		  </div>
		</div>
	</div>
	<?php endif; ?>
	
	<!-- IN成都查看 -->
	<a class="btn btn-link" href="http://in.chengdu.cn/qr/inplace/<?php echo $id ?>">在IN成都查看</a>
</div>

<?php 
	$this->start('script');
	echo $this->Html->script('jquery-1.9.1.min');
?>
<script type="text/javascript" charset="utf-8">
	$(function() {
		$(".taggle-dl").each(function() {
			var $dd = $(this).children("dd:gt(0)"),
				$show = $(this).next();
				$dd.hide();
			var text = $show.children().text();
			
			$show.bind("click", function(e) {
				if ($dd.is(":visible")) {
				    $dd.hide();
				    $show.children().text(text);
			    } else {
			        $dd.show();
			        $show.children().text('收起');
			    }
			    return false;
			});
		});
		$(".taggle-dl3").each(function() {
			var $dd = $(this).children("dd:gt(0)"),
				$show = $(this).next();
				$dd.hide();
			var text = $show.children().text();
			
			$show.bind("click", function(e) {
				if ($dd.is(":visible")) {
				    $dd.hide();
				    $show.children().text(text);
			    } else {
			        $dd.show();
			        $show.children().text('收起');
			    }
			    return false;
			});
		});
	})
</script>
<?php
$this->end()
?>
