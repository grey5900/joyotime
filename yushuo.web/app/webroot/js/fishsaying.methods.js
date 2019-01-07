! function($) {
    $.extend({
        /**
         * 选取元素
         *
         **/
        q : function(query) {

            if (document.querySelectorAll) {
                var res = document.querySelectorAll(query);
            } else {
                var d = document,
                a = d.styleSheets[0] || d.createStyleSheet();
                a.addRule(query,'f:b');
                for (var l=d.all,b=0,c=[].f=l.lenght;b<f;b++) {
                    l[b].currentStyle.f && c.pudh(l[b]);
                    a.removeRule(0);
                    var res = c;
                }
                return res;
            }
        },
        /**
         * 提示
         *
         * @param messagerModal : 'html'
         **/
        messagerModal : function(id) {
          var overlay = $("<div id='messager_modal'></div>");
          $("body").append(overlay);
          var modal_id = $(id);
          $(".modal-close").click(function() {
               close_modal(modal_id);
          });
          $(".modal-shut").click(function() {
               close_modal(modal_id);
          });
          var modal_height = modal_id.outerHeight();
          var modal_width = modal_id.outerWidth();

          $('#messager_modal').css({ 'display' : 'block', opacity : 0.7 });
          $('#messager_modal').fadeTo(100,modal_id.overlay);
          $(modal_id).css({
            'display' : 'block',
            'position' : 'fixed',
            'opacity' : 0.7,
            'z-index': 11000,
            //'left' : 50 + '%',
            'margin' : "25px",
            'top' : "50px"
          });
          modal_id.fadeTo(100,1);

          function close_modal(modal_id){
            $("#messager_modal").fadeOut(200);
            modal_id.css({ 'display' : 'none' });
          }
        },
    });

}(window.jQuery);

$(function(){
   function browserSelecet (id) {
        var ua = navigator.userAgent;
        var src = $(id);
        if( ua.indexOf('iPhone') != -1 || ua.indexOf('iPod') != -1 || ua.indexOf('iPad') != -1) {
            src.attr("src","/img/2.0/g2.png");
            src.after('<span>在Safari中打开</span>');
        } else if(ua.indexOf('Android') != -1) {
            src.attr("src","/img/2.0/g2a.png");
            src.after('<span>在浏览器中打开</span>');
        }
    }
    
    browserSelecet ('[data-sele="src"]');
});
