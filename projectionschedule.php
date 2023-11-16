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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>


</head>
<body>
<title>Projections</title>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
$conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error); }
$adminpage=true;
require 'curtains.php';
?>

<script>var movies = [];</script>
<div class="sections">
<div class="section gapless">
	<div class="sectitle">
	<h2>Projections</h2>
</div>
<div>

<label for="movies">Choose a movie: </label>
<select class="inputs" name="movies" id="movies">
<?php
//select movies data from movie
$sql = "SELECT Movie_ID, Title FROM movies";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo "<option value='".$row['Movie_ID']."'>".$row['Title']."</option>";
		echo '<script>tempmv={"movie":"'.$row['Title'].'","id":"'.$row['Movie_ID'].'"};';
		echo "movies.push(tempmv);</script>";
	};
}
?>
</select>
<br>
<label for="proj">Choose a Projection Room: </label>
<select class="inputs" name="proj" id="proj">
<?php
//select halls and cinemas info
$sql="SELECT halls.hall_ID, halls.hall_Name, cinemas.name FROM halls INNER JOIN cinemas ON halls.cinema_ID = cinemas.cinema_ID";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo "<option value='".$row['hall_ID']."'>".$row['name']." ".$row['hall_Name']."</option>";
	};
}
?>
</select>
<br>
<label for="time">Choose time: </label>
<select class="inputs" name="time" id="time">
<?php
//selects every timestamp
$sql="SELECT DISTINCT projtime.timestamp FROM projtime";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$time = substr($row['timestamp'],0,-3);
		echo "<option value='".$time."'>".$time."</option>";
	};
}
?>
</select>
</div>
<button onclick="loadDates()"  class="seemore">load Dates</button><br>
<div class="datestable" style="min-height:100px;">
<div id="loader">
<div style="position:relative; width:100%"><span onclick="nextWeek('-')" class="fa fa-chevron-left cancelbutton leftarr"></span><h2 id="currmonth"></h2> <span onclick="nextWeek('+')" class="fa fa-chevron-right cancelbutton rightarr"></span></div>
<div class="daterow">
  <div class="datecolumn">
  <div class="datetitles">
    <h4 style="text-align:center;">Monday</h4>
	<h4 id="datemonday" style="text-align:center;"></h4>
	</div>
    <div id="resmonday" class="dateresult disabled"></div>
  </div>
  <div class="datecolumn">
  <div class="datetitles">
    <h4 style="text-align:center;">Tuesday</h4>
	<h4 id="datetuesday" style="text-align:center;"></h4>
	</div>
    <div id="restuesday" class="dateresult disabled"></div>
  </div>
  <div class="datecolumn">
  <div class="datetitles">
    <h4 style="text-align:center;">Wednesday</h4>
	<h4 id="datewednesday" style="text-align:center;"></h4>
	</div>
    <div id="reswednesday" class="dateresult disabled"></div>
  </div>
  <div class="datecolumn">
  <div class="datetitles">
    <h4 style="text-align:center;">Thursday</h4>
	<h4 id="datethursday" style="text-align:center;"></h4>
	</div>
    <div id="resthursday" class="dateresult disabled"></div>
  </div>
  <div class="datecolumn">
  <div class="datetitles">
    <h4 style="text-align:center;">Friday</h4>
	<h4 id="datefriday" style="text-align:center;"></h4>
	</div>
    <div id="resfriday" class="dateresult disabled"></div>
  </div>
  <div class="datecolumn">
  <div class="datetitles">
    <h4 style="text-align:center;">Saturday</h4>
	<h4 id="datesaturday" style="text-align:center;"></h4>
	</div>
    <div id="ressaturday" class="dateresult disabled"></div>
  </div>
  <div class="datecolumn">
	<div class="datetitles">
    <h4 style="text-align:center;">Sunday</h4>
	<h4 id="datesunday" style="text-align:center;"></h4>
	</div>
    <div id="ressunday" class="dateresult disabled"></div>
  </div>
</div>

<div id="loaderback" style="display:none; height: 100%;width: 100%;position: absolute;top: 0%;background-image: linear-gradient(to bottom, #ffffffab,#ffffffab,#ffffffab, #fff0);"> </div>
<img id="loaderanim" src="./Images/loadinganimation.gif" style="display:none; position:absolute; top:30%; left:50%; transform:translate(-50%,-50%);" alt="">

<button onclick="saveChanges()"  class="seemore">Save Changes</button><br>
</div>
</div>

</div>

