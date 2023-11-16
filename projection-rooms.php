<!DOCTYPE html>
<html lang="en">
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
    <title>Pop Cinemas</title>

</head>
<body>
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
//selects all cinemas data
$sql = "SELECT cinema_ID,cinemas.name, isOpen FROM cinemas";
$cinemas = array();
$projrooms = array();
$result = $conn->query($sql);
$count=0;
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$cinemas[$count][0]= $row['cinema_ID'];
		$cinemas[$count][1]= $row['name'];
        $cinemas[$count][2]= $row['isOpen'];
		$count++;
	}
}
//select halls data
$sql = "SELECT hall_id, hall_Name FROM halls";
$result = $conn->query($sql);
$count=0;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $projrooms[$count][0]= $row['hall_id'];
        $projrooms[$count][1]= $row['hall_Name'];
        $count++;
    }
}
$conn->close();
?>

<div class="sections">
<div class="section gapless">
<div class="sectitle">
	<h2>Cinemas</h2>
</div>
<div class="cinemasboxes">
<?php
for($i=0, $iMax = count($cinemas); $i< $iMax; $i++)
{
    echo '<label for="cinema'.$i.'">Cinema #'.($i+1).' Name: </label>';
    echo "<input class='inputs' name='cinema".$i."' placeholder='Cinema #".$i." Name' id='cinemainp".$cinemas[$i][0]."' value='".$cinemas[$i][1]."'/>";
    echo '<label for="cinemacheck'.$i.'">Is Open</label>';
    if($cinemas[$i][2]==='1')
    {
        echo '<input class="inputs" type="checkbox" name="cinemacheck'.$cinemas[$i][0].'" id="cinemacheck'.$cinemas[$i][0].'" checked>';
    }else
    {
        echo '<input class="inputs" type="checkbox" name="cinemacheck'.$cinemas[$i][0].'" id="cinemacheck'.$cinemas[$i][0].'">';
    }

    echo '<button class="sidebutton" onclick="updateCinema('.$cinemas[$i][0].')">Save Cinema #'.($i+1).' Name</button><br>';
}
?>
    <label for="addcinemainp">Cinema Name:</label>
    <input name="addcinemainp" class="inputs" placeholder="Cinema Name" id="addcinemainp"/>
    <button class="sidebutton" onclick="addCinema()">Add Cinema</button>
    <br>
</div>
</div>
<div class="section gapless">
<div class="sectitle">
	<h2>Projection Rooms</h2>
</div>
    <h4>Click to edit:</h4>
    <div class="cinemboxesd">
    <?php
    for($i=0, $iMax = count($projrooms); $i< $iMax; $i++)
    {
        echo "<div class=\"cinemabox\">".$projrooms[$i][1]."<span onclick=\"editproj(".$projrooms[$i][0].")\" class=\"fa fa-edit cancelbutton small red\"></span></div>";
    }
    ?>
    </div>
    <br>
    <br>
    <h4>Add Room:</h4>
    <label for="cinemas">Select Cinema</label>
<select class="inputs" name="cinemas" id="cinemas">
<?php
	for($i=0, $iMax = count($cinemas); $i< $iMax; $i++)
	{
	    echo '<label for="cinemas">Choose a Cinema: </label>';
		echo "<option value='".$cinemas[$i][0]."'>".$cinemas[$i][1]."</option>";
	}
?>
</select>
<br>
<label for="rows">Rows: </label>
<input type="number" class="inputs" placeholder="Rows" id="rows" name="rows">
<label for="columns">Columns: </label>
<input type="number" class="inputs" placeholder="Columns" id="columns" name="columns">
<button onclick="loadRoom()"  class="seemore">Load Projection Room</button>
<div id="roomdiv2" class="roomtableedit" >
</div>
</div>
<script>
const rows = document.getElementById('rows');
const columns = document.getElementById('columns');
const roomdiv = document.getElementById('roomdiv2');
let avseats = 0;
let disabledseats = [];
let canceledseats = [];
let rowsnum;
let colnum;

