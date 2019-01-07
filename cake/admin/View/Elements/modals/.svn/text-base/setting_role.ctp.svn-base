<div id="setting-role-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo __('为') ?>`<span class="username"></span>`<?php echo __('设置账号类型') ?></h4>
      </div>
      <div class="modal-body">
        <?php 
        echo $this->Form->input('role', array(
			'label' => __('设置为以下哪一种类型？'),
        	'options' => array(
				'admin' 	=> __('管理员'), 
				'user' 		=> __('普通账号'), 
				'checker'	=> __('审核账号'), 
				'freeze' 	=> __('合作导游账号')
			),
			'type' => 'radio',
			'default' => 'user'
        ));
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('关闭') ?></button>
        <button type="button" class="btn btn-primary"><?php echo __('设置'); ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

