<?php 
$this->request->data['WeixinLocationConfig']['ImageAttachment'] = 
    isset($this->request->data['WeixinLocationConfig']['ImageAttachment']) ? 
        $this->request->data['WeixinLocationConfig']['ImageAttachment'] : array();
?>
<div class="page-wrapper">
	<?php echo $this->element('weixin_configs_tabs', array('active' => 'setting')); ?>
	<div class="tab-content clearfix active">
		<div class="mainway col-7">
		    <?php echo $this->Form->create('WeixinConfig', array('class' => 'form-horizontal', 'novalidate' => true)); ?>
		    <fieldset>
	            <?php echo $this->Form->input('WeixinConfig.id', array('type' => 'hidden'));?>
	            <?php echo $this->Form->input('WeixinLocationConfig.id', array('type' => 'hidden'));?>
	            
	            <?php 
		            // echo $this->Form->input('WeixinConfig.message_type', array(
		                // 'type' => 'radio',
		                // 'label' => '图文素材回复形式',
		                // 'options' => array(
		                    // 'news' => '图文',
		                    // 'text' => '文字（节约流量）'
		                // ),
		                // 'default' => isset($this->request->data['WeixinConfig']['message_type']) 
		                  // ? $this->request->data['WeixinConfig']['message_type'] : 'news'
		            // ));
	            ?>
        		<div class="control-group">
        			<label for="WeixinLocationConfigType" class="control-label">地理位置回复形式：</label>
        			<div class="controls box-witch">
    					<ul class="nav-radio">
							<li>
								<label class="radio">
									<input type="radio" name="data[WeixinLocationConfig][type]" id="WeixinLocationConfigTypeSingle" value="single" required="required" checked="checked">
									单个地点
								</label>
							</li>
							<li>
								<label class="radio">
									<input type="radio" name="data[WeixinLocationConfig][type]" id="WeixinLocationConfigTypeMultiply" value="multiply" required="required">
									多个地点
								</label>
							</li>
						</ul>
		    		    <div class="tab-pane active">
		    		    </div>
        			</div>
        		</div>
	            
	            <div class="form-actions">
		            <?php echo $this->Form->submit('保存', array(
		                'div' => false,
		                'class' => 'btn btn-primary'
		            ));?>
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
	echo $this->element('left_menu', array('active' => 'weixin_config'));
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
?>
<?php 
$this->start('script');
?>
<script type="text/javascript">
$(function(){
    // Click event for switching tab.
    $('input[type="radio"][name="data[WeixinLocationConfig][type]"]').click(function(e){

    	var container = $('.tab-pane', $(this).parents('.box-witch'));
    		
    	container.html('');    // remove content in the container
    	
    	var templatename = $(this).val();
    	$.tmpl($('#template-'+templatename).html()).appendTo(container);
    	if (templatename == 'multiply') {
    		$('#fileupload').image_uploder({
	    		attachmentField: 'data[WeixinLocationConfig][image_attachment_id]'
	    	});
    	};
	});
	<?php 
	// to initialize WeixinLocationConfig.type field.
	if(isset($this->request->data['WeixinLocationConfig']['type'])) {
	    switch ($this->request->data['WeixinLocationConfig']['type']) {
	        case WeixinLocationConfig::TYPE_SINGLE:
	            echo "$('input[type=\"radio\"][name=\"data[WeixinLocationConfig][type]\"][value=\"".WeixinLocationConfig::TYPE_SINGLE."\"]').trigger('click')";
	            break;
	        default:
	            echo "$('input[type=\"radio\"][name=\"data[WeixinLocationConfig][type]\"][value=\"".WeixinLocationConfig::TYPE_MULTIPLY."\"]').trigger('click')";
	    }
	}
	?>
});
</script>
<?php 
$this->end();
?>

<!-- The template to display exlink input -->
<script id="template-multiply" type="text/x-tmpl">
	<div class="well well-small">
	    <span class="label label-info">说明</span>                 
		返回离用户最近的多个地点
	</div>
	<?php echo $this->Form->input('WeixinLocationConfig.title', array(
	    'label' => '<span class="required">*</span>封面标题：',
	    'type' => 'text',
	    'class' => 'input-large',
	    'placeholder' => '请输入封面标题'
	));?>
	
	<?php echo $this->element('fileupload', array(
		'model' => 'WeixinLocationConfig', 
		'image' => $this->request->data['WeixinLocationConfig']['ImageAttachment']
	));?>
</script>

<!-- The template to display exlink input -->
<script id="template-single" type="text/x-tmpl">
	<div class="well well-small">
	    <span class="label label-info">说明</span>             
		返回离用户最近的一个地点及该地点关联的扩展信息。
	</div>
</script>