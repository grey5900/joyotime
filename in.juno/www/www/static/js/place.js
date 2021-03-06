
$(function() {

	
	//回到顶部
	$.returntop();
	
	$('#place-map').on('click', function() {
        $("#map").html('');
		$('#place-map-modal').modal('show');
	});

	$('#place-map-modal').on('shown', function() {
		$("#map").mapper();
	});
	$('.text-more').each(function() {
		$this = $(this);
		$this.on('click', function() {
			$this.parent('.text').text($this.data('full-text')).end();
			$this.remove();
		});
	});

	$("a.item-qrcode[rel=popover]").popover({'html' : true}).click(function(e) {
		e.preventDefault();
	});

	$(".popover .close").live('click', function() {
		$("a[rel='popover']").popover('hide');
	});

	/*
	 * 滚动加载
	 */
	$('.posts').infinitescroll({
		navSelector : '.pagination',
		nextSelector : 'a:contains("加载更多")',
		itemSelector : '.post',
		dataType : 'html',
		loading : {
			msgText : '加载中...',
			speed : 0
		}
    },function(newElements){
        var $newElems = $( newElements ).css({ opacity: 0 });
            $newElems.animate({ opacity: 1 });
         	$newElems.find('[rel="popover"]').popover();
			$newElems.find('[rel="tooltip"]').tooltip();
	});

	$('.action-join[disabled!="disabled"]').live('click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this);
		$this.button('loading');
		$.ajax({
			type: 'POST',
			url: '/user_join/'+online_id,//$this.data('uid'),
			dataType: 'json',
			success: function(data){
				if(data.code == 0){
					$this.button('reset');
					$.messager(data.msg);
					$this
						.text('取消会员')
						.data('loading-text','取消会员中...')
						.removeClass('action-join')
						.addClass('action-unjoin');
				}else if(data.code == 1){
					$.modalSignin();
				}
			}
		});
	});
	$('.action-unjoin[disabled!="disabled"]').live('click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this);
		$this.button('loading');
		$.ajax({
			type: 'POST',
			url: '/user_unjoin/'+online_id,//$this.data('uid'),
			dataType: 'json',
			success: function(data){
				if(data.code == 0){
					$this.button('reset');
					$.messager(data.msg);
					$this
						.text('成为会员')
						.data('loading-text','成为会员中...')
						.removeClass('action-unjoin')
						.addClass('action-join');
				}else if(data.code == 1){
					$.modalSignin();
				}
			}
		});
	});

    $('#textarea')
    .on('focusin',function(){
    	$(this).animate({
    		height: '180px'},
    		300, function() {
    		// stuff to do after animation is complete
    	});
    })
    .on('focusout',function(){
    	if($(this).val() == '') {
	    	$(this).animate({
	    		height: '80px'},
	    		300, function() {
	    		// stuff to do after animation is complete
	    	});
	    }
    });
	//输入框提示
	$.checkAuth() ? $('.txt_tip').hide() : $('#modal').modal('hide');
	
    $.getScript('/static/js/timeline.min.js');
})
