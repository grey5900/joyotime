<div class="box-content">
	<div class="content-wrapper">
		<div class="tab-content">
			<div class="tab-pane active">
				<div class="operate-box">
					<?php 
						echo $this->Session->flash(); 
						echo $this->element('message');
					?>
		            <div class="pull-left">
		    	        <a href="#edit_shop" data-toggle="modal" class="btn btn-primary">新建门店</a>
		            </div>
			        <div class="pull-right">
			        </div>
	           </div>
	            <table class="table table-bordered table-striped">
		            <thead>
	            		<?php 
							echo $this->Html->tableHeaders(array(
    						    array('门店' => array('width' => '30%')),
    				    	 	array('收银员总数' => array('width' => '20%')),
    				    	 	array('兑换码总数' => array('width' => '20%')),
    				    	 	array('无效码总数' => array('width' => '10%')),
    				    	 	array('操作' => array('width' => '10%'))
							),array('class' => 'table-header'));
						?>
		            </thead>
		            <tbody>
	                <?php foreach($shops as $item): ?>
	                <tr class="">
	                    <td><?php echo $item['Shop']['name']?></td>
	                    <td><?php echo $item['Shop']['saler_total']?></td>
	                    <td><?php echo $item['Shop']['coupon_total']?></td>
	                    <td><?php echo $item['Shop']['invalid_coupon_total'] ?></td>
	                    <td>
	                        <a class="edit-link" data='<?php echo json_encode($item['Shop']); ?>' sid="<?php echo $item['Shop']['id'] ?>">
	                		    <i class="icon-edit"></i>编辑</a>
	                        <?php echo $this->Link->remove('/shops/delete/', $item['Shop']['id'])?>
	                    </td>
	                </tr>
	                <?php endforeach; ?>
	                </tbody>
	            </table>	
<?php echo $this->element('paginator'); ?>	 
			</div><!-- div.tab-pane .active end -->
		</div>
	</div>
</div>
<?php echo $this->element('/modals/delete'); ?>	
<?php echo $this->element('/modals/edit_shop'); ?>

<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
	$this->start('left_menu');
	echo $this->element('left_menu', array('active' => 'shop'));
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
	$this->start('script');
?>
<script type="text/javascript">
<!--
$('#filter').change(function(){
	var sel = $(this);
	var val = sel.val();
	if(!val) {
	    val = 0;
	}
	var url = '/auto_reply_messages/index/'+val;
    window.location.href = url;
	return false;
});

$('.edit-link').click(function(){
	shop_edit(this);
});

function shop_edit(o) {
	var alert = $('#edit_shop');
  	var data = JSON.parse($(o).attr('data'));
  	if(data) {
    	$('#edit_shop input#ShopName').val(data.name);
    	$('#edit_shop input#ShopId').val(data.id);
  	}
  	alert.modal('show');
}
//-->
</script>
<?php 
$this->end();
?>