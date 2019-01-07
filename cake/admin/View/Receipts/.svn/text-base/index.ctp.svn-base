<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('订单号') => array('width' => '')),
                    array(__('充值时间') => array('width' => '')),
                    array(__('金额') => array('width' => '')),
                    array(__('用户') => array('width' => '')),
                    array(__('来源') => array('width' => '')),
                    array(__('时间') => array('width' => ''))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Receipt->init($row); ?>
                <tr class="">
                    <td><?php echo $this->Receipt->id() ?></td>
                    <td><?php echo $this->Receipt->amount(); ?></td>
                    <td><?php echo $this->Receipt->price(); ?><?php echo $this->Receipt->exception(); ?></td>
                    <td><?php echo $this->Receipt->User->username(); ?></td>
                    <td><?php echo $this->Receipt->type(); ?></td>
                    <td><?php echo $this->Receipt->created() ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
?>