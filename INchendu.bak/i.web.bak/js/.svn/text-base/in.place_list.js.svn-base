function search(){
	var url         = $("#search_form").attr("action"),
		f_cid       = $("#cid").val(),
		s_cid       = $("#s_cid").val(),
		star        = $("input[name=star]").val(),
		pcc         = $("#pcc").val(),
		order       = $("#order").val(),
		keyword     = $("#keyword").val(),
		keyword_str = '',
		group       = 0,
		event       = 0,
		prefer      = 0;
	//构造url
	if(f_cid == "") f_cid="0";
	if(s_cid=="") s_cid="0";
	if(star=="") star="0";
	if(pcc=="") pcc = "F";
	if(order=="") order="default";
	if( keyword != "" && keyword != "输入您想找的地点名，如麦当劳" ) keyword_str=keyword+"/";
	
	url += f_cid+"/"+s_cid+"/"+star+"/"+pcc+"/"+group+"/"+event+"/"+prefer+"/"+order+"/"+keyword_str;
	window.location.href=url;
}
function addOptions(obj, type) {
    var checkValue = obj.innerHTML;
    var screenFilters = $(obj).parent().parent().attr("id");
    var data = null;
    if(type == "cid"){
        data = $(obj).attr("data-cid");
        $("#cid").val(data);
        $("#s_cid").val('0');
        //检查子分类
        show_son($(obj).attr("data-cid"));
    }else if(type == "s_cid"){
        $("#cid").val('0');
        data = $(obj).attr("data-cid");
        $("#s_cid").val(data);
    }else if(type == "star"){
        data = $(obj).attr("data-star");
        $("#order").val('star');
        $("#star").val($(obj).attr("data-star"));
    }else if(type == "pcc"){
        data = $(obj).attr("data-pcc");
        $("#order").val('pcc');
        $("#pcc").val($(obj).attr("data-pcc"));
    }else if(type == "order"){
        data = $(obj).attr("data-order");
        $("#order").val($(obj).attr("data-order"));
    }
    //$(".screenBox").append("<li><div class='filter-tag'><a href='javascript:void(0);' data='"+data+"'  class='highlight' onclick='delOptions(this,\"" + screenFilters + "\",\""+type+"\")'><em>" + checkValue + "</em><span class='close'>×</span></a></div></li>");
    //$(obj).hide();
    search();
    return false;
}

function delOptions(obj, type) {
    //var checkValue = obj.innerHTML;
    var data = $(obj).attr("data");
    var del = $("<li></li>");
    var delA = $("<a onclick='addOptions(this, \'"+type+"\');'></a>");
    delA.attr("data-"+type, data);
    delA.append($("<em></em>").html($(obj).find("em").html()));
    del.append(delA);
    //搜索条件还原
    if(type == 'cid'){
        $("#cid").val('');
    }else if(type == 's_cid'){
    	$("#s_cid").val('');
    }else if(type == 'star'){
        $("input[name=star]").val('');
    }else if(type == 'pcc'){
        $("#pcc").val("F");
        $("#order").val('default');
    }else if(type == 'order'){
        $("#order").val('default');
    }else if(type == 'keyword'){
        $("#keyword").val('');
    }
    //
    search();
    return false;
}
function show_son(cid){
    $.ajax({
        type:"get",
        url:"/list_son/"+cid+"/",
        dataType:"json",
        success:function(data){
            if(data != null && data != ""){
                //有子分类
                var $cate = $('<ul clsss="second"></ul>');
                $.each(data, function(i, item){
                    var li = $("<li id=\"cate-" + item.id + "\"></li>");
                    var a = $("<a onclick='addOptions(this,\"s_cid\");' data-cid='"+ item.id +"'>"+ item.content +"</a>");
                    li.append(a);
                    $cate.append(li);
                });
                $("#cate-"+cid).append($cate);
            }
        }
    });
}
    
// function more(){
    // alert("2");
    // if($("#"+this.parentNode.className).css("height")=="20px"){
        // $("#"+this.parentNode.className).css("height","auto");
    // }
    // else{
        // $("#"+this.parentNode.className).animate({height:"20px"},"slow");
    // }
// }

$(function(){
	$('#star_selector').raty({
		score   : function() {
			return $(this).attr('data-rating');
		},
		scoreName: 'star',
		hints   : ['一星', '两星', '三星', '四星', '五星'],
		size    : 36,
		space   : false,
		starOn  : 'star-large-on.png',
		starOff : 'star-large-off.png',
		click   : function(rating, evt){
			search();
		}
	});

	$('#add-poi .btn').on('click',function(){
		if( !$.is_signin()){
			return false;
		}else{
			return true;
		}
	});

});

