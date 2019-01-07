document.write("<div id='ie6'>您正在使用 <font color='red'>Internet Explorer 6</font>，极大的影响你的浏览器安全，并且对体验有较大的影响，建议您升级到 <a href=\"http:\/\/www.microsoft.com\/china\/windows\/internet-explorer\/\" target=\"_blank\">Internet Explorer 8<\/a> 或以下浏览器： <a href=\"http:\/\/www.mozillaonline.com\/\">Firefox<\/a> \/ <a href=\"http:\/\/www.google.com\/chrome\/?hl=zh-CN\">Chrome<\/a> \/ <a href=\"http:\/\/www.apple.com.cn\/safari\/\">Safari<\/a> \/ <a href=\"http:\/\/www.operachina.com\/\">Opera<\/a><a id=\"ie6Close\" href=\"###\">关闭提示</a></div>");
var oStyle = document.createElement('style');
oStyle.type = 'text/css';
if('styleSheet' in oStyle){
	oStyle.styleSheet.cssText = "#ie6 a{color:blue}#ie6{line-height:20px;font-size:12px;background:yellow;position:absolute;top:0;left:0;width:100%;z-index:99999;text-align:center}a#ie6Close{color:red;margin-left:10px;}";
}
document.body.appendChild(oStyle);
var oScroll = document.getElementById('ie6');
document.getElementById('ie6Close').onclick = function(){
	var e = window.event;
	e.returnValue = false;
	document.getElementById('ie6').style.display = 'none';
};
window.attachEvent('onscroll',function(){
	var _top = document.body.scrollTop || document.documentElement.scrollTop;
	oScroll.style.top = _top + 'px';
});