<script>
    let mid;
    let projid;
    let timesel;
    const loader = document.getElementById("loader");
    const loaderback = document.getElementById("loaderback");
    const loaderanim = document.getElementById("loaderanim");

    function callLoader()//Every time its called it shows a loading screen
{
	if (loader.style.maxHeight)
	{
		//loader.style.maxHeight=null;
		//setTimeout(function(){
		//	loader.style.visibility="hidden";
		//}, 200);
		
	}
	else
	{
		loader.style.maxHeight=loader.scrollHeight + "px";
		loader.style.visibility="visible";
	}
}
function callAnimation()// calls loader animation
{
	if(loaderanim.style.display=="none")
	{
		loaderanim.style.display="block";
		loaderback.style.display="block";
	}
	else
	{
		loaderanim.style.display="none";
		loaderback.style.display="none";
	}
}

    const days = [];
    let resdates = [];
    let resdays = [];
    const curdates = [];
    const newdate = new Date();
    const today = new Date();
    let addeddays = [];
    let deleteddays = [];
    days.push('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
const monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];
getdate();	
function getdate()//gets current date
{
    const c = newdate.toUTCString();
    const curr = new Date(c);
    const first = curr.getDate() - curr.getDay() + 1;
    const firstday = new Date(curr.setDate(first));
    let date = firstday;
    for (let i=0;i<days.length;i++)
	{
        let tempdate = date.getDate();
        let tempmonth = date.getMonth() + 1;
        if(tempdate<10)
		{
			tempdate="0"+tempdate;
		}
		if(tempmonth<10)
		{
			tempmonth= "0"+tempmonth;
		}
		curdates.push(date);
		document.getElementById('date'+days[i]).innerHTML =  tempdate+"/"+tempmonth+"/"+date.getFullYear();
		date = new Date(curr.setDate(curr.getDate()+1));		
		document.getElementById('currmonth').innerHTML= monthNames[newdate.getMonth()];
	}
}
function loadDates()// loads already submitted dates
{
	mid = document.getElementById('movies').value;
	projid = document.getElementById('proj').value;
	timesel = document.getElementById('time').value;
	if(projid && timesel)
	{
	callLoader();
	callAnimation();
        const obj = '{"projroom":"' + projid + '","time":"' + timesel + '"}';
        jQuery.ajax({
			url:"schedulecontroller.php",
			type:"POST",
			data:obj,
			dataType:"json",
			success:function(result)
			{
				if(result['status']=='success')
				{
					if(result['days'])
					{
						resdays = result['days'];
					}else {resdays = [];}
					if(result['results'])
					{
						resdates = result['results'];
					}else {resdates = [];}
					addeddays = [];
					deleteddays = [];
					
					disableDates();
					
					setTimeout(function(){
					callAnimation();
					}, 500);
				}
				else if(result['status']==='error')
				{
					alert("Error! Something Went Wrong with the data.");
				}
			},
			error: function(){
				alert('Something Went Wrong!');
			}
		});
	}
	else
	{
		alert("Some inputs have no options selected.");
	}
	
}
function disableDates() //disables dates that dont contain the timestamp
{
	
		for (let i=0;i<days.length;i++)
		{
            const tempda = document.getElementById('res' + days[i]);
            tempda.classList.add('disabled');
			tempda.classList.remove('takendate');
			tempda.classList.remove('minusbackground');
			tempda.innerHTML="";
			
		}
		for (let i=0;i<resdays.length;i++)
		{
            const tempd = document.getElementById('date' + resdays[i]).innerHTML;
            const splitd = tempd.split("/");
            const resda = new Date(splitd[2] + "-" + splitd[1] + "-" + splitd[0]);
            if(resda >= today)
			{
				let tempday = document.getElementById('res'+resdays[i]);
				if(tempday)
				{
					tempday.classList.remove('disabled');
					tempday.innerHTML="<span onclick=\"addmovie('"+resdays[i]+"')\" class=\"fa fa-plus addbutt\"></span>";
				}
			}
			
		}
		for (let i=0;i<resdates.length;i++)
		{
			let tempdate = document.getElementById('date'+resdates[i]['day']);
			if(tempdate)
			{
				var tempdat = new Date(resdates[i]['date']);
                let tempordate = tempdat.getDate();
                let tempmonth = (tempdat.getMonth() + 1);
                if(tempordate<10)
				{
					tempordate = "0"+tempordate;
				}
				if(tempmonth<10)
				{
					tempmonth = "0"+tempmonth;
				}
				tempdat = tempordate+'/'+tempmonth+'/'+tempdat.getFullYear();
				if(tempdate.innerHTML === tempdat)
				{
					let tempday = document.getElementById('res'+resdates[i]['day']);
					if(tempday)
					{
						tempday.classList.add('takendate');
						tempday.innerHTML="<div > Movie Playing: "+resdates[i]['title']+"</div><span onclick=\"removemovie('"+resdates[i]['day']+"','"+resdates[i]['projid']+"')\" class=\"fa fa-times cancelbutton small white\"></span>";
					}
				}
			}
		}
		for (let i=0;i<addeddays.length;i++)
		{
			console.log(addeddays[i][3]);
            const tempdate = document.getElementById('date' + addeddays[i][3]);
            if(tempdate)
			{
				if(tempdate.innerHTML === addeddays[i][2])
				{
                    const tempday = document.getElementById('res' + addeddays[i][3]);
                    if(tempday)
					{
						tempday.classList.add('minusbackground');
						tempday.innerHTML="<span onclick=\"raddedmovie('"+addeddays[i][2]+"','"+addeddays[i][3]+"')\" class=\"fa fa-minus minusbutt\"></span>";
					}
				}
			}
		}
}
function nextWeek(symbol)//gets next or previous week
{
	if(symbol=='+')
	{
		newdate.setDate(newdate.getDate() + 7);
		getdate();
		disableDates();
		
	}
	else if (symbol=='-')
	{
		newdate.setDate(newdate.getDate() - 7);
		getdate();
		disableDates();
	}
}
function removemovie(from,id)// removes a movie from projection
{
    const movieblock = document.getElementById('res' + from);
    const date = document.getElementById('date' + from);
    for (let i=0;i<resdates.length;i++)
	{
		if(resdates[i]['projid']==id)
		{
		    console.log(resdates[i]);
		    if(resdates[i]['tickets']>0)
            {
                alert('Tickets are booked on this date. Cant be deleted.')
            }else
            {
                resdates.splice(i,1);
                deleteddays.push([id,date.innerHTML,from]);
                movieblock.innerHTML ="<span onclick=\"addmovie('"+from+"')\" class=\"fa fa-plus addbutt\"></span>";
                movieblock.classList.remove('takendate');
            }
		}
	}	
}
function addmovie(day)//adds a movie to a projection
{
    const movieblock = document.getElementById('res' + day);
    const date = document.getElementById('date' + day);
    movieblock.classList.add('minusbackground');
	addeddays.push([mid,projid,date.innerHTML,day,timesel]);
	movieblock.innerHTML="<span onclick=\"raddedmovie('"+date.innerHTML+"','"+day+"')\" class=\"fa fa-minus minusbutt\"></span>";
}
function raddedmovie(dater,day)//removes added movie
{
    const movieblock = document.getElementById('res' + day);
    const date = document.getElementById('date' + day);
    movieblock.classList.remove('minusbackground');
	movieblock.innerHTML ="<span onclick=\"addmovie('"+day+"')\" class=\"fa fa-plus addbutt\"></span>";
	for (let i=0;i<addeddays.length;i++)
	{
		if(addeddays[i][2]==dater)
		{
			addeddays.splice(i,1);
		}
	}
}
function bookmovies()//after movies saved converts them to booked movies (red and movie name with white letters)
{
    let mname = "";
    for(let j=0;j<movies.length;j++)
	{
		if(movies[j]['id']==mid)
		{
			mname= movies[j]['movie'];
		}
	}
	for (let j=0;j<deleteddays.length;j++)
	{
		for(let i=0;i<resdates.length;i++)
		{
			let splitd = deleteddays[j][1].split("/");
			let resda = splitd[2]+"-"+splitd[1]+"-"+splitd[0];
			if(resda==resdates[i]['date'] && deleteddays[j][2]==resdates[i]['day'] && mname==resdates[i]['title'])
			{
				resdates.splice(i,1);
			}
		}
	}
	deleteddays= [];
	for(let i=0;i<addeddays.length;i++)
	{
		let splitd = addeddays[i][2].split("/");
        let resda = splitd[2]+"-"+splitd[1]+"-"+splitd[0];
        let obj = {"projid":addeddays[i][1],"title":mname,"date":resda,"day":addeddays[i][3]}
		resdates.push(obj);
	}
	addeddays = [];
	disableDates();
}
function saveChanges()//saves movies projection dates
{
    let obj = {"removed":deleteddays,"added":addeddays};
    let jsonobj = JSON.stringify(obj);
	jQuery.ajax({
			url:"schedulecontroller.php",
			type:"POST",
			data:jsonobj,
			dataType:"json",
			success:function(result)
			{
				if(result['status']=='success')
				{
					bookmovies();
					alert("Projections Updated Successfully");
				}
				else if(result['status']=='error')
				{
					alert("Error! Something Went Wrong with the data.");
				}
			},
			error: function(){
				alert('Something Went Wrong!');
			}
		});
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