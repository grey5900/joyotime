<div class="page-wrapper">
    
<?php 
    $this->Voice->rows = $items;
    if(isset($user)): 
        $this->User->init($user);
        $this->Voice->setUser($this->User);
?>
    <div class="title-voice"><?php echo $this->Voice->subTitle(Voice::STATUS_APPROVED) ?></div>
<?php endif; ?>
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::RECOMMENDED); ?>"><?php echo __('推荐鱼说') ?></a>
                    </div>
                </li>
<?php if(!isset($user)):  ?>
                <li class="">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::RECOMMEND); ?>"><?php echo __('新增推荐') ?></a>
                    </div>
                </li>
<?php endif; ?>
            </ul>
            <form class="navbar-form navbar-right" name="search" action="/voices/index/4" method="get">
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
                    array(__('Length')=> array()),
                    array(__('Score')=> array()),
                    array(__('Author') => array('width' => '10%')),
                    array(__('购买人数')=> array()),
                   
                    
                    array(__('Comments')=> array()),
array(__('Earn')=> array()),
              array(__('PlayCount')=> array()),
					array(__('标签') => array('width' => '8%')),
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
                            	<!-- <a data-toggle="lightbox" href="#demoLightbox<?php echo $idx ?>" class="thumbnail">
                            	    <img src="<?php echo $this->Voice->cover(); ?>" alt="">
                            	</a>
                            	<div id="demoLightbox<?php echo $idx ?>" class="lightbox fade"  tabindex="-1" role="dialog" aria-hidden="true">
                            	    <div class='lightbox-content'>
                            	        <img src="<?php echo $this->Voice->cover('source'); ?>">
                            	    </div>
                            	</div> -->
                            </div>
                            <div class="details">
                                <p><?php echo $this->Voice->title()?></p>
                                <a class="icon-play" data-play="modal" data-player-id="player-<?php echo $idx; ?>" data-voice="<?php echo $this->Voice->address() ?>" href="#modalPlay"></a>
                                <?php $point = $this->Voice->point(); ?>
                                <a class="icon-map"  data-map="modal" data-map-id="map-<?php echo $idx; ?>" data-lat="<?php echo $point->latitude(); ?>" data-lng="<?php echo $point->longitude(); ?>" href="#modalMap"></a> 
                                <div class="block-left"><a class="icon-qrcode" data-qrcode="modal" data-qrid="<?php echo $idx; ?>" data-qrurl="<?php echo $this->Voice->orurl($this->Session->read('Api.Token.web_host')) ?>" data-qrtitle="<?php echo $this->Voice->title() ?>" href="#modalQrcode"></a>
                                </div>
                                <?php echo $this->Voice->isfree(); ?>
                                <div class="qrimage-raw" id="qrimage-<?php echo $idx; ?>" style="display: none">
                                	<?php echo $this->Voice->orimage($this->Voice->orurl($this->Session->read('Api.Token.web_host'))); ?>
                                </div>
                            </div>                            
                        </div>
                    </td>
                    <td><?php echo $this->Voice->language(); ?></td>
                    <td><?php echo $this->Voice->length();?></td>
                    <td><?php echo $this->Voice->score() ?></td>
                    <td><?php echo $this->Voice->author('',1) ?><br /><?php echo $this->Voice->rolename() ?></td>
                    <td><?php echo $this->Voice->bought() ?></td>
                   
                    
                    <td><a href="/comments/index?voice=<?php echo $this->Voice->id(); ?>"><?php echo $this->Voice->commentTotal() ?></a></td>
          <td><?php echo $this->Voice->earnTotal() ?></td>
          <td><?php echo $this->Voice->playTotal() ?></td>
                     <td><?php echo $this->Voice->tagTotal(4) ?></td>
                    <td><?php echo $this->Voice->recommendCancel(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>
<?php echo $this->element('/modals/qrcode'); ?>
<?php echo $this->element('/modals/unavailable'); ?>
<?php echo $this->element('/modals/invalid'); ?>
<?php echo $this->element('/modals/lightbox'); ?>
<?php echo $this->element('/modals/common_tip'); ?>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
?>

<?php 
$this->start('script'); ?>
<script type="text/javascript">
$(function() {
	$('a.recommend-tip-link').click(function(){
	    linkRemove = $(this);
	    data_url = $(this).attr('data-url');
	});
    // submit comment as reason for why invalid that one...
	  $('#common-hide-modal button.btn-primary').click(function(){
		    $.getJSON(data_url, function(data){
		        $.messager(data.message);
		        linkRemove.closest("tr").remove();
		        $('.modal').modal('hide');
			});
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
    $('#common-modal-body').html('确定要取消推荐该鱼说吗？');
})
</script>
<?php
$this->end();
?>