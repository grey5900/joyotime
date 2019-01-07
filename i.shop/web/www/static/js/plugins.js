$(function() {
    //表单验证插件 v1.9.0 http://bassistance.de/jquery-plugins/jquery-plugin-validation/
    //用户名正则
		jQuery.validator.addMethod("date", function(value, element){
			var ereg = /^(\d{1,4})(-|\/)(\d{1,2})(-|\/)(\d{1,2})$/;
			var r = value.match(ereg);
			if (r == null) {
				return false;
			}
			var d = new Date(r[1], r[3] - 1, r[5]);
			var result = (d.getFullYear() == r[1] && (d.getMonth() + 1) == r[3] && d.getDate() == r[5]);
			return this.optional(element) || (result);
		}, "请输入正确的日期");
		
    $.validator.addMethod("validpassword", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9_\`\~\!\@\#\$\%\^\&\*\(\)\-\=\+\[\]\{\}\\\|\;\:\'\"\<\>\,\.\/\?]{2,15}$/.test(value);
    }, "Password Error.");
    $.validator.addMethod("valilength", function(v, e, p){
    	var check = false;
    	$.ajax({
    		type:'POST',
    		url:'/message/check_length',
    		data:{'msg':v},
    		dataType:'json',
				async: false,
				cache: false,
    		success:function(json){
    			if(json.code == 0)
    				check = true;
    		}
    	});
    	return check;
    });
    // $.validator.addMethod("endDate",function(value, element) {
    //      var startDate = $('#opendate').val();
    //      return new Date(Date.parse(startDate.replace("-", "/"))) <= new Date(Date.parse(value.replace("-", "/")));
    //    },"结束日期必须大于开始日期!");
    $.validator.addMethod("nowDate",function(value, element) {
        var nowdate = new Date();
        var intNow = Number(nowdate.getFullYear()+''+(nowdate.getMonth()+1)+''+nowdate.getDate());
        var intVal = Number(value.replace(/-?/g,''));
				if (intVal > intNow) {
					return true;
				} else {
					return false;
				}},"结束时间不能早于当前时间");
				
				
    $.validator.addMethod("checkDefaultPicUrl", function(value, element) {  
        var isAutoGain=$("#autoGain").attr("checked");
            if(!isAutoGain){
                return value.length > 0;
            }else{
                return true;
            }    
        }, "请上传图片!");
    $.validator.addMethod('minchecked',function(value, element) {
          return $(element).filter(':checked').length >= 1;
    }, 'please make a selection');

    //修改密码
    $("#revisepassword-form").validate({
        rules : {
            oldpwd : {
                required : true,
                validpassword : true
            },
            newpwd : {
                required : true,
                minlength : 2,
                maxlength : 15
            },
            renew : {
                required : true,
                equalTo : "#newpwd"
            }
        },
        messages : {
            oldpwd : {
                required : "请输入登录密码",
                validpassword : "密码不合法"
            },
            newpwd : {
                required : "请输入新密码",
                minlength : "合法到密码长度是2-15个字符",
                maxlength : "合法到密码长度是2-15个字符"
            },
            renew : {
                required : "请再次输入新密码",
                equalTo : "两次输入的密码不一致"
            }
        },
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            $.ajax({
                type : "POST",
                url : $(form).attr("action"),
                data : $(form).serialize(),
                dataType : "json",
                success : function(data) {
                    alert(data.msg);
                    if(data.code == 0) {
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $("#messager").messager({message:data.msg});
                        $submit.button('submit');
                    }
                }
            });
            return false;
        }
    });
    //登录
    $('#login-form').validate({
        rules : {
            username : {
                required : true
            },
            password : {
                required : true
            }
        },
        messages : {
            username : {
                required : "请输入帐号"
            },
            password : {
                required : "请输入密码"
            }
        },
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            $.ajax({
                type : "POST",
                url : $(form).attr("action"),
                data : $(form).serialize(),
                dataType : "json",
                success : function(data) {
                    if(data.code == 0) {
                        if(data.refer != "") {
                            window.location = data.refer;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        $("#messager").messager({message:data.msg});
                        $submit.button('reset');
                    }
                }
            });
            return false;
        }
    });
    //编辑会员卡
    $("#membercard-form").validate({
    	rules:{
    		image:{
    			required:true
    		},
    		content:{
    			required:true
    		},
    		summary:{
    			maxlength:30,
    			required:true
    		}
    	},
    	messages:{
    		image:{
    			required:"请上传会员卡封面图片"
    		},
    		content:{
    			required:"特权详情不能为空"
    		},
    		summary:{
    			maxlength:"摘要长度不能超过30个字",
        		required:"特权摘要不能为空"
    		}
    	},
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            $.ajax({
                type : "POST",
                url : $(form).attr("action"),
                data : $(form).serialize(),
                dataType : "json",
                success : function(data) {
                    if(data.code == 0) {
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $("#messager").messager({message:data.msg});
                    }
                }
            });
            return false;
        }
    });
    //创建优惠
    $("#createprefe-form").validate({
        rules : {
            files : {
                required : true ,
                checkDefaultPicUrl : true
            },
            title : {
                required : true
            },
            prefename : {
                required : true
            },
            detail : {
                required : true
            },
    
            endDate : {
            	required : true,
            	nowDate : true
            }
        },
        messages : {
            files : {
                required : "请上传图片",
                checkDefaultPicUrl : "请上传图片"
            },
            title : {
                required : "请输入优惠标题"
            },
            detail : {
                required : "请输入优惠详情"
            },
            endDate : {
            	required : "请输入结束时间",
            	nowDate : "结束日期必须大于当前!"
            }
        },
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            $.ajax({
                type : "POST",
                url : $(form).attr("action"),
                data : $(form).serialize(),
                dataType : "json",
                success : function(data) {
                    if(data.code == 0) {
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        // $(".alert-error").html(data.msg);
                        // $(".alert-error").show();
                        $submit.button('reset');
                        $("#messager").messager({message:data.msg});
                    }
                }
            });
            return false;
        }
    });
    //编辑欢迎信息
    $("#message-form").validate({
        rules : {
            msg : {
            	required : true,
            	valilength : true
            }
        },
        messages : {
            msg : {
            	required : "欢迎信息不能为空",
            	valilength : "欢迎信息长度不能超过140个汉字"
            }
        },
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            $.ajax({
                type : "POST",
                url : $(form).attr("action"),
                data : $(form).serialize(),
                dataType : "json",
                success : function(data) {
                    if(data.code == 0) {
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $("#messager").messager({message:data.msg});
                        $submit.button('reset');
                    }
                }
            });
            return false;
        }
    });
    //修改优惠
    $("#editprefer-form").validate({
        rules : {
            prefename : {
                required : true
            },
            detail : {
                required : true
            },
            endDate : {
            	required : true,
                nowDate : true
            }
        },
        messages : {
            title : {
                required : "请输入标题"
            },
            detail : {
                required : "描述内容"
            },
            endDate : {
            	required : "请输入结束时间", 
            	nowDate: "结束日期必须大于当前!"
            	
            }
        },
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            $.ajax({
                type : "POST",
                url : $(form).attr("action"),
                data : $(form).serialize(),
                dataType : "json",
                success : function(data) {
            		alert(data.msg);
                    if(data.code == 0) {
                        alert(data.msg);
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $submit.button('reset');
                        $("#messager").messager({message:data.msg});
                    }
                }
            });
            return false;
        }
    });
    //修改未审核的碎片
    $("#profile-form").validate({
        rules : {
        	
        },
        messages : {
        	
        },
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            $.ajax({
                type : "POST",
                url : $(form).attr("action"),
                data : $(form).serialize(),
                dataType : "json",
                success : function(data) {
                    if(data.code == 0) {
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $("#messager").messager({message:data.msg});
                        $submit.button('reset');
                    }
                }
            });
            return false;
        }
    });
    //Add at 2012-11-27 by Liuw,商家发送推送消息
    $("#push-form").validate({
        rules : {
            content : {
            	required : true,
            	valilength : true
            }
        },
        messages : {
            content : {
            	required : "消息内容不能为空",
            	valilength : "消息内容长度不能超过140个汉字"
            }
        },
        highlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("valid success valid").addClass("error");
            } else {
                $(element).closest(".control-group").removeClass("valid success valid").addClass("error");
            }
        },
        unhighlight : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("valid");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("valid");
            }
        },
        success : function(element) {
            if(element.type === 'radio') {
                this.findByName(element.name).closest(".control-group").removeClass("error success valid").addClass("success");
            } else {
                $(element).closest(".control-group").removeClass("error success valid").addClass("success");
            }
        },
        submitHandler : function(form) {
            var $submit = $(form).find('input[type=submit]');
            $submit.button('loading');
            if(confirm("确定要发送该推送消息吗？")){
	            $.ajax({
	                type : "POST",
	                url : $(form).attr("action"),
	                data : $(form).serialize(),
	                dataType : "json",
	                success : function(data) {
	                    if(data.code == 0) {
	                    	alert(data.msg);
	                        if(data.refer != "")
	                            window.location.href = data.refer;
	                        else
	                            window.location.reload();
	                    } else {
	                        $("#messager").messager({message:data.msg});
	                        $submit.button('reset');
	                    }
	                }
	            });
            }else{
            	$submit.button('reset');
            }
            return false;
        }
    });
});
