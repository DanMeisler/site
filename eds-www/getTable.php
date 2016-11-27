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
	elseif($_REQUEST["table"] == "history")
	{
		$collection = $db->history;
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
	elseif($_REQUEST["table"] == "users")
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
	elseif($_REQUEST["table"] == "user")
	{
		$collection = $db->users;
		$username = $_REQUEST['username'];
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
	else
	{
		$m->close();
		die("No such collection");
	}

	
	
	

?>