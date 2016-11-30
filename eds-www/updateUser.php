<?php
	require_once('authenticate.php');
	// Get job
	$job = '';
	if (isset($_REQUEST['job'])){
		$job = $_REQUEST['job'];
		if ($job != 'update_password' &&
			$job != 'update_isAdmin'){
				die("wrong job");
			}
	} else {
		die();
	}
	// connect to mongo
	try{
		$m = new MongoClient();
	} catch(MongoConnectionException $e)
	{
		die();
	}
	// select a database
	$db = $m->gpsDB;
	// select a collection (analogous to a relational database's table)
	$collection = $db->users;  
	// Execute job
	if ($job == 'update_password'){
		// Edit password user
		if($_SESSION['isAdmin'] == 'true')
		{
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			if (isset($username) && isset($password)){
				$collection->update(array("username" => $username), array('$set' => array('password' => $password)));
			}
		}
		elseif(($_REQUEST['username'] == $_SESSION['username'])){
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			if (isset($username) && isset($password)){
				$collection->update(array("username" => $username), array('$set' => array('password' => $password)));
			}
		}
		else
			die('you are not admin');
		
	} 
	elseif ($job == 'update_isAdmin'){
		// Edit isAdmin
		if($_SESSION['isAdmin'] == 'true')
		{
			$username = $_REQUEST['username'];
			$isAdmin = $_REQUEST['isAdmin'];
			if (isset($username) && isset($password)){
				$collection->update(array("username" => $username), array('$set' => array('isAdmin' => $isAdmin)));
			}
		}
		else
			die('you are not admin');
	} 
	// Close connection
	$m->close();
?>