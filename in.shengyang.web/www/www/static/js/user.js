$(function(){

	//回到顶部
	$.returntop();

	/*
	 * 滚动加载
	 */
	$.getScript('/static/js/jquery.infinitescroll.min.js', function(){
	    $('.posts').infinitescroll({
			navSelector  	: '.pagination',
			nextSelector 	: 'a:contains("加载更多")',
			itemSelector 	: '.post',
			debug		 	: true,
			dataType	 	: 'html',
			loading         : {
				msgText         : '加载中...',
				speed       : 0
			}
	    },function(newElements){
	        $(newElements).hide();
	        $(newElements).fadeIn();
         	$newElems.find('[rel="popover"]').popover();
			$newElems.find('[rel="tooltip"]').tooltip();
            $('.timeline-nav').affix('refresh');

		});
	})

	//关注
	$('.btn.follow[disabled!="disabled"]').live('click',function(){
		var $this = $(this);
		if( !$.is_signin()){
			return false;
		}else{
			$this.button('loading');
			$.ajax({
				type: "POST",
				url: "/do_follow/",
				data: {"bf":$this.data('id'),"do":$this.data('action')},
				dataType: "json",
				success: function(data){
					$this.button('reset');
					$.messager(data.msg);
					if(data.code == 1){
						$this.text("取消关注").data('action','uf').removeClass("btn-primary follow").addClass("followed");
					}
					return false;
				}
			});
		}
	});
	$('.btn.followed[disabled!="disabled"]').live('click',function(){
		var $this = $(this);
		if( !$.is_signin()){
			return false;
		}else{
			$this.button('loading');
			$.ajax({
				type: "POST",
				url: "/do_follow/",
				data: {"bf":$this.data('id'),"do":$this.data('action')},
				dataType: "json",
				success: function(data){
					$this.button('reset');
					$.messager(data.msg);
					if(data.code == 1){
						$this.text("加关注").data('action','f').removeClass("followed").addClass("btn-primary follow");
					}
					return false;
				}
			});
		}
	});
	$('#user_map').on('click', function() {
		$('#user-map-modal').modal('show');
	});
})