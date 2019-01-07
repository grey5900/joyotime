<div class="page-wrapper">
    <div class="box-navtool">
        <ul class="nav nav-tabs">
            <li>
                <div class="nav-tab-active">
                    <a href="/broadcasts/add"><?php echo __('推送消息') ?></a>
                </div>
            </li>
            <li class="active">
                <div class="nav-tab-active">
                    <a href="/broadcasts/index"><?php echo __('历史消息') ?></a>
                </div>
            </li>
        </ul>
    </div>
    <div class="tab-content clearfix active">
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                        array(__('消息内容') => array('width' => '8%')),
                        array(__('语言') => array('width' => '8%')),
                        array(__('推送对象') => array('width' => '10%')),
                        array(__('推送时间') => array('width' => '10%')),
                        array(__('状态') => array('width' => '10%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Broadcast->init($row);?>
                <tr class="">
                    <td><?php echo $this->Broadcast->message() ?></td>
                    <td><?php echo $this->Broadcast->locale() ?></td>
                    <td><?php echo $this->Broadcast->user() ?></td>
                    <td><?php echo $this->Broadcast->created() ?></td>
                    <td><?php echo $this->Broadcast->read_total() ?></td>
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