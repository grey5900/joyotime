<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('被举报解说') => array('width' => '20%')),
                    array(__('语言') => array('width' => '8%')),
                    array(__('举报内容') => array('width' => '25%')),
                    array(__('举报人') => array('width' => '8%')),
                    array(__('举报时间') => array('width' => '15%')),
                    array(__('状态') => array('width' => '8%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Report->init($row); ?>
                <tr class="">
                    <td>
                        <div class="intro">
                            <div class="pull-left">
                            	<a data-toggle="lightbox" href="#demoLightbox<?php echo $idx ?>" class="thumbnail">
                            	    <img src="<?php echo $this->Report->Voice->cover(); ?>" alt="">
                            	</a>
                            	<div id="demoLightbox<?php echo $idx ?>" class="lightbox fade"  tabindex="-1" role="dialog" aria-hidden="true">
                            	    <div class='lightbox-content'>
                            	        <img src="<?php echo $this->Report->Voice->cover('source'); ?>">
                            	    </div>
                            	</div>
                            </div>
                            <div class="details">
                                <p><?php echo $this->Report->Voice->title()?></p>
                                <a class="icon-play" data-play="modal" data-player-id="player-<?php echo $idx; ?>" data-voice="<?php echo $this->Report->Voice->address() ?>" href="#modalPlay"></a>
                                <?php $point = $this->Report->Voice->point(); ?>
                                <a class="icon-map"  data-map="modal" data-map-id="map-<?php echo $idx; ?>" data-lat="<?php echo $point->latitude(); ?>" data-lng="<?php echo $point->longitude(); ?>" href="#modalMap"></a> 
                                <div class="block-left"><?php echo $this->Report->Voice->isfree(); ?></div>
                            </div>                            
                        </div>
                    </td>
                    <td><?php echo $this->Report->Voice->language(); ?></td>
                    <td>
                        
                        <div rel="tooltip" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo $this->Report->content();?>">
                            <?php echo $this->Report->content(40);?>
                        </div>
                    </td>
                    <td><?php echo $this->Report->User->username() ?></td>
                    <td><?php echo $this->Report->created() ?></td>
                    <td><?php echo $this->Report->status() ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>

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
    
    $('[rel="tooltip"]').tooltip('hide');
    
    // handle click approved link...
	$('a.report-pending-link').click(function(){
	    var $this = $(this);
	    var td = $this.parent();
	    url = $(this).attr('data-url');
	    $.getJSON($(this).attr('data-url'), function(data){
	        $.messager(data.message);
	        td.html('<?php echo $this->Report->status(ReportHelper::STATUS_DONE) ?>');
		});
	});
})
</script>
<?php
$this->end();
?>