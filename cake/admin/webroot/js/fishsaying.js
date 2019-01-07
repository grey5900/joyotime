! function($) {
    //插件
    $.fn.extend({
        /**
         * 为空不提交表单，目标设置为焦点
         *
         **/
        is_empty: function() {
            var $this = $(this).first();
            if ($this.val()) {
                return false;
            } else {
                $this.focus();
                return true;
            }
        },
        AjaxFormSubmit:function(url) {
            var _thisform=$(this);
            $(this).ajaxForm({dataType:"json",success:function(json){
                if( json.result == true){
                    $.messager(json.message);
                    if(url)
                    {
                        setTimeout(function(){window.location.href =url +'?'+ new Date().getTime();},1500);
                    }
                }else {
                    $.messager(json.message);
                }
            }});
            $(this).find('[data-type="submit"]').on('click',function(){
                var $this=$('[data-type="submit"]');
                $this.attr('disabled',true);
                _thisform.submit();
                setTimeout(function(){
                    $this.attr('disabled',false);
                },3000)
            });
        }
    });
    //方法
    $.extend({
        /**
         * 提示
         *
         * $.messager();
         **/

        messager : function(message, callback) {
            if (!message) {
                return false;
            }
            $(".alert-info").remove();
            var $el = $("<div id=\"messager\"></div>");
            $(".alert-message").html($el).removeClass('hide');
            $el.html(message);
            $.lightning();
        },

        /**
         * 闪退
         *
         **/
        lightning : function() {
            var alert = $('.alert');
            if (alert) {

                alert.delay('3000').animate({
                    queue : true
                }, 200, function() {
                    $(".alert").addClass('hide');
                });
            }
        }
    });

}(window.jQuery);




//计算字符串长度
function getByteLen(str) {
    var len = 0;
    for (var i = 0; i < str.length; i++) {
        if (str[i].match(/[^\x00-\xff]/ig) != null) //全角
            len += 2;
        else
            len += 1;
    };
    return len;
}

String.prototype.sub = function(n)
{
    var len = 0;
    for (var i = 0; i < this.length; i++) {
        if (this[i].match(/[^\x00-\xff]/ig) != null) //全角
        {
            len += 2;
        }
        else{
            len += 1;
        }
        if(len>n){
            return this.substr(0,i);
        }
    }
    return this;
};


(function ($) {
    $.fn.setmaxlength = function () {
        var _maxlength=$(this).attr('data-maxlength');
        $(this).keydown(function(event){
            var code = event.keyCode;
            switch (code) {
                case 8:  // allow delete
                case 9:
                case 17:
                case 36: // and cursor keys
                case 35:
                case 37:
                case 38:
                case 39:
                case 40:
                case 46:
                case 65:
                    return true;
                default:
                    var curLength=getByteLen($(this).val());
                    if(curLength>=_maxlength){
                        var maxtext=$(this).val().sub(_maxlength);
                        $(this).val(maxtext);
                        return false;
                    }else{
                        return true;
                    }
            }
        });
        $(this).keyup(function(event){
            var _maxlength=$(this).attr('data-maxlength');
            var curLength=getByteLen($(this).val());
            if(curLength>=_maxlength){
                var maxtext=$(this).val().sub(_maxlength);
                $(this).val(maxtext);
                return false;
            }
        });
    };
})(jQuery);

$(function() {
    $('.data-maxlength').setmaxlength();
    $.lightning();
    
    $('[data-lightbox]').Lightbox();
    
    // 试听音频
    
    $('[data-play="modal"]').on('click',function(){
        var $this = $(this);
        var $voice = $this.data('voice');
        var playerId = $this.data('player-id');
    
        $('#modalPlay').modal('show');
        $('#modalPlay .modal-body').prepend($('<div id="'+playerId+'" class="jp-jplayer"></div>'));
        $("#"+playerId).jPlayer({
            ready: function (event) {
                $(this).jPlayer("setMedia", {
                    m4a: $voice
                });
            },
            swfPath: "/js",
            supplied: "m4a",
            wmode: "window",
            smoothPlayBar: true,
            keyEnabled: true
        });
    });
    
    $('[data-id="colsePlay"]').on('click',function(){
        $('#modalPlay').modal('hide');
        $(".jp-jplayer").jPlayer("clearMedia" );
    });
    
    // 二维码扫描
    
    $('[data-qrcode="modal"]').click(function(){
        var $this = $(this);
        var idx = $this.data('qrid');
        var $modal = $('#modalQrcode');
        $modal.modal('show');
        var img = $('#qrimage-'+idx).html();
        $modal.find('.modal-header .voice-title').html($this.data('qrtitle'));
        $modal.find('.modal-body .voice-url').html($this.data('qrurl'));
        $modal.find('.modal-body .voice-qrcode').html(img);
    });

    function loadScript() {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=modal_map";
        document.body.appendChild(script);
    }

    if($('#fs-map').length > 0){
        return;
    }else{
        // google地图定位

        window.modal_map = function() {
            $('[data-map="modal"]').click(function(){
                var $this = $(this);
                var $modalmap = $('#modalMap');
                $modalmap.modal('show');
                $("#map-modal").remove();
                $modalmap.find('.modal-body').prepend($('<div id="map-modal" style="height: 250px;width:500px;"></div>'));
                $modalmap.data('lat',$this.data('lat'));
                $modalmap.data('lng',$this.data('lng'));
            });
            $('#modalMap').on('shown.bs.modal', function (event) {
                var $this= $(this),
                    lat = $this.data('lat'),
                    lng = $this.data('lng');

                var latlng = new google.maps.LatLng(lat, lng);
                var myOptions = {
                    zoom: 14,
                    center: latlng,
                    mapTypeControl : false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                var map = new google.maps.Map(document.getElementById("map-modal"), myOptions);
                var marker = new google.maps.Marker({
                    position : latlng,
                    map : map
                });
                google.maps.event.trigger(map, "resize");
                map.setCenter(latlng);
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'latLng': latlng}, function(results, status) {
                    var address = results[0].formatted_address;
                    var infowindow = new google.maps.InfoWindow({
                        content: address
                    });
                    infowindow.open(map, marker);
                });
            });
        };
        loadScript();
    }




});


