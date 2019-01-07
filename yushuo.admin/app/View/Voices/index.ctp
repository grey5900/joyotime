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
            <form class="navbar-form navbar-right" name="search" action="/voices/index" method="get">
              <div class="form-group">
                <input type="text" name="title" class="input-search" placeholder="解说标题搜索" value="<?php echo $kw; ?>">
              </div>
              <a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
            </form>
        </div>
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_APPROVED); ?>"><?php echo __('Approved') ?></a>
                    </div>
                </li>
<?php if(!isset($user)):  ?>
                <li class="">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_UNAVAILABLE); ?>"><?php echo __('Unavailable') ?></a>
                    </div>
                </li>
<?php endif; ?>
            </ul>
        </div>
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('Voice') => array('width' => '25%')),
                    array(__('Language') => array('width' => '6%')),
                    array(__('Length') => array('width' => '6%')),
                    array(__('Score') => array('width' => '10%')),
                    array(__('Author') => array('width' => '10%')),
                    array(__('Checkouts') => array('width' => '10%')),
                    array(__('Earn') => array('width' => '8%')),
                    array(__('Comments') => array('width' => '6%')),
                    array(__('Approved Date') => array('width' => '8%')),
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
                            	<a data-toggle="lightbox" href="#demoLightbox<?php echo $idx ?>" class="thumbnail">
                            	    <img src="<?php echo $this->Voice->cover(); ?>" alt="">
                            	</a>
                            	<div id="demoLightbox<?php echo $idx ?>" class="lightbox fade"  tabindex="-1" role="dialog" aria-hidden="true">
                            	    <div class='lightbox-content'>
                            	        <img src="<?php echo $this->Voice->cover('source'); ?>">
                            	    </div>
                            	</div>
                            </div>
                            <div class="details">
                                <p><?php echo $this->Voice->title()?></p>
                                <a class="icon-play" data-play="modal" data-player-id="player-<?php echo $idx; ?>" data-voice="<?php echo $this->Voice->address() ?>" href="#modalPlay"></a>
                                <?php $point = $this->Voice->point(); ?>
                                <a class="icon-map"  data-map="modal" data-map-id="map-<?php echo $idx; ?>" data-lat="<?php echo $point->latitude(); ?>" data-lng="<?php echo $point->longitude(); ?>" href="#modalMap"></a> 
                                <div class="block-left"><?php echo $this->Voice->isfree(); ?></div>
                            </div>                            
                        </div>
                    </td>
                    <td><?php echo $this->Voice->language(); ?></td>
                    <td><?php echo $this->Voice->length();?></td>
                    <td><?php echo $this->Voice->score() ?></td>
                    <td><?php echo $this->Voice->author() ?></td>
                    <td><?php echo $this->Voice->checkoutTotal() ?></td>
                    <td><?php echo $this->Voice->earnTotal() ?></td>
                    <td><?php echo $this->Voice->commentTotal() ?></td>
                    <td><?php echo $this->Voice->approvedDate() ?></td>
                    <td><?php echo $this->Voice->invalid(); ?> - <?php echo $this->Voice->unavailable(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>
<?php echo $this->element('/modals/unavailable'); ?>
<?php echo $this->element('/modals/invalid'); ?>

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
    // submit comment as reason for why invalid that one...
    var unavailable_url = '';
    var linkRemove = '';
    // save global data when click invalid link...
    $('a.unavailable-link').click(function(){
        linkRemove = $(this);
        unavailable_url = $(this).attr('data-url');
    });
    // handle click submit comment button in modal...
    $('.modal button.btn-primary').click(function(){
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="comment"]', parent);
        var comment = textarea.val();
        var btn = $('button.btn-unavailable');
            btn.text('<?php echo __('正在下架...')?>').attr('disabled',true);
        $.getJSON(unavailable_url+'?comment='+comment, function(data) {
        	if(data.result) {
            	linkRemove.closest("tr").remove();
            	textarea.val('');
            	$('#unavailable-modal').modal('hide');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('下架')?>').attr('disabled',false);
        });
    })
    
    // submit comment as reason for why invalid that one...
	var data_url = '';
// 	var linkRemove = '';
	// save global data when click invalid link...
	$('a.invalid-link').click(function(){
	    linkRemove = $(this);
	    data_url = $(this).attr('data-url');
	});
	// handle click submit comment button in modal...
	$('.modal button.btn-primary').click(function(){
	    var parent = $(this).parents('.modal-dialog');
	    var textarea = $('textarea[name="comment"]', parent);
	    var comment = textarea.val();
	    var btn = $('button.btn-invalid');
	    $.getJSON(data_url+'?comment='+comment, function(data) {
	    	if(data.result) {
            	linkRemove.closest("tr").remove();
            	textarea.val('');
            	$('#invalidmodal').modal('hide');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('下架')?>').attr('disabled',false);
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
})
</script>
<?php
$this->end();
?>