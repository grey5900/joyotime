<?php 
$model = isset($model) ? $model : 'AutoReplyMessageNews';
?>
<div class="control-group">
	<label class="control-label" for="title">
	    封面图片：
	</label>
	<div class="controls box-fileupload">
		<div class="fileupload-operate">
			<?php if(!isset($image['original_url'])): ?>
				<span class="btn btn-primary fileinput-button">
					<span>选择文件</span>
					<input id="fileupload" type="file" name="files[]" data-url="/upload/cover" >
				</span>
				<button type="button" class="btn btn-danger fileinput-del hide">
			        <span>删除封面</span>
	  			</button>
	  			<?php else: ?>
				<span class="btn btn-primary fileinput-button ">
					<span>选择文件</span>
					<input id="fileupload" type="file" name="files[]" data-url="/upload/cover">
				</span>
				<button type="button" class="btn btn-danger fileinput-del hide">
			        <span>删除封面</span>
				</button>
			<?php endif; ?>
		</div>
  		<span class="fileupload-info">图片尺寸最小640*640px，及其以上，jpg, jpeg, png, gif格式</span>
  		
		<div class="fileupload-view">
			<?php if(isset($image['original_url'])): ?>
				<i class="fileupload-loading hide"></i>
				<p class="img-default hide">上传封面图片</p>
				<img id="cover-img" class="img-polaroid" alt="" src="<?php echo $image['original_url'] ?>">
			<?php else: ?>
				<i class="fileupload-loading hide"></i>
				<p class="img-default">上传封面图片</p>
				<img id="cover-img" class="img-polaroid hide" alt="" src="">
			<?php endif; ?>
		</div>
		<?php echo $this->Form->input($model.'.image_attachment_id', array('type' => 'hidden'))?>
	</div>
</div>
<?php
$this->start('script');
echo $this->Html->script('/js/plug-in/blueimp/jquery.fileupload');
echo $this->Html->script('/js/plug-in/blueimp/jquery.fileupload-process.min');
echo $this->Html->script('/js/plug-in/blueimp/jquery.fileupload-validate.min');
echo $this->Html->script('/js/plug-in/blueimp/jquery.iframe-transport.min');
echo $this->Html->script('/js/plug-in/fishsaying.fileupload');
$this->end();
?>