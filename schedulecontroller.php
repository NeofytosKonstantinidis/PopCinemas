<?php
$table=array();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
$rawdata = json_decode(file_get_contents('php://input'),true);
if(isset($rawdata['projroom'], $rawdata['time']))
{
	$conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error); }
	
	$projroom = $rawdata['projroom'];
	$time = $rawdata['time'].":00";
	$sql = "SELECT projtime.dayP FROM projtime WHERE projtime.timestamp='".$time."'";
	$result = $conn->query($sql);
	
	$days=array();
	$closed=array();
	if ($result->num_rows > 0) {
		$count =0;
		while($row = $result->fetch_assoc()) {
			$days[$count]= $row['dayP'];
			$count++;
		};
		$table['days'] = $days;
		$sql="SELECT projtime.dayP,projection.projection_ID,movies.Title,projection.projDate,COUNT(ticket.ticket_ID)as tickets FROM projtime INNER JOIN projection ON projtime.time_ID = projection.time_ID INNER JOIN movies ON projection.movie_ID = movies.Movie_ID LEFT JOIN ticket ON projection.projection_ID=ticket.projection_ID WHERE projtime.timestamp='".$time."' AND projection.hall_ID=".$projroom." GROUP BY projection.projection_ID";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$count =0;
			while($row = $result->fetch_assoc()) {
				$closed[$count]['projid']= $row['projection_ID'];
				$closed[$count]['title']= $row['Title'];
				$closed[$count]['date']= $row['projDate'];
				$closed[$count]['day']= $row['dayP'];
				$closed[$count]['tickets']=$row['tickets'];
				$count++;
			}
			$table['results'] = $closed;
		}
	}
	$table['status'] = 'success';
    $conn->close();
	echo json_encode($table);
}
else if (isset($rawdata['removed'], $rawdata['added']))
{
	$success=true;
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error); }
	$removed = $rawdata['removed'];
	$added = $rawdata['added'];
	for ($i=0, $iMax = count($removed); $i< $iMax; $i++)
	{
		$sql = "DELETE FROM projection WHERE projection_id=".$removed[$i][0];
		if ($conn->query($sql) === TRUE) {
		} else {
		  $success=false;
		}
		
	}
	for ($i=0, $iMax = count($added); $i< $iMax; $i++)
	{
		$sql = "SELECT projtime.time_ID FROM projtime WHERE dayP='".$added[$i][3]."' AND projtime.timestamp='".$added[$i][4]."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$id = $row['time_ID'];
				$date =date_create_from_format('d/m/Y',$added[$i][2]);
				$formdate = date_format($date,'Y-m-d');
				$sql = "INSERT INTO projection (hall_ID,movie_ID,time_ID,projDate) VALUES (".$added[$i][1].",".$added[$i][0].",".$id.",'".$formdate."')";
				if ($conn->query($sql) === TRUE) {
				} else {
				  $success=false;
				}
			}
			
		}
	}
	if($success)
	{
		$table['status']='success';
	}
	else {$table['status']='error';}
    $conn->close();
	echo json_encode($table);
}else
{
	$table['status'] = 'error';
	echo json_encode($table);
}

