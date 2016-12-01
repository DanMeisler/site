<?php
    require_once('authenticate.php');
?>

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="./sources/css/scroller.dataTables.min.css">
<style type="text/css">
            .gmap-button{
				width: 32px;
				height: 32px;
                background: url('./sources/images/gmap.ico') no-repeat center center;
                cursor: pointer;
                border: none;
            }
</style>
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script type="text/javascript" charset="utf8" src="./sources/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" charset="utf8" src="./sources/js/papaparse.js"></script>
<script type="text/javascript" charset="utf8">
    $(document).ready( function () {
		var renderUnits = function(data) {return data};
		var renderTags = function(data) {return data};
		var renderAreas = function(data) {return ''}; 
        var table = $('#history_table').DataTable({
			"processing": true,
			"serverSide": true,
			"order": [[ 0, 'desc' ]],
			"ajax": {
            "url": "getTable.php",
			"type": "POST",
            "data": function ( d ) {
				d.table = 'history';
                d.minDate = $('#min').val();
				d.maxDate = $('#max').val(); 
            }
			},
			scroller: {
				loadingIndicator: true
			},
            dom: 'Bfrti',
            buttons: [
                'copy', 'csv', 'excel',
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                }, 'print'
            ],
			"scrollX": true,
            "scrollY": 250,
			"columnDefs": [
			{
				"data": "DATE",
				"targets": 0
			},
            {
				"data": "UID",
                "render": function ( data, type, row ) {					
                    return renderUnits(data);
                },
                "targets": 1
            },
			{
				"data": "TID",
                "render": function ( data, type, row ) {
                    return renderTags(data);
                },
                "targets": 2
            },
			{
				"data": "TBAT",
				"render": function ( data, type, row ) {
                    return data + ' V';
                },
				"targets": 3
			},
			{
				"data": "TTMP",
				"targets": 4
			},
			{
				"data": "UBAT",
				"render": function ( data, type, row ) {
                    return data + ' V';
                },
                "targets": 5
			},
			{
				"data": "MVOLIND",
				"targets": 6
			},
			{
				"data": "UCSQ",
				"targets": 7
			},
			{
				"data": "NETCON",
				"targets": 8
			},
			{
				"data": "MCUTMP",
				"targets": 9
			},
			{
				"data": "EXTTMP",
				"targets": 10
			},
			{
				"render": function ( data, type, row ) {
                    return data + '°';
                },
                "targets": [4,9,10]
			},
			{
				"render": function ( data, type, row ) {
                    return renderAreas([parseFloat(row['LONGITUDE']),parseFloat(row['LATITUDE'])]);
                },
				"targets": 11
			},
			{
				"data": "LOC",
				"targets": 12
			},
			{
				"data": "LATITUDE",
				"targets": 13
			},

			{
				"data": "LONGITUDE",
				"targets": 14
			},
			{
				"data": "SPEED",
				"targets": 15
			},
			{
				"targets": -1,
				"data": null,
				"searchable": false,
				"orderable": false,
				"defaultContent": "<button class='gmap-button'></button>"
			},
			<?php if($_SESSION["isAdmin"] == 'false') {?>
			{
				"targets": [9,10,12,13,14,15],
				"visible": false,
			},
			<?php } ?>
			]
        });
		// Event listener for opening on google maps
		$('#history_table tbody').on('click', 'button', function () {
			var data = table.row( $(this).parents('tr')).data();
			//location.href = "./index.php?Lat=" + data["LATITUDE"] + "&Lon=" + data["LONGITUDE"];
			var form = $('<form action="./index.php" method="get">' +
			'<input type="hidden" name="Lat" value="' + data["LATITUDE"] + '" />' +
			'<input type="hidden" name="Lon" value="' + data["LONGITUDE"] + '" />' +
			'</form>');
			$('body').append(form);
			form.submit();
		});
        // Event listener to the two range filtering inputs to redraw on input
        $('#min, #max').change( function() {
            table.draw();
        } );
		setInterval( function () {
			scrollPos = $(".dataTables_scrollBody").scrollTop();
			table.ajax.reload( function() {
				$(".dataTables_scrollBody").scrollTop(scrollPos);
			}, false);
		}, 10000 );
		Papa.parse('./sources/render/units/uploads/units.csv', {
					download: true,
					complete: function(results){
						renderUnits = function(data)
						{
							var result = results['data'];
				
							return function() {
								for (var i = 0; i < result.length; i++) {
									if(data == result[i][0])
										return result[i][1];
								}
								return data;
							};
							
						};
					}
					});
		Papa.parse('./sources/render/tags/uploads/tags.csv', {
					download: true,
					complete: function(results){
						renderTags = function(data)
						{
							var result = results['data'];
				
							return function() {
								for (var i = 0; i < result.length; i++) {
									if(data == result[i][0])
										return result[i][1];
								}
								return data;
							};
							
						};
					}
					});
		$.get("/sources/kml/uploads/areas.kml", function(data){
			var areas = [], coords, place, poly;
			//loop through placemarks tags
			$(data).find("Placemark").each(function(){
				//get coordinates and place name
				coords = $(this).find("coordinates").text().replace(/,0.0/g,'').split(' ');
				place = $(this).find("name").text();
				//store as JSON
				poly = [];
				coords.forEach(function(element) {
					poly.push([parseFloat(element.split(',')[0]), parseFloat(element.split(',')[1])]);
				});
				areas.push({
					"place": place,
					"polygon": poly,
				});
				
			});
			function isInside(point, vs) {
				var x = point[0], y = point[1];
				
				var inside = false;
				for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
					var xi = vs[i][0], yi = vs[i][1];
					var xj = vs[j][0], yj = vs[j][1];
					
					var intersect = ((yi > y) != (yj > y))
						&& (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
					if (intersect) inside = !inside;
				}
				
				return inside;
			};
			renderAreas = function(coord)
			{
				for (var i = 0; i < areas.length; i++) {
						if(isInside(coord, areas[i]['polygon']))
							return areas[i]["place"];
					}
					return '';	
			};
		});
		});
</script>
<div id="controls" style="position: fixed;top: 20;right: 20;">
    <button onclick="window.location.href='/index.php'">map view</button>
    <button onclick="window.location.href='/settings.php'">settings</button>
</div>
<table border="0" cellspacing="5" cellpadding="5">
    <tbody>
        <tr>
            <td>
                <input placeholder="Start date:" type="text" id="min" onfocus="(this.type='date')" onblur="(this.type='text')">
            </td>
        </tr>
        <tr>
            <td>
                <input placeholder="End date:" type="text" id="max" onfocus="(this.type='date')" onblur="(this.type='text')">
            </td>
        </tr>
    </tbody>
</table>


<table id="history_table" class="display" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th>Date</th>
            <th>Unit ID</th>
            <th>Tag ID</th>
            <th>Tag voltage</th>
			<th>Tag temp</th>
            <th>Unit voltage</th>
            <th>Main voltage indicate</th>
            <th>GSM signal</th>
			<th>Network host</th>
			<th>Unit cpu temp</th>
			<th>Unit sns temp</th>
			<th>Area</th>
			<th>GPS data</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th>Speed</th>
			<th></th>
        </tr>
    </thead>
</table>
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>
