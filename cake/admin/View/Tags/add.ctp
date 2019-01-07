<div class="page-wrapper">
 <?php if($category && !isset($type)): ?><strong>分类名称：<?php echo $category;?></strong>
        <br /><br /><?php endif; ?>
    <ul class="breadcrumb">
      <li><i class="i-left"></i><a href="/tags/index"  class="active">标签列表</a></li>
    </ul>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create("tag", array('class' => 'form-horizontal')); ?>
        <fieldset>
            <?php echo $this->Form->input('category', array(
                'label' => '<span class="required">*</span>'.__('分类名称：'), 
                'type' => 'text',
                'required' => 'required'
            ));?>
           
            <?php echo $this->Form->input('name', array(
                'label' => '<span class="required">*</span>'.__('标签名称：'), 
                'type' => 'text',
            	'required' => 'required'
            ));?>
            
            <div class="form-actions">
                <?php echo $this->Form->submit(__('保存'), array(
                    'div' => false,
                	//	'type'=>'button',
                    'class' => 'btn btn-primary'
                )); ?>
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
<script type="text/javascript">
$(function(){
    if ( $("#tagAddForm").length > 0 ) {
        $('#tagAddForm').AjaxFormSubmit('/tags/index');
    }
    if ( $("#tagEditForm").length > 0 ) {
        $('#tagEditForm').AjaxFormSubmit('/tags/index');
    }
});
</script>