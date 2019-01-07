$(function(){
	//留言滚动加载	
	console.log($('.timeline .posts'));
	console.log($('.timeline .posts').infinitescroll);
	$('.timeline .posts').infinitescroll({
		navSelector:'.timeline .pagination',
		nextSelector:'a:contains("加载更多")',
		itemSelector:'.post',
		dataType:'html',
		//maxPage:5,
		errorCallback:function(){
			$('.timeline .pagination').fadeIn();
		},
		loading:{
			msgText:'加载中...',
			img:"/static/skin/more.png",
			finishedMsg:"没有更多了...."
		}
	},function(newElements){		
		var $items = $(newElements);
		console.log($items);
		$items.find('[data-toggle="tooltip"],[rel="tooltip"]').tooltip();
		
		'placeholder' in document.createElement('input') || $items.find('textarea,input').placeholder();
	});
	//瀑布流	
	var $container = $('.waterfall .thumbnails');
	$container.imagesLoaded(function(){
		$container.masonry({
			itemSelector : '.thumbnail'
		});
	});	
	
	$container.infinitescroll({
		navSelector:'.waterfall .pages,.waterfall .pagination',
		nextSelector:'#nextPage,a:contains("加载更多")',
		itemSelector:'.thumbnail',
		errorCallback:function(){
			$('.waterfall .pagination').fadeIn();
		}
	},function(newElements){
		var $items = $(newElements);		
		$items.find('[data-toggle="tooltip"],[rel="tooltip"]').tooltip();
		$items.css('opacity',0);
		$items.imagesLoaded(function(){
			$items.animate({opacity:1});
			$container.masonry('appended',$items,true);
		});
		'placeholder' in document.createElement('input') || $items.find('textarea,input').placeholder();
	});
})