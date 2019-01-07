<div class="page-wrapper">
    <div class="tab-content clearfix active">
         <div class="box-navtool">
            <ul class="nav nav-tabs">
               <li >
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->User->authUserLink() ;?>"><?php echo __('用户认证列表') ?></a>
                    </div>
                </li>
                <li class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->User->authAddLink(); ?>"><?php echo __('新增认证') ?></a>
                    </div>
                </li>
            </ul>
            <form class="navbar-form navbar-right" name="search" action="/users/authAddList" method="get">
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
                        array(__('鱼说') => array('width' => '5%')),
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
                      <?php echo $this->User->authLink() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/user_auth'); ?>
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

    // save global data when click invalid link...
    $('a.user-auth-link').click(function(){
        link = $(this).attr('data-url');
        linkRemove = $(this);
    });

    // handle click submit comment button in modal...
    $('#user-auth-modal button.btn-primary').click(function(){
        console.log('dd');
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="message"]', parent);
       
        var verified_description = textarea.val();
        var btn = $('button.btn-primary');
        if(!verified_description) {
        	$.messager('<?php echo __('请填认证信息') ?>');
        	return ;
        }
        if(getByteLen(verified_description)>60) {
        	$.messager('<?php echo __('认证信息30字以内!') ?>');
        	return ;
        }
        btn.text('<?php echo __('正在处理...') ?>').attr('disabled',true);
        $.getJSON(link+'?verified_description='+verified_description, function(data) {
            // if success...
            if(data.result) {
            	$('.modal').modal('hide');
            	linkRemove.closest("tr").remove();
            	textarea.val('');
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
        window.location.href = "/users/authAddList/" + username;
    }
})
</script>
<?php
$this->end();
?>