function loadRoom()//loads projection room with the inserted data
{
	if(rows.value && columns.value && roomdiv)
	{
		disabledseats = [];
		canceledseats = [];
		rowsnum= rows.value;
		colnum = columns.value;
        let htmlstring = '<table class="datastable" align="center" cellpadding="0" cellspacing="10">';
        for(let i=0; i<=rowsnum; i++)
		{
			htmlstring+='<tr>';
			if(i>0)
			{
				htmlstring +='<td class="roomtd">'+String.fromCharCode(64+i)+'</td>';
			}
			for (let j=0; j<=colnum; j++)
			{
				if(i===0)
				{
					if(j===0)
					{
						htmlstring+='<td class="roomtd"></td>';
					}
					else
					{
						htmlstring+='<td class="roomtd">'+j+'</td>';
					}
				}
				if(i>0 && j>0)
				{
				htmlstring+='<td class="roomtd">';
				htmlstring+='<div><i class="icon-Seat seat" id="seat'+String.fromCharCode(64+i)+j+'" onclick="changeColor(\'seat'+String.fromCharCode(64+i)+j+'\')"></i></div>'+
									'</td>';
				}
			}
			htmlstring+='</tr>';
		}
		htmlstring+='</table>';
		avseats = (rowsnum*colnum);
		htmlstring+='<div style="width: 100%;background-color: #2e2e3b;color: #cecece;padding: 5px;"><i class="icon-Seat seat previewseat"></i> Available Seats: <div id="avseats">'+avseats+'</div>';
		htmlstring+=' <i class="icon-Seat seat cancelled previewseat"></i>'+
									'Disabled Seats</div>';
        htmlstring +='<br><label for="hallname">Name: </label><input id="hallname" class="inputs" name="hallname" type="text" placeholder="Enter Hall Name">';
		htmlstring +='<br><button onclick="saveProjectionRoom()"  class="seemore">Save Projection Room</button>';
		roomdiv.innerHTML = htmlstring;
	}
}
function changeColor(seatid)//changes seat state (available seat, cancelled seat, corridor)
{
    const currseat = document.getElementById(seatid);
    const availseats = document.getElementById('avseats');
    let x = disabledseats.indexOf(seatid);
    if(x>-1)
	{
		disabledseats.splice(x,1);
		currseat.classList.remove('corridor');
	}
	if(x<0)
	{
		x = canceledseats.indexOf(seatid);
		if(x>-1)
		{
			canceledseats.splice(x,1);
			disabledseats.push(seatid);
			currseat.classList.remove('cancelled-edit');
			currseat.classList.add('corridor');
		}
	}
	if(x<0)
	{
		currseat.classList.add('cancelled-edit');
		canceledseats.push(seatid);
	}
	availseats.innerHTML = (avseats - canceledseats.length - disabledseats.length).toString();
}
function saveProjectionRoom()// saves projection room
{
    const cinemas = document.getElementById('cinemas');
    const hallname = document.getElementById('hallname');
    if(cinemas.value && hallname.value)
        {
            const cin = cinemas.value;
            const name = hallname.value;
            const availseats = avseats - canceledseats.length - disabledseats.length;
            const data = {
                "type":"saveproj",
                "cinemas": cin,
                "hallname": name,
                "rows": rowsnum,
                "columns": colnum,
                "avseats": availseats,
                "cseats": canceledseats,
                "dseats": disabledseats
            };
            const jsondata = JSON.stringify(data);
            jQuery.ajax({
                    url:"projroom.php",
                    type:"POST",
                    data:jsondata,
                    dataType:"json",
                    success:function(result)
                    {
                        if(result['status']==='success')
                        {
                            alert("Projection Room Added Successfully");
                            CloseCurtains("here");
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
        }else{alert('Some fields are not filled.');}
    
}
function updateCinema(id)// updates cinema
{
    const cininp = document.getElementById('cinemainp'+id);
    const cinchk = document.getElementById('cinemacheck'+id);
    let cinopen= 0;
    if (cinchk.checked)
    {
        cinopen=1;
    }
    if(cininp.value)
    {
        const data = {
            "type":"updcinema",
            "cinname":cininp.value,
            "cinopen":cinopen,
            "cinid":id
        };
        const jsondata = JSON.stringify(data);
        jQuery.ajax({
            url:"projroom.php",
            type:"POST",
            data:jsondata,
            dataType:"json",
            success:function(result)
            {
                if(result['status']==='success')
                {
                    alert("Cinema Updated Successfully");
                    CloseCurtains("here");
                }
                else if(result['status']==='error')
                {
                    alert("Error! Something Went Wrong with the data.");
                }
            },
            error: function(){
                alert('Something Went Wrong!');
            }
        })
    }else{alert('Input not filled');}
}
function addCinema() //adds cinema
{
    const cininp = document.getElementById('addcinemainp');
    if(cininp.value)
    {
        const data = {
            "type":"addcinema",
            "cinname":cininp.value
        };
        const jsondata = JSON.stringify(data);
        jQuery.ajax({
            url:"projroom.php",
            type:"POST",
            data:jsondata,
            dataType:"json",
            success:function(result)
            {
                if(result['status']==='success')
                {
                    alert("Cinema Added Successfully");
                    CloseCurtains("here");
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
    }else{alert('Input not filled');}
}
function editproj(projroomid) //redirects to edit projection page
{
    CloseCurtains('./edit-proj-room?id='+projroomid);
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