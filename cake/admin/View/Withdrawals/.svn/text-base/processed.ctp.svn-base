<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <ul class="nav nav-tabs">
                <li class="">
                    <div class="nav-tab-active">
                        <a href="/withdrawals"><?php echo __('待处理') ?></a>
                    </div>
                </li>
                <li class="active">
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
                        array(__('处理时间') => array('width' => '10%')),
                        array(__('状态') => array('width' => '8%')),
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
                    <td><?php echo $this->Withdrawal->modified() ?></td>
                    <td>
                        <?php echo $this->Withdrawal->status(); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php // echo $this->element('/modals/unavailable'); ?>

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
    var unavailable_url = '';
    var linkRemove = '';
    // save global data when click invalid link...
    $('a.unavailable-link').click(function(){
        linkRemove = $(this);
        unavailable_url = $(this).attr('data-url');
    });
    // handle click submit comment button in modal...
    $('.modal button.btn-primary').click(function(){
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="comment"]', parent);
        var comment = textarea.val();
        var btn = $('button.btn-primary');
            btn.text('正在下架...').attr('disabled',true);
        $.getJSON(unavailable_url+'?comment='+comment, function(data) {
            $.messager(data.message);
            linkRemove.closest("tr").remove();
            textarea.val('');
            btn.text('下架').attr('disabled',false);
            $('#unavailable-modal').modal('hide');
        });
    })
})
</script>
<?php
$this->end();
?>