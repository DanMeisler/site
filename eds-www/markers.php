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
						"UID" => '$UID',
						"DATE" => '$DATE',
						"LATITUDE" => '$LATITUDE',
						"LONGITUDE" => '$LONGITUDE'
					),
					"tags" => array(
						'$push' => array(
							'TID' => '$TID',
							'MCUTMP' => '$MCUTMP'
						)
					),
				),
			),
	);
	// find everything in the collection
	$results = $collection->aggregate($ops)["result"];
	$m->close();
	$units = array();
	foreach ($results as $doc) {
		$lat = $doc["_id"]["LATITUDE"];
		$lon = $doc["_id"]["LONGITUDE"];
		$unitID = $doc["_id"]["UID"];
		$info = array();
		array_push($info,$unitID);
		array_push($info,$doc["_id"]["DATE"]);
		$tags = array();
		foreach ($doc["tags"] as $tag) {
			array_push($tags,array($tag['TID'],$tag['MCUTMP']));
		}
		array_push($info,$tags);
		array_push($units,array( $lat, $lon, $info));
	}
	echo json_encode($units);
?>
