﻿$(function(){
    $("#star-ranker .ranker").raty({
        hints : ['很差', '差', '一般', '好', '很好'],
        half : false,
        target : "#star-ranker .hint",
        targetKeep : true
    });
    
    $(".review-bd").on('focusin mousedown',function(){
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{
			return true;
		}
    });
    $('form#tip-post').on('submit',function(){
    	var $this = $(this),
    		$input = $this.find('.reply-write').first();
    	if($input.is_empty()){
    		return false;
    	} else if($input.val().length < 10) {
    		$('#messager').messager({message:'请输入10个字以上的点评'})
    		return false;
    	} else if($input.val().length > 500) {
    		$('#messager').messager({message:'点评字数限制在500个字以内'})
    		return false;
    	}
    })
    $('form#photo-post').on('submit',function(){
    	var $this = $(this),
    		$input = $this.find('.reply-write').first(),
    		$upload = $this.find('#upload').first();
    	if($upload.is_empty()){
    		$('#messager').messager({message:'请选择要上传的照片'})
    		return false;
    	}
    	if($input.val().length > 500) {
    		$('#messager').messager({message:'点评字数限制在500个字以内'})
    		return false;
    	}
    })


	$('.btn.favorite[disabled!="disabled"]').on('click', function(){
		var $this = $(this);
		//检查登录状态
		if( !$.is_signin()){
			return false;
		}else{//已登录
			var $id = $this.attr("date-id");
			var $type = $this.attr("date-type");
			$.ajax({
				type:"POST",
				url:"/favorite/",
				data:{"id":$id,"type":$type},
				dataType:"json",
				success:function(data){
					if(data.code != 1){
						$("#messager").messager({message:data.msg});
					}else{
						$this.text("已收藏").removeClass("btn-primary favorite").addClass("favorited disabled").off();
					}
				}
			});
		}
	});


	/* 新的报错 */
	$('.btn.place-report').on('click',function(){
		if( !$.is_signin()){
			return false;
		}
	})
	$('#mapCon').on('click',function(){
			$('#place-map').modal('show');
	})
	$('#place-map').on('shown', function(){
		$("#place-map-container").html('');
		var marker = new soso.maps.Marker({
			position : new soso.maps.LatLng($("#place-map-container").attr('data-lat'), $("#place-map-container").attr('data-lng'))
		});
		$("#place-map-container").mapper({
			zoomLevel : 15,
			marker : marker
		});
	});

	$('#place-report-list a').on('click',function(){
			var $this = $(this);
			switch($this.attr('data-type')){
				case 'map':
					$('#place-report-form-map').modal('show');
					break;
				case 'info':
					$('#place-report-form-info').modal('show');
					break;
				case 'other':
					$('#place-report-form-other').modal('show');
					break;
				default:
					$.ajax({
						type:"POST",
						url:"/place_report/"+$this.attr("date-id"),
						data:{type:$this.attr("data-err")},
						dataType:"json",
						success:function(json){
							$("#messager").messager({message:json.msg});
						}
					});
					break;
			}
	})
	$('#place-report-form-map').on('shown', function(){
		var $submit = $(this).find('input[type=submit]'),
			$latlng = $("#coordinate");
		$submit.prop({disabled: true});
		$("#map").html('');
		var marker = new soso.maps.Marker({
			position : new soso.maps.LatLng($("#map").attr('data-lat'), $("#map").attr('data-lng')),
			draggable: true
		});
		$("#map").mapper({
			zoomLevel : 15,
			marker : marker,
	        after : function(){
				soso.maps.Event.addListener(marker, 'dragend', function() {
					var position = marker.getPosition();
					$latlng.val(position);
					$submit.prop({disabled: false});
				});
	        }
		});
	});
	$('#place-report-form-map').on('submit', function(){
    	var $this = $(this),
    		$btn = $this.find('input[type=submit]');
		$btn.button('loading');
		$.ajax({
			type: "POST",
			url: $this.attr('action'),
			data: $this.serialize(),
			dataType: "json",
			success: function(json){
				$("#messager").messager({message:json.msg});
				$btn.button('reset');
				if(json.code == 1){
					$this.modal("hide");
				}
			}
		});
		return false;
	});
	$('#place-report-form-info').on('submit', function(){
    	var $this = $(this),
    		$btn = $this.find('input[type=submit]'),
    		$place_name = $this.find('input[name=place_name]'),
    		$place_address = $this.find('input[name=place_address]');
    	if( $place_name.is_empty() || $place_address.is_empty() ){
			return false;
		}else if( $place_name.val() == $place_name.attr('data-source') && $place_address.val() == $place_address.attr('data-source') ){
			$("#messager").messager({message:'请输入新的地点信息'});
			return false;
		}else{
			$btn.button('loading');
			$.ajax({
				type: "POST",
				url: $this.attr('action'),
				data: $this.serialize(),
				dataType: "json",
				success: function(json){
					$("#messager").messager({message:json.msg});
					$btn.button('reset');
					if(json.code == 1){
						$this.modal("hide");
					}
				}
			});
			return false;
		}
	});
	$('#place-report-form-other').on('submit', function(){
    	var $this  = $(this),
    		$btn   = $this.find('input[type=submit]'),
    		$input = $this.find('textarea[name=content]');
    	if($input.is_empty()){
			return false;
		}else{
			$btn.button('loading');
			$.ajax({
				type: "POST",
				url: $this.attr('action'),
				data: $this.serialize(),
				dataType: "json",
				success: function(json){
					$("#messager").messager({message:json.msg});
					$btn.button('reset');
					if(json.code == 1){
						$this.modal("hide");
					}
				}
			});
			return false;
		}
	});

	//限制数字
	$("#pcc")
		.on("keyup",function(){
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		})
		.on("paste",function(){
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		})
		.css("ime-mode", "disabled");



	//FF 选图片
    if ($.browser.mozilla && online_id) {
        $(".photo-selector label").on('click',function(){
            $("#upload").click();
        });
    }

    $("#upload").on("change", function() {
        var $preview = $(".photo-preview"),
            type  = ["gif", "jpeg", "jpg", "bmp", "png"],
            fileObj  = this,
            file;
        if (fileObj.value) {
            if (!RegExp("\.(" + type.join("|") + ")$", "i").test(this.value.toLowerCase())) {
                alert("照片必须为" + type.join("，") + "中的一种，请重选");
                fileObj.value = "";
                $preview.fadeOut();
                return false;
            }
            if (fileObj.files) {
                file = fileObj.files[0];
                var fr = new FileReader;
                fr.onloadend = function(str){
                    if(typeof str === "object") {
                        str = str.target.result; // file reader
                    }
                    $preview.css({
                        "background-size":  "cover",
                        "background-image": "url(" + str + ")"
                    }).fadeIn();
                };
                fr.readAsDataURL(file);
            } else {
                fileObj.select();
                file = document.selection.createRange().text;
                $preview.css({
                    filter : "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + file + "',sizingMethod='scale')"
                }).fadeIn();
            }
        }
    });

	//同步
	var $sync_sina = $('#sync-settings span.sync-sina'),
		$sync_tencent = $('#sync-settings span.sync-tencent');
	if($.cookie('is_sync_sina') == false) {
		$sync_sina.removeClass('on');
		$sync_sina.find('input[type=checkbox]').prop('checked',false);
	}
	if($.cookie('is_sync_tencent') == false) {
		$sync_tencent.removeClass('on');
		$sync_tencent.find('input[type=checkbox]').prop('checked',false);
	}
	$('.sync-settings span.sync-sina, .sync-settings span.sync-tencent').on('click',function(){
			var $this = $(this),
				$input = $this.find('input[type=checkbox]');
			$this.toggleClass('on');
			$input.prop("checked", !$input.prop("checked"));
		}
	)

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

});
