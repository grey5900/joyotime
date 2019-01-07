<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtools">
            <form class="navbar-form navbar-right" name="search" action="/users/index/" method="get">
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
                        array(__('头像') => array('width' => '8%')),
                        array(__('用户名') => array('width' => '13%')),
                        array(__('语言') => array('width' => '8%')),
                        array(__('邮箱') => array('width' => '14%')),
                        array(__('账户余额') => array('width' => '10%')),
                        array(__('可提现') => array('width' => '10%')),
                        array(__('购买次数') => array('width' => '8%')),
                        array(__('鱼说') => array('width' => '6%')),
                    	array(__('注册时间') => array('width' => '14%')),
//                         array(__('粉丝') => array('width' => '10%')),
//                         array(__('关注') => array('width' => '10%')),
//                         array(__('收藏') => array('width' => '10%')),
                        array(__('操作') => array('width' => '8%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->User->init($row);?>
                <tr class="radius">
                    <td><?php echo $this->User->avatar() ?></td>
                    <td>
                           <?php echo $this->User->username() ?><br /><?php echo $this->User->rolename() ?>
                              <div><a class="icon-qrcode" data-qrcode="modal" data-qrid="<?php echo $idx; ?>" data-qrurl="<?php echo $this->User->orurl($this->Session->read('Api.Token.web_host')) ?>" data-qrtitle="<?php echo strip_tags($this->User->username()) ?>" href="#modalQrcode"></a>
                               </div>
                           
                             <div class="qrimage-raw" id="qrimage-<?php echo $idx; ?>" style="display: none">
                           <?php echo $this->User->orimage($this->User->orurl($this->Session->read('Api.Token.web_host'))); ?>
                       </div>  
                    </td>
                    <td><?php echo $this->User->locale() ?></td>
                    <td><?php echo $this->User->email() ?></td>
                    <td><?php echo $this->User->money() ?></td>
                    <td><?php echo $this->User->earn() ?></td>
                    <td><?php echo $this->User->purchaseTotal() ?></td>
                    <td>
                        <a href="/voices/index/1/<?php echo $this->User->id() ?>">
                        <?php echo $this->User->voiceTotal() ?></a></td>
                        <td><?php echo $this->User->created() ?></td>
                    <!--  td><?php //echo $this->User->followers() ?></td>
                    <td><?php //echo $this->User->following() ?></td>
                    <td><?php //echo $this->User->favorites() ?></td -->
                    <td>
                        <?php echo $this->User->sendMessage(); ?>
                        <br />
                        <?php echo $this->User->sendGift(); ?>
                        <br />
                        <?php echo $this->User->setRole(); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php // echo $this->element('/modals/unavailable'); ?>
<?php echo $this->element('/modals/send_message'); ?>
<?php echo $this->element('/modals/send_gift'); ?>
<?php echo $this->element('/modals/setting_role'); ?>
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
$this->start('script'); ?>
<script type="text/javascript">
$(function() {
    // send message to user
    var link = '';
    // Role row
    var roleRow = '';
    // save global data when click invalid link...
    $('a.send-message-link').click(function(){
        link = $(this).attr('data-url');
        var username = $('#send-message-modal .modal-title span.username');
        username.text($(this).attr('username'));
    });

    $('a.setting-role-link').click(function(){
        roleRow = $(this);
        link = $(this).attr('data-url');
        var username = $('#setting-role-modal .modal-title span.username');
        username.text($(this).attr('username'));

        var role = $(this).attr('data-role');
        var $radios = $('input:radio[name="data[role]"]', '#setting-role-modal');
//         if($radios.is(':checked') === false) {
            $radios.filter('[value='+role+']').prop('checked', true);
//         }
    });
    
    // handle click submit comment button in modal...
    $('#setting-role-modal button.btn-primary').click(function(){
        var parent = $(this).parents('.modal-dialog');
        var role = $('input[name="data[role]"]:checked', parent).val()
        console.log('The selected role is ');
        console.log(role);
        var btn = $('button.btn-primary');
        
        btn.text('<?php echo __('正在提交...') ?>').attr('disabled',true);
        $.getJSON(link+'?role='+role, function(data) {
            // if success...
            if(data.result) {
                roleRow.attr('data-role', role);
            	$('#setting-role-modal').modal('hide');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('设置') ?>').attr('disabled', false);
        });
    })
    
    // handle click submit comment button in modal...
    $('#send-message-modal button.btn-primary').click(function(){
        console.log('dd');
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="message"]', parent);
        var message = textarea.val();
        var btn = $('button.btn-primary');
        if(!message) {
        	$.messager('<?php echo __('请填写想发送给用户的消息') ?>');
        	return ;
        }
        
        btn.text('<?php echo __('正在发送...') ?>').attr('disabled',true);
        $.getJSON(link+'?message='+message, function(data) {
            // if success...
            if(data.result) {
            	textarea.val('');
            	$('#send-message-modal').modal('hide');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('发送') ?>').attr('disabled',false);
        });
    })
    
    // save global data when click invalid link...
    $('a.send-gift-link').click(function(){
        link = $(this).attr('data-url');
        var username = $('#send-gift-modal .modal-title span.username');
        username.text($(this).attr('username'));
    });
    // handle click submit comment button in modal...
    $('#send-gift-modal button.btn-primary').click(function(){
        var parent = $(this).parents('.modal-dialog');
        var minutes = $('input[name="minutes"]', parent).val();
        var textarea = $('textarea[name="message"]', parent);
        var message = textarea.val();
        var btn = $('button.btn-primary');
        // @todo check whether seconds is numeric or not. 
        if(!minutes) {
        	$.messager('<?php echo __('请填写时长，单位：分钟') ?>');
        	return ;
        }
        if(!message) {
        	$.messager('<?php echo __('请填写想发送给用户的消息') ?>');
        	return ;
        }
        
        btn.text('<?php echo __('正在发送...') ?>').attr('disabled',true);
        $.getJSON(link+'?message='+message+'&minutes='+minutes, function(data) {
            // if success...
            if(data.result) {
            	textarea.val('');
            	$('#send-gift-modal').modal('hide');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('发送') ?>').attr('disabled',false);
        });
    });
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
        window.location.href = "/users/index/" + username;
    }
})
</script>
<?php
$this->end();
?>