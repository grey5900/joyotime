<div class="modal fade" id="edit_category">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>修改分类名称</h3>
			</div>
			<div class="modal-body">
				<form class="form-modal">
				    <div class="control-group">
				        <label class="control-label" for="category_name">
				            <span class="required">*</span>分类名称：
				        </label>
		                <div class="controls">
		                    <input class="input-xlarge" id="category_name" name="AutoReplyCategory[title]" type="text">
		                    <input id="category_id" name="AutoReplyCategory[id]" type="hidden">
		                </div>
				    </div>
				</form>
			</div>
			<div class="modal-footer">
			    <a href="javascript:void(0)" class="btn btn-primary">确定</a>
				<a href="javascript:void(0)" class="btn btn-default" data-dismiss="modal">关闭</a>
			</div>
		</div>
	</div>
</div>

<?php 
$this->start('script');
?>
<script type="text/javascript">
$(function(){
	$('#edit_category a.btn-primary').click(function(e){
		e.preventDefault();	// stop	a in normal way.
		console.log('ss');
		var popup = $('#edit_category');
		var form = $('form', popup);
		var alert = $('.alert', popup);
		$.ajax({
			dataType: "json",
			url: '/auto_reply_categories/save',
			type: 'POST',
			data: form.serialize(),
			success: function(resp) {
				if(resp.result == true)	{
					popup.modal('hide');	
					window.location.reload();
				} else {
				    $.messager(resp.message);
				    alert.removeClass('hide');
				}
			}
		});
		return false;
	});
});
</script>
<?php 
$this->end();
?>