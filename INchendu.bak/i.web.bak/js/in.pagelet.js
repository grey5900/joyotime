!function ($) {

  $(function(){
    
    var $nav = $('#mini-header')
      , navTop = $('#mini-header').length && $('#mini-header').offset().top
      , isFixed = 0;

    $(window).on('scroll', function(){
      var i, scrollTop = $(window).scrollTop();
      if (scrollTop >= navTop && !isFixed) {
        isFixed = 1;
        $nav.addClass('fixed');
      } else if (scrollTop <= navTop && isFixed) {
        isFixed = 0;
        $nav.removeClass('fixed');
      }
    });
    
    $(".badge_list a").on('click', function(){
        link = $(this);
        
        $("#console_container").find('> .active').removeClass('active');

        link.parent("li").addClass("active");
        $('.badge_detail > .photo img').attr({
            src : link.attr('data-photo')
        });
        $('.badge_detail > h3').text(link.attr('data-title'));
        $('.badge_detail > .intro').text(link.attr('data-intro'));
        $('.badge_detail > .time').text(link.attr('data-time'));
    });

    
    //收藏
    $('.btn.favorite[disabled!="disabled"]').on("click", function() {
        var $this = $(this);
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
	        $.ajax({
	            type:"POST",
	            url:"/favorite/",
	            data:{"id":$this.attr("date-id"),"type":$this.attr("date-target")},
	            async:false,
	            cache:false,
	            dataType:"json",
	            success:function(data){
	                if(data.code != 1){
                        $("#messager").messager({message:data.msg});
	                }else{
	                    $this.text("已收藏").removeClass("favorite btn-primary").addClass("favorited disabled").off();
	                }
	            }
	        });
		}
        return false;
    });
	$('.btn.follow[disabled!="disabled"]').live('click',function(){
		var $this = $(this);
		if( !$.is_signin()){
			return false;
		}else{
			$this.button('loading');
			$.ajax({
				type: "POST",
				url: "/do_follow/",
				data: "bf="+$this.attr('data-id')+"&do="+$this.attr('data-action'),
				dataType: "json",
				success: function(data){
					$this.button('reset');
					$("#messager").messager({message:data.msg});
					if(data.code == 1){
						$this.text("取消关注").attr('data-action','uf').removeClass("btn-primary follow").addClass("followed");
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
				data: "bf="+$this.attr('data-id')+"&do="+$this.attr('data-action'),
				dataType: "json",
				success: function(data){
					$this.button('reset');
					$("#messager").messager({message:data.msg});
					if(data.code == 1){
						$this.text("加关注").attr('data-action','f').removeClass("followed").addClass("btn-primary follow");
					}
					return false;
				}
			});
		}
	});
	$('.setmnemonic').on('click',function(){
		var $this = $(this),
			$form = $('#user-setmnemonic-form'),
			$input = $('#user_mnemonic');
		$form.data('id', $this.data("id"));
		$input.val($this.data('mnemonic'));
		$form.modal('show');
	});
	$('#user-setmnemonic-form').on('submit',function(){
		var $this = $(this),
			$targetArr = $("#u_"+$this.data('id')).find('.username');
		$.ajax({
			url: $this.attr('action')+"/"+$this.data("id")+"/",
			type: 'post',
			dataType: 'json',
			data: $this.serialize(),
			success: function(data, textStatus, xhr) {
				if(data.code = 1){
					if ( $('#user_mnemonic').val() ){
						$targetArr.each(function(){
							$(this).find('a').text($('#user_mnemonic').val());
						});
					} else {
						window.location.reload();
					}
					$('#user_mnemonic').val('');
					$this.modal('hide');
				} else {
					$('#messager').messager({'msg':data.msg});
				}
				return false;
			}
		});
		return false;
	});
  })
}(window.jQuery)