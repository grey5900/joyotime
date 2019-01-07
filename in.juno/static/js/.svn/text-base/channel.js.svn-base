$(function(){
	/*
	 * 滚动加载
	 */
    var $container = $('.timeline');
    $container.infinitescroll({
		navSelector  	: '.pagination a',
		nextSelector 	: '.pagination a:contains("下一页")',
		itemSelector 	: '.timeline .post',
		debug		 	: true,
		dataType	 	: 'html',
    }, function(newElements, data, url){
    });
    /*
     * 回复、分享、赞
     * contentfortest 用来测试的，最后去掉
     */
    $('.action-reply').on('click',function(){
    	var $this = $(this);
    	$.get('auto.html?' + event.timeStamp + '#contentfortest.replys', function(html){
    		$this.colsest('.replys').html(html);
    	});
    	
    });
    
    /*
     * 签到动态
     */
	var scrtime;
 	$("#feedroll").hover(function(){
		clearInterval(scrtime);
	},function(){
		scrtime = setInterval(function(){
			var $ul = $("#feedroll ul");
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
	
	/*
	 *宝宝时钟倒计时
	 */
	countdown = setInterval(function(){
	    var now = new Date();
		$("#babyclock .more").html(now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds());
		if (now.getSeconds() == 0) {
			$("#babyclock .more").attr({
				title: 'get ' + now.getHours() + ':' + now.getMinutes()
			});
		}
	}, 1000);

	/*
	 *房产微博
	 */
	$('#weibo .tit a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})


});
