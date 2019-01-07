	
	//解析地址获得坐标并标注到地图上
	function codeAddress(){
		var addr = $(window.parent.document).find("#address").val();
		if(addr != null){
			geocoder.geocode({"address":addr}, function(result, status){
				if(status == soso.maps.GeocoderStatus.OK){
					flagToMap(result.location);
				}else{
					flagToMap(center);
				}
			});
		}
	}
	
	//解析坐标获得地址
	function codeLocation(location){
		geocoder.geocode({"location":location}, function(result, status){
			if(status == soso.maps.GeocoderStatus.OK){
				$(window.parent.document).find("#address").val(result.address);
			}else{
				$(window.parent.document).find("#address").val("没找到");
			}
		});
	}
	
	function flagToMap(location){
		var coordinate = location.getLat()+","+location.getLng();
		map.moveTo(location);
		if(marker == null)
			marker = new soso.maps.Marker({map:map,draggable:true,position:location});
		else
			marker.setPosition(location);
		$(window.parent.document).find("#coordinate").val(coordinate);
		$("#lat").val(location.getLat());
		$("#lng").val(location.getLng());
        codeLocation(location);
	}
	
	function flagToMapNoDrag(location){
		var coordinate = location.getLat()+","+location.getLng();
		map.moveTo(location);
		if(marker == null)
			marker = new soso.maps.Marker({map:map,position:location});
		else
			marker.setPosition(location);
		$(window.parent.document).find("#coordinate").val(coordinate);
        codeLocation(location);
	}


	var map,geocoder,marker=null;
	var center = new soso.maps.LatLng(30.657517,104.065847);
	/**
	 * 自定义地图中心点并显示地图
	 * @param lat
	 * @param lng
	 */
	function show_map(lat, lng, canDrag){
		center = new soso.maps.LatLng(lat, lng);
		mapInit(canDrag);
	}
	function mapInit(canDrag){
		map = new soso.maps.Map(document.getElementById("map"),{
			center:center,
			zoomLevel:15
		});
		geocoder = new soso.maps.Geocoder();
		//设置标注点
		if(canDrag){
			flagToMap(center);
		    //setMap
		    var mapM= document.getElementById("mapM");
		    soso.maps.Event.addDomListener(mapM,"click",function(){
		        navControl.setVisible(true);
		        if(navControl.getMap()){
		            navControl.setMap(null);
		        }else{
		            navControl.setMap(map);
		        }
		    });
		    //setAlign
		   var sAlign=document.getElementById("sAlign");
		    var aIndex=0;
		    soso.maps.Event.addDomListener(sAlign,"click",function(){
		        navControl.setMap(map);
		        switch(aIndex){
		            case 0:
		                navControl.setAlign(soso.maps.ALIGN.TOP_RIGHT);
		                aIndex++;
		                break;
		            case 1:
		                navControl.setAlign(soso.maps.ALIGN.TOP_LEFT);
		                aIndex=0;
		        }
		    });
		    //setStyle
		    var sStyle=document.getElementById("sStyle");
		    var sIndex=0;
		    soso.maps.Event.addDomListener(sStyle,"click",function(){
		        navControl.setMap(map);
		        switch(sIndex){
		            case 0:
		                navControl.setStyle(soso.maps.NavigationControlStyle.NORMAL);
		                sIndex++;
		                break;
		            case 1:
		                navControl.setStyle(soso.maps.NavigationControlStyle.LARGE);
		                sIndex=0;
		        }
		    });
		}else{
			flagToMapNoDrag(center);
		}
		
		// 添加拖动事件
        soso.maps.Event.addListener(marker, "dragend", function() {
            var latLng = marker.getPosition();
            flagToMap(latLng);
        });
		// 添加拖动事件
        soso.maps.Event.addListener(marker, "dragging", function() {
            var latLng = marker.getPosition();
            flagToMap(latLng);
        });

	    var navControl = new soso.maps.NavigationControl({
	        align: soso.maps.ALIGN.TOP_LEFT,
	        margin: new soso.maps.Size(5, 15),
	        map: map
	    });
	}
