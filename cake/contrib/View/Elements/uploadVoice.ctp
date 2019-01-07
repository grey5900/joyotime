<div class="control-group">
	<label class="control-label" for="title">
	   <span class="required">*</span>音频文件：
	</label>
	<div class="controls box-fileupload">
        <input type="file" accept="audio/*" id="submitVoice"  name="voice" required="required">
        <input type="hidden" name="data[voices][voice]" value="" id="voicesVoice"/>
        <b class='cropHelp' id='promptVocie'></b>
	</div>
</div>
