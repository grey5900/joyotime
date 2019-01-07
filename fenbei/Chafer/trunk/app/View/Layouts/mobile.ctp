<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @package       app.View.Layouts
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php echo $this->Html->docType('html5');?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<meta name="keywords" content="" />
	<meta name="description" content="">
	<?php
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->Html->css('jqueryui/jquery-ui.min.css?v='.Configure::read('Css.version'));
	?>
	<!-- The styles -->
	<?php
	    echo $this->Html->css('mobile-style.css?v='.Configure::read('Css.version'));
	?>
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>
<body>
	<div class="main-container">
		<div class="container">	
		<?php echo $this->Session->flash(); ?>
		<?php echo $this->fetch('content'); ?>
		</div>
		<div class="footer">
			<p>由分贝提供技术支持</p>
		</div>
	</div>
</body>
</html>
