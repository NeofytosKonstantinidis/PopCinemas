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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>



</head>
<body>
<title>Pop Cinemas</title>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
$conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error); }
require 'curtains.php';//Calls curtains.php
?>

<div class="sections">
<?php
require 'carousel.html';//Calls carousel.html
?>


<div class="section gapless">
	<div class="sectitle">
	<h2>Playing Now</h2>
	</div>
	<?php
    //Selects movies that started playing before current date but isPlaying is still true
	$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE (ReleaseDate <= CURDATE()) AND ( isPlaying=1) GROUP BY movies.Title LIMIT 4";
	$result = $conn->query($sql);
	$countmovies=0;
	if ($result->num_rows > 0) {
			echo "<div class=\"cardcontainer\">";
			while($row = $result->fetch_assoc()) {
				echo "<div class=\"card\" onclick=\"CloseCurtains('http://localhost/popcinemas/movie?id=".$row['Movie_ID']."')\" >";
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
				$countmovies++;
			};
			echo "</div>";
			if($countmovies>3)
			{
				echo "<button onclick=\"CloseCurtains('http://localhost/popcinemas/movies?released=playing_now')\" class=\"seemore\">Load All</button>";
			}
			} else {   echo "<h4 style=\"padding-top: 15px;\"><center>No Movies found</center></h4>"; }
	
	?>
</div>

<div class="section">
	<div class="sectitle">
	<h2>Coming Soon</h2>
	</div>
	<?php
    //Selects all movies that will start playing soon
	$sql="SELECT movies.Movie_ID,Title,preview,ROUND(AVG(moviesrating.rating),1) as rating FROM `movies` LEFT JOIN moviesrating ON movies.Movie_ID = moviesrating.movie_ID WHERE (ReleaseDate >= DATE_ADD(CURDATE(), INTERVAL 1 DAY)) GROUP BY movies.Title LIMIT 4";
	$result = $conn->query($sql);
	$countmovies=0;
	if ($result->num_rows > 0) {
			echo "<div class=\"cardcontainer\">";
			while($row = $result->fetch_assoc()) {
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
				$countmovies++;
			};
			echo "</div>";
			if($countmovies>3)
			{
				echo "<button onclick=\"CloseCurtains('./movies?released=coming_soon')\" class=\"seemore\">Load All</button>";
			}
			} else {   echo "<h4 style=\"padding-top: 15px;\"><center>No Movies found</center></h4>"; }
	$conn->close();
	?>
	
</div>
<div class="footer">
<footer>
<div style="text-align: center;">© 2021 Pop Cinemas. All Rights Reserved.</div>
</footer>
</div>
</div>
</div>
</body>
</html>