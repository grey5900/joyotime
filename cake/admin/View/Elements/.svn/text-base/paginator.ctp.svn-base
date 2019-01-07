<?php 
// $this->Paginator->options(array(
// 	'update' => 'tbody',
// 	'evalScripts' => true,
// ));

if($this->Paginator->hasPage(2)) {
	echo '<div class="pager"><ul>';
	
	echo $this->Paginator->first('<i class="icon-pager-left"></i>', array(
		'tag' => 'li',
		'escape' => false,
// 		'disabledTag' => 'a'
		), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	echo $this->Paginator->numbers(array(
		'tag' => 'li',
		'separator' => NULL,
		'currentClass' => 'activing',
		'currentTag' => 'a',
		'escape' => false
	));  
	echo '<li><input id="pageto" type=#33608c" value=""/></li>';
	echo $this->Paginator->last('<i class="icon-pager-right"></i>', array(
			'tag' => 'li',
			'escape' => false,
// 			'disabledTag' => 'a'
	), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	
	echo '</ul></div>';
}
?>

<script>
    $(function(){
        $('#pageto').bind('keypress',function(event){
            if(event.keyCode == "13")
            {
                var _value=$(this).val();
                if((/^(\+|-)?\d+$/.test( _value )) && _value>0)
                {
                    var _url= $('.pager > ul >li:eq(0) > a').attr('href') + '&page=' + $('#pageto').val();
                    window.location.href=_url;
                }
            }
        });
    });
</script>