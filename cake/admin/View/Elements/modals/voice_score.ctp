<div id="score-hide-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo __('评分并上架') ?><span class="username"></span></h4>
      </div>
      <div class="modal-body" id="score-info">
       <input type="hidden" value="0" id="score-hide">
       <p onmouseover="rate(this,event)">
		<img src="/img/icon_star_1.png" title="很烂">
		<img src="/img/icon_star_1.png" title="一般">
		<img src="/img/icon_star_1.png" title="还好">
		<img src="/img/icon_star_1.png" title="较好">
		<img src="/img/icon_star_1.png" title="很好">
		</p>
       	<input type="hidden" value="0" id="score" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('关闭') ?></button>
        <button type="button" class="btn btn-primary"><?php echo __('上架'); ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

