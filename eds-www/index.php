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
	
	function renderUnits(unit)
	{
		<?php 
			$filename = "./sources/render/units/uploads/units.csv";
			if (file_exists($filename)) {
				$file = fopen($filename, "r");
				while(!feof($file))
				{
					$line = (fgetcsv($file));
				    if ($line)
					{
						echo "if(\"" . $line[0] . "\" == unit)\n\treturn \"" . $line[1] . "\";\n";
					}	
				}
				fclose($file);
			}
		?>
		return unit;
	}
	
	function renderTags(tag)
	{
		<?php 
			$filename = "./sources/render/tags/uploads/tags.csv";
			if (file_exists($filename)) {
				$file = fopen($filename, "r");
				while(!feof($file))
				{
					$line = (fgetcsv($file));
				    if ($line)
					{
						echo "if(\"" . $line[0] . "\" == tag)\n\treturn \"" . $line[1] . "\";\n";
					}	
				}
				fclose($file);
			}
		?>
		return tag;
	}
	
    function setMarkers(map, locations) {
		var lat,lon,info,unitID,date,tags,text,marker,i;
		for (var i = 0; i < locations.length; i++) {
			info = locations[i][2];
			unitID = info[0];
			date = info[1];
			tags = info[2];
			text = "<br>";
			text += '<h3>Unit ID:' + renderUnits(unitID) + '</h3>';
			text += '<h3>' + date + '</h3>';
			for(j=0;j < tags.length; j++) {
						text += '<br>';
						text += 'Tag ID:' + renderTags(tags[j][0]) + '\t';
						text += 'TBAT:'+ tags[j][1] + '\t';
						text += 'TRSSI:'+ tags[j][2] + '\t';
					}
			var myinfowindow = new google.maps.InfoWindow({
				content: text
			});
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(locations[i][0], locations[i][1]),
				map: map,
				infowindow: myinfowindow
			});
			google.maps.event.addListener(marker, 'mousedown', function() {
				this.infowindow.open(map, this);
				});
        arrMarkers.push(marker);
      }
    }

    function initialize() {
      var mapOptions = {
        zoom: 9,
		<?php
			if(isset($_REQUEST["Lat"]) && isset($_REQUEST["Lon"]))
			{
				$lat = $_REQUEST["Lat"];
				$lon = $_REQUEST["Lon"];
		?>
        center: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>),
		<?php } else {?>
		center: new google.maps.LatLng(31.0461, 34.8516),
		<?php } ?>
      }
      map = new google.maps.Map(document.getElementById('map-canvas'),
                                    mapOptions);
	  <?php
		if(isset($lat) && isset($lon))
		{
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
    <button onclick="window.location.href='/history.php'">history view</button>
    <button onclick="window.location.href='/settings.php'">settings</button>
</div>
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>