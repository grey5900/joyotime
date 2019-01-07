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
				signIn();
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
				//.modal();
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
