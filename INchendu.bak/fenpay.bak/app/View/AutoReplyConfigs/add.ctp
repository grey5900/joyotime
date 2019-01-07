<div class="box-content">
	<div class="content-wrapper">
		<?php echo $this->element('auto_reply_tabs', array('active' => 'defaults')); ?>      
		<div id="weixinConfigContent" class="tab-content">
        	<div class="tab-pane active" id="basic-info">
    	        <div class="operate-box">
    	        <?php echo $this->Session->flash(); ?>
	    	        <div class="well well-small">
	    	           <span class="label label-info">说明</span>                 
	    	                            被添加的关键词的回复将作为默认回复，若添加多个关键词则会随机选择其中一个的回复发送给用户。
	    	        </div>
    	        </div>
				<?php echo $this->Form->create('AutoReplyConfig', array('class' => 'form-horizontal', 'action' => 'add')); ?>
				  <fieldset>
				  	<?php echo $this->Form->hidden('AutoReplyConfig.id', 
				  			array('value' => isset($subscribe['AutoReplyConfig']['id']) ? $subscribe['AutoReplyConfig']['id'] : '')); ?>
				  	<?php echo $this->Form->hidden('situation', array('value' => AutoReplyConfig::EVT_SUBSCRIBE)); ?>
	                <?php echo $this->element('auto_reply_tag_input', array('id' => 'subscribe', 'label' => '被关注自动回复', 'tags' => $subscribe))?>
	                
	                <div class="form-actions">
    				  <?php echo $this->Form->submit('保存', array(
    						'div' => false,
    						'class' => 'btn btn-primary',
    					)); ?>
    				</div>
				  </fieldset>
				<?php echo $this->Form->end(); ?> 
				<?php echo $this->Form->create('AutoReplyConfig', array('class' => 'form-horizontal'))?>
				  <fieldset>
				  	<?php echo $this->Form->hidden('AutoReplyConfig.id', 
				  			array('value' => isset($noanswer['AutoReplyConfig']['id']) ? $noanswer['AutoReplyConfig']['id'] : '')); ?>
				  	<?php echo $this->Form->hidden('situation', array('value' => AutoReplyConfig::EVT_NOANSWER)); ?>
	                <?php echo $this->element('auto_reply_tag_input', array('id' => 'no_answer', 'label' => '回答不上时自动回复', 'tags' => $noanswer))?>
	                
	                <div class="form-actions">
    				  <?php echo $this->Form->submit('保存', array(
    						'div' => false,
    						'class' => 'btn btn-primary',
    					)); ?>
    				</div>
				  </fieldset>
				<?php echo $this->Form->end(); ?> 
        	</div><!-- div#basic-info end -->
        </div>
	</div>
</div>

<?php 
$this->start('top_nav');
echo $this->element('top_nav');
$this->end();
$this->start('left_menu');
echo $this->element('left_menu', array('active' => 'auto_replies_kw'));
$this->end();
$this->start('footer');
echo $this->element('footer');
$this->end();
?>
<?php
$this->start('script');
?>
<script type="text/javascript" charset="utf-8">
$(function() {	
    $('#subscribe').tags({
    	availableTags: <?php echo json_encode($available) ?>,
    	autocomplete: {delay: 0, minLength: 2, autoFocus: true},
    	formatId: 'data[AutoReplyConfigTag][{0}][AutoReplyTag][id]'
    });
    $('#no_answer').tags({
    	availableTags: <?php echo json_encode($available) ?>,
    	autocomplete: {delay: 0, minLength: 2, autoFocus: true},
    	formatId: 'data[AutoReplyConfigTag][{0}][AutoReplyTag][id]'
    });
});
</script>
<?php
$this->end()
?>