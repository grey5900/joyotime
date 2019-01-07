window.onerror = function (e) {
    return true;
};
//初始化兼容元素


//login

function signIn(){
	if($('.modal-loading').length){
		$('#modal').modal();
		$.ajax({
			url:'/web/index',
			dataType:'html',
			success:function(r){
				$('#modal').html(r);
			}
		});
	}
	else $('#modal').modal();
	
};
function checkUser(){
	if((typeof online_id === 'undefined') || online_id === null || online_id === 0 || online_id === ''){
		$('.header .sign_in').hide();
	}
	else{
		$('.header .sign_in').show().siblings().hide();
		//$('a[data-rel="popover"]').popover();
		
	}
}


$(function(){
	//search
	if(current_url.indexOf("/search")>=0){
		$('.search_bar').remove();
	}
	//index
	$('.carousel-indicators li').bind('mouseenter',function(){
		var $this = $(this);
		$('.carousel').carousel($this.index());
	});
	
	$('a[data-rel="popover"]').popover();
	$('[data-toggle="tooltip"],[rel="tooltip"]').tooltip();
	
	checkUser();
	
	
	
	$('.header .reg a').on('click',function(e){
		e.preventDefault();
		signIn();
	});
	
	//登录框自动伸缩
	/*$('#signin-modal').on('focusin','form',function(){
		if($(this).siblings('form').width()!=210)
		$(this).animate({'width':'310px'},300).siblings('form').animate({'width':'210px'},300);
	});*/
	
	//达人滑动效果
	$('.talent li').on('mouseenter',function(){
		if($.browser.msie && $.browser.version <=9){
			$(this).find('.img').animate({marginLeft:5},300);
			$(this).siblings().find('.img').animate({marginLeft:-60},300);
			$(this).find('.info').animate({paddingLeft:75},300);
			$(this).siblings().find('.info').animate({paddingLeft:10},300);
			
		}
		else{
			$(this)
			.addClass('active')
			.siblings()
			.removeClass('active');
		}
	})
	//二维码
	$('a.qrcode')
		.popover({
			placement:'left',
			html:true,
			trigger:'hover'
			//content:'<img src="http://pic-a.out.chengdu.cn/common/item100x100/boom.png" />'
			
		});
	//火狐上传按钮绑定方式
	if($.browser.mozilla){
		$('.header,.setting').on('click','.upload',function(e){
			if($(e.target).is('label')){
				$(e.target).find('input[type="file"]').trigger('click');
			}
		});
	}
	//收藏夹
	$('.header').on('click','#clt',function(e){
		e.preventDefault();
		var sURL = window.location.href,
			sTitle = $('title').html();
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
				alert("您的浏览器不支持直接添加，请使用Ctrl+D将网址添加到收藏夹");
			}
		}

	});
	//顶踩
	$(document).delegate('.action-like,.action-step','click',function(e){
		e.preventDefault();
		if(!$.checkAuth()) { return false; }
		var $this = $(this);
		var sort = parseInt($this.data('sort')) || 1;
		if($this.hasClass('on')){
			$.messager("你已经点击过了！");
			return;
		}
	    $.ajax({
	        type: 'POST',
	        url: '/common/praise',
	        data: {'id':$this.data('id'),'uid':online_id,'type':$this.data('type'),sort:sort},
			dataType: 'json',
	        cache: false,
	        success: function(json){				
				if(json.code != 0){
		            $.messager(json.msg);
				}else{
					$.messager(json.msg);
	                if($this.parent().hasClass('sub_tit')){
						var char = $this.text().slice(0,1);
						var num = $this.text().slice(2,-1);
						var other = $this.siblings('a');
						var oNum = other.text().slice(2,-1);
						var oChar = other.text().slice(0,1);
						oNum = oNum.length ? oNum : 0;
						if(!num.length){
							num = 0;
						}
						num = parseInt(num);
						num++;
						oNum = parseInt(oNum);
						oNum--;
						
						$this.text(char+'('+ num +')');
						other.removeClass('on')
							.text(oChar+'('+oNum+')');
						oNum < 0 && other.text(oChar);
					}
					else{
						var $num = $this.find('.num'),
							html = $num.html(),
							num = html ? html.match(/\-?\d+/g) : 0;
						num++;
						$num.html('('+num+')');
					}
					$this.addClass('on');
	            }
	        }
	    });
	    return false;
	});
	//收藏
	$('.intro_banner').on('click','#cltLoc',function(e){
		e.preventDefault();
		if($.checkAuth()){
			var $this = $(this);
			if($this.find('.collect').length){
				$.ajax({
					url:'/common/favorite_placecoll',
					type:'post',
					data:{id:$this.data('id')},
					dataType:'json',
					success:function(json){
						$.messager(json.msg);
					},
					error:function(){
						$.messager('通信失败，请检查网络');
					}
				});
				
			}
		};
	})
	//yy
	$('#tip-post').live('submit',function(){
		var $this = $(this);
		var cover = $('#cover');
		var content = $('#yText').val();
		if($this.data('submiting') === true){
			$.messager("请不要重复提交表单");
			return false;
		}
		if(content.length >= 10 && content.length <=500){
			$this.data('submiting',true);
			$.ajax({
				url:$this.attr('action'),
				type:'post',
				data:$this.serialize(),
				dataType:'json',
				success:function(json){
					if(json.code == 0){
						$.messager(json.msg);
						$('#preview').empty().hide();
						cover.empty();
						$('<input type="file" id="upload" name="Filedata">').appendTo(cover);
						$this.data('submiting',false);
						$('#yText').val('');
					}
					else{
						$.messager(json.msg);
						$this.data('submiting',false);
					}
				},
				error:function(){
					$.messager("通信失败，请检查网络！");
					$this.data('submiting',false);
				}
			});
		}
		else{
			$.messager("内容不能少于10字符大于500字符！");
		}
		return false;
	});
	
	//上传图片
	$('.upload #upload').live('change',function(){
		var $this = $(this);
		var cover = $('#cover');
		var preview = $('#preview');
		$.ajaxFileUpload({
			url:'/upload/upload_image',
			data:{uploadFile:$this.attr('name')},
			fileElementId:'upload',
			dataType:'json',
			type:'post',
			success:function(json){
				if(json.code != 1){
					$.messager(json.msg);
				}
				else{
					cover.html("");
					$('<img />').attr('src',json.msg).appendTo(preview);
					preview.show();
					$('<input type="hidden" id="photo" name="photo" >').val(json.msg).appendTo(cover);
					
				}
			}
		})
	})
	//图片预览关闭
	$('.header').on('click','#preview',function(){
		var $this = $(this);
		var cover = $('#cover');
		$this.empty();
		$this.hide();
		cover.empty();
		$('<input type="file" id="upload" name="Filedata">').appendTo(cover);
	});
	
	//点击关注
	$(document).on('click','.action-follow',function(e){
		e.preventDefault();
		var $this = $(this);
		if(!$.checkAuth()){
			$this.addClass('focusing');
			return false;
		}
		if(!$this.hasClass('disabled')){
			$this
				.addClass('disabled')
				.text($this.data('loading-text'))
				
		}
	});
	
	//回复提交
	$('html').on('submit','.reply-form',function(e){
		e.preventDefault();
		var $this = $(this),
			$txt = $this.find('input').length ? $this.find('input') : $this.find('textarea'),
			$btn = $this.find('.btn'),
			$num = $this.parent().siblings('.footbar').find('.action-reply .num'),
			content = $txt.val(),
			num = $num.html().length ? $num.html().match(/\-?\d+/g).join('')*1 : 0;
		$btn.addClass('posting').text('发布中...');
		$.ajax({
			url:'/common/reply',
			type:'post',
			data:{
				pid:$this.data('pid'),
				id:$this.data('id'),
				uid:$this.data('uid'),
				content:encodeURIComponent(content),
				type:$this.data('type')
			},
			dataType:'json',
			success:function(json){
				if(json.code > 0){
					$.messager(json.msg);
				}
				else{
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
					
					//重置
					$btn.removeClass('posting').text('发布');
					$txt.blur().val('');
					num++;
					$num.html('('+num+')');
				}
			}
		});
	});
	
	if($.browser.msie && $.browser.version <=7){
		$('.timeline').on('click','.action-share',function(){
			var $this = $(this),
				open = $this.hasClass('open'),
				$outer = $this.closest('.post');
				
			if(!open){
				
				$outer.css('z-index',1000);
			}
			else{
				$outer.css('z-index',1);
			}
		});
	}
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
    });
	$(".message_input textarea").charCount();
	//地点册
	$('.loc_album .img').on('click','.book',function(){
		var $this = $(this);
		var url = $this.prev().attr('href');
		window.open(url);
	});
	//placeholder
	'placeholder' in document.createElement('input') || $('input,textarea').placeholder();
})