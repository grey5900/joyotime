<?php echo $this->Html->docType('html5');?>
<html>
<head>
	<?php echo $this->Html->charset();?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<meta name="keywords" content="" />
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<?php
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		echo $this->Html->css('mobile-style');
	?>
	<!-- The fav icon -->
	<link rel="shortcut icon" href="/img/favicon.ico">
</head>
<body>
	<div class="container">
		<div class="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
