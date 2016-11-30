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
	$password = $_REQUEST['password'];
	$isAdmin = $_REQUEST['isAdmin'];
	if(empty($username) || empty($password) || empty($isAdmin))
	{
		$m->close();
		die('missing field');
	}
	
	$collection->remove(array('username' => $username));
	$collection->insert(array('username' => $username, 'password' => $password, 'isAdmin' => $isAdmin));
	$m->close();
?>