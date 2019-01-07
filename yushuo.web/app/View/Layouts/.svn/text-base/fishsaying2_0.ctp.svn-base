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
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo __('鱼说') ?></title>
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
    	Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0" />
	<meta name="apple-itunes-app" content="app-id=747780955">
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('style2_0');
        echo $this->Html->script('jquery.1.10.2.min');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('responsiveImg');
        echo $this->Html->script('fishsaying.methods');
        echo $this->Html->script('fishsaying.public');
	?>
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
       <script src="/js/html5shiv.js"></script>
       <script src="/js/respond.min.js"></script>
    <![endif]-->
</head>

<body id="<?php echo $this->App->language(); ?>">
<!--[if lt IE 7]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
    
    <div class="intro-wrap">
        <?php echo $this->fetch('topbar2_0'); ?>
        <?php echo $this->fetch('start2_0'); ?>
        <?php echo $this->fetch('content'); ?>
        <?php echo $this->fetch('footer2_0'); ?>
    </div>
	<?php echo $this->fetch('script'); ?>
    <!-- Google Analytics. -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
        ga('create', 'UA-45954718-1', 'fishsaying.com');
        ga('send', 'pageview');
    </script>
</body>
</html>