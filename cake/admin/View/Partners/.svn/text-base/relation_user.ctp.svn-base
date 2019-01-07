<div class="page-wrapper">
    <ul class="breadcrumb">
        <li>
            <i class="i-left"></i>
            <a href="/partners/index"  class="active">
                <?php echo __('内容合作商列表'); ?>
            </a>
        </li>
        <li>
            <span>
                <strong>“<?php echo $data['Partner']['name'];?>”的关联帐号</strong>
            </span>
        </li>
    </ul>
    <div class="tab-content clearfix active">
    <input type="hidden" value="<?php echo $id;?>" id="partner_id" />
        <br />
         <div class="box-navtool">
            <ul class="nav nav-tabs">
               <li  class="active">
                    <div class="nav-tab-active">
                        <a href="/partners/relationUser/<?php echo $id; ?>"><?php echo __('已关联') ?></a>
                    </div>
                </li>
                <li >
                    <div class="nav-tab-active">
                        <a href="/partners/userList/<?php echo $id; ?>"><?php echo __('新增关联') ?></a>
                    </div>
                </li>
            </ul>
        </div>
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                        array(__('头像') => array('width' => '10%')),
                        array(__('用户名') => array('width' => '15%')),
                        array(__('语言') => array('width' => '10%')),
						array(__('邮箱') => array('width' => '17%')),
                        array(__('账户余额') => array('width' => '8%')),
                        array(__('可提现') => array('width' => '8%')),
                        array(__('购买次数') => array('width' => '8%')),
                        array(__('鱼说') => array('width' => '8%')),
                        array(__('操作') => array('width' => '10%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->User->init($row);?>
                <tr class="radius">
                    <td><?php echo $this->User->avatar() ?></td>
                    <td><?php echo $this->User->username() ?><br /><?php echo $this->User->rolename() ?></td>
                    <td><?php echo $this->User->locale() ?></td>
   					<td><?php echo $this->User->email() ?></td>
                    <td><?php echo $this->User->money() ?></td>
                    <td><?php echo $this->User->earn() ?></td>
                    <td><?php echo $this->User->purchaseTotal() ?></td>
                    <td>
                        <a href="/voices/index/1/<?php echo $this->User->id() ?>">
                        <?php echo $this->User->voiceTotal() ?></a>
                    </td>
                    <td>
                      <?php echo $this->User->relationPartnerCancleLink() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/partner_relation_user'); ?>
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
	 
    var link = '';
	var linkThis;
    
    $('a.partner-hide-link').click(function(){
        link = $(this).attr('data-url');
        linkThis = $(this);
    });

    $('#partner-hide-modal button.btn-primary').click(function(){
        
     	var role = $("#role").is(':checked');
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="message"]', parent);
        var partner_id= $('#partner_id').val();
        var recommend_reason = textarea.val();
        var btn = $('#partner-hide-modal button.btn-primary');
       
        btn.text('<?php echo __('正在处理...') ?>').attr('disabled',true);
        $.getJSON(link+'?partner_id='+partner_id+'&role='+role, function(data) {
            if(data.result) {
            	$('.modal').modal('hide');
            	
            } 
            linkThis.closest("tr").remove();
           	$.messager(data.message);
            btn.text('<?php echo __('确定') ?>').attr('disabled',false);
        });
    })
    $('#partner-role-cancel').html('同时取消帐户冻结(设置为普通帐户)');
})
</script>
<?php
$this->end();
?>