!function($) {
    //插件
    $.fn.extend({
	    /*下载页 */
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
        },
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
	    /* 字数审核 */
	    charCount: function(givenOptions) {
	        return this.each(function() {
	            var $this = $(this),
	                options = $.extend({
	                    maxChars: 140,                  
	                    maxCharsWarning: 130,            
	                    msgFontSize: '12px',            
	                    msgFontColor: '#aaa',           
	                    msgFontFamily: 'Arial',      
	                    msgTextAlign: 'left',   
	                    msgWarningColor: '#e44443',
	                    msgAppendMethod: 'insertAfter', 
	                    msg: '还可以输入：',            
	                    msgPlacement: 'prepend',   
	                    numFormat: 'CURRENT/MAX' 
	                }, givenOptions);

				if(options.maxChars <= 0) return;

				var jqEasyCounterMsg = $("<div class=\"jqEasyCounterMsg\">&nbsp;</div>");
				var jqEasyCounterMsgStyle = {
					'font-size' : options.msgFontSize,
					'font-family' : options.msgFontFamily,
					'color' : options.msgFontColor,
					'text-align' : options.msgTextAlign,
					//'width' : $this.width(),
					'width' : '140px',
					'opacity' : 0,
					'display' : 'inline-block'
				};
				jqEasyCounterMsg.css(jqEasyCounterMsgStyle);
				jqEasyCounterMsg[options.msgAppendMethod]($this);
				$this
					.bind('keydown keyup keypress', doCount)
					.bind('focus paste', function(){setTimeout(doCount, 10);})
					.bind('blur', function(){jqEasyCounterMsg.stop().fadeTo( 'fast', 0);return false;});

				function doCount(){
					var val = $this.val(),
						length = val.length

					if(length >= options.maxChars) {
						val = val.substring(0, options.maxChars); 				
					};

					if(length > options.maxChars){
						var originalScrollTopPosition = $this.scrollTop();
						$this.val(val.substring(0, options.maxChars));
						$this.scrollTop(originalScrollTopPosition);
					};

					if(length >= options.maxCharsWarning){
						jqEasyCounterMsg.css({"color" : options.msgWarningColor});
					}else {
						jqEasyCounterMsg.css({"color" : options.msgFontColor});
					};

					if(options.msgPlacement == 'prepend')
					{
						html = options.msg + options.numFormat;
					}
					else
					{
						html = options.numFormat + options.msg;
					}
					html = html.replace('CURRENT', $this.val().length);
					html = html.replace('MAX', options.maxChars);
					html = html.replace('REMAINING', options.maxChars - $this.val().length);

					jqEasyCounterMsg.html(html);
	                jqEasyCounterMsg.stop().fadeTo( 'fast', 1);
				};
	        });
	    },
        /**
         * 加一
         *
         **/
        plus: function(number) {
        	var $this = $(this);
			if($this.first().html() == ''){
				$this.each(function(){
					$(this).html("("+number+")");
				})
			}else{
				var num = parseInt($this.first().html().replace("(","").replace(")",""));
				$this.each(function(){
					$(this).html("("+(num+number)+")");
				})
			}
        },
		//回复框自动伸缩
		autoText:function(option){
			return this.each(function(){
				var $this = $(this),
					dft = {minHeight:$this.outerHeight()},
					options = $.extend({},dft,option),
					space = parseInt($this.css('padding-top').split('p')[0]) + parseInt($this.css('padding-bottom').split('p')[0]);
					
				$this.bind('propertychange input',function(){
				
					var tHeight = this.scrollHeight;
					
					this.style.height = (tHeight-space) > options.minHeight ?  (tHeight-space) + 'px' : options.minHeight+'px';
					
				})
			})
		}

    });
    //方法
    $.extend({
		checkAuth : function() {
			if( (typeof online_id === 'undefined') || online_id === null || online_id === 0 || online_id === ''){
				$('.modal.in').modal('hide');
				$('#modal')
					.modal({
						'remote' : '/web/index'
					});
				return false;
			} else {
				return true;
			}
		},
        /**
         * 补零
         *
         * @param message : 'html'
         * @param refer : '/'
         **/
		padNum : function(num, n) {
		    var len = num.toString().length;
		    while(len < n) {
		        num = "0" + num;
		        len++;
		    }
		    return num;
		},
        /**
         * 积分提示
         *
         * @param message : 'html'
         * @param refer : '/'
         **/
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
        },
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
		modalSignin : function() {
            $('.modal.in').modal('hide');
			
			$('#modal')
				.modal({
					'remote' : '/web/index'
				});
            return false;
		},
		modalSignup : function() {
            $('.modal.in').modal('hide');
			
			$('#modal')
				.modal({
					'remote' : '/web/index'
				});
            return false;
		},
		signout: function() {
			$.ajax({
				type: 'GET',
				url: signout_url,
				dataType: 'jsonp',
				jsonpCallback: "callback",
				success: function(json){
					//SSO 退出登录
  					$.getScript(sso_logout_url,function(){
			            $.messager(json.message,'reload');
  					});
				}
			});
		}
    });
} (window.jQuery);


