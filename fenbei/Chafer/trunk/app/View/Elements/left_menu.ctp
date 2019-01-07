<div class="accordion" id="accordion">
	<div class="accordion-group">
		<!-- <div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#">
				<span class="arrow-down"><i class="icon-home"></i>首页</span>
			</a>
		</div>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#">
				<span class="arrow-down"><i class="icon-sns"></i>客户管理</span>
			</a>
		</div>
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#">
				<span class="arrow-down"><i class="icon-core"></i>积分发放</span>
			</a>
		</div> -->
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
				<span><i class="icon-wx"></i>微信互动</span>
			</a>
		</div>
		<div id="collapseOne" class="accordion-body collapse in">
			<div class="accordion-inner">
				<div class="<?php echo ($active == 'weixin_config') ? 'active' : ''?>">
					<a href="/weixin_configs/add">公众号配置</a>
				</div>
				<div class="<?php echo ($active == 'auto_reply_fixcode') ? 'active' : ''?>">
					<a href="/auto_reply_fixcodes/">固定指令</a>
				</div>
				<div class="<?php echo ($active == 'auto_repdives_kw') ? 'active' : ''?>">
					<a href="/auto_reply_messages/">图文内容库</a>
				</div>
				<div class="<?php echo ($active == 'auto_repdives_geo') ? 'active' : ''?>">
					<a href="/auto_reply_locations">地理位置自动回复</a>
				</div>
				<!-- <div class="<?php echo ($active == 'customize_menu') ? 'active' : ''?>">
					<a href="/pages/weixin_customize_menu">自定义菜单</a>
				</div> -->
				<div class="<?php echo ($active == 'third_interface') ? 'active' : ''?>">
					<a href="/auto_reply_echos/">第三方接口</a>
			    </div> 
				<!-- <div class="<?php echo ($active == 'hot_keyword') ? 'active' : ''?>">
					<a href="/pages/weixin_hot_keyword">热门关键词</a>
				</div> -->
			</div>
		</div>
		<!-- <div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#">
				<span class="arrow-down"><i class="icon-setup"></i>设置</span>
			</a>
		</div> -->
	</div>
</div>
