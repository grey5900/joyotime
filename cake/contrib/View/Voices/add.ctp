<div class="page-wrapper">
    <ul class="breadcrumb">
      <li><i class="i-left"></i><a href="/voices/index"  class="active">解说列表</a></li>
    </ul>
    <div class="tab-content clearfix active">
        <div class="mainway col-7" data-session='<?php echo session_id(); ?>'>
        <?php echo $this->Form->create('voices', array('class' => 'form-horizontal', 'type' => 'file', 'novalidate' => true,'onkeypress'=>'if(event.keyCode==13||event.which==13){return false;}')); ?>
                <?php echo $this->Form->input('title', array(
                    'label' => '<span class="required">*</span>解说标题：', 
                    'type' => 'text',
                    'class' => 'input-stand data-maxlength',
                    'required' => 'required',
                    'maxlength' => '30',
                        'data-maxlength'=>'30'
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
                        <div class="control-group">
                            <label class="control-label" for="ViewAddress">
                                <span class="required">*</span>地&nbsp;&nbsp;&nbsp;址:
                            </label>
                            <div class="controls">
                                <input type="text" readonly="readonly" id="ViewAddress" />
                            </div>
                        </div>
                        <?php echo $this->Form->hidden('address', array(
                        'label' => false,
                        'id' => 'RecordCommentAddress'
                        ))?>
                        <?php echo $this->Form->hidden('address_components', array(
                            'label' => false,
                            'id' => 'address_components'
                        ))?>
                    
                    </div>
                </div>

                
                <!-- 标签开始 -->
          
			
		 <div class="control-group">
             <label class="control-label" for="title">标签管理：</label>
             <div class="controls">
                 <div id="fs-tags">

                 </div>
                 <?php echo $this->Form->input('tags', array(
                         'label' => false,
                         'div'=>false,
                         'type' => 'hidden',
                         'required' => 'required',
                         "id"=>'tags'
                         ));
                         ?>
                 <?php echo $this->Form->input('id', array(
                         'label' => false,
                         'div'=>false,
                         'type' => 'hidden',
                         'required' => 'required',
                         ));
                         ?>
                 <div class="fs-taggrounp">
                     <?php foreach ($tags as $key=>$val):?>
                     <p><?php echo $val['category'];?></p>

                     <?php foreach ($val['tag'] as $k=>$v): ?>
                     <a type="button" class="btn btn-primary btn-small <?php if(isset($voice['tags'])&&is_array($voice['tags']) &&in_array($v['name'], $voice['tags'])){echo 'active';}?>" data-toggle="button" data-text="<?php echo $v['name'];?>"><?php echo $v['name'];?>
                     </a>
                     <?php endforeach;?>

                     <?php endforeach; ?>
                 </div>
             </div>
		</div>
			
	
                
                 <!-- 标签结束 -->
                <?php echo $this->Form->hidden('user_id', array('value' => $this->Session->read('Auth.User._id')));?>

            <div class="control-group">
                <div class="controls">
                    <input type="submit" class="btn btn-primary" id="voice-save" style="display: inline-block;" value="提交审核" />

                </div>
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
    echo $this->Html->script('/js/jquery.uploadify.min');
    echo $this->Html->script('/js/ajaxfileupload.js');
    echo $this->Html->script('/js/fishsaying.uploadImage');
    echo $this->Html->script('/js/fishsaying.uploadVoice');
    echo $this->Html->script('/js/plug-in/bootstrap-tags/js/bootstrap-tags');
    $this->end();
?>
<?php $this->start('script'); ?>
<script type="text/javascript">
var tags;
$(function(){

    //$('.btn').button();
    tags = $("#fs-tags").tags({
        tagSize: "sm",
        promptText:"<?php echo __('最多选择5个标签,也可直接输入.')?>",
        tagData: <?php if(isset($voice['tags']) && $voice['tags']!=''){ ?><?php echo json_encode($voice['tags']);?><?php } else { ?>[]<?php }?>,
    afterDeletingTag:function(tag){
        var obj= $('.fs-taggrounp .btn[data-text='+tag+']');
        obj.removeClass('active');
    }


});
$('.fs-taggrounp .btn').on('click',function (e) {
    var str=$.trim($(this).text());
    if ($(this).hasClass('active')) {
        tags.removeTag(str);
        $(this).removeClass('active');
        return false;
    }else{
        if (tags.getTags().length>=5) {
            return false;
        }
        tags.addTag(str);
        return true;
    }
});

$('#voice-save').on('click',function (e) {
    var tagslist= tags.getTags();
    $('#tags').val(tagslist);
});

    window.modal_map = function() { 
        $.getScript("/js/fishsaying.map.js", function(){
            var initial = {};
                initial['lat'] = <?php echo isset($this->request->data['voices']['latitude']) ? $this->request->data['voices']['latitude'] : '30.65744145856209'; ?>;
                initial['lng'] = <?php echo isset($this->request->data['voices']['longitude']) ? $this->request->data['voices']['longitude'] : '104.06588791534422'; ?>;
                $('#RecordCommentLatitude').val(<?php echo isset($this->request->data['voices']['latitude']) ? $this->request->data['voices']['latitude'] : ''; ?>);
                $('#RecordCommentLongitude').val(<?php echo isset($this->request->data['voices']['longitude']) ? $this->request->data['voices']['longitude'] : ''; ?>);
            var map, marker = null;
            var center = new google.maps.LatLng(initial['lat'],initial['lng']);
            getAdress(center);
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


    
})
</script>
<?php
$this->end();
?>
