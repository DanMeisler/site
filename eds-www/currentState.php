<?php
    require_once('authenticate.php');
?>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
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
            var min = Date.parse( $('#min').val());
            var max = Date.parse( $('#max').val());
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
        var table = $('#currentState_table').DataTable({
			"ajax": {
				"url": "getTable.php",
				"data": {
					"table": "currentState"
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
			{ "data": "Date" }],
            "columnDefs": [
            {
                "render": function ( data, type, row ) {
					<?php 
						$filename = "./sources/render/uploads/modem.csv";
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
            }]
        });
        // Event listener to the two range filtering inputs to redraw on input
        $('#min, #max').keyup( function() {
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
                <input type="date" id="min" name="min" placeholder="Minimum date:">
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" id="max" name="max" placeholder="Maximum date:">
            </td>
        </tr>
    </tbody>
</table>


<table id="currentState_table" class="display" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th>Modem ID</th>
            <th>Unit ID</th>
            <th>GPS_DATA</th>
            <th>Bat voltage indicate</th>
            <th>MCU_TEMPERATURE</th>
            <th>Ex_TEMP_Sensor</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th>Area</th>
			<th>Date</th>
        </tr>
    </thead>
</table>
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>
