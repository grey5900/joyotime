<div class="control-group">
	<label class="control-label" for="title">
	    封面图片：
	</label>
	<div class="controls box-fileupload">
		<div class="fileupload fileupload-new" data-provides="fileupload">
          <div id="uploadButtonHide">
          	<span class="btn btn-primary btn-file">
          	  <span class="fileupload-new">选择图片</span>
          	  <span class="fileupload-exists">更改图片</span>
          	  <input type="file" accept="image/*" name="cover" class="input-stand" required="required" id="imageCover" /> 图片尺寸640*314px
          	  <input type="hidden" name="data[packages][crop][left]" id="l" value />
          	  <input type="hidden" name="data[packages][crop][top]" id="t" value />
          	</span>
          	 <!-- <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">删除文件</a> -->
             <!-- <p class="help-block">图片尺寸最小640*640px，及其以上，jpg, jpeg, png, gif格式</p> -->
              <div class="help-block hide">
                  <p class="hide">图片尺寸须大于640*314px，请重新上传</p>
                  <b class="cropHelp hide">在图上拖动选择要显示的区域</b>
                  <div class="cropText hide">
                  	<input type="text" name="data[packages][crop][width]" class="input-text" readonly="readonly" id="w" value="0" />
                  	*
                  	<input type="text" name="data[packages][crop][height]" class="input-text" readonly="readonly" id="h" value="0" />
                    （宽高须大于640）
                  </div>
              </div> 
          </div>

          <?php if(isset($this->request->data['packages'])): ?>
          <div class="fileupload-preview fileupload-exists thumbnail"></div>
          <div class="thumbnail" id="readyPreview">
              <img src="<?php echo $this->Packages->cover($this->request->data['packages'], 'x160')?>" />
          </div>
         <?php else: ?>
          <div class="fileupload-preview fileupload-exists thumbnail"></div>
          <div class="fileupload-exists" id="readyPreview">
              <img src="" />
          </div>
         <?php endif; ?>       
        </div>
        <input type="button" id="submitImage" class="btn btn-primary hide" value="上传图片" />
        <input type="hidden" name="data[packages][cover]" id="voicesCover" value />
        <b class="cropHelp" id="promptImage"></b>
	</div>
</div>
