<div class="page-wrapper">
	<div class="title-voice"><?php // if(isset($title) && !empty($title)) echo $title.__('的评论') ?></div>
    <div class="tab-content clearfix active">
        <table class="table table-condensed">
            <thead>
                <?php 
                    echo $this->Html->tableHeaders(array(
                    array(__('鱼说') => array('width' => '')),
                    array(__('鱼说语言') => array('width' => '10%')),
                    array(__('打分') => array('width' => '5%')),
                    array(__('评论内容') => array('width' => '35%')),
                    array(__('用户') => array('width' => '')),
                    array(__('时间') => array('width' => '')),
                    array(__('操作') => array('width' => '5%'))
                    ),array('class' => 'table-header'));
                ?>
            </thead>
            <tbody>
            <?php foreach($items as $idx => $row): ?>
                <?php $this->Comment->init($row); ?>
                <tr class="">
                    <td><a href="/comments/index?voice=<?php echo $this->Comment->Voice->id(); ?>"><?php echo $this->Comment->Voice->title() ?></a></td>
                    <td><?php echo $this->Comment->Voice->language(); ?></td>
                    <td><?php echo $this->Comment->score(); ?></td>
                    <td class="comment-content"><?php echo $this->Comment->hidden(); ?><?php echo $this->Comment->content(); ?></td>
                    <td><?php echo $this->Comment->User->username(); ?></td>
                    <td><?php echo $this->Comment->modified(); ?></td>
                    <td class="comment-hide-link"><?php echo $this->Comment->hideLink(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $this->element('paginator'); ?>   
    </div>
</div>

<?php echo $this->element('/modals/comment_hide'); ?>


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
    // submit comment as reason for why invalid that one...
    var data_url = '';
    var row = '';
    // save global data when click invalid link...
    $('a.comment-hide-link').click(function(){
        row = $(this).parents('tr');
        data_url = $(this).attr('data-url');
    });
    // handle click submit comment button in modal...
    $('.modal button.btn-primary').click(function(){
//         var parent = $(this).parents('.modal-dialog');
//         var textarea = $('textarea[name="comment"]', parent);
//         var comment = textarea.val();
        var btn = $('button.btn-primary');
            btn.text('<?php echo __('正在屏蔽...')?>').attr('disabled',true);
        $.getJSON(data_url, function(data) {
        	if(data.result) {
//             	linkRemove.closest("tr").remove();
//             	textarea.val('');
            	$('#comment-hide-modal').modal('hide');
                $('td.comment-content', row).prepend('<?php echo $this->Comment->hiddenMark(); ?>');
                $('td.comment-hide-link', row).html('');
            } 
            $.messager(data.message);
            btn.text('<?php echo __('是的，屏蔽吧')?>').attr('disabled',false);
        });
    })
})
</script>
<?php
$this->end();
?>