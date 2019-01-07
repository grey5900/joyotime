<div class="page-wrapper">
<div class="box-navtool">
	<?php echo $items['created']['sec'];?>
	</div>
	<div class="box-navtool">时长：<?php echo round($items['length']/60,2);?>' &nbsp;&nbsp;过期时间：<?php echo date('Y-m-d',$items['expire']);?></div>
	<ul class="breadcrumb">
		<li><i class="i-left"></i><a href="/coupons/index" class="active">二维码列表</a></li>
	</ul>
	<br />
	<div class="tab-content clearfix active">

		<table class="table table-condensed">
			<thead>
                <?php
																echo $this->Html->tableHeaders(
																		array(
																				array(
																						__('编号')=>array(
																								'width'=>'40%' 
																						) 
																				),
																				
																				array(
																						__('用户')=>array(
																								'width'=>'30%' 
																						) 
																				),
																				array(
																						__('使用时间')=>array(
																								'width'=>'30%' 
																						) 
																				)
																		), array(
																				'class'=>'table-header' 
																		));
																?>
            </thead>
			<tbody>
            <?php foreach($items['codes'] as $idx => $row): ?>
                <?php $this->Coupon->init($row);?>
                <td><?php echo $this->Coupon->code() ?></td>
				<td><?php echo $this->Coupon->codeUseUser() ?></td>
				<td><?php echo $this->Coupon->used() ?></td>
				</tr>
            <?php endforeach; ?>
            </tbody>
		</table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/common_tip'); ?>
<?php

$this->start('header');
echo $this->element('header');
$this->end();
$this->start('sidebar');
echo $this->element('sidebar');
$this->end();
?>
<?php

$this->start('script');
?>
<script type="text/javascript">
$(function() {
    var row = '';
    var delete_data_url ='';
    $('a.delete-hide-link').click(function(){  
    	linkRemove = $(this);
        delete_data_url = $(this).attr('data-url');
    });
    $('#common-hide-modal button.btn-primary').click(function(){
        var btn = $('button.btn-primary');
            btn.text('<?php echo __('正在处理...')?>').attr('disabled',true);
        $.getJSON(delete_data_url, function(data) {
        	if(data.result) {
        		$('.modal').modal('hide');
        		linkRemove.closest("tr").remove();
            } 
            $.messager(data.message);
            btn.text('确定').attr('disabled',false);
        });
    })
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
        var input = $("input[name=title]");
        var form = input.parents('form');
        var title = input.val();
        var url = form.attr('action');
        window.location.href = url + "/?title=" + title;
    }
})
</script>
<?php
$this->end();
?>