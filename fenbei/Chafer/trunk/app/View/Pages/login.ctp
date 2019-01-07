<?php 
$flash = $this->Session->flash('flash', array('element' => 'flash'));
if(!$flash) {
	echo $this->element('flash', array('message' => '请输入用户名和密码登录'));
} else {
	echo $flash;
}
?>
<div class="container">
	<div class="login-header">
		<h1 class="logo "><a href="/" class="logo_anchor">fenpay分贝</a></h1>
	</div>
	<div class="login-forms">
		<div class="well">
			<form class="form-login" action="/users/login" method="post">
				<div class="control-group">
					<div class="controls">
						<input autofocus class="input-large" name="User[username]" id="username" type="text" placeholder="输入用户名"/>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<input class="input-large" name="User[password]" id="password" type="password" placeholder="输入密码"/>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<label class="checkbox" for="remember">
							<input type="checkbox" name="User[remember_me]" id="remember_me" />
							保持登录状态</label>
					</div>
				</div>
				<div class="control-group text-center">
					<?php echo $this -> Form -> submit('登录', array('div' => false, 'class' => 'btn btn-primary')); ?>
				</div>
			</form>
		</div>
	</div>
</div>
