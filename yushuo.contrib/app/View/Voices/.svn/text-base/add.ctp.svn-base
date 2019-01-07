<div class="page-wrapper">
    <ul class="breadcrumb">
      <li><i class="i-left"></i><a href="/voices/index"  class="active">解说列表</a></li>
    </ul>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create('voices', array('class' => 'form-horizontal', 'type' => 'file', 'novalidate' => true)); ?>
                <?php echo $this->Form->input('title', array(
                    'label' => '<span class="required">*</span>解说标题：', 
                    'type' => 'text', 
                    'class' => 'input-stand',
                    'required' => 'required',
                    'maxlength' => '30'
                ));?>
                
                                
                <?php echo $this->element('uploadVoice')?>
                
                <?php echo $this->Form->input('language', array(
                    'label' => '<span class="required">*</span>解说语言：', 
                    'type' => 'select', 
                    'class' => 'input-stand',
                    'options' => array(
                        'zh_CN' => '中文',
                        'en_US' => '英文'
                    ),
                ));?>
                <?php 
//                 echo $this->Form->input('isfree', array(
//                     'label' => '<span class="required">*</span>是否免费：', 
//                     'type' => 'select', 
//                     'class' => 'input-stand',
//                     'options' => array(
//                         '0' => '收费',
//                         '1' => '免费'
//                     ),
//                 ));
                ?>
                
                <!-- 上传图片 -->
                <?php echo $this->element('uploadImage')?>
                
                <!-- 地图 -->
                <div class="control-group">
                    <label class="control-label" for="title">地理位置：</label>
                    <div class="controls box-map">
                        <span class="help-block">拖动气泡至地点所在位置,系统会自动填入经纬度</span>
                        <input type="text" name="" value="" id="inputMapSearch" placeholder="地图搜索..." />
                        <div id="mapContainer" style="width:428px;height:300px"></div>
                        <ul>
                            <li>
                                <?php echo $this->Form->input('longitude', array(
                                    'label' => '<span class="required">*</span>经度(E):',
                                    'class' => 'input-small',
                                    'required' => 'required',
                                    'id' => 'RecordCommentLongitude',
                                    'readonly' => 'readonly'
                                ))?>
                            </li>
                            <li>
                                <?php echo $this->Form->input('latitude', array(
                                    'label' => '<span class="required">*</span>纬度(N):',
                                    'class' => 'input-small',
                                    'required' => 'required',
                                    'id' => 'RecordCommentLatitude',
                                    'readonly' => 'readonly'
                                ))?>
                            </li>
                        </ul>
                        <?php echo $this->Form->input('address', array(
                            'label' => '<span class="required">*</span>地&nbsp;&nbsp;&nbsp;址:',
                            'required' => 'required',
                            'id' => 'RecordCommentAddress',
                            'readonly' => 'readonly'
                        ))?>
                        <?php echo $this->Form->hidden('address_components', array(
                            'label' => false,
                            'id' => 'address_components'
                        ))?>
                    
                    </div>
                </div>

                <?php echo $this->Form->hidden('user_id', array('value' => $this->Session->read('Auth.User._id')));?>
                <div class="form-actions">
                    <?php echo $this->Form->submit('提交审核', array(
                        'div' => false,
                        'class' => 'btn btn-primary'
                    )); ?>
                </div>
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
    echo $this->Html->script('/js/jquery.imgareaselect.min');
    echo $this->Html->script('/js/fishsaying.upload');
    $this->end();
?>
<?php $this->start('script'); ?>
<script type="text/javascript">
$(function(){
    window.modal_map = function() { 
        $.getScript("/js/fishsaying.map.js", function(){
            var initial = {};
                initial['lat'] = <?php echo isset($this->request->data['voices']['latitude']) ? $this->request->data['voices']['latitude'] : '30.65744145856209'; ?>;
                initial['lng'] = <?php echo isset($this->request->data['voices']['longitude']) ? $this->request->data['voices']['longitude'] : '104.06588791534422'; ?>;
                $('#RecordCommentLatitude').val(<?php echo isset($this->request->data['voices']['latitude']) ? $this->request->data['voices']['latitude'] : ''; ?>);
                $('#RecordCommentLongitude').val(<?php echo isset($this->request->data['voices']['longitude']) ? $this->request->data['voices']['longitude'] : ''; ?>);
            var map, marker = null;
            var center = new google.maps.LatLng(initial['lat'],initial['lng']);
                        
            map = new google.maps.Map(document.getElementById('mapContainer'),{
                mapTypeControl: false,
                center: center,
                zoom: 16
            });
            marker = new google.maps.Marker({
                position: center,
                draggable: true,
                map: map
            });
            dragendLocation(marker);
            $('#inputMapSearch').bind('keypress', function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                var addressname = e.target.value ;
                if(code == 13) { //Enter keycode
                    searchKeyword(addressname,marker,map);
                    return false;
                }
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
    
});
</script>
<?php
$this->end();
?>
