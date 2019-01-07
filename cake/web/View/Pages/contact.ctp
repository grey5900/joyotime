<div class="fs-home-container-map">
    <div id="map-canvas" class="map-canvas"></div>
</div>

<?php 
	$this->start('header');
	echo $this->element('header', array('active' => 'contact', 'class' => 'fs-home-header'));
	$this->end();
	$this->start('banner');
// 	echo $this->element('address');
	$this->end();
	$this->start('footer');
	echo $this->element('footer');
	$this->end();
	$this->start('script');
?>
<script type="text/javascript">
<!--
$(function(){
    window.modal_map = function() { 
        function initialize() {
          var myLatlng = new google.maps.LatLng(30.5439570,104.0701260);
          var mapOptions = {
            zoom: 4,
            center: myLatlng,
            disableDefaultUI: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          }
        
          var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        
          var contentString = '<div id="content">'+
              '<div id="siteNotice">'+
              '<h1>'+'<?php echo __('联系我们') ?>'+'</h1>'+
              '</div>'+
              '</div>';
        
          // var infowindow = new google.maps.InfoWindow({
              // content: contentString
          // });
        
          var marker = new google.maps.Marker({
              position: myLatlng,
              map: map,
              title: '<?php echo __('联系我们') ?>'
          });
            
          //infowindow.open(map,marker);
          
          google.maps.event.addListener(marker, 'click', function() {
          });
        }
        
        google.maps.event.addDomListener(window, 'load', initialize);
}  
function loadScript() {  
    var script = document.createElement("script");  
    script.type = "text/javascript";  
    script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=modal_map";  
    document.body.appendChild(script);
}
loadScript();
})
//-->
</script>
<?php 
$this->end();
?>