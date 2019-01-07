<div class="control-group">
	<label class="control-label" for="title">
	    封面图片：
	</label>
	<div class="controls box-fileupload">
		<div class="fileupload fileupload-new" data-provides="fileupload">
          <div>
          	<span class="btn btn-primary btn-file">
          	  <span class="fileupload-new">选择文件</span>
          	  <span class="fileupload-exists">更改文件</span>
          	  <input type="file" accept="image/*" name="data[voices][cover]" data-access-url="<?php echo $coverDownload ?>" data-url="http://up.qiniu.com" data-token="<?php echo $token['uptoken']['cover'] ?>" data-provides="fileupload" data-input-status="<?php echo $data_input_status; ?>" class="input-stand" required="required" id="voicesCover">
          	  <input type="hidden" name="data[voices][crop][left]" id="l" value="" />
          	  <input type="hidden" name="data[voices][crop][top]" id="t" value="" />
          	</span>
          	 <!-- <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">删除文件</a> -->
             <!-- <p class="help-block">图片尺寸最小640*640px，及其以上，jpg, jpeg, png, gif格式</p> -->
              <div class="help-block hide">
                  <p class="hide">图片尺寸须大于640*640px，请重新上传</p>
                  <b class="cropHelp hide">在图上拖动选择要显示的区域</b>
                  <div class="cropText hide">
                  	<input type="text" name="data[voices][crop][width]" class="input-text" readonly="readonly" id="w" value="0" />
                  	*
                  	<input type="text" name="data[voices][crop][height]" class="input-text" readonly="readonly" id="h" value="0" />
                    （宽高须大于640）
                  </div>
              </div> 
          </div>
          
          <?php if(isset($this->request->data['voices'])): ?>
          <div class="fileupload-preview fileupload-exists thumbnail"></div>
          <div class="fileupload-new thumbnail" id="readyPreview">
              <img src="<?php echo $this->Voice->cover($this->request->data['voices'], 'x160')?>" />
          </div>
         <?php else: ?>
          <div class="fileupload-preview fileupload-exists thumbnail"></div>
         <?php endif; ?>       
          
        </div>
	</div>
</div>
