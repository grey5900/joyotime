<div class="login-forms">
    <div class="login-header">
        <h1 class="logo "><a href="/" class="logo_anchor">鱼说</a></h1>
    </div>
	<form class="form-login" action="/users/login" method="post" id="form-login">
	    <div class="well">
			<div class="pull-left">
				<div class="control-group">
					<div class="controls">
						<i class="icon-user"></i>
						<input autofocus class="input-large" name="username" id="username" maxlength="15" type="text" placeholder="输入用户名"/>
					</div>
				</div>
				<div class="control-group">
				    <div class="controls">
				        <div class="block-row"></div>
				    </div>
				</div>
				<div class="control-group">
					<div class="controls">
					    <i class="icon-pass"></i>
						<input class="input-large" name="password" id="password" maxlength="15" type="password" placeholder="输入密码"/>
					</div>
				</div>
				
			</div>
			<div class="pull-right join-button">
                <?php echo $this->Form->submit('<i class="icon-enter"></i>', array('div' => false, 'class' => 'join-button','escape' => false)); ?>
            </div>
		</div>
		<!-- <div class="controls">
			<label class="checkbox" for="remember">
				<input type="checkbox" name="remember_me" id="remember_me" />
				记住密码</label>
		</div> -->
	</form>
</div>