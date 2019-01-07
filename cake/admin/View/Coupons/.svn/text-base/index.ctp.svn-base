<div class="page-wrapper">
	<div class="tab-content clearfix active">
		<div class="box-navtool">
			<ul>
				<li>
				<?php if($role=='admin') { ?>
				<a class="btn btn-primary" href="<?php echo $this->Coupon->addList($kw); ?>"><?php echo __('批量生成') ?></a>
				<?php } ?>
				</li>
			</ul>
			<!-- 
			<form class="navbar-form navbar-right" name="search"
				action="/packages/index" method="get">
				<div class="form-group">
					<input type="text" name="title" class="input-search"
						placeholder="标签搜索" value="<?php echo $kw; ?>">
				</div>
				<a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
			</form>
			 -->
		</div>
		<table class="table table-condensed">
			<thead>
                <?php
																echo $this->Html->tableHeaders(
																		array(
																				array(
																						__('批号')=>array(
																								'width'=>'10%' 
																						) 
																				),
																				
																				array(
																						__('面值')=>array(
																								'width'=>'8%' 
																						) 
																				),
																				array(
																						__('数量')=>array(
																								'width'=>'8%' 
																						) 
																				),
																				array(
																						__('过期时间')=>array(
																								'width'=>'10%' 
																						) 
																				),
																				array(
																						__('备注')=>array(
																								'width'=>'20%' 
																						) 
																				),
																				array(
																						__('操作')=>array(
																								'width'=>'8%' 
																						) 
																				) 
																		), array(
																				'class'=>'table-header' 
																		));
																?>
            </thead>
			<tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Coupon->init($row);?>
                	<td><?php echo $this->Coupon->batchNumber() ?></td>
				<td><?php echo $this->Coupon->length() ?>'</td>
				<td><?php echo $this->Coupon->number() ?></td>
				<td><?php echo $this->Coupon->expire() ?></td>
				<td><?php echo $this->Coupon->description() ?></td>
				<td>
                      <?php echo $this->Coupon->editLink($role); ?> 
                      <?php echo $this->Coupon->export($role); ?> 
       					<?php echo $this->Coupon->detail($role); ?> 
                    </td>
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