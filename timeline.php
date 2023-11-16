<!DOCTYPE html>
<html>
<head>
	<link href="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet"/>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/menustyle.css">
	<link rel="stylesheet" type="text/css" href="./css/style.css"/>
	<link rel="stylesheet" href="./css/indexstyle.css">
	<link rel="stylesheet" href="./css/moviestyle.css"/>
    <link rel="stylesheet" media="only screen and (max-width: 740px)" href="./css/mobilestyle.css"/>
	<script src="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<style>


</style>
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
$adminpage=true;
require 'curtains.php';//calls curtains.php
?>

<div class="sections">
<div class="section gapless">
	<div class="sectitle">
	<h2>Week</h2>
</div>
<br>
<div class="week">
<div class="weekday">	
<strong class="day">Monday</strong>
 <ul class="timeslist" id="monday">
</ul> 
</div>
<br>

<div class="weekday">	
<strong class="day">Tuesday</strong>
 <ul class="timeslist" id="tuesday">
</ul> 
</div>
<br>

<div class="weekday">	
<strong class="day">Wednesday</strong>
 <ul class="timeslist" id="wednesday">
</ul> 
</div>
<br>

<div class="weekday">	
<strong class="day">Thursday</strong>
 <ul class="timeslist" id="thursday">
</ul> 
</div>
<br>

<div class="weekday">	
<strong class="day">Friday</strong>
 <ul class="timeslist" id="friday">
</ul> 
</div>
<br>

<div class="weekday">	
<strong class="day">Saturday</strong>
 <ul class="timeslist" id="saturday">
</ul> 
</div>
<br>

<div class="weekday">	
<strong class="day">Sunday</strong>
 <ul class="timeslist" id="sunday">
</ul> 
</div>

</div>
<br>
<button onclick="saveData()" class="seemore">Save Schedule</button>
<div class="sectitle">
	<h2>Add Projection Time</h2>
</div>
<br>
<p style="text-align:left;margin-left:10px;display: inline;">Time </p>
<input type="text" id="timer" placeholder="Time" >
<button onclick="addTime()" style="display: inline;" class="seemore">Add Time</button>
<br>
<br>
<div class="checkboxes">
<input type="checkbox" id="day1" name="day1" value="monday">
<label for="day1"> Monday</label><br>
<input type="checkbox" id="day2" name="day2" value="tuesday">
<label for="day2"> Tuesday</label><br>
<input type="checkbox" id="day3" name="day3" value="wednesday">
<label for="day3"> Wednesday</label><br>
<input type="checkbox" id="day4" name="day4" value="thursday">
<label for="day4"> Thursday</label><br>
<input type="checkbox" id="day5" name="day5" value="friday">
<label for="day5"> Friday</label><br>
<input type="checkbox" id="day6" name="day6" value="saturday">
<label for="day6"> Saturday</label><br>
<input type="checkbox" id="day7" name="day7" value="sunday">
<label for="day7"> Sunday</label><br>
</div>
</div>
<script>
    const timepicker = new TimePicker('timer', {
        theme: 'dark',
        lang: 'en',
    });
    timepicker.on('change', function(evt) {

        const value = (evt.hour || '00') + ':' + (evt.minute || '00');
        evt.element.value = value;

});
let days = [];
<?php
    //select projection day, time and movies playing from database
$sql = "select projtime.dayP, projtime.timestamp, GROUP_CONCAT(movies.Title SEPARATOR', ') AS movies FROM projtime LEFT JOIN projection ON projtime.time_ID=projection.time_ID LEFT JOIN movies ON projection.movie_ID=movies.Movie_ID GROUP BY projtime.dayP, projtime.timestamp ORDER BY projtime.dayP, projtime.timestamp ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$time = substr($row['timestamp'],0,-3);
		if($row['movies']!=null)
		{
			echo "days.push(['".$row['dayP']."','".$time."','".$row['movies']."']);\n";
		}
		else
		{
			echo "days.push(['".$row['dayP']."','".$time."']);\n";
		}
	}
}
?>
loadTime();
let tempdays = [...days];

