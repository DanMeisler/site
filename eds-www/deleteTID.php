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
	if( $TID == 'all' )
	{
		$collection->drop();
	}
	else
	{
		$collection->remove(array('TID' => $TID));
	}
	$m->close();
	
	echo('deleted successfuly!');
?>