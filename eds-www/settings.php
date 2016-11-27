<?php
	require_once('authenticate.php');
?>
<link href="sources/css/uploadFile.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="sources/js/uploadFile.js"></script>
<script>
	var table;
	$(document).ready(function()
	{
		$("#upload1").uploadFile({
			url: "sources/kml/upload.php",
			uploadStr:"Upload kml file",
			returnType: "json",
			showDelete: true,
			showDownload:true,
			statusBarWidth:600,
			maxFileSize:10000*1024,
			previewHeight: "100px",
			previewWidth: "100px",
			maxFileCount:1,
			acceptFiles:".kml",
			dragDrop:false,
			
			onLoad: function(obj)
			{
				$.ajax({
						cache: false,
						url: "sources/kml/load.php",
						dataType: "json",
						success: function(data) 
						{
							for(var i=0;i<data.length;i++)
							{ 
								obj.createProgress(data[i]["name"],data[i]["path"],data[i]["size"]);
							}
						}
					});
			},
			deleteCallback: function (data, pd) {
				for (var i = 0; i < data.length; i++) {
					$.post("sources/kml/delete.php", {op: "delete",name: data[i]},
						function (resp,textStatus, jqXHR) {
							//Show Message	
							alert("File Deleted");
						});
				}
				pd.statusbar.hide(); //You choice.
			
			},
			downloadCallback:function(filename,pd)
				{
					location.href="sources/kml/download.php?filename="+filename;
				}
		}); 
		
		$("#upload2").uploadFile({
			url: "sources/render/upload.php",
			uploadStr:"Upload csv file for modem rendering",
			returnType: "json",
			showDelete: true,
			showDownload:true,
			statusBarWidth:600,
			maxFileSize:10000*1024,
			previewHeight: "100px",
			previewWidth: "100px",
			maxFileCount:1,
			acceptFiles:".csv",
			dragDrop:false,
			
			onLoad:function(obj)
			{
				$.ajax({
						cache: false,
						url: "sources/render/load.php",
						dataType: "json",
						success: function(data) 
						{
							for(var i=0;i<data.length;i++)
						{ 
							obj.createProgress(data[i]["name"],data[i]["path"],data[i]["size"]);
						}
						}
					});
			},
			deleteCallback: function (data, pd) {
				for (var i = 0; i < data.length; i++) {
					$.post("sources/render/delete.php", {op: "delete",name: data[i]},
						function (resp,textStatus, jqXHR) {
							//Show Message	
							alert("File Deleted");
						});
				}
				pd.statusbar.hide(); //You choice.
			
			},
			downloadCallback:function(filename,pd)
				{
					location.href="sources/render/download.php?filename="+filename;
				}
		}); 
		
		table = $('#users_table').DataTable({
			"ajax": {
				"url": "getTable.php",
				"data": {
					<?php if($_SESSION["isAdmin"] == 'true') {?>
					"table": "users"
					<?php } else { ?>
					"table": "user",
					"username": "<?php echo $_SESSION["username"];?>"
					<?php } ?>
				}
			},
            "columnDefs": [
			{
				"targets": 0,
				"width": "100%",
				"data": "username"
			},
			{
				"targets": 1,
				"data": "password",
				"render": function ( data, type, row ) {
                    return '<button onClick=changePassword(' + data + ',\"' + row['username'] + '\")>password</button>';
                },
				"searchable": false,
				"orderable": false,
			},
			<?php if($_SESSION["isAdmin"] == 'true') {?>
			{
				"targets": 2,
				"data": "isAdmin",
				"render": function ( data, type, row ) {
					if(data == 'true')
					{
						return '<input type="checkbox" onclick=changeIsAdmin(\"' + row['username'] + '\",this.checked) checked>';
					}
					else
					{
						return '<input type="checkbox" onclick=changeIsAdmin(\"' + row['username'] + '\",this.checked)>';
					}	
                },
				"searchable": false,
				"orderable": false,
			}
			<?php } ?>
			]
		});
	});
	
	//change password function
	function changePassword(currentPassword, username) {
		var newPassword = onClick=window.prompt(username + '\nCurrent password:\n' + currentPassword + '\nChange password:',currentPassword);
		if(newPassword) //user clicked ok
		{
			$.post( "updateUser.php", { job: "update_password", username: username, password: newPassword })
				.done(function( data ) {
					alert("Success");
					table.ajax.reload( null, false);
				});
		}
	}
	
	//change isAdmin function
	function changeIsAdmin(username, isChecked) {
		$.post( "updateUser.php", { job: "update_isAdmin", username: username, isAdmin: isChecked })
			.done(function( data ) {
				alert("Success\nNotice that only after the next login it will take changes!");
			});
	}
</script>
<div id="upload1">Upload</div>
<div id="upload2">Upload</div>
<table id="users_table" class="display" cellspacing="0" width="100%">
	<thead>
        <tr>
            <th>Username</th>
            <th>Password</th>
			<?php if($_SESSION["isAdmin"] == 'true') {?>
			<th>Is admin</th>
			<?php }?>

            
        </tr>
    </thead>
</table>
<button onClick=location.href="./logout.php">Logout</button>
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>