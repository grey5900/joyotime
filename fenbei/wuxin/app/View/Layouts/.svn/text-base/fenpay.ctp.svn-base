<?php echo $this->Html->docType('html5');?>
<html>
<head>
	<!--
		Charisma v1.0.0

		Copyright 2012 Muhammad Usman
		Licensed under the Apache License v2.0
		http://www.apache.org/licenses/LICENSE-2.0

		http://usman.it
		http://twitter.com/halalit_usman
	-->
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<title><?php echo $title_for_layout; ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="">
	<?php
		echo $this->Html->charset();
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->Html->css('jqueryui/jquery-ui.min.css?v='.Configure::read('Css.version'));
	?>

	<!-- The styles -->
	<?php 
	    echo $this->Html->css('bootstrap.css?v='.Configure::read('Css.version'));
// 	    echo $this->Html->css('charisma-app');
	    echo $this->Html->css('style.css?v='.Configure::read('Css.version'));
	?>

	<!-- The fav icon -->
	<link rel="shortcut icon" href="/img/favicon.ico">
	
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
		
</head>

<body>
	<div class="page-content-wrapper">	
	    <?php echo $this->fetch('top_nav'); ?>
	    <!--/.header-->
    	<div class="container">
		    <div class="row">
			    <?php echo $this->fetch('left_menu'); ?>
    			<?php echo $this->fetch('content'); ?>
		    </div><!--/row-->
    	</div><!--/.container-->
    </div>
    <?php //echo $this->fetch('footer'); ?> 
	<!-- external javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<!-- jQuery -->
	<?php 
	    echo $this->Html->script('jquery-1.9.1.min.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('bootstrap.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('bootstrap-multiselect.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('jquery.validate.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('fenpay.account.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('fenpay.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('jqueryui/jquery-ui.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('bootstrap-datepicker.js?v='.Configure::read('Js.version'));
	?>
	<?php 
	echo $this->fetch('script');
	?>
	<?php // echo $this->element('sql_dump'); ?>
</body>
</html>
