<?php
    require_once('authenticate.php');
	// connect
	try{
		$m = new MongoClient();
	} catch(MongoConnectionException $e)
	{
		die();
	}
	
	// select a database
	$db = $m->gpsDB;
	
if($_SESSION['isAdmin'] == 'true')
{
	$collection = $db->users;
	// find everything in the collection
	$results = $collection->find();
	$units = array();
	$recordsTotal = 0;
	foreach ($results as $doc) {
		unset($doc["_id"]);
		array_push($units,$doc);
		$recordsTotal+=1;
	}
	$m->close();
	$json = array("data" => $units);
	echo json_encode($json);
}
else
{
	$collection = $db->users;
	$username = $_SESSION['username'];
	if (isset($username)) {
		$units = array();
		$result = $collection->findOne(array("username" => $username));
		unset($result["_id"]);
		array_push($units,$result);
		$m->close();
		$json = array("data" => $units);
		echo json_encode($json);
	}
	else
	{
		$m->close();
		die("No username given");
	}
}

	
	
	

?>