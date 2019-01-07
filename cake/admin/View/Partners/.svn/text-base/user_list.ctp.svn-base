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
    <input type="hidden" value="<?php echo $data['Partner']['name'];?>" id="partner_name" /><br />
         <div class="box-navtool">
            <ul class="nav nav-tabs">
               <li >
                     <div class="nav-tab-active">
                        <a href="/partners/relationUser/<?php echo $id; ?>"><?php echo __('已关联') ?></a>
                    </div>
                </li>
                <li  class="active">
                   <div class="nav-tab-active">
                        <a href="/partners/userList/<?php echo $id; ?>"><?php echo __('新增关联') ?></a>
                    </div>
                </li>
            </ul>
            <form class="navbar-form navbar-right" name="search" action="/users/recommendList" method="get">
              <div class="form-group">
                <input type="text" name="username" class="input-search" placeholder="用户名搜索" value="<?php echo $kw; ?>">
              </div>
              <a id="search_btn" class="btn-search"><i class="icon-search"></i></a>
            </form>
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
                    <?php if($row['role']=='admin'){ ?>
                    <?php }else if(in_array($this->User->id(),$partner_users)){ ?>
                    <?php echo '<span class="text-muted">已关联</span>'; ?>  
                    <?php }else {?> 
                      <?php echo $this->User->relationPartnerLink() ?>
                      <?php } ?>
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
    // auth link 
    var link = '';
	var linkThis;
    // save global data when click invalid link...
    $('a.partner-hide-link').click(function(){
        link = $(this).attr('data-url');
        linkThis = $(this);
    });

    // handle click submit comment button in modal...
    $('#partner-hide-modal button.btn-primary').click(function(){
        
     	var role = $("#role").is(':checked');
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="message"]', parent);
        var partner_id= $('#partner_id').val();
        var partner_name= $('#partner_name').val();
        var btn = $('#partner-hide-modal button.btn-primary');
       // alert(linkThis.parent().parent().find('.role-user'));
       // linkThis.parent().parent().find('.role-user').html('合作导游账号');
        //return false;

        btn.text('<?php echo __('正在处理...') ?>').attr('disabled',true);
        $.getJSON(link+'?partner_id='+partner_id+'&role='+role+'&partner_name='+partner_name, function(data) {
            if(data.result) {
            	$('.modal').modal('hide');
            	
            	if(role==true){
					
            		linkThis.parent().parent().find('.role-user').html('合作导游账号');
            		linkThis.parent().parent().find('.belong_partner').html($('#partner_name').val());
            		
                }
            	linkThis.parent().html('<span class="text-muted">已关联</span>');
            	
            } 
           	$.messager(data.message);
            btn.text('<?php echo __('确定') ?>').attr('disabled',false);
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
        var username = $("input[name=username]").val();
        window.location.href = "/partners/userList/<?php echo $id;?>/" + username;
    }
    $('#partner-tip').html('确定要将此用户关联到'+$('#partner_name').val()+'吗？');
    
})
</script>
<?php
$this->end();
?>