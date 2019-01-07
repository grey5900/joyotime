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
/************************************
 *
 * 备选列表类, 用于获取数据及添加到保存列表类中
 *
 ************************************/
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
        alert(msg);
    }
    // 列表中被选中的列表都存储在这个数组里面
    this.selected = new Array();
    /****************************
     * 下面的函数必须先绑定后才能使用
     ****************************/
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
    this._push  = function(listitem, isreplace){
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
            itemobj.attr("category", listitem.category);
            itemobj.attr("category_link", listitem.category_link);
            itemobj.attr("author", listitem.author);
            itemobj.attr("author_link", listitem.author_link);
            itemobj.attr("author_avatar", listitem.author_avatar);
            itemobj.attr("intro",listitem.intro);
            itemobj.attr("start_time", listitem.start_time);
            itemobj.attr("end_time", listitem.end_time);
            // 二外两个保存图片的绝对地址
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
                    // editor.selected.push({tid:thisobj.attr("tid"), title:thisobj.attr("title"), link:thisobj.attr("link"), imgs:thisobj.attr("imgs"), desc:thisobj.attr("desc"), cat:thisobj.attr("cat"), cat_link:thisobj.attr("cat_link"), img_link:thisobj.attr("img_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),subtitle:thisobj.attr("subtitle"),sublink:thisobj.attr("sublink")});
                }else{
                    thisobj.siblings().removeClass("selected");
                    thisobj.addClass("selected");
                    // 没有按住Shift键, 将选中的列表清空, 然后再将当前选择项目加入到选中列表中
                    editor.selected = new Array();
                    // editor.selected.push({tid:thisobj.attr("tid"), title:thisobj.attr("title"), link:thisobj.attr("link"), imgs:thisobj.attr("imgs"), desc:thisobj.attr("desc"), cat:thisobj.attr("cat"), cat_link:thisobj.attr("cat_link"), img_link:thisobj.attr("img_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),subtitle:thisobj.attr("subtitle"),sublink:thisobj.attr("sublink")});
                }
                editor.selected.push({id:thisobj.attr("id"), title:thisobj.attr("title"), title_link:thisobj.attr("title_link"), category:thisobj.attr("category"), category_link:thisobj.attr("category_link"), image:thisobj.attr("image"), image_link:thisobj.attr("image_link"), author:thisobj.attr("author"), author_link:thisobj.attr("author_link"),author_avatar:thisobj.attr("author_avatar"),intro:thisobj.attr("intro"),image_url:thisobj.attr("image_url"),author_avatar_url:thisobj.attr("author_avatar_url"),start_time:thisobj.attr("start_time"),end_time:thisobj.attr("end_time")});
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
    this.push = function(listitem, isreplace){
        // 传递的参数是数组
        if(listitem instanceof Array){
            for(var n=0; n<listitem.length; n++){
                this._push(listitem[n], isreplace);
            }
        }else{
            this._push(listitem, isreplace);
        }
    }
    this.submit = function(submit_url){
        var id        = "";
        var title      = "";
        var title_link    = "";
        var category     = "";
        var category_link = "";
        var image  = "";
        var image_link     = "";
        var author      = "";
        var author_link     = "";
        var author_avatar    = "";
        var intro = "";
        var start_time = "";
        var end_time = "";
        if(this.editor.find("li").length <=0){
            if(window.confirm("请选择您要保存的列表, 如果真的要提交请确定！")) {}else{return;}
        }
        this.editor.find("li").each(function(){
            id      += (typeof $(this).attr("id") == "undefined" ? "" : $(this).attr("id")) + "┆";
            title    += (typeof $(this).attr("title") == "undefined" ? "" : $(this).attr("title")) + "┆";
            title_link     += (typeof $(this).attr("title_link") == "undefined" ? "" : $(this).attr("title_link")) + "┆";
            category += (typeof $(this).attr("category") == "undefined" ? "" : $(this).attr("category")) + "┆"; 
            category_link += (typeof $(this).attr("category_link") == "undefined" ? "" : $(this).attr("category_link")) + "┆"; 
            image     += (typeof $(this).attr("image") == "undefined" ? "" : $(this).attr("image_url")) + "┆";
            image_link      += (typeof $(this).attr("image_link") == "undefined" ? "" : $(this).attr("image_link")) + "┆";
            author       += (typeof $(this).attr("author") == "undefined" ? "" : $(this).attr("author")) + "┆";
            author_link    += (typeof $(this).attr("author_link") == "undefined" ? "" : $(this).attr("author_link")) + "┆";
            author_avatar    += (typeof $(this).attr("author_avatar") == "undefined" ? "" : $(this).attr("author_avatar_url")) + "┆";
            intro        += (typeof $(this).attr("intro") == "undefined" ? "" : $(this).attr("intro")) + "┆";
            start_time        += (typeof $(this).attr("start_time") == "undefined" ? "" : $(this).attr("start_time")) + "┆";
            end_time        += (typeof $(this).attr("end_time") == "undefined" ? "" : $(this).attr("end_time")) + "┆";
        });
        id      = id.replace(/┆$/, "");
        title    = title.replace(/┆$/, "");
        title_link = title_link.replace(/┆$/, "");
        category  = category.replace(/┆$/, "");
        category_link     = category_link.replace(/┆$/, "");
        image     = image.replace(/┆$/, "");
        image_link      = image_link.replace(/┆$/, ""); 
        author       = author.replace(/┆$/, "");  
        author_link        = author_link.replace(/┆$/, "");   
        author_avatar        = author_avatar.replace(/┆$/, "");   
        intro        = intro.replace(/┆$/, "");   
        start_time        = start_time.replace(/┆$/, "");   
        end_time        = end_time.replace(/┆$/, "");   
        
        $.post(submit_url, {
            'id'      :encodeURIComponent(id),
            'title'     :encodeURIComponent(title),
            'title_link'      :encodeURIComponent(title_link),
            'category'  :encodeURIComponent(category),
            'category_link'   :encodeURIComponent(category_link),
            'image'      :encodeURIComponent(image),
            'image_link'    :encodeURIComponent(image_link),
            'author'       :encodeURIComponent(author),
            'author_link'  :encodeURIComponent(author_link),
            'author_avatar'  :encodeURIComponent(author_avatar),
            'intro'        :encodeURIComponent(intro),
            'start_time'        :encodeURIComponent(start_time),
            'end_time'        :encodeURIComponent(end_time)
        }, function(data) {
            alert(data);
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
function page_load(){
    // 将list_source类与HTML元素绑定
    list_source.bind($("#list_source"));
    // 设置list_source项目双击事件
    list_source.ondblclick = function(editor){
        list_save.push(list_source.selected);
    }
    // 将list_save类与HTML元素绑定
    list_save.bind($("#list_save"));
    list_save.ondblclick = function(editor){
        if(editor.selected.length > 0){
            var obj = editor.selected[0];
            // 先清空编辑器
            clear_editor();
            
            put_editor(obj);
            
            $('#ext_add').hide();
            $('#ext_edit').show();
            openEditor();
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

// 添加数据事件
function addData(){
    clear_editor();
    
    $("#ext_edit").hide();
    $("#ext_add").show();
    openEditor();
}

// 清空编辑器
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

// 设置数据到编辑框
function put_editor(obj) {
    $("#f_id").val(obj.id);
    $("#f_title").val(obj.title);
    $("#f_title_link").val(obj.title_link);
    $("#f_category").val(obj.category);
    $("#f_category_link").val(obj.category_link);
    $("#f_image").val(obj.image);
    $("#f_image_link").val(obj.image_link);
    $("#f_author").val(obj.author);
    $("#f_author_link").val(obj.author_link);
    $("#f_author_avatar").val(obj.author_avatar);
    $("#f_intro").val(obj.intro);
    $("#f_start_time").val(obj.start_time);
    $("#f_end_time").val(obj.end_time);
    
    // 设定两个图片
    var t = (new Date()).getTime();
    if(obj.image_url) {
        init_editor_image_widget(obj.image, obj.image_url, "f_image", 45);
        // $("a[rel='f_image']", $("#ooxx_image_span_id_f_image")).attr("href", obj.image_url + "?" + t);
        // $("img", $("#ooxx_image_span_id_f_image")).attr("src", obj.image_url + "?" + t);
        // $("#ooxx_image_span_id_f_image").show();
    }
    
    if(obj.author_avatar_url) {
        init_editor_image_widget(obj.author_avatar, obj.author_avatar_url, "f_author_avatar", 45);
        // $("a[rel='f_author_avatar']", $("#ooxx_image_span_id_f_author_avatar")).attr("href", obj.author_avatar_url + "?" + t);
        // $("img", $("#ooxx_image_span_id_f_author_avatar")).attr("src", obj.author_avatar_url + "?" + t);
        // $("#ooxx_image_span_id_f_author_avatar").show();
    }
}

// 控制按钮事件
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

function openEditor(){
    // var bodyobj = document.body;
    // var docobj  = document.documentElement;
    // 创建窗口
    var divobj  = document.getElementById("editor_wnd");
    divobj.style.display    = "";
    // 遮罩层
    var overlay = document.getElementById("editor_overlay");
    // overlay.style.height    = bodyobj.offsetHeight + "px";
    // 双击遮罩层隐藏
    overlay.ondblclick  = function(){
        closeEditor();
    }

    // 内容框架
    
    // 隐藏页面滚动条
    //document._documentOverFlow    = docobj.style.overflow;
    //docobj.style.overflow = "hidden";
    // 设置各对象位置
    positionEditor();
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

/**
 * 获取保存的数据
 */
function get_saved_data(url) {
    list_save.clear();
    $.getJSON(url, function(json) {
        if(json.length) {
            for(var i=0; i<json.length; i++) {
                var row = json[i];
                list_save.push({id:row['id'], title:row['title'], title_link:row['title_link'], category:row['category'], category_link:row['category_link'], author:row['author'], author_link:row['author_link'], author_avatar:row['author_avatar'], author_avatar_url:row['author_avatar_url'], intro:row['intro'], image:row['image'], image_link:row['image_link'], image_url:row['image_url'], start_time:row['start_time'], end_time:row['end_time']});         
            }
        } else {
            alert("没有保存的记录");
        }
    });
}

/**
 * 初始化。编辑框的图片
 * 
 * image_name 图片名称
 * image_url 图片绝对地址
 * image_id 图片ID
 * height 大小
 */
function init_editor_image_widget(image_name, image_url, image_id, height) {
    // 图片的span的ID号
    var image_span_id = "ooxx_image_span_id_" + image_id;
    // 图片的span、图片链接、图片、删除链接DIV、删除链接
    var image_span, image_a, image, delete_div, delete_a;
    
    var hidden = $("#" + image_id);
    
    image_span = $("#" + image_span_id);
    if(image_span.length == 0) {
        image_span = $("<div id=\"" + image_span_id + "\"></div>").css({
            position: "relative",
            width: "200px",
            "text-align": "center", 
            border: "1px solid #9cc67e",
            height: height + "px",
            overflow: "hidden"
        });
        image_a = $("<a rel=\"" + image_id + "\"></a>");
        image = $("<img height=\"" + height + "\" border=\"0\" />");
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
        $("#iframe_" + image_id).parent().append(image_span);
    } else {
        image_a = $("a[rel=" + image_id + "]", image_span);
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
    image_a.attr("href", image_url + "?" + (new Date().getTime()));
    image.attr("src", image_url + "?" + (new Date().getTime()));
    hidden.val(image_name);
    image_span.show();
}
$(window).scroll(positionEditor);
$(window).resize(positionEditor);