<div class="login-header">
	<div class="header-logo">
<!-- 		<img src="/img/fenpay_logo.png" title="" alt="" /> -->
		<h2>五星茶楼</h2>
	</div>
</div>
<div class="login-box">
	<div class="well">
		<?php
		$flash = $this->Session->flash('flash', array('element' => 'flash'));
		if(!$flash) {
			echo $this->element('flash', array('message' => '请输入用户名和密码登录'));
		} else {
			echo $flash;
		}
		?>
		<?php echo $this->Form->create('user', array('class' => 'form-horizontal', 'action' => 'login')); ?>
				<div class="control-group">
						<div class="input-prepend" title="输入用户名">
							<span class="add-on"><i class="icon-user"></i></span>
							<input autofocus class="input-large" name="User[username]" id="username" type="text" />
						</div>
				</div>
				<div class="control-group">
					<div class="input-prepend" title="输入密码">
						<span class="add-on"><i class="icon-lock"></i></span>
						<input class="input-large" name="User[password]" id="password" type="password" />
					</div>
				</div>
				<div class="control-group">
					<div class="input-prepend">
						<label class="checkbox" for="remember">
							<input type="checkbox" name="User[remember_me]" id="remember_me" />
							保持登录状态</label>
					</div>
				</div>
				<div class="control-group">
					<?php echo $this->Form->submit('登录', array(
			          'div' => false,
			          'class' => 'btn btn-primary'
			        ));?>
				</div>
		<?php echo $this->Form->end(); ?>
	</div><!--/span-->
</div>

