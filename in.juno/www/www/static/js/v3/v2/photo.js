$(function() {
     var $container = $('.thumbnails');
    $container.imagesLoaded(function(){
        $container.masonry({
            itemSelector : '.thumbnail'
        });
    });
    $container.infinitescroll({
        navSelector  : '.pages',
        nextSelector : '#nextPage',
        itemSelector : '.thumbnail',
        extraScrollPx: 500,
        loading: {
            finishedMsg: "没有更多了...",
            msgText: "",
            //selector: null,
            speed: 'fast'
        }
    },
    function( newElements ) {
        var $newElems = $( newElements ).css({ opacity: 0 });
        $newElems.imagesLoaded(function(){
            $newElems.animate({ opacity: 1 });
            $container.masonry( 'appended', $newElems, true );
        });
    });
})
