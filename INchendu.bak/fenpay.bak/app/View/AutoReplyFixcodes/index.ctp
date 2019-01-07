<div class="page-wrapper">
	<div class="box-navtool">
        <div class="pull-left">
	        <a href="/auto_reply_fixcodes/add" role="button" class="btn btn-primary">新建指令</a>
	        <i class="icon-pentagram"></i><span>被关注自动回复</span>
	        <i class="icon-love"></i><span>回答不上时的回复</span>
        </div>
        <form id="search-query" class="form-search pull-right">
			<input type="text" name="criteria" value="<?php echo $criteria?>" placeholder="搜索指令">
		    <button type="submit"><i class="icon-search"></i></button>
		</form>
   </div>
   
    <table class="table table-condensed">
        <thead>
    		<?php 
				echo $this->Html->tableHeaders(array(
				    array('指令' => array('width' => '10%')),
		    	 	array('回复' => array('width' => '60%')),
		    	 	array('请求次数' => array('width' => '10%')),
		    	 	array('操作' => array('width' => '10%'))
				 ), array('class' => 'table-header'));
			?>
        </thead>
        <tbody>
        <?php foreach($messages as $item): ?>
        <tr class="">
            <td><?php echo implode(', ', Hash::extract($item['AutoReplyKeyword'], '{n}.name'))?>
            <?php if($item['AutoReplyFixcode']['subscribe']) echo '<i class="icon-pentagram"></i>' ?>
            <?php if($item['AutoReplyFixcode']['noanswer']) echo '<i class="icon-love"></i>' ?></td>
            <td rel="substr" class="hide-substr"><?php echo count_news_or_content($item); ?></td>
            <td><?php echo $item['AutoReplyFixcode']['request_total'] ?></td>
            <td>
            	<a href="/auto_reply_fixcodes/edit/<?php echo $item['AutoReplyFixcode']['id']?>"><i class="icon-edit"></i></a> 
        	    <?php echo $this->Link->remove('/auto_reply_fixcodes/delete/', $item['AutoReplyFixcode']['id']); ?>
        	</td>
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
	echo $this->element('left_menu', array('active' => 'auto_reply_fixcode'));
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
	    window.location.href = '/auto_reply_fixcodes/?criteria='+criteria;
	} else {
	    window.location.href = '/auto_reply_fixcodes/';
	}
	return false;
});
//-->
</script>
<?php 
$this->end();
?>

<?php 
function count_news_or_content(&$item) {
    $total = count($item['AutoReplyMessage']);
    if($total > 1) {
        return count($item['AutoReplyMessage']).'条图文';
    } else {
        if($total == 1 && $item['AutoReplyMessage'][0]['type'] == 'text') {
            return $item['AutoReplyMessage'][0]['description'];
        } else {
            return count($item['AutoReplyMessage']).'条图文';
        }
    }
    return '';
}
?>