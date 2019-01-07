<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_APPROVED); ?>"><?php echo __('Approved') ?></a>
                    </div>
                </li>
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_UNAVAILABLE); ?>"><?php echo __('Unavailable') ?></a>
                    </div>
                </li>
            </ul>
            <form class="navbar-form navbar-right" name="search" action="/voices/index/3" method="get">
              <div class="form-group">
                <input type="text" name="title" class="input-search" placeholder="鱼说标题搜索" value="<?php echo $kw; ?>">
              </div>
              <a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
            </form>
        </div>
        
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('Voice') => array('width' => '25%')),
                    array(__('Language') => array()),
                    array(__('Length') => array()),
                    array(__('Score') => array()),
                    array(__('Author') => array('width' => '8%')),
                    array(__('Checkouts') => array()),
                    array(__('Earn') => array()),
                    array(__('PlayCount') => array()),
                    array(__('Comments') => array()),
                    array(__('Unavailable Date') => array('width' => '8%')),
                    array(__('Unavailable Reason') => array('width' => '8%')),
                    array(__('Action') => array('width' => '8%'))
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
                    <td><?php echo $this->Voice->language(); ?></td>
                    <td><?php echo $this->Voice->length(); ?></td>
                    <td><?php echo $this->Voice->score() ?></td>
                   <td><?php echo $this->Voice->author('',3) ?><br /><?php echo $this->Voice->rolename() ?></td>
                    <td><?php echo $this->Voice->checkoutTotal() ?></td>
                    <td><?php echo $this->Voice->earnTotal() ?></td>
                    <td><?php echo $this->Voice->playTotal() ?></td>
                    <td><a href="/comments/index?voice=<?php echo $this->Voice->id(); ?>"><?php echo $this->Voice->commentTotal() ?></a></td>
                    <td><?php echo $this->Voice->unavailableDate(); ?></td>
                    <td><?php echo $this->Voice->unavailableComment() ?></td>
                    <td><?php echo $this->Voice->approved(__('Reshelf')); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>
<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>
<?php echo $this->element('/modals/lightbox'); ?>
<?php echo $this->element('/modals/voice_score'); ?>
<?php echo $this->element('/modals/common_tip'); ?>
<script type="text/javascript">
<!--
$(function(){
	// handle click approved link...
    $('a.approved-link').click(function(){
        var $this = $(this);
        url = $(this).attr('data-url');
        $this.text('正在上架...');
        $.getJSON($(this).attr('data-url'), function(data){
            $.messager(data.message);
            $this.closest("tr").remove();
        });
    });
    //评分上架
	 $('#score-hide-modal button.btn-primary').click(function(){
      var parent = $(this).parents('.modal-dialog');
      var textarea = $('textarea[name="message"]', parent);
      var score = $('#score').val();
      var btn = $('button.btn-primary');
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
  	//上架
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
  
    $('a.score-tip-link').click(function(){
	    scoreLinkRemove = $(this);
	    score_data_url = $(this).attr('data-url');
	});
    // Search...
    $("#search_btn").on("click", function(){
        submit_search();
        return false;
    });
    $("form[name=search]").on("submit", function(){
        submit_search();
        return false;
    });
    function submit_search(){
        var input = $("input[name=title]");
        var form = input.parents('form');
        var title = input.val();
        var url = form.attr('action');
        window.location.href = url + "/?title=" + title;
    }
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
	 	var imgnum=parseInt(target.parentNode.previousSibling.value);
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