<div class="box-content">
	<div class="content-wrapper">
		<ul class="breadcrumb">
		  <li><a href="/salers/"><i class="iconc-left"></i>编辑收银员</a></li>
		</ul>
  		<?php echo $this->Session->flash(); ?>
		<div class="tab-content">
  			<div class="tab-pane active">
      		<?php echo $this->Form->create('Saler', array('class' => 'form-horizontal', 'action' => 'add')); ?>
        		<fieldset>
					<?php echo $this->Form->input('Saler.id'); ?>
					<?php echo $this->Form->input('name', array(
						'label' => '<span class="required">*</span>姓名',
						'type' => 'text',
						'class' => 'span4'
					)); ?>
					
					<?php echo $this->Form->input('shop_id', array(
					    'label' => '<span class="required">*</span>所属门店',
					    'type' => 'select',
					    'options' => $shops,
					))?>
					
               		<?php echo $this->Form->input('contact', array(
	                   'label' => '联系方式',
	                   'type' => 'text',
	                   'class' => 'span6',
               		)); ?>
                        
				    <div class="form-actions">
						<?php echo $this->Form->submit('保存', array(
		  					'div' => false,
		  					'class' => 'btn btn-primary'
						)); ?>
					</div>
        		</fieldset>
        		<?php echo $this->Form->end(); ?> 
			</div><!-- div.tab-pane .active end -->
		</div>
	</div>
</div>

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
?>
<?php 
$this->start('script');
?>
<script type="text/javascript">
$(function(){
   
});
</script>
<?php 
$this->end();
?>