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
	$collection = $db->currentState;
	
	$ops = array(
			array(
				'$group' => array(
					"_id" => array(
						"Modem ID" => '$Modem ID',
						"Date" => '$Date',
						"Latitude" => '$Latitude',
						"Longitude" => '$Longitude'
					),
					"units" => array(
						'$push' => array(
							'Unit ID' => '$Unit ID',
							'MCU_TEMPERATURE' => '$MCU_TEMPERATURE'
						)
					),
				),
			),
	);
	// find everything in the collection
	$results = $collection->aggregate($ops)["result"];
	$m->close();
	$modems = array();
	foreach ($results as $doc) {
		$lat = $doc["_id"]["Latitude"];
		$lon = $doc["_id"]["Longitude"];
		$modemId = $doc["_id"]["Modem ID"];
		$info = array();
		array_push($info,$modemId);
		array_push($info,$doc["_id"]["Date"]);
		$units = array();
		foreach ($doc["units"] as $unit) {
			array_push($units,array($unit['Unit ID'],$unit['MCU_TEMPERATURE']));
		}
		array_push($info,$units);
		array_push($modems,array( $lat, $lon, $info));
	}
	echo json_encode($modems);
?>
