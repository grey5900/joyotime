<div class="page-wrapper">
	<?php echo $this->element('weixin_configs_tabs', array('active' => 'add')); ?>
	<div class="tab-content clearfix active">
		<div class="mainway col-7">
			<?php echo $this->Form->create('WeixinConfig', array('class' => 'form-horizontal', 'action' => 'add', 'novalidate' => true)); ?>
			    <fieldset>
			        <?php echo $this->Form->input('WeixinConfig.name', array(
			            'label' => '<span class="required">*</span>公众号名称:',
			            'type' => 'text',
			            'class' => 'input-stand',
			            'prepend' => '',
			            'value' => isset($config['WeixinConfig']['name']) ? 
			                $config['WeixinConfig']['name'] : '',
			        ))?>
			        <?php echo $this->Form->input('weixin_id', array(
			            'label' => '<span class="required">*</span>微信号:',
			            'type' => 'text',
			            'class' => 'input-stand',
			            'value' => isset($config['WeixinConfig']['weixin_id']) ?
			                $config['WeixinConfig']['weixin_id'] : '',
			        ))?>
			        <?php echo $this->Form->input('initial_user_num', array(
			            'label' => '微信用户数:',
			            'type' => 'text',
			            'append' => '个',
			            'value' => isset($config['WeixinConfig']['initial_user_num']) ?
			                $config['WeixinConfig']['initial_user_num'] : '0',
			        ))?>
			        <?php echo $this->Form->input('id', array(
			            'type' => 'hidden',
			            'value' => isset($config['WeixinConfig']['id']) ? $config['WeixinConfig']['id'] : ''
			        ))?>
				
				    <div class="form-actions">
					    <?php echo $this->Form->submit('保存', array(
        					'div' => false,
        					'class' => 'btn btn-primary',
        				)); ?>
					</div>
					<?php if(isset($config['WeixinConfig']['token'])): ?>
					<?php endif; ?>
			 </fieldset>
			<?php echo $this->Form->end(); ?>  
 		</div><!-- div.col-9>form end -->
 		<div class="causeway col-5">
 			<div class="well">
			    <h3>接口配置信息</h3>
			    <div><span class="bold">URL: </span><?php echo $config['WeixinConfig']['interface']?></div>
			    <div><span class="bold">Token: </span><?php echo $config['WeixinConfig']['token']?></div>
			</div>
		 </div>
	</div>
</div>

<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
	$this->start('left_menu');
	echo $this->element('left_menu', array('active' => 'weixin_config'));
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
?>