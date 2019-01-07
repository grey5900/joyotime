$(function(){
    $.ajaxSetup({cache:false});    
});

function checkboxToRadio(id, clazz){
    var dom = $("#"+id);
    var list = $("input."+clazz);
    if(dom.attr("checked")){
        list.each(function(i){
            this.type="radio";
            this.checked=false;
        });
    }else{
        list.each(function(i){
            this.type="checkbox";
        });
    }
}

/**
 * 判断是否在选择列表中
 */
function in_select(value, select_obj) {
    var b = false;
    $("option", select_obj).each(function(){
        if(value == $(this).attr("value")) {
            b = true;
            return false;
        }
    });
    
    return b;
}

/**
 * 处理有剪切图片的时候，关闭dialog的时候回调函数
 * @returns {Boolean}
 */
function imgareaselect_close() {
	$("#imgareaselect_in").html("");
	return true;
}

/**
 * 获得图片地址
 * options
 * @param file_name 图片文件名
 * @param file_type 图片类型
 * @param resolution 返回文件格式hdp mdp odp
 * @param id 传入ID
 */
function get_image_url(options, callback) {
    $.getJSON("/main/get_image_url", {file_name:options.file_name, file_type:options.file_type, resolution:options.resolution}, function(json){
        callback && callback(json);
    });
}

/**
 * 处理dialog提交了数据以后，自定义操作
 */
function dialog_ajax_done(json) {
    DWZ.ajaxDone(json);
    if (json.statusCode == DWZ.statusCode.ok){
        if (json.navTabId){
            navTab.reload(json.forwardUrl, {navTabId: json.navTabId});
        } else if (json.rel) {
            navTabPageBreak({}, json.rel);
        }
        if ("closeCurrent" == json.callbackType) {
            $.pdialog.closeCurrent();
        }
    }
    
    if(json.value.action == 'reload') {
        $("#" + json.value.id).loadUrl(json.value.url);
    }
}

/**
 * ajax返回处理
 */
function nav_tab_ajax_done(json){
    DWZ.ajaxDone(json);
    
    if(json.value.action == 'reload') {
        $("#" + json.value.id).loadUrl(json.value.url);
    }
}

/**
 * 提交表单需要执行
 */
function onsubmit_form(form, message, prev_func, next_func) {
    var form = $(form);
    if (!form.valid()) {
        return false;
    }
    
    if ($.isFunction(prev_func)) {
        if(!prev_func()) {
            return false;
        }
    }
    
    if(message) {
        // 有提示消息
        alertMsg.confirm(message, {
            okCall : function() {
                $.ajax({
                    type: 'POST',
                    url:form.attr("action"),
                    data:form.serializeArray(),
                    dataType:"json",
                    cache: false,
                    success: $.isFunction(next_func)?next_func:navTabAjaxDone,
                    error: DWZ.ajaxError
                });
            }
        });
    } else {
        $.ajax({
            type: 'POST',
            url:form.attr("action"),
            data:form.serializeArray(),
            dataType:"json",
            cache: false,
            success: $.isFunction(next_func)?next_func:navTabAjaxDone,
            error: DWZ.ajaxError
        });
    }
}

/**
 * 修改排序
 * @param obj DOM对象
 * @param table 数据库的表
 * @param id 字段
 * @param field 字段名
 */
function modify_rank(obj, table, id, field) {
    var element = $(obj);
    if(element.attr("edit"))
        return;
    
    field = field?field:'rankOrder';
    var rank = parseInt(element.text());
    
    var rank_input = $("<input type=\"text\" size=\"4\" style=\"width:35px;\" value=\"" + rank + "\" />");
    element.html(rank_input);
    element.attr("edit", "1");
    rank_input.focus();
    
    rank_input.blur(function(){
        var order = parseInt($(this).val());
        if(isNaN(order)) {
            alertMsg.error("请输入正确的数字");
            return;
        }
        
        if(rank == order) {
            // 没有修改，那么直接返回，不要提交
            return;
        }
        
        // 提交数据
        $.getJSON("/block/rank/id/" + id + "/table/" + table + "/field/" + field + "/order/" + order, function(json){
            if(json.status != 0) {
                alertMsg.error("修改失败");
                return;
            }
        });
        
        element.removeAttr("edit");
        element.html(order);
    });
}

/**
 * 修改排序 
 * 为什么是2呢？因为这个方法很2.本来是点击才出来INPUT的，非要改成直接INPUT控件，哎。
 * 所以只能是2
 * @param 事件对象
 * @param table 数据库的表
 * @param id 字段
 * @param field 字段名
 */
