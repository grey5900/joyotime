
<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create(null, array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data')); ?>
        <fieldset>
             <div class="control-group">
             	<span class="btn btn-primary btn-file">
	          	  <span class="fileupload-new">选择图片</span>
	          	  <input id="file-put" onchange="PreviewImage(this,'imgPre','divimgPre')" type="file" name="data[Splash][image]" accept="image/*" required="required" class="input-stand" id="SplashImage">
	          	</span>
	          	<span class="text-muted">尺寸：1136*640</span>
	          	<?php echo $this->Form->submit(__('上传'), array(
                    'div' => false,
                    'class' => 'btn btn-primary',
                    'id'=> 'btn_Saveimg',
                    'disabled'=>'disabled'
                )); ?>
             </div>
             <div class="control-group">
             	<div class="thumbnail divimgPre-out">
	             	<div id="divimgPre">
		              <?php if(isset($splash)): ?>
			            <img id="imgPre" src="<?php echo $splash; ?>"/>
			          <?php endif; ?>
		          	</div>
	          	</div>
             </div>
            <div class="form-actions">
                
            </div>
          
        </fieldset>
        <?php echo $this->Form->end();?>
        </div>
    </div>
</div>


<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
?>
