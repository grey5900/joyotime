define("seajs/banner",[],function(i,e){e.slide=function(i,e){for(var n=$(i),a=n.find(".content"),t=n.find(".left"),l=n.find(".right"),r=["large","middle","small","small","middle","long"],o=[],s="",c=0,v=0;e.length&&(v>5&&(v=0),void 0!=e[c]);)-1!=e[c].type.indexOf(r[v])?(o.push(e.splice(c,1)[0]),v++,c=0):c++;for(var d=0;o.length>d;d++){var v=d%6,f=o[d],m=f.praiseCount,g=r[v];s+=m>0?'<div class="box '+g+'"><a href="'+f.link+'"><img alt="" src="'+f.image+'"></a><div class="item">'+f.title+'</div><div class="rate"><i></i>'+m+"</div></div>":'<div class="box '+g+'"><a href="'+f.link+'"><img alt="" src="'+f.image+'"></a><div class="item">'+f.title+"</div></div>",5==v&&($(s).wrapAll('<div class="loop"></div>').parent().appendTo(a),s="")}var p=a.find(".loop");a.width(p.outerWidth(!0)*p.length);var d=1,u=[644,726],h=2*a.find(".loop").length,b=0;l.on("click",function(i){a.stop(),d++,d>h?(d=1,b=0):b+=u[d%2],i.preventDefault(),a.animate({marginLeft:-b})}),t.on("click",function(i){a.stop(),console.log(d),1>=d?(d=1,b=0):b-=u[d%2],i.preventDefault(),a.animate({marginLeft:-b}),d--});var k;k=setInterval(function(){l.trigger("click")},5e3),n.hover(function(){k&&clearInterval(k)},function(){k&&clearInterval(k),k=setInterval(function(){l.trigger("click")},5e3)})}});