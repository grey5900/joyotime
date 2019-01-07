<div class="modal fade" id="add_category">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>新建分类名称</h3>
			</div>
			<div class="modal-body">
				<form class="form-modal">
				    <div class="control-group">
				        <label class="control-label" for="category_name">
				            <span class="required">*</span>分类名称：
				        </label>
		                <div class="controls">
		                    <input class="input-xlarge" id="category_name" name="AutoReplyCategory[title]" type="text">
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
	$('#add_category a.btn-primary').click(function(e){
		e.preventDefault();	// stop	a in normal way.
		var popup = $('#add_category');
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