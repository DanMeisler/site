<?php
    require_once('authenticate.php');
?>
<script type="text/javascript" charset="utf8" src="http://code.jquery.com/jquery-1.12.3.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDScN9rVkda4l9rzwRT-xb-3jdCdnO_bY"></script>
<script>
	var map;
	var infowindow = new google.maps.InfoWindow();
    var arrMarkers = [];
    var markers = [];

    function setMarkers(map, locations) {
	  var lat,lon,info,modemId,date,units,text;
      for (var i = 0; i < locations.length; i++) {
		lat = locations[i][0];
		lon = locations[i][1];
		
        var myLatLng = new google.maps.LatLng(lat, lon);
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map
        });
		google.maps.event.addListener(marker, 'mousedown', (function (marker, i) {
                return function () {
					info = locations[i][2];
					modemId = info[0];
					date = info[1];
					units = info[2];
					text = "<br>";
					text += '<h3>Modem ID:' + modemId + '</h3>';
					text += '<h3>' + date + '</h3>';
					for(i=0;i < units.length; i++) {
						text += '<br>';
						text += 'Unit ID:' + units[i][0] + '\t';
						text += 'MCU_TEMPERATURE:'+ units[i][1] + '\t';
					}
                    infowindow.setContent(text);
                    infowindow.open(map, marker);

                }
            })(marker, i));

        arrMarkers.push(marker);
      }
    }

    function initialize() {
      var mapOptions = {
        zoom: 9,
		<?php
		if(isset($_GET["Lat"]) && isset($_GET["Lon"]))
		{
			$lat = $_GET["Lat"];
			$lon = $_GET["Lon"];
		?>
        center: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>),
		<?php } else {?>
		center: new google.maps.LatLng(31.0461, 34.8516),
		<?php } ?>
      }
      map = new google.maps.Map(document.getElementById('map-canvas'),
                                    mapOptions);
	  <?php
		if(isset($_GET["Lat"]) && isset($_GET["Lon"]))
		{
			$lat = $_GET["Lat"];
			$lon = $_GET["Lon"];
	  ?>
	  var myLatLng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>);
      var marker = new google.maps.Marker({
          position: myLatLng,
          map: map
      });
	  marker.setIcon('./sources/images/yellow-dot.png')
	  <?php } ?>
	  updateTheMarkers();
      setMarkers(map, markers);
    }

    function removeMarkers(){
     var i;
     for(i=0;i<arrMarkers.length;i++){
       arrMarkers[i].setMap(null);
     }
     arrMarkers = [];

    }

    google.maps.event.addDomListener(window, 'load', initialize);

    setInterval(function() { 
       updateTheMarkers();
    }, 10000);

    function updateTheMarkers(){
      $.ajax({
      type: "GET",
      url: "./markers.php",
              success: function (data) {
                  //We remove the old markers
                  removeMarkers();
                  var jsonObj = $.parseJSON(data),i;
			
                  markers =[];//Erasing the markers array
				  
                  //Adding the new ones
                  for(i=0;i < jsonObj.length; i++) {
                    markers.push(jsonObj[i]);
                  }

                  //Adding them to the map
                  setMarkers(map, markers);
              }
         });
    }

</script>
<div id="map-canvas" style="height: 100%; width: 100%"></div>
<div id="controls" style="position: fixed;top: 20;right: 20;">
    <button onclick="window.location.href='/currentState.php'">current state view</button>
    <br>
    <br>
    <button onclick="window.location.href='/history.php'">history view</button>
    <br>
    <br>
    <button onclick="window.location.href='/settings.php'">settings</button>
</div>
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>