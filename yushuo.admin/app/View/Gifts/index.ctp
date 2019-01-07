<div class="page-wrapper">
    <div class="box-navtool">
        <ul class="nav nav-tabs">
            <li>
                <div class="nav-tab-active">
                    <a href="/gifts/add"><?php echo __('赠送时长') ?></a>
                </div>
            </li>
            <li class="active">
                <div class="nav-tab-active">
                    <a href="/gifts/index"><?php echo __('赠送历史') ?></a>
                </div>
            </li>
        </ul>
    </div>
    <div class="tab-content clearfix active">
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                        array(__('赠送时长') => array('width' => '8%')),
                        array(__('赠送消息') => array('width' => '8%')),
                        array(__('语言') => array('width' => '8%')),
                        array(__('赠送对象') => array('width' => '10%')),
                        array(__('赠送时间') => array('width' => '10%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->GiftLog->init($row);?>
                <tr class="">
                    <td><?php echo $this->GiftLog->time() ?></td>
                    <td><?php echo $this->GiftLog->message() ?></td>
                    <td><?php echo $this->GiftLog->language() ?></td>
                    <td><?php echo $this->GiftLog->user() ?></td>
                    <td><?php echo $this->GiftLog->created() ?></td>
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