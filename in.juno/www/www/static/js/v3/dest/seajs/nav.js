define("seajs/nav",[],function(e,i){var n=$(".nav"),t=n.find(".site_nav i"),a=n.find(".active");t.css({width:a.width(),backgroundColor:n.css("border-bottom-color")});var o={baby:"#fc7791",digital:"#30a5de",edu:"#99cef1",f:"#96292b",auto:"#96292b",fb:"#ff7104",home:"#9c7f73",mes:"#265174",movie:"#fd5502",tour:"#a6c526"};i.hover=function(){window.theme&&(t.css("background-color",o[theme]),n.css("border-color",o[theme])),n.find("a").on("mouseenter",function(){$(this).addClass("active").siblings().removeClass("active"),t.stop(),t.animate({left:this.offsetLeft,width:this.clientWidth},200)})}});