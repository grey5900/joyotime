<?php APP::uses('Voice', 'Model')?>
<?php 
$path = $this->request->query('url');
?>
<script>
	$(function () {
	  $('.list-group-item.active').parent().show();
	  $('.list-group-item.active').parent().prev().children('.arrow').addClass('on');
	  $(".menu-group-item-header").on("click",function () {
	  	var _parent=$(this).parent();
	  	var _group=$(this).next()
	  	if (_group.is(":hidden")) {
	  		_group.slideToggle();
	  		$(this).children('.arrow').addClass('on');
	  		_parent.siblings().children('.fs-menu').slideUp();
	  		_parent.siblings().find('.arrow').removeClass('on');
	  	} else{
	  		_group.slideToggle();
	  		$(this).children('.arrow').removeClass('on');
	  	};
	  })
	  
	})
</script>
<ul class="menu-group">
  <li class="menu-group-item">
  	<a class="menu-group-item-header"><span class="i i-sound"></span>内容管理 <span class="arrow off"></span></a>
  	<div class="list-group fs-menu">
	  <a href="/voices/index/1" class="list-group-item <?php echo preg_match('/voices\\/index\\/([1|3]+|\\?title=(.*?))|voices\/tag\/[0-9a-z]+\/[1|3]+/i', $path) ? 'active' : ''?>">
	    <?php echo __('Voice List') ?>
	  </a>
	  <a href="/voices/index/<?php echo Voice::STATUS_PENDING ?>" class="list-group-item <?php echo preg_match('/voices\\/index\\/[0|2]+|voices\/tag\/[0-9a-z]+\/[0|2]+/i', $path) ? 'active' : ''?>">
	  	<?php echo __('Approve voice') ?>
	  </a>
	  <a href="/voices/index/<?php echo Voice::RECOMMENDED ?>" class="list-group-item <?php echo preg_match('/voices\\/index\\/[4|5]+|voices\/tag\/[0-9a-z]+\/[4|5]+/i', $path) ? 'active' : ''?>">
	  	<?php echo __('鱼说推荐') ?>
	  </a>
	  <a href="/packages/index" class="list-group-item <?php echo preg_match('/packages/i', $path) ? 'active' : ''?>">
	    <?php echo __('鱼说包列表') ?>
	  </a>
	   <a href="/voices/index/6" class="list-group-item <?php echo preg_match('/voices\\/index\\/([6|7]+|\\?title=(.*?))|voices\/tag\/[0-9a-z]+\/[6|7]+/i', $path) ? 'active' : ''?>">
	    <?php echo __('广告鱼说管理') ?>
	  </a>
	 
	  <a href="/comments/index" class="list-group-item <?php echo preg_match('/comments/i', $path) ? 'active' : ''?>">
	  	<?php echo __('评论管理') ?>
	  </a>
	  <a href="/reports/index" class="list-group-item <?php echo preg_match('/reports/i', $path) ? 'active' : ''?>">
	  	<?php echo __('举报管理') ?>
	  </a>
	</div>
  </li>
  <li class="menu-group-item">
  	<a class="menu-group-item-header"><span class="i i-user"></span>用户管理<span class="arrow off"></span></a>
  	<div class="list-group fs-menu">
	  <a href="/users/index" class="list-group-item <?php echo preg_match('/users\/index/i', $path) ? 'active' : ''?>">
	    <?php echo __('用户列表') ?>
	  </a>
	  <a href="/gifts/add" class="list-group-item <?php echo preg_match('/gifts/i', $path) ? 'active' : ''?>">
	  	<?php echo __('赠送时长') ?>
	  </a>
	  <a href="/users/authList" class="list-group-item <?php echo preg_match('/users\/auth/i', $path) ? 'active' : ''?>">
	    <?php echo __('用户认证') ?>
	  </a>
	  <a href="/users/agencyAuth" class="list-group-item <?php echo preg_match('/users\/agency/i', $path) ? 'active' : ''?>">
	    <?php echo __('机构认证') ?>
	  </a>
	   <a href="/users/recommendList" class="list-group-item <?php echo preg_match('/users\/(recommendList|recommendAddList)/i', $path) ? 'active' : ''?>">
	  	<?php echo __('用户推荐') ?>
	  </a>
	   <a href="/partners/index" class="list-group-item <?php echo preg_match('/partners/i', $path) ? 'active' : ''?>">
	  	<?php echo __('内容合作商管理') ?>
	  </a>
	</div>
  </li>
  <li class="menu-group-item">
  	<a class="menu-group-item-header"><span class="i i-log"></span>账目管理<span class="arrow off"></span></a>
  	<div class="list-group fs-menu">
	  <a href="/receipts/index" class="list-group-item <?php echo preg_match('/receipts/i', $path) ? 'active' : ''?>">
	  	<?php echo __('订单管理') ?>
	  </a>
	  <a href="/withdrawals/index" class="list-group-item <?php echo preg_match('/withdrawals/i', $path) ? 'active' : ''?>">
	    <?php echo __('提现管理') ?>
	  </a>
	  <a href="/coupons/index" class="list-group-item <?php echo preg_match('/coupons/i', $path) ? 'active' : ''?>">
	    <?php echo __('二维码生成管理') ?>
	  </a>
	</div>
  </li>
  <li class="menu-group-item">
  	<a class="menu-group-item-header"><span class="i i-msg"></span>消息管理<span class="arrow off"></span></a>
  	<div class="list-group fs-menu">
	  <a href="/broadcasts/add" class="list-group-item <?php echo preg_match('/broadcasts/i', $path) ? 'active' : ''?>">
	    <?php echo __('推送消息') ?>
	  </a>
	</div>
  </li>
   <li class="menu-group-item">
  	<a class="menu-group-item-header"><span class="i i-phone"></span>数据统计<span class="arrow off"></span></a>
  	<div class="list-group fs-menu">
	  <a href="/statistics/map" class="list-group-item <?php echo preg_match('/statistics/i', $path) ? 'active' : ''?>">
	  	<?php echo __('鱼说地图') ?>
	  </a>
	</div>
  </li>
  <li class="menu-group-item">
  	<a class="menu-group-item-header"><span class="i i-phone"></span>客户端设置<span class="arrow off"></span></a>
  	<div class="list-group fs-menu">
	  <a href="/versions/index" class="list-group-item <?php echo preg_match('/versions/i', $path) ? 'active' : ''?>">
	  	<?php echo __('版本管理') ?>
	  </a>
	  <a href="/splashes/add" class="list-group-item <?php echo preg_match('/splashes/i', $path) ? 'active' : ''?>">
	  	<?php echo __('启动画面管理') ?>
	  </a>
	   <a href="/tags/index" class="list-group-item <?php echo preg_match('/tags/i', $path) ? 'active' : ''?>">
	    <?php echo __('标签管理') ?>
	  </a>
	   <!--  <a href="/tags/index" class="list-group-item <?php echo preg_match('/tags/i', $path) ? 'active' : ''?>">
	    <?php echo __('销售补贴') ?>-->
	  </a>
	  
	</div>
	
  </li>
  <li class="menu-group-item">
  	<a class="menu-group-item-header"><span class="i i-set"></span>后台设置<span class="arrow off"></span></a>
  	<div class="list-group fs-menu">
	  <a href="/histories/index" class="list-group-item <?php echo $path == 'histories' 
                    || $path == 'histories/index' ? 'active' : ''?>">
	  	<?php echo __('操作记录') ?>
	  </a>
	</div>
  </li>
</ul>