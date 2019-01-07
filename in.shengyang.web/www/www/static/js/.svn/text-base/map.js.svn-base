!function ($) {
  var center = null;
  var map = null;
  
  $(function(){
    center = new soso.maps.LatLng(30.657517,104.065847);
    map = new soso.maps.Map(document.getElementById("map"), {center:center,zoomLevel:15});
    moveCenter(center.getLat(),center.getLng());
    
    //标记地图
    <!--{eval $i=1;}-->
    <!--{loop $place $k $position}-->
    var plat = '<!--{$position[latitude]}-->';
    var plng = '<!--{$position[longitude]}-->';
    alert(plat+","+plng);
    var latLng = new soso.maps.LatLng(plat, plng);
    var marker = new soso.maps.Marker({
    	map:map ,
    	draggable:false , 
    	click:false , 
    	position:latLng , 
    	title:"<!--{$i}-->. <!--{$position[placename]}-->"
    });
    var decor = new soso.maps.MarkerDecoration({content:"<!--{$i}-->", margin:new soso.maps.Size(0, -4), align:soso.maps.ALIGN.CENTER, marker:marker});
    <!--{eval $i++;}-->
    <!--{/loop}-->
    
      var navControl = new soso.maps.NavigationControl({
          align: soso.maps.ALIGN.TOP_LEFT,
          margin: new soso.maps.Size(5, 15),
          map: map
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
  });
  
  //移动中心点
  function moveCenter(lat, lng, li){
    center = new soso.maps.LatLng(lat, lng);
    map.setCenter(center);
    if(li != null){
      $(".maplist li").each(function(){
        $(this).removeAttr("class");
      });
      $(li).attr("class", "active");
    }
  }
}(window.jQuery)