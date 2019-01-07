$(function(){
    $("#posts-action .praise").on('click',function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
	        dom = $(this);
	        $.ajax({
	            url: '/review_praise/',
	            type: 'post',
	            dataType: 'json',
	            data: {id: dom.attr('data-id'),type:dom.attr('data-type')},
                async: false,
	            success: function(data, textStatus, xhr) {
	                //更新数字
	                var msg = data.msg;
	                //提示
	                $("#messager").messager({
	                    message : msg
	                });
	                if(data.code == 1){
						$(".praise-num").plus(1);
	                    $("#praise-list").prepend('<li><a href="/user/'+online_id+'/" class="avatar large"><i></i><img src="'+online_avatar+'" alt=""></a><a href="/user/'+online_id+'/" class="username">'+online_name+'</a></li>');
	                    //更新文字
	                    dom.text('已赞').removeClass("praise").addClass("praised").off();
	                }
	            }
	        });
		}
        return false;
    });
    $("#posts-action .favorite").on('click',function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
	        dom = $(this);
	        $.ajax({
	            url: '/review_favorite/',
	            type: 'post',
	            dataType: 'json',
	            data: {id: dom.attr('data-id'),type:dom.attr('data-type')},
                async: false,
	            success: function(data, textStatus, xhr) {
	                var msg = data.msg;
	                //提示
	                $("#messager").messager({
	                    message : msg
	                });
	                if(data.code == 1){
	                    //更新文字
	                    dom.text("已收藏").removeClass("favorite").addClass("favorited").off();
	                }
	            }
	        });
		}
        return false;
    });
    //review页面的回复
    $("#review_post").click(function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
	        var $this = $(this);
	    	if ($("#reply-write").is_empty()) {
	    		return false;
	    	}else{
		        $this.button('loading');
		        $.ajax({
		            url: "/review_reply/",
		            type: "post",
		            dataType: "json",
		            data: {pid:$("#pid").val(),content:$("#reply-write").val()},
	                async: false,
		            success: function(data, textStatus, xhr){
		                var msg = data.msg;
		                $("#messager").messager({message:msg});
		                if(data.code == 1){
		                	//+1
		                    $(".reply-num").plus(1);
		                    //刷新回复列表
		                    load_reply("/review_replies", $("#pid").val(), 1);
		                    $("#reply-write").val("");
		                    $this.button('reset');
		                }
		            }
		        });
			}
		}
        return false;
    });
    $("#posts-action .reply").on('click',function(){
        $("#reply-write").focus();
    });
    $("#reply-write").on('focusin mousedown',function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{
			return true;
		}
    });
    $("#reply_list").delegate('#reply_to_form','submit',function(){
        if($("#reply_to_form").find("#message").is_empty()){
            return false;
        }else{
            var dom = $(this);
            $.ajax({
                url: dom.attr("action"),
                type: "post",
                dataType: "json",
                data:dom. serialize(),
                async: false,
                complete: function(xhr, textStatus){},
                success: function(data, textStatus, xhr){
                    var msg = data.msg;
                    $("#messager").messager({message:msg});
                    if(data.code == 1){
	                	//+1
	                    $(".reply-num").plus(1);
                        //刷新回复列表
                        load_reply("/review_replies", $("#reply_pid").val(), 1);
                    }
                    $("#reply_to_form").hide();
                }
            });
        }
        return false;
    });
    $("#reply_list").delegate('.reply_to','click',function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
	        $(this).replyTo({
	            rid : $(this).attr('data-rid'),
	            pid : $(this).attr('data-pid')
	        });
		}
        return false;
    });
    $("#reply_list").delegate('.reply_delete','click',function(){
        $(this).replyDelete({
            uid : $(this).attr('data-uid'),
            message: '确定要删除这条回复吗？'
        });
    });
    $("#reply-write").charCount({
        allowed: 140,
        warning: 10,
        css: 'counter',
        counterElement: 'span',
        cssWarning: 'warning',
        cssExceeded: 'exceeded',
        counterText: '还可以输入'
    });
    
    //获取当前高度
    $("#praise-list").css('height','auto');
    var praiseHeight = $("#praise-list").height();
    $("#praise-list").css('height','106px');
    
    $("#praise-expand").toggle(
	    function(){
	    	$("#praise-list").animate({
	    		height: praiseHeight
	    	});
	    	$("#praise-expand").text('收起全部');
	    },
	    function(){
	    	$("#praise-list").animate({
	    		height: '106px'
	    	});
	    	$("#praise-expand").text('展开全部');
	    }
    );


});
