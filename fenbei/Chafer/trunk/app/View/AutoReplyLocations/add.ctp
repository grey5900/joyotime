<?php 
$this->request->data['ImageAttachment'] = isset($this->request->data['ImageAttachment']) ? 
    $this->request->data['ImageAttachment'] : '';
?>
<div class="page-wrapper">
	<ul class="breadcrumb">
	  <li><i class="i-left"></i><a href="/auto_reply_locations/" class="active">编辑地点</a></li>
	</ul>
    <div class="tab-content clearfix active">
		<div class="mainway col-7">
            <?php echo $this->Form->create('AutoReplyLocation', array('class' => 'form-horizontal', 'action' => 'add', 'novalidate' => true)); ?>
            <fieldset>
                <?php echo $this->Form->input('AutoReplyLocation.id'); ?>
                <?php echo $this->Form->input('AutoReplyLocation.title', array(
                	'label' => '<span class="required">*</span>标 题：', 
                	'type' => 'text', 
                	'class' => 'input-stand', 
                	'prepend' => ''
				)); ?>
                
                <?php echo $this->element('fileupload', array('model' => 'AutoReplyLocation', 'image' => $this->request->data['ImageAttachment']));?>
                
                <div class="control-group">
                    <div class="controls box-map">
                        <div id="mapContainer" style="width:428px;height:300px"></div>
                        <!-- <div class="form-inline">
				            <input type="text" id="keyword" class="search-query input-stand" placeholder="">
				            <a type="button" id="searchKeyword" class="btn btn-default">搜索</a>
				        </div> -->
				        <ul>
							<li>
						        <label><span class="required">*</span>经度(E):</label>
						        <input name="data[AutoReplyLocation][longitude]" class="input-small" type="text" id="AutoReplyLocationLongitude" required="required">
							</li>
							<li>
								<label><span class="required">*</span>纬度(N):</label>
		                    	<input name="data[AutoReplyLocation][latitude]" class="input-small" type="text" id="AutoReplyLocationLatitude" required="required">
							</li>
						</ul>
                    </div>
                </div>
                <?php echo $this->Form->hidden('AutoReplyLocation.map_url')?>

                <?php echo $this->Form->input('AutoReplyLocation.address', array(
                	'label' => '详细地址：', 
                	'type' => 'text',
					'class' => 'input-large'
				)); ?>
                <?php echo $this->Form->input('AutoReplyLocation.description', array(
                	'label' => '地点描述：',
                	'type' => 'text',
                	'placeholder' => '如本店特色、联系信息等',
                	'class' => 'input-large'
				)); ?>

                <div class="form-actions">
                    <?php echo $this->Form->submit('保存修改', array('div' => false, 'class' => 'btn btn-primary', )); ?>
                </div>
            </fieldset>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<?php 
$this->start('top_nav');
echo $this->element('top_nav');
$this->end();
$this->start('left_menu');
echo $this->element('left_menu', array('active' => 'auto_repdives_geo'));
$this->end();
$this->start('footer');
echo $this->element('footer');
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
			initial['lat'] = <?php echo isset($this->request->data['AutoReplyLocation']['latitude']) ? $this->request->data['AutoReplyLocation']['latitude'] : '30.65744145856209'; ?>;
			initial['lng'] = <?php echo isset($this->request->data['AutoReplyLocation']['longitude']) ? $this->request->data['AutoReplyLocation']['longitude'] : '104.06588791534422'; ?>;
			$('#AutoReplyLocationLatitude').val(<?php echo isset($this->request->data['AutoReplyLocation']['latitude']) ? $this->request->data['AutoReplyLocation']['latitude'] : ''; ?>);
			$('#AutoReplyLocationLongitude').val(<?php echo isset($this->request->data['AutoReplyLocation']['longitude']) ? $this->request->data['AutoReplyLocation']['longitude'] : ''; ?>);
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
			$('#AutoReplyLocationLongitude').val(lng);
			$('#AutoReplyLocationLatitude').val(lat);
		});
	}
	selectLocation();
	
	$('#fileupload').image_uploder({
		attachmentField: 'data[AutoReplyLocation][image_attachment_id]'
	});
});
</script>
<?php
$this->end();
?>