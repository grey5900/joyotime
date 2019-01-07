<div class="navbar navbar-inverse <?php echo $class ?>">
	<div class="container">
		<div class="fs-nav-logo clearfix">
			<a href="/" class="fs-logo"><?php echo __('鱼说') ?></a>
		</div>
		<nav class="collapse navbar-collapse fs-navbar-collapse">
			<ul class="nav navbar-nav">
				<li><a href="/" class="<?php echo $active == 'index' ? 'active': '' ?>" data-hover="<?php echo __('首页') ?>"><?php echo __('首页') ?></a></li>
				<!-- <li><a href="recharge.html">充值中心</a></li> -->
				<!-- <li><a href="withdraw.html">余额提现</a></li> -->
				<li><a href="/contact" class="<?php echo $active == 'contact' ? 'active': '' ?>" data-hover="<?php echo __('联系我们') ?>"><?php echo __('联系我们') ?></a></li>
			</ul>
		</nav>
		<?php if($class == 'fs-home-header'): ?>
		<?php echo $this->element('address'); ?>
		<?php endif; ?>
	</div>
</div>