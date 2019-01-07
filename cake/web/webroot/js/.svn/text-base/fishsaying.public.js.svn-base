$(function(){
    if($('[data-whatapp="rel"]').length>0){
        if($.isWeibo()){
            $("[data-whatapp]").attr('rel','weibo');
        }
    }

    $("[rel='weixin']").on("click", function(){
        var ua = navigator.userAgent;
        if( ua.indexOf('iPhone') != -1 || ua.indexOf('iPod') != -1 || ua.indexOf('iPad') != -1) {
            $('[data-sele="src2"]').attr("src","/img/2.0/g2.png");
            $('[data-sele="msgopen"]').html('在Safari中打开');
        } else if(ua.indexOf('Android') != -1) {
            $('[data-sele="msg"]').html("微信屏蔽了向外跳转的链接，请点击右上角的【更多】，在浏览器中打开。");
            $('[data-sele="msgshhare"]').html("点击右上角 [更多]图标");
            $('[data-sele="src1"]').attr("src","/img/2.0/t1-and.png");
            $('[data-sele="src2"]').attr("src","/img/2.0/t2-and.png");
        }
        var _page= $(this).attr('data-page');
        if(_page=="voice"){
            $('[data-sele="title"]').html("打不开链接？");
        }
        $.messagerModal("#popMessager");
        return false;
    });

    $("[rel='weibo']").on("click", function(){
        var ua = navigator.userAgent;
        if( ua.indexOf('iPhone') != -1 || ua.indexOf('iPod') != -1 || ua.indexOf('iPad') != -1) {
            $('[data-sele="src1"]').attr("src","/img/xinlang-and-t1.png");
            $('[data-sele="src2"]').attr("src","/img/xinlang-and-t2.png");
        }else if(ua.indexOf('Android') != -1) {
            $('[data-sele="src1"]').attr("src","/img/xinlang-ios-t1.png");
            $('[data-sele="src2"]').attr("src","/img/xinlang-ios-t2.png");
        }
        $('[data-sele="title"]').html("打不开链接？");
        $('[data-sele="msg"]').html("新浪微博屏蔽了向外跳转的链接，请点击右上角的 ··· 图标，在浏览器中打开。");
        $('[data-sele="msgshhare"]').html("点击右上角···图标");

        var _page= $(this).attr('data-page');
        $.messagerModal("#popMessager");
        return false;
    });


    $("#saidimg").responsiveImg({
        breakpoints:{
            "@320":320,
            "@780":780
        }
    });
    $("#listenimg").responsiveImg({
        breakpoints:{
            "@320":320,
            "@780":780
        }
    });
    $("#roundedimg").responsiveImg({
        breakpoints:{
            "@320":320,
            "@640":640,
            "@780":800
        },
        srcAttribute:"data-src"
    });
});
