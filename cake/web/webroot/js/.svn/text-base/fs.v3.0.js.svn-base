/**
 * Created by Jaskang on 14-3-27.
 */

function isANDROID(){
    var ua = navigator.userAgent;
    return (ua.indexOf('Android') != -1);
}
function isIOS(){
    var ua = navigator.userAgent;
    return ( ua.indexOf('iPhone') != -1 || ua.indexOf('iPod') != -1 || ua.indexOf('iPad') != -1);
}

function checkbrowser(){
    var isShowUpdate=false;
    if(navigator.userAgent.indexOf("MSIE")>0)
    {
        if(navigator.userAgent.indexOf("MSIE 6.0")>0)
        {
            isShowUpdate = true;
        }
        if(navigator.userAgent.indexOf("MSIE 7.0")>0)
        {
            isShowUpdate = true;
        }
        if(navigator.userAgent.indexOf("MSIE 8.0")>0)
        {
            isShowUpdate = true;
        }
    }
    if(isShowUpdate){
        $('#model-update-browser').show();
    }
    $('.go-on').on('click',function(){
        $('#model-update-browser').hide();
    })
}




function delay(url, timeout) {
    if(!timeout) timeout = 1000;
    setTimeout(function() {
        if (!document.webkitHidden){
            alert(false);
            window.location = url;
        }
    }, timeout);
}

function iframe(APP_URL,WEB_PLAYER) {
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

function redirectTo(APP_URL,WEB_PLAYER) {
    if (navigator.userAgent.match(/Android/)) {
        if (navigator.userAgent.match(/SQ/)
        /* || navigator.userAgent.match(/XiaoMi/) */) {
            iframe(APP_URL,WEB_PLAYER);
        } else {
            delay(WEB_PLAYER);
            window.location = APP_URL;
        }
    }
    if (navigator.userAgent.match(/Android/)) {
        window.location = APP_URL;
        delay(WEB_PLAYER);
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