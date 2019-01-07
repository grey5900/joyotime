<div class="page-wrapper">
    <div class="tab-content clearfix active">
         <div class="box-navtool">
            <ul class="nav nav-tabs">
              <li   class="active">
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->User->recommendUserLink() ;?>"><?php echo __('推荐用户列表') ?></a>
                    </div>
                </li>
                <li>
                    <div class="nav-tab-active">
                        <a href="<?php echo $this->User->recommendAddUserLink(); ?>"><?php echo __('新增推荐') ?></a>
                    </div>
                </li>
            </ul>
        </div>
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                        array(__('头像') => array('width' => '20%')),
                        array(__('推荐用户') => array('width' => '20%')),
                    	array(__('推荐理由') => array('width' => '40%')),
                        array(__('操作') => array('width' => '20%')),
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody id="gridtbody">
            <?php foreach($items as $idx => $row): ?>
                <?php $this->User->init($row);?>
                <tr class="radius" data-id="<?php echo $this->User->id(); ?>" data-sort="<?php echo $idx; ?>">
                    <td><?php echo $this->User->avatar() ?></td>
                    <td><?php echo $this->User->username() ?><br /><?php echo $this->User->rolename() ?></td>
                    <td><?php echo $this->User->recommendReason() ?></td>
                    <td>
                    <?php echo $this->User->recommendEditLink() ?><br />
                      <?php echo $this->User->recommendDeleteLink() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php // echo $this->element('/modals/unavailable'); ?>
<?php echo $this->element('/modals/user_recommend'); ?>
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
$this->start('script'); ?>
<script type="text/javascript">
$(function() {
    // send message to user
    var link = '';
    var user_id='';
    
    // save global data when click invalid link...
    $('a.user-auth-link').click(function(){
        $('#auth-info textarea').val($('#vd-'+$(this).attr('user_id')).html());
        link = $(this).attr('data-url');
        user_id = $(this).attr('user_id');
    });
    //cancel auth
    $('a.auth-cancel-link').click(function(){
    	delete_data_url = $(this).attr('data-url');
    	linkRemove = $(this);
    });
    
    // handle click submit comment button in modal...
    $('#user-recommend-modal button.btn-primary').click(function(){
        var parent = $(this).parents('.modal-dialog');
        var textarea = $('textarea[name="message"]', parent);
        if(textarea.val().length>30){
	    	$.messager('推荐理由30字以内');
	    	return false;
    	}
        var recommend_reason = textarea.val();
        var btn = $('button.btn-primary');
        if(!recommend_reason) {
        	$.messager('<?php echo __('请填推荐理由') ?>');
        	return ;
        }
        if(getByteLen(recommend_reason)>60) {
        	$.messager('<?php echo __('推荐理由30字以内!') ?>');
        	return ;
        }
        btn.text('<?php echo __('正在处理...') ?>').attr('disabled',true);
        $.getJSON(link+'?recommend_reason='+recommend_reason, function(data) {
            // if success...
            if(data.result) {
            	textarea.val('');
            	$('.modal').modal('hide');
            	$('#vd-'+user_id).html(data.recommend_reason);
            } 
           	$.messager(data.message);
            btn.text('<?php echo __('确定') ?>').attr('disabled',false);
        });
    })
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
   
    $('#recommend-title').html("<?php echo __('编辑推荐理由');?>");
    $('#common-modal-body').html("<?php echo __('确定要删除对此用户的推荐吗？');?>");

    //拖拽排序
    $("#gridtbody").dragsort({
        dragSelector: "tr",
        dragEnd: saveSort,
        dragBetween: false,
        placeHolderTemplate: "<tr></tr>",
        scrollContainer:".main",
        scrollSpeed:10
    });
    //拖拽动作结束--数据提交服务器
    function saveSort(){
        var _item=$(this);
        var _user_id=_item.attr('data-id');
        var _index=$("#gridtbody tr").index($(this));
        var _offset=_index -parseInt(_item.attr('data-sort'));
        $.ajax({
            type: "GET",
            url: "/users/recommendOrder",
            data: {user_id:_user_id,recommend_offset:_offset},
            dataType: "json",
            success: function (data) {
                if(data.result){
                    $.messager(data.message);
                    //_item.attr('data-id',_index);
                    window.location.href=window.location.href;
                }else{
                    $.messager(data.message);
                }
            },
            error: function (msg) {
                alert(msg);
            }
        })
    }
    
})
</script>
<?php
$this->end();
?>