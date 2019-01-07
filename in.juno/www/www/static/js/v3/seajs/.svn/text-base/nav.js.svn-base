define(function(require,exports,module){
	//var $ = require('jquery');
	var nav = $('.header .nav');
	var bd = nav.find('.site_nav i');
	var active = nav.find('.active');
	//console.log(nav.css('border-bottom-color'))
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
	exports.hover = function(){
		if(window.theme){
			bd.css('background-color',oTheme[theme]);
			nav.css('border-color',oTheme[theme]);
		}
		nav.find('a').on('mouseenter',function(e){
			$(this).addClass('active').siblings().removeClass('active');
			//nav.css('border-color',this.getAttribute('data-color'));
			bd.stop();
			//bd.css({backgroundColor:this.getAttribute('data-color')});
			bd.animate({'left':this.offsetLeft,width:this.clientWidth},200);
		})
	}
	
})