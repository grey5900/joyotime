<div class="navbar">
	<div class="navbar-nav-logo">
		<a href="/voices/index" class="logo"  title="FishSaying-鱼说">
			<cite>欢迎你来到鱼说解说录制后台</cite>	
		</a>
	</div>
	<div class="navbar-nav-menu">
		<span class=" ">欢迎你，<em><?php echo CakeSession::read('Auth.User.username') ?></em></span>
		<a href="/users/logout" class="logout-close"><i class="icon-close"></i></a>
	</div>
</div>