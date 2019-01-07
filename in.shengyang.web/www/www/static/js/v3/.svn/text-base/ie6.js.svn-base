$.fn.carousel = function(num){
	this.find('.carousel-inner > .item').eq(num).addClass('active').show().siblings().removeClass('active').hide();
	this.find('.carousel-indicators > li').eq(num).addClass('active').siblings().removeClass('active');
};
$.fn.modal = function(option){
	var $modal = this;
	var $shadow = $('.modal-backdrop').length ? $('.modal-backdrop') : $("<div class=\"modal-backdrop\"></div>").appendTo('body');
	var _width = document.body.clientWidth,
		_height = document.body.clientHeight,
		_top = document.body.scrollTop,
		wHeight = window.innerHeight || document.documentElement.clientHeight,
		wWidth = window.innerWidth || document.documentElement.clientWidth;
	$modal
		.css({
			left:'50%',
			top:(wHeight-300)/2+_top,
			position:'absolute'
		})
		.show();
	$shadow
		.css({
			'width':_width,
			'height':_height
		})
		.show();
	$shadow.on('click',function(){
		$modal.hide();
		$(this).hide();
	});
	$modal.on('click','.close',function(){
		$modal.hide();
		$shadow.hide();
	});
	switch(option){
		case 'hide':
		$modal.hide();
		$shadow.hide();
		break;
	}
	return $modal;
};
//style
$(document).ready(function(){
	
})