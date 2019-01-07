$(function() {
	$(".photo-box").each(function() {
		//alert("2");
		var self = $(this),delay;
		self.mouseover(function() {
			delay = setTimeout(function() {
				delay = null;
				self.find(".subtitle").stop().animate({
					"bottom": 0
				}, 100);
			}, 100);
		}).mouseout(function() {
			if (delay) {
				clearTimeout(delay);
			} else {
				setTimeout(function() {
					self.find(".subtitle").stop().animate({
						"bottom": -30
					}, 100);
				}, 100);
			}
		});
	});

//	$('.app-feed').scrollLine();

	var scrtime;
 	$("#app-feed").hover(function(){
		clearInterval(scrtime);
	
	},function(){
	
		scrtime = setInterval(function(){
			var $ul = $("#app-feed ul");
			var liHeight = $ul.find("li:last").outerHeight();
			$ul.animate(
				{ marginTop : liHeight +"px" },
				1000,
				function(){
					$ul.find("li:last").prependTo($ul)
					$ul.find("li:first").hide();
					$ul.css({marginTop:0});
					$ul.find("li:first").fadeIn(1000);
				});	
		},6000);
	
	}).trigger("mouseleave");


	$(".hot-list").each(function(){
		var $this = $(this),
			$li = $this.find('li');
		$li.first().addClass('active');
		$li.each(function(){
			$(this).on('hover',function(){
				if( ! $(this).hasClass('active')) {
					$li.removeClass('active');
					$(this).addClass('active');
				}
			})
		})
	});
	/*
	 幻灯
	 */
	$('#a1-slide').load($('#a1-slide').attr("data-url"),function(html){
		var $this = $(this);
		$this
			.html(html)
			.slider({
			loop: true,
			effect: "no",
			pagination: true,
			autoSlide: true,
			nextBtn: '',
			prevBtn: ''
		});
	})

	$('.talent-main').each(function(){
		$(this).slider({
			loop: true,
			dispItems: 7
		});
	})
	$('.item').each(function(){
		var $this = $(this),
			$li = $this.find('li');
		$li.last().css('border-bottom','0');
	})
});