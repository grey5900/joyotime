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
		echo $this->Html->css('styles.css?v='.Configure::read('Css.version'),null, 
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
	<div class="page-container">
		<?php 
			echo $this->Session->flash();
			echo $this->element('message'); 
		?>
		<div class="header">
			<?php echo $this->fetch('top_nav'); ?>
		</div>
	    <!--/.header-->
    	<div class="container">
    		<div class="sidebar">
				<?php echo $this->fetch('left_menu'); ?>
			</div>
			<div class="main">
				<?php echo $this->fetch('content'); ?>
			</div>
    	</div><!--/.container-->
    </div>
    <?php //echo $this->fetch('footer'); ?> 
	<!-- external javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<!-- jQuery -->
	<?php 
	    echo $this->Html->script('jquery-1.9.1.min.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('jqueryui/jquery.ui.widget.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('jquery.tmpl.min.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('bootstrap.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('jqueryui/jquery-ui.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('fenpay.multiselect.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('jquery.validate.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('fenpay.account.min.js?v='.Configure::read('Js.version'));
		echo $this->Html->script('select2/select2.min.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('fenpay.tags.min.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('fenpay.truncation.min.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('fenpay.charcount.min.js?v='.Configure::read('Js.version'));
	    echo $this->Html->script('fenpay.js?v='.Configure::read('Js.version'));
	?>
	<?php 
	echo $this->fetch('script');
	?>
	<?php // echo $this->element('sql_dump'); ?>
</body>
</html>
