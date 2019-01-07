<div class="modal in hide" id="place_map">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>地图</h3>
	</div>
	<div class="modal-body">
		<div id="landmark" class="img-polaroid" style="width:98%;height:300px">
		</div>
	</div>
</div>

<?php 
$this->start('script');
echo $this->Html->script('http://map.soso.com/api/v2/main.js');
?>
<script type="text/javascript">
$(function(){
	var map, marker = null;
    var center = new soso.maps.LatLng(30.6438007,104.0469971);
    map = new soso.maps.Map(document.getElementById('landmark'),{
        center: center,
        minZoom:10,
        maxZoom:18,
        zoomLevel: 13
    });
	marker = new soso.maps.Marker({
        position: center,
        map: map
    });
});
</script>
<?php 
$this->end();
?>
