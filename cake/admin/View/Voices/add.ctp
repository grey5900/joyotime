<div class="page-wrapper">
    <ul class="breadcrumb">
      <li><i class="i-left"></i><a href="/voices/index"  class="active"><?php echo __('Post new voice'); ?></a></li>
    </ul>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create('voices', array('class' => 'form-horizontal', 'type' => 'file', 'method' => 'POST')); ?>
            <fieldset>
                <?php echo $this->Form->input('title', array(
                    'label' => '<span class="required">*</span>'.__('Subject: '), 
                    'type' => 'text', 
                    'class' => 'input-stand',
                    'required' => 'required',
                ));?>
                
                <?php // echo $this->element('fileupload')?>
                <?php echo $this->Form->input('cover', array_merge(array(
                    'label' => '<span class="required">*</span>'.__('Cover: '), 
                    'type' => 'file', 
                    'class' => 'input-stand',
                ),($required) ? array('required' => 'required') : array()));?>
                
                <div class="control-group">
                    <label class="control-label" for="title"><?php echo __('Geo location: '); ?></label>
                    <div class="controls box-map">
                        <span><?php echo __('The coordinate will be filled in automatically until drag the bubble'); ?></span>
                        <div id="mapContainer" style="width:428px;height:300px"></div>
                        <ul>
                            <li>
                                <?php echo $this->Form->input('longitude', array(
                                    'label' => '<span class="required">*</span>'.__('Longitude(E): '),
                                    'class' => 'input-small',
                                    'required' => 'required',
                                    'id' => 'RecordCommentLongitude'
                                ))?>
                            </li>
                            <li>
                                <?php echo $this->Form->input('latitude', array(
                                    'label' => '<span class="required">*</span>'.__('Latitude(N): '),
                                    'class' => 'input-small',
                                    'required' => 'required',
                                    'id' => 'RecordCommentLatitude'
                                ))?>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <?php // echo $this->element('audioupload')?>
                <?php echo $this->Form->input('voice', array_merge(array(
                    'label' => '<span class="required">*</span>'.__('Audio file: '), 
                    'type' => 'file', 
                    'class' => 'input-stand',
                ), ($required) ? array('required' => 'required') : array()));?>
                <?php echo $this->Form->input('language', array(
                    'label' => '<span class="required">*</span>'.__('Language: '), 
                    'type' => 'select', 
                    'class' => 'input-stand',
                    'options' => array(
                		'zh_CN' => __('Chinese'),
                		'en_US' => __('English')
                    ),
                ));?>
                <?php echo $this->Form->input('isfree', array(
                    'label' => '<span class="required">*</span>'.__('Is Free: '), 
                    'type' => 'select', 
                    'class' => 'input-stand',
                    'options' => array(
                		'0' => __('It\'s not free, needs to buy first'),
                		'1' => __('Yeah, It\'s free'),
                    ),
                ));?>
                <?php echo $this->Form->input('length', array(
                    'label' => '<span class="required">*</span>'.__('Voice length: '), 
                    'type' => 'text', 
                    'required' => 'required',
                    'append' => '\'s',
                ));?>
                <?php echo $this->Form->hidden('user_id', array('value' => $this->Session->read('Auth.User._id')));?>
                
                <div class="form-actions">
                    <?php echo $this->Form->submit(__('Post now'), array(
                        'div' => false,
                        'class' => 'btn btn-primary'
                    )); ?>
                </div>
            </fieldset>
            <?php echo $this->Form->end();?>
        </div>
    </div>
</div>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
    $this->start('script');
    echo $this->Html->script('http://map.soso.com/api/v2/main.js');
    $this->end();
?>
<?php $this->start('script'); ?>
<script type="text/javascript">
$(function(){
    function selectLocation(){
        var initial = {};
            initial['lat'] = <?php echo isset($this->request->data['voice']['latitude']) ? $this->request->data['voice']['latitude'] : '30.65744145856209'; ?>;
            initial['lng'] = <?php echo isset($this->request->data['voice']['longitude']) ? $this->request->data['voice']['longitude'] : '104.06588791534422'; ?>;
            $('#RecordCommentLatitude').val(<?php echo isset($this->request->data['voice']['latitude']) ? $this->request->data['voice']['latitude'] : ''; ?>);
            $('#RecordCommentLongitude').val(<?php echo isset($this->request->data['voice']['longitude']) ? $this->request->data['voice']['longitude'] : ''; ?>);
        var map, marker = null;
        
        var center = new soso.maps.LatLng(initial['lat'],initial['lng']);
                    
        map = new soso.maps.Map(document.getElementById('mapContainer'),{
            center: center,
            zoom: 15
        });
        marker = new soso.maps.Marker({
            position: center,
            draggable: true,
            map: map
        });
        dragendLocation(marker);
        $('.map-search-critia').bind('keypress', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code == 13) { //Enter keycode
               searchKeyword(map);
            return false;
            }
        });
    }
    function clearOverlays(overlays){
        var overlay;
        while(overlay = overlays.pop()){
            overlay.setMap(null);
        }
    }
    function dragendLocation(marker){
        soso.maps.event.addListener(marker,"dragend",function(event){
            var lat = marker.getPosition().getLat(),
            lng = marker.getPosition().getLng();
            $('#RecordCommentLongitude').val(lng);
            $('#RecordCommentLatitude').val(lat);
        });
    }
    selectLocation();
    
    $('#fileupload').image_uploder({
        attachmentField: 'data[image_attachment_id]'
    });
});
</script>
<?php
$this->end();
?>