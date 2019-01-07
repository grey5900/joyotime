<?php APP::uses('Voice', 'Model')?>
<?php 
$path = $this->request->query('url');
?>
<div class="accordion" id="accordion">
	<div class="accordion-group">
		<div id="collapseOne" class="accordion-body collapse in">
			<div class="accordion-inner">
				<div class="<?php echo $path == 'voices/index' || 
				    $path == 'voices/index/'.Voice::STATUS_APPROVED || 
				    $path == 'voices/index/'.Voice::STATUS_UNAVAILABLE ||
				    $path == 'voices' ? 'active' : ''?>">
					<a href="/voices/index"><?php echo __('Voice List') ?></a>
				</div>
				<div class="<?php echo $path == 'voices/index/'.Voice::STATUS_PENDING
				    || $path == 'voices/index/'.Voice::STATUS_INVALID ? 'active' : ''?>">
					<a href="/voices/index/<?php echo Voice::STATUS_PENDING ?>"><?php echo __('Approve voice') ?></a>
				</div>
				<div class="<?php echo $path == 'withdrawals/index'
                    || $path == 'withdrawals'
                    || $path == 'withdrawals/no_process_yet'
				    || $path == 'withdrawals/processed' ? 'active' : ''?>">
					<a href="/withdrawals/index"><?php echo __('提现管理') ?></a>
				</div>
				<div class="<?php echo $path == 'users/index'
                    || $path == 'users' ? 'active' : ''?>">
					<a href="/users/index"><?php echo __('用户列表') ?></a>
				</div>
				<div class="<?php echo $path == 'broadcasts/add' 
                    || $path == 'broadcasts/index' ? 'active' : ''?>">
					<a href="/broadcasts/add"><?php echo __('推送消息') ?></a>
				</div>
				<div class="<?php echo $path == 'gifts/add' 
                    || $path == 'gifts/index' ? 'active' : ''?>">
					<a href="/gifts/add"><?php echo __('赠送时长') ?></a>
				</div>
				<div class="<?php echo $path == 'reports' 
                    || $path == 'reports/index' ? 'active' : ''?>">
					<a href="/reports/index"><?php echo __('举报管理') ?></a>
				</div>
				<div class="<?php echo $path == 'receipts' 
                    || $path == 'receipts/index' ? 'active' : ''?>">
					<a href="/receipts/index"><?php echo __('订单管理') ?></a>
				</div>
				<div class="<?php echo $path == 'comments' 
                    || $path == 'comments/index' ? 'active' : ''?>">
					<a href="/comments/index"><?php echo __('评论管理') ?></a>
				</div>
				<div class="<?php echo $path == 'histories' 
                    || $path == 'histories/index' ? 'active' : ''?>">
					<a href="/histories/index"><?php echo __('操作记录') ?></a>
				</div>
			</div>
		</div>
	</div>
</div>