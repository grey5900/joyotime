!function ($) {
	$(function() {

	    //赞
	    $(".praise").live("click", function() {
			//检查登录状态
			if( !$.is_signin()){
				return false;
			}else{//已登录
		        btn = $(this);
		        $.ajax({
		            type: "POST",
		            url: "/praise/",
		            data: {"id":btn.attr("date-id"),"type":btn.attr("date-target")},
		            cache: false,
		            dataType:"json",
		            success:function(data){
                        $("#messager").messager({message:data.msg});
		                if(data.code == 1){
		                    btn.text("已赞").removeClass("praise").addClass("praised").off();
		                }
		            }
		        });
			}
	        return false;
	    });
	    
	    //收藏
	    $(".favorite").live("click", function() {
			//检查登录状态
			if( !$.is_signin()){
				return false;
			}else{//已登录
		        btn = $(this);
		        $.ajax({
		            type: "POST",
		            url: "/favorite/",
		            data: {"id":btn.attr("date-id"),"type":btn.attr("date-target")},
		            cache: false,
		            dataType: "json",
		            success:function(data){
                        $("#messager").messager({message:data.msg});
		                if(data.code == 1){
		                    btn.text("已收藏").removeClass("favorite").addClass("favorited").off();
		                }
		            }
		        });
			}
	        return false;
	    });
	    //回复
	    $(".reply").live("click", function() {
			//检查登录状态
			if( !$.is_signin()){
				return false;
			}else{//已登录
		        var dom = $(this);
	            $("#reply-form").load("/show_form/reply",function(html){
	                $("#reply-form").html(html);
	                $("#reply-form").modal('show');
	                $("#reply-form h3").html("回复 <strong>" + dom.attr("date-target") + "</strong> ：");
	                $("#reply_id").val(dom.attr("date-id"));
	                $("#reply_uid").val(dom.attr("date-uid"));
	                $("#reply_to").val(dom.attr("date-type"));
	                $("#reply-form textarea").text("").focus();
				    $("#reply-form textarea").charCount({
				        allowed: 140,
				        warning: 25,
				        css: 'counter',
				        counterElement: 'span',
				        cssWarning: 'warning',
				        cssExceeded: 'exceeded',
				        counterText: '还可以输入'
				    });

	            });
			}
	        return false;
	    });
	    //提交回复
	    $("#reply-form").on("submit", function() {
	        var $this = $(this);
	    	if ( $this.find("textarea").is_empty()) {
	    		return false;
	    	}else{
                $this.modal("hide");
		        $.ajax({
		            type:"POST",
		            url: $this.attr("action"),
		            data: $this.serialize(),
		            cache: false,
		            dataType: "json",
		            success:function(data){
		                $("#messager").messager({message:data.msg});
		                if(data.code == 1){
		                	$("#reply-count-"+data.id).plus(1);
		                }
		            }
		        });
			}
	        return false;
	    });
	    //分享
	    $(".share").on("click", function() {
			//检查登录状态
			if( !$.is_signin()){
				return false;
			}else{//已登录
	            $("#share-form").load("/show_form/share", function(html){
	                $("#share-form").html(html);
	                $("#share-form").modal("show");
	                $("#share_id").val(dom.attr("date-id"));
	                $("#share-form textarea").focus();
	            });
			}
	        return false;
	    });
	    //提交分享
	    $("#share-form").on("submit", function() {
	        $.post($(this).attr("action"), $(this).serialize(), $(this).modal('hide'));
	        return false;
	    });


	    var $container = $('.thumbnails');
	    //$container.imagesLoaded(function(){
	        $container.masonry({
	            itemSelector : '.thumbnail'
	        });
	    //});
	    $container.infinitescroll({
	        navSelector  : '.pagination',
	        nextSelector : '.pagination a:contains("下一页")',
	        itemSelector : '.thumbnail',
	        extraScrollPx: 500,
	        loading: {
	            finished: undefined,
	            finishedMsg: "没有更多了...",
	            img: "/img/loading.gif",
	            msg: null,
	            msgText: "",
	            selector: null,
	            speed: 'fast',
	            start: undefined
	        }
	    },
	    function( newElements ) {
	        var $newElems = $( newElements ).css({ opacity: 0 });
	        //$newElems.imagesLoaded(function(){
	            $newElems.animate({ opacity: 1 });
	            $container.masonry( 'appended', $newElems, true ); 
	        //});
	    });

	    $("#returntop").returntop();
	})
}(window.jQuery)