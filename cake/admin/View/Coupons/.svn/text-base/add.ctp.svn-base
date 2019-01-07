<div class="page-wrapper">
    <ul class="breadcrumb">
      <li><i class="i-left"></i><a href="/coupons/index"  class="active">二维码列表</a></li>
    </ul>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create("coupon", array('class' => 'form-horizontal')); ?>
        <fieldset>
        <div <?php if ($type=='edit'){ echo "class='hide'";}?> >
            <div class="control-group">
                <label for="couponLength" class="control-label">
                    <span class="required">*</span>时长：
                </label>
                <div class="controls">
                    <div class="input-group">
                    <?php echo $this->Form->input('length', array(
                            'label' => false,
                            'div'=>false,
                            'type' => 'number',
                            'required' => 'required',
                    		'placeholder'=>'时长必须是1-5位的正整数',
                    		'onkeyup'=>'validate.handle(this)'
                            ));
                            ?>
                        <span class="input-group-addon">/分钟</span>
                    </div>
                </div>
            </div>
            <?php echo $this->Form->input('number', array(
                'label' => '<span class="required">*</span>'.__('数量：'), 
                'onkeyup'=>'validate.handle(this)',
            	'required' => 'required',
            	'placeholder'=>'数量必须是1-5位的正整数'
            	
            ));?> 
            </div>
           
           <div class="control-group"><label for="tagNumber" class="control-label"><span class="required">*</span>过期时间：</label><div class="controls">
           <input  onClick="WdatePicker({minDate:'Y%-M%-{%d+1}',readOnly:true})" value="<?php if(isset($this->request->data['coupon']['expire'])){echo date('Y-m-d',$this->request->data['coupon']['expire']);} ?>" style="width: 80%;display: inline-block;" name="data[coupon][expire]"  size="12" type="text" id="expire">
           <img src="/js/plug-in/My97DatePicker/skin/datePicker.gif" width="16" height="22" align="absmiddle" style="cursor:pointer" onclick="WdatePicker({el:'expire',minDate:'Y%-M%-{%d+1}',readOnly:true})">
           </div></div>
          
             <?php echo $this->Form->input('description', array(
                'label' => __('备注：'), 
                'type' => 'textarea',
            	
                'maxlength'=>'30'
            ));?>
            <div class="form-actions">
                <?php echo $this->Form->submit(__('保存'), array(
                    'div' => false,
                	//	'type'=>'button',
                	'onclick'=>'return validate.dateValid();return false;',
                    'class' => 'btn btn-primary'
                )); ?>
            </div>
        </fieldset>
        <?php echo $this->Form->end();?>
        </div>
    </div>
</div>

<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    echo $this->Html->script('/js/plug-in/My97DatePicker/WdatePicker.js');
    $this->end();
?>
<script type="text/javascript">
$(function(){
    if ( $("#couponAddForm").length > 0 ) {
        $('#couponAddForm').AjaxFormSubmit('/coupons/index');
    }
    if ( $("#couponEditForm").length > 0 ) {
        $('#couponEditForm').AjaxFormSubmit('/coupons/index');
    } 
   	
});

//验证时长 数据
var validate={
	"re":new RegExp(/^[1-9]\d{0,4}$/),
 	"handle":function(obj){
		if(obj.value.match(this.re)==null){
			 $.messager($(obj).attr('placeholder'));
			 $(obj).val('');
		}
	},
	'dateValid':function(){
		 var start_time = $('#expire').val();//选择的时间
		 var arr = start_time.split("-");
		 var starttime = new Date(arr[0], arr[1], arr[2]);
		 var nowDate = new Date();
		 var end_time = nowDate.getFullYear()+'-'+(nowDate.getMonth()+1)+'-'+nowDate.getDate();//当前时间
		 var arrs = end_time.split("-");
		 var lktime = new Date(arrs[0], arrs[1], arrs[2]);
		 var days = (lktime - starttime)/(86400*1000);
		 if(days>-1){
		    $.messager('过期时间必须大于当天时间('+end_time+')');
			return false;
		 }   
	}
	
};

</script>