<div class="page-wrapper">
	<ul class="breadcrumb">
	  <li><i class="i-left"></i><a href="/auto_reply_fixcodes/"  class="active">编辑固定指令</a></li>
	</ul>
	<div class="tab-content clearfix active">
		<div class="mainway col-7">
  		<?php echo $this->Form->create('AutoReplyFixcode', array('class' => 'form-horizontal', 'action' => 'add', 'novalidate' => true)); ?>
    		<fieldset>
				<?php echo $this->Form->input('AutoReplyFixcode.id'); ?>
				<div class="control-group">
        			<div class="controls box-witch">
    					<ul class="nav-radio">
							<li>
							    <?php echo $this->Form->input('noanswer', array(
						            'label' => false,
                                    'type' => 'checkbox', 
						            'after' => '设为【回答不上时的回复】',
							        'value' => 1,
                                    'hiddenField' => false,
                                    'div' => false
							    ))?>
							</li>
							<li>
							    <?php echo $this->Form->input('subscribe', array(
						            'label' => false,
                                    'type' => 'checkbox', 
						            'after' => '设为【被关注自动回复】',
							        'value' => 1,
                                    'hiddenField' => false,
                                    'div' => false
							    ))?>
							</li>
						</ul>
        			</div>
        		</div>
				
				<?php echo $this->element('auto_reply_tag_input', array('label' => '<span class="required">*</span>指 令：')); ?>
				
				<div class="control-group">
        			<label class="control-label"><span class="required">*</span>回  复：</label>
        			<div class="controls box-witch">
    					<ul class="nav-radio">
							<li>
							    <?php echo $this->Form->input('type', array(
						            'label' => false,
                                    'type' => 'radio', 
						            'options' => array(
						                'text' => '文本',
						            ),
                                    'hiddenField' => false,
                                    'class' => 'fixcode_type',
                                    'default' => 'text',
                                    'div' => false
							    ))?>
							</li>
							<li>
							    <?php echo $this->Form->input('type', array(
						            'label' => false,
                                    'type' => 'radio', 
						            'options' => array(
						                'news' => '图文内容',
						            ),
                                    'hiddenField' => false,
                                    'class' => 'fixcode_type',
                                    'default' => 'text',
                                    'div' => false
							    ))?>
							</li>
						</ul>
		    		    <div class="tab-pane active">
		    		    	<div class="tab-pane-subset">
							<?php if(isset($this->request->data['AutoReplyFixcode']['type']) 
						            && $this->request->data['AutoReplyFixcode']['type'] == 'news'): ?>
						    <div class="fake-select-bg">
							    <select id="selectMultiple" name="data[AutoReplyFixcode][selected_messages][]" multiple="multiple">
        						    <?php 
        						    foreach($messages as $id => $title): 
        						       if(in_array($id, $selected_messages)) {
        						           $selected = 'selected';
        	                            } else {
                                           $selected = '';
                                        }
        						    ?>
        							<option <?php echo $selected ?> value="<?php echo $id ?>"><?php echo $title ?></option>
        							<?php endforeach;?>
        						</select>
                    		</div>
							<?php else: ?>
							    <?php echo $this->Form->input('AutoReplyFixcodeMessage.0.AutoReplyMessage.description', array(
								    'label' => false,
								    'type' => 'textarea',
								    'rows' => 8,
								    'maxlength' => '650',
								    'class' => 'summary',
								    'required' => 'required'
								));?>
							    <?php echo $this->Form->hidden("AutoReplyFixcodeMessage.0.AutoReplyMessage.type", array('value' => 'text')); ?>
							<?php endif; ?>
							</div>
		    		    </div>
        			</div>
        		</div>
				
			    <div class="form-actions">
					<?php echo $this->Form->submit('保存修改', array(
	  					'div' => false,
	  					'class' => 'btn btn-primary'
					)); ?>
				</div>
    		</fieldset>
    		<?php echo $this->Form->end(); ?> 
    	</div>
	</div>
</div>

<?php //echo $this->element('/modals/auto_reply_message/place_map'); ?>

<?php 
$this->start('top_nav');
echo $this->element('top_nav');
$this->end();
$this->start('left_menu');
echo $this->element('left_menu', array('active' => 'auto_reply_fixcode'));
$this->end();
$this->start('footer');
echo $this->element('footer');
$this->end();
$this->start('script');
echo $this->Html->script('jquery.tmpl.min'); 
$this->end();
?>
<?php 
$this->start('script');
?>
<script type="text/javascript">
$(function(){
	// initial select2 plugin
    $('#keywords').tags({
		placeholderText: '指令不可重复,多个指令用回车键分开',
		ajax: {
            url: "/auto_reply_keywords/check/",
            dataType: 'json',
            quietMillis: 100,
            data: function (term, page) { // page is the one-based page number tracked by Select2
                return {
                    tag: term, //search term
                };
            },
            results: function (data, page) {
                if(data.result) {
                    return {results: data.data, more: false };
                } else {
                    return {results: [], more: false};
                }
            },
        },
        initSelection: function(element, callback) {
        	var data = [];
            $(element.val().split(",")).each(function () {
                data.push({id: this, text: this});
            });
            callback(data);
        },
	});

	function initial_multiselect() {
		$('#selectMultiple').multiselect({
			includeSelectAllOption: true,
			enableFiltering: true,
			buttonClass: 'hide',
	  		maxHeight: 200,
	  		filterPlaceholder: '搜索图文'
		});
	}

	initial_multiselect();

    // Click event for switching tab.
    $('input[type="radio"][name="data[AutoReplyFixcode][type]"]').click(function(e){
    	var container = $('.tab-pane', $(this).parents('.box-witch'));
    	container.html('');    // remove content in the container
    	var templatename = $(this).val();
		$.tmpl($('#template-'+$(this).val()).html()).appendTo(container);
		
		if(templatename == 'text') {
			$('textarea.summary').charCount({
				maxChars: 650
			});
		} else if(templatename == 'news') {
			initial_multiselect();
		}
	});
	
	$('textarea.summary').charCount({
		maxChars: 650,
		maxCharsWarning: 640
	});
	
	$('form#AutoReplyFixcodeAddForm').submit(function(){
		var $limit =$("#keywords").select2("val");
		//$('.checkbox span').remove();
		if ($limit == ''){
			$.messager('必填指令');
			$("#keywords").select2("open");
			return false;
		}
	});
});
</script>

<!-- The template to display custom content inputs -->
<script id="template-text" type="text/x-tmpl">
<div class="tab-pane-subset">
	<?php echo $this->Form->input('AutoReplyFixcodeMessage.0.AutoReplyMessage.description', array(
	    'label' => false,
	    'type' => 'textarea',
	    'rows' => 8,
	    'maxlength' => '650',
	    'class' => 'summary',
	    'value' => '',
	    'required' => 'required'
	));?>
    <?php echo $this->Form->hidden("AutoReplyFixcodeMessage.0.AutoReplyMessage.type", array('value' => 'text')); ?>
</div>
</script>
<!-- The template to display exlink input -->
<script id="template-news" type="text/x-tmpl">
<div class="tab-pane-subset">
    <div class="fake-select-bg">

<select id="selectMultiple" name="data[AutoReplyFixcode][selected_messages][]" multiple="multiple">
    <?php 
    foreach($messages as $id => $title): 
       if(in_array($id, $selected_messages)) {
           $selected = 'selected';
        } else {
           $selected = '';
        }
    ?>
    <option <?php echo $selected ?> value="<?php echo $id ?>"><?php echo $title ?></option>
    <?php endforeach;?>
</select>

    </div>
</div>
</script>
<?php 
$this->end();
?>