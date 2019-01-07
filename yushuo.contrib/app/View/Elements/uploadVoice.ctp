<div class="control-group">
	<label class="control-label" for="title">
	   <span class="required">*</span>音频文件：
	</label>
	<div class="controls box-fileupload">
        <div class="fileupload fileupload-new" data-provides="fileupload">
            <span class="uneditable-input">
                <span class="fileupload-preview">
                    <?php echo $this->Voice->file($this->request->data('voices')); ?>
                </span>
            </span>
        	<span class="btn btn-primary btn-file">
        	    <span class="fileupload-new">选择上传文件</span>
        	    <span class="fileupload-exists">更改上传文件</span>
        	    <?php echo $this->Form->hidden('length', array(
                    'label' => false,
                    'id' => 'voiceDuration'
                ))?>
        	    <input type="file" accept="audio/x-m4a" id="voicesVoice" value="<?php echo $this->Voice->file($this->request->data('voices')); ?>" name="data[voices][voice]" data-access-url="<?php echo $voiceDownload ?>" data-url="http://up.qiniu.com" data-token="<?php echo $token['uptoken']['voice'] ?>" data-input-status="<?php echo $data_input_status; ?>" data-provides="fileupload" class="input-stand" required="required" >
        	</span>
        	
            <audio id="voices_voice" >
                <source src="" type="audio/mp4">
                <source src="" type="audio/x-m4a">
                <source src="" type="audio/mp4a-latm">
                <source src="" type="audio/ogg">
            </audio>
        	<a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">删除文件</a>
        	<p class="help-block">音频必须在5分钟内，60秒之上，m4a格式音频</p>
        </div>  
	</div>
</div>
