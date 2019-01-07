<div id="send-gift-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo __('赠送时长给') ?><span class="username"></span></h4>
      </div>
      <div class="modal-body">
        <?php echo $this->Form->input('minutes', array(
            'label' => false, 
            'type' => 'text', 
            'name' => 'minutes',
            'class' => 'input-modal-small',
            'placeholder' => __('请填写赠送时长数额...'),
            'helpInline' => __('（分钟）'),
        ));?>
        <textarea rows="5" name="message" placeholder="<?php echo __('请写下想发给用户的话...'); ?>"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('关闭') ?></button>
        <button type="button" class="btn btn-primary"><?php echo __('赠送'); ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

