<div class="page-wrapper">
	<?php //echo $this->element('auto_reply_tabs', array('active' => 'hybird')); ?>
	<div class="box-navtool">
        <div class="pull-left">
	        <a href="/auto_reply_messages/add" role="button" class="btn btn-primary">新建图文内容</a>
	        <a href="/auto_reply_categories" role="button" class="btn btn-default">分类管理</a>
        </div>
        <ul class="pull-right">
	        <li>
	        	<?php 
		            echo $this->Form->input('filter', array(
		                'label' => false,
						'type' => 'select',
						'options' => $cates,
		                'empty' => '所有分类',
		                'default' => $filter,
	                    'class' => 'input-small',
	                    'div' => false
		    		)); 
	    		?>
	        </li>
        	<li>
        		<form id="search-query" class="form-search">
	        	    <input type="text"name="criteria" value="<?php echo $criteria?>" placeholder="搜索标签和标题">
	        	    <button type="submit"><i class="icon-search"></i></button>
	        	</form>
        	</li>
        </ul>
   </div>
    <table class="table table-condensed">
        <thead>
    		<?php 
				echo $this->Html->tableHeaders(array(
			    array('图文标题' => array('width' => '30%')),
	    	 	array('标签' => array('width' => '20%')),
	    	 	array('分类' => array('width' => '20%')),
	    	 	array('请求次数' => array('width' => '10%')),
	    	 	array('查看次数' => array('width' => '10%')),
	    	 	array('操作' => array('width' => '10%'))
						),array('class' => 'table-header'));
			?>
        </thead>
        <tbody>
        <?php foreach($messages as $item): ?>
        <tr class="">
            <td><?php echo $item['AutoReplyMessageNews']['title']?></td>
            <td><?php echo implode(', ', Hash::extract($item['AutoReplyTag'], '{n}.name'))?></td>
            <td><?php echo !empty($item['AutoReplyMessageNews']['AutoReplyCategory']['title']) ? $item['AutoReplyMessageNews']['AutoReplyCategory']['title'] : '未分类' ?></td>
            <td><?php echo $item['AutoReplyMessage']['request_total'] ?></td>
            <td><?php echo $item['AutoReplyMessageNews']['view_total'] ?></td>
            <td>
            	<a href="/auto_reply_messages/edit/<?php echo $item['AutoReplyMessage']['id']?>"><i class="icon-edit"></i></a> 
        	    <?php echo $this->Link->remove('/auto_reply_messages/delete/', $item['AutoReplyMessage']['id'])?>
        	</tr>
        <?php endforeach; ?>
        </tbody>
    </table>	
	<?php echo $this->element('paginator'); ?>	 
</div>
	
<?php echo $this->element('/modals/delete'); ?>

<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
	$this->start('left_menu');
	echo $this->element('left_menu', array('active' => 'auto_repdives_kw'));
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
$('#search-query').submit(function(){
	var criteria = $('input[name="criteria"]', $(this)).val();
	if(criteria) {
	    window.location.href = '/auto_reply_messages/?criteria='+criteria;
	} else {
	    window.location.href = '/auto_reply_messages/';
	}
	return false;
});
//-->
</script>
<?php // echo $this->Js->writeBuffer(); ?>
<?php 
$this->end();
?>