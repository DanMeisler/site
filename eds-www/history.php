<?php
    require_once('authenticate.php');
?>

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
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
<script type="text/javascript" charset="utf8">
    /* Custom filtering function which will search data in column four between two values */
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var min = new Date($('#min').val());
            var max = new Date($('#max').val());
            var date = Date.parse( data[9] ) || 0; // use data for the date column

            if ( ( isNaN( min ) && isNaN( max ) ) ||
                 ( isNaN( min ) && date <= max ) ||
                 ( min <= date   && isNaN( max ) ) ||
                 ( min <= date   && date <= max ) )
            {
                return true;
            }
            return false;
        }
    );
    $(document).ready( function () {
        var table = $('#history_table').DataTable({
			"order": [[ 9, 'desc' ]],
			"ajax": {
				"url": "getTable.php",
				"data": {
					"table": "history"
				}
			},

            dom: 'Bfrtip',
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
            
			"columns": [
            { "data": "Modem ID" },
            { "data": "Unit ID" },
            { "data": "GPS_DATA" },
            { "data": "Bat voltage indicate" },
            { "data": "MCU_TEMPERATURE" },
			{ "data": "Ex_TEMP_Sensor" },
			{ "data": "Latitude" },
            { "data": "Longitude" },
			{ "data": "Area" },
			{ "data": "Date" },
			{ "data": null }],
			"columnDefs": [
            {
                "render": function ( data, type, row ) {
					<?php 
						$filename = "./sources/render/modems/uploads/modems.csv";
						if (file_exists($filename)) {
							$file = fopen($filename, "r");
							while(!feof($file))
							{
								$line = (fgetcsv($file));
							    if ($line)
								{
									echo "if(\"" . $line[0] . "\" == data)\n\treturn \"" . $line[1] . "\";\n";
								}	
							}
							fclose($file);
						}
					?>
                    return data;
                },
                "targets": 0
            },
			{
                "render": function ( data, type, row ) {
					<?php 
						$filename = "./sources/render/units/uploads/units.csv";
						if (file_exists($filename)) {
							$file = fopen($filename, "r");
							while(!feof($file))
							{
								$line = (fgetcsv($file));
							    if ($line)
								{
									echo "if(\"" . $line[0] . "\" == data)\n\treturn \"" . $line[1] . "\";\n";
								}	
							}
							fclose($file);
						}
					?>
                    return data;
                },
                "targets": 1
            },
			{
				"render": function ( data, type, row ) {
                    return data + ' V';
                },
                "targets": 3
			},
			{
				"render": function ( data, type, row ) {
                    return data + '°';
                },
                "targets": [4,5]
			},
			{
				"targets": -1,
				"data": null,
				"searchable": false,
				"orderable": false,
				"defaultContent": "<button class='gmap-button'></button>"
			}]
        });
		// Event listener for opening on google maps
		$('#history_table tbody').on('click', 'button', function () {
			var data = table.row( $(this).parents('tr')).data();
			//location.href = "./index.php?Lat=" + data["Latitude"] + "&Lon=" + data["Longitude"];
			var form = $('<form action="./index.php" method="post">' +
			'<input type="text" name="Lat" value="' + data["Latitude"] + '" />' +
			'<input type="text" name="Lon" value="' + data["Longitude"] + '" />' +
			'</form>');
			$('body').append(form);
			form.submit();
		});
        // Event listener to the two range filtering inputs to redraw on input
        $('#min, #max').change( function() {
            table.draw();
        } );
		setInterval( function () {
			table.ajax.reload( null, false);
		}, 10000 );
		
		});
</script>
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
            <th>Modem ID</th>
            <th>Unit ID</th>
            <th>Gps data</th>
            <th>Bat voltage indicate</th>
            <th>Mcu temperature</th>
            <th>Ex Temp Sensor</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th>Area</th>
			<th>Date</th>
			<th></th>
        </tr>
    </thead>
</table >
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>
