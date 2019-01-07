<?php 
// debug($messages);
?>
<div class="page-wrapper">
		<?php echo $this->element('auto_reply_location_tabs', array('active' => 'index')); ?>
		<div class="tab-content clearfix active">
			<div class="box-navtool">
	            <div class="pull-left">
	    	        <a href="/auto_reply_locations/add" role="button" class="btn btn-primary">新建地点</a>
	            </div>
                <form id="search-query" class="form-search pull-right">
		            <input type="text" name="criteria" value="<?php echo $criteria?>" placeholder="搜索地点名称">
		            <button type="submit"><i class="icon-search"></i></button>
		        </form>
           </div>
           <table class="table table-condensed">
	            <thead>
            		<?php 
						echo $this->Html->tableHeaders(array(
					    array('地点名称' => array('width' => '30%')),
			    	 	array('地点描述' => array('width' => '30%')),
			    	 	array('扩展信息' => array('width' => '20%')),
			    	 	array('请求次数' => array('width' => '10%')),
			    	 	array('操作' => array('width' => '10%'))
					    ),array('class' => 'table-header'));
					?>
	            </thead>
	            <tbody>
                <?php foreach($messages as $item): ?>
                <tr class="">
                    <td><?php echo $item['AutoReplyLocation']['title']?></td>
                    <td><?php echo $item['AutoReplyLocation']['description']?></td>
                    <td><?php echo count($item['AutoReplyLocationMessage'])?></td>
                    <td><?php echo $item['AutoReplyLocation']['request_total']?></td>
                    <td>
                    	<a href="/auto_reply_locations/edit/<?php echo $item['AutoReplyLocation']['id']?>"><i class="icon-edit"></i></a> 
                        <?php echo $this->Link->remove('/auto_reply_locations/delete/', $item['AutoReplyLocation']['id'])?>
                    </td>
                </tr>
                <?php endforeach; ?>
             </tbody>
            </table>
            <?php echo $this->element('paginator'); ?>		 
		</div>
	</div>
<?php echo $this->element('/modals/delete'); ?>
<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
	$this->start('left_menu');
	echo $this->element('left_menu', array('active' => 'auto_repdives_geo'));
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
	$this->start('script');
?>
<script type="text/javascript">
<!--
$('#search-query').submit(function(){
	var criteria = $('input[name="criteria"]', $(this)).val();
	if(criteria) {
	    window.location.href = '/auto_reply_locations/?criteria='+criteria;
	} else {
	    window.location.href = '/auto_reply_locations/';
	}
	return false;
});
//-->
</script>
<?php 
$this->end();
?>