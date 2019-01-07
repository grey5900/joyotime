<div class="page-wrapper">
	<div class="clearfix">
	 <?php echo $this->Form->create("voices", array('class' => 'form-horizontal',
             'action' => '/adminEdit',
             'id'=>'tagEditForm',
             'onkeypress'=>'if(event.keyCode==13||event.which==13){return false;}'
             )); ?>

		<div class="col-7">
			<h4 class="fs-h"><?php echo __('编辑标签')?> <span class="text-muted"><?php echo __('鱼说')?>:<?php echo $voice['title'];?></span></h4>
		
			<div id="fs-tags"></div>
			 <?php echo $this->Form->input('tags', array(
                            'label' => false,
                            'div'=>false,
                            'type' => 'hidden',
                            'required' => 'required',
							"id"=>'tags'
                            ));
                            ?>
                         <?php echo $this->Form->input('id', array(
                            'label' => false,
                            'div'=>false,
                            'type' => 'hidden',
                            'required' => 'required',
                            ));
                            ?>   
			<div class="fs-taggrounp">
			<?php foreach ($data as $key=>$val):?>
			  	<p><?php echo $val['category'];?></p>

				<?php foreach ($val['tag'] as $k=>$v): ?>
    				<button type="button" class="btn btn-primary btn-small <?php if(isset($voice['tags'])&&is_array($voice['tags']) &&in_array($v['name'], $voice['tags'])){echo 'active';}?>" data-toggle="button" data-text='<?php echo $v['name'];?>'><?php echo $v['name'];?></button>
				<?php endforeach;?>


		<?php endforeach; ?>
			</div>
			<p class="text-center">
			  <button type="button" data-type="submit" id="tags-save" class="btn btn-primary"><?php echo __('保存')?></button>
			</p>
		</div>
		 <?php echo $this->Form->end();?>
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
<?php
$this->start('script');
?>
<script>
  $(function(){
  	//$('.btn').button();
    var tags = $("#fs-tags").tags({
    	tagSize: "sm",
    	promptText:"<?php echo __('最多选择5个标签,也可直接输入.')?>",
      	tagData: <?php if(isset($voice['tags']) && $voice['tags']!=''){ ?><?php echo json_encode($voice['tags']);?><?php } else { ?>[]<?php }?>,
      	afterDeletingTag:function(tag){
      		var obj= $('.fs-taggrounp .btn[data-text='+tag+']');
      		obj.removeClass('active');
      	}
    });

    $('.fs-taggrounp .btn').on('click',function (e) {
        var str=$.trim($(this).text());
      if ($(this).hasClass('active')) {
      	tags.removeTag(str);
          $(this).removeClass('active');
          return false;
      }else{
      	if (tags.getTags().length>=5) {
  			return false;
  		}
      	tags.addTag(str);
          return true;
      }
    }); 
    $('#tags-save').on('click',function (e) {
      var tagslist= tags.getTags();
      $('#tags').val(tagslist);
    });
  	 if ($("#tagEditForm").length > 0 ) {
  	       $('#tagEditForm').AjaxFormSubmit('/voices/index/<?php echo $index;?>');
  	 }
  	
 
  });
</script>
<?
$this->end();
?>
