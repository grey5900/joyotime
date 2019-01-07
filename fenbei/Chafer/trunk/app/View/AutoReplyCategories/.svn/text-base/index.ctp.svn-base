<div id="auto_reply_category" class="page-wrapper">
	<ul class="breadcrumb">
	  <li><i class="i-left"></i><a href="/auto_reply_messages/" class="active">分类管理</a></li>
	</ul>
	<div class="tab-content clearfix active">
	    	<div class="box-navtool">
		        <div class="pull-left">
	    	        <a href="#add_category" role="button" class="btn btn-primary" data-toggle="modal">新建分类</a>
	            </div>
	        </div>
	        <div class="mainway">
	        	<ul class="box-well">
	        		<?php foreach($cates as $cate): ?>
	        		<li>
	        			<div class="well well-large">
				            <h3><?php echo $cate['AutoReplyCategory']['title'] ?></h3>
				            <span class="detail">
				                (<?php if($cate['AutoReplyCategory']['total']): ?>
				                <?php echo $cate['AutoReplyCategory']['total']?>条图文
				                <?php else: ?>
                                	分类下无内容
				                <?php endif; ?>)
				            </span> 
				            <div class="overlay">
				                <span>
				                	<a class="edit-cate-link" href="#edit_category" data='<?php echo json_encode($cate['AutoReplyCategory']); ?>' cid="<?php echo $cate['AutoReplyCategory']['id'] ?>">
				                		<i class="icon-edit"></i>
				                	</a>
				                </span>
				                <span>
				                	<?php echo $this->Link->remove('/auto_reply_categories/delete/', $cate['AutoReplyCategory']['id']); ?>
				                </span>
				            </div>
				        </div> 
	        		</li>
	        		<?php endforeach; ?>
	        	</ul>
	        </div>
		</div>
  </div>

<?php echo $this->element('/modals/auto_replies/add_category'); ?>
<?php echo $this->element('/modals/auto_replies/edit_category'); ?>
<?php echo $this->element('/modals/delete'); ?>

<?php 
$this->start('top_nav');
echo $this->element('top_nav');
$this->end();
$this->start('left_menu');
echo $this->element('left_menu', array('active' => 'auto_replies_kw'));
$this->end();
$this->start('footer');
echo $this->element('footer');
$this->end();
$this->start('script');
?>
<script type="text/javascript">
<!--
$(function(){
	// $('.well-block').hover(function(){
	    // $('.overlay', this).show();
	// }, function(){
	    // $('.overlay', this).hide();
	// });
	
	$('.edit-cate-link').click(function(){
	    auto_reply_category_edit(this);
	});

	function auto_reply_category_edit(o) {
    	var alert = $('#edit_category');
      	var id = $(o).attr('cid');
      	var data = JSON.parse($(o).attr('data'));
      	if(id && data) {
        	$('#edit_category input#category_name').val(data.title);
        	$('#edit_category input#category_id').val(data.id);
      	}
      	alert.modal('show');
	}
});
//-->
</script>
<?php 
$this->end();
?>