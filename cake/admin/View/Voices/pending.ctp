<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_PENDING); ?>"><?php echo __('Pending') ?></a>
                    </div>
                </li>
                <li class="">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_INVALID); ?>"><?php echo __('Invalid') ?></a>
                    </div>
                </li>
            </ul>
        </div>
        
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('Voice') => array('width' => '30%')),
					array(__('音频来源') => array('width' => '')),
                    array(__('Language') => array('width' => '')),
                    array(__('Length') => array('width' => '')),
                    array(__('Author') => array('width' => '')),
                    array(__('Created') => array('width' => '')),
					array(__('标签个数')=> array()),
                    array(__('Action') => array('width' => '')),
					
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Voice->init($row); ?>
                <tr class="">
                    <td>
                        <div class="intro">
                            <div class="pull-left">
                                <a data-href="<?php echo $this->Voice->cover('source'); ?>" href="#modalLightbox" class="thumbnail" data-toggle="modal" data-lightbox>
                                    <img src="<?php echo $this->Voice->cover(); ?>" alt="" />
                                </a>
                            </div>
                            <div class="details">
                                <p><?php echo $this->Voice->title()?></p>
                                <a class="icon-play" data-play="modal" data-player-id="player-<?php echo $idx; ?>" data-voice="<?php echo $this->Voice->address() ?>" href="#modalPlyer"></a>
                                <?php $point = $this->Voice->point(); ?>
                                <a class="icon-map"  data-map="modal" data-lat="<?php echo $point->latitude(); ?>" data-lng="<?php echo $point->longitude(); ?>" href="#modalMap"></a> 
                                <div class="block-left"><?php echo $this->Voice->isfree(); ?></div>
                            </div>                            
                        </div>
                    </td>
                    <td><?php echo $this->Voice->createForm(); ?></td>
                    <td><?php echo $this->Voice->language(); ?></td>
                    <td><?php echo $this->Voice->length(); ?></td>
                    <td><?php echo $this->Voice->author('',0) ?><br /><?php echo $this->Voice->rolename() ?></td>
                    <td><?php echo $this->Voice->approvedDate(); ?></td>
                    <td><?php echo $this->Voice->tagTotal(0) ?></td>
                    <td><?php echo $this->Voice->invalid(); ?> - <?php echo $this->Voice->approved(__('Approve')); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>
<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>
<?php echo $this->element('/modals/invalid'); ?>
<?php echo $this->element('/modals/lightbox'); ?>
<?php echo $this->element('/modals/voice_score'); ?>
<?php echo $this->element('/modals/common_tip'); ?>
<script type="text/javascript">
<!--
$(function(){
	// submit comment as reason for why invalid that one...
	var data_url = '';
	var linkRemove = '';
	// save global data when click invalid link...
	$('a.invalid-link').click(function(){
	    linkRemove = $(this);
	    data_url = $(this).attr('data-url');
	});
	$('a.score-tip-link').click(function(){
	    scoreLinkRemove = $(this);
	    score_data_url = $(this).attr('data-url');
	});
	
	// handle click submit comment button in modal...
	$('#invalidmodal button.btn-primary').click(function(){
	    var parent = $(this).parents('.modal-dialog');
	    var textarea = $('textarea[name="comment"]', parent);
	    var comment = textarea.val();
	    var btn = $('#invalidmodal button.btn-primary');
	    $.getJSON(data_url+'?comment='+comment, function(data) {
	    	if(data.result) {
            	linkRemove.closest("tr").remove();
            	textarea.val('');
            	$('#invalidmodal').modal('hide');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('驳回')?>').attr('disabled',false);
    	});
	})
	//评分上架
	 $('#score-hide-modal button.btn-primary').click(function(){
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="message"]', parent);
        var score = $('#score').val();
        var btn = $('#score-hide-modal button.btn-primary');
        if(score==0) {
        	$.messager('<?php echo __('请先评分') ?>');
        	return ;
        }
        btn.text('<?php echo __('正在处理...') ?>').attr('disabled',true);
        $.getJSON(score_data_url+'?score='+score, function(data) {
            // if success...
            if(data.result) {
            	$('.modal').modal('hide');
            	scoreLinkRemove.closest("tr").remove();
            } 
            $.messager(data.message);
          	$('#score-info img').attr('src','/img/icon_star_1.png');
          	$('#score').val(0);
          	btn.text('<?php echo __('上架')?>').attr('disabled',false);
        });
    })
    //有评分直接上架
	 $('#common-hide-modal button.btn-primary').click(function(){
      var btn = $('#common-hide-modal button.btn-primary');
      
      btn.text('<?php echo __('正在处理...') ?>').attr('disabled',true);
      $.getJSON(score_data_url, function(data) {
          // if success...
          if(data.result) {
          	$('.modal').modal('hide');
          	scoreLinkRemove.closest("tr").remove();
          } 
          $.messager(data.message);
        	btn.text('<?php echo __('确定')?>').attr('disabled',false);
      });
  })
	// handle click approved link...
	$('a.approved-link').click(function(){
	    var $this = $(this);
	    url = $(this).attr('data-url');
	    $.getJSON($(this).attr('data-url'), function(data){
	        $.messager(data.message);
	        $this.closest("tr").remove();
		});
	});
});
$('#common-modal-body').html('确定要上架该条鱼说吗？');
function rate(obj,oEvent){
	var imgSrc = '/img/icon_star_1.png';
	var imgSrc_2 = '/img/icon_star_2.png';
	if(obj.rateFlag) return;
	var e = oEvent || window.event;
	var target = e.target || e.srcElement;
	var imgArray = obj.getElementsByTagName("img");
	for(var i=0;i<imgArray.length;i++){
	   imgArray[i]._num = i;
	   imgArray[i].onclick=function(){
	    if(obj.rateFlag) return;
	    //alert(this._num+1);
	    $('#score').val(this._num+1);
	 	var inputid=this.parentNode.previousSibling
	 	inputid.value=this._num+1;
	   }
	}
	if(target.tagName=="IMG"){
	   for(var j=0;j<imgArray.length;j++){
	    if(j<=target._num){
	     	imgArray[j].src=imgSrc_2;
	    } else {
	     	imgArray[j].src=imgSrc;
	    }
	 	target.parentNode.onmouseout=function(){
	 	//var imgnum=parseInt(target.parentNode.previousSibling.value);
	 	var imgnum =$('#score').val();
	 	//console.log(target.parentNode.previousSibling);
	   for(n=0;n<imgArray.length;n++){
	   		imgArray[n].src=imgSrc;
	    }
	  for(n=0;n<imgnum;n++){
	   	imgArray[n].src=imgSrc_2;
	  }
	 }
	   }
	} else {
	  return false;
	}
	}
//-->
</script>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
?>