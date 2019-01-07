// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

!function($) {
	var loading = false;
    $.signout = function(){
    	$.ajax({
    		type:"GET",
    		url:"/signout/",
    		dataType:"json",
    		success:function(json){
    			if(json.code == 1){
    				window.location.href = '/';
    			}else{
					$("#messager").messager({message:json.msg});
    			}
    		}
    	});
    }
    $.is_signin = function(){
		if( !online_id ){
			var $signin = $("#signin-modal");
			if( $.trim($signin.html()) == ''){
				$signin.load('/show_form/signin/',function(html){
					$signin
						.html(html)
						.modal("show");
					$signin.find('input[placeholder], textarea[placeholder]').placeholder();
				});
			}
			else
			{
				$signin.modal("show");
			}
			return false
		}else{
			return true
		}
    }
    /**
     * jQuery Cookie
     * 
     **/
    $.cookie = function(key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
            options = $.extend({},
            options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires,
                t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = String(value);

            return (document.cookie = [encodeURIComponent(key), '=', options.raw ? value: encodeURIComponent(value), options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path: '', options.domain ? '; domain=' + options.domain: '', options.secure ? '; secure': ''].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var decode = options.raw ?
        function(s) {
            return s;
        }: decodeURIComponent;

        var pairs = document.cookie.split('; ');
        for (var i = 0,
        pair; pair = pairs[i] && pairs[i].split('='); i++) {
            if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
        }
        return null;
    };
    $.fn.emoji = function() {
        return this.each(function(){
            $(this).html(
                $(this).html().replace(/([\ue001-\ue537])/g, $.fn.emoji.replacer)
            );
        });
    };

    $.fn.emoji.replacer = function (str, p1) {
        return p1.charCodeAt(0).toString(16).toUpperCase().replace(
            /^([\da-f]+)$/i,
            '<img src="/emoji/emoji-$1.png" alt="emoji" />'
        );
    }


    /**
     * jQuery Placeholder
     * 
     **/
    function Placeholder(input) {
        this.input = input;
//        if (input.attr('type') == 'password') {
//            this.handlePassword();
//        }
        // Prevent placeholder values from submitting
        $(input[0].form).submit(function() {
            if (input.hasClass('placeholder') && input[0].value == input.attr('placeholder')) {
                input[0].value = '';
            }
        });
    }
    Placeholder.prototype = {
        show: function(loading) {
            // FF and IE saves values when you refresh the page. If the user refreshes the page with
            // the placeholders showing they will be the default values and the input fields won't be empty.
            if (this.input[0].value === '' || (loading && this.valueIsPlaceholder())) {
//                if (this.isPassword) {
//                    try {
//                        this.input[0].setAttribute('type', 'text');
//                    } catch(e) {
//                        this.input.before(this.fakePassword.show()).hide();
//                    }
//                }
                this.input.addClass('placeholder');
                this.input[0].value = this.input.attr('placeholder');
            }
        },
        hide: function() {
            if (this.valueIsPlaceholder() && this.input.hasClass('placeholder')) {
                this.input.removeClass('placeholder');
                this.input[0].value = '';
                if (this.isPassword) {
                    try {
                        this.input[0].setAttribute('type', 'password');
                    } catch(e) {}
                    // Restore focus for Opera and IE
                    this.input.show();
                    this.input[0].focus();
                }
            }
        },
        valueIsPlaceholder: function() {
            return this.input[0].value == this.input.attr('placeholder');
        },
        handlePassword: function() {
            var input = this.input;
            input.attr('realType', 'password');
            this.isPassword = true;
            // IE < 9 doesn't allow changing the type of password inputs
            if ($.browser.msie && input[0].outerHTML) {
                var fakeHTML = $(input[0].outerHTML.replace(/type=(['"])?password\1/gi, 'type=$1text$1'));
                this.fakePassword = fakeHTML.val(input.attr('placeholder')).addClass('placeholder').focus(function() {
                    input.trigger('focus');
                    $(this).hide();
                });
                $(input[0].form).submit(function() {
                    fakeHTML.remove();
                    input.show()
                });
            }
        }
    };
    var NATIVE_SUPPORT = !!("placeholder" in document.createElement("input"));
    $.fn.placeholder = function() {
        return NATIVE_SUPPORT ? this: this.each(function() {
            var input = $(this);
            var placeholder = new Placeholder(input);
            placeholder.show(true);
            input.focus(function() {
                placeholder.hide();
            });
            input.blur(function() {
                placeholder.show(false);
            });

            // On page refresh, IE doesn't re-populate user input
            // until the window.onload event is fired.
            if ($.browser.msie) {
                $(window).load(function() {
                    if (input.val()) {
                        input.removeClass("placeholder");
                    }
                    placeholder.show(true);
                });
                // What's even worse, the text cursor disappears
                // when tabbing between text inputs, here's a fix
                input.focus(function() {
                    if (this.value == "") {
                        var range = this.createTextRange();
                        range.collapse(true);
                        range.moveStart('character', 0);
                        range.select();
                    }
                });
            }
        });
    }

    $.fn.extend({
        /**
         * 返回头部
         * 
         * $("#returntop").returntop();
         **/
        returntop: function() {
            var $el = $(this);
            //add dom
            if (!$el[0]) {
                var $el = $("<div id=\"returntop\"></div>");
                $("body").append($el);
            }
            var obj = $el.click(function(){
                $("html, body").animate({
                    scrollTop: 0
                },
                120)
            });
            $(window).bind("scroll",function(){
                var topHeight = $(document).scrollTop(),
                winHeight = $(window).height();
                if (200 < topHeight) {
                    obj.fadeIn();
                } else {
                    obj.fadeOut();
                }
            })
        },
        /**
         * 积分提示
         *
         * @param message : 'html'
         * @param delay : '3000'
         **/
        messager: function(options) {
            var $el = $(this);
            //add dom
            if (!$el[0]) {
                var $el = $("<div id=\"messager\"></div>");
                $("body").append($el);
            }
            $el.html(options.message);
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
            .delay(options.delay ? options.delay: '3000')
            .animate({
                opacity: '0',
                top: elTop + 10,
                queue: true
            }, 200,
            function() {
            	$el.remove();
            	if (options.refer) {
            		if (options.refer == 'reload') {
	            		window.location.reload();
            		} else {
            			window.location.href = options.refer;
            		}
            	}
            });
        },
        /**
         * 登录栏
         * 
         * $("#signin-pop").signinpop();
         **/
        signinpop: function() {
            if (this[0]) {
                var obj = this;
                $(window).bind("scroll",
                function() {
                    var topHeight = $(document).scrollTop(),
                    winHeight = $(window).height();
                    if (1000 < topHeight) {
                        obj.slideDown("fast");
                    } else {
                        obj.slideUp("fast");
                    }
                })
            }
        },
        scrollLine: function() {
			var scrtime;
		 	$("#con").hover(function(){
				clearInterval(scrtime);
			
			},function(){
			
				scrtime = setInterval(function(){
					var $ul = $("#con ul");
					var liHeight = $ul.find("li:last").height();
					$ul.animate({marginTop : liHeight+40 +"px"},1000,function(){
					
					$ul.find("li:last").prependTo($ul)
					$ul.find("li:first").hide();
					$ul.css({marginTop:0});
					$ul.find("li:first").fadeIn(1000);
					});	
				},3000);
			
			}).trigger("mouseleave");
	
	    },
        displayFeedback: function() {
            if (this[0]) {
                var obj = $(document).append("<div id=\"feedback\"><div class=\"\"></div></div>");
            }
        },
        /**
         * 复制文字
         *
         **/
        copy: function() {
            if (document.all) {
                window.clipboardData.setData('text', this.val());
                alert("复制成功。");
            } else {
                alert("您的浏览器不支持剪贴板操作，请自行复制。");
            }
        },
        /**
         * 未知
         *
         **/
        close: function(id) {
            var obj = $("#" + id);
            var mask = $("#mask");
            obj.hide();
            mask.hide();
        },
        /**
         * 未知
         *
         **/
        pop: function(id) {
            var obj = $("#" + id);
            var body_height = document.body.clientHeight;
            var window_height = $(window).height();
            var window_width = $(window).width();
            var obj_height = obj.height();
            var obj_width = obj.width();
            var scroll_height = $(document).scrollTop();
            if (window_height > body_height) {
                body_height = window_height;
            }
            $("#mask").css({
                "display": "block",
                "height": body_height
            });
            obj.css({
                "display": "block",
                "top": (window_height - obj_height) / 2 + scroll_height,
                "left": (window_width - obj_width) / 2
            });
        },
        /**
         * 震动
         *
         **/
        shake: function() {
            var $this = $(this);
            box_left = ($(window).width() - $this.outerWidth()) / 2;
            $this.css({
                'left': box_left,
                'position': 'absolute',
                'margin-left': 0
            });
            for (var i = 1; 4 >= i; i++) {
                $this.animate({
                    left: box_left - (4 - i)
                },
                50);
                $this.animate({
                    left: box_left + 2 * (4 - i)
                },
                50);
            }
            return this;
        },
        /**
         * 声音
         *
         **/
        sound: function(which) {
            var soundEmbed = null;
            if (!soundEmbed) {
                soundEmbed = document.createElement("embed");
                soundEmbed.setAttribute("type", "audio/mpeg");
                soundEmbed.setAttribute("hidden", true);
                soundEmbed.setAttribute("autostart", true);
                soundEmbed.setAttribute("src", which);
            } else {
                document.body.removeChild(soundEmbed);
                soundEmbed.removed = true;
                soundEmbed = null;
                soundEmbed = document.createElement("embed");
                soundEmbed.setAttribute("type", "audio/mpeg");
                soundEmbed.setAttribute("hidden", true);
                soundEmbed.setAttribute("autostart", true);
                soundEmbed.setAttribute("src", which);
            }
            soundEmbed.removed = false;
            document.body.appendChild(soundEmbed);
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
        /**
         * 字数统计
         *
         **/
        charCount: function(options) {

            var defaults = {
                allowed: 140,
                warning: 10,
                css: 'counter',
                counterElement: 'span',
                cssWarning: 'warning',
                cssExceeded: 'exceeded',
                counterText: ''
            };

            var options = $.extend(defaults, options);

            function calculate(obj) {
                var count = $(obj).val().length;
                var available = options.allowed - count;
                if (available <= options.warning && available >= 0) {
                    $(obj).next().addClass(options.cssWarning);
                } else {
                    $(obj).next().removeClass(options.cssWarning);
                }
                if (available < 0) {
                	//设置为0
	            	available = 0;
	            	//截断超出部分
	            	$(obj).val($(obj).val().substr(0,options.allowed));
                    $(obj).next().addClass(options.cssExceeded);
                } else {
                    $(obj).next().removeClass(options.cssExceeded);
                }
                $(obj).next().html(options.counterText + available);
            };

            this.each(function() {
                $(this).after('<' + options.counterElement + ' class="' + options.css + '">' + options.counterText + '</' + options.counterElement + '>');

                calculate(this);
                $(this).keyup(function() {
                    calculate(this)
                });
                $(this).change(function() {
                    calculate(this)
                });
            });

        },
		//＋1
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
        /**
         * 回复的回复
         *
         * @param message : 'html'
         * @param delay : '3000'
         **/
        replyTo: function(options) {
            var $item = this,
            $form = $("#reply_to_form");
            if (!$form[0]) {
                $form = $('<form action="/review_reply" id="reply_to_form" method="post"><input type="hidden" value="0" name="rid" id="reply_rid" /><input type="hidden" id="reply_pid" value="0" name="pid"/><input name="content" maxlength="140" id="message" class="text" /><input type="submit" value="回复" class="btn btn-primary" /></form>');
            }
            $form.hide();
            $item.closest('li').append($form);
            $form.find("input[name='rid']").val(options.rid);
            if (options.pid != '') $form.find("input[name='pid']").val(options.pid);
            $form.find("input.text").val('');
            $form.slideDown('fast',
            function() {
                $form.find("input.text").focus();
            });
        },
        /**
         * 删除回复
         *
         * @param message : 'html'
         * @param delay : '3000'
         **/
        replyDelete: function(options) {
        },
        lazyload: function()
        {
            var d = $(window).data('lazyloads');
            if (!d)
            {
                d = [];
            }
            this.each( function() { d.push(this); } );
            $(window).data('lazyloads', d );
        },
        
        lazyshow: function()
        {
            return this.each(function()
            {
                var $this = $(this),
                    $container = $this.next();

				if ( ! $this.hasClass('loaded') && loading == false) {
						loading = true;
						$this.addClass('loading loaded');
						$.ajax({
							url: $this.attr('href'),
							type: 'get',
							dataType: 'html',
							success: function(data) {
								$container
									.append(data)
									.masonry('reload')
									.animate({opacity: 1},function(){
										$('[data-spy="scroll"]').scrollspy('refresh');
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
                               
								$this.removeClass('loading');
								loading = false;
							},
							error: function(xhr, textStatus, errorThrown) {
								$this.removeClass('loading loaded');
								loading = false;
							}
						});
				}
            });
        }
    })
    
} (window.jQuery);
$(function() {
    //Jquery place plugin
    $('input[placeholder], textarea[placeholder]').placeholder();
    //IE6 selector fix
    $('input[type=text]').addClass('text-input');
    $('input[type=password]').addClass('text-input');
    $('.list-reply li:odd').addClass('odd');
    $('.list-reply li:even').addClass('even');
//    $("textarea[maxlength]").on('keyup change',function(){
//        var $area=$(this);
//        var max=parseInt($area.attr("maxlength"),10);
//        if(max>0){
//            if($area.val().length>max){
//                $area.val($area.val().substr(0,max));
//            }
//         }
//    });
//  焦点时变长和语音输入有冲突
//	$('.search-query').on('focus webkitspeechchange',function(){
//		var $this = $(this);
//		$this.animate({
//			width : '85%'
//		},
//		500);
//	});
//	$('.search-query').on('blur',function(){
//		var $this = $(this);
//		$this.animate({
//			width : '60%'
//		},
//		500);
//	});
    $('#signin-modal').on('submit',function(){
        var $form = $(this),
        	$submit = $form.find('input[type=submit]');
        $submit.button('loading');
        $.ajax({
            type : "POST",
            url : $form.attr("action"),
            data : $form.serialize(),
            dataType : "json",
            success : function(data) {
                if(data.code == 1) {
                    $form.modal('hide');
                    window.location.reload();
                } else {
                	alert(data.msg);
                }
                $submit.button('reset');
            },
            error : function(data) {
            	alert('登录失败，请重试。');
            	$submit.button('reset');
            }
        });
        return false;
    });
});