<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/menustyle.css">
	<link rel="stylesheet" type="text/css" href="./css/style.css"/>
	<link rel="stylesheet" href="./css/indexstyle.css">
	<link rel="stylesheet" href="./css/moviestyle.css"/>
    <link rel="stylesheet" media="only screen and (max-width: 740px)" href="./css/mobilestyle.css"/>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js'></script>
</head>
<body>
<title>Pop Cinemas</title>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
$conn = new mysqli($servername, $username, $password, $dbname);
$offset=0;
$header="";
$sql;
$search;
$error=false;
$multiple=false;
$foundmovies=false;

if(isset($_GET["offset"]))
{
	$offset = $_GET["offset"];
}
if (isset($_GET["search"]))
	{
		$genres = array("Action","Adventure","Horror","Short","Drama","Mystery","Comedy","Fantasy","Sci-Fi","Romance","Animation","Crime","Talk-Show","Family","Documentary","Reality-TV","Music","History","Western","Biography","War","Musical","Game-Show","Sport","Film-Noir","Adult");
		$search = $_GET["search"];
		$search = $conn->real_escape_string($search);
		$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies`  LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE Title LIKE '%".$search."%' GROUP BY movies.Movie_ID";
		$header= "Results of '".$search."'";
		if(ctype_digit($search) && strlen($search)==4)
		{
			$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE Year=".$search." GROUP BY movies.Movie_ID ORDER BY ReleaseDate";
			$header= 'Released in '.$search;
			$multiple=true;
		}
		else
		{
			$count=1;
			foreach ($genres as &$genre)
			{
				if(strtoupper($search)===strtoupper($genre))
				{
					$multiple=true;
					$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID INNER JOIN moviegenres ON movies.Movie_ID = moviegenres.movie_ID WHERE moviegenres.genre_ID=".$count." GROUP BY movies.Title";
					$header = $genre." Movies";
					echo $sql;
				}
			$count++;
			}
		}
		
		
	}
else if(isset($_GET["genre"]))
	{
		$genre = $_GET["genre"];
		if(ctype_digit($genre))
		{
		$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID INNER JOIN moviegenres ON movies.Movie_ID = moviegenres.movie_ID WHERE moviegenres.genre_ID=".$genre." GROUP BY movies.Title";
		$result = $conn->query("SELECT Title FROM genre WHERE Gen_ID=".$genre.";");
	if ($result->num_rows > 0) {

			while($row = $result->fetch_assoc()) {
				$header = $row['Title']." Movies";
			};


			} else {   $header= 'No Genre Found'; }
		}
		else
		{$error=true;}
		
	}
	else if (isset($_GET["released"]))
	{
		$released = $_GET["released"];
		if($released==="coming_soon")
		{
			$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE (ReleaseDate >= DATE_ADD(CURDATE(), INTERVAL 1 DAY)) GROUP BY movies.Movie_ID";
			$header= 'Coming Soon';
		}
		else if($released==="playing_now")
		{
			$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE (ReleaseDate <= CURDATE()) AND ( isPlaying=1) GROUP BY movies.Movie_ID";
			$header= 'Playing Now';
		}
	}
	else if (isset($_GET["year"]))
	{
		$year = $_GET["year"];
		if(ctype_digit($year))
		{
			$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE Year=".$year." GROUP BY movies.Title ORDER BY ReleaseDate";
			$header= 'Released in '.$year;
		}
		else
		{$error=true;}
		
	}
	else
	{
		header("Location: ./");
	}

if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error); }
require 'curtains.php';
?>

<div class="sections">

<div class="section gapless">
	<div class="sectitle">
	<h2><?php echo $header ?></h2>
	</div>
	<?php
	if(!$error)
	{
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$foundmovies=true;
		$counter=0;
			echo "<div class=\"cardcontainer\">";
			while($row = $result->fetch_assoc()) {
				if($counter==4)
				{
					echo "</div>";
					echo "<div class=\"cardcontainer\">";
					$counter=0;
				}
				echo "<div class=\"card\" onclick=\"CloseCurtains('./movie?id=".$row['Movie_ID']."')\" >";
				echo "<div class=\"movieimage\">";
				echo "<img src=\"./Images/".$row['preview']."\" alt=\"f9preview\" style=\"width:100%\">";
				echo "</div>";
				echo "<div class=\"divmovietitle\">";
				echo "<h1 class=\"movietitle\" >".$row['Title']."</h1>";
				echo "</div>";
				if(!is_null($row['rating']))
					{
						echo "<span class=\"fa fa-star checked rate\"></span>";
						echo "<p class=\"rate-text\">".$row["rating"]."</p>";
					}
				echo "</div>";
				$counter++;
			};
			echo "</div>";

			} else {   echo "<h4 style=\"padding-top: 15px;\"><center>No Movies found</center></h4>"; }
	}else {   echo "<h4 style=\"padding-top: 15px;\"><center>No results found</center></h4>"; }
	?>

</div>
<?php
if (isset($search))
{
	if($multiple)
	{
		echo "<div class=\"section gapless\">";
		echo "<div class=\"sectitle\">";
		echo "<h2>Results of '".$search."'</h2>";
		echo "</div>";
		$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies`  LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE Title LIKE '%".$search."%' GROUP BY movies.Movie_ID";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$foundmovies=true;
			$counter=0;
				echo "<div class=\"cardcontainer\">";
				while($row = $result->fetch_assoc()) {
					if($counter==4)
					{
						echo "</div>";
						echo "<div class=\"cardcontainer\">";
						$counter=0;
					}
					echo "<div class=\"card\" onclick=\"CloseCurtains('./movie?id=".$row['Movie_ID']."')\" >";
					echo "<div class=\"movieimage\">";
					echo "<img src=\"./Images/".$row['preview']."\" alt=\"f9preview\" style=\"width:100%\">";
					echo "</div>";
					echo "<div class=\"divmovietitle\">";
					echo "<h1 class=\"movietitle\" >".$row['Title']."</h1>";
					echo "</div>";
					if(!is_null($row['rating']))
					{
						echo "<span class=\"fa fa-star checked rate\"></span>";
						echo "<p class=\"rate-text\">".$row["rating"]."</p>";
					}
					
					echo "</div>";
					$counter++;
				};
				echo "</div>";

				} else {   echo "<h4 style=\"padding-top: 15px;\"><center>No results found</center></h4>"; }
		echo "</div>";
	}
}
if (!$foundmovies)
{
	echo "<div class=\"section gapless\">";
	echo "<div class=\"sectitle\">";
	echo "<h2>Suggested Movies</h2>";
	echo "</div>";
	$sql="SELECT movies.Movie_ID,movies.Title,movies.preview,ROUND(AVG(moviesrating.rating),1) as rating FROM movies INNER JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE RATING>=3 AND movies.isPlaying=1 GROUP BY movies.Movie_ID ORDER BY AVG(moviesrating.rating) desc";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$foundmovies=true;
		$counter=0;
			echo "<div class=\"cardcontainer\">";
			while($row = $result->fetch_assoc()) {
				if($counter==4)
				{
					echo "</div>";
					echo "<div class=\"cardcontainer\">";
					$counter=0;
				}
				echo "<div class=\"card\" onclick=\"CloseCurtains('./movie?id=".$row['Movie_ID']."')\" >";
				echo "<div class=\"movieimage\">";
				echo "<img src=\"./Images/".$row['preview']."\" alt=\"f9preview\" style=\"width:100%\">";
				echo "</div>";
				echo "<div class=\"divmovietitle\">";
				echo "<h1 class=\"movietitle\" >".$row['Title']."</h1>";
				echo "</div>";
				echo "<span class=\"fa fa-star checked rate\"></span>";
				echo "<p class=\"rate-text\">".$row["rating"]."</p>";
				echo "</div>";
				$counter++;
			};
			echo "</div>";

			} else {   echo "<h4 style=\"padding-top: 15px;\"><center>No Suggested Movies found</center></h4>"; }
	echo "</div>";
}
?>
<div class="footer">
<footer>
<div style="text-align: center;">© 2021 Pop Cinemas. All Rights Reserved.</div>
</footer>
</div>
</div>
</div>
</body>
</html>