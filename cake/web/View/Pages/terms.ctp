<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />

        <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
        Remove this if you use the .htaccess -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

        <title><?php echo __('鱼说录制指南'); ?></title>

        <meta name="viewport" content="width=device-width; initial-scale=1.0" />

        <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
        <link rel="stylesheet" href="/css/mobile.style.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
        
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="/js/html5shiv.js"></script>
          <script src="/js/respond.min.js"></script>
        <![endif]-->
    
    </head>

    <body id="<?php echo $this->App->language(); ?>">
        <div class="container">
            <div class="box">
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('讲述你对这个景观的了解'); ?></h3></div>
                    <div class="content">
                        <p>
                            <?php echo __('这里有哪些特别的故事？分享给来自世界各地的游客吧！'); ?>
                        </p>
                    </div>
                </div>
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('人们更愿意为优秀的内容买单'); ?></h3></div>
                    <div class="content">
                        <p>
                            <?php echo __('受欢迎的鱼说往往富有自己的特色，让听过的游客都有所收获'); ?>
                        </p>
                    </div>
                </div>
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('前30秒很重要'); ?></h3></div>
                    <div class="content">
                        <p>
                            <?php echo __('前30秒为免费试听，游客会根据这段时间的内容质量来判断鱼说是否值得购买'); ?>
                        </p>
                    </div>
                </div>
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('图片是鱼说的门面'); ?></h3></div>
                    <div class="content">
                        <p>
                            <?php echo __('高质量的图片更容易吸引游客的点击，建议您上传的图片至少大于640px'); ?>
                        </p>
                    </div>
                </div>
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('位置越精确，越容易被需要的人发现'); ?></h3></div>
                    <div class="content">
                        <p>
                            <?php echo __('游客主要通过定位来寻找当地的鱼说，距离更近的鱼说会排在更前面'); ?>
                        </p>
                    </div>
                </div>
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('在目标语言环境中录制'); ?></h3></div>
                    <div class="content">
                        <p>
                            <?php echo __('如果你的录音面向中文听众，请务必使用中文版的鱼说来录音'); ?>
                        </p>
                    </div>
                </div>
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('审核机制'); ?></h3></div>
                    <div class="content">
                        <p>
                            <?php echo __('为保障鱼说质量，通过鱼说管理员审核的鱼说才能上架销售'); ?>
                        </p>
                    </div>
                </div>
                <div class="typo text-article">
                    <div class="title"><h3><?php echo __('上架须知'); ?></h3></div>
                </div>
            </div>
            <div class="box">
            	<div class="typo text-title ">
            		<h3><em>1.</em><?php echo __('鱼说上架规范'); ?></h3>
            		<ul>
            			<li><em>1.1</em><div><?php echo __('录音不少于60秒;'); ?></div></li>
            			<li><em>1.2</em><div><?php echo __('语音清晰，避免嘈杂的录音环境;'); ?></div></li>
            			<li><em>1.3</em><div><?php echo __('图片需与主题相关，且不能出现其他平台的水印信息;'); ?></div></li>
            			<li><em>1.4</em><div><?php echo __('地理位置精确，偏差不能过大;'); ?></div></li>
            			<li><em>1.5</em><div><?php echo __('内容不得涉黄及含有政治敏感内容;'); ?></div></li>
            			<li><em>1.6</em><div><?php echo __('尊重民族习惯和当地风俗;'); ?></div></li>
            			<li><em>1.7</em><div><?php echo __('不得宣扬邪教和封建迷信;'); ?></div></li>
            			<li><em>1.8</em><div><?php echo __('未经管理员许可不得发布商业广告性质内容;'); ?></div></li>
            			<li><em>1.9</em><div><?php echo __('不得重复发布同一条鱼说或雷同鱼说;'); ?></div></li>
            			<li><em>1.10</em><div><?php echo __('禁止抄袭、剽窃别人的作品;'); ?></div></li>
            		</ul>
            	</div>
            	<div class="typo text-title">
            		<h3><em>2.</em><?php echo __('上下架规则'); ?></h3>
            		<ul>
            			<li><em>2.1</em><div><?php echo __('提交的鱼说经审核通过后立即上架;'); ?></div></li>
            			<li><em>2.2</em><div><?php echo __('若提交的鱼说被驳回，可修改后再次提交审核;'); ?></div></li>
            			<li><em>2.3</em><div><?php echo __('已上架的鱼说原则上不能修改和删除，也不能自主下架，如遇特殊情况需要下架，请通过举报功能请求下架并说明下架原因;'); ?></div></li>
            			<li><em>2.4</em><div><?php echo __('若已上架的鱼说被用户举报，经调查核实后，违规的鱼说将会被下架，被下架的鱼说不能再次上架;'); ?></div></li>
            		</ul>
            	</div>
            	<div class="typo text-title">
            	    <h3><em>3.</em><?php echo __('定价方式'); ?></h3>
            	    <ul>
            	    	<li><em>3.1</em><div><?php echo __("以时间为虚拟货币单位，按照鱼说实际时长定价;"); ?></div></li>
            	    	<li><em>3.2</em><div><?php echo __("买家可免费试听前30秒，但购买时需为整段付费;"); ?></div></li>
            	    	<li><em>3.3</em><div><?php echo __("目前买家充值的价格为￥0.3/分钟;"); ?></div></li>
            	    </ul>
            	</div>
                <div class="typo text-title">
                    <h3><em>4.</em><?php echo __('补贴政策'); ?></h3>
                    <ul>
                        <li><em>4.1</em><div><?php echo __("为鼓励创作，鱼说实行销售补贴政策，鱼说每被购买一次，系统自动发放补贴至鱼说作者账户；"); ?></div></li>
                        <li><em>4.2</em><div><?php echo __("目前补贴比例为1.2，即补贴时长相当于鱼说时长的120%；"); ?></div></li>
                        <li><em>4.3</em><div><?php echo __("同一台手机切换多个账号购买同一条鱼说，只会补贴一次。"); ?></div></li>
                    </ul>
                </div>
            	<div class="typo text-title">
            	    <h3><em>5.</em><?php echo __('收入分配'); ?></h3>
            	    <ul>
            	    	<li><em>5.1</em><div><?php echo __('收入提现时的价格需先除去支付平台抽成（30%）和鱼说抽成（30%）;'); ?></div></li>
            	    	<li><em>5.2</em><div><?php echo __('所得收入可用于购买鱼说，也可以折算成现金提现至个人账户中;'); ?></div></li>
            	    	<li><em>5.3</em><div><?php echo __('鱼说收入高于20分钟才能申请提现;'); ?></div></li>
            	    	<li><em>5.4</em><div><?php echo __('提现过程中支付平台可能会向您收取一部分手续费;'); ?></div></li>
            	    </ul>
            	</div>
            	<div class="typo text-title">
            	    <h3><em>6.</em><?php echo __('版权声明'); ?></h3>
            	    <ul>
            	    	<li><em>6.1</em><div><?php echo __('鱼说仅为内容存储和交易平台，无须对用户发布的任何内容承担任何责任;'); ?></div></li>
            	    	<li><em>6.2</em><div><?php echo __('在鱼说上传或发布的任何内容版权均归发布者所有，受到著作权和知识产权相关法律的保护;'); ?></div></li>
            	    	<li><em>6.3</em><div><?php echo __('若您的著作权或知识产权遭到侵害，可以联系鱼说要求删除侵权内容;'); ?></div></li>
            	    	<li><em>6.4</em><div><?php echo __('若您发布的内容对他人的著作权或知识产权造成了侵害，鱼说将按照有关法律规定，删除侵权内容甚至终止您对账户的使用;'); ?></div></li>
            	    </ul>
            	</div>
            	<p class="text-footer"><?php echo __('所有解释权归鱼说，如有疑义，可通过【设置-意见反馈】提交申诉或建议。'); ?></p>
            </div>
        </div>
    </body>
</html>
