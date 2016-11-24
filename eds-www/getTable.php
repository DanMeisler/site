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
	
	// select a collection (analogous to a relational database's table)
	if($_REQUEST["table"] == "currentState")
	{
		$collection = $db->currentState;
	}
	elseif($_REQUEST["table"] == "history")
	{
		$collection = $db->history;
	}
	else
		die("No such collection");
	
	
	
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
?>