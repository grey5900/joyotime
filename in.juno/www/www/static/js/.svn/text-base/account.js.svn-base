﻿﻿$(function(){

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
   
	$.validator.addMethod("checktaboo", function(value, element) {
		var check = false;
		$.ajax({
			type: "POST",
			url: "/common/check_taboo/",
			data: {'content':value},
			async: false,
			cache: false,
			dataType: "json",
			success: function(data){
				if (data.code == 0){
					check = true;
				}
			}
		})
		return check;
	});

	/* 修改基本资料  */
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
					$.getScript("/sso");
					$.messager(data.msg,'reload');
					$submit.button('reset');
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
						$.messager(data.msg);
						$submit.button('reset');
					}else{
						$.messager(data.msg);
						$submit.button('reset');
					}
				}
			});
			return false;
		}
	});
	//修改密码
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
				validpassword:"密码输入英文、数字和下划线，长度在2~15位之间，区分大小写。"
			},
			newpwd: {
				required:"请输入新密码",
				validpassword:"密码输入英文、数字和下划线，长度在2~15位之间，区分大小写。"
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
					if(data.code == 1) {//原密码错误
						$.messager(data.msg);
						$submit.button('reset');
					}else if (data.code ==0){//修改密码成功
						$.ajax({
							type: 'GET',
							url: signout_url,
							dataType: 'jsonp',
							jsonpCallback: "callback",
							success: function(data){
								//SSO 退出登录
			  					$.getScript(sso_logout_url,function(){
						            $.messager("密码保存成功请重新登陆",'/');
			  					});
							}
						});
					}else if (data.code ==2){//修改密码失败
						$.messager(data.msg);
						$submit.button('reset');
					}else if (data.code == -1) {//没登陆
						$.messager(data.msg);
					}
				}
			});
			return false;
		}
	});
});