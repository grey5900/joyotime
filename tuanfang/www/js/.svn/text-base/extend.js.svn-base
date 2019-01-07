!function($) {
    //方法
    $.extend({
        /**
         * 返回顶部
         *
         * $.returntop();
         **/
        returntop: function() {
            var $el = $("<div id=\"returntop\"></div>");
            $("body").append($el);
            var obj = $el.click(function(){
                $("html, body").animate({
                    scrollTop: 0
                },
                120)
            });
            $(window).on("scroll",function(){
                var topHeight = $(document).scrollTop(),
                winHeight = $(window).height();
                if (200 < topHeight) {
                    obj.fadeIn();
                } else {
                    obj.fadeOut();
                }
            })
        },
        messager: function(message,refer,callback) {
            if(!message) {
                return false;
            }
            var $el = $("<div id=\"messager\"></div>");
            $("body").append($el);
            $el.html(message);
            var sTop = $(document).scrollTop(),
                sHeight = $(window).height(),
                sWidth = $(window).width(),
                elHeight = $el.outerHeight(),
                elWidth = $el.outerWidth(),
                elTop = (sHeight - elHeight) / 2 + sTop,
                elLeft = (sWidth - elWidth) / 2;
            $el.css({
                top: elTop + 10,
                left: elLeft,
                opacity: '0'
            })
            .animate({
                opacity: '1',
                top: elTop,
                queue: true
            }, 200)
            .delay('2000')
            .animate({
                opacity: '0',
                top: elTop + 10,
                queue: true
            }, 200,
            function() {
                $el.remove();
                if (refer) {
                    if (refer == 'reload') {
                        window.location.reload();
                    } else {
                        window.location.href = refer;
                    }
                }

                if (callback) {
                    callback();
                }
            });
        }
    });
    $.fn.extend({
        /**
         * 为空不提交表单，目标设置为焦点
         *
         **/
        is_empty: function() {
            var $this = $(this).first();
            if ($this.val()) {
                return false;
            } else {
                $this.focus();
                return true;
            }
        },
        /**
         * 下载页
         *
         **/
        phone_switch:function(id){
            var obj = $("#" + id);
            var linewidth = obj.find("li").outerWidth(true);
            var objwidth = linewidth * obj.find("li").length;
            obj.width(objwidth);
            function marquee(){
                obj.animate({
                    marginLeft: -linewidth},1000,function(){
                    $(this).css({marginLeft:"0px"}).find("li:first").appendTo(this);
                });
            }
            if(obj.find("li").length > 1){
                var mar = setInterval(marquee, 4000);
            }
            obj.hover(function(){
                if(mar){
                    clearInterval(mar);
                }},
                function(){
                    clearInterval(mar);
                    mar = setInterval(marquee, 4000);
            });
        }
    });
} (window.jQuery);

function autoScroll(obj){ 
    $(obj).find(".list").animate({ 
        marginTop : "-25px" 
    },500,function(){ 
        $(this).css({marginTop : "0px"}).find("tr:first").appendTo(this); 
    }) 
} 

$(function(){
    $.returntop();
    
    setInterval('autoScroll(".scroll")',3000);
    
    $.getScript('/js/jquery-scrolltofixed-min.js',function(){
        $('.model-nav').scrollToFixed();
    });
    
    $(".text-view").each(function() {
        $(this).on("click", function() {
            $(this).toggleClass("on");
        });
    })
    
    $('#ct-modal-form').on('submit',function(){
        if($(this).find('input[name=tel]').is_empty() || $(this).find('input[name=username]').is_empty()) {
            return false;
          }
    	var tel = $(this).find("input[name=tel]").val();
    	var username = $(this).find("input[name=username]").val();
    	var agreed =  $(this).find("input[name=agreed]");
    	var btn = $(this).find("input[type=submit]");
            btn.button('loading');
    	
        if (agreed.prop('checked') == true) {
            $.post("/web/apply",{type:0,name:username,cell:tel,id:appy_house_id},function(data){
                var obj = eval('('+data+')');
                if(obj.code == 0){
                    $.messager('报名成功');
                    $('.modal').modal('hide');
                }
                else{
                    $(".alert-danger").removeClass('hide').text(obj.msg);
                    return false
                }
                btn.button('reset');
                return false;
            });
            
        } else {
            $(".alert-danger").removeClass('hide').text("是否同意协议？");
            return false;
        }
        return false;
        
    });
    $('#yh-modal-form').on('submit',function(){
        if($(this).find('input[name=tel]').is_empty() || $(this).find('input[name=username]').is_empty()) {
            return false;
          }

    	var tel = $(this).find("input[name=tel]").val();
    	var username = $(this).find("input[name=username]").val();
    	var agreed =  $(this).find("input[name=agreed]");
    	var btn = $(this).find("input[type=submit]");
            btn.button('loading');
            
            
    	if (agreed.prop('checked') == true) {
            $.post("/web/apply",{type:1,name:username,cell:tel,id:appy_house_id},function(data){
                var obj = eval('('+data+')');
                if(obj.code == 0){
                    $.messager('成功优惠');
                    $('.modal').modal('hide');
                }
                else{
                    $(".alert-danger").removeClass('hide').text(obj.msg);
                    return false
                }
                btn.button('reset');
                return false
            });
        } else {
            $(".alert-danger").removeClass('hide').text("是否同意协议？");
            return false;
        }
    	return false;
    });
    
    $('[placeholder]').placeholder();
});