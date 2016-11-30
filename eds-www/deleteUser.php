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
	$collection = $db->users;
	$username = $_REQUEST['username'];
	$collection->remove(array('username' => $username));
	$m->close();
?>