function clearOverlays(overlays) {
    var overlay;
    while ( overlay = overlays.pop()) {
        overlay.setMap(null);
    }
}
//拖动地图取得相应坐标和地址
function dragendLocation(marker) {
    google.maps.event.addListener(marker, "dragend", function(event) {
        var lat = marker.getPosition().lat(), 
            lng = marker.getPosition().lng(),
            latLng = new google.maps.LatLng(lat,lng);
        $('#RecordCommentLongitude').val(lng);
        $('#RecordCommentLatitude').val(lat);
        getAdress(latLng);
    });
}
//取得详细地址
function getAdress(latLng){
    geocoder = new google.maps.Geocoder(latLng);
    geocoder.geocode({'latLng': latLng}, function(results, status) {
        
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
              
              $('#RecordCommentAddress').val(results[0].formatted_address);
              
              $.getJSON("http://maps.google.com/maps/api/geocode/json?latlng="+latLng.lat()+","+latLng.lng()+"&language=en_US@&sensor=false", function(json){
                  var address_components = JSON.stringify(json.results[0].address_components);
                  $('input#address_components').val(address_components);
                });
          } else {
            $.messager('没找到地址');
          }
        } else {
           $.messager('未知地址');
        }
    });
}
//通过lat,lng 获取详细地址
function initAdress(lat,lng){
     latLng = new google.maps.LatLng(lat,lng);
	 getAdress(latLng);
}
function searchKeyword(addressname,marker,map) {
	 
    var geocoder = new google.maps.Geocoder();
    
    geocoder.geocode( { 'address': addressname }, function(results, status) {
        
        if (status == google.maps.GeocoderStatus.OK) {
            if(results[0]){
                var lat = results[0].geometry.location.lat(), 
                    lng = results[0].geometry.location.lng();

                var center = new google.maps.LatLng(lat,lng);
                
                map = new google.maps.Map(document.getElementById('mapContainer'),{
                    center: center,
                    zoom: 16,
                    mapTypeControl : false,
                });
                marker = new google.maps.Marker({
                    position: center,
                    draggable: true,
                    map: map
                });      
                dragendLocation(marker);
                $('#RecordCommentAddress').val(results[0].formatted_address);
                $('#RecordCommentLongitude').val(lng);
                $('#RecordCommentLatitude').val(lat);
                
                $.getJSON("http://maps.google.com/maps/api/geocode/json?latlng="+lat+","+lng+"&language=en_US@&sensor=false", function(json){
                    var address_components = JSON.stringify(json.results[0].address_components);
                    $('input#address_components').val(address_components);
                });
            }
        } else {
            $.messager("当前搜索地址不存在");
        }
    });
}
