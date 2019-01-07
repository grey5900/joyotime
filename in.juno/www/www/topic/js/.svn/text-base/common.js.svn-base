$(function(){
	//story
	var fileMenu = "images/story",
		arr = [];
	$('.inner img').each(function(i,n){
		var name = n.src.slice(0,-4);
		var str = "<li><a href=''><img src='"+name+"（2）.png'></a></li>";
		arr.push(str);
	});
	$('.trigers ul').html(arr.join(''));
	var items = $('.banner .inner').find('.items')
		,box = $('.banner .inner')
		,dis = items.eq(0).outerWidth()
		,trigers = $('.trigers').find('li')
		,tBox = $('.trigers').find('ul')
		,tDis = trigers.eq(1).outerWidth(true)
		,max = items.length
		,i = 0
		,p = 0
		,tPage = Math.ceil(max/5);
	box.width(max*dis);
	tBox.width(max*tDis);
	function show(n){
		if(n<0)i= 0;
		if(n>=max)i = 0;
		box.animate({marginLeft:-i*dis});
		trigers.eq(i).addClass('active').siblings().removeClass('active');
	};
	function sPage(n){
		if(n>tPage || n < 0) p =0;
		tBox.animate({marginLeft:-p*tBox.parent().width()-5});
	};
	$('.inner .prev').on('click',function(e){
		
		e.preventDefault();
		i--;
		show(i);
	});
	$('.inner .next').on('click',function(e){
		e.preventDefault();
		i++;
		show(i);
	});
	$('.trigers .prev').on('click',function(e){
		e.preventDefault();
		p--;
		sPage(p);
	});
	$('.trigers .next').on('click',function(e){
		e.preventDefault();
		p++;
		sPage(p);
	});
	trigers.click(function(e){
		e.preventDefault();
		var $this = $(this);
		$this.addClass('active').siblings().removeClass('active');
		i = $this.index();
		show(i);
	});
	//story end
	$('.side li').mouseenter(function(){
		var $this = $(this)
			,top = $this.position().top
			,glass = $('#glass').length ? $('#glass') : $('.glass').clone().attr('id','glass').appendTo($('.side'))
			,topCss = '0 '+(-top)+'px'
			,link = $this.find('a').attr('href');
		glass.show().css({top:top,backgroundPosition:topCss});
		$this.addClass('active').siblings().removeClass('active');
		glass.attr('data-tar',$this.attr('data-tar'));
		glass.attr('data-link',link);
	});
	$('.side li').click(function(e){
		e.preventDefault();
		var $this = $(this)
			,top = $this.position().top
			,glass = $('#glass').length ? $('#glass') : $('.glass').clone().attr('id','glass').appendTo($('.side'))
			,topCss = '0 '+(-top)+'px'
			,link = $this.find('a').attr('href');
		glass.show().css({top:top,backgroundPosition:topCss});
		$this.addClass('active').siblings().removeClass('active');
		glass.attr('data-tar',$this.attr('data-tar'));
		glass.attr('data-link',link);
		glass.click();
	});
	if($('.side li.active').length){
		var $this = $('.side li.active'),
			topNum = $this.position().top,
			topCss = '0 '+(-topNum)+'px',
			tar = $this.attr('data-tar'),
			link = $this.find('a').attr('href');
		$('.glass').attr('data-tar',tar).show().css({top:topNum,backgroundPosition:topCss});
		$('.'+tar).show();
		$('.glass').attr('data-link',link);
	};
	$(document).on('click','.glass',function(){
		//模拟跳转
		
		
		var $this = $(this);
		var tar = $this.attr('data-tar'),
			row = $('.side li.active'),
			top = row.position().top,
			topCss = '0 '+(-top)+'px',
			glass = $this.data('clone') ? $this : $this.clone().removeAttr('id')
			,target = $('.'+tar),
			visible = $('.'+tar+':visible').length ? true : false,
			link = $this.attr('data-link');
		if(link){
			window.location.href=link;
		}
		else if(!$('.primary').length)window.location.href = '/topic/second_ring2';
		$('.primary .toggle').hide();
		$('#img').attr('src','/images/map.jpg');
		$('.title').slideUp();
		switch(tar){
			case 'bus' :
			$('.overlay').hide();
			$('.brige_link').hide();
			if(visible){
				target.hide();
				$('.glass').length >1 ? $this.remove() : $this.hide();
				tar == 'brige' && $('.brige_link').hide();
			}
			else{
				$('.glass').remove();
				target.show();
				tar == 'brige' && $('.brige_link').show();
				glass.show().data('clone',true).show().css({top:top,backgroundPosition:topCss}).appendTo($('.side'));
			}
			break;
			
			default:
				
			$('.bus').hide();
			$('.glass[data-tar=bus]').remove();
			if(visible){
				if(tar =='brige' && $('.side li:first').hasClass('active'))return;
				target.hide();
				$('.glass').length >1 ? $this.remove() : $this.hide();
				tar == 'brige' && $('.brige_link').hide();
			}
			else{
				target.show();
				if(tar == 'down' || tar=='stack' && !link){
					$('.brige_link').show();
					$('.side li:first').click();
				}
				tar == 'brige' && $('.brige_link').show();
				glass.show().data('clone',true).show().css({top:top,backgroundPosition:topCss}).appendTo($('.side'));
			}
			
		}
	});
	var data = {
		'ymk':'营门口',
		'bx':'北新',
		'rj':'刃具',
		'sbq':'衫板桥',
		'sqz':'双桥子',
		'hxl':'红星路',
		'rn':'人南',
		'yf':'永丰',
		'sn':'双楠',
		'cw':'成温'
	};
	$('.primary .toggle .left').click(function(e){
		
		$('.title .prev').trigger('click');
	});
	$('.primary .toggle .right').click(function(e){
		$('.title .next').trigger('click');
	});
	$('.brige_link a').click(function(e){
		e.preventDefault();
		var tar  = $(this)[0].className,
			img = $('#img');
		$('.primary .toggle').show();
		$('.overlay').hide();
		$(this).parent().hide();
		$('.title').slideDown();
		$('#address').text(data[tar]+'立交');
		window.location.hash = tar;
		$('.glass').length > 1 ? $('.glass').hide().not('#glass').remove() : $('.glass').hide();
		img.attr('src','/images/'+tar+'.png');
	});
	
	$('.title a').click(function(e){
		e.preventDefault();
		var cur = window.location.hash.slice(1);
		var tar = $('.brige_link').find('.'+cur);
		var curClass = $(this).attr('class');
		var target = tar[curClass]();
		if(!target.length){
			curClass == 'prev' ? target = $('.brige_link > a:last') : target = $('.brige_link > a:first');
		};
		target.click();
	});
});