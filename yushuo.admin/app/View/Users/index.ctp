<div class="page-wrapper">
    <div class="tab-content clearfix active">
        <div class="box-navtool">
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
                        array(__('用户名') => array('width' => '8%')),
                        array(__('语言') => array('width' => '8%')),
                        array(__('登录账号') => array('width' => '14%')),
                        array(__('账户余额') => array('width' => '10%')),
                        array(__('可提现') => array('width' => '10%')),
                        array(__('购买次数') => array('width' => '8%')),
                        array(__('解说') => array('width' => '6%')),
//                         array(__('粉丝') => array('width' => '10%')),
//                         array(__('关注') => array('width' => '10%')),
//                         array(__('收藏') => array('width' => '10%')),
                        array(__('操作') => array('width' => '14%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->User->init($row);?>
                <tr class="radius">
                    <td><?php echo $this->User->avatar() ?></td>
                    <td><?php echo $this->User->username() ?></td>
                    <td><?php echo $this->User->locale() ?></td>
                    <td><?php echo $this->User->email() ?></td>
                    <td><?php echo $this->User->money() ?></td>
                    <td><?php echo $this->User->earn() ?></td>
                    <td><?php echo $this->User->purchaseTotal() ?></td>
                    <td>
                        <a href="/voices/index/1/<?php echo $this->User->id() ?>">
                        <?php echo $this->User->voiceTotal() ?></a></td>
                    <!--  td><?php //echo $this->User->followers() ?></td>
                    <td><?php //echo $this->User->following() ?></td>
                    <td><?php //echo $this->User->favorites() ?></td -->
                    <td>
                        <?php echo $this->User->sendMessage(); ?>
                        <?php echo $this->User->sendGift(); ?>
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
    // save global data when click invalid link...
    $('a.send-message-link').click(function(){
        link = $(this).attr('data-url');
        var username = $('#send-message-modal .modal-title span.username');
        username.text($(this).attr('username'));
    });
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