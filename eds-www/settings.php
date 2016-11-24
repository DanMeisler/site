<?php
	require_once('authenticate.php');
?>
<link href="sources/css/uploadFile.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="sources/js/uploadFile.js"></script>
<script>
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
	});
</script>
<div id="upload1">Upload</div>
<div id="upload2">Upload</div>
<div style="position: fixed; width: 229px; height: 151px; bottom: 10;left: 10; background-image: url('sources/images/logo.png');">
</div>