<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>  <script type="text/javascript" src="/static/js/jquery.cookies.min.js"></script>
  <script type="text/javascript" src="/static/js/crypto-js/rollups/aes.js"></script>
  <script type="text/javascript">
  $(function(){
  	$("#login-modal-form").on('submit',function(){
  		// 提交的时候对password加密
  		var username = encodeURIComponent($("input[name=username]").val());
  		var password = $("input[name=password]").val();
  		var salt = $.cookies.get("salt");
  		password = encodeURIComponent(CryptoJS.AES.encrypt(password, salt));
  		var sign = encodeURIComponent(CryptoJS.AES.encrypt(password, salt));
  		
  		$.ajax({
  			url: "<?=$passport_sign_url?>",
  			data: {username: username, password: password, sign: sign, salt: salt},
  			dataType: "jsonp",
  			jsonpCallback: "callback",
  			success: function(data){
  				if(data.code) {
  					alert(data.message);
  				} else {
  					// 登陆成功那么去访问同步登录接口
  					$.getScript("<?=$passport_sso_url?>",function(){
  						// 成功后刷新页面
  						location.reload();
  					});
  				}
  			}
  		});
  		return false;
  	});
  });
  </script>
  <form method="post" action="/web/index" class="form-inline" id="login-modal-form">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>登录</h3>
  </div>
  <div class="modal-body">
  	<input type="text" name="username" placeholder="用户名" class="span8" />
  	<input type="password" name="password" placeholder="密码" class="span8" />
  </div>
  <div class="modal-footer">
    <a class="btn btn-primary" onclick="$('#login-modal-form').submit();">登录</a>
  </div>
  </form>
