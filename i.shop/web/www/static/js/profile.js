//删除封面图片
function delCover(id){
	var li = $("#cover_"+id);
	var style = $("#style").val();
	li.children().remove();
	var upFile = $("<input type='file' class='imgPrew' name='Filedata' id='cover' size='8' onchange='uploadCover($(this),\"cover\",1);'/>");
	$("<span>添加图片</span>").appendTo(li);
	$(upFile).appendTo(li);
	if(style == 3){
		$("#pre-data-cover-"+id).remove();
	}else{
		$("#pre-cover-"+style).find('img').remove();
	}
	return false;
}
//初始化封面图片的上传控件
function initCover(size){
	$("#covers").html("");
	for(var i=0;i<size;i++){
		var upFile = $("<input type='file' class='imgPrew' name='Filedata"+(i+1)+"' id='cover"+(i+1)+"' size='8' onchange='uploadCover($(this),\"cover"+(i+1)+"\", 1);'/>");
		var li = $("<li></li>");
		var li_s = $("<div class='fileupload fileupload-new'></div>").appendTo(li);
		var li_ss =$("<a class='slect-img'></a>").appendTo(li_s);
		$("<span>添加图片</span>").appendTo(li_ss);
		$(upFile).appendTo(li_ss);
	//	$(upBtn).appendTo(li);
		$("#covers").append(li);
	}
}
//增加上传图片的控件
function addCover(maxSize, validate){
	var nowSize = $("."+validate).length;
	if(maxSize > 0 && nowSize < maxSize){
		var upFile = $("<input />").attr({type:"file",name:"Filedata",id:"cover",size:"8"}).bind('change', {jBtn:$(this),fileElement:'cover',resize:'1'}, function(e){uploadCover(e.data.jBtn,e.data.fileElementId,e.data.resize);});
	//	var upBtn = $("<button type=\"button\" onclick=\"uploadCover($(this), 'cover', 1);\">上传</button>");
		var upDel = $("<button type=\"button\" onclick=\"$(this).parent().remove();return false;\">取消</button>");
		var li = $("<li></li>");
		var li_s = $("<div class='fileupload fileupload-new'></div>").appendTo(li);
		var li_ss =$("<a class='btn btn-primary btn-file'></a>").appendTo(li_s);
		$("<span>图片上传</span>").appendTo(li_ss);
		$(upFile).appendTo(li_ss);
	//	$(upBtn).appendTo(li);
		$(upDel).appendTo(li_ss);
		$("#covers").append(li);
	}else{
		alert('已达到最大上传数量了，不能再上传了');
	}
	return false;
}
//删除单图
function delImage(fieldId){
	$("#pre-data-image-"+fieldId).remove();
	var p = $("#profile-data-field-"+fieldId);
	p.html("");
	var f = $("<input/>");
	var ff = $("<a class='btn btn-primary btn-file'></a>");
	$("<span>图片上传</span>").appendTo(ff);
	f.attr({type:"file",name:"uploader",id:"uploader",size:"8"});
	ff.bind('change',{fileElementId:'uploader',jBtn:$(this),fieldId:fieldId},function(e){uploadImage(e.data.fileElementId,e.data.jBtn,e.data.fieldId);});
	ff.append(f);
	p.append(ff);
}
//上传图片
function uploadImage(fileElementId, jBtn, fieldId){
	$.ajaxFileUpload({
		url:"/upload_profile/0",
		secureuri:false,
		fileElementId:fileElementId,
		data:{"uploadFile":fileElementId},
		dataType:"json",
		success:function(data, status){
			if(data.code != 0)
				alert(data.msg);
			else{
				var img = data.src;
				$.ajax({
					type:"POST",
					url:"/upload_to_pic",
					data:{"name":img},
					dataType:"json",
					success:function(json){
						if(json.code == 0){
							var parent = $('#'+fileElementId).parent();
							var img = $("<img width='100px' height='100px' />").attr({alt:fieldId,src:json.view});
							var h = $("<input />").attr({type:"hidden",name:fieldId,id:fieldId}).val(json.file_name);
							var a = $("<a href=\"javascript:;\" class=\"filupload-move\" onclick=\"delImage('"+fieldId+"');\">删除</a>");
							var p = $("#profile-data-field-"+fieldId);
							p.html("");
							p.append(a).append(img).append(h);
							//预览
							var view_img = $(img).clone().attr({"width": 200}).removeAttr("height");
							view_img.attr("id","pre-data-image-"+fieldId);
							$("#pre-data-dd-"+fieldId).append(view_img);
						}else{
							alert(json.msg);
						}
					}
				});
			}
		}
	});
}

