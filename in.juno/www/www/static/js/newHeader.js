
function signIn(){
	if($('.modal-loading').length){
		$('#modal').modal();
		$.ajax({
			url:'/web/index',
			dataType:'html',
			success:function(r){
				$('#modal').html(r);
			}
		})
	}
	else $('#modal').modal();
	
};
function checkUser(){
	if((typeof online_id === 'undefined') || online_id === null || online_id === 0 || online_id === ''){
		$('.header .sign_in').hide();
	}
	else{
		$('.header .sign_in').show().siblings().hide();
		$('a[data-rel="popover"]').popover();
		
	}
};
$(function(){
	$('.header .reg a').on('click',function(e){
		e.preventDefault();
		signIn();
	});
	checkUser();
	$('.site-nav').find('li.back').remove();
	//yy
	$('#tip-post2').live('submit',function(){
		var $this = $(this);
		var cover = $('#cover2');
		var content = $('#yText').val();
		if($this.data('submiting') === true){
			$.messager("请不要重复提交表单");
			return false;
		}
		if(content.length >= 10 && content.length <=500){
			$this.data('submiting',true);
			$.ajax({
				url:$(this).attr('action'),
				type:'post',
				data:$(this).serialize(),
				dataType:'json',
				success:function(json){
					
					if(json.code == 0){
						$.messager(json.msg);
						$('#preview').empty().hide();
						cover.empty();
						$('<input type="file" id="upload" name="Filedata">').appendTo(cover);
						$this.data('submiting',false);
						$('#yText').val('');
					}
					else{
						$.messager(json.msg);
						$this.data('submiting',false);
					}
				},
				error:function(){
					$.messager("通信失败，请检查网络！");
					$this.data('submiting',false);
				}
			});
		}
		else{
			$.messager("内容不能少于10字符大于500字符！");
		}
		return false;
	});
	
	//上传图片
	$('.upload #upload').live('change',function(){
		var $this = $(this);
		var cover = $('#cover2');
		var preview = $('#preview');
		$.ajaxFileUpload({
			url:'/upload/upload_image',
			data:{uploadFile:$this.attr('name')},
			fileElementId:'upload',
			dataType:'json',
			type:'post',
			success:function(json){
				var json = new Function("return "+json)();
				if(json.code != 1){
					$.messager(json.msg);
				}
				else{
					cover.html("");
					$('<img />').attr('src',json.msg).appendTo(preview);
					preview.show();
					$('<input type="hidden" id="photo" name="photo" >').val(json.msg).appendTo(cover);
					
				}
			}
		})
	})
	//图片预览关闭
	$('.header').on('click','#preview',function(){
		var $this = $(this);
		var cover = $('#cover2');
		$this.empty();
		$this.hide();
		cover.empty();
		$('<input type="file" id="upload" name="Filedata">').appendTo(cover);
	});
})
