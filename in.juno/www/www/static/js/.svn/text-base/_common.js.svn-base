//默认文字
$('input.txt').each(function(){
	var v=$(this).val();
	$(this).focus(function(){
		if($(this).val()==v){
			$(this).val('');
			$(this).css({color:'#666'});
		}
	});	
	$(this).blur(function(){
		if($(this).val()==''){
			$(this).val(v);
			$(this).css({color:'#aaa'});
		}
	});
});


//评分
$('.pf').each(function(){
	$(this).find('i').width($(this).attr('pf')*8); 
});

//幻灯
function index_show(id,time){
	var index = 0;
	 lis = $('#'+id).find('.focus_tit li').length;
	 btn_width = $('#'+id).find(".focus_Numid").width();
	 imgnav_width = $('#'+id).find(".focus").width();
	 msg_width = imgnav_width;
	 $('#'+id).find(".focus_tit").css("width" , msg_width);
	 $('#'+id).find(".focus_Numid span").mouseover(function(){
		index  =  $('#'+id).find(".focus_Numid span").index(this);
		showImg(index,id);
	});
	//自动开始
	var MyTime = setInterval(function(){
		showImg(index,id)
		index++;
		if(index==lis){index=0;}
	} , time);

	//滑入 停止动画，滑出开始动画.
	$('.focus').hover(function(){
		if(MyTime){
			clearInterval(MyTime);
		}
	},function(){
		MyTime = setInterval(function(){
			showImg(index,id)
			index++;
			if(index==lis){index=0;}
		} ,time);
	});
}
//通过控制i ，来显示不同的幻灯片
function showImg(i,id){
	$('#'+id).find(".focus_pic img")
	.eq(i).stop(true,true).show()
	.parent().siblings().find("img").hide();
	$('#'+id).find(".focus_tit li")
	.eq(i).stop(true,true).show()
	.siblings().hide();
	 $('#'+id).find(".focus_Numid span")
	.eq(i).addClass("hov")
	.siblings().removeClass("hov");
}
 //滚动
function scrollLeft(obj,btn,time){
	obj.children().wrapAll('<div style="width:9000px;"></div>');
	obj=obj.children();
	btn.find('.next').click(function(){
		obj.animate({marginLeft:-obj.children(':first').width()},time,function(){
			obj.css({marginLeft:0}).children(':first').appendTo(obj);
		});
	});
	btn.find('.prev').click(function(){
		obj.children(":last").prependTo(obj);
		obj.css({marginLeft:-obj.children(":last").width()+"px"});
		obj.animate({marginLeft:0},time);		
	});
}
