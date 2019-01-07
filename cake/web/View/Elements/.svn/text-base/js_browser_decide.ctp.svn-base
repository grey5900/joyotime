<script type="text/javascript">
$(document).ready(function(){
    function browserDecide (id) {
        var ua = navigator.userAgent;
        var btn = $(id);
        if( ua.indexOf('iPhone') != -1 || ua.indexOf('iPod') != -1 || ua.indexOf('iPad') != -1) {
            btn.attr("href","<?php echo Configure::read('Download.ios')?>");
        } else if(ua.indexOf('Android') != -1) {
            btn.attr("href","<?php echo Configure::read('Download.android')?>");
        }
    }
    browserDecide ('[data-id="downBtn"]');

})
</script>
