<?php 
$classDisabled = ($locationCheckboxAvailable) ? 'disabled' : '';
function getRegexpInput($form, $i, $label) {
    $input = '';
    $input .= $form->input("AutoReplyEchoRegexp.$i.regexp", array(
	    'label' => '正则表达式'.$label.':',
	    'type' => 'text',
	    'class' => 'input-stand pull-left',
	    'placeholder' => '请输入正则表达式',
        'after' => '<a href="javascript:void(0)" onclick="removeRegexp(this)"><i class="icon-delete"></i></a>',
        'between' => $form->hidden("AutoReplyEchoRegexp.$i.id")
    ));
    return $input;
}
?>
<div class="page-wrapper">
	<ul class="breadcrumb">
	  <li><i class="i-left"></i><a href="/auto_reply_echos" class="active">编辑第三方接口</a></li>
	</ul>
	<div class="tab-content clearfix active">
		<div class="mainway col-7">
	        <?php echo $this->Form->create('AutoReplyEcho', array('class' => 'form-horizontal', 'action' => 'add')); ?>
	        <?php echo $this->Form->hidden('AutoReplyEcho.id')?>
		    <fieldset>
                <div class="control-group">
                	<label class="control-label" for="AutoReplyEchoEnabledRegexp">
                		<span class="required">*</span>触发条件：
                	</label>
            		<div class="controls box-witch">
            			<ul class="nav-radio">
            				<li>
            				    <?php echo $this->Form->input('enabled_regexp', array(
                                    'value' => 1, 
                                    'label' => false, 
                                    'hiddenField' => false,
                                    'type' => 'checkbox',
                                    'after' => '正则表达式',
                                    'div' => false
                                ))?>    
            				</li>
            				<li>
	            				<?php echo $this->Form->input('enabled_location', array(
        					        'value' => 1, 
        					        'label' => false, 
        					        'hiddenField' => false,
        					        'type' => 'checkbox',
        					        'after' => '<span class="'.$classDisabled.'">地理位置</span>',
	            				    'disabled' => $locationCheckboxAvailable,
	            				    'div' => false
        					    ))?>
            				</li>
            			</ul>
            		</div>
            	</div>
                <div class="box-wrap">
	                <?php if(isset($this->request->data['AutoReplyEchoRegexp'])): ?>
	                    <?php foreach($this->request->data['AutoReplyEchoRegexp'] as $i => $item): ?>
	                    <?php echo getRegexpInput($this->Form, $i, ((int) $i) + 1); ?>
	    				<?php endforeach; ?>
					<?php else: ?>
	    				<?php echo getRegexpInput($this->Form, 0, 1); ?>
					<?php endif; ?>
					<div class="control-group">
					   <div class="controls add-regexp">
                           <a href="javascript:void(0)" id="addRegexp"><i class="icon-add"></i></a>
					   </div>
					</div>
				</div>
				<?php echo $this->Form->input('url', array(
				    'label' => '<span class="required">*</span>接口地址：',
				    'type' => 'text',
				    'class' => 'input-large',
				    'placeholder' => '接口地址',
				    'required'=>'required',
				    'error' => false
				));?>
			    <div class="form-actions">
				  	<?php echo $this->Form->submit('保存修改', array(
						'div' => false,
						'class' => 'btn btn-primary',
					)); ?>
				</div>
			</fieldset>
			<?php echo $this->Form->end(); ?> 
    	</div>
    </div>
</div>

<?php 
$this->start('top_nav');
echo $this->element('top_nav');
$this->end();
$this->start('left_menu');
echo $this->element('left_menu', array('active' => 'third_interface'));
$this->end();
$this->start('css');
$this->end();
$this->start('script');
?>
<script type="text/javascript" charset="utf-8">
function removeRegexp(e) {
	var  group = $('.box-wrap > .control-group');
	if (group.length == 2) {
		return $.messager('最后一条不能删除');
	} else {
		return e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
	}
}

$(function(){
    var i = <?php echo $initInputNumber; ?>; // Sort sequence number
    $('#addRegexp').click(function(){
        var container = $('.box-wrap'),
            group = $('.box-wrap > .control-group'),
            add_group = $(this).parent().parent();
        if (group.length >= 11) {
            $.messager('最多只能添加10条');
            return true;
        } else {
            $.tmpl($('#template-regex').html(), {'idx': i, 'label': i + 1}).insertBefore(add_group);
            i++;
            return true;
        }
    })
})
</script>

<!-- The template to display custom content inputs -->
<script id="template-regex" type="text/x-tmpl">
	<?php echo getRegexpInput($this->Form, '${idx}', '${label}'); ?>
</script>
<?php
$this->end()
?>