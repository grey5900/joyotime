<div class="page-wrapper">
    <div class="box-navtool">
        <?php echo $this->element('version_tab');?>
    </div>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create(null, array('class' => 'form-horizontal')); ?>
        <fieldset>
            <?php echo $this->Form->input('platform', array(
                'label' => '<span class="required">*</span>'.__('平台：'), 
                'type' => 'select',
            	'options' => array(
            		'android' => 'Android',
					'ios' => 'iOS'
				), 
                'required' => 'required'
            ));?>
            <?php echo $this->Form->input('version', array(
                'label' => '<span class="required">*</span>'.__('版本号：'), 
                'type' => 'text', 
                'required' => 'required'
            ));?>
            <?php echo $this->Form->input('description', array(
                'label' => __('版本描述：'), 
                'type' => 'textarea'
            ));?>
            
            <div class="form-actions">
                <?php echo $this->Form->submit(__('立即发布'), array(
                    'div' => false,
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