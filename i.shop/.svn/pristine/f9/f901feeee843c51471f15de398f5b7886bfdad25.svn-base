!function($) {
  $.selectTime = function(){
    var startDate = new Date;
    var endDate = new Date;
    $('#beginDate').datepicker().on('changeDate', function(ev){
      if (ev.date.valueOf() > endDate.valueOf()){
        $('#alert').show().find('strong').text('开始日期不能大于结束日期');
      } else {
        $('#alert').hide();
        startDate = new Date(ev.date);
      }
    });
    $('#endDate').datepicker().on('changeDate', function(ev){
      if (ev.date.valueOf() < startDate.valueOf()){
        $('#alert').show().find('strong').text('结束日期不能小于开始日期');
      } else {
        $('#alert').hide();
        endDate = new Date(ev.date);
      }
    });
  }
  $.uploadify = function(){
      $("#uploadify").uploadify({
        'uploader': 'uploadify.swf',
        'script': 'UploadHandler.ashx',
       // 'cancelImg': 'js/uploadify-v2.1.0/cancel.png',
        'folder': 'UploadFile',
        'queueID': 'fileQueue',
        'auto': false,
        'multi': true
      });
      /*
       *火狐上传
       */
    if ($.browser.mozilla) {
      $(".upload-img label").on('click',function(){
          $("#upload").click();
      });
    }
  }

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
            change_name: false
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
        
        // 用于存放提交返回的图片文件名
        var hidden = $("<input type=\"hidden\"" + (options.required?"class=\"required\"":"") + " name=\"" + options.name + "\" id=\"" + options.id + "\" />");
        element.parent().append(hidden);
        
        var iframeDiv = $("<div></div>");
        // 初始化iframe上传组件
        var iframe = $("<iframe style=\"border:0px;height:30px;width:200px;\" frameborder=\"0\" border=\"0\" cellspacing=\"0\" id=\"iframe_" + options.id + "\" name=\"iframe_" + options.id + "\" allowtransparency=\"true\" scrolling=\"no\" resizable=\"no\"></iframe>");
        iframeDiv.append(iframe);
        
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
            span = $("<span></span>").css({"font-size":"12px", overflow:"hidden", position:"absolute"});
            file = $("<input type=\"file\" name=\"file\" size=\"1\">").css({position:"absolute","z-index":"100", "margin-left":"-180px","font-size":"60px",opacity:"0",filter:"alpha(opacity=0)","margin-top":"-5px"});
            button = $("<button type=\"button\">" + element.text() + "</button>");
            
            var p = eval("(" + params + ")");
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
                $.get('/main/rsa_sign', function(data){
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
                        image = $("<img height=\"" + options.thumb_height + "\" border=\"0\" />");
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
                    hidden.val(json.image_name);
                    image_span.show();
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
                    alert(json.result_msg);
                }
            }
        });
        // 将当前元素替换成iframe上传组件内容
        element.replaceWith(iframeDiv);
    }

  });
  function preview(img, selection) {
    if(!selection.width || !selection.height)
        return;
    var cur_w = $("#photo").width;
    var cur_h = $("#photo").height;
    var scaleX = 160  / selection.width;
    var scaleY = 160 / selection.height;
//  var size = Math.round((cur_w < cur_h) ? scaleX * cur_w : scaleX * cur_h);
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
} (window.jQuery);
$(function(){
  /*
   *头部定位
   */
  var $nav = $('#fixed-header')
    , navTop = $('#fixed-header').length && $('#fixed-header').offset().top
    , isFixed = 0;
  $(window).on('scroll', function(){
    var i, scrollTop = $(window).scrollTop();
    if (scrollTop >= navTop && !isFixed) {
      isFixed = 1;
      $nav.addClass('navbar-fixed-top');
    } else if (scrollTop <= navTop && isFixed) {
      isFixed = 0;
      $nav.removeClass('navbar-fixed-top');
    }
  });
});