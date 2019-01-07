<div class="page-wrapper">
    
 <div class="box-navtool">
       <span style="font-size: 16px">鱼说包：<?php echo $package['title'];?></span>
        <br />
    </div>
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Packages->relationVoiceLink($package['_id']) ;?>"><?php echo __('已关联') ?></a>
                    </div>
                </li>
<?php if(!isset($user)):  ?>
                <li class="">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->Packages->newRelationLink($package['_id']); ?>"><?php echo __('新增关联') ?></a>
                    </div>
                </li>
<?php endif; ?>
            </ul>
        </div>
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('Voice') => array('width' => '90%')),
                   
                    array(__('Action') => array('width' => '10%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody id="gridtbody">
            <?php foreach($package['voices'] as $idx => $row): ?>
                <?php $this->Voice->init($row); ?>
                <tr data-id="<?php echo $this->Voice->id(); ?>" data-sort="<?php echo $idx; ?>">
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
                 
                    <td><?php echo $this->Packages->pullVoiceLink($this->Voice->id(),$package['_id']); ?></td>
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
    // submit comment as reason for why invalid that one...
	var data_url = '';


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
    $('a.pull-voice-link').click(function(){
	    var $this = $(this);
	    url = $(this).attr('data-url');
	    $.getJSON($(this).attr('data-url'), function(data){
	        $.messager(data.message);
	        $this.closest("tr").remove();
		});
	});

    //拖拽排序
    $("#gridtbody").dragsort({
        dragSelector: "tr",
        dragEnd: saveSort,
        dragBetween: false,
        placeHolderTemplate: "<tr></tr>",
        scrollContainer:".main"

    });
    //拖拽动作结束--数据提交服务器
    function saveSort(){
        var _item=$(this);
        var _package_id="<?php echo $package['_id'];?>";
        var _voice_id=_item.attr('data-id');
        var _index=$("#gridtbody tr").index($(this));
        var _offset=_index -parseInt(_item.attr('data-sort'));
        $.ajax({
            type: "GET",
            url: "/packages/offset",
            data: {package_id:_package_id,voice_id:_voice_id,offset:_offset},
            dataType: "json",
            success: function (data) {
                if(data.result){
                    $.messager(data.message);
                    //_item.attr('data-id',_index);
                    window.location.href=window.location.href;
                }else{
                    $.messager(data.message);
                }
            },
            error: function (msg) {
                alert(msg);
            }
        })
    }
})

</script>
<?php
$this->end();
?>