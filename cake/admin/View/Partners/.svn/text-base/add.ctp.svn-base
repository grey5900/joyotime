<div class="page-wrapper">
    <ul class="breadcrumb">
      <li><i class="i-left"></i><a href="/partners/index"  class="active"><?php echo __('内容合作商列表'); ?></a></li>
    </ul>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create('partner', array('class' => 'form-horizontal', 'method' => 'POST')); ?>
            <fieldset>
                <?php echo $this->Form->input('name', array(
                    'label' => '<span class="required">*</span>'.__('合作商名称: '), 
                    'type' => 'text', 
                    'class' => 'input-stand',
                    'required' => 'required',
                ));?>
                <?php echo $this->Form->input('_id', array(
                    'type' => 'hidden', 
                
                ));?>
                <?php if (isset($data['Partner']['name'])) { ?>
                <input type="hidden" value="<?php echo $data['Partner']['name'];?>" name="org_name">
                <?php } ?>
                <div class="form-actions">
                    <?php echo $this->Form->submit('提交', array(
                        'div' => false,
                    	'type'=>'button',
                    		'data-type'=>'submit',
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
<?php $this->start('script'); ?>
<script type="text/javascript">
$(function(){
	  if ( $("#partnerAddForm").length > 0 ) {
	        $('#partnerAddForm').AjaxFormSubmit('/partners/index');
	    }
	    if ( $("#partnerEditForm").length > 0 ) {
	        $('#partnerEditForm').AjaxFormSubmit('/partners/index');
	    }
});
</script>
<?php
$this->end();
?>