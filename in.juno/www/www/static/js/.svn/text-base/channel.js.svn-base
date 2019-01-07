$(function(){

	//回到顶部
	$.returntop();


	/*
	 * 滚动加载
	 */
	$container = $('.posts');
    $container.infinitescroll({
		navSelector  	: 'div.pagination',
		nextSelector 	: 'a:contains("加载更多")',
		itemSelector 	: '.post',
		//debug		 	: true,
		dataType 		: 'html',
		loading         : {
			msgText         : '加载中...',
			speed       : 0
		}
    },function(newElements){
        var $newElems = $( newElements ).css({ opacity: 0 });
            $newElems.animate({ opacity: 1 });
         	$newElems.find('[rel="popover"]').popover();
			$newElems.find('[rel="tooltip"]').tooltip();
        $('.channel-nav').height() > $('.main').height() ||  $('.channel-nav').affix('refresh');
	});



    /*
     * 签到动态
     */
	var scrtime;
	$('.feedroll').each(function(){
		var $this = $(this);
	 	$this.hover(function(){
			clearInterval(scrtime);
		},function(){
			scrtime = setInterval(function(){
				var $ul = $this.find("ul");
				var liHeight = $ul.find("li:last").outerHeight();
				$ul.animate(
					{ marginTop : liHeight +"px" },
					1000,
					function(){
						$ul.find("li:last").prependTo($ul)
						$ul.find("li:first").hide();
						$ul.css({marginTop:0});
						$ul.find("li:first").fadeIn(1000);
					});	
			},6000);
		}).trigger("mouseleave");
	})
	
	/*
	 *宝宝时钟倒计时
	 */
    var $babyclock = $('#babyclock');
	function getBaby(hours,minutes) {
		$.ajax({
		  url: '/babyclock/get_info/' + hours + '/' + minutes,
		  type: 'GET',
		  dataType: 'json',
		  cache: false,
		  success: function(json, textStatus, xhr) {
		  	if (json.id) {
			  	if (json.gender == 0) {
			  		var genderStr = 'girl',
			  		genderStrCn = '女',
			  		genderStrThird = '她';
			  	} else {
			  		var genderStr = 'boy',
			  		genderStrCn = '男',
			  		genderStrThird = '他';
			  	}
			    $babyclock
			    	.find('.photo').html('\
			    		<a href="/babyclock/' + json.id + '" target="_blank">\
			    			<img src="' + json.medium + '" />\
							<em class="t"><span>' + json.surport + '人支持' + genderStrThird + '</span>' + json.name + '<i class="sex ' + genderStr + '">' + genderStrCn + '</i></em>\
			    		</a>\
			    	');

		  	}
		  }
		});
	}
    if ($babyclock[0]) {
    	//首次
		getBaby(new Date().getHours(),new Date().getMinutes());
		
    	var $clock = $babyclock.find('.tit .more');
		countdown = setInterval(function(){
		    var now = new Date();
			$clock.html($.padNum(now.getHours(),2) + ':' + $.padNum(now.getMinutes(),2) + ':' + $.padNum(now.getSeconds(),2));
			if (now.getSeconds() == 0) {
				getBaby(now.getHours(),now.getMinutes());
			}
		}, 1000);
    }

	/*
	 *房产微博
	 */
	$('#weibo .tit a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})

	/*
	 * 幻灯
	 */
	$('#focus').carousel();

    /*
     * 发言
     */

    $('#channel-say-btn').on('click',function(event){
        var $this = $(this),
            $that = $('#review-form');
        $this.addClass("on");
        $that.show(0,function(){
			if(!$.checkAuth())return false;
            $that.find('#textarea').trigger('focus');
        });
        event.preventDefault();
    });

    $.getScript('/static/js/timeline.min.js');
});

