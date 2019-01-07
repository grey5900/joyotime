$(function () {
	$(".show-avatar").hide();

	$.getScript('/static/js/jquery.imgareaselect.min.js');
	$.getScript('/static/js/account.min.js');

	$.getScript('/static/js/jquery.ajaxfileupload.min.js',function(){
		if ($.browser.mozilla) {
            $("#avatarCover").on('click',function(e){
                if($(e.target).is('label')){
                    $("#avatar").trigger('click');
                }
            });
        }

	    $('#avatarCover').delegate('#fileupload-exists','click',function(){
	        var cover = $("#avatarCover");
	        cover.empty();
	        var upFile = $("<input type='file' class='imgbtn' name='Filedata' id='avatar'/>");
	        $(upFile).appendTo(cover);
	        $(".show-avatar").hide();
	    });

	    $('#avatarCover').delegate('#avatar','change', function(){
	        var $this = $(this);
	        $('#avatarCover')
		    .ajaxStart(function(){
		        $(this).addClass("loading");
		    })
		    .ajaxComplete(function(){
		        $(this).removeClass("loading");
		    });
	        if($this.val() != ''){
	            var cover =  $('#avatarCover');
	            $.ajaxFileUpload({
	                url:'/upload/avatar',
	                secureuri: false,
	                fileElementId:'avatar',
	                data:{"uploadFile":$this.attr("name"),"uid":online_id},
	                dataType:"json",
	                success:function(data, status){
	                    var data = eval("("+data+")");
	                    var img =data.msg;
		                if(data.code == 1){
							cover.html("");
							cover.append('<div id="preview_big"></div>');	
							var a_del = $("<a href='javascript:void(0)' class='close' id='fileupload-exists'>&times;</a>");
		                	$(".show-avatar").show();
							var preview_big = $('#preview_big');
								preview_big.append(a_del);
								preview_big.append('<img id="photo" style="width: 360px; height: 360px;"/>');
								$('#preview').append('<img id="thumb" style="width: 160px; height: 160px;"/>');
								$("#photo").attr({src:img});
								$("#thumb").attr({src:img});
								$("#image_src").val(img);
								$('#photo').imgAreaSelect({ 
									aspectRatio: '1:1', 
									handles: true,
									fadeSpeed: 200,
									resizeable:false,			
									onSelectChange: preview
								});
		                }else{
		                	$.messager(data.msg);
		                }
	                }
	            });
	            $this.val('');
	        }
	    })
	});

	$("#upload_thumb").on("submit" , function(){
		var $submit = $('#save_avatar');
		$submit.button('loading');
		$.ajax({
			type: "POST",
			url: $("#upload_thumb").attr("action"),
			data: $("#upload_thumb").serialize(),
			dataType: "json",
			success: function(json){
				if( json.code == -1){
					$.messager(json.msg);
					$submit.button('reset');
				}else {
					$.getScript("/sso");
					$.messager(json.msg,'reload',function(){
						$submit.button('reset');
					});
					$("#image_src").val('');
				}
			}
		});
		return false;
	});
})

function preview(img, selection) {
    var cur_w = $("#photo").width;
    var cur_h = $("#photo").height;
    var scaleX = 160 / selection.width;
    var scaleY = 160 / selection.height;
    $('#preview img').css({
        width : Math.round(scaleX * 360) + 'px',
        height : Math.round(scaleY * 360) + 'px',
        marginLeft : '-' + Math.round(scaleX * selection.x1)+ 'px',
        marginTop : '-' + Math.round(scaleY * selection.y1)+ 'px'
    });
    $('#x1').val(selection.x1);  
    $('#y1').val(selection.y1);  
    $('#x2').val(selection.x2);  
    $('#y2').val(selection.y2);  
    $('#w').val(selection.width);  
    $('#h').val(selection.height); 
}