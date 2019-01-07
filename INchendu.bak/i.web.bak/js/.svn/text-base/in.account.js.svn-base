﻿$(function(){
	//表单验证插件 v1.9.0 http://bassistance.de/jquery-plugins/jquery-plugin-validation/
	//用户名正则
	$.validator.addMethod("validusername", function(value, element) {
		return this.optional(element) || /^[a-zA-Z0-9_]{1,15}$/.test(value);
	}, "User Name Error."
	);

	//用户名正则
	$.validator.addMethod("validpassword", function(value, element) {
		return this.optional(element) || /^[a-zA-Z0-9_\`\~\!\@\#\$\%\^\&\*\(\)\-\=\+\[\]\{\}\\\|\;\:\'\"\<\>\,\.\/\?]{2,15}$/.test(value);
	}, "Password Error."
	);

	//电话正则
	$.validator.addMethod("validcellphone", function(value, element) {
		return this.optional(element) || /^(1(([35][0-9])|(47)|[8][01236789]))\d{8}$/.test(value);
	}, "Cellphone Error."
	);
   
	//自定义方法检测用户名是否可用，示例：checkusername: true
	$.validator.addMethod("checkusername", function(value, element) {
		var check = false;
		$.ajax({
			type: "GET",
			url: "/check_username/"+value,
			async: false,
			cache: false,
			dataType: "json",
			success: function(data){
				if (data.code == 1){
					check = true;
				}
			}
		})
		return check;
	});
	$.validator.addMethod("checktaboo", function(value, element) {
		var check = false;
		$.ajax({
			type: "POST",
			url: "/check_taboo/",
			data: {'content':value},
			async: false,
			cache: false,
			dataType: "json",
			success: function(data){
				if (data.code == 1){
					check = true;
				}
			}
		})
		return check;
	});
	$.validator.addMethod("checkemail", function(value, element) {
		var check = false;
		$.ajax({
			type: "POST",
			url: "/check_email/",
			async: false,
			data: "email="+value,
			cache: false,
			dataType: "json",
			success: function(data){
				if (data.code == 1){
					check = true;
				}
			}
		});
		return check;
	});
	
	$.validator.addMethod("checkcaptcha", function(value, element) {
		var check = false;
		$.ajax({
			type: "POST",
			url: "/check_captcha/",
			async: false,
			data: "captcha="+value,
			cache: false,
			dataType: "json",
			success: function(data){
				if (data.code == 1){
					check = true;
				}
			}
		})
		return check;
	});

	
	//登录表单
	$('#signin-form').validate({
		rules: {
			username: {
				required: true
			},
			password: {
				required: true
			},
			verifycode: {
				required: true
				//checkcaptcha: true
			}
		},
		messages: {
			username: {
				required: '请输入用户名'
			},
			password: {
				required: '请输入密码'
			},
			verifycode: {
				required: '请输入验证码'
				//checkcaptcha: '验证码不正确'
			}
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button('loading');
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				success: function(data) {
					if(data.code == 1) {
						if(data.refer != "")
							window.location.href = data.refer;
						else
							window.location.reload();
					}else{
						$(".alert-error").html(data.msg);
						$(".alert-error").show();
						$submit.button('reset');
					}
				}
			});
			return false;
		}
	});

	
	//注册表单
	$('#signup-form').validate({
		rules: {
			username: {
				required: true,
				checkusername: true,
				validusername: true
			},
			password: {
				required: true,
				validpassword: true
			},
			password_again: {
				equalTo: "#password",
				required: true
			},
			email: {
				email: true,
				checkemail: true
			},
			nickname: {
				checktaboo: true
			},
			recommender: {
				number: true
			},
			verifycode: {
				required: true
				// checkcaptcha: true
			}

		},
		messages: {
			username: {
				required: '请输入用户名',
				checkusername: '帐号已存在，请直接<a href="account_signin.php">登录</a>或更换用户名',
				validusername: '用户名不合法'
			},
			password: {
				required: '请输入密码',
				validpassword: '密码不合法'
			},
			password_again: {
				equalTo: '两次输入的密码不一致',
				required: '请输入确认密码'
			},
			email: {
				email: '请输入正确的邮箱地址',
				checkemail: '帐号已存在，请直接<a href="account_signin.php">登录</a>或更换邮箱'
			},
			nickname: {
				checktaboo: '昵称包含不适宜内容，请重新输入'
			},
			recommender: {
				number: '请输入正确的推荐人ID'
			},
			verifycode: {
				required: '请输入验证码'
				//checkcaptcha: '验证码不正确'
			}
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button('loading');
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				success: function(data) {
					if(data.code == 1) {
						if(data.refer != "")
							window.location.href = data.refer;
						else
							window.location.reload();
					}else{
						// $(".alert-error").html(data.msg);
						// $(".alert-error").show();
						alert(data.msg);
						$submit.button('reset');
					}
				}
			});
			return false;
		}
	});

	//完善帐号
	$('#complete-form').validate({
		rules: {
			username: {
				required: true,
				checkusername: true,
				validusername: true
			},
			password: {
				required: true,
				validpassword: true
			},
			password_again: {
				equalTo: "#password",
				required: true
			},
			email: {
				email: true,
				checkemail: true
			},
			nickname: {
				checktaboo: true
			},
			recommender: {
				number: true
			}
		},
		messages: {
			username: {
				required: '请输入用户名',
				checkusername: '帐号已存在，请直接<a href="account_signin.php">登录</a>或更换用户名',
				validusername: '用户名不合法'
			},
			password: {
				required: '请输入密码',
				validpassword: '密码不合法'
			},
			password_again: {
				equalTo: '两次输入的密码不一致',
				required: '请输入确认密码'
			},
			email: {
				email: '请输入正确的邮箱地址',
				checkemail: '帐号已存在，请直接<a href="account_signin.php">登录</a>或更换邮箱'
			},
			nickname: {
				checktaboo: '昵称包含不适宜内容，请重新输入'
			},
			recommender: {
				number: '请输入正确的推荐人ID'
			}
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button('loading');
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				success: function(data) {
					if(data.code == 1) {
						if(data.refer != "")
							window.location.href = data.refer;
						else
							window.location.reload();
					}else{
						// $(".alert-error").html(data.msg);
						// $(".alert-error").show();
						$("#messager").messager({message:data.msg});
						$submit.button('reset');
					}
				}
			});
			return false;
		}
	});
	
	//修改邮箱
	$("#email-form").validate({
		rules:{
			password:{
				required:true
			},
			email:{
				required:true,
				email:true
			}
		},
		message:{
			password:{
				required:"请输入登录密码"
			},
			email: {
				required: "请输入邮箱地址",
				email: "请输入正确的邮箱地址"
			}		   
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button('loading');
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				success: function(data) {
					//alert(data.msg);
					if(data.code == 1) {
						if(data.refer !="")
							window.location.href = data.refer;
						else
							window.location.reload();
					}else{
						$(".alert-error").html(data.msg);
						$(".alert-error").show;
						$submit.button('reset');
					}
				}
			});
			return false;
		}
	});
	
	//修改邮箱
	$("#revisepassword-form").validate({
		
		rules: {
			oldpwd: {
				required: true
			},
			newpwd: {
				required: true,
				validpassword: true
			},
			renew: {
				required:true,
				equalTo: "#newpwd"
				
			}
		},
		messages: {
			oldpwd: {
				required:"请输入登录密码",
				validpassword:"密码不合法"
			},
			newpwd: {
				required:"请输入新密码",
				validpassword:"密码不合法"
			},	  
			renew: {
				required:"请再次输入新密码",
				equalTo:"两次输入的密码不一致"
			}
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button('loading');
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				success: function(data) {
					//alert(data.msg);
					if(data.code == 1) {
						if (data.refer != "") 
							window.location.href = data.refer;
						else
							window.location.reload();
					}else{
						$(".alert-error").html(data.msg);
						$(".alert-error").show;
						$submit.button('reset');
					}
				}
			});
			return false;
		}
	});

	//修改基本资料
	$("#user-settings-form").validate({
		rules: {
			nickname: {
				checktaboo: true
			},
			description: {
				checktaboo: true
			},
			tel: {
				validcellphone: true
			}
		},
		messages: {
			nickname: {
				checktaboo: '昵称包含不适宜内容，请重新输入'
			},
			description: {
				checktaboo: '签名包含不适宜内容，请重新输入'
			},
			tel: {
				validcellphone: '请输入正确的手机号码'
			}
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button("loading");
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				complete: function(xhr, textStatus) {
					$submit.button("reset");
				},
				success: function(data) {
					$("#messager").messager({
						message: data.msg,
						refer: "reload"
					});
				}
			});
			return false;
		}
	});
	
	//找回密码
	$('#resetpassword-form').validate({
		rules: {
			email: {
				required: true,
				email: true
			}
		},
		messages: {
			email: {
				required: "请输入邮箱地址",
				email: "请输入正确的邮箱地址"
			}
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button('loading');
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				success: function(data) {
					//alert(data.msg);
					$submit.button('reset');
					if( navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) && data.code == 1){
						window.close();
					}else if(data.code == 1) {
						window.location.href = "/";
					}
				}
			});
			return false;
		}
	});


	//找回密码
	$('#resetpassword-form-1').validate({
		rules: {
			newpwd: {
				required: true,
				validpassword: true
			},
			repwd: {
				required: true,
				equalTo: "#newpwd"
			}
		},
		messages: {
			newpwd: {
				required: "请输入新密码"
			},
			repwd: {
				equalTo: '两次输入的密码不一致',
				required: '请输入确认密码'
			}
		
		},
		highlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("valid success valid")
					.addClass("error");
			}
		},
		unhighlight: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("valid");
			}
		},
		success: function(element) {
			if (element.type === 'radio') {
				this.findByName(element.name)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			} else {
				$(element)
					.closest(".control-group")
					.removeClass("error success valid")
					.addClass("success");
			}
		},
		submitHandler: function(form) {
			var $submit = $(form).find('input[type=submit]');
			$submit.button('loading');
			$.ajax({
				type: "POST",
				url: $(form).attr("action"),
				data: $(form).serialize(),
				dataType: "json",
				success: function(data) {
					alert(data.msg);
					$submit.button('reset');
					if( navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/) && data.code == 1){
						window.close();
					}else if(data.code == 1) {
						window.location.href = "/signin";
					}
				}
			});
			return false;
		}
	});
	$("#signature").charCount({
		allowed: 30,
		warning: 0,
		css: 'counter',
		counterElement: 'span',
		cssWarning: 'warning',
		cssExceeded: 'exceeded',
		counterText: '还可以输入'
	});
	$("#telephone")
		.on("keyup",function(){
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		})
		.on("paste",function(){
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		})
		.css("ime-mode", "disabled");
});
