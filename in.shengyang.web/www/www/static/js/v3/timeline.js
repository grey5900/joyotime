//回复
$('.timeline').on('click','.action-reply',function(e){
	var $this = $(this);
	e.preventDefault();
	
	if($.checkAuth()){
		var $reply = $this.closest('.post').find('.replys');
			$reply.toggle();
	};
});
//timeline回复
$('.timeline').on('focusin','.reply-form',function(){
	var $this = $(this),
		$btn = $this.find('span');
	$btn.fadeIn();
});
$('.timeline').on('focusout','.reply-form',function(){
	var $this = $(this),
		$btn = $this.find('span');
	if($this.find('.txt').val()==''){
		$btn.fadeOut();
	}
});
$('.timeline').on('click','.reply-form .btn',function(){
	var $this = $(this),
		$txt = $this.prev();
	if($this.hasClass('posting')){
		return false;
	}
	if($txt.val() == ''){
		$.messager('请填写回复内容');
	}
	else{
		$this.parent('form').trigger('submit');
	}
});
//回复的回复
$('.timeline').on('click','.action-replyto',function(){
	if(!$.checkAuth()) { return false; }
	var $this = $(this),
		$replyForm = $this.closest('.reply-list').prev('.reply-form'),
		$textBox = $replyForm.find('input').length ? $replyForm.find('input') : $replyForm.find('textarea.txt');
	$replyForm.data('id', $this.data('id'));
	$replyForm.data('uid', $this.data('uid'));
	$replyForm.data('type', 'reply');
	$replyForm.data('user', $this.data('user'));
	$textBox
		.attr('placeholder','回复 ' + $this.data('user'))
		.focus();
	
});
//详情
$('.timeline').on('mouseenter','.post',function(){
	$(this).find('.link').fadeIn(200);
});
$('.timeline').on('mouseleave','.post',function(){
	$(this).find('.link').fadeOut(200);
});