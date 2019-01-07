<div class="fs-reset">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="col-md-10 conter alert-right hide">
                <img src="/img/fs.right.png" alt="" />
                <p><?php echo __('密码重置完成！你现在可以使用新密码登录鱼说了！') ?></p>
            </div>
            <div class="col-md-10 form-reset">
                <div class="alert alert-danger hide">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <strong><?php echo __('重置失败!'); ?></strong> 
                </div>
                <h2 class="fs-tilte"><?php echo __('重置密码') ?></h2>
                <form method="post" class="fs-form-horizontal" id="resetForm">
                    <div class="form-group">
                        <input type="password" class="form-control pwd" name="pwd1" placeholder="<?php echo __('新密码，6-12位')?>" />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control pwd" name="pwd2" placeholder="<?php echo __('确认密码') ?>" />
                    </div>
                    <input type="hidden" name="email"  value="<?php echo $email ?>" />
                    <input type="hidden" name="hash"   value="<?php echo $hash ?>" />
                    <input type="hidden" name="expire" value="<?php echo $expire ?>" />
                    <div class="form-group">
                  
                        <input type="submit" class="fs-btn-submit btn" value="<?php echo __('提交')?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(function(){
		$("#chang-lang").hide();
	    $("form#resetForm").on("submit" , function(){
	        var $this = $(this);
	        
	        var $data = {
	            pwd1 : $('input[name="pwd1"]').val(),
	            pwd2 : $('input[name="pwd2"]').val(),
	            email : $('input[name="email"]').val(),
	            hash : $('input[name="hash"]').val(),
	            expire : $('input[name="expire"]').val()
	        };
	        
	        $.ajax({
                type: "POST",
                url: "/reset",
                data: $data,
                dataType: "json",
                success: function(json){
                    console.log(json.result);
                    if(json.result == false) {
                        $('.alert-danger').removeClass('hide').text(json.message);
                    } else {
                        $('.form-reset').addClass('hide');
                        $('.alert-right').removeClass('hide');
                    }
                }
            });
            return false;
	    });

	})
</script>

<?php 
    $this->start('header');
    echo $this->element('header', array('class' => 'fs-home-header'));
    $this->end();
?>
