$(function(){
	$('.carousel').carousel({
	  interval: 5000
	});

    $('input[value="填写报名表"]').on('click',function(){
        if(!$.checkAuth()) { return false; }
    });
	$('#event-enter-btn[class!=".disabled"]').on('click',function(){
		if(!$.checkAuth()) { return false; }
		var $this = $(this);
		var data_str = "";
		var error = false;
		$("input[type=text]", "form[name=signup_form]").each(function(){
			//fc_lamp:报名表必填项2013320
			if($(this).hasClass("required") && $(this).val()==''){
				error = true;
				return false;
			}
			data_str += $(this).attr("name") + "：" + $(this).val() + "^_^";
		});
		
		if(error){
			alert('亲，无法提交，必要信息未填写完整。');
			return false;
		}
		
		
  		$.ajax({
  			url: '/event_auth/signup/'+$this.data('type')+'/'+$this.data('id'),
  			type: 'post',
  			dataType: 'json',
  			data: {data: data_str},
  			success: function(json, textStatus, xhr) {
	          if(json.code > 0 ){
	            $.messager(json.message);
	          } else {
  				$('#modal-event-enter').modal('hide');
				$.messager(json.message,'reload');
			  }
  			}
  		});
		return false;
	});

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
	        var $newElems = $( newElements ).css({ opacity: 0 });
	            $newElems.animate({ opacity: 1 });
	         	$newElems.find('[rel="popover"]').popover();
				$newElems.find('[rel="tooltip"]').tooltip();
		});
	})

    $.getScript('/static/js/timeline.min.js');

})
