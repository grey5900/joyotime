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

$(function() {
    $.lightning();
        
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
    
    window.modal_map = function() { 
        $('[data-map="modal"]').click(function(){
            var $this = $(this);
            $('#modalMap').modal('show');
            $("#map-modal").remove();
            $('#modalMap .modal-body').prepend($('<div id="map-modal" style="height: 250px;width:500px;"></div>'));
            var lat = $this.data('lat'),
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
            $('#modalMap').on('shown.bs.modal', function (event) {
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
        });
        
    };
    function loadScript() {  
        var script = document.createElement("script");  
        script.type = "text/javascript";  
        script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=modal_map";  
        document.body.appendChild(script);
    }
    loadScript();
});

function clearOverlays(overlays) {
    var overlay;
    while ( overlay = overlays.pop()) {
        overlay.setMap(null);
    }
}

function dragendLocation(marker) {
    google.maps.event.addListener(marker, "dragend", function(event) {
        var lat = marker.getPosition().lat(), lng = marker.getPosition().lng();
        $('#RecordCommentLongitude').val(lng);
        $('#RecordCommentLatitude').val(lat);
    });
}
