<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Voice->listLink(Voice::STATUS_PENDING); ?>"><?php echo __('Pending') ?></a>
                    </div>
                </li>
                <li class="active">
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
					array(__('音频来源') => array('width' => '10%')),
                    array(__('Language') => array('width' => '10%')),
                    array(__('Length') => array('width' => '10%')),
                    array(__('Author') => array('width' => '10%')),
                    array(__('Invalid Date') => array('width' => '20%')),
                    array(__('Invalid Reason') => array('width' => '20%'))
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
                    <td><?php echo $this->Voice->author('',2) ?><br /><?php echo $this->Voice->rolename() ?></td>
                    <td><?php echo $this->Voice->invalidDate(); ?></td>
                    <td><?php echo $this->Voice->invalidComment(); ?></td>
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
	// handle click submit comment button in modal...
	$('.modal button.btn-primary').click(function(){
	    var parent = $(this).parents('.modal-dialog');
	    var comment = $('textarea[name="comment"]', parent).val();
	    $.getJSON(data_url+'?comment='+comment, function(data) {
	        $.messager(data.message);
	        linkRemove.closest("tr").remove();
    	});
	})
	// handle click approved link...
	$('a.approved-link').click(function(){
	    var $this = $(this);
	    url = $this.attr('data-url');
	    $.getJSON($(this).attr('data-url'), function(data){
	        $.messager(data.message);
	        $this.closest("tr").remove();
		});
	});
});
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