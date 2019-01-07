<div class="page-wrapper">
    <div class="box-navtool">
        <ul class="nav nav-tabs">
            <li class="active">
                <div class="nav-tab-active">
                    <a href="/broadcasts/add"><?php echo __('推送消息') ?></a>
                </div>
            </li>
            <li>
                <div class="nav-tab-active">
                    <a href="/broadcasts/index"><?php echo __('历史消息') ?></a>
                </div>
            </li>
        </ul>
    </div>
    <div class="tab-content clearfix active">
        <div class="mainway col-7">
        <?php echo $this->Form->create(null, array('class' => 'form-horizontal')); ?>
        <fieldset>
            <?php echo $this->Form->input('message', array(
                'label' => '<span class="required">*</span>'.__('消息内容：'), 
                'type' => 'textarea', 
                'required' => 'required',
            ));?>
            
            <?php echo $this->Form->input('link', array(
                'label' => __('消息链接：'), 
                'type' => 'text', 
                'helpBlock' => 'comment://{voice_id} #跳转到评论页面<br />
user://{user_id} #跳转到用户页面<br />
voice://{voice_id} #跳转到语音页面<br />
editvoice://{voice_id} #跳转到语音编辑页面<br />
myvoices://0 #跳转到我的解说页面<br />
account://0 #跳转到我的账户页面<br />'
            ));?>
            
            <div class="form-actions">
                <?php echo $this->Form->submit(__('发送广播'), array(
                    'div' => false,
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
    $this->end();
?>