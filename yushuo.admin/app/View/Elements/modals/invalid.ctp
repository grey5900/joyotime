<div id="invalidmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo __('Invalid Reason') ?></h4>
      </div>
      <div class="modal-body">
        <textarea rows="5" name="comment" placeholder="<?php echo __('请填写驳回理由...') ?>"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close') ?></button>
        <button type="button" class="btn btn-primary btn-invalid"><?php echo __('Invalid it now !'); ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
