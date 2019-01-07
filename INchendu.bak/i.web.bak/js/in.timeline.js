﻿$(function(){
	$('[data-spy="scroll"]').scrollspy('refresh');

	//点击任意位置隐藏回复框
    $(document).on("click", function(e){
		if ($(e.target).is("#reply-message,#reply-submit,div.reply-placeholder,a.reply_to,a.reply")){
			return true;
		}else{
			$("#reply-form").remove();
			$("div.reply-placeholder").show();
		}
    });
    $(".timeline-adventitia").delegate(".action > .reply, .reply-placeholder", "click", function(){
		var $this = $(this);
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
			var $item = $this.closest(".timeline-post"),
			$holder = $('div.reply-placeholder');
			//重置回复框
			$("#reply-form").remove();
			var $form = $('<form action="/reply/" id="reply-form" method="post" date-id="'+$this.attr("date-id")+'"><input type="hidden" name="reply_id" value="'+$this.attr("date-id")+'" /><input type="hidden" name="reply_to" value="'+$this.attr("date-type")+'" /><input type="hidden" name="reply_uid" value="'+$this.attr("date-uid")+'" /><input type="text" id="reply-message" name="message" maxlength="140" class="text" /><input type="submit" id="reply-submit" data-loading-text="……" value="发布" class="btn btn-primary" /></form>');
			$holder.each(function(){
				$(this).show();
			});
			$item.find('div.reply-placeholder').hide();
			$item.append($form);
			$form.show(function(){
				$form.find("#reply-message").focus();
			});
		}
    });
    $(".timeline-adventitia").delegate(".action > .reply_to", "click", function(){
		var $this = $(this);
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{
			//已登录
			var $item = $this.closest(".timeline-post"),
			$holder = $('div.reply-placeholder');
			//重置回复框
			$("#reply-form").remove();
			var $form = $('<form action="/reply/" id="reply-form" method="post" date-id="'+$this.attr("date-id")+'" date-uname="'+$this.attr("date-uname")+'" date-uid="'+$this.attr("date-uid")+'"><input type="hidden" name="reply_id" value="'+$this.attr("date-rid")+'" /><input type="hidden" name="reply_to" value="reply" /><input type="hidden" name="reply_uid" value="'+$this.attr("date-uid")+'" /><input type="text" id="reply-message" name="message" maxlength="140" autocomplete="off" class="text" /><input type="submit" id="reply-submit" data-loading-text="……" value="发布" class="btn btn-primary" /></form>');
			$holder.each(function() {
				$(this).show();
			});
			$item.find('div.reply-placeholder').hide();
			$item.append($form);
			$form.show(function() {
				$form.find("#reply-message").attr('placeholder','回复'+$this.attr("date-uname")).focus();
			});
		}
    });
    $(".timeline-adventitia").delegate(".action > .praise", "click", function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
	        btn = $(this);
	        $.ajax({
	            type: "POST",
	            url: "/praise/",
	            data: {"id":btn.attr("date-id"),"type":btn.attr("date-target")},
	            async: false,
	            cache: false,
	            dataType: "json",
	            success: function(data){
                    $("#messager").messager({message:data.msg});
	                if(data.code == 1){
	                	//+1
	                    $("#praise_count_"+btn.attr('date-id')).plus(1);

	                    btn.text("已赞").removeClass("praise").addClass("praised").off();
	                }
	            }
	        });
		}
        return false;
    });
    $(".timeline-adventitia").delegate(".action > .favorite", "click", function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
	        btn = $(this);
	        $.ajax({
	            type: "POST",
	            url: "/favorite/",
	            data: {"id":btn.attr("date-id"),"type":btn.attr("date-target")},
	            async: false,
	            cache: false,
	            dataType: "json",
	            success: function(data){
                    $("#messager").messager({message:data.msg});
	                if(data.code == 1){
	                    btn.text("已收藏").removeClass("favorite").addClass("favorited").off();
	                }
	            }
	        });
		}
        return false;
    });
    $(".timeline-adventitia").delegate("#reply-form", "submit", function(){
    	var $this = $(this),
    		$placeholder = $this.prev(".reply-placeholder"),
    		$item = $this.closest(".timeline-column"),
    		$container = $this.closest(".timeline-adventitia"),
    		action = $this.attr("action"),
    		form_data = $this.serialize(),
    		message = $this.find('#reply-message').val(),
    		uid = $this.attr("date-uid"),
    		uname = $this.attr("date-uname"),
    		post_id = $this.attr('date-id');
		if (message == '') return false;
    	$this.remove();
        $placeholder
        	.text(message.substr(0,26)+'...')
        	.css('background-image','url(/img/loading_mini.gif)')
        	.show();
    	var $rlist = $("#replies-" + post_id);
		if (!$rlist[0]) {
			$item
				.find(".post-body")
				.after('\
					<div class="reply-list hide">\
						<a href="/review/'+post_id+'/" target="_blank">查看全部回复</a>\
						<div class="reply-box">\
							<ul id="replies-'+post_id+'"></ul>\
							<div class="reply_arrow"><i class="arrow-border">◆</i><i class="arrow-inside">◆</i></div>\
						</div>\
					</div>\
				');
			$rlist = $("#replies-" + post_id);
		};
		$.ajax({
			type: "POST",
			url: action,
			data: form_data,
			dataType: "json",
			async: true,
			complete: function(xhr, textStatus) {
			},
			success: function(json){
				$("#messager").messager({message:json.msg});
				if(json.code == 1){
					//把新的回复加到前台显示
					var $repLi,
						replyto_str = '';
					if (uname){
						replyto_str = '回复<a href="/user/'+uid+'/" target="_blank">'+uname+'</a>';
					}
					$repLi = $('\
					<li class="hide">\
						<a class="avatar small" target="_blank" href="/user/'+online_id+'/">\
						<i></i><img alt="'+online_name+'" src="'+online_avatar+'" />\
						</a>\
						<div class="detail">\
						<a target="_blank" href="/user/'+online_id+'/">您</a>'+replyto_str+'<i>：</i><span class="grayDrak">'+message+'</span>\
						<div class="action"><span class="time">刚刚</span></div>\
						</div>\
					</li>\
					');
					$rlist.append($repLi);
					$rlist.closest(".reply-list").show();
					$repLi.fadeIn(1000);
					$container
						.find(".friangle-lt,.friangle-rt")
						.remove()
						.end()
						.masonry()
						.find('.timeline-column').each(function(){
	                        var posRight = $(this).css("left");
	                        if(posRight == "0px"){
	                            html = "<i class='friangle-lt'></i>";
	                            $(this).find('.feed').prepend(html);
	                        } else {
	                            html = "<i class='friangle-rt'></i>";
	                            $(this).find('.feed').prepend(html);
	                        }
					    });


	            	//+1
	                $("#reply_count_"+post_id).plus(1);
			        $placeholder
			        	.css('background-image','')
			        	.text("回复内容");
				}
			}
		});
		return false;
    	
    });

    //Timeline 滚动加载
    var scrolling = false;
    $(window).scroll( function()
    {
        //Prevent scroll stacking
        if (!scrolling)
        {
            scrolling = true;
            //don't unlock for 250ms
            setTimeout( function()
            {
                var d = $(window).data('lazyloads');
                if (!d || !d.length)
                {
                    return;
                }
    
                $( d ).each( function(i)
                {
                    if ( this && $.inviewport( this, {threshold:0} ) )
                    {
                        $(this).lazyshow();
                    }
                });

                scrolling = false;          
            }, 250);
                    
        }
    });
    $(window).trigger( 'scroll' );

	//首月
	$('#timeline-now-adventitia')
		.masonry({
			itemSelector: '.timeline-column',
			isResizable: false
		})
		.find('.timeline-column').each(function(){
            var posRight = $(this).css("left");
            if(posRight == "0px"){
                html = "<i class='friangle-lt'></i>";
                $(this).find('.feed').prepend(html);
            } else {
                html = "<i class='friangle-rt'></i>";
                $(this).find('.feed').prepend(html);
            }
	    });


    var $timeline_hd = $('.timeline-hd-title[href]');
    var offset = 10;
    $timeline_hd.lazyload();
    $timeline_hd.on('click',function(e){
        e.preventDefault();
        $timeline_hd[0].scrollIntoView(true);     	
	   	scrollBy(0, offset);

    });
    $timeline_hd.next().masonry({
		itemSelector: '.timeline-column',
		isResizable: false
	});
	
    
    var $nav = $('#timebar')
      , navTop = $('#timebar').length && $('#timebar').offset().top
      , isFixed = 0;

    $(window).on('scroll', function(){
      var i, scrollTop = $(window).scrollTop();
      if (scrollTop >= navTop && !isFixed) {
        isFixed = 1;
        $nav.addClass('fixed');
      } else if (scrollTop <= navTop && isFixed) {
        isFixed = 0;
        $nav.removeClass('fixed');
      }
    });

    $('.nav li > a').click(function(){
    	var href = $(this).attr("href");
    	var pos = $(href).offset().top;
	    $("html,body").animate({scrollTop: pos}, 200);
	    return false;
	});
	
	$("#returntop").returntop();
});
