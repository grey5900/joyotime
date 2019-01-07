$(function(){
  /*
   *宝宝时钟倒计时
   */
    function getNext(hours,minutes) {
      $.ajax({
        url: '/babyclock/get_info/' + hours + '/' + minutes,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(json, textStatus, xhr) {
            if (json.id) {
                window.location.href = '/babyclock/' + json.id;
            }
        }
      });
    }
    
    var $detailbabyclock = $('#detail-countdown');
    if ($detailbabyclock[0]) {
      detailcountdown = setInterval(function(){
            var now = new Date();
            $('#detail-countdown').html($.padNum(now.getHours(),2) + ':' + $.padNum(now.getMinutes(),2) + ':' + $.padNum(now.getSeconds(),2));
            if (now.getSeconds() == 0) {
                getNext(now.getHours(),now.getMinutes());
            }
      }, 1000);
    }
    $('.imgartwork')
      .on('mouseover',function(){
        $(this).find('.action-support').fadeIn();
      })
      .on('mouseleave',function(){
        $(this).find('.action-support').fadeOut();
      });

    //支持按钮
    $('.action-support').on('click', function(){
        var $this = $(this);
        $.ajax({
          url: '/babyclock/support/' + $this.data('id'),
          type: 'GET',
          dataType: 'json',
          cache: false,
          success: function(json, textStatus, xhr) {
            if (json.code == 1) {
              $.messager('谢谢你的支持');
            } else {
              $.messager(json.msg);
            }
          }
        });
    });
    $('#babyclock-upload-btn').on('click', function(){
        if(!$.checkAuth()) { return false; }
    })
});

/**
 * 获得图片地址
 * options
 * @param file_name 图片文件名
 * @param file_type 图片类型
 * @param resolution 返回文件格式hdp mdp odp
 * @param id 传入ID
 */
function get_image_url(options, callback) {
    $.getJSON("/active_source/get_image_url", {file_name:options.file_name, file_type:options.file_type, resolution:options.resolution}, function(json){
        callback && callback(json);
    });
}

