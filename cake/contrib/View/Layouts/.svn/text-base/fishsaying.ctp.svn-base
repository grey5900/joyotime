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
        echo $this->Html->script('/js/jquery-1.9.1.min');
        echo $this->Html->script('/js/bootstrap.min');
        echo $this->Html->script('/js/plug-in/bootstrap-lightbox.min');
        echo $this->Html->script('/js/loading-overlay.min');
    ?>
    
    <!-- The fav icon -->
    <link rel="shortcut icon" href="/img/favicon.ico">
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
        
</head>

<body>
    <div class="page-container" id="target">
       <?php 
            echo $this->element('message'); 
            $flash = $this->Session->flash('flash', array('element' => 'flash'));
            echo $flash;
        ?>
        <div class="header">
            <?php echo $this->fetch('header'); ?>
        </div>
        <!--/.header-->
        <div class="container">
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
        echo $this->Html->script('/js/fishsaying');
        echo $this->Html->script('/js/plug-in/jQuery-jPlayer/jquery.jplayer.min');
    ?>
    <?php 
    echo $this->fetch('script');
    ?>
</body>
</html>
