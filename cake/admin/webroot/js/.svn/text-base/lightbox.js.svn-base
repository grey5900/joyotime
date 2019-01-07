(function($) {

    $.fn.Lightbox = function() {

        return this.each(function() {

            var $this = $(this);

            init();

            function init(e, options) {

                var settings = $.extend({
                    placeholder : 'value'
                }, options);

                $this.click(function(e) {
                    e.preventDefault();
                    showBox();
                });
            }

            function showBox(e) {
                // Load the image 
                var imageSource = $this.data('href');
                var img = new Image();
                img.src =imageSource;
                $('.lightbox .modal-dialog .modal-content img').attr("src",imageSource);
                img.onload = function(){
                    var w = this.width+2;
                    if(w<560){
                        $('.lightbox .modal-dialog').width(w);
                        $('.lightbox .modal-dialog').css('margin-left','-'+(w/2)+'px');
                    }
                };
            };
        });

    };

})(jQuery);
