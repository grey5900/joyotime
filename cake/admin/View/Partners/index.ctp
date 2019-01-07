<div class="page-wrapper">
    
<?php 
    $this->Partner->rows = $items;
   
?>
   
    <div class="tab-content clearfix active">
       <div class="box-navtool">
			<ul>
				<li><a class="btn btn-primary"
					href="<?php echo $this->Partner->addLink(); ?>"><?php echo __('新建内容合作商') ?></a>
				</li>
			</ul>
			<form class="navbar-form navbar-right" name="search"
				action="/partners/index" method="get">
				<div class="form-group">
					<input type="text" name="title" class="input-search"
						placeholder="合作商名称搜索" value="<?php echo $kw; ?>">
				</div>
				<a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
			</form>
		</div>
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('合作商名称') => array('width' => '25%')),
                    array(__('关联帐号') => array()),
                    array(__('鱼说数')=> array()),
                    array(__('总时长')=> array()),
                    array(__('销量') => array('width' => '10%')),
                    array(__('播放次数')=> array()),
                    array(__('总收入')=> array()),
                    array(__('Action') => array('width' => '15%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Partner->init($row); ?>
                <tr class="">
                    <td>
                        <?php echo $this->Partner->name(); ?>
                    </td>
                    <td><?php echo $this->Partner->relationUserNum(); ?></td>
                    <td><?php echo $this->Partner->voiceNum();?></td>
                    <td><?php echo $this->Partner->timeLength() ?></td>
                    <td><?php echo $this->Partner->saleNum() ?> </td>
                    <td><?php echo $this->Partner->playNum() ?></td>
                    <td><?php echo $this->Partner->earnTotal() ?></td>
                    <td><?php echo $this->Partner->statisticsLink() ?> <?php echo $this->Partner->editLink() ?> <?php echo $this->Partner->relationUserLink() ?></td>
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
	 // Search...
    $("#search_btn").on("click", function(){
        submit_search();
        return false;
    });
    
    $("form[name=search]").on("submit", function(){
        submit_search();
        return false;
    });
    function submit_search(){
        var kw = $("input[name=title]").val();
        window.location.href = "/partners/index/" + kw;
    }
})
</script>
<?php
$this->end();
?>