(function($){
	$.fn.extend({   
        /**
         * @param name 存放上传图片名称的input的name
         * @param id 存放上传图片名称的input的id
         * @param file_name 传给接口的文件名 提交给接口的名称，有名称，那么根据名称生成文件
         * @param file_type 上传文件的类型 如：user,head,common 
         * 用户发的图片放到user下 header放用户头像 common放所有其他的
         * @param resolution 分辨率 如：hdp mdp udp 等
         * @param image_name 图片名称 主要用于编辑或预览
         * @param required 是否必须 默认为必须 true   true/false
         * @param thumb_height 默认显示图片高度
         * @param change_name 是否改变名字 默认为 false true/false
         * Add by Liuw at 2012-12-04
         * @param up_url 处理上传的URL
         * @param width 缩略宽度
         * @param height 缩略高度
         * @param is_rec 请求是否来自推荐碎片
         * @param water 水印名
         * @param location 水印位置 1=左上角；2=顶部正中；3=右上角；4=中部左边；5=正中；6=中部右边；7=左下角；8=底部正中；9=右下角
         * @param input 文件组件的名字
         */
        my_upload: function(options){
            var element = $(this);
            options = $.extend({
                file_type: "",
                name: "upload_image_name",
                id: "upload_image_id",
                file_name: "",
                resolution: "",
                image_name: "",
                required: true,
                thumb_height: 100,
                change_name: false,
                up_url: '',
                width: 0,
                height: 0,
                is_rec: 0,
                water: '',
                location:9,
                input: 'file'
            }, options);

            if(options.file_type == "") {
                alert("请设置上传文件类型");
                return false;
            }

            var action_url, params;
            if(options.resolution == "") {
                // 那么是普通保存接口
                // action_url = "http://joyotime.gicp.net/hl/private_api/image/save_image";
                // action_url = "http://223.4.93.206/image/save_image";
                action_url = conf_save_image_uri;
                // action_url = "http://i.admin/main/test";
                params = "{file_type: options.file_type}";
            } else {
                // 特殊接口需要指定分辨率的，不处理图片接口
                // action_url = "http://joyotime.gicp.net/hl/private_api/image/transfer_image";
                // action_url = "http://223.4.93.206/image/transfer_image";
                action_url = conf_transfer_image_uri;
                // action_url = "http://i.admin/main/test";
                params = "{file_type: options.file_type, file_name: options.file_name , resolution: options.resolution}";
            }
            //Add by Liuw:指定了上传处理路径的到上传地处理
        	if(typeof options.up_url != 'undefined' && options.up_url != ''){
        		action_url = options.up_url;
        	}
            
            // 用于存放提交返回的图片文件名
            var hidden = $("<input type=\"hidden\"" + (options.required?"class=\"required\"":"") + " name=\"" + options.name + "\" id=\"" + options.id + "\" />");
            element.parent().append(hidden);
            
            var iframeDiv = $("<div></div>");
            // 初始化iframe上传组件
            var iframe = $("<iframe style=\"border:0px;height:30px;width:200px;\" frameborder=\"0\" border=\"0\" cellspacing=\"0\" id=\"iframe_" + options.id + "\" name=\"iframe_" + options.id + "\" allowtransparency=\"true\" scrolling=\"no\" resizable=\"no\"></iframe>");
            iframeDiv.append(iframe);
            iframe.domain = '*';
            setTimeout(init_widget, 200);
            
            var form, span, file, button;
            // 初始化，编辑的时候
            // 图片的span的ID号
            var image_span_id = "ooxx_image_span_id_" + options.id;
            // 图片的span、图片链接、图片、删除链接DIV、删除链接
            var image_span, image_a, image, delete_div, delete_a;
            
            function init_widget() {
                myFrame = arguments[0];
                image_name = arguments[1]?arguments[1]:options.image_name;
                form = $("<form action=\"" + action_url + "\" method=\"post\" enctype=\"multipart/form-data\"></form>").css({margin:"0px"});
                
                //Add by Liuw:要求缩略图片的
                if(typeof options.up_url != 'undefined' && options.up_url != ''){
	                if(typeof options.width != 'undefined' && options.width > 0){
	                	$('<input />').attr({type:'hidden', name:'tw'}).val(options.width).appendTo(form);
	                }
	                if(typeof options.height != 'undefined' && options.height > 0){
	                	$('<input />').attr({type:'hidden', name:'th'}).val(options.height).appendTo(form);
	                }
                }
                
                span = $("<span></span>").css({"font-size":"12px", overflow:"hidden", position:"absolute"});
                file = $("<input type=\"file\" name=\"file\" size=\"1\">").css({position:"absolute","z-index":"100", "margin-left":"-180px","font-size":"60px",opacity:"0",filter:"alpha(opacity=0)","margin-top":"-5px"});
                button = $("<button type=\"button\">" + element.text() + "</button>");
                
                var p = eval("(" + params + ")");
            	//Add by Lliuw:增加一些参数 
            	if(typeof options.water != 'undefined' && options.water != ''){
            		p.water = options.water;
            		p.location = options.location;
            	}
            	p.input = options.input;
            	
                file.click(function(){
                    if(options.resolution != "" && options.change_name == false) {
                        // 直接保存某种分辨率。而且不改变名称，如果需要改变名称，那么不带入原来的名称
                        // 处理指定分辨率的接口调用才用这个
                        // 获取名称
                        $("input[name='" + options.name + "']", iframeDiv[0].window).each(function(){
                            if($(this).val() != "") {
                                var file_name = $("input[name=file_name]", form);
                                if(file_name) {
                                    file_name.val($(this).val());
                                } else {
                                    form.append("<input type=\"hidden\" name=\"file_name\" value=\"" + $(this).val() + "\" />");
                                }
                                return;
                            }
                        });
                    }
                });
                
                file.change(function(){
                    var sign_input = $("input[name=sign]", form);
                    if(sign_input.val() == undefined) {
                        sign_input = $("<input type=\"hidden\" name=\"sign\" value=\"\" />");
                        form.append(sign_input);
                    }
                    $.get('/active_source/rsa_sign', function(data){
                        sign_input.val(data);
                        form.submit();
                    });
                });
                
                frame = $.isPlainObject(myFrame)?myFrame:iframe;
                frame.contents().find("body").html("");
                frame.contents().find("body").css({margin:"0px"});
                frame.contents().find("body").append(form);
                
                form.append(span);
                span.append(file);
                span.append(button);
                
                // 添加参数
                $.each(p, function(k, v){
                    form.append("<input type=\"hidden\" name=\"" + k + "\" value=\"" + v + "\" />");
                });
                html = frame.contents().find("body").html();
                
                // 带入了图片名称
                if(image_name) {
                    get_image_url({file_name:image_name, file_type:options.file_type, resolution:options.resolution}, function(json){
                        // 每次重新获取
                        image_span = $("#" + image_span_id);
                        if(image_span.length == 0) {
                            image_span = $("<div id=\"" + image_span_id + "\"></div>").css({
                                position: "relative",
                                width: "200px",
                                "text-align": "center", 
                                border: "1px solid #9cc67e",
                                height: options.thumb_height + "px",
                                overflow: "hidden"
                            });
                            image_a = $("<a rel=\"" + options.id + "\"></a>");
                            image = $("<img height=\"" + options.thumb_height + "\" border=\"0\"  />");
                            delete_div = $("<div></div>").css({
                                position: "absolute",
                                left: "0px",
                                right: "0px",
                                bottom: "0px",
                                height: "20px",
                                background: "#9cc67e",
                                opacity: "0.8",
                                filter: "alpha(opacity=80)",
                                display: "none"
                            });
                            delete_a = $("<a href=\"javascript:;\">删除</a>").css({
                                "line-height": "20px"
                            });
                            
                            image_a.append(image);
                            delete_div.append(delete_a);
                            image_span.append(image_a);
                            image_span.append(delete_div);
                            iframeDiv.append(image_span);
                        } else {
                            image_a = $("a[rel=" + options.id + "]", image_span);
                            image = $("img", image_span);
                            delete_a = $("a[href='javascript:;']", image_span);
                        }
                        image_a.fancybox();
                        delete_a.click(function(){
                            $($(this).parent().parent()).hide();
                            hidden.val("");
                        });
                        image_span.hover(function(){
                            delete_div.show();
                        }, function(){
                            delete_div.hide();
                        });
                        image_a.attr("href", json.source_image + "?" + (new Date().getTime()));
                        image.attr("src", json.image + "?" + (new Date().getTime()));
                        image_span.show();
                        if(options.is_rec == 0){
                            hidden.val(json.image_name);
                            hidden.trigger('change');
                        }else{
                            $('#'+options.name).val(json.image+'?'+(new Date().getTime()));
                        }
                    });
                }
            }
                        
            iframe.unbind().load(function(){ 	
                var myFrame = document.getElementById(iframe.attr('id'));
                var response = $(myFrame.contentWindow.document.body).text();
                
                if(response) {
                    var json = eval("(" + response + ")");
                    if(json.result_code == 0) {
                        init_widget($(myFrame), json.file_name);
                    } else {
                        alertMsg.error(json.result_msg);
                    }
                }
            });
            // 将当前元素替换成iframe上传组件内容
            element.replaceWith(iframeDiv);
        }
	});
})(jQuery);
