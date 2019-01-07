<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo __('鱼说') ?></title>
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
    	Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0" />
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap');
		echo $this->Html->css('style');
		
		echo $this->Html->script('/js/jquery.1.10.2.min');
		echo $this->Html->script('/js/bootstrap.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
	?>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
       <script src="/js/html5shiv.js"></script>
       <script src="/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="fs-index">
    <div class="fs-index-banner">
        <?php echo $this->fetch('header'); ?>
        <?php echo $this->fetch('banner'); ?>
    </div>
	<div class="container fs-index-container">
		<?php echo $this->fetch('content'); ?>
	</div>
	<div class="fs-index-footer">
		<div class="container">
			<?php echo $this->fetch('footer'); ?>
		</div>
	</div>
	<?php echo $this->fetch('script'); ?>
</body>
</html>