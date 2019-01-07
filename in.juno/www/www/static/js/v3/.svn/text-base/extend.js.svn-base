//$(function(){
	$.fn.slide = function(option){
		var def = {
			trigger:'.trigger',
			single:'li',
			box:'ul',
			num:4,
			initDis:0
		};
		$.extend(def,option);
		var _this  = this,
			_trigger = _this.find(def.trigger),
			_single = _this.find(def.single),
			_box =_this.find(def.box),
			initDis = def.initDis,
			num = def.num,
			dis = _single.outerWidth(true),
			n = 0,
			max = _single.length,
			page = Math.ceil(max/num);
		_box.width(max*dis);		
		_trigger.on('click',function(e){			
			e.preventDefault();
			_box.stop();
			var _this = $(this);
			if(_this.hasClass('prev')){
				if(n==0){
					return false;
				}
				n--;
				_show(n);
			}
			else{
				n++;
				if(n>=page)n=0;				
				_show(n);
			}
		});
		var _show = function(i){			
			_box.animate({marginLeft:-i*dis*num+initDis});
		};
		return this;
	};
	$.fn.fixAd = function(option){
		var def = {
			link:'',
			src:'',
			type:'img',
			width:270,
			height:200
		};
		$.extend(def,option);
		var	$this = this,
			obj;			
		if(def.type=='img'){
			var link = document.createElement('a'),
				img = document.createElement('img');
			img.src = def.src;
			link.href = def.link;
			link.target = "_blank";
			link.appendChild(img);
			obj = link;
		}
		else{
			obj = document.createElement('embed');
			obj.src = def.src;
			obj.width = def.width;
			obj.height = def.height;
			obj.type = "application/x-shockwave-flash";
			obj.getAttribute('wmode','transparent');
		}
		var $obj = $('<div class="fixAd"><i class="icon-remove"></i></div>').css({
			position:'fixed',
			right:0,
			bottom:-(def.height+15),
			width:def.width,
			height:def.height
		}).append(obj).appendTo($this);
		$obj.find('.icon-remove').on('click',function(){
			$obj.remove();
		});
		setTimeout(function(){
			$obj.animate({bottom:0},1000);
		},3000);
		if($.browser.msie && $.browser.version == 6){
			ie6Fix($obj[0],$(window).innerHeight()-def.height);
		}
		return $this;
	};
	$.fn.changeImg = function(options){
		var def = {
				speed:3000,
				arr:[]
			};
		def = $.extend(def,options);
		var $this = this,
			timer,len,
			i=1,
			arr = def.arr;
			arr.unshift($this.attr('src'));
			len = arr.length;
		show = function(i){
			i = i >= len ? 0 : i;
			timer = setTimeout(function(){
				//$this.attr('src',arr[i]);
				$this.fadeOut(function(){
					$this.attr('src',arr[i]);
					$this.fadeIn(function(){
						i++;
						show(i);
					});
				});
			},def.speed);
		}
		if(len>i){
			show(i)
		}
		return this;
	};
	function ie6Fix(dom,extra){
		dom.style.position = 'absolute';
		var _height = extra || 0;
		window.attachEvent('onscroll',function(){
			var _top = document.body.scrollTop || document.documentElement.scrollTop;
			dom.style.top = (_top + _height)+'px';
		});
	};
//})