function loadTime()//loads times added on each day
{
	for(let i=0;i<days.length;i++)
	{
		let ul = document.getElementById(days[i][0]);

		if(ul)
		{
			ul.innerHTML = "";
		}	
	}
	for(let i=0;i<days.length;i++)
	{
        let ul = document.getElementById(days[i][0]);

		if(ul)
		{
			ul.innerHTML += "<li class=\"time\" id=\"li"+i+"\"><div id=\"timed"+i+"\">"+days[i][1]+"</div><span onclick=\"removeli("+i+",'"+days[i][0]+"')\" class=\"fa fa-times cancelbutton small red\"></span></li>";
		}	
	}
}
function removeli(id,day)//Removes time from day on X click
{
    const li = document.getElementById("li" + id);
    const time = document.getElementById("timed" + id);
    if (li)
	{
        let index;
        for(let j=0;j<days.length;j++)
		{
			if(days[j][0] == day && days[j][1] == time.innerHTML)
			{
				index = j;
				
			}
		}
		if(days[index][2])
		{
			alert("These movies are asigned in this time schedule: "+days[index][2]+" .Please remove their projections before removing the time schedule");
		}
		else{
			days.splice(index,1);
			li.remove();
		}
	}
}
function addTime()//adds time to selected days
{
    let time = document.getElementById("timer").value;
    const temptime = time.split(":");
    if (parseInt(temptime[0])<10)
	{
		temptime[0]="0"+temptime[0];
		time = temptime[0]+":"+temptime[1];
	}
    const activedays = [];
    for(let i=1;i<8;i++)
	{
        const checkbox = document.getElementById("day" + i);
        if(checkbox.checked)
		{
			activedays.push([checkbox.value]);
		}
	}
	for(let i=0;i<activedays.length;i++)
	{
        let exists = false;
        for(let j=0;j<days.length;j++)
		{
			if(days[j][0] == activedays[i][0] && days[j][1] == time)
			{
				exists = true;
				
			}
		}
		if(!exists)
		{
			days.push([activedays[i][0],time]);
		}
	}
	days.sort(function(a,b){
		return Date.parse('1970/01/01 '+a[1]) - Date.parse('1970/01/01 ' + b[1]);
	});
	loadTime();
}
function saveData()//saves the data.
{
    const removed = [];
    const added = [];
    for(let i=0;i<tempdays.length;i++)
	{
		let found = false;
		for(let j=0;j<days.length;j++)
		{
			if(days[j][0] == tempdays[i][0] && days[j][1] == tempdays[i][1])
			{
				found = true;
			}
		}
		if(!found)
		{
			removed.push(i);
		}
	}
	for(i=0;i<days.length;i++)
	{
		let found = false;
		for(j=0;j<tempdays.length;j++)
		{
			if(tempdays[j][0] == days[i][0] && tempdays[j][1] == days[i][1])
			{
				found = true;
				
			}
		}
		if(!found)
		{
			added.push(i);
		}
	}
	if(added.length>0 || removed.length>0)
	{
        const remdays = [];
        const addays = [];
        for (let i=0;i<removed.length;i++)
		{
			remdays.push([tempdays[removed[i]][0],tempdays[removed[i]][1]]);
		}
		for (let i=0;i<added.length;i++)
		{
			addays.push([days[added[i]][0],days[added[i]][1]]);
		}
        const data = [remdays, addays];
        const jsondata = JSON.stringify(data);
        jQuery.ajax({
			url:"updateschedule.php",
			type:"POST",
			data:jsondata,
			dataType:"json",
			success:function(result)
			{
				if(result=='success')
				{
					alert("Schedule updated Successfully!");
				}
				else if(result=='error')
				{
					alert("Error! Something Went Wrong with the data.");
				}
				CloseCurtains("here");
			},
			error: function(){
				alert('Something Went Wrong!');
			}
		});
	}
}
</script>


<div class="footer">
<footer>
<div style="text-align: center;">© 2021 Pop Cinemas. All Rights Reserved.</div>
</footer>
</div>
</div>
</div>
</body>
</html>