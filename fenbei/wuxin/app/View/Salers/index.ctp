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
		    	        <a href="/salers/add" class="btn btn-primary">新建收银员</a>
		            </div>
			        <div class="pull-right">
			        </div>
	           </div>
	            <table class="table table-bordered table-striped">
		            <thead>
	            		<?php 
							echo $this->Html->tableHeaders(array(
    						    array('姓名' => array('width' => '30%')),
    				    	 	array('所属门店' => array('width' => '20%')),
    				    	 	array('微信身份认证' => array('width' => '20%')),
    				    	 	array('联系方式' => array('width' => '10%')),
    				    	 	array('微信绑定码' => array('width' => '10%')),
    				    	 	array('操作' => array('width' => '10%'))
							),array('class' => 'table-header'));
						?>
		            </thead>
		            <tbody>
	                <?php foreach($salers as $item): ?>
	                <tr class="">
	                    <td><?php echo $item['Saler']['name']?></td>
	                    <td><?php echo $item['Shop']['name']?></td>
	                    <td><?php echo empty($item['Saler']['open_id']) ? '未认证' : '已认证'?></td>
	                    <td><?php echo $item['Saler']['contact'] ?></td>
	                    <td><?php echo $item['Saler']['token'] ?></td>
	                    <td>
	                        <a href="/salers/edit/<?php echo $item['Saler']['id'] ?>">编辑</a> 
	                        <?php echo $this->Link->remove('/salers/delete/', $item['Saler']['id'])?>
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

<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
	$this->start('left_menu');
	echo $this->element('left_menu', array('active' => 'saler'));
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
//-->
</script>
<?php 
$this->end();
?>