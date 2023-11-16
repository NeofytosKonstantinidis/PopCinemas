<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="en">
    <head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css'>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/menustyle.css"/>
	<link rel="stylesheet" href="./css/style.css"/>
    <link rel="stylesheet" href="./css/moviestyle.css"/>
	<link rel="stylesheet" href="./css/indexstyle.css"/>
    <link rel="stylesheet" media="only screen and (max-width: 740px)" href="./css/mobilestyle.css"/>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js'></script>
		<?php
        $projarray=null;
        $projinfo=null;
        $cancelledseats=array();
        $disabledseats=array();
        $takenseats=array();
		if(isset($_POST['arrayres']))//if no seats set return to main page
		{
            $projarray = json_decode($_POST["arrayres"], true);
		}else
		{
			header("Location: ./");
		}
        $addedseats = $projarray['seats'];

		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "cinemadtbs";
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error); }
		//Selects all info for current projection
		$sql = 'SELECT projection.projDate,projtime.timestamp, movies.Title, movies.Movie_ID,halls.hall_Name,halls.hall_ID, halls.rows, halls.columns,cinemas.name FROM projection 
    LEFT JOIN movies ON projection.movie_ID=movies.Movie_ID LEFT JOIN halls ON projection.hall_ID=halls.hall_ID 
    LEFT JOIN projtime ON projection.time_ID=projtime.time_ID LEFT JOIN cinemas ON halls.hall_ID = cinemas.cinema_ID WHERE projection.projection_ID='.$projarray['projection'];
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {

			while($row = $result->fetch_assoc()) {
			    $tempdate = strtotime($row['projDate']);
                $projinfo['date']=date('d/m/Y',$tempdate);
                $projinfo['timestamp']=substr($row['timestamp'],0,-3);
                $projinfo['Title']=$row['Title'];
                $projinfo['mid']=$row['Movie_ID'];
                $projinfo['hallName']=$row['hall_Name'];
                $projinfo['hallid']=$row['hall_ID'];
                $projinfo['rows']=$row['rows'];
                $projinfo['columns']=$row['columns'];
                $projinfo['cname']=$row['name'];
                $projinfo['projid']=$projarray['projection'];
			}     } else {   header("Location: http://localhost/popcinemas"); }

		//selects cancelled seats of hall
		$sql= 'SELECT seat FROM cancelledseats WHERE hall_ID='.$projinfo['hallid'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $count=0;
            while($row = $result->fetch_assoc()) {
                $cancelledseats[$count]['seat']=$row['seat'];
                $count++;
            }
        }
        //selects disabled seats of hall
        $sql= 'SELECT seat FROM disabledseats WHERE hall_ID='.$projinfo['hallid'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $count=0;
            while($row = $result->fetch_assoc()) {
                $disabledseats[$count]['seat']=$row['seat'];
                $count++;
            }
        }
        //selects taken seats of projection
        $sql= 'SELECT seatName FROM ticket WHERE projection_ID='.$projarray['projection'];
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $count=0;
            while($row = $result->fetch_assoc()) {
                $takenseats[$count]['seat']=$row['seatName'];
                $count++;
            }
        }
        ?>
        <title>Tickets for <?php echo $projinfo['Title'] ?></title>
    </head>

    <body>
	<?php
    $checkoutpage=true;
	require 'curtains.php';//Calls curtains.php
	?>
		<div class="sections">
		<div class="section">
            <div class="bookingtickets">
                <div class="sectitle">
                <h2>Checkout</h2>
                </div>
                <div id="roomdiv">
                </div>

            </div>
		</div>
		<div class="footer">
		<footer>
		<div style="text-align: center;">© 2021 Pop Cinemas. All Rights Reserved.</div>
		</footer>
		</div>
		</div>
		</div>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js'></script>

        <script>

        const disabledseats =<?php echo json_encode($cancelledseats); ?>;
        const cancelledseats = <?php echo json_encode($disabledseats); ?>;
        const addedseats = <?php echo json_encode($addedseats); ?>;
        const takenseats = <?php echo json_encode($takenseats); ?>;
        const projinfo = <?php echo json_encode($projinfo);?>;
        const rowsnum=<?php echo $projinfo['rows']; ?>;
        const colnum=<?php echo $projinfo['columns']; ?>;
        let roomdiv=document.getElementById('roomdiv');
        loadTable();
        function loadTable()//Loads projection room data
        {
            let htmlstring='<div id="projinfo"class="seatpreviews"></div>';
            htmlstring += '<div class="roomtabl"><table class="datastable" align="center" cellpadding="0" cellspacing="10">';
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
                        htmlstring+='<div><i class="icon-Seat seat previewbigseat" id="seat'+String.fromCharCode(64+i)+j+'" ></i></div>'+
                            '</td>';
                    }
                }
                htmlstring+='</tr>';
            }
            htmlstring+='</table><h2>Screen</h2></div>';
            roomdiv.innerHTML = htmlstring;
            loadSeatsType();
        }
        function loadSeatsType()//Loads seats correct color (taken, disabled and cancelled)
        {
            for(let i=0; i<cancelledseats.length; i++)
            {
                let tempseat = document.getElementById(cancelledseats[i]['seat']);
                tempseat.classList.add('cancelled-edit');
            }
            for(let i=0; i<disabledseats.length; i++)
            {
                let tempseat = document.getElementById(disabledseats[i]['seat']);
                tempseat.classList.add('corridorrun');
            }
            for(let i=0; i<takenseats.length; i++)
            {
                let tempseat = document.getElementById(takenseats[i]['seat']);
                tempseat.classList.add('taken');
            }
            for(let i=0; i<addedseats.length; i++)
            {
                let tempseat = document.getElementById(addedseats[i]['seat']);
                tempseat.classList.add('goodseat');
            }
            loadProjInfo();
        }

        function addTicketPreview()//Adds selected seat preview
        {
            const ticketspreviewsdiv = document.getElementById("ticketpreviews");
            for(let i=0;i<addedseats.length;i++) {
                const seatname = addedseats[i]['seat'];
                const str = addedseats[i]['seat'].replace('seat', '');
                const row = str.substring(0, 1);
                const column = str.substring(1, str.length);
                if (ticketspreviewsdiv != null) {
                    ticketspreviewsdiv.innerHTML += '<div class="prevticket" id="prevticket' + seatname + '">'
                        + '<div class="titleticket">Selected Seat #' + (i+1) + '</div>'
                        + '<label for="row' + seatname + '">Ticket Row: </label><input name="row' + seatname + '" class="smallinput" value="' + row + '" readonly><br> '
                        + '<label for="column' + seatname + '">Ticket Column: </label><input name="column' + seatname + '" class="smallinput" value="' + column + '" readonly><br>'
                        + '<label for="cost' + seatname + '">Cost: </label><input name="cost' + seatname + '" class="smallinput" value="7,50€" readonly></div>';
                }
            }
            calculateCosts();
        }
        function loadProjInfo()//Loads projection movie name, hall name, projection date and time.
        {
            document.getElementById('projinfo').innerHTML='<label for="moviename">Movie:  </label><input name="moviename" class="smallinput mediuminput" value="' + projinfo['Title'] + '" readonly><br>'
            +'<label for="theatrename">Cinema:  </label><input name="theatrename" class="smallinput mediuminput" value="' + projinfo['cname'] + '" readonly><br>'
            +'<label for="projroom">Room:  </label><input name="projroom" class="smallinput mediuminput" value="' + projinfo['hallName'] + '" readonly><br>'
            +'<label for="projdate">Date:  </label><input name="projdate" class="smallinput mediuminput" value="' + projinfo['date'] + '" readonly><br>'
            +'<label for="projtime">Time:  </label><input name="projtime" class="smallinput mediuminput" value="' + projinfo['timestamp'] + '" readonly><br><div id="ticketpreviews" style="max-height: 285px;"></div>'
            +'<div id="resultcosts"></div>';
            addTicketPreview();
        }
        function calculateCosts()//Calculates total cost and prints it
        {
            const cost = addedseats.length*7.5;
            const resultdiv = document.getElementById('resultcosts').innerHTML='<label for="total">Total:  </label><input name="total" class="smallinput" style="width: 80px;" value="' + cost.toFixed(2) + '€" readonly><br>'
            +'<button onclick="submitData();" class="seemore" style="border-radius: 20px;margin-top: 15px;">Buy Tickets</button>';
        }
        function submitData()//Completes Order.
        {
            const data = {'type':'addtickets','projid':projinfo['projid'],'tickets':addedseats,'hallid':projinfo['hallid']};
            const jsondata = JSON.stringify(data);
            jQuery.ajax({
                url:"bookmovie.php",
                type:"POST",
                data:jsondata,
                dataType:"json",
                success:function(result)
                {
                    if(result['status']==='success')
                    {
                        alert('Tickets bought successfully!')
                        CloseCurtains('./movie?id='+projinfo['mid']);
                    }
                    else if(result['status']==='error')
                    {
                        alert("Error! Seats already taken.");
                        CloseCurtains('./movie?id='+projinfo['mid']);

                    }
                },
                error: function(){
                    alert('Something Went Wrong!');
                    CloseCurtains('./movie?id='+projinfo['mid']);
                }
            });
        }
        </script>
    </body>
</html>
