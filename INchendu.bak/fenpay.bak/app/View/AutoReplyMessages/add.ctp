<?php 
$this->request->data['AutoReplyMessageNews']['ImageAttachment'] = 
    isset($this->request->data['AutoReplyMessageNews']['ImageAttachment']) ?
        $this->request->data['AutoReplyMessageNews']['ImageAttachment'] : '';
?>
<div class="page-wrapper">
	<ul class="breadcrumb">
	  <li><i class="i-left"></i><a href="/auto_reply_messages/"  class="active">编辑图文内容</a></li>
	</ul>
	<div class="tab-content clearfix active">
		<div class="mainway col-7">
  			<?php echo $this->Form->create('AutoReplyMessage', array('class' => 'form-horizontal', 'action' => 'add', 'novalidate' => true)); ?>
    		<fieldset>
				<?php echo $this->Form->input('AutoReplyMessage.id'); ?>
				<?php echo $this->Form->input('AutoReplyMessageNews.id'); ?>
				<?php echo $this->Form->input('AutoReplyMessageCustom.id'); ?>
				<?php echo $this->Form->input('AutoReplyMessageExlink.id'); ?>
				<?php echo $this->Form->input('AutoReplyMessageLocation.id'); ?>
				<?php echo $this->Form->input('AutoReplyMessageNews.title', array(
					'label' => '<span class="required">*</span>标 题：',
					'type' => 'text',
					'class' => 'input-stand'
				)); ?>
				
				<?php echo $this->element('fileupload', array('image' => $this->request->data['AutoReplyMessageNews']['ImageAttachment']));?>
				
				<?php echo $this->Form->input('AutoReplyMessageNews.auto_reply_category_id', array(
					'label' => '分 类：',
					'type' => 'select',
					'options' => $cates,
					'class'=>'input-small',
					'empty' => '未分类',
	       		)); ?>

           		<?php echo $this->element('auto_reply_tag_input'); ?>
                    
           		<?php echo $this->Form->input('AutoReplyMessage.description', array(
                   'label' => '摘 要：',
                   'type' => 'textarea',
                   'class' => 'summary',
                   'maxlength' => '200',
                   'rows' => 5,
           		)); ?>
				
				<div class="control-group">
        			<label class="control-label">
        			<span class="required">*</span>详 情：</label>
        			<div class="controls box-witch">
    					<ul class="nav-radio">
							<li>
							    <?php echo $this->Form->input('type', array(
						            'label' => false,
                                    'type' => 'radio', 
						            'options' => array(
						                AutoReplyMessageNews::CUSTOM => '自定义内容',
						            ),
                                    'hiddenField' => false,
                                    'default' => AutoReplyMessageNews::CUSTOM,
                                    'div' => false
							    ))?>
							</li>
							<li>
							    <?php echo $this->Form->input('type', array(
						            'label' => false,
                                    'type' => 'radio', 
						            'options' => array(
						                AutoReplyMessageNews::LINK => '外链网址',
						            ),
                                    'hiddenField' => false,
                                    'div' => false
							    ))?>
							</li>
						</ul>
		    		    <div class="tab-pane active">
		    		    	<div class="tab-pane-subset">
								<?php echo $this->element('auto_reply_message_ueditor');?>
							</div>
		    		    </div>
        			</div>
        		</div>
			    <div class="form-actions">
					<?php echo $this->Form->submit('保存修改', array(
	  					'div' => false,
	  					'class' => 'btn btn-primary'
					)); ?>
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
echo $this->element('left_menu', array('active' => 'auto_repdives_kw'));
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
    // initialize tag based on select2 plugin.
    $('#keywords').tags();

    $('#fileupload').image_uploder({
    	action: 'validate',
		attachmentField: 'data[AutoReplyMessageNews][image_attachment_id]'
	});

    // Click event for switching tab.
    
    $('input[type="radio"][name="data[AutoReplyMessage][type]"]').click(function(e){
		var container = $('.tab-pane', $(this).parents('.box-witch'));
			container.html('');    // remove content in the container
		var templatename = $(this).val();
		
		$.tmpl($('#template-'+templatename).html()).appendTo(container);
		if(templatename=='custom') {
			var editor = new UE.ui.Editor({initialFrameHeight:410});
				editor.render("AutoReplyMessageCustomCustomContent");
				editor.reset();
		}
	});
				
	$('textarea.summary').charCount();
});
</script>
<!-- The template to display custom content inputs -->
<script id="template-custom" type="text/x-tmpl">
<div class="tab-pane-subset">
	<?php echo $this->element('auto_reply_message_ueditor');?>
</div>
</script>
<!-- The template to display exlink input -->
<script id="template-link" type="text/x-tmpl">
<div class="tab-pane-subset">
	<?php echo $this->Form->input('AutoReplyMessageExlink.exlink', array(
	    'label' => false,
	    'type' => 'text',
	    'prepend' => 'http://',
	    'placeholder' => '输入外链链接'
	));?>
</div>
</script>
<!-- The template to display map input -->
<!-- <script id="template-map" type="text/x-tmpl">
<div class="tab-pane-subset">
	<?php echo $this->Form->input('AutoReplyMessageLocation.location_id', array(
		'label' => false,
		'type' => 'select',
		'options' => array(
			'1' => 'loc1',
			'2' => 'locdd2',
			'3' => 'loc2'
		)
	)); ?>
	<a id="place_show" href="#place_map" data-toggle="modal" role="button" class="btn hide"><i class="icon-map-marker"></i><span id="place_name"></span></a>
    <?php echo $this->Form->input('AutoReplyMessageLocation.id'); ?>
</div>
</script> -->
<?php 
$this->end();
?>