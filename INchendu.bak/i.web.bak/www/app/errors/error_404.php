<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>IN成都 - 敢耍，爱泡！</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link rel="icon" href="/img/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="/css/in.css" type="text/css"/>
        <script type="text/javascript" src="/js/jquery.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/in.min.js"></script>
		<script type="text/javascript">
		    $(function(){
		        $.get("/online/", function(data){
		            $("#user_online").html(data);
		        });
		    });
		</script>
    </head>
    <body>
		<div class="notification">
		    <div class="notification-header">
				<h1><a href="/" class="logo">IN成都</a></h1>
		        <h3><?php echo $heading; ?></h3>
		        <ul class="user_online" id="user_online"></ul>
		    </div>
		    <div class="notification-body">
		        <div class="message"><?php echo $message; ?></div>
		    </div>
		    <div class="notification-footer">
		        <div class="links"><a href="/about/">关于我们</a>|<a href="/privacy/">使用条款和隐私政策</a>|<a href="/contact/">联系我们</a>|<a href="/cms_list/1/">媒体掌声</a>|<a href="/jobs/">人才招聘</a>|<a href="/help/">帮助</a></div>
		        <div class="copyright">&copy;2012 in.chengdu.cn</div>
		    </div>
		</div>
	</body>
</html>
