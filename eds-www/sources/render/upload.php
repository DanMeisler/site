<?php
require_once('authenticate.php');
$output_dir = "uploads/";
$fileName = "modem.csv";
if(isset($_FILES["file"]))
{
	$ret = array();
 	move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir.$fileName);
    $ret[]= $fileName;
    echo json_encode($ret);
 }
 ?>