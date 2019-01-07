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
		echo $this->fetch('meta');
		echo $this->fetch('css');
	?>

	<!-- The styles -->
	<?php 
	    echo $this->Html->css('bootstrap3.0.0.css?v='.Configure::read('Css.version'),null, 
	    	array('media' => 'screen','type'=>'text/css'));
	    echo $this->Html->css('login-style.css?v='.Configure::read('Css.version'),null, 
	    	array('media' => 'screen','type'=>'text/css'));
	?>
	
	<!-- The fav icon -->
	<link rel="shortcut icon" href="/images/favicon.ico">
	
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
		
</head>

<body>
	<div class="account-login-container">
		<?php echo $this->fetch('content'); ?>
	</div><!--/.container-->
	
    <!--/.footer-->
    
	<!-- external javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<!-- jQuery -->
	<?php 
	    echo $this->Html->script('jquery-1.9.1.min');
	    echo $this->Html->script('bootstrap.min');
		echo $this->Html->script('jquery.validate.min');
	?>
	<?php 
	echo $this->fetch('script');
	?>
</body>
</html>
