<div class="page-wrapper">
    <div class="box-navtool">
        <ul class="nav nav-tabs">
            <li class="active">
                <div class="nav-tab-active">
                    <a href="/gifts/add"><?php echo __('赠送时长') ?></a>
                </div>
            </li>
            <li>
                <div class="nav-tab-active">
                    <a href="/gifts/index"><?php echo __('赠送历史') ?></a>
                </div>
            </li>
        </ul>
    </div>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create(null, array('class' => 'form-horizontal', 'action' => '/send')); ?>
        <fieldset>
            <?php echo $this->Form->input('seconds', array(
                'label' => '<span class="required">*</span>'.__('赠送数额：'), 
                'type' => 'text', 
                'required' => 'required',
                'class' => 'input-modal-small',
                'helpInline' => '(分钟)'
            ));?>
            
            <?php echo $this->Form->input('message', array(
                'label' => '<span class="required">*</span>'.__('消息内容：'), 
                'type' => 'textarea', 
                'required' => 'required',
            ));?>
            
            <div class="form-actions">
                <?php echo $this->Form->submit(__('赠送'), array(
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