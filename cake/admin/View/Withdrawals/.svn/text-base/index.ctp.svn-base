<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="/withdrawals"><?php echo __('待处理') ?></a>
                    </div>
                </li>
                <li class="">
                    <div class="nav-tab-active">
                        <a href="/withdrawals/processed"><?php echo __('已处理') ?></a>
                    </div>
                </li>
            </ul>
        </div>
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                        array(__('兑换分钟数') => array('width' => '8%')),
                        array(__('金额') => array('width' => '8%')),
                        array(__('账户') => array('width' => '20%')),
                        array(__('用户') => array('width' => '10%')),
                        array(__('申请时间') => array('width' => '10%')),
                        array(__('操作') => array('width' => '8%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Withdrawal->init($row);?>
                <tr class="">
                    <td><?php echo $this->Withdrawal->amount() ?></td>
                    <td><?php echo $this->Withdrawal->money() ?></td>
                    <td><?php echo $this->Withdrawal->account() ?></td>
                    <td><?php echo $this->Withdrawal->user()->username() ?></td>
                    <td><?php echo $this->Withdrawal->created() ?></td>
                    <td>
                        
                        <?php echo $this->Withdrawal->revert() ?>
                        <?php echo $this->Html->link(__('已处理'), '/withdrawals/process/'.$this->Withdrawal->id())?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/withdrawal_revert'); ?>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
?>

<?php 
$this->start('script'); ?>
<script type="text/javascript">
$(function() {
    // submit comment as reason for why invalid that one...
    var link = '';
    var linkRemove = '';
    // save global data when click invalid link...
    $('a.withdrawal-revert-link').click(function(){
        linkRemove = $(this);
        link = $(this).attr('data-url');
    });
    // handle click submit comment button in modal...
    $('.modal button.btn-primary').click(function(){
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="comment"]', parent);
        var comment = textarea.val();
        var btn = $('button.btn-primary');
            btn.text('<?php echo __('正在驳回...') ?>').attr('disabled',true);
        $.getJSON(link+'?reason='+comment, function(data) {
            // if success...
            if(data.result) {
            	linkRemove.closest("tr").remove();
            	textarea.val('');
            	$('#withdrawal-revert-modal').modal('hide');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('驳回') ?>').attr('disabled',false);
        });
    })
})
</script>
<?php
$this->end();
?>