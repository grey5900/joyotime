$(function(){
	$('.detail .side .module').each(function(){
		var $this = $(this),
			$hd = $this.find('.hd');
		if($hd.length){
			var tit = $hd.text();
			$hd.remove();
			
			$this.prepend($('<div class="tit"><h3>'+tit+'<i></i></h3></div>'));
		};
		if($this.hasClass('old_module')){
			$this.removeClass('old_module');
			$this.find('.tit h3').append("<i></i>");
		};
	});
});