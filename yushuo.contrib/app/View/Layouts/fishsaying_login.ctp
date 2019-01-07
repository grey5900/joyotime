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
        echo $this->Html->css('style-login');
    ?>
    
    <!-- jQuery -->
    <?php 
        echo $this->Html->script('jquery-1.9.1.min');
        echo $this->Html->script('bootstrap.min');
    ?>
    
    <!-- The fav icon -->
    <link rel="shortcut icon" href="/img/favicon.ico">
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
        
</head>

<body>
    <div class="container">
        <?php 
            $flash = $this->Session->flash('flash', array('element' => 'flash'));
            if(!$flash) {
                echo $this->element('flash', array('message' => '请输入用户名和密码登录'));
            } else {
                echo $flash;
            }
        ?>
        <?php echo $this->fetch('content'); ?>
    </div><!--/.container-->
    
    <!--/.footer-->
    
    
    <!-- external javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <!-- jQuery -->
    <?php 
        echo $this->Html->script('/js/plug-in/jquery-validation/jquery.validate.min');
    ?>
    <?php 
    echo $this->fetch('script');
    ?>
</body>
</html>
