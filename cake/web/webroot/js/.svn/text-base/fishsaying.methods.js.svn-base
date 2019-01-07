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
        isWeixin:function(){
            var ua = navigator.userAgent.toLowerCase();
            if(ua.match("micromessenger")=="micromessenger") {
                return true;
            } else {
                return false;
            }
        },
        isWeibo:function(){
            var ua = navigator.userAgent.toLowerCase();
            if(ua.match("weibo")=="weibo") {
                return true;
            } else {
                return false;
            }
        },
//      分享到QQ空间
        shareQzone:function(title,url,summary,images){
            var _urlQQ='http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?';
            var _url;
            if(url==""){
                _url='url='+encodeURIComponent(location.href);
            }else{
                _url='url='+encodeURIComponent(url);
            }
            var _title='&title='+title;
            var _summary='&summary='+summary;
            var _images='&pics='+encodeURIComponent(images);
            _urlQQ=_urlQQ+_url+_title+_summary+_images;
            window.open(_urlQQ,'_blank');
        },
        shareSina:function share(title,url,images){
            var _urlSina='http://v.t.sina.com.cn/share/share.php?appkey=1784177743';
            var _type="&type=audio";
            var _url;
            if(url==""){
                _url='&url='+encodeURIComponent(location.href);
            }else{
                _url='&u'+encodeURIComponent(url);
            }
            var _title='&title='+title;
            var _images='&pic='+encodeURIComponent(images);

            _urlSina=_urlSina+_url+_title+_images;
            window.open(_urlSina,'_blank');
        },
        shareTqq:function(title,url,images){
            var _urlTqq='http://v.t.qq.com/share/share.php?appkey=100588393';
            var _title='&title='+title;
            var _url;
            if(url==""){
                _url='&url='+encodeURIComponent(location.href);
            }else{
                _url='&u'+encodeURIComponent(url);
            }
            var _images='&pic='+encodeURIComponent(images);
            var _site = ''; //你的网站地址
            _urlTqq = _urlTqq +_title+ _url +  _images;
            window.open(_urlTqq,'_blank');
        },
        shareFacebook:function(title,url,image){

            var _urlFb='http://www.facebook.com/sharer.php?';
            var _title='&t='+title;
            var _url;
            if(url==""){
                _url='&u='+encodeURIComponent(location.href);
            }else{
                _url='&u'+encodeURIComponent(url);
            }
            var _images='&image='+encodeURIComponent(image);
            _urlFb= _urlFb +_title+ _url + _images;
            window.open(_urlFb,'_blank');
        }


    });

}(window.jQuery);

function getLocalTime(nS) {
    return  new Date(parseInt(nS) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
}

