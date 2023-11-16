<?php 
header('Content-type: application/json');
$filled= true;
$check2 = true;
$success = true;
$trailers="";
$photos="";
$edit;
$id=-1;
$response="adeio";
$rawdata = json_decode(file_get_contents('php://input'),true);
if(isset($rawdata['type']) && !empty($rawdata['type']))
{
	$response="vrhke type";
	if($rawdata['type']==='edit')
	{
		$edit=true;
		if(isset($rawdata['id']) && !empty($rawdata['id']))
		{
			$id=$rawdata['id'];
		}
		else
		{
			$filled= false;	
		}
	}
	else if ($rawdata['type']==='add')
	{
		$edit=false;
	}
	else{
		$filled= false;
	}
}
else
{
	$filled= false;
}
if(isset($rawdata['title'], $rawdata['description'], $rawdata['releasedate'], $rawdata['duration'], $rawdata['preview']) && !empty($rawdata['title']) && !empty($rawdata['description']) && !empty($rawdata['releasedate']) && !empty($rawdata['duration']) && !empty($rawdata['preview']))
{
	
}
else
{
	$filled = false;
}
if(!isset($rawdata['trailers']) || empty($rawdata['trailers']))
{
	$check2 = false;
	$trailers="";
}
if(!isset($rawdata['photos']) || empty($rawdata['photos']))
{
	if(!$check2)
	{
		$filled = false;
	}
	$photos="";
}
if(!isset($rawdata['genres']) || empty($rawdata['genres']))
{
	$filled=false;
}
if($filled)
{
	$temptitle = addslashes($rawdata['title']);
	$title = str_replace('%26','&',$temptitle);
	$tempdescription = addslashes($rawdata['description']);
	$description = str_replace('%26','&',$tempdescription);
	$description = ltrim($description);
	$year = $rawdata['year'];
	$releasedate = $rawdata['releasedate'];
	$duration = $rawdata['duration'];
	$preview = $rawdata['preview'];
	$trailers = $rawdata['trailers'];
	$photos = $rawdata['photos'];
	$genres = $rawdata['genres'];
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "cinemadtbs";
	//echo $rawdata['type'].$title.$description.$year.$releasedate.$duration.$preview.$trailers.$photos.$genres;
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error); }
	$sqlquery="";
	if($edit)
	{
		$sqlquery = "UPDATE movies SET Title='".$title."', Description='".$description."', Year=".$year.", ReleaseDate='".$releasedate."', Duration=".$duration.", preview='".$preview."', Trailers='".$trailers."', Photos='".$photos."' WHERE Movie_ID=".$id.";";
	}
	else
	{
		$sqlquery = "INSERT INTO movies (Title,Description,Year,ReleaseDate,Duration,preview,Trailers,Photos,isPlaying) VALUES ('".$title."','".$description."',".$year.",'".$releasedate."',".$duration.",'".$preview."','".$trailers."','".$photos."',1)";
	}
	if ($conn->query($sqlquery) === TRUE) {
	} else {
	  $success=false;
	}
	$genreslist = explode(",",$genres);
	if($success)
	{
		$response="eftase 101";
		if($edit)
		{
			$sqlquery = "DELETE FROM moviegenres WHERE movie_ID=".$id;
			if ($conn->query($sqlquery) === TRUE) {
			} else {
			  $success=false;
			}
		}
		else{
			$sqlquery = "SELECT Movie_ID FROM movies WHERE Title='".$title."' AND ReleaseDate='".$releasedate."'";
			$result = $conn->query($sqlquery);
			if ($result->num_rows > 0) {
				
				while($row = $result->fetch_assoc()) {
					$id = $row['Movie_ID'];
				}    ; }
		}
			foreach ($genreslist as $gen)
			{
				if($success)
				{
				$sqlquery = "INSERT INTO moviegenres (genre_ID,movie_ID) VALUES (".$gen.",".$id.")";
				if ($conn->query($sqlquery) === TRUE) {
					} else {
					  $success=false;
					}
				}
			}
	}
	$conn->close();
}


if($success)
{
	echo json_encode("success");
}else{
	echo json_encode("error");
}


?>