function isOldIE(){
    var isOldIE8=false;
    if(navigator.userAgent.indexOf("MSIE")>0)
    {
        if(navigator.userAgent.indexOf("MSIE 6.0")>0)
        {
            isOldIE8 = true;
        }
        if(navigator.userAgent.indexOf("MSIE 7.0")>0)
        {
            isOldIE8 = true;
        }
        if(navigator.userAgent.indexOf("MSIE 8.0")>0)
        {
            isOldIE8 = true;
        }
    }
    return isOldIE8;
}

function clearOverlays(overlays) {
    var overlay;
    while ( overlay = overlays.pop()) {
        overlay.setMap(null);
    }
}
//计算字符串长度
function getByteLen(str) {
    var len = 0;
    for (var i = 0; i < str.length; i++) {
        if (str[i].match(/[^\x00-\xff]/ig) != null) //全角
            len += 2;
        else
            len += 1;
    };
    return len;
}
function dragendLocation(marker) {
    google.maps.event.addListener(marker, "dragend", function(event) {
        var lat = marker.getPosition().lat(), lng = marker.getPosition().lng();
        $('#RecordCommentLongitude').val(lng);
        $('#RecordCommentLatitude').val(lat);
    });
}

function change_url(web_url){
    var durl=/http:\/\/([^\/]+)\//i;
    domain = web_url.match(durl);
    return domain[0];
}

function imgfileview(obj,imgid,imgdivid){
    PreviewImage(obj,imgid,imgdivid);
    $('.btn-single-file').html('上传');
    $('.btn-single-file').attr("disabled",false);
}
//js本地图片预览，兼容ie[6-9]、火狐、Chrome17+、Opera11+、Maxthon3
function PreviewImage(fileObj,imgPreviewId,divPreviewId){
    var allowExtention=".jpg,.bmp,.gif,.png";//允许上传文件的后缀名document.getElementById("hfAllowPicSuffix").value;
    var extention=fileObj.value.substring(fileObj.value.lastIndexOf(".")+1).toLowerCase();
    var browserVersion= window.navigator.userAgent.toUpperCase();
    if(allowExtention.indexOf(extention)>-1){
        if(fileObj.files){//HTML5实现预览，兼容chrome、火狐7+等
            if(window.FileReader){
                var reader = new FileReader();
                reader.onload = function(e){
                    document.getElementById(imgPreviewId).setAttribute("src",e.target.result);
                }
                reader.readAsDataURL(fileObj.files[0]);
            }else if(browserVersion.indexOf("SAFARI")>-1){
                alert("<?php echo __('不支持Safari6.0以下浏览器的图片预览!'); ?>");
            }
        }else if (browserVersion.indexOf("MSIE")>-1){
            if(browserVersion.indexOf("MSIE 6")>-1){//ie6
                document.getElementById(imgPreviewId).setAttribute("src",fileObj.value);
            }else{//ie[7-9]
                fileObj.select();
                if(browserVersion.indexOf("MSIE 9")>-1)
                    fileObj.blur();//不加上document.selection.createRange().text在ie9会拒绝访问
                var newPreview =document.getElementById(divPreviewId+"New");
                if(newPreview==null){
                    newPreview =document.createElement("div");
                    newPreview.setAttribute("id",divPreviewId+"New");
                    newPreview.style.width = document.getElementById(imgPreviewId).width+"px";
                    newPreview.style.height = document.getElementById(imgPreviewId).height+"px";
                    newPreview.style.border="solid 1px #d2e2e2";
                }
                newPreview.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale',src='" + document.selection.createRange().text + "')";
                var tempDivPreview=document.getElementById(divPreviewId);
                tempDivPreview.parentNode.insertBefore(newPreview,tempDivPreview);
                tempDivPreview.style.display="none";
            }
        }else if(browserVersion.indexOf("FIREFOX")>-1){//firefox
            var firefoxVersion= parseFloat(browserVersion.toLowerCase().match(/firefox\/([\d.]+)/)[1]);
            if(firefoxVersion<7){//firefox7以下版本
                document.getElementById(imgPreviewId).setAttribute("src",fileObj.files[0].getAsDataURL());
            }else{//firefox7.0+
                document.getElementById(imgPreviewId).setAttribute("src",window.URL.createObjectURL(fileObj.files[0]));
            }
        }else{
            document.getElementById(imgPreviewId).setAttribute("src",fileObj.value);
        }
        $('#btn_Saveimg').attr("disabled",false);
    }else{
        alert("<?php echo __('仅支持 .jpg,.bmp,.gif,.png 为后缀名的文件!'); ?>");
        fileObj.value="";//清空选中文件
        if(browserVersion.indexOf("MSIE")>-1){
            fileObj.select();
            document.selection.clear();
        }
        fileObj.outerHTML=fileObj.outerHTML;
    }
}


