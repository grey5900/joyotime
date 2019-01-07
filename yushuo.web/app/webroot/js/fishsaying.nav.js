$(function(){
	var nav = $('.header .nav');
	var bd = nav.find('.site_nav i');
	var active = nav.find('.active');
	bd.css({width:active.width(),backgroundColor:nav.css('border-bottom-color')});
	bd.css({'left':active[0].offsetLeft,width:active[0].clientWidth});
	var oTheme = {
		baby:'#fc7791',
		digital:'#30a5de',
		edu:'#99cef1',
		f:'#96292b',
		auto:'#696053',
		fb:'#ff7104',
		home:'#9c7f73',
		mes:'#265174',
		movie:'#fd5502',
		tour:'#a6c526'
	}
	if(window.theme){
		bd.css('background-color',oTheme[theme]);
		nav.css('border-color',oTheme[theme]);
	}
	nav.find('a').on('mouseenter',function(e){
		$(this).addClass('active').siblings().removeClass('active');
		bd.stop();
		bd.animate({'left':this.offsetLeft,width:this.clientWidth},200);
	});	
})
	
