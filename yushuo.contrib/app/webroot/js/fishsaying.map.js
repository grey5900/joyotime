function clearOverlays(overlays) {
    var overlay;
    while ( overlay = overlays.pop()) {
        overlay.setMap(null);
    }
}

function dragendLocation(marker) {
    google.maps.event.addListener(marker, "dragend", function(event) {
        var lat = marker.getPosition().lat(), 
            lng = marker.getPosition().lng(),
            latLng = new google.maps.LatLng(lat,lng);
        $('#RecordCommentLongitude').val(lng);
        $('#RecordCommentLatitude').val(lat);
        // 地址
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({'latLng': latLng}, function(results, status) {
            
            if (status == google.maps.GeocoderStatus.OK) {
              if (results[0]) {
                  $('#RecordCommentAddress').val(results[0].formatted_address);
                  
                  $.getJSON("http://maps.google.com/maps/api/geocode/json?latlng="+latLng.lat()+","+latLng.lng()+"&language=en_US@&sensor=false", function(json){
                      var address_components = JSON.stringify(json.results[0].address_components);
                      $('input#address_components').val(address_components);
                    });
              } else {
                alert('没找到地址');
              }
            } else {
               alert('Geocoder failed due to: ' + status);
            }
        });
    });
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
                    zoom: 16
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
            alert("Geocode was not successful for the following reason: " + status);
        }
    });
}