//上传封面图
function uploadCover(jBtn, fileElementId, resize){
	if(jBtn.val() != ''){
		var style = $("#style").val();
		$.ajaxFileUpload({
			url:"/upload_profile/"+resize+"/"+style,
			secureuri:false,
			fileElementId:fileElementId,
			data:{"uploadFile":$(jBtn).attr("name")},
			dataType:"json",
			beforeSend:function(){
				$("#loading").show();
			},
			complete:function(){
				$("#loading").hide();
			},
			success:function(data, status){
				if(data.code==0){
					var img = data.src;
					$.ajax({
						type:"POST",
						url:"/upload_to_pic",
						data:{"name":img},
						dataType:"json",
						success:function(json){
							if(json.code != 0)
								alert(json.msg);
							else{
								var li = $('#'+fileElementId).parent();
								li.html("");
								var arr = json.file_name.split(".");
								var id_name = arr[0];
								var li_id = "cover_"+id_name;
								li.attr("id", li_id);
								var img = $("<img />").attr({alt:json.file_name,src:json.view});
								if(style != 2){
									img.attr({"width":"60px","height":"60"});
								}
								var form_data = $("<input type='hidden' name='covers[]' class='img-covers'/>").attr("value",json.file_name);
								var a_del = $("<a href='javascript:void(0)' class='fileupload-exists' onclick='delCover(\""+id_name+"\")'> 删除 </a>");
								li.append(img).append(form_data);
								li.append(a_del);
								//预览
								var show = $(img).clone();
								var show_li = $("<li></li>").attr("id","pre-data-cover-"+id_name);
								$(show).attr("id","pre-data-cover-img-"+id_name).appendTo(show_li);
								if(style == 3){
									$("#pre-cover-list").find("li").each(function(){
										if($(this).attr("id") == null || $(this).attr("id") == "")
											$(this).remove();
									});
									$(show_li).appendTo($("#pre-cover-list"));
								}else if(style == 1){
									$("#pre-cover-1").html('');
									$(show).appendTo($("#pre-cover-1"));
								}else{
									$("#pre-cover-2").html('');
									$(show).appendTo($("#pre-cover-2"));
								}
							}
						}
					});
				}else{
					var msg = "图片上传失败了!";
					if(data.msg != "") msg += data.msg;
					alert(msg);
				}
			}
		});
		$(jBtn).val('');
	}
}
//上传多图
function uploadRich(jBtn, fileId, fieldId){
	$.ajaxFileUpload({
		url:"/upload_profile/0",
		secureuri:false,
		fileElementId:fileId,
		data:{"uploadFile":fileId},
		dataType:"json",
		success:function(data, status){
			if(data.code != 0)
				alert(data.msg);
			else{
				var img = data.src;
				$.ajax({
					type:"POST",
					url:"/upload_to_pic",
					data:{"name":img},
					dataType:"json",
					success:function(json){
						if(json.code == 0){
							var arr = json.file_name.split(".");
							var li = $('#'+fileId).parent();
							li.html("");
							var id_name = arr[0];
							var li_id = "pre-data-riches-"+arr[0];
							li.attr({id:li_id,'class':''});
							
							//图像
							// var d_i = $("<div></div>").css({"width":"60px","display":"block","overflow":"none;","float":"left"});
							var d_i = $("<div class='fileupload-preview'></div>");
							var i = $("<img width='80px' height='80px' />").attr({alt:json.file_name,src:json.view});
							$("<div class='thumbnail'></div>").append(i)
							var h = $("<input />").attr({type:"hidden",name:fieldId+"_imgs[]"}).val(json.file_name);
							var a_a = $("<a href=\"javascript:void(0)\" class=\"filupload-add btn\" onclick=\"addRich('"+fieldId+"');\"> 增加 </a>");
							var a_d = $("<a href=\"javascript:void(0)\" class=\"filupload-del\" onclick=\"delRich('"+fieldId+"','"+arr[0]+"');\"> 删除 </a>");
							d_i.append(i).append(h).append($("<p></p>").append(a_d).append(a_a));
							//标题&介绍
							var d_t = $("<div class='fileupload-info'></div>");
							var t = $("<input/>").attr({type:"text","class":"text-input",name:fieldId+"_titles[]",size:20});
							var d = $("<textarea></textarea>").attr({name:fieldId+"_ds[]",cols:"16",rows:"4"});
							d_t.append(t).append(d);
							li.append(d_i).append(d_t);
							//预览
							var v_i = $(i).clone().attr({"width": 275}).removeAttr("height");
							var v_li = $("<li class='fileupload'></li>").attr("id","pre-data-rich-"+arr[0]).append($(v_i));
							$("#pre-data-imgs-"+fieldId).append($(v_li));
						}else{
							alert(json.msg);
						}
					}
				});
			}
		}
	});
}
//删除多图
function delRich(fieldId, imgName){
	//如果删除了所有图片，则重新添加上传控件
	if($("#rimg-list-" + fieldId).find("li").length <= 1){ 
		$("#pre-data-riches-"+imgName).parent().remove();
		$("#pre-data-rich-"+imgName).remove();
		addRich(fieldId);
	}else{
		//删除图片
		$("#pre-data-riches-"+imgName).parent().remove();
		//删除预览
		$("#pre-data-rich-"+imgName).remove();

	}
}
//添加多图上传控件
function addRich(fieldId){
	var li_add = $("<li class='fileupload'></li>");
	var up_f = $("<input />").attr({type:"file",name:"richUploader",id:"richUploader",size:"8"});
	var up_btn = $("<a class='btn btn-small btn-primary btn-file'></a>").append($("<span></span>").text("图片上传"));
	var up_bf = up_btn.append(up_f);
	up_f.bind('change',{jBtn:$(this),fileId:'richUploader',fieldId:fieldId},function(e){uploadRich(e.data.jBtn,e.data.fileId,e.data.fieldId)});
	//var up_b = $("<button type=\"button\" onclick=\"uploadRich($(this),'richUploader','"+fieldId+"');\">上传</button>");
	li_add.append(up_bf);
	$("#rimg-list-"+fieldId).append(li_add);
}
//预览文本、日期、时间和单选框
function preview_cur(jObj, value){
	$(jObj).html('');
	if(value != ''){
		$(jObj).append(value);
	}
}
//预览复选框
function preview_checkbox(jObj, value){
	var old = $(jObj).html();
	if(value != ''){
		if(old == ''){
			$(jObj).html(value);
		}else{
			$(jObj).html(old+','+value);
		}
	}else{
		old = old.replace(value, '');
		if(old.indexOf(',') >= 0){
			old = old.replace(',,',',');
		}
		$(jQbj).html(old);
	}
}
