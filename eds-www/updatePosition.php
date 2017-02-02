<?php
    require_once('authenticate.php');
	if($_SESSION['isAdmin'] == 'false')
	{
		die('You are not an admin');
	}
	// connect
	try{
		$m = new MongoClient();
	} catch(MongoConnectionException $e)
	{
		die('no mongo connection');
	}
	
	// select a database
	$db = $m->gpsDB;
	$collection = $db->currentState;
	$TID = $_REQUEST['TID'];
	$Lon = $_REQUEST['Longitude'];
	$Lat = $_REQUEST['Latitude'];
	if (isset($TID) && isset($Lon) && isset($Lat)){
		$collection->update(array("TID" => $TID), array('$set' => array('LONGITUDE' => $Lon, 'LATITUDE' => $Lat)));
		echo('updated successfuly!');
	}
	else
	{
		echo('error!');
	}
	$m->close();
	
	
?>