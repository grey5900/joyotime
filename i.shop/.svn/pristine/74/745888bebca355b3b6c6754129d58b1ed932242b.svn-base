$(function() {
    //表单验证插件 v1.9.0 http://bassistance.de/jquery-plugins/jquery-plugin-validation/
    //用户名正则
    $.validator.addMethod("validpassword", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9_\`\~\!\@\#\$\%\^\&\*\(\)\-\=\+\[\]\{\}\\\|\;\:\'\"\<\>\,\.\/\?]{2,15}$/.test(value);
    }, "Password Error.");
    //修改密码
    $("#revisepassword-form").validate({

        rules : {
            oldpwd : {
                required : true
            },
            newpwd : {
                required : true,
                validpassword : true
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
                validpassword : "密码不合法"
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
                    if(data.code == 1) {
                        alert(data.msg);
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $(".alert-error").html(data.msg);
                        $(".alert-error").show();
                        $submit.button('reset');
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
                    if(data.code == 1) {
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $(".alert-error").html(data.msg);
                        $(".alert-error").show();
                        $submit.button('reset');
                    }
                }
            });
            return false;
        }
    });
    //创建优惠
    $("#createprefe-form").validate({

        rules : {
            prefename : {
                required : true
            },
            depict : {
                required : true,
            },
            expire : {
                required : true,
            },
            use : {
                required : true,
            }
        },
        messages : {
            prefename : {
                required : "请输入标题",
            },
            depict : {
                required : "描述内容",
            },
            expire : {
                required : "请输入到期时间",
            },
            use : {
                required : "请输入可使用次数",
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
                    if(data.code == 1) {
                        alert(data.msg);
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        $(".alert-error").html(data.msg);
                        $(".alert-error").show();
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
            image:{required:true},
            content:{required:true},
            summary:{required:true}
        },
        messages:{
            image:{required:"请上传会员卡封面图片"},
            content:{required:"特权详情不能为空"},
            summary:{required:"特权摘要不能为空"}
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
                        alert(data.msg);
                        if(data.refer != "")
                            window.location.href = data.refer;
                        else
                            window.location.reload();
                    } else {
                        //alert(data.msg);
                        $(".alert-error").html(data.msg);
                        $(".alert-error").show();
                        $submit.button('reset');
                    }
                }
            });
            return false;
        }
    });
});
