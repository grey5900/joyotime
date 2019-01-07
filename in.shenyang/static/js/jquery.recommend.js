// 为Jquery扩展一个格式化字符串的函数
jQuery.extend({
    formatString: function(){
        var rval    = "";
        if(arguments.length >= 1){
            rval    = arguments[0];
        }
        for(var n=1; n<arguments.length; n++){
            var arg = arguments[n];
            rval    = rval.replace(new RegExp("\\{" + (n-1) + ",(\\d+)\\}", "ig"), function($1, $2){var zerostr = "";for(var n=0; n<parseInt($2) - String(arg).length; n++){zerostr += "0";}return zerostr  + arg;});
            rval    = rval.replace(new RegExp("\\{" + (n-1) + "\\}", "ig"), arg);
        }
        return rval;
    }
});


var arr = null;

function bb_bold(objstr){
    $("#" + objstr).focus();
    $("#" + objstr).select();
    if (typeof document.selection !== 'undefined' && document.selection && document.selection.type == "Text") {
        var range   = document.selection.createRange();
        var re      = /\[b\](.*?)\[\/b\]/ig;
        if(re.test(range.text)){
            range.text  = range.text.replace(re, '$1');
        }else{
            range.text  = "[b]" + range.text + "[/b]";
        }
    }else{
        var txtval = $('#' + objstr).val();
        var re      = /\[b\](.*?)\[\/b\]/ig;
        if(re.test(txtval)){
            txtval  = txtval.replace(re, '$1');
        }else{
            txtval  = "[b]" + txtval + "[/b]";
        }
        $('#' + objstr).val(txtval);
    }
}
function bb_color(objstr, color){
    $("#" + objstr).focus();
    $("#" + objstr).select();
    if (typeof document.selection !== 'undefined' && document.selection && document.selection.type == "Text") {
        var range = document.selection.createRange();
        var re      = /\[color.*?\](.*?)\[\/color\]/ig;
        if(color==""){
            range.text  = range.text.replace(re, "$1");
        }else{
            range.text = "[color=" + color + "]" + range.text.replace(re, '$1') + "[/color]";
        }
    }else{
        var txtval = $('#' + objstr).val();
        var re      = /\[color.*?\](.*?)\[\/color\]/ig;
        if(color==""){
            txtval  = txtval.replace(re, '$1');
        }else{
            txtval  = "[color=" + color + "]" + txtval.replace(re, '$1') + "[/color]";
        }
        $('#' + objstr).val(txtval);
    }
}
function bbcode2html(str) {
    str = String(str);
    
    if(str == "" || str == "undefined") {
        return "";
    }

    // 替换HTML标签
    str = str.replace(/&/ig, "&amp;");
    str = str.replace(/</ig, "&lt;");
    str = str.replace(/>/ig, "&gt;");
    
    str = str.replace(/\[color=([^\[\<]+?)\](.*?)\[\/color\]/ig, '<font color="$1">$2</font>');
    str = str.replace(/\[b\](.*?)\[\/b\]/ig, '<strong>$1</strong>');

    return(str);
}
function filterbbcode(str){
    str = String(str);
    
    if(str == "" || str == "undefined") {
        return "";
    }
    // 替换HTML标签
    str = str.replace(/&/ig, "&amp;");
    str = str.replace(/</ig, "&lt;");
    str = str.replace(/>/ig, "&gt;");
    str = str.replace(/\[color=([^\[\<]+?)\](.*?)\[\/color\]/ig, '$2');
    str = str.replace(/\[b\](.*?)\[\/b\]/ig, '$1');

    return(str);
}

//添加数据事件
function addData(fid){
	openEditor(fid, null);
}

//清空编辑器
function clear_editor() {
 $("#f_id").val("");
 $("#f_title").val("");
 $("#f_title_link").val("");
 $("#f_image").val("");
 $("#f_image_link").val("");
 $("#f_category").val("");
 $("#f_category_link").val("");
 $("#f_author").val("");
 $("#f_author_link").val("");
 $("#f_author_avatar").val("");
 $("#f_intro").val("");
 $("#f_start_time").val("");
 $("#f_end_time").val("");
 
 $("#ooxx_image_span_id_f_image").hide();
 $("a[rel='f_image']", $("#ooxx_image_span_id_f_image")).attr("href", "");
 $("img", $("#ooxx_image_span_id_f_image")).attr("src", "");
 $("#ooxx_image_span_id_f_author_avatar").hide();
 $("a[rel='f_author_avatar']", $("#ooxx_image_span_id_f_author_avatar")).attr("href", "");
 $("img", $("#ooxx_image_span_id_f_author_avatar")).attr("src", "");
}

//设置数据到编辑框
function put_editor(fid, obj) {
	openEditor(fid, obj);
}

//控制按钮事件
function buttonEvent(editor){
 // 取得list_save.editor中的jQuery实例
 var $editor = editor.editor;
 // 取得选中的对象
 var $selobj = $editor.find(".selected");
 if($selobj.length > 0){
     $('#editor_delete').attr("disabled", false);
     $('#editor_top').attr("disabled", false);
     $('#editor_up').attr("disabled", false);
     $('#editor_down').attr("disabled", false);
 }else{
     $('#editor_delete').attr("disabled", true);
     $('#editor_top').attr("disabled", true);
     $('#editor_up').attr("disabled", true);
     $('#editor_down').attr("disabled", true);
 }
 if($selobj.length == 1){
     $('#editor_edit').attr("disabled", false);
 }else{
     $('#editor_edit').attr("disabled", true);
 }
}

/*
 * 打开编辑窗口
 * @param int fid,碎片ID
 * @param JSON || empty sel_data,选中的数据
 */
function openEditor(fid, sel_data){
	var url = '/web/new_recommend/open_editor/fid/'+fid;
	var t = '添加数据';
	//DIALOG窗口属性
	var options = {
		width:'660',
		height:'500',
		max:false,
		mask:true,
		mixable:true,
		minable:true,
		resizable:true,
		drawable:true,
		fresh:false
	};
	if(typeof sel_data != 'undefined'){
		if(typeof sel_data == 'object'){
			var sel = JSON.stringify(sel_data);
			var s = $.base64.encode(encodeURIComponent(sel));
			url += '/sel/'+s;
		}
		t = '编辑数据';
	}
	//DIALOG打开
	$.pdialog.open(url, 'dlg_rec_data_editor', t, options);
}
/* 
* 移除弹出窗口
* 参数@url, 参数选择器URL
*/
function closeEditor(){
 var bodyobj = document.body;
 var docobj  = document.documentElement;
 var overlay = document.getElementById("editor_wnd");
 overlay.style.display = "none";

 // 还原滚动条
 //docobj.style.overflow = document._documentOverFlow;
}
/* 
* 设置弹出窗口位置
* 参数@url, 参数选择器URL
*/
function positionEditor(){
 var bodyobj = document.body;
 var docobj  = document.documentElement;
 var divobj  = document.getElementById("editor_wnd");
 if(divobj){
     // 遮罩层
     var overlay = document.getElementById("editor_overlay");
     overlay.style.height    = (parseInt($(overlay).css("height")) + bodyobj.offsetHeight) + "px";
     // 内容框架
     var frameobj        = document.getElementById("editor_form");
     // 获取内容窗口宽高
     // var width, height; 
     // if (self.innerHeight) {    // all except Explorer 
         // width = self.innerWidth; 
         // height = self.innerHeight; 
     // } else if (docobj && docobj.clientHeight) { // Explorer 6 Strict Mode 
         // width = docobj.clientWidth; 
         // height = docobj.clientHeight; 
     // } else if (document.body) { // other Explorers 
         // width = document.body.clientWidth; 
         // height = document.body.clientHeight; 
     // }
     // 设置框架宽度和位置
     frameobj.style.top  = "10px";
     frameobj.style.left = (frameobj.offsetWidth)/2 + "px";
 }
}

/*
 * 格式化数据编辑窗口的扩展属性编辑面板
 */
function format_extra_input(data){
	var output = '';
	for(exp in data) {
		output += '\
			<p class="nowrap">\
				<label for="' + data[exp].key + '">' + data[exp].view + '：</label>\
		';
		switch (data[exp].type) {
			//多选框
			case 'check':
				var value = data[exp].def_value.split(",");
				for (var i in value){
					output += '<label><input type="checkbox" name="' + data[exp].key + '" value="' + value[i] + '" id="' + data[exp].key + '_' + i + '" />' + value[i] +'</label> ';
				}
				break;
			//单选框
			case 'radio':
				var value = data[exp].def_value.split(",");
				for (var i in value){
					output += '<label><input type="radio" name="' + data[exp].key + '" value="' + value[i] + '" id="' + data[exp].key + '_' + i + '" />' + value[i] +'</label> ';
				}
				break;
			//下拉列表
			case 'select':
				output += '\
					<select id="' + data[exp].key + '" class="combox">\
						<option value="">请选择</option>\
				';
				var value = data[exp].def_value.split(',');
				for (var i in value){
					output += '<option value="' + value[i] + '">' + value[i] + '</option>';
				}
				output += '</select>';
				break;
			//文本域
			case 'textarea':
				output += '<textarea name="' + data[exp].key + '" id="' + data[exp].key + '" cols="80" rows="5" class="textInput"></textarea>';
				break;
			//日期
			case 'date':
				output += '<input type="text" name="' + data[exp].key + '" value="" id="' + data[exp].key + '" class="date textInput valid" datefmt="yyyy-MM-dd" mindate="{%y-10}-%M-%d" maxdate="{%y+10}-%M" /><a class="inputDateButton" href="javascript:;">选择</a>';
				break;
			//时间
			case 'time':
				output += '<input type="text" name="' + data[exp].key + '" value="" id="' + data[exp].key + '" class="textInput" />';
				break;
			//图片
			case 'img':
				output += '<input type="file" name="' + data[exp].key + '" value="" id="' + data[exp].key + '" />';
				break;
			//文本框
			default:
				output += '<input type="text" name="' + data[exp].key + '" value="" id="' + data[exp].key + '" class="textInput" />';
				break;
		}
		output += '</p>';
	}
	return output;
}

/**
* 添加或修改记录
*/
function editor_onclick(b) {
 var image_url = $("a[rel='f_image']", $("#ooxx_image_span_id_f_image")).attr("href");
 if(image_url) {
     image_url = image_url.substring(0, image_url.lastIndexOf("?"));
 } else {
     image_url = "";
 }
 
 var author_avatar_url = $("a[rel='f_author_avatar']", $("#ooxx_image_span_id_f_author_avatar")).attr("href");
 if(author_avatar_url) {
     author_avatar_url = author_avatar_url.substring(0, author_avatar_url.lastIndexOf("?"));
 } else {
     author_avatar_url = "";
 }

 list_save.push(
     // 数据
     {id:$("#f_id").val(),title:$("#f_title").val(),title_link:$("#f_title_link").val(),category:$("#f_category").val(),category_link:$("#f_category_link").val(),image:$("#f_image").val(),image_link:$("#f_image_link").val(),author:$("#f_author").val(),author_link:$("#f_author_link").val(),author_avatar:$("#f_author_avatar").val(),intro:$("#f_intro").val(),image_url:image_url,author_avatar_url:author_avatar_url,start_time:$("#f_start_time").val(),end_time:$("#f_end_time").val()},
     // 替换标志
     b
 );
 closeEditor();
}

/**
* 加入一条记录
*/
function add_onclick() {
 editor_onclick(false);
}

/**
* 编辑一条记录
*/
function edit_onclick() {
 editor_onclick(true);
}

$(window).scroll(positionEditor);
$(window).resize(positionEditor);
/************************************
 *
 * 保存列表类, 用于保存及编辑数据
 *
 ************************************/
function ListSave(){
    // 与此备绑定的HTML元素, 这是个JQuery对象
    this.editor = null;
    // 是否绑定标志
    this.isbind = false;
    // 与HTML元素绑定的函数
    this.bind = function(obj){
        this.editor = obj;
        this.isbind = true;
        // 事件[按键]
        this.editor.bind("keydown", {editor:this}, function(event){
            var editor = event.data.editor;
            editor.onkeydown(editor);
        });
    }
    // 显示提示信息, 这里用于扩展其他显示方式, 不只有alert
    this.message = function(msg){
        alert(msg);
    }
    // 列表中被选中的列表都存储在这个数组里面
    this.selected = new Array();
    // 提交按钮
    
    /****************************
     * 下面的函数必须先绑定后才能使用
     ****************************/
    // 清除列表中的数据
    this.clear  = function(){
        //if(!this.isbind){this.message("请绑定html元素后再使用此方法"); return false;}
        this.editor.empty();
        this.onchange(this);
    }
    // 向列表中追加数据[单个]
    this._push  = function(listitem, isreplace, extraProperty){
        //if(!this.isbind){this.message("请绑定html元素后再使用此方法"); return false;}
        // tid不为空, 判断列表中是否存在与此ID相同的数据
        if(isreplace === true){
            var replaceobj = this.editor.find("li.selected:first");
            var nextobj = replaceobj.next();
            replaceobj.remove();
        }
        if(listitem.id){
            if(this.editor.find("li[id=" + listitem.id + "]").length > 0){
                if(confirm("列表中已经存在相同的数据, 是否覆盖?")){
                    var replaceobj = this.editor.find("li[id=" + listitem.id + "]");
                    var nextobj = replaceobj.next();
                    replaceobj.remove();
                }else{
                    return;
                }
            }
        }
        if(listitem){
            // 生成列表中的项目html
            var itemstr = $.formatString('<li><a rel="{0}" title="{1}" href="{0}"><span class="title">{2}</span></a></li>', listitem.title_link, filterbbcode(listitem.title), bbcode2html(listitem.title));
            // 将html转换为jQuery对象
            var itemobj = $(itemstr);
            if(listitem.category){
                itemobj.find("a .title").prepend($.formatString('[{0}] ', listitem.category));
            }
            if(listitem.title_link){
                itemobj.find("a .title").prepend($.formatString(' <span title="打开标题链接" class="open" onclick="window.open(\'{0}\');"></span>', listitem.title_link));
            }
            if(listitem.author) {
                itemobj.find("a .title").append($.formatString('[{0}] ', listitem.author));
            }
            if(listitem.start_time && listitem.end_time) {
                itemobj.find("a .title").append($.formatString('[{0} ~ {1}] ', listitem.start_time, listitem.end_time));
            }
            if(listitem.author_avatar) {
                // 用户头像
                itemobj.find("a").prepend($.formatString('<span class="img"><img src="{0}" height="30" /></span>', listitem.author_avatar_url));                
            }
            if(listitem.image){
                if(listitem.image.indexOf(".swf") == -1) {
                    itemobj.find("a").prepend($.formatString('<span class="img"><img src="{0}" height="60" /></span>', listitem.image_url));
                } else {
                    itemobj.find("a").prepend($.formatString('<span class="img"><embed type="application/x-shockwave-flash" src="{0}" width="80" height="60" /></span>', listitem.image_url));
                }
            }
            if(listitem.intro){
                itemobj.find("a").append($.formatString('<span class="desc">{0}</span>', listitem.intro));
            }
            // 将附加数据存入这些对象中
            itemobj.attr("id", listitem.id);
            itemobj.attr("title", listitem.title);
            itemobj.attr("title_link", listitem.title_link);
            itemobj.attr("image",listitem.image);
            itemobj.attr("image_link",listitem.image_link);
            itemobj.attr("intro",listitem.intro);
            // 二外两个保存图片的绝对地址
            itemobj.attr("image_url", listitem.image_url);
            
            //Add by Liuw: 增加扩展属性
            if(typeof extraProperty != 'undefined' && extraProperty != ''){
            	for(var k in extraProperty){
            		if(typeof listitem[k] != 'undefined'){
            			itemobj.attr('extra_'+k, listitem[k]);
            		}
            	}
            }
            
            // 为此列表项目添加事件[单击], 并在传递当前备选列表类实例对象到event.data内
            itemobj.bind("click", {editor:this}, function(event){
                // 取得备选列表类实例对象
                var editor = event.data.editor;
                var thisobj = $(this);
                // 按住Shift键多选
                if(event.shiftKey){
                    thisobj.toggleClass("selected");
                    // editor.selected.push({tid:thisobj.attr("tid"), title:thisobj.attr("title"), link:thisobj.attr("link"), imgs:thisobj.attr("imgs"), desc:thisobj.attr("desc"), cat:thisobj.attr("cat"), cat_link:thisobj.attr("cat_link"), img_link:thisobj.attr("img_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),subtitle:thisobj.attr("subtitle"),sublink:thisobj.attr("sublink")});
                }else{
                    thisobj.siblings().removeClass("selected");
                    thisobj.addClass("selected");
                    // 没有按住Shift键, 将选中的列表清空, 然后再将当前选择项目加入到选中列表中
                    editor.selected = new Array();
                    // editor.selected.push({tid:thisobj.attr("tid"), title:thisobj.attr("title"), link:thisobj.attr("link"), imgs:thisobj.attr("imgs"), desc:thisobj.attr("desc"), cat:thisobj.attr("cat"), cat_link:thisobj.attr("cat_link"), img_link:thisobj.attr("img_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),subtitle:thisobj.attr("subtitle"),sublink:thisobj.attr("sublink")});
                }
                var sel = {
                	id:thisobj.attr("id"), 
                	title:thisobj.attr("title"), 
                	title_link:thisobj.attr("title_link"), 
                	image:thisobj.attr("image"), 
                	image_link:thisobj.attr("image_link"),
                	intro:thisobj.attr("intro"),
                	image_url:thisobj.attr("image_url")
                };
                var extras = arr.extraProperty;
                if(typeof extras != 'undefined' && extras != ''){
                	for(var key in extras){
                		var d = thisobj.attr('extra_'+key);
                		if(typeof d != 'undefined' && d != '')
                			sel[key] = d;
                	}
                }
                editor.selected.push(sel);
                // 调用接口: 当选择列表中的项目
                editor.onselect(editor);
                return false;
            });
            // 事件[双击]
            itemobj.bind("dblclick", {editor:this}, function(event){
                // 取得备选列表类实例对象
                var editor = event.data.editor;
                editor.ondblclick(editor);
            });
            // 如果存在相同的数据, 即定义了nextobj
            if(nextobj && nextobj.length>0){
                nextobj.before(itemobj);
            }else{
                // 不存在相同的数据, 追加到末尾
                this.editor.append(itemobj);
            }
            // 调用事件数据变化
            this.onchange(this);
        }
    }
    // 从列表中删除选中在对象
    this.del = function(){
        // 取得选中的对象
        var $selobj = this.editor.find(".selected");
        // 如果选中了对象且确认删除
        /*
        if($selobj.length == 1){
            msg = "是否删除这条数据?";
        }else{
            msg = "是否删除这些数据?";
        }
        */
        if($selobj.length>0){
            // 删除这些DOM对象
            $selobj.remove();
        }
        // 调用接口[数据改变]
        this.onchange(this);
    }
    // 将选中列表上移到顶部
    this.top = function(){
        var $selobj = this.editor.find(".selected");
        // 从当前选中的对象最后一个开始循环, 依次插入到这个对象的前一个对象
        for(var n=$selobj.length - 1; n >= 0; n--){
            $($selobj[n]).parent().prepend($selobj[n]);
        }
    }
    // 将选中列表上移一位
    this.up = function(){
        // 取得选中的对象
        var $selobj = this.editor.find(".selected");
        // 从当前选中的对象最后一个开始循环, 依次插入到这个对象的前一个对象
        for(var n=0; n<$selobj.length; n++){
            $($selobj[n]).prev().before($selobj[n]);
        }
    }
    // 将选中列表下移一位
    this.down = function(){
        // 取得选中的对象
        var $selobj = this.editor.find(".selected");
        // 从当前选中的对象最后一个开始循环, 依次插入到这个对象的前一个对象
        for(var n=$selobj.length-1; n>=0; n--){
            $($selobj[n]).next().after($selobj[n]);
        }
    }
    // 向列表中追加数据[数组]或单个对象
    this.push = function(listitem, isreplace, extraProperty){
        // 传递的参数是数组
        if(listitem instanceof Array){
            for(var n=0; n<listitem.length; n++){
                this._push(listitem[n], isreplace, extraProperty);
            }
        }else{
            this._push(listitem, isreplace, extraProperty);
        }
    }
    this.submit = function(submit_url, extraProperty){
        var id        = "";
        var title      = "";
        var title_link    = "";
        var image  = "";
        var image_link     = "";
        var intro = "";
        var start_time = "";
        var end_time = "";    

        //EXTRA DATA
        if(typeof extraProperty != 'undefined' && extraProperty != ''){ 
            var extData = eval('({})');
        	for(var key in extraProperty){
        		extData['extra_'+key] = "";
        	}
        }
        
        if(this.editor.find("li").length <=0){
            if(window.confirm("请选择您要保存的列表, 如果真的要提交请确定！")) {}else{return;}
        }
        this.editor.find("li").each(function(){
            id      += (typeof $(this).attr("id") == "undefined" ? "" : $(this).attr("id")) + "┆";
            title    += (typeof $(this).attr("title") == "undefined" ? "" : $(this).attr("title")) + "┆";
            title_link     += (typeof $(this).attr("title_link") == "undefined" ? "" : $(this).attr("title_link")) + "┆";
            image     += (typeof $(this).attr("image") == "undefined" ? "" : $(this).attr("image_url")) + "┆";
            image_link      += (typeof $(this).attr("image_link") == "undefined" ? "" : $(this).attr("image_link")) + "┆";
            intro        += (typeof $(this).attr("intro") == "undefined" ? "" : $(this).attr("intro")) + "┆";
            //扩展属性数据
            if(typeof extData != 'undefined'){
            	for(var key in extData){
            		var d = $(this).attr(key);
            		extData[key] += (typeof $(this).attr(key) == 'undefined' ? "" : $(this).attr(key))+'┆';
            	}
            }
        });
        id      = id.replace(/┆$/, "");
        title    = title.replace(/┆$/, "");
        title_link = title_link.replace(/┆$/, "");
        image     = image.replace(/┆$/, "");
        image_link      = image_link.replace(/┆$/, ""); 
        //处理扩展属性数据
        if(typeof extData != 'undefined'){
        	for(var key in extData){
        		extData[key] = extData[key].replace(/┆$/,"");
        	}
        }
        
        //SUBMIT DATA
        var data = {
        	'id'			:encodeURIComponent(id),
        	'title'			:encodeURIComponent(title),
        	'title_link'	:encodeURIComponent(title_link),
        	'image'			:encodeURIComponent(image),
        	'image_link'	:encodeURIComponent(image_link),
        	'intro'			:encodeURIComponent(intro)
        };
        //链接JSON
        if(typeof extData != 'undefined'){
        	for(var key in extData){
        		data[key] = encodeURIComponent(extData[key]);
        	}
        }
        $.post(submit_url, data, function(data) {
            var json = eval('('+data+')');
            var msg = json.msg;
            var code = json.code;
            if(code == 0){//SUCCESS
            	alertMsg.correct(msg);
            	list_source.clear();
            	list_save.clear();
            }else{
            	alertMsg.error(msg);
            }
        });
    }
    // [接口] 当在一个列表项目上按键时调用
    this.onkeydown = function(editor){
        return false;
    }
    // [接口] 当双击列表中的项目时调用
    this.ondblclick = function(editor){
        return false;
    }
    // [接口] 当选择列表中的项目时调用
    this.onselect = function(editor){
        return false;
    }
    // [接口] 当列表中的数据改变时
    this.onchange = function(editor){
        return false;
    }
}

/*
 * 备选列表
 */
function ListSource(){
    // 与此备绑定的HTML元素, 这是个JQuery对象
    this.editor = null;
    // 是否绑定标志
    this.isbind = false;
    // 与HTML元素绑定的函数
    this.bind = function(obj){
        this.editor = obj;
        this.isbind = true;
    }
    // 显示提示信息, 这里用于扩展其他显示方式, 不只有alert
    this.message = function(msg){
        alertMsg.correct(msg);
    }
    // 列表中被选中的列表都存储在这个数组里面
    this.selected = new Array();
    /***************************
     * 下面的函数必须先绑定后才能使用
     ***************************/
    // 清除列表中的数据
    this.clear  = function(){
        //if(!this.isbind){this.message("请绑定html元素后再使用此方法"); return false;}
        this.editor.empty();
    }
    // 向列表中追加数据
    this._push  = function(listitem){
        //if(!this.isbind){this.message("请绑定html元素后再使用此方法"); return false;}
        if(listitem){
            // 生成列表中的项目html
            var itemstr = $.formatString('<li><a rel="{0}" title="{1}" href="{0}"><span class="title">{2}</span></a></li>', listitem.title_link, filterbbcode(listitem.title), bbcode2html(listitem.title));
            // 将html转换为jQuery对象
            var itemobj = $(itemstr);
            if(listitem.category){
                itemobj.find("a .title").prepend($.formatString('[{0}] ', listitem.category));
            }
            if(listitem.title_link){
                itemobj.find("a .title").prepend($.formatString(' <span title="打开标题链接" class="open" onclick="window.open(\'{0}\');"></span>', listitem.title_link));
            }
            if(listitem.author) {
                itemobj.find("a .title").append($.formatString('[{0}] ', listitem.author));
            }
            if(listitem.author_avatar) {
                // 用户头像
                itemobj.find("a").prepend($.formatString('<span class="img"><img src="{0}" height="30" /></span>', listitem.author_avatar_url));                
            }
            if(listitem.image){
                if(listitem.image.indexOf(".swf") == -1) {
                    itemobj.find("a").prepend($.formatString('<span class="img"><img src="{0}" height="60" /></span>', listitem.image_url));
                } else {
                    itemobj.find("a").prepend($.formatString('<span class="img"><embed type="application/x-shockwave-flash" src="{0}" width="80" height="60" /></span>', listitem.image_url));
                }
            }
            if(listitem.intro){
                itemobj.find("a").append($.formatString('<span class="desc">{0}</span>', listitem.intro));
            }
            // 将附加数据存入这些对象中
            itemobj.attr("id", listitem.id);
            itemobj.attr("title", listitem.title);
            itemobj.attr("title_link", listitem.title_link);
            itemobj.attr("image",listitem.image);
            itemobj.attr("image_link",listitem.image_link);
            itemobj.attr("category", listitem.category);
            itemobj.attr("category_link", listitem.category_link);
            itemobj.attr("author", listitem.author);
            itemobj.attr("author_link", listitem.author_link);
            itemobj.attr("author_avatar", listitem.author_avatar);
            itemobj.attr("intro",listitem.intro);
            // 额外两个
            itemobj.attr("image_url", listitem.image_url);
            itemobj.attr("author_avatar_url", listitem.author_avatar_url);
            // 为此列表项目添加事件[单击], 并在传递当前备选列表类实例对象到event.data内
            itemobj.bind("click", {editor:this}, function(event){
                // 取得备选列表类实例对象
                var editor = event.data.editor;
                var thisobj = $(this);
                // 按住Shift键多选
                if(event.shiftKey){
                    thisobj.toggleClass("selected");
                    // editor.selected.push({id:thisobj.attr("id"), title:thisobj.attr("title"), title_link:thisobj.attr("title_link"), category:thisobj.attr("category"), category_link:thisobj.attr("category_link"), image:thisobj.attr("image"), image_link:thisobj.attr("image_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),author_avatar:thisobj.attr("author_avatar"),intro:thisobj.attr("intro")});
                }else{
                    thisobj.siblings().removeClass("selected");
                    thisobj.addClass("selected");
                    // 没有按住Shift键, 将选中的列表清空, 然后再将当前选择项目加入到选中列表中
                    editor.selected = new Array();
                    // editor.selected.push({id:thisobj.attr("id"), title:thisobj.attr("title"), title_link:thisobj.attr("title_link"), category:thisobj.attr("category"), category_link:thisobj.attr("category_link"), image:thisobj.attr("image"), image_link:thisobj.attr("image_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),author_avatar:thisobj.attr("author_avatar"),intro:thisobj.attr("intro")});
                }
                editor.selected.push({id:thisobj.attr("id"), title:thisobj.attr("title"), title_link:thisobj.attr("title_link"), category:thisobj.attr("category"), category_link:thisobj.attr("category_link"), image:thisobj.attr("image"), image_link:thisobj.attr("image_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),author_avatar:thisobj.attr("author_avatar"),intro:thisobj.attr("intro"),image_url:thisobj.attr("image_url"),author_avatar_url:thisobj.attr("author_avatar_url")});
                // 调用接口: 当选择列表中的项目
                editor.onselect(editor);
                return false;
            });
            // 事件[双击]
            itemobj.bind("dblclick", {editor:this}, function(event){
                // 取得备选列表类实例对象
                var editor = event.data.editor;
                editor.ondblclick(editor);
            });
            this.editor.append(itemobj);
        }
    }
    // 向列表中追加数据[数组]或单个对象
    this.push = function(listitem){
        // 传递的参数是数组
        if(listitem instanceof Array){
            for(var n=0; n<listitem.length; n++){
                this._push(listitem[n]);
            }
        }else{
            this._push(listitem);
        }
    }
    this.remove = function(listitem){
        // 传递的参数是数组
        if(listitem instanceof Array){
            for(var n=0; n<listitem.length; n++){
                this._remove(listitem[n]);
            }
        }else{
            this._remove(listitem);
        }
    }
    // [接口] 当双击列表中的项目时调用
    this.ondblclick = function(editor){
        return false;
    }
    // [接口] 当选择列表中的项目时调用
    this.onselect = function(editor){
        return false;
    }
    this.onchange = function(editor){
        return false;
    }
}
/*
 * 用于碎片推荐的JQUERY插件
 * 
 * Create by 2012-11-29
 * @author Liuw
 * 
 */
(function($){
	var dom = null;
	var d_lsource = $("<ol></ol>").attr('id','list_source');
	var d_lsave = $("<ol></ol>").attr('id','list_save');
	
	$.fn.page_load=function(){
	    // 将list_source类与HTML元素绑定
	    list_source.bind(d_lsource);
	    list_source.clear();
	    // 设置list_source项目双击事件
	    list_source.ondblclick = function(editor){
	        list_save.push(list_source.selected);
	    }
	    // 将list_save类与HTML元素绑定
	    list_save.bind(d_lsave);
	    list_save.clear();
	    list_save.ondblclick = function(editor){
	        if(editor.selected.length > 0){
	            var obj = editor.selected[0];
	            // 先清空编辑器
	            clear_editor();
	            openEditor(arr.fid, obj);
	        }
	    }
	    // 传递过来的参数, 1, list_save实例, 2, 触发此事件的DOM对象
	    list_save.onkeydown = function(editor){
	        switch(event.keyCode){
	            // Del 键
	            case 46:
	                editor.del();
	                break;
	            // 上方向键[当前选中新闻向上移动]
	            case 38:
	                editor.up();
	                break;
	            // 下方向键[当前选中新闻向下移动]
	            case 40:
	                editor.down();
	                break;
	            // 回车键[显示编辑文本框]
	            case 13:
	                
	                break;
	            default:
	                //alert(event.keyCode);
	                break;
	        }
	    }
	    list_save.onselect = function(editor){
	        // 调用按钮状态事件
	        buttonEvent(editor);
	    }
	    list_save.onchange = function(editor){
	        // 取得list_save.editor中的jQuery实例
	        var $editor = editor.editor;
	        // 取得选中的对象
	        var $alllist = $editor.find("li");
	        $("#news_savedcount").text($alllist.length);
	        // 调用按钮状态事件
	        buttonEvent(editor);
	    }
	}
	
	/*
	 * 初始化推荐面板
	 * using:$(dom).inRecommend(JSON);
	 * @param string domId,父元素ID
	 * @param JSON settings，推荐碎片属性,包含：碎片ID，数据源，最大推荐数，图片尺寸，推荐数据扩展属性 
	 */
	$.fn.inRecommend=function(options){
		dom = $(this);
		list_source = new ListSource();
		list_save = new ListSave();
		dom.page_load();
		var btnClass = 'btn_silver';
		var updateLink = '';
		var defaults = {
			fid:-1,//碎片ID，默认为-1
			name:'',//碎片名称
			description:'',//碎片描述
			dataSource:'',//备选数据源
			fregType:0,//碎片类型，0为手动推荐，1为自动推荐
			rule:{
				max_length:'*',//最大推荐数，默认不限制
				pic_size:''//图片尺寸，默认为空
			},
			extraProperty:''//扩展属性
		};
		var fragment = $.extend({}, defaults, options);
		arr = fragment;
		var saveDataLink = '/web/new_recommend/save_rec/fid/'+fragment.fid+'/?'+(new Date().getTime());
		var savedDataLink = '/web/new_recommend/rec_list/fid/'+fragment.fid+'/?'+(new Date().getTime());
		
		if(fragment.fid <= 0){
			alertMsg.error("非法的碎片");
		}else{		
			//样式表
			dom.append($("<link />").attr({media:'screen',type:'text/css',rel:'stylesheet',href:'/static/skin/recommend_editor/style.css?'+(new Date().getTime())}));
			if(fragment.fregType == 1){
				dom.append($("<button>更新数据</button>").attr({type:'button',id:'autoUpload'}));
			}else{
				updateLink = '/web/new_fragment/update_frag/fid/'+fragment.fid;
				saveDataLink = '/web/new_recommend/save_rec/fid/'+fragment.fid;
				//卡片
				var dl = $("<dl></dl>").addClass('card');
				dom.append(dl);
				//卡片标题
				dl.append($("<dt><ul></ul></dt>").append($("<li></li>").append($("<a></a>").attr({'class':'active',href:'#'}).append("<strong>正在编辑：</strong>").append($("<font></font>").attr('color','red').append(fragment.name)))));
				//主面板
				var dd = $("<dd></dd>");
				dl.append(dd);
				var panel  =$("<table></table>").attr({width:'100%',border:'0',cellspacing:'5',cellpadding:'0',style:'border-spacing:5px;table-layout:fixed;'}).appendTo(dd);
				//顶部的动作按钮
				var tr = $("<tr></tr>");
				var tbody = $("<tbody></tbody>").append(tr)
				panel.append(tbody);
				if(fragment.dataSource != ''){
					//备选
					var tdLeft = $("<td></td>").attr({valign:'top',width:'45%'});
					tr.append(tdLeft);
		//			tdLeft.append(this.dsPanel(fragment.dataSource));
					tdLeft.append(dom.ds_list());
					//中间
					$("<td></td>").attr({width:'40',valign:'top'}).appendTo(tr);
				}
				//推荐
				var tdRight = $("<td></td>").attr({valign:'top'}).appendTo(tr);
				var btnAdd = $('<button>添加数据</button>').attr('class',btnClass).appendTo(tdRight);
				btnAdd.click(function(){
					addData(fragment.fid);
				});
				//标题栏
				tr = null;
				tr = $("<tr></tr>");
				tbody.append(tr);
				if(fragment.dataSource != ''){
					//备选
					$("<td>备选列表</td>").attr({'valign':'top'}).appendTo(tr);
					//中间
					$("<td></td>").attr({valign:'top'}).appendTo(tr);
				}
				//推荐
				var tdRList = $("<td>保存列表</td>").attr('valign','top').appendTo(tr);
				tdRList.append('(<span id="news_savedcount">0</span>/<span id="news_maxcount">'+(typeof fragment.rule.max_length == 'undefined' || fragment.rule.max_length == '' ? '*' : fragment.rule.max_length)+'</span>');
				if(fragment.rule.pic_size != null && fragment.rule.pic_size != null){
					tdRList.append('&nbsp;');
					var f = $("<font></font>").attr('color','red').appendTo(tdRList);
					f.append(fragment.rule.pic_size);
				}
				tdRList.append(")");
				//数据区
				tr = null;
				tr = $("<tr></tr>").appendTo(tbody);
				//备选区
				if(fragment.dataSource != ''){
					//备选数据
					var td = $("<td></td>").attr({width:'45%',valign:'top'}).appendTo(tr);
					var dv = $("<div></div>").attr({style:'background:#fff;border:2px inset;height:350px;overflow:auto;'});
					dv.append(d_lsource);
					td.append(dv);
					//左到右
					td = null;
					td = $("<td></td>").attr({width:'10%',align:'center',valign:'top'}).appendTo(tr);
					$("<button>&gt;&gt;</button>").attr({id:'left2Right','class':btnClass}).appendTo(td).click(function(){
						list_save.push(list_source.selected);
					});
				}
				//保存区
				var td = $("<td></td>").attr({valign:'top'}).appendTo(tr);
				var dv = $("<div></div>").attr({style:'background:#fff;border:2px inset;height:350px;overflow:auto;'});
				dv.append(d_lsave);
				td.append(dv);
				//按钮
				$("<button>保存</button>").attr({'class':btnClass,id:'btn_saved'}).appendTo(td).click(function(){list_save.submit(saveDataLink, fragment.extraProperty);});
				$("<button>编辑</button>").attr({'class':btnClass,id:'btn_edit'}).appendTo(td).click(function(){list_save.ondblclick(list_save);});
				$("<button>Del</button>").attr({'class':btnClass,id:'btn_del'}).appendTo(td).click(function(){list_save.del();});
				$("<button>置顶</button>").attr({'class':btnClass,id:'btn_top'}).appendTo(td).click(function(){list_save.top();});
				$("<button>&uarr;</button>").attr({'class':btnClass,id:'btn_up'}).appendTo(td).click(function(){list_save.up();}).dblclick(function(){list_save.up();});
				$("<button>&darr;</button>").attr({'class':btnClass,id:'btn_down'}).appendTo(td).click(function(){list_save.down();}).dblclick(function(){list_save.down();});
				$("<button>清空</button>").attr({'class':btnClass,id:'btn_clean'}).appendTo(td).click(function(){list_save.clear();});
				$("<button>获取已保存数据</button>").attr({'class':btnClass,id:'btn_get_saved'}).appendTo(td).click(function(){dom.get_saved(savedDataLink);});
			}
		}
	}
	/*
	 * 初始化被选数据筛选面板
	 * @param dom JQuery Object,绑定按钮事件时会用到
	 * @param ds string,数据源
	 */
	$.fn.ds_list = function(){
		var ds = arr.dataSource;
		var search_div = $("<div></div>").attr({'id':'ds_search',style:'width:100%;'});
		//SELECTOR
		var select_pt = $("<select></select>").attr({id:'pt'});
		//KEY
		var key_input = $("<input />").attr({type:'text',id:'key',size:'20'});
		if(ds == 'post'){//POST
			search_div.append(select_pt);
			search_div.append(key_input);
			//SELECTOR ITEM
			$("<option></option>").attr('value', '').appendTo(select_pt);
			$("<option>签到</option>").attr('value', '1').appendTo(select_pt);
			$("<option>点评</option>").attr('value', '2').appendTo(select_pt);
			$("<option>图片</option>").attr('value', '3').appendTo(select_pt);
		}else if(ds == 'user'){//用户
			search_div.append(select_pt);
			search_div.append(key_input);	
			//SELECTOR ITEM
			$("<option></option>").attr('value', '').appendTo(select_pt);
			$("<option>用户名</option>").attr('value', '1').appendTo(select_pt);
			$("<option>昵称</option>").attr('value', '2').appendTo(select_pt);
			$("<option>签名</option>").attr('value', '3').appendTo(select_pt);		
		}else if(ds == 'place'){//地点
			search_div.append(select_pt);
			search_div.append(key_input);
			//SELECTOR ITEM
			$("<option selected>地点名</option>").attr('value', '1').appendTo(select_pt);
			$("<option>地址</option>").attr('value', '2').appendTo(select_pt);
			$("<option>品牌商家</option>").attr('value', '3').appendTo(select_pt);
			$("<option>积分商户</option>").attr('value', '4').appendTo(select_pt);
		}else if(ds == 'event'){//活动
			search_div.append(key_input);
		}else if(ds == 'news'){//CMS
			search_div.append(key_input);
		}else if(ds == 'groupon'){//团购
			search_div.append(select_pt);
			search_div.append(key_input);
			//SELECTOR ITEM
			$("<option></option>").attr('value', '').appendTo(select_pt);
			$("<option>章鱼团</option>").attr('value', '1').appendTo(select_pt);
			$("<option>买购</option>").attr('value', '2').appendTo(select_pt);
		}else if(ds == 'placecoll'){//地点册
			search_div.append(key_input);
		}
		//按钮
		var btn_search = $("<button>获取数据</button>").attr({'class':'btn_silver'}).appendTo(search_div).click(function(){
			var pt = select_pt.val();
			var key = key_input.val();
			dom.searchData(pt, key);
		});
		return search_div;
	}
	/*
	 * 获取备选数据列表
	 */
	$.fn.searchData = function(pt, key){
		var ds = arr.dataSource;
		var url = '';
		if(ds.indexOf('http') == 0){//自定义的数据源，直接访问数据源获取数据
			url = '/web/new_recommend/alternatives/link/'+encodeURIComponent(ds);
		}else{
			url = '/web/new_recommend/get_web_data/type/'+ds+'/fid/'+arr.fid;
			if(typeof pt != 'undefined' && pt != '' && pt != null) url += '/pt/'+pt;
			if(typeof key != 'undefined' && key != '' && key != null) url += '/key/'+encodeURIComponent(key);
		}
		url += '/';
		$.get(url, function(json){
			try{
				list_source.clear();
				json = eval('('+json+')');
				if(json.length){
					for(var i=0;i<json.length;i++){
						var row = json[i];
						//向备选列表添加数据
						list_source.push({
							id:row['id'],
							title:row['title'],
							title_link:row['title_link'],
							intro:row['intro'],
							image:row['image'],
							image_link:row['image_link'],
							image_url:row['image_url'],
							category:'',
							category_link:'',
							author:'',
							author_link:'',
							authro_avatar:''
						});
					}
				}else{
					alertMsg.warn('没有数据');
				}
			}catch(e){
				alertMsg.error(e);
			}
		});
	}
	/*
	 * 获取已保存列表
	 */
	$.fn.get_saved = function(url){
		$.get(url, function(json){
			list_save.clear();
			json = eval('('+json+')');
			if(json.length){
				for(var i=0;i<json.length;i++){
					var item;
					var row = json[i];
					//基础属性
					item = {
						id:row.id,
						title:row.title,
						intro:row.intro,
						title_link:row.title_link,
						image:row.image,
						image_link:row.image_link,
						image_url:row.image_url
					};
					//扩展属性
					if(typeof row.extra != 'undefined' && row.extra != ''){
						for(var k in row['extra']){
							item[k] = row['extra'][k];
						}
					}
					//在列表显示 
					if(typeof item != 'undefined'){
						list_save.push(item, false, arr.extraProperty);
					}
				}
			}
		});
	}
})(jQuery);