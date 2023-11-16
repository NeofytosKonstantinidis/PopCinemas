<?php
$rawdata = json_decode(file_get_contents('php://input'),true);
if(isset($rawdata[0], $rawdata[1]))
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "cinemadtbs";
	$conn = new mysqli($servername, $username, $password, $dbname);//Connects to database
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error); }
	$success= true;
	$removed = $rawdata[0];
	$added = $rawdata[1];
	for($i=0, $iMax = count($removed); $i< $iMax; $i++)
	{
		$time = $removed[$i];
		$sql = "DELETE FROM projtime WHERE projtime.dayP = '".$time[0]."' AND projtime.timestamp='".$time[1]."'";
		if ($conn->query($sql) === TRUE) {
		} else {
		  $success=false;
		}
		
	}
	for($i=0, $iMax = count($added); $i< $iMax; $i++)
	{
		$time = $added[$i];
		$sql = "INSERT INTO projtime (projtime.dayP, projtime.timestamp) VALUES  ('".$time[0]."','".$time[1]."')";
		if ($conn->query($sql) === TRUE) {
		} else {
		  $success=false;
		}
	}
	if($success)
	{
		echo json_encode("success");
	}
	else
	{
		echo json_encode("fail");
	}
	
 }
	

?>