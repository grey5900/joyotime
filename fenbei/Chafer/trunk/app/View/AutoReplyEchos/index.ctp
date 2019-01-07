<div class="page-wrapper">
	<div class="tab-content clearfix active">
		<div class="box-navtool">
			<div class="alert alert-error hide"></div>
            <div class="pull-left">
            	<a href="/auto_reply_echos/add" role="button" class="btn btn-primary">新增接口</a>
				<div class="well well-small">
		           <span class="label label-info">说明</span>                 
		           只支持微信标准接口，互动时优先返回第三方接口的回复
		        </div>
            </div>
       	</div>
		<table class="table table-condensed">
            <thead>
        		<?php 
					echo $this->Html->tableHeaders(array(
				    array('接口地址' => array('class' => 'span2')),
		    	 	array('触发条件' => array('class' => 'span2')),
		    	 	array('请求次数' => array('class' => 'span2')),
		    	 	array('操作' => array('class' => 'span1'))
					),array('class' => 'table-header'));
				?>
            </thead>
            <tbody>
	            <?php foreach ($messages as $item): ?>
	            <tr class="">
	                <td><?php echo $item['AutoReplyEcho']['url']?></td>
	                <td><?php echo implode('<br />', getConditions($item))?></td>
	                <td><?php echo $item['AutoReplyEcho']['sent_num']?></td>
	                <td>
	               	    <?php 
	               	    	echo $this->Html->link('<i class="icon-edit"></i>', '/auto_reply_echos/edit/'.$item['AutoReplyEcho']['id'], array(
					            'escape' => false
					        ));
	               	    ?>
	               	    <?php echo $this->Link->remove('/auto_reply_echos/delete/', $item['AutoReplyEcho']['id']); ?>
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
	echo $this->element('left_menu', array('active' => 'third_interface'));
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
	$this->start('script');
?>
<script type="text/javascript">
<!--
//-->
</script>
<?php 
$this->end();

function getConditions(&$item = array()) {
    $conditions = array();
    if($item['AutoReplyEcho']['enabled_regexp']) $conditions[] = '正则表达式';
    if($item['AutoReplyEcho']['enabled_location']) $conditions[] = '地理位置';
    return $conditions;
}
?>