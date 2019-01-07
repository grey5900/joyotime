!function ($) {
	var loading = false;
	  $(function(){
	    /*
	     *头部定位
	     */
	    var $nav = $('#fixed-header')
	      , navTop = $('#fixed-header').length && $('#fixed-header').offset().top
	      , isFixed = 0;
	    $(window).on('scroll', function(){
	      var i, scrollTop = $(window).scrollTop();
	      if (scrollTop >= navTop && !isFixed) {
	        isFixed = 1;
	        $nav.addClass('navbar-fixed-top');
	      } else if (scrollTop <= navTop && isFixed) {
	        isFixed = 0;
	        $nav.removeClass('navbar-fixed-top');
	      }
	    });
	  });
	  $.fn.extend({
	  	messager: function(options) {
            var $el = $(this);
            //add dom
            if (!$el[0]) {
                var $el = $("<div id=\"messager\"></div>");
                $("body").append($el);
            }
            $el.html(options.message);
            var sTop = $(document).scrollTop(),
	            sHeight = $(window).height(),
	            sWidth = $(window).width(),
	            elHeight = $el.outerHeight(),
	            elWidth = $el.outerWidth(),
	            elTop = (sHeight - elHeight) / 2 + sTop,
	            elLeft = (sWidth - elWidth) / 2;
            $el.css({
                top: elTop + 10,
                left: elLeft,
                opacity: '0'
            })
            .animate({
                opacity: '1',
                top: elTop,
                queue: true
            }, 200)
            .delay(options.delay ? options.delay: '3000')
            .animate({
                opacity: '0',
                top: elTop + 10,
                queue: true
            }, 200,
            function() {
            	$el.remove();
            	if (options.refer) {
            		if (options.refer == 'reload') {
	            		window.location.reload();
            		} else {
            			window.location.href = options.refer;
            		}
            	}
            });
        }
	  });
}(window.jQuery)

function preview(img, selection) {
	if (!selection.width || !selection.height)
		return;
	var cur_w = $("#photo").width;
	var cur_h = $("#photo").height;
	var scaleX = 190 / selection.width;
	var scaleY = 138.8 / selection.height;
	$('#preview img').css({
		width: Math.round(scaleX * 360 )+ 'px',	
  	height: Math.round(scaleY * 226.8 )+ 'px',
		marginLeft : '-' + Math.round(scaleX * selection.x1) + 'px',
		marginTop : '-' + Math.round(scaleY * selection.y1) + 'px'
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
}