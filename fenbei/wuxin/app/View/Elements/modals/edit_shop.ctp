<div class="modal in hide" id="edit_shop">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>编辑门店信息</h3>
	</div>
	<div class="modal-body">
	    <div class="alert alert-error hide">
	    	<button type='button' class='close' data-dismiss='alert'>×</button>
            <span class="append-text"></span>
        </div>
		<?php echo $this->Form->create('Shop', array('class' => 'form-horizontal', 'action' => 'add')); ?>
		<?php echo $this->Form->input('name', array(
		    'label' => '<span class="required">*</span>门店名称',
		))?>
		<?php echo $this->Form->hidden('id'); ?>
		<?php echo $this->Form->end(); ?>
	</div>
	<div class="modal-footer">
	    <a href="javascript:void(0)" class="btn btn-primary">确定</a>
		<a href="#" class="btn" data-dismiss="modal">关闭</a>
	</div>
</div>

<?php 
$this->start('script');
?>
<script type="text/javascript">
$(function(){
	$('#edit_shop a.btn-primary').click(function(e){
		e.preventDefault();	// stop	a in normal way.
		var popup = $('#edit_shop');
		var form = $('form', popup);
		var alert = $('.alert', popup);
		$.ajax({
			dataType: "json",
			url: '/shops/add',
			type: 'POST',
			data: form.serialize(),
			success: function(resp) {
				if(resp.result == true)	{
					popup.modal('hide');	
					window.location.reload();
				} else {
				    $('span.append-text', alert).text(resp.message);
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