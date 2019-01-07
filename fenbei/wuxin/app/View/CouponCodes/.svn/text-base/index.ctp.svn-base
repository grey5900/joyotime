<div class="box-content">
	<div class="content-wrapper">
		<div class="tab-content">
			<div class="tab-pane active">
				<div class="operate-box">
					<?php 
						echo $this->Session->flash(); 
						echo $this->element('message');
					?>
		            <div class="pull-left">
		    	                      选择时段:
		    	        <input class="input-medium" type="text" name="start" id="beginDate" value="<?php echo $start; ?>" readonly data-date-format="yyyy-mm-dd" placeholder="开始时间" /> ~ 
                  		<input class="input-medium" type="text" name="end" id="endDate" value="<?php echo $end; ?>" readonly data-date-format="yyyy-mm-dd" placeholder="结束时间"/>
		    	        <input type="button" class="btn btn-inverse" onclick="search(this)" value="搜索" />
		            </div>
			        <div class="pull-right">
			        <select id="filter">
		    	        <option value="0">所有门店</option>
		    	        <?php foreach($shops as $id => $name): ?>
		    	        <option value="<?php echo $id?>" <?php if($id == $filter) echo 'selected="selected"'?>><?php echo $name?></option>
		    	        <?php endforeach; ?>
		    	    </select>
			        </div>
	           </div>
	            <table class="table table-bordered table-striped">
		            <thead>
	            		<?php 
							echo $this->Html->tableHeaders(array(
    						    array('兑换码' => array('width' => '30%')),
    				    	 	array('门店' => array('width' => '20%')),
    				    	 	array('收银员' => array('width' => '20%')),
    				    	 	array('时间' => array('width' => '10%')),
    				    	 	array('操作' => array('width' => '10%'))
							),array('class' => 'table-header'));
						?>
		            </thead>
		            <tbody>
	                <?php foreach($messages as $item): ?>
	                <tr class="">
	                    <td><?php echo $item['CouponCode']['coupon']?>
	                        <?php if($item['CouponCode']['invalid']): ?>
	                        <span class="label label-important">无效</span>
	                        <?php endif; ?>
	                    </td>
	                    <td><?php echo $item['Shop']['name']?></td>
	                    <td><?php echo $item['Saler']['name']?></td>
	                    <td><?php echo $item['CouponCode']['created'] ?></td>
	                    <td>
	                    <?php if($item['CouponCode']['invalid']): ?>
	                        <a href="/coupon_codes/invalid/<?php echo $item['CouponCode']['id'] ?>">设置为有效</a>
	                    <?php else: ?>
	                        <a href="/coupon_codes/invalid/<?php echo $item['CouponCode']['id'] ?>">设置为无效</a>
	                    <?php endif; ?>
	                    </td>
	                </tr>
	                <?php endforeach; ?>
	                </tbody>
	            </table>	
<?php echo $this->element('paginator'); ?>	 
			</div><!-- div.tab-pane .active end -->
		</div>
	</div>
</div>

<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
	$this->start('left_menu');
	echo $this->element('left_menu', array('active' => 'coupon_code'));
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
	$this->start('script');
?>

<script type="text/javascript">
<!--
$('#filter').change(function(){
	var sel = $(this);
	var val = sel.val();
	if(!val || val == 0) {
		var url = '/coupon_codes/index/';
		window.location.href = url;
		return false;
	}
	var url = '/coupon_codes/index/?shop='+val;
    window.location.href = url;
	return false;
});

function search(handle) {
	var parent = $(handle).parent();
	var start = $('input[name="start"]', parent).val();
	var end = $('input[name="end"]', parent).val();
	if(start && end) {
	    window.location.href = '/coupon_codes/index/?start='+start+'&end='+end;
	    return false;
	} else {
		window.location.href = '/coupon_codes/';
	    //$.messager('请选择开始和截止日期，并且截止日期大于开始日期');
	}
}

$(function(){
	var nowTemp = new Date();
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	
    var checkin = $('#beginDate').datepicker().on('changeDate', function(ev) {
    	var newDate = new Date(ev.date)
		    newDate.setDate(newDate.getDate() + 1);
		    checkout.setValue(newDate);
		checkin.hide();
		$('#endDate')[0].focus();
		
    }).data('datepicker');
    var checkout = $('#endDate').datepicker().on('changeDate', function(ev) {
  		checkout.hide();
    }).data('datepicker');
})
//-->
</script>
<?php 
$this->end();
?>