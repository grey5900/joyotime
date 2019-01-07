<?php 
$selected_locations = isset($selected_locations) ? $selected_locations : array();
$selected_message = isset($this->request->data['AutoReplyMessage']) 
    ? $this->request->data['AutoReplyMessage'] : array();
?>
<div class="page-wrapper">
	<ul class="breadcrumb">
	  <li><i class="i-left"></i><a href="/auto_reply_location_extends"  class="active">编辑扩展信息</a></li>
	</ul>
	<div class="tab-content clearfix active">
		<div class="mainway col-7">
        	<?php echo $this->Form->create('AutoReplyLocationExtends', array('class' => 'form-horizontal', 'action' => 'add', 'novalidate' => true, 'id' => 'AutoReplyLocationMessageForm')); ?>
		    <?php echo $this->Form->hidden('AutoReplyMessage.id'); ?>
		    <fieldset>
    		    <div class="control-group">
    		    	<label class="control-label" for="criteria"><span class="required">*</span>图文素材：</label>
                    <div class="controls">
                    <?php if(!isset($this->request->data['AutoReplyMessage']['id'])): ?>
                        <a href="#select_news" id="selectGraphic" role="button" class="btn btn-default" data-toggle="modal">选择图文</a>
                    <?php endif; ?>
                        
                   		<?php if(isset($this->request->data['AutoReplyMessage'])): ?>
                   		<div class="box-graphic" id="show_graphic">
					    	<?php echo $this->element('select_message_item', array('item' => $this->request->data))?>
						</div>
						<?php else: ?>
						<div class="box-graphic hide" id="show_graphic">
							<div class="pull-right" href="#">
					    	  <img id="show_graphic_img" class="media-object" data-src="http://www.placeholder-image.com/image/80x80" alt="80x80" src="">
					    	</div>
					    	
					    	<div class="media-body">
					    		<h3 id="show_graphic_text"></h3>
					    		<p id="show_graphic_desc"></p>
					    	</div>			
						</div>
						<?php endif; ?>	 
						
						<?php 
						    echo $this->Form->error('AutoReplyLocationMessage.0.auto_reply_message_id'); 
						?>
                    </div>
                </div>
                <?php echo $this->element('/modals/auto_reply_location_extends/select_news', 
                    array(compact('cates', 'messages'))); ?>
    		    <div class="control-group">
    		        <label class="control-label" for="criteria"><span class="required">*</span>关联地点：</label>
                    <div class="controls">
						<select id="selectMultiple" data-select="multiple" multiple="multiple">
						    <?php 
						    foreach($locations as $item): 
						       if(in_array($item['AutoReplyLocation']['id'], $selected_locations)) {
						           $selected = 'selected';
	                            } else {
                                   $selected = '';
                                }
						    ?>
							<option <?php echo $selected ?> data='<?php echo json_encode($item['AutoReplyLocation'])?>' value="<?php echo $item['AutoReplyLocation']['id']?>"><?php echo $item['AutoReplyLocation']['title']?></option>
							<?php endforeach;?>
						</select>
						
						<?php 
						    echo $this->Form->error('AutoReplyLocationMessage.0.auto_reply_location_id'); 
						?>
                    </div>
                </div>
			    <div class="form-actions">
				  <?php echo $this->Form->submit('保存', array(
    					'div' => false,
    					'class' => 'btn btn-primary',
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
echo $this->element('left_menu', array('active' => 'auto_repdives_geo'));
$this->end();
$this->start('script');
?>
<script type="text/javascript">
$(function(){
	
	$('form#AutoReplyLocationMessageForm').submit(function(){
		var selected = $('#select_news .ui-selected');
		if (selected.length > 0) {
			var data = JSON.parse($('.data', selected).text());
        	$('input.input_auto_reply_message_id').val(data.id);
		} else {
		    var id = $('input[name="data[AutoReplyMessage][id]"]').val();
		    $('input.input_auto_reply_message_id').val(id);
		}
	});
});
</script>
<?php 
$this->end();
?>