<?php 
if(!$items) {
    $items = array();
}
?>
<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <div class="alert alert-error hide"></div>
            <div class="pull-left">
                <a href="/voices/add" role="button" class="btn btn-primary">新建解说</a>
            </div>
        </div>
        
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('解说') => array('width' => '40%')),
                    array(__('时长') => array('width' => '10%')),
                    array(__('提交时间') => array('width' => '20%')),
                    array(__('状态') => array('width' => '10%')),
                    array(__('操作') => array('width' => '20%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <tr class="">
                    <td>
                        <div class="intro">
                            <div class="pull-left">
                                <a data-toggle="lightbox" href="#demoLightbox<?php echo $idx ?>" class="thumbnail">
                                    <img src="<?php echo $this->Voice->cover($row); ?>" alt="">
                                </a>
                                <div id="demoLightbox<?php echo $idx ?>" class="lightbox fade"  tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class='lightbox-content'>
                                        <img src="<?php echo $this->Voice->cover($row, 'source'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="details">
                                <p><?php echo $row['title']?></p>
                                <a class="icon-play" data-play="modal" data-player-id="player-<?php echo $idx; ?>" data-voice="<?php echo $this->Voice->address($row) ?>" href="#modalPlay"></a>
                                <?php $point = $this->Voice->point($row); ?>
                                <a class="icon-map"  data-map="modal" data-lat="<?php echo $point->latitude(); ?>" data-lng="<?php echo $point->longitude(); ?>" href="#modalMap"></a> 
                            </div>                            
                        </div>
                    </td>
                    <td>
                        <?php echo $row['length']?> <?php echo __('秒')?>
                        <?php echo $this->Voice->isfree($row); ?>
                    </td>
                    <td><?php echo $this->Voice->modified($row); ?></td>
                    <td><?php echo $this->Voice->status($row); ?></td>
                    <td><?php echo $this->Voice->remove($row); ?> <?php echo $this->Voice->edit($row); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>
<?php echo $this->element('/modals/map'); ?>
<?php echo $this->element('/modals/play'); ?>
<?php echo $this->element('/modals/remove'); ?>

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
    window.modal_map = function() { 
        $('[data-map="modal"]').on('click',function(){
            var $this = $(this);
            $('#modalMap').modal('show');
            $("#map-modal").remove();
            $('#modalMap .modal-body').prepend($('<div id="map-modal" style="height: 250px;width:500px;"></div>'));
            var lat = $this.data('lat'),
                lng = $this.data('lng');
                
            var latlng = new google.maps.LatLng(lat, lng);
            var myOptions = {
                zoom: 14,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById("map-modal"), myOptions);
            var marker = new google.maps.Marker({
                position : latlng,
                map : map
            });
            google.maps.event.trigger(map, 'resize');
            
            $('#modalMap').on('shown.bs.modal', function () {
                google.maps.event.trigger(map, "resize");
                map.setCenter(latlng);
            });
        });
    }  
    function loadScript() {  
        var script = document.createElement("script");  
        script.type = "text/javascript";  
        script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=modal_map";  
        document.body.appendChild(script);
    }
    loadScript();
})
</script>
<?php
$this->end();
?>
