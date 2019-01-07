<div class="page-wrapper">
	<?php echo $this->element('auto_reply_location_tabs', array('active' => 'extend')); ?>
	<div class="tab-content clearfix active">
		<div class="box-navtool">
	        <div class="pull-left">
		        <a href="/auto_reply_location_extends/add" role="button" class="btn btn-primary">新建扩展信息</a>
	        </div>
	        <form id="search-query" class="form-search pull-right">
	            <input type="text" name="criteria" value="<?php echo $criteria?>" placeholder="搜索文章标题和地点名称">
	            <button type="submit"><i class="icon-search"></i></button>
	        </form>
	   	</div>
	   	
   		<table class="table table-condensed">
            <thead>
                <?php
                echo $this->Html->tableHeaders(array(
                    array('扩展信息' => array('width' => '30%')),
                    array('关联地点' => array('width' => '60%')),
                    array('操作' => array('width' => '10%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            	<?php foreach($messages as $item): ?>
                <tr class="">
                    <td>
                    	<?php echo $item['AutoReplyMessageNews']['title']?>
                    </td>
                	<td>
                    	<ul>
	                    	<?php 
	                    		foreach($item['AutoReplyLocation'] as $loc):
	                       		echo '<li>'.$loc['title'].'</li>';	                    
	                    		endforeach;
	                    	?>
                    	</ul>
                	</td>
                	<td>
                		<a href="/auto_reply_location_extends/edit/<?php echo $item['AutoReplyMessage']['id']?>"><i class="icon-edit"></i></a>
                	    <?php echo $this->Link->remove('/auto_reply_location_extends/delete/', $item['AutoReplyMessage']['id'])?>
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
	    window.location.href = '/auto_reply_location_extends/?criteria='+criteria;
	} else {
	    window.location.href = '/auto_reply_location_extends/';
	}
	return false;
});
//-->
</script>
<?php 
$this->end();
?>