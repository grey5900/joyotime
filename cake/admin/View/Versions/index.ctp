<div class="page-wrapper">
    <div class="box-navtool">
        <?php echo $this->element('version_tab');?>
        <br />
        <div class="pull-left">
             <a href="/versions/add" role="button" class="btn btn-primary"><?php echo __('发布新版本'); ?></a>
        </div>
    </div>
    <div class="tab-content clearfix active">
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                        array(__('历史版本') => array('width' => '10%')),
                        array(__('平台') => array('width' => '10%')),
                        array(__('版本描述') => array()),
                        array(__('发布时间') => array('width' => '20%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Version->init($row);?>
                <tr class="">
                    <td><?php echo $this->Version->version() ?></td>
                    <td><?php echo $this->Version->platform() ?></td>
                    <td><?php echo $this->Version->description() ?></td>
                    <td><?php echo $this->Version->created() ?></td>
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