$(function(){
	$('.btn.follow[disabled!="disabled"]').live('click',function(){
		var $this = $(this);
		if( !$.is_signin()){
			return false;
		}else{
			$this.button('loading');
			$.ajax({
				type: "POST",
				url: "/do_follow/",
				data: {"bf":$this.attr('data-id'),"do":$this.attr('data-action')},
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
				data: {"bf":$this.attr('data-id'),"do":$this.attr('data-action')},
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

	$("a.item-qrcode[rel=popover]")
		.popover({
			'html':true
		})
		.click(function(e) {
			e.preventDefault()
		})
	$(".popover .close").live('click',function(){
		$("a[rel='popover']").popover('hide')
	})


})