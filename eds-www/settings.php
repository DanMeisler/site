<?php
	require_once('authenticate.php');
?>
<link href="sources/css/uploadFile.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.12.3.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8"src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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
			url: "sources/render/units/upload.php",
			uploadStr:"Upload csv file for units rendering",
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
						url: "sources/render/units/load.php",
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
					$.post("sources/render/units/delete.php", {op: "delete",name: data[i]},
						function (resp,textStatus, jqXHR) {
							//Show Message	
							alert("File Deleted");
						});
				}
				pd.statusbar.hide(); //You choice.
			
			},
			downloadCallback:function(filename,pd)
				{
					location.href="sources/render/units/download.php?filename="+filename;
				}
		}); 
		
		$("#upload3").uploadFile({
			url: "sources/render/tags/upload.php",
			uploadStr:"Upload csv file for tags rendering",
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
						url: "sources/render/tags/load.php",
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
					$.post("sources/render/tags/delete.php", {op: "delete",name: data[i]},
						function (resp,textStatus, jqXHR) {
							//Show Message	
							alert("File Deleted");
						});
				}
				pd.statusbar.hide(); //You choice.
			
			},
			downloadCallback:function(filename,pd)
				{
					location.href="sources/render/tags/download.php?filename="+filename;
				}
		}); 
		
		var createUserDialog = $( "#addUserDiv" ).dialog({
			autoOpen: false,
			buttons: {
				"Create an account": function() {
						var username = document.getElementById("username").value;
						var password = document.getElementById("password").value;
						var isAdmin = document.getElementById("isAdmin").checked
						if(username.length > 0 && password.length > 0)
						{
							$.get( "addUser.php", {username: username, password: password, isAdmin: isAdmin })
								.done(function( data ) {
									alert("Success");
									table.ajax.reload( null, false);
								});
						}	
						else
							alert('name or password are empty');
					},
				Cancel: function() {
					createUserDialog.dialog( "close" );
				}
			},
		});
		var deleteUserDialog = $( "#deleteUserDiv" ).dialog({
			autoOpen: false,
			buttons: {
				"Delete an account": function() {
						var username = document.getElementById("usernameToDelete").value;
						$.get( "deleteUser.php", {username: username})
								.done(function( data ) {
									alert("Success");
									table.ajax.reload( null, false);
								});
					},
				Cancel: function() {
					deleteUserDialog.dialog( "close" );
				}
			},
		});
		
		table = $('#users_table').DataTable({
			dom: 'B',
			buttons: [
			<?php if($_SESSION["isAdmin"] == 'true') {?>
            {
				
                text: 'Add a user',
                action: function () {
                    createUserDialog.dialog('open');
                }
				
            },
			{
				
                text: 'Delete a user',
                action: function () {
                    deleteUserDialog.dialog('open');
                }
				
            }
			<?php } ?>
			],
			"ajax": {
				"url": "getUsers.php",
				"type": "POST",
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
                    return '<button onClick=changePassword(\"' + data + '\",\"' + row['username'] + '\")>password</button>';
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
				table.ajax.reload( null, false);
			});
	}
</script>
<div id="controls" style="position: fixed;top: 20;right: 20;">
    <button onclick="window.location.href='/index.php'">map view</button>
	<button onclick="window.location.href='/logout.php'">logout</button>
</div>
<br><br><br><br><br><br>
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
<br><br><br><br><br><br>
<div id="upload1">Upload</div>
<div id="upload2">Upload</div>
<div id="upload3">Upload</div>

<br><br><br><br><br><br><br><br><br><br><br><br>
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>


<div id="addUserDiv" title="Add a user"> 
    <fieldset>
      <label for="username">Username</label>
      <input type="text" name="username" id="username">
      <label for="password">Password</label>
      <input type="password" name="password" id="password">
	  <label for="isAdmin">isAdmin</label>
	  <input type="checkbox" name="isAdmin" id="isAdmin">
    </fieldset>
</div>

<div id="deleteUserDiv" title="Delete a user"> 
    <fieldset>
      <label for="username">Username</label>
      <input type="text" name="username" id="usernameToDelete">
    </fieldset>
</div>