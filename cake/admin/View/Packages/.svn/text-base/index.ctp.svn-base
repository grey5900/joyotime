<div class="page-wrapper">
	<div class="tab-content clearfix active">
		<div class="box-navtool">
			<ul>
				<li><a class="btn btn-primary"
					href="<?php echo $this->Packages->addList(); ?>"><?php echo __('新建鱼说包') ?></a>
				</li>
			</ul>
			<form class="navbar-form navbar-right" name="search"
				action="/packages/index" method="get">
				<div class="form-group">
					<input type="text" name="title" class="input-search"
						placeholder="鱼说包标题搜索" value="<?php echo $kw; ?>">
				</div>
				<a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
			</form>
		</div>
		<table class="table table-condensed">
			<thead>
                <?php
																echo $this->Html->tableHeaders(
																		array(
																				array(
																						__('鱼说包')=>array(
																								'width'=>'18%' 
																						) 
																				),
																				array(
																						__('关联鱼说')=>array(
																								'width'=>'8%' 
																						) 
																				),
																				array(
																						__('时长')=>array(
																								'width'=>'8%' 
																						) 
																				),
																				array(
																						__('收藏数')=>array(
																								'width'=>'6%' 
																						) 
																				),
																				array(
																						__('创建时间')=>array(
																								'width'=>'12%' 
																						) 
																				),
																				array(
																						__('状态')=>array(
																								'width'=>'6%' 
																						) 
																				),
																				array(
																						__('操作')=>array(
																								'width'=>'14%' 
																						) 
																				) 
																		), array(
																				'class'=>'table-header' 
																		));
																?>
            </thead>
			<tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Packages->init($row);?>
                <tr>
					<td>
						<div class="intro">
							<div class="pull-left">

								<a data-href="<?php echo $this->Packages->cover($row,'source'); ?>"
									href="#modalLightbox" class="thumbnail" data-toggle="modal"
									data-lightbox> <img
									src="<?php echo $this->Packages->cover($row); ?>" alt="" />
								</a>

							</div>
							<div class="details">
								<p><?php echo $this->Packages->title()?></p>


								<div class="block-left">
									<a class="icon-qrcode" data-qrcode="modal"
										data-qrid="<?php echo $idx; ?>"
										data-qrurl="<?php echo $this->Packages->orurl($webHostUrl) ?>"
										data-qrtitle="<?php echo $this->Packages->title() ?>"
										href="#modalQrcode"></a>
								</div>

								<div class="qrimage-raw" id="qrimage-<?php echo $idx; ?>"
									style="display: none">
                                	<?php echo $this->Packages->orimage($this->Packages->orurl($webHostUrl)); ?>
                                </div>
							</div>
						</div>

					</td>
					<td><?php echo $this->Packages->relationVoices() ?></td>
					<td><?php echo $this->Packages->voiceLengthTotal() ?></td>
					<td><?php echo $this->Packages->favoriteTotal() ?></td>
					<td><?php echo $this->Packages->created() ?></td>
					<td><?php echo $this->Packages->status() ?></td>
					<td>
                      <?php echo $this->Packages->edit(); ?> 
                      <?php echo $this->Packages->statusHandle(); ?> 
                      <?php echo $this->Packages->relation(); ?> 
                      <?php echo $this->Packages->delete(); ?>
                    </td>
				</tr>
            <?php endforeach; ?>
            </tbody>
		</table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>
<?php echo $this->element('/modals/lightbox'); ?>
<?php echo $this->element('/modals/package_status'); ?>
<?php echo $this->element('/modals/delete'); ?>
<?php echo $this->element('/modals/qrcode'); ?>
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
    var data_url = '';
    var row = '';
    var status_str ='';
    var delete_data_url ='';
    $('a.package-hide-link').click(function(){  
        row = $(this);
        status_str = $(this).html()=='下架' ? '下架':'上架';
		$('#modal-title').html(status_str+'鱼说包');
		$('#modal-body').html('确定'+status_str+'鱼说包？');
		$('#status-btn').html('是的，'+status_str+'吧');
        data_url = $(this).attr('data-url');
    });
    $('a.delete-hide-link').click(function(){  
    	linkRemove = $(this);
      
        delete_data_url = $(this).attr('data-url');
    });
    
    $('#package-hide-modal button.btn-primary').click(function(){
        var btn = $('#package-hide-modal button.btn-primary');
            btn.text('<?php echo __('正在处理...')?>').attr('disabled',true);
        $.getJSON(data_url, function(data) {
        	if(data.result) {
            	$('#package-hide-modal').modal('hide');
            	$(row).html(data.status==<?=Package::PENDING?> ?'下架':'上架');
            	if(data.status==<?=Package::PENDING?>){
            		$('#status-'+data.id).html('已上架');
            		$('#status-'+data.id).removeClass('text-danger');
                }else{
                	$('#status-'+data.id).html('未上架');
                	$('#status-'+data.id).addClass('text-danger');
                }
            	
            	$('#status-'+data.id).html(data.status==<?=Package::PENDING?> ?'已上架':'未上架');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('是的，上架吧')?>').attr('disabled',false);
        });
    })
     $('#delete-hide-link button.btn-primary').click(function(){
        var btn = $('#delete-hide-link button.btn-primary');
            btn.text('<?php echo __('正在处理...')?>').attr('disabled',true);
        	$.getJSON(delete_data_url, function(data) {
        	if(data.result) {
            	$('#delete-hide-link').modal('hide');
            	linkRemove.closest("tr").remove();
            } 
            $.messager(data.message);
            btn.text('<?php echo __('是的，删除吧')?>').attr('disabled',false);
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