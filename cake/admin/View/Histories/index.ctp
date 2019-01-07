<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtools">
            <form class="navbar-form navbar-right" name="filter" action="/histories" method="GET">
              <div class="form-group">
                <input type="text" name="action" class="input-search" placeholder="controller/action" value="<?php echo $param; ?>">
              </div>
              <a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
            </form>
        </div>
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('登录名') => array('width' => '')),
                    array(__('动作') => array('width' => '')),
                    array(__('请求路径') => array('width' => '')),
                    array(__('提交数据') => array('width' => '35%')),
                    array(__('时间') => array('width' => '')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->History->init($row); ?>
                <tr class="">
                    <td><?php echo $this->History->username() ?></td>
                    <td><?php echo $this->History->method(); ?></td>
                    <td><?php echo $this->History->query('url'); ?></td>
                    <td><?php echo $this->History->data(); ?></td>
                    <td><?php echo $this->History->created(); ?></td>
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
    $('#search_btn').click(function(){
        $(this).parents('form').submit();
    });
})
</script>
<?php
$this->end();
?>