$(function(){
   

    $('[rel="popover"]').popover();
    $('[rel="tooltip"]').tooltip();
    $('[placeholder]').placeholder();

    $('#modal').on('shown',function(){
        var $this = $(this);
        $this.find('#signup-modal-form input[type!=submit]').each(function(){
            $(this).val('');
        });
        $this.find('#signin-modal-form input[type!=submit]').each(function(){
            $(this).val('');
        });
        $this.find('#signup-modal-form .caption p')
            .removeClass('error')
            .text('没有IN成都帐号？5秒钟急速注册。');
        $this.find('#signin-modal-form .caption p')
            .removeClass('error')
            .text('已有IN成都帐号，请登录。');
    })

	/*
	 * timeline 箭头
	 */
	$('.timeline').delegate('.post', 'mouseover', function(event) {
		$(this).find('.link').fadeIn();
	});

	$('.timeline').delegate('.post', 'mouseleave', function(event) {
		$(this).find('.link').fadeOut();
	});

	//回复按钮


	//发送回复按钮
	$('.timeline').delegate('.reply-form','focusin',function(){
        if(!$.checkAuth()) { return false; }
		var $this = $(this);
		$this.find('.btn').fadeIn();
	});
	$('.timeline').delegate('.reply-form','focusout',function(){
		var $this = $(this);
		if ($this.find('.txt').val() == ''){
			$(this).find('.btn').hide();
		}
	});
	$('.timeline').delegate('.reply-form .btn','click',function(){
		$(this).parent('form').trigger('submit');
	});
	$('.timeline').delegate('.photo img','click',function(){
		$(this).parent('.photo').toggleClass('on');
	});

	//发送回复
	$('html').delegate('.reply-form','submit',function(event){
		if(!$.checkAuth()) { return false; }
	    var $this = $(this),
			$textBox = $this.find('input').length ? $this.find('input') : $this.find('textarea');
			content = $textBox.val();
		// 重置回复框
		$textBox
			.val('')
			.attr('placeholder','回复内容...')
			.trigger('blur');

	    $.ajax({
			type: 'POST',
			url: '/common/reply',
			data:{'pid':$this.data('pid'),'id':$this.data('id'),'uid':$this.data('uid'),'content':encodeURIComponent(content),'type':$this.data('type')},
			dataType: 'json',
	        cache: false,
			success:function(json){
				if(json.code > 0){
		            $.messager(json.msg);
				}else{
					// 加入 DOM 到replylist
					var replyToStr = '';
					if ($this.data('type') == 'reply') {
						replyToStr = '回复<a href="/user/' + $this.data('uid') + '" target="_blank" class="name">' + $this.data('user') + '</a>';
					}
					$this.siblings('.reply-list')
						.prepend('\
						<li>\
							<a href="/user/' + online_id + '" class="avatar"><img src="' + online_avatar + '"></a>\
							<p><a href="/user/' + online_id + '" class="name">' + online_name + '</a>' + replyToStr + '：' + content + '<br/><span class="time">刚刚</span></p>\
						</li>\
						');
					if ($this.data('type') == 'reply') {
						$this.data('type', $this.closest('.post').data('type'));
						$this.data('user', '');
						$this.data('id', $this.closest('.post').data('id'));
					}
					// 加 1
					$('#reply_count_'+$this.data('id')).plus(1);
				}
			}
	    });
		event.preventDefault();
	});

	// 回复的回复
	$('html').delegate('.reply-list .action-replyto','click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this),
			$replyForm = $this.closest('.reply-list').prev('.reply-form')
			$textBox = $replyForm.find('input').length ? $replyForm.find('input') : $replyForm.find('textarea.txt');
		$replyForm.data('id', $this.data('id'));
		$replyForm.data('uid', $this.data('uid'));
		$replyForm.data('type', 'reply');
		$replyForm.data('user', $this.data('user'));
		$textBox
			.attr('placeholder','回复 ' + $this.data('user'))
			.focus();
		return false;
	});
	
	
	//回复
	$('html').delegate('.action-reply','click',function(){
		var $this = $(this);
			$replys = $this.closest('.footbar').next('.replys');
			$replys.toggleClass('hide');
        $('.channel-nav').affix('refresh');
        $('.timeline-nav').affix('refresh');
        return false;
	});

	//喜欢
	$('html').delegate('.action-like','click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this);
	    $.ajax({
	        type: 'POST',
	        url: '/common/praise',
	        data: {'id':$this.data('id'),'uid':online_id,'type':$this.data('type')},
			dataType: 'json',
	        cache: false,
	        success: function(json){
				if(json.code > 0){
		            $.messager(json.msg);
				}else{
					$.messager(json.msg);
	                $('#praise_count_'+$this.data('id')).plus(1);
	                $this.addClass('on');
	            }
	        }
	    });
	    return false;
	});
    /*
     * 弹出分享框
     */
	$('html').delegate('.action-share-weibo,.action-share-qq,.action-share-icd','click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this),
            $parend = $this.closest('.action-share'),
            $that = $("#share-form");
        $that.data('s',$this.data('s'));
        $that.data('type',$parend.data('type'));
        $that.data('id',$parend.data('id'));
        $that.find('textarea').val($this.data('content'));
        $that.modal('show');
	});
    /*
     * 发布分享
     */
    $('#share-form').on('submit',function(){
        var $this = $(this),
            $submit = $this.find('input[type=submit]');
        $submit.button('loading');
        $.ajax({
            type: 'POST',
            url: '/common/share',
            data: {'id':$this.data('id'),'uid':online_id,'s':$this.data('s'),'type':$this.data('type'),'content':$this.find('textarea').val()},
            dataType: 'json',
            cache: false,
            success: function(json){
                if(json.code == 4502 || json.code == 4506){
                    var r = confirm('分享到微博需要你绑定你的微博账号，现在去绑定？');
                    if(r){
                        window.location.href = '/profile/sync/'
                    }
                }else if(json.code > 0){
                    $.messager(json.msg);
                }else{
                    $.messager(json.msg);
                    $this.modal('hide');
                    $('#share_count_'+$this.data('id')).plus(1);
                }
                $submit.button('reset');
            }
        });
        return false;
    })
	$('html').delegate('.action-favorite','click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this);
	    $.ajax({
	        type: 'POST',
	        url: '/common/favorite',
	        data: {'id':$this.data('id'),'uid':online_id,'type':$this.data('type')},
			dataType: 'json',
	        cache: false,
	        success: function(json){
				if(json.code > 0){
		            $.messager(json.msg);
				}else{
	                $('#favorite_count_'+$this.data('id')).plus(1);
	                $this.addClass('on')
	                	.off();
	            }
	        }
	    });
	});

	$('.action-follow[disabled!="disabled"]').live('click',function(){
		//点击关注
		var $this = $(this);
		if(!$.checkAuth()) { $this.addClass('focusing');return false; }
		
		$this.button('loading');
		$.ajax({
			type: 'POST',
			url: '/user_follow/'+$this.data('uid'),
			dataType: 'json',
			success: function(data){
				if(data.code == 0){
					$this.button('reset');
					$.messager(data.msg);
					$this
						.text('取消关注')
						.data('loading-text','取消中...')
						.removeClass('action-follow')
						.addClass('action-unfollow');
				}else if(data.code == 1){
					$.modalSignin();
				}else if(data.code == -1 || data.code == -2) {
					$.messager(data.msg);
					$this.hide();
					$this.parent(".link-sj").hide();
				}
			}
		});
	});
	$('.action-unfollow[disabled!="disabled"]').live('click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this);
		$this.button('loading');
		$.ajax({
			type: 'POST',
			url: '/user_unfollow/'+$this.data('uid'),
			dataType: 'json',
			success: function(data){
				if(data.code == 0){
					$this.button('reset');
					$.messager(data.msg);
					$this
						.text('加关注')
						.data('loading-text','关注中...')
						.removeClass('action-unfollow')
						.addClass('action-follow');
				}else if(data.code == 1){
					$.modalSignin();
				}else if(data.code == -1 || data.code == -2) {
					$.messager(data.msg);
					$this.hide();
					$this.parent(".link-sj").hide();
				}
			}
		});
	});
	$(".message_input textarea").charCount();
