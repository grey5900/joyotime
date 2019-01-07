
<div class="page-wrapper">
   <div id="fs-map" style="width:100%;height:500px;">

   </div>
    <div class="fs-map-Info">
        <span>鱼说总数：<span class="num">13344条</span></span><br />
        <span>总时长：<span class="num">18800'45"</span></span><br />
        <span>总销量：<span class="num">18800条</span></span>
    </div>

</div>


<?php 
    $this->start('header');
    echo $this->element('header');
    $this->end();
    $this->start('sidebar');
    echo $this->element('sidebar');
    $this->end();
?>
<?php $this->start('script'); ?>
<!--<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false" ></script>-->
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3&sensor=false" ></script>
<script type="text/javascript" src="/js/infobox.js" ></script>
<script type="text/javascript">

    var beaches = [
        ['Bondi Beach1', 25.04,102.42],
        ['Bondi Beach2', 25.04,102.42],
        ['Bondi Beach2', 25.04,102.42],
        ['Bondi Beach2', 25.04,102.42],
        ['Bondi Beach2', 25.04,102.42],
        ['Coogee Beach', 36.4566360115962, 115.191650390625, 5],
        ['Cronulla Beach', 34.3797125804622, 112.203369140625, 3],
        ['Manly Beach', 29.983486718474694, 112.840576171875, 2],
        ['Maroubra Beach', 25.25463261974945, 114.752197265625, 1]

    ];
    var _i=null;
    var infoWindow=[];
    function initialize() {
        var myLatlng = new google.maps.LatLng(30.65744145856209,104.06588791534422);
        var mapOptions = {
            zoom: 4,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            panControl:false,
            zoomControl:false,
            scaleControl:false,
            mapTypeControl:false,
            streetViewControl:false
        }
        var map = new google.maps.Map(document.getElementById("fs-map"), mapOptions);
        setMark(beaches,map);
        google.maps.event.addListener(map, 'click', function(){
            if(_i != null){
                infoWindow[_i].close();
            }
        });
    }

    function setMark(beaches,map)
    {
        var _image = '/img/marker.png';
        for (var i = 0; i < beaches.length; i++) {
            var beach = beaches[i];
            var location = new google.maps.LatLng(beach[1], beach[2]);
//            var marker = new google.maps.Marker({
//                position: location,
//                map: map,
//                icon: _image
//            });
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });
            var j = i + 1;
            marker.setTitle(beach[0]);
            attachSecretMessage(map,marker,i, beach[0]);
        }
    }

    function attachSecretMessage(map,marker, number,ok) {
        var contentString = '<div class="fs-map-markerinfo"><div class="in-box">'+
                '<a class="link" href="" target="_blank"><img class="marker-img" src="/img/marker-test.jpg" alt=""/>'+
                '<div class="marker-info"><h5 class="title">'+
                '武侯祠'+
                '</h5><div class="bar">'+
                '<i class="erji"></i><label>3491</label>'+
                '<i class="star"></i><label>9.0</label>'+
                '</div></div></a></div></div>';
        infoWindow[number] = new google.maps.InfoWindow({
            content: contentString
        });
        google.maps.event.addListener(marker, 'click', function(e) {
            if(_i != null){
                infoWindow[_i].close();
            }
            infoWindow[number].open(map,marker);
            _i=number;
        });
//        var boxText = document.createElement("div");
//        boxText.style.cssText = "border: 1px solid black; margin-top: 8px; background: yellow; padding: 5px;";
//        boxText.innerHTML = contentString;
//
//        var myOptions = {
//            content: contentString
//            ,disableAutoPan: false
//            ,maxWidth: 0
//            ,pixelOffset: new google.maps.Size(-65,-90)
//            ,zIndex: null
//            ,boxStyle: {
//                background: "#fff"
//                ,opacity: 1
//                ,width: "260px"
//            }
//            ,closeBoxMargin: "5px"
//            ,closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
//            ,infoBoxClearance: new google.maps.Size(1, 1)
//            ,isHidden: false
//            ,pane: "floatPane"
//            ,enableEventPropagation: false
//        };
//        var ib = new InfoBox(myOptions);
//        ib.open(map, marker);
    }
    $(function(){
        initialize();
    })
</script>
<?php $this->end(); ?>