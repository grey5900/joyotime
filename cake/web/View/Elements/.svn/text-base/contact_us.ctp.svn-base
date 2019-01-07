<div class="modal fade" id="contact-us" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?php echo __('联系我们'); ?><span id="contact-msg"></span></h4>
        </div>
        <div class="modal-body">
            <form role="form">
                <div class="form-group">
                    <label for="txt-contect"><?php echo __('您的联系方式') ?></label>
                    <input type="email" class="form-control" id="txt-contect" >
                </div>
                <div class="form-group">
                    <label for="txt-content"><?php echo __('您的留言') ?></label>
                    <textarea class="form-control" rows="5" id="txt-content" ></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <span class="text-left"><?php echo __('你也可以发送邮件至contact@fishsaying.com') ?></span>
            <button type="button" id="contact-us-save" class="btn btn-green"><?php echo __('提交') ?></button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    var _contacttimer;
    $(function(){
        $('#contact-us-save').on('click',function(){
            var _this=$(this);
            var _content=$('#txt-content').val();
            var _contact=$('#txt-contect').val();
            if($.trim(_content)==""){
                $('#txt-content').val("");
                $('#txt-content').attr('placeholder','<?php echo __('内容不能为空') ?>');
                return;
            }
            $('#txt-content').attr('disabled',true);
            $('#txt-contect').attr('disabled',true);
            $('#contact-msg').html('-<?php echo __('正在提交') ?>...',3000);
            $.ajax({
                type: "POST",
                url: "/feedbacks/post",
                data: {content:_content,contact:_contact},
                dataType: "json",
                success: function (json) {
                    $('#txt-content').attr('disabled',false);
                    $('#txt-contect').attr('disabled',false);
                    if(json.result){
                        $('#txt-content').val("");
                        $('#txt-contect').val("");
                        showmsg('-<?php echo __('提交成功') ?>',2000);
                        $('#contact-us').modal('hide')
                    }else{
                        $('#contact-msg').html(json.message);
                    }
                },
                error:function(){
                    showmsg('-<?php echo __('提交失败') ?>',2000);
                    $('#txt-content').attr('disabled',false);
                    $('#txt-contect').attr('disabled',false);
                }
            })
        })
    });
    function showmsg(str,timer){
        clearTimeout(_contacttimer);
        $('#contact-msg').html(str);
        if(timer>0){
            _contacttimer= setTimeout(function(){
                clearmsg();
            },timer);
        }
    }
    function clearmsg(){
        $('#contact-msg').html('');
    }
</script>