function modify_rank2(obj, table, id, field) {
    var old_order = parseInt($(obj).val());
    //刷新缓存
    var cache = 0;
	cache = arguments[4] ? arguments[4] : cache;
	cache = parseInt(cache);
    $(obj).blur(function(){
    	
        var order = parseInt($(this).val());
        if(isNaN(order)) {
            alertMsg.error("请输入正确的数字");
            return;
        }
        
        if (order == old_order) {
            // 没有修改。直接返回
            return;
        }
        
        // 提交数据
        $.getJSON("/block/rank/id/" + id + "/table/" + table + "/field/" + field + "/order/" + order+ "/cache/"+cache, function(json){
            if(json.status != 0) {
                alertMsg.error("修改失败");
                return;
            }
            if(table == "ItemAwardAction" || table == "ItemAwards"){
            	navTab.reload();
            }
        });
    });
}

function modify_by_where(obj, table, where, field) {
    var old_order = parseInt($(obj).val());
    //刷新缓存
    var cache = 0;
	cache = arguments[4] ? arguments[4] : cache;
	cache = parseInt(cache);
    $(obj).blur(function(){
    	
        var order = parseInt($(this).val());
        if(isNaN(order)) {
            alertMsg.error("请输入正确的数字");
            return;
        }
        
        if (order == old_order) {
            // 没有修改。直接返回
            return;
        }
        
        // 提交数据
        /*$.getJSON("/block/rank/id/" + id + "/table/" + table + "/field/" + field + "/order/" + order+ "/cache/"+cache, function(json){
            if(json.status != 0) {
                alertMsg.error("修改失败");
                return;
            }
            if(table == "ItemAwardAction" || table == "ItemAwards"){
            	navTab.reload();
            }
        });*/
        
        $.post("/block/rank_by_where",{where:where,table:table,field:field,order:order,cache:cache},function(json){
        	json = eval('('+json+')');
        	if(json.status != 0) {
                alertMsg.error("修改失败");
                return;
            }
        });
    });
}

function modify_boost(obj, table, postid,catid,channelid, field){
	
	var old_boost = parseInt($(obj).val());
	$(obj).blur(function(){
		var boost = parseInt($(this).val());
		if(isNaN(boost)) {
            alertMsg.error("请输入正确的额外权重");
            return;
        }
		if (boost == old_boost) {
            // 没有修改。直接返回
            return;
        }
		
		
		$.post("/block/editBoost",{'table':table,'postid':postid,'catid':catid,'channelid':channelid,'field':field,'boost':boost},function(json){
			json = eval("("+json+")");
			if(json.status != 0) {
                alertMsg.error("修改失败"+json.status);
                return;
            }
			
		});
	});
}


