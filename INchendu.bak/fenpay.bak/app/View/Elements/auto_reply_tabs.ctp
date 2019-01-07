<ul class="nav nav-tabs" id="weixin-auto-replay-tabs">
	<li class="<?php echo ($active == 'hybird') ? 'active' : ''?>">
		<div class="nav-tab-active">
			<a href="/auto_reply_messages">图文素材库</a>
		</div>	</li>
	<li class="<?php echo ($active == 'text') ? 'active' : ''?>">
		<div class="nav-tab-active">
			<a href="/auto_reply_texts">文本回复</a>
		</div>
	</li>
	<li class="<?php echo ($active == 'music') ? 'active' : ''?>">
		<div class="nav-tab-active">
			<a href="/auto_reply_musics">音乐回复</a>
		</div>
	</li>
	<!-- li class="<?php echo ($active == 'video') ? 'active' : ''?>">
		<div class="nav-tab-active">
			<a href="/auto_replies/video">视频回复</a>
		</div>
	</li -->
	<li class="<?php echo ($active == 'defaults') ? 'active' : ''?>">
		<div class="nav-tab-active">
			<a href="/auto_reply_configs/add">默认回复</a>
		</div>
	</li>
</ul>