//	$(document).delegate('.action-like','click',function(){
//		$.register();
//	})
//	$(document).delegate('.action-share','click',function(){
//		$.register();
//	})
	//注册登陆
	$('html').delegate('.person a.sin','click',function(e){
		e.preventDefault();
		
		if($.browser.msie && $.browser.version == 6){
			$('#modal').load('/web/index',function(){
				var $this = $(this);
				$this.find('.divider').css('display','inline');
				$('#signup-modal-form').css('overflow','hidden');
				$('#signin-modal').css('background-color','#fff');
				$this.find('.close').on('click',function(){
					$this.hide();
				})
			}).show().css({position:'absolute'});
		}
		else $.modalSignup();
		//ie placeholder
		if($.browser.msie && $.browser.version <10){
			setTimeout(function(){
				$('#modal').find('input,textarea').each(function(){
					var $this = $(this);
					if($this.attr('placeholder')){
						var _placeHolder = $this.attr('placeholder');
						if($this.attr('type')=='password'){
							//ie9以下不能更改type
							var clone;
							try{
								$this.show().siblings('input').remove();
								clone = $this.clone().attr('type','text').removeAttr('name').val(_placeHolder);
							}
							catch(e){
								clone = $('<input>');
								var attArr = {};
								
								$.each($this[0].attributes,function(i,n){
									if(n.secified && !/^jQuery\d+$/.test(n.name)){
										attArr[n.name] = n.value;
									}
								});
								clone.attr($.extend(attArr,{type:'text'})).removeAttr('name').val(_placeHolder);
							}
							$this.hide().before(clone);
							clone.bind('focus',function(){
								clone.hide().next().show().focus();
							});
							$this.on('blur',function(){
								$this.val() || $this.hide().prev().show();
							})
						}
						else{
							$this.val(_placeHolder);
							$this.on('focus',function(){
								$this.val() == _placeHolder ? $this.val('') : '';
							});
							$this.on('blur',function(){
								$this.val() == '' ? $this.val(_placeHolder) : '';
							})
						}
					}
				});
				$('#modal').addClass('placeholdered');
			},200)
		}
	});
	//回复框自动伸长
	$('textarea.txt').autoText();
	//顶部固定
	$('#header .sub-nav').affix();
	//收藏加
	$('#clt').bind('click',function(e){
		e.preventDefault();
		var sURL = window.location.href,
			sTitle = $('title').text();
	  try
		{
			window.external.addFavorite(sURL, sTitle);
		}
		catch (e)
		{
			try
			{
				window.sidebar.addPanel(sTitle, sURL, "");
			}
			catch (e)
			{
				alert("请使用Ctrl+D将网址添加到收藏夹");
			}
		}

	})
});




/**
 * 格式化输出的方法
 * @return string
 */
function sprintf() {
    var arg = arguments,
        str = arg[0] || '',
        i, n;
    for (i = 1, n = arg.length; i < n; i++) {
        str = str.replace(/%s/, arg[i]);
    }
    return str;
}