(function($){
	$.fn.extend({
	    my_info_status: function(callback) {
	       var element = $(this);
	       element.change(function(event){
	           // 之前选中
	           var status = element.attr("status");
	           if(element.val() == status) {
	               // 两次一直
	               alertMsg.error("改变的状态和当前记录一样的");
                   return false;
	           }
	           
                alertMsg.confirm("你确定要改变状态？", {
                    cancelCall:function(){
                        element.val("-1");
                    },
                    okCall:function(){
                        // 提交改变状态
                        ajaxTodo(element.attr("url") + "/" + element.val(), function(json) {
                            if(json.statusCode == DWZ.statusCode.ok) {
                                alertMsg.correct(json.message);
                                callback && callback(json.value);
                            } else {
                                alertMsg.error(json.message);
                                element.val("-1");
                            }
                        });
                    }
                });
	       });
	    },
	    
	    /**
	     * @param callback 回调函数处理结果
	     */
	    my_table_status: function(callback, attrField) {
	        var element = $(this);
	        attrField = attrField || "status";
	        element.change(function(event){
                if(element.val() == -1) return false;
                var url = unescape(element.attr("url")).replaceTmById($(event.target).parents(".unitBox:first"));
                DWZ.debug(url);
                
                
                if(!url.isFinishedTm()) {
                    alertMsg.error(element.attr("warn") || DWZ.msg("alertSelectMsg"));
                    return false;
                }
                var id = url.substr(url.lastIndexOf("/") + 1);
                var status = $("#row_" + id).attr(attrField);
                if(element.val() == status) {
                    alertMsg.error("改变的状态和当前记录一样的");
                    return false;
                }
                var ifcancel = element.find(":selected").attr('cancel');
            	if(ifcancel == $("#row_status_"+id).attr(attrField) && ifcancel!=undefined){
            		alertMsg.error("该记录已经被用户删除，不能操作!");
            		element.val("-1");
            		return false;
            	}
                /* 2013 2 19 如果选项target=dialog 弹出一个对话框 */
                if(element.find(":selected").attr('target') == "dialog"){
                	
                	$.pdialog.open(url + "/status/" + element.val() +"/target/dialog", 'placecollection_change_status_'+status, element.find(":selected").attr('title') , {width: 500, height: 300});
                	return false;
                }
                alertMsg.confirm("你确定要改变状态？", {
                    cancelCall:function(){
                        element.val("-1");
                    },
                    okCall: function(){
                    	
                        // 提交改变状态
                        ajaxTodo(url + "/status/" + element.val(), function(json) {
                            if(json.statusCode == DWZ.statusCode.ok) {
                                alertMsg.correct(json.message);
                                callback && callback(json.value);
                            } else {
                                alertMsg.error(json.message);
                            }
                            element.val("-1");
                        });
                    }
                }); 
	        });
	    },
	    
	    /**
	     * 多图上传插件，切可以输入标题、详情等等
	     * @param name 里面空间的名称前缀
	     * @param file_type 提交接口上传文件的类型 如：user/head/common等，接口根据类型存放图片地址
	     * @param resolution 提交接口分辨率，用于图片生成且存放的文件夹 如：ldp/hdp/mdp/udp
         * @param field 包括那些控件 JSON格式
         * 如：
         * [{key:"title", name:"标题", type:"input"}, {key:"detail", name:"详情", type:"textarea"}]
	     * @param config 其他的一些配置信息 JSON格式
         * @param data 数组 控件初始化显示的内容，包括图片、标题、内容等等 JSON格式
         * data格式：
         * [
         *     {image:"1.jpg", title:"标题", detail:"详情",style:""},
         *     {image:"2.jpg", title:"标题2", detail:"详情2",style:""}
         * ]
         * Add by Liuw at 2012-12-07
         * @param up_url 处理上传的URL
         * @param width 缩略宽度
         * @param height 缩略高度
         * @param is_rec 请求是否来自推荐碎片
         * @param water 水印名
         * @param location 水印位置 1=左上角；2=顶部正中；3=右上角；4=中部左边；5=正中；6=中部右边；7=左下角；8=底部正中；9=右下角
         * @param input 文件组件的名字
         * @param api api传图接口
	     */
	    my_rich_upload: function(options) {
	        options = $.extend(false, {
	            name: "",
	            file_type: "common",
	            resolution: "",
	            field: [{key:"title", name:"标题", type:"input" , style:""}, {key:"detail", name:"详情", type:"textarea", style:'' ,value:'',tip:''}],
	            config: {width: 100, height: 80, timeout: 100, rows: 5, cols: 30},
                up_url: '',
                width: 0,
                height: 0,
                is_rec: 0,
                water: '',
                location:9,
                input: 'file',
	            data: {},
	            api: '3'
	        }, options);
	        
	        if(options.name == "") {
	            alert("设置input的name");
	            return false;
	        }
	        
            var action_url, params;
            if(options.resolution == "") {
                // 那么是普通保存接口 需要接口处理图片大小的
                action_url = options.api?conf_save_image_uri_v3:conf_save_image_uri;
                // 接口参数
                params = "{file_type: options.file_type}";
            } else {
                // 特殊接口需要指定分辨率的，不处理图片接口
                action_url = options.api?conf_transfer_image_uri_v3:conf_transfer_image_uri;
                // 接口参数
                params = "{file_type: options.file_type, resolution: options.resolution}";
            }
            //Add by Liuw:指定了上传处理路径的到上传地处理
        	if(typeof options.up_url != 'undefined' && options.up_url != ''){
        		action_url = options.up_url;
        	}
            
            // 绑定控件的对象
            var obj = $(this);
            
            // 上传控件的iframe
            var iframe_obj = $("<iframe style=\"border:0px;height:30px;width:200px;\" id=\"iframe_" + (new Date().getTime()) + "\" frameborder=\"0\" border=\"0\" cellspacing=\"0\" allowtransparency=\"true\" scrolling=\"no\" resizable=\"no\"></iframe>");
            // 把iframe放入到一个div中
            var div_obj = $("<div></div>");
            div_obj.append(iframe_obj);
            
            // 初始化iframe的时候。一定要有个间隔时间，否则document报错
            setTimeout(init_iframe, options.config.timeout);
            function init_iframe() {
                // iframe中的表单
                var form = $("<form action=\"" + action_url + "\" method=\"post\" enctype=\"multipart/form-data\"></form>").css({margin:"0px"});
                var span = $("<span></span>").css({
                                   "font-size":"12px", 
                                   overflow:"hidden", 
                                   position:"absolute"
                               });
                var file = $("<input type=\"file\" name=\"file\" size=\"1\">").css({
                    position:"absolute",
                    "z-index":"100", 
                    "margin-left":"-180px",
                    "font-size":"60px",
                    opacity:"0",
                    filter:"alpha(opacity=0)",
                    "margin-top":"-5px"
                    });
                var button = $("<button type=\"button\">"
                               + (obj.attr("text")?obj.attr("text"):"上传图片")
                               + "</button>").css({
                                   border: "#6600ff 1px solid",
                                   padding: "5px 10px",
                                   background: "#9933ff",
                                   color: "#ffffff",
                                   "font-size": "14px"
                               });
                
                // 绑定file事件，自动上传
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
                
                // iframe 添加body内容，添加form
                iframe_obj.contents().find("body").html("");
                iframe_obj.contents().find("body").css({margin:"0px"});
                iframe_obj.contents().find("body").append(form);
                // form里面加入span，便于控制样式
                form.append(span);
                // span中加入file button，button在file上面。file隐藏，这样子可以用一个按钮上传
                span.append(file);
                span.append(button);
                //Add by Liuw:要求缩略图片的
                if(typeof options.up_url != 'undefined' && options.up_url != ''){
	                if(typeof options.width != 'undefined' && options.width > 0){
	                	$('<input />').attr({type:'hidden', name:'tw'}).val(options.width).appendTo(form);
	                }
	                if(typeof options.height != 'undefined' && options.height > 0){
	                	$('<input />').attr({type:'hidden', name:'th'}).val(options.height).appendTo(form);
	                }
                }
                
                // 格式化参数
                var p = eval("(" + params + ")");
                
            	//Add by Lliuw:增加一些参数 
            	if(typeof options.water != 'undefined' && options.water != ''){
            		p.water = options.water;
            		p.location = options.location;
            	}
            	p.input = options.input;
            	
                // 添加参数
                $.each(p, function(k, v){
                    form.append("<input type=\"hidden\" name=\"" + k + "\" value=\"" + v + "\" />");
                });
            }
            
            // iframe提交以后，结果处理
            iframe_obj.unbind().load(function(){
                setTimeout(function(){
                    // 获取到iframe的dom对象
                    var my_frame = document.getElementById(iframe_obj.attr('id'));
                    try {
                        // 获取返回结果
                        var response = $(my_frame.contentWindow.document.body).text();
                        
                        if(response) {
                            var json = eval("(" + response + ")");
                            if(json.result_code == 0) {
                                // 成功
                                // 初始iframe
                                init_iframe();
                                // 加入图片数据
                                init_image({image: json.file_name});
                            } else {
                                // 失败
                                alert(json.result_msg);
                            }
                        }
                    } catch(e) {
                        
                    }
                }, options.config.timeout);
            });
            
            /**
             * 初始图片
             * @param row 一行数据
             */
            function init_image(row) {
                var row = $.extend({image:""}, row);
                
                if(row.image) {
                    // 如果有图片才构建所有的页面控件
                    get_image_url({
                        file_name: row.image, 
                        file_type: options.file_type, 
                        resolution: options.resolution
                        }, function(image){
                            var data_html = "<div style=\"margin:20px 20px 0;\"><div style=\"margin-right:"
                                             + "20px;float:left;width:" + options.config.width + "px;overflow:hidden\">" 
                                             + "<a href=\"" + image.source_image + "\" rel=\"rich_image_fancybox\"><img src=\""
                                             + image.image + "\" height=\"" + options.config.height
                                             + "px\" style=\"margin:5px;\" /></a><br/>" 
                                             + "<a href=\"javascript:;\" rel=\"rich_image_delete\">[删除]</a></div><div style=\"float:left;\">";
                        
                           $.each(options.field, function(k, field){
                                data_html += field.type!="hidden" ? "<dl><dt>" + field.name + "：</dt><dd>" : "";
                                
                                var val = eval("row." + field.key);
                                val = val==undefined?'':val;
                                if('input' == field.type) {
                                    data_html += "<input type=\"input\" name=\""
                                     + options.name + "[" + field.key + "][]\" value=\""
                                     + (val || !field.value ? val : field.value) + "\" style=\""+field.style+"\" /> "+(field.tip ? field.tip : '');
                                } else if('textarea' == field.type) {
                                    data_html += "<textarea name=\""
                                     + options.name + "[" + field.key + "][]\" rows=\""
                                     + options.config.rows + "\" cols=\"" 
                                     + options.config.cols + "\" style=\""+field.style+"\" >"
                                     + (val  || !field.value ? val : field.value) + "</textarea> "+(field.tip ? field.tip : '');
                                }
                                else if('hidden' == field.type) {
                                	data_html += "<input type=\"hidden\" name=\""
                                        + options.name + "[" + field.key + "][]\" value=\""
                                        + (val || !field.value ? val : field.value) + "\" /> "+(field.tip ? field.tip : '');
                                }
                                
                                data_html += "</dd></dl>";
                            });
                            
                            // 加入图片的hidden表
                            data_html += "<input type=\"hidden\" name=\"" + options.name + "[image][]\" value=\"" 
                                         + image.image_name + "\" />";
                            
                            data_html += "</div><div style=\"clear:both;display:block;content:'';width:100%\"></div></div>";
                            obj.append(data_html);
                            
                            // 图片绑定打开原图预览
                            $("a[rel='rich_image_fancybox']").unbind();
                            $("a[rel='rich_image_fancybox']").fancybox();
                            
                            // 删除DIV。时间绑定
                            $("a[rel='rich_image_delete']").unbind();
                            $("a[rel='rich_image_delete']").click(function(){
                                $(this).parent().parent().remove();
                            });
                        });
                }
            }
            
            // 添加到绑定控件
            obj.append(div_obj);
            
            // 初始化数据
            $.each(options.data, function(key, value) {
                init_image(value);
            });
	    },
	    
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
         * @param api 传图接口api
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
                input: 'file',
                api: '3'
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
            	action_url = options.api?conf_save_image_uri_v3:conf_save_image_uri;
                // action_url = "http://i.admin/main/test";
                params = "{file_type: options.file_type}";
            } else {
                // 特殊接口需要指定分辨率的，不处理图片接口
                // action_url = "http://joyotime.gicp.net/hl/private_api/image/transfer_image";
                // action_url = "http://223.4.93.206/image/transfer_image";
                action_url = options.api?conf_transfer_image_uri_v3:conf_transfer_image_uri;
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
                        init_widget($(myFrame));
                    }
                }
            });
            
            // 将当前元素替换成iframe上传组件内容
            element.replaceWith(iframeDiv);
        },
		
		/**
		 * 重写了DWZ的导出函数
		 */
        myDwzExport: function(){
            return this.each(function(){
                var $this = $(this);
                $this.click(function(event){
                    var url = unescape($this.attr("href")).replaceTmById($(event.target).parents(".unitBox:first"));
                    DWZ.debug(url);
                    if (!url.isFinishedTm()) {
                        alertMsg.error($this.attr("warn") || DWZ.msg("alertSelectMsg"));
                        return false;
                    }
                    var title = $this.attr("title");
                    if (title) {
                        alertMsg.confirm(title, {
                            okCall: function(){
                                window.open(url);
                            }
                        });
                    } else {
                        window.open(url);
                    }
                    event.preventDefault();
                });
            });
        },
        toJSON : function (object)  
        {  
         var type = typeof object;  
         if ('object' == type)  
         {  
          if (Array == object.constructor)  
           type = 'array';  
          else if (RegExp == object.constructor)  
           type = 'regexp';  
          else  
           type = 'object';  
         }  
           switch(type)  
         {  
             case 'undefined':  
            case 'unknown':   
           return;  
           break;  
          case 'function':  
            case 'boolean':  
          case 'regexp':  
           return object.toString();  
           break;  
          case 'number':  
           return isFinite(object) ? object.toString() : 'null';  
             break;  
          case 'string':  
              //return '"' + object.replace(/(\/\/|\/")/g,"\/\/$1").replace(\/\/n|/r|/t/g,function(){var a = arguments[0];return (a == '/n') ? '\/\/n':  (a == '/r') ? '\/\/r': (a == '/t') ? '\/\/t': ""  }) + '"';  
              break;  
          case 'object':  
           if (object === null) return 'null';  
             var results = [];  
             for (var property in object) {  
               var value = jQuery.toJSON(object[property]);  
               if (value !== undefined)  
                 results.push(jQuery.toJSON(property) + ':' + value);  
             }  
             return '{' + results.join(',') + '}';  
           break;  
          case 'array':  
           var results = [];  
             for(var i = 0; i < object.length; i++)  
           {  
            var value = jQuery.toJSON(object[i]);  
               if (value !== undefined) results.push(value);  
           }  
             return '[' + results.join(',') + ']';  
           break;  
           }  
        }  
	});
})(jQuery);
