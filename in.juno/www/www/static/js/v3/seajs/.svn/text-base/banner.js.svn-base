/*
	middle和small个数必为偶数
*/
define(function(require,exports,module){
	//var $ = require('jquery');
	exports.slide = function(selector,data){
		
		var ele = $(selector),
			box = ele.find('.content'),
			prev = ele.find('.left'),
			next = ele.find('.right'),
			//large = box.find('.large'),
			//middles = box.find('.middle'),
			//smalls = box.find('.small'),
			//longs = box.find('.long'),
			//loops = box.find('.loop'),
			maxWidth = 0;
			//smallPage = smalls.length / 2
		var rangeArr = ['large','middle','small','small','middle','long'];
		var imgArr = [];
		var imgList = '';
		var j = 0;
		var n = 0;
		while(data.length){
			if(n>5)n=0;
			if(data[j] == undefined)break;
			if(data[j].type.indexOf(rangeArr[n])!= -1){
				imgArr.push(data.splice(j,1)[0]);
				
				n++;
				j=0;
			}
			else{
				j++;
			}
		}
		
		for(var i=0;i<imgArr.length;i++){
			var n = i%6;
			var item = imgArr[i]
			var praise = item.praiseCount;
			var imgType = rangeArr[n];
			if(praise>0)
			{
				imgList += '<div class="box '+imgType+'"><a href="'+item.link+'" target="_blank"><img alt="" src="'+item.image+'"></a><div class="item">'+item.title+'</div><div class="rate"><i></i>'+praise+'</div></div>';
			}
			else{
				imgList += '<div class="box '+imgType+'"><a href="'+item.link+'" target="_blank"><img alt="" src="'+item.image+'"></a><div class="item">'+item.title+'</div></div>';
			}
			if(n==5){
				$(imgList).wrapAll("<div class=\"loop\"></div>").parent().appendTo(box);
				
				imgList ='';
				
			}
		}
		var loops = box.find('.loop');
		//非规则布局
		box.width(loops.outerWidth(true)*loops.length);
		/*
		//规则排列
		large.parent().width(large.length * large.outerWidth(true));
		middles.parent().width((middles.length/2)*middles.outerWidth(true));
		
		
		if(smallPage>1){
			if(smallPage % 2 != 0){
				smalls.slice(0,-2).wrapAll("<div class='smalls'></div>").parent().width((smallPage -1) * smalls.outerWidth(true));
			}
			else{
				smalls.wrapAll("<div class='smalls'></div>").parent().width((smallPage) * smalls.outerWidth(true));
			}
		}
	
		box.find('.others').width(Math.ceil((smalls.length/2 +longs.length)/2)*longs.outerWidth(true));
		box.children().each(function(i,n){
			maxWidth += $(n).outerWidth(true);
		})
		box.width(maxWidth);*/
		var i = 1;
		var pageArr = [644,726];
		var max = box.find('.loop').length*2;
		var pageDis = 0;
		next.on('click',function(e){
			box.stop();
			i++;
			if(i>max){
				i = 1;
				pageDis = 0
			}
			else{
				pageDis+=pageArr[i%2];
			}
			e.preventDefault();
			box.animate({marginLeft:-pageDis});
		});
		prev.on('click',function(e){
			box.stop();
			if(i<=1){
				i = max+1;
				pageDis = box.width()-726;
			}
			else{
				pageDis-=pageArr[i%2];
			}
			e.preventDefault();
			box.animate({marginLeft:-pageDis});
			i--;
		});
		var timer;
		timer = setInterval(function(){
			next.trigger('click');
		},5000);
		ele.hover(function(){
			if(timer)clearInterval(timer);
		},function(){
			if(timer)clearInterval(timer);
			timer = setInterval(function(){
				next.trigger('click');
			},5000)
		})
	}
});