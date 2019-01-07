<div class="box-content">
	<div class="content-wrapper">
		<ul class="nav nav-tabs" id="weixinConfig">
	    	<li class="active"><div class="nav-tab-active"><a href="#basic-info">基本资料</a></div></li>
	    	<li class=""><div class="nav-tab-active"><a href="#basic-config">基础设置</a></div></li>
	    </ul>
		<div id="weixinConfigContent" class="tab-content">
	    	<div class="tab-pane active" id="basic-info">
				<form class="form-horizontal" action="/weixin_configs/save_basic">
				  <fieldset>
					<div class="control-group">
					  <label class="control-label" for="typeahead">公众号名称 </label>
					  <div class="controls">
						<input type="text" class="input-xlarge" name="Weixin[name]">
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label" for="date01">微信号</label>
					  <div class="controls">
						<input type="text" class="input-xlarge" name="Weixin[weixin_id]" value="">
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label" for="date01">微信初使ID</label>
					  <div class="controls">
						<input type="text" class="input-xlarge" name="Weixin[weixin_id]" value="">
						<a class="help-block alert-info" href="#">如何查看初使ID</a>
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label" for="date01">微信用户数</label>
					  <div class="controls">
						<input type="text" class="input" name="Weixin[source_number]" value="0">
					  </div>
					</div>
					<div class="form-actions">
					  <button type="submit" class="btn btn-primary">保存</button>
					</div>
				  </fieldset>
				</form>   
	    	</div><!-- div#basic-info end -->
	    	<div class="tab-pane" id="basic-config">
				<form class="form-horizontal" action="/weixin_configs/save_replay">
				  <fieldset>
					<div class="control-group">
					  <label class="control-label" for="typeahead">图文素材回复形式 </label>
					  <div class="controls">
					    <label class="radio">
					        <input type="radio" name="loc_graphic" checked="checked" value="hybrid"> 图文
					    </label>
					    <label class="radio">
					        <input type="radio" name="loc_graphic" value="text"> 文字（节约流量）
					    </label>
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label" for="date01">图文内容个性签名</label>
					  <div class="controls">
						<select name="signature">
						    <option value="without">无签名</option>
						    <option value="1">签名1</option>
						</select>
						<a href="#" class="help-block alert-info">添加个性签名</a>
						<div class="signature-preview">
						    <pre>
	<b>万达国际影城</b>
	
	万达电影网隆重推出两大互动平台：
	
	微信：万达电影生活，登陆微信客户端搜索“万达电影生活”并关注
	
	企业QQ：4000806060，通过QQ客户端加好友即可实时沟通
	
	
	承接业务如下：
	
	营销活动推广、新影片宣传、受理用户咨询/投诉、电影评论&评价、电子月刊、电影类新闻&咨询、第三方撰写的超前预告片影评、万达电影的最新资讯、新影城开张等。
							    
							    </pre>
						</div>
					  </div>
					</div>
					<div class="control-group">
					  <label class="control-label" for="date01">地理位置回复形式</label>
					  <div class="controls">
					    <label class="radio">
					        <input type="radio" name="loc_num" checked="checked" value="hybrid"> 单个地址
					    </label>
					    <label class="radio">
					        <input type="radio" name="loc_num" value="multiple"> 多个地址
						</label>
						<div class="wall">返回离用户最近的一个地点及其关联的扩展信息</div>
					  </div>
					</div>
	
					<div class="form-actions">
					  <button type="submit" class="btn btn-primary">保存</button>
					</div>
				  </fieldset>
				</form>   
	    	</div>
	    </div>
	</div>
</div>

<?php 
	$this->start('top_nav');
	echo $this->element('top_nav');
	$this->end();
	$this->start('left_menu');
	echo $this->element('left_menu', array('active' => 'weixin_config'));
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
?>
?>