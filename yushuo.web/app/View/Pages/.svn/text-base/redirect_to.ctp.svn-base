<script type="text/javascript">
//<![CDATA[
var WEB_PLAYER = '/voice/<?php echo $shortId ?>';
var APP_URL = 'fishsaying://voice/<?php echo $shortId ?>';

	function delay(url, timeout) {
		if(!timeout) timeout = 500;
		setTimeout(function() {
            if (!document.webkitHidden){
                window.location = url;
            }
        }, timeout);
	}

	function iframe() {
		var iframe = document.createElement("iframe");
        iframe.style.border = "none";
        iframe.style.width = "1px";
        iframe.style.height = "1px";
        setTimeout(function() {
           	 window.location = WEB_PLAYER;
        }, 1000);
        iframe.src = APP_URL;
        document.body.appendChild(iframe);
	}

	function redirect() {
	     if (navigator.userAgent.match(/Android/)) {
	    	 if (navigator.userAgent.match(/SQ/) 
	    	    || navigator.userAgent.match(/XiaoMi/)) {
	    		 iframe();
	    	 } else {
	    		 delay(WEB_PLAYER);
	             window.location = APP_URL;
	    	 }
	     } 
	     if (navigator.userAgent.match(/Android/)) {
            delay(WEB_PLAYER);
            window.location = APP_URL;
	     } 
	     else if (navigator.userAgent.match(/iPhone|iPad|iPod/)) {
	         // iOS
	         delay(WEB_PLAYER);
	         window.location = APP_URL;
	     }
	     else if (navigator.userAgent.match(/MSIE/)) {
	         // Windows Phone
	         delay(WEB_PLAYER);
	         window.location = APP_URL;
	     }
	     else {
	         // Not mobile
	  	     window.location = WEB_PLAYER;
	     }
	}

 	redirect();
//]]>
</script>

<div class="redirect_message">正在跳转中...</div>