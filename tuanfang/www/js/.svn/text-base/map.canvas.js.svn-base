$(function() {
    $('#map-canvas').each(function() {
        var $this = $(this) ,
            lat = $this.data('lat') ,
            lng = $this.data('lng');

        init(lat, lng);

        function init(lat, lng){
            var center=new soso.maps.LatLng(lat, lng);
            var map=new soso.maps.Map(document.getElementById("map-canvas"),{
                center:center,
                zoomLevel:16
            });
            setTimeout(function(){
                var marker=new soso.maps.Marker({
                    position:center,
                    animation:soso.maps.Animation.DROP,
                    map:map
                });
                //marker.setAnimation(soso.maps.Animation.DROP);
            },2000);
        }

    });

    $('#map-modal').each(function() {
        var $this = $(this) ,
            lat = $this.data('lat') ,
            lng = $this.data('lng');    

        init(lat, lng);

        function init(lat, lng){
            var center=new soso.maps.LatLng(lat, lng);
            var map=new soso.maps.Map(document.getElementById("map-modal"),{
                center:center,
                zoomLevel:16
            });
            setTimeout(function(){
                var marker=new soso.maps.Marker({
                    position:center,
                    animation:soso.maps.Animation.DROP,
                    map:map
                });
                //marker.setAnimation(soso.maps.Animation.DROP);
            },2000);
        }
    });
})
