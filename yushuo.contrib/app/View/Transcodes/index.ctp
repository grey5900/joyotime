<?php 
if(!$items) {
    $items = array();
}
?>
<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
            <div class="alert alert-error hide"></div>
            <div class="pull-left">
                <h3>正在转码列表</h3>
            </div>
        </div>
        
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('解说') => array('width' => '40%')),
                    array(__('时长') => array('width' => '10%')),
                    array(__('提交时间') => array('width' => '20%')),
                    array(__('状态') => array('width' => '10%')),
                    array(__('操作') => array('width' => '20%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php 
            foreach($items as $idx => $row): 
                $this->Transcode->init($row);
            ?>
                <tr class="">
                    <td><?php echo $this->Transcode->title(); ?></td>
                    <td><?php echo $this->Transcode->length(); ?></td>
                    <td><?php echo $this->Transcode->created(); ?></td>
                    <td><?php echo $this->Transcode->status(); ?></td>
                    <td><?php echo $this->Transcode->retry(); ?></td>
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
<?php 
$this->start('script'); ?>
<script type="text/javascript">
$(function() {
    
})
</script>
<?php
$this->end();
?>
