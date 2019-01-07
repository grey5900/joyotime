$(function(){
//    if(window.matchMedia("(min-width: 20em)").matches) {
//        // load in the imgaes
//        var lazy = $.q('[data-src]');
//        console.log(lazy);
//        for (var i = 0; i < lazy.length; i++) {
//            var soure = lazy[i].getAttribute('data-src');
//            console.log(soure);
//            // create the image
//            var img = new Image();
//            img.src = soure;
//            // insert is inside of the link
//            lazy[i].insertBefore(img, lazy[i].firstChild);
//        }
//    }
    $("[rel='weixin']").on("click", function(){
        $.messagerModal("#popMessager");
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
