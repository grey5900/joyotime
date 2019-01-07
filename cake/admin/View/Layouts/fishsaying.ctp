<?php echo $this->Html->docType('html5');?>
<html>
<head>
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
        echo $this->Html->css('styles');
    ?>
    
    <!-- jQuery -->
    <?php 
        echo $this->Html->script('jquery-1.9.1.min');
        echo $this->Html->script('/js/plug-in/jquery-form/jquery.form');
        echo $this->Html->script('bootstrap.min');
        echo $this->Html->script('/js/plug-in/bootstrap-lightbox.min');
		echo $this->Html->script('/js/plug-in/bootstrap-tags/js/bootstrap-tags');
        echo $this->Html->script('/js/jquery.dragsort-0.5.1');
        echo $this->Html->script('/js/fishsaying');

    ?>
    
    <!-- The fav icon -->
    <link rel="shortcut icon" href="/img/favicon.ico">
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script>
    $(function () {
      $('.main').height($(window).height()-60);
    })
    </script>
</head>

<body>
    <div class="page-container">
        <?php 
            echo $this->element('message'); 
        ?>
        <?php 
            $flash = $this->Session->flash('flash', array('element' => 'flash'));
            echo $flash;
        ?>
        <div class="header">
            <?php echo $this->fetch('header'); ?>
        </div>
        <!--/.header-->
        <div class="container layout-main">
            <div class="sidebar">
                <?php echo $this->fetch('sidebar'); ?>
            </div>
            <div class="main">
                <?php echo $this->fetch('content'); ?>
            </div>
        </div><!--/.container-->
    </div>
    
    <!--/.footer-->
    
    
    <!-- external javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <!-- jQuery -->
    <?php 
        echo $this->Html->script('/js/plug-in/jQuery-jPlayer/jquery.jplayer.min');
        echo $this->Html->script('/js/lightbox');

    ?>

    <?php 
    echo $this->fetch('script');
    ?>
</body>
</html>
