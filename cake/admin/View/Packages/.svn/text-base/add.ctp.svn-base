<div class="page-wrapper">
    <ul class="breadcrumb">
      <li><i class="i-left"></i><a href="/packages/index"  class="active">鱼说包列表</a></li>
    </ul>
    <div class="tab-content clearfix active">
        <div class="mainway col-7" data-session='<?php echo session_id(); ?>'>
        <?php echo $this->Form->create('packages', array('class' => 'form-horizontal', 'type' => 'file', 'novalidate' => true)); ?>
                <?php echo $this->Form->input('title', array(
                    'label' => '<span class="required">*</span>标题：', 
                    'type' => 'text', 
                    'class' => 'input-stand data-maxlength',
                    'required' => 'required',
                    'maxlength' => '30',
                    'data-maxlength' => '30'
                ));?>

            <!-- 上传图片 -->

            <input type="hidden" name="data[packages][cover]" id="voicesCover" value="" />
                <div class="control-group">
                    <label class="control-label" for="title">
                        封面图片：
                    </label>
                    <div class="controls">
                        <span class="btn btn-primary btn-file">
                            <span class="fileupload-new">选择图片</span>
                            <input id="file-put" type="file" name="data[Package][image]" accept="image/*" required="required" class="input-stand" id="SplashImage" />
                        </span>
                        <span class="text-muted">尺寸：640*314</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="title">

                    </label>
                    <div class="controls">
                        <div class="thumbnail voicedivimgPre-out">
                            <div id="voicedivimgPre">
                                <img id="voiceimgPre" src="<?php echo $this->Packages->cover($this->request->data['packages'], 'x160')?>"/>
                            </div>
                        </div>
                        <span class="btn btn-primary btn-single-file" disabled="disabled">上传</span>
                        <span class="text-warning remsg"></span>
                    </div>

                </div>

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
                    <button id="svae-btn" type="button" class="btn btn-primary">保存</button>
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
    echo $this->Html->script('/js/loading-overlay.min.js');
    echo $this->Html->script('/js/jquery.imgareaselect.min');
    echo $this->Html->script('/js/jquery.uploadify.min');
    echo $this->Html->script('/js/ajaxfileupload.js');
    echo $this->Html->script('/js/fishsaying.uploadImage');
    $this->end();
?>
<?php $this->start('script'); ?>
<script type="text/javascript">
$(function(){
    var _imgval="<?php echo $this->Packages->cover($this->request->data['packages'], 'source')?>";
    if(_imgval!=''){
        $('#voicesCover').val(_imgval.replace('http://cover.fishsaying.com/',''));
    }
    $('#file-put').on('change',function(){
        PreviewImage(this,'voiceimgPre','voicedivimgPre');
        $('.btn-single-file').html('上传');
        $('.btn-single-file').attr("disabled",false);
    });
    $('.btn-single-file').on('click',function(){
        $('.btn-single-file').html('正在上传');
        $('.btn-single-file').attr("disabled",true);
        $.ajaxFileUpload({
            url : "/Packages/upload",
            secureuri : false,
            fileElementId : 'file-put',
            dataType : 'json',
            success : function(data, status) {
                if(data.result == true) {
                    $.messager('上传成功');
                    $('#voicesCover').val(data.file);
                    $('.btn-single-file').html('上传成功');
                    $('.btn-single-file').attr("disabled",true);
                } else {
                    $.messager('上传失败');
                    $('.btn-single-file').html('上传');
                    $('.btn-single-file').attr("disabled",false);
                }
                $('#file-put').on('change',function(){
                    PreviewImage(this,'voiceimgPre','voicedivimgPre');
                    $('.btn-single-file').html('上传');
                    $('.btn-single-file').attr("disabled",false);
                });
            },
            error : function(data, status, e) {
                $('.btn-single-file').html('上传');
                $('.btn-single-file').attr("disabled",false);
                $.messager(data.message);
                $('#file-put').on('change',function(){
                    PreviewImage(this,'voiceimgPre','voicedivimgPre');
                    $('.btn-single-file').html('上传');
                    $('.btn-single-file').attr("disabled",false);
                });
            }
        });
    });

    var _form=$("#svae-btn").parents('form');
    _form.ajaxForm({
        dataType:"json",
        success:function(json){
            if( json.result == true){
                $.messager(json.message);
                setTimeout(function(){window.location.href = '/packages/index?'+ new Date().getTime();},1500);
            }else {
                $.messager(json.message);
                $("#svae-btn").attr('disabled',false);
            }
        },
        error:function(){
            $("#svae-btn").attr('disabled',false);
        }
    });
    $("#svae-btn").on("click" , function(){
        var _btn=$(this);
        var _thisform=$(this).parents('form');
        _btn.attr('disabled',true);

        var packagesTitle = _thisform.find('#packagesTitle'),
                voicesCover = _thisform.find('#voicesCover'),

                voiceLong = _thisform.find('#RecordCommentLongitude'),
                voiceLat = _thisform.find('#RecordCommentLatitude'),
                voiceAddress = _thisform.find('#RecordCommentAddress'),
                addressComponents = _thisform.find('#address_components');

        if (packagesTitle.is_empty() || packagesTitle.length > 15 ) {
            packagesTitle.focus();
            $.messager('请填写标题，不能超过15个字');
            _btn.attr('disabled',false);
            return false;
        }

        if(voicesCover.is_empty()){
            $.messager('请上传封面图片');
            _btn.attr('disabled',false);
            return false;
        }
        if(voiceLong.is_empty() || voiceAddress.is_empty() || voiceLat.is_empty()) {
            $.messager('请选择地理位置');
            _btn.attr('disabled',false);
            return false;
        }

        _thisform.submit();
        return true;
    });


    window.modal_map = function() { 
	   	 $.getScript("/js/fishsaying.map.js", function(){
	         var initial = {};
	             initial['lat'] = <?php echo isset($this->request->data['packages']['latitude']) ? $this->request->data['packages']['latitude'] : '30.65744145856209'; ?>;
	             initial['lng'] = <?php echo isset($this->request->data['packages']['longitude']) ? $this->request->data['packages']['longitude'] : '104.06588791534422'; ?>;
	             $('#RecordCommentLatitude').val(<?php echo isset($this->request->data['packages']['latitude']) ? $this->request->data['packages']['latitude'] : ''; ?>);
	             $('#RecordCommentLongitude').val(<?php echo isset($this->request->data['packages']['longitude']) ? $this->request->data['packages']['longitude'] : ''; ?>);
	
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
	         //如果是编辑重新从google获取详细地址 因为数据库中没有存
	         if ( $("#packagesEditForm").length > 0 ) {
	        	 initAdress(initial['lat'],initial['lng']);
	         }
	        
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
   // loadScript();
    
});

</script>
<?php
$this->end();
?>
