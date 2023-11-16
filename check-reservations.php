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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <?php
    $id=-1;
    if(isset($_GET["id"]))//if no id given then redirect to first page
    {
        $id = $_GET["id"];
        echo "<script>const movieid='".$id."';</script>";
    }else
    {
        header("Location: ./");
    }

    $cinemas = array();
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinemadtbs";
    $conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); }
    //Selects cinemas that play this movie
    $sql= "SELECT cinemas.name, cinemas.cinema_ID FROM projection LEFT JOIN halls ON projection.hall_ID= halls.hall_ID LEFT JOIN cinemas ON halls.cinema_ID= cinemas.cinema_ID WHERE movie_ID=".$id." AND projDate>= curdate() AND cinemas.isOpen=1 GROUP BY cinemas.name";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $count=0;
        while($row = $result->fetch_assoc()) {
            $cinemas[$count]['name']=$row['name'];
            $cinemas[$count]['cinemaid']=$row['cinema_ID'];
            $count++;
        }
    }
    ?>
    <title>Check Reservations</title>
</head>

<body>
<?php
$adminpage=true;
require 'curtains.php';//Calls curtains.php

?>
<div class="sections">
    <?php
        if(count($cinemas)>0)
        {?>
            <div class="section gapless">
                <div class="bookingtickets">
                    <div class="sectitle">
                        <h2>Preview Tickets</h2>
                    </div>
                    <?php
                    echo "<label for='cinemas'>Select Cinema: </label><select class='bookinputs' onchange='loadcinemaDates()' name='cinemas' id='cinemas'  autocomplete='off'> <option disabled selected value>Select Cinema</option>";
                    for ($i=0, $iMax = count($cinemas); $i< $iMax; $i++)
                    {
                        echo "<option value='".$cinemas[$i]['cinemaid']."'>".$cinemas[$i]['name']."</option>";
                    }
                    echo "</select>";
                    ?>
                    <div id="datepickerdiv">
                    </div>

                </div>
            </div>
            <?php
        }
        ?>
    <div class="footer">
        <footer>
            <div style="text-align: center;">© 2021 Pop Cinemas. All Rights Reserved.</div>
        </footer>
    </div>
</div>
</div>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js'></script>

<script>
    let adates = {};
    let hall = [];
    let disabledseats =[];
    let cancelledseats = [];
    let addedseats = [];
    let takenseats = [];
    let rowsnum=0;
    let colnum=0;
    let roomdiv;
    let seatlimit = 1;
    let currprojectionid;

    function createDatePicker() //Creates the datepicker
    {

        function setdates(date) //Sets datepicker dates
        {
            const fulldate = adates[date];
            if (fulldate) {
                return [true];
            }
            return [false];
        }

        $('#date').datepicker({
            onClose: function(dateText, inst) {
                $(this).attr("disabled", false);
            },
            beforeShow: function(input, inst) {
                $(this).attr("disabled", true);
            },
            onSelect: function(dateText){
                setTimes(dateText);
            },
            beforeShowDay: setdates,
            dateFormat: 'dd/mm/yy',
        });
    }
    function setTimes(date) // Sends the selected date to jquery()
    {
        const cinema = document.getElementById('cinemas');
        const data = {"type":"date","date":date,"movieid":movieid,"cinemaid":cinema.value};
        const jsondata = JSON.stringify(data);
        jqry(jsondata,'date');
    }
    function jqry(jsondata,type) //Sends and recieves data from php file: bookmovie.php using JSON. then if success calls handleResponse()
    {
        jQuery.ajax({
            url:"bookmovie.php",
            type:"POST",
            data:jsondata,
            dataType:"json",
            success:function(result)
            {
                if(result['status']==='success')
                {
                    handleResponse(result,type);
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
    function loadcinemaDates() //Sends selected cinema to jquery()
    {
        const cinema = document.getElementById('cinemas');
        if(cinema.value !=null)
        {
            const data = {"type":"cinema2","movieid":movieid,"cinemaid":cinema.value};
            const jsondata = JSON.stringify(data);
            jqry(jsondata,'cinema');
        }
    }
    function loadRoom() //Sends selected time to jquery()
    {
        const projection = document.getElementById('timessel');
        if(projection.value!=null)
        {
            currprojectionid = projection.value
            const data = {"type":"room","projection_id":currprojectionid};
            const jsondata = JSON.stringify(data);
            jqry(jsondata,'room');
        }
    }
    function handleResponse(result,type)//Receives data from server and handle them to fill the fields or the projection
    {
        if(type==='cinema' && result!=null)
        {
            for(let i=0;i<result['date'].length;i++)
            {
                const newdate = new Date(result['date'][i]+' 00:00:00');
                adates[newdate] = newdate;
            }
            const datepickerdiv = document.getElementById('datepickerdiv');
            if(datepickerdiv!=null)
            {
                datepickerdiv.innerHTML='<label for="date">Select Date:</label> <input type="text" name="date" id="date" class="bookinputs" placeholder="Select Date"> <div id="dateresults"> </div>';
                createDatePicker();
            }
        }
        else if(type==='date' && result['time']!=null)
        {
            const datesdiv = document.getElementById("dateresults");
            const timearr = result['time'];
            let htmlstring ='<label for="times">Select Projection: </label><select name="times" onchange="loadRoom()" class="bookinputs" id="timessel"><option disabled selected value>Select Projection</option>'
            for(let i=0;i<timearr.length;i++)
            {
                const tmstmp = timearr[i]['timestamp'];
                if((timearr[i]['seatsLeft']/timearr[i]['avSeats'])>.5)
                {
                    htmlstring+='<option class="greend" value="'+timearr[i]['proj_ID']+'">'+tmstmp.substring(0,tmstmp.length-3)+' ('+timearr[i]['hallName']+')</option>'
                }
                else if((timearr[i]['seatsLeft']/timearr[i]['avSeats'])>.25)
                {
                    htmlstring+='<option class="yellowd" value="'+timearr[i]['proj_ID']+'">'+tmstmp.substring(0,tmstmp.length-3)+' ('+timearr[i]['hallName']+')</option>'
                }
                else if(timearr[i]['seatsLeft']===0)
                {
                    htmlstring+='<option class="redd" value="nothing" disabled>'+tmstmp.substring(0,tmstmp.length-3)+' ('+timearr[i]['hallName']+')</option>'
                }

            }
            htmlstring +='</select><div id="roomdiv"></div>'
            datesdiv.innerHTML = htmlstring;
        }
        else if(type==='room' && result['hallinfo']!=null && result['disabledseats'] != null && result['cancelledseats'] != null && result['takenseats'] != null)
        {
            hall = result['hallinfo'];
            disabledseats =result['disabledseats'];
            cancelledseats = result['cancelledseats'];
            takenseats = result['takenseats'];
            rowsnum=hall['rows'];
            colnum=hall['cols'];
            roomdiv =document.getElementById('roomdiv');
            loadTable();
        }
    }
    function loadTable()//loads projection room
    {
        let htmlstring = '<div class="roomtabl"><table class="datastable" align="center" cellpadding="0" cellspacing="10">';
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
                    htmlstring+='<div><i class="icon-Seat seat nodropcursor" id="seat'+String.fromCharCode(64+i)+j+'" onclick="changeColor(\'seat'+String.fromCharCode(64+i)+j+'\')"></i></div>'+
                        '</td>';
                }
            }
            htmlstring+='</tr>';
        }
        htmlstring+='</table><h2>Screen</h2></div>';
        htmlstring+='<div class="seatpreviews">'
            +'<div class="prevsdi"><span class="svgicon"><i class="icon-Seat seat previewseat "></i></span> <span class="svgtext">Available Seats</span></div>';
        htmlstring+='<div class="prevsdi"> <span class="svgicon"><i class="icon-Seat seat cancelled previewseat"></i></span><span class="svgtext">Disabled Seats</span></div>';
        htmlstring+='<div class="prevsdi"> <span class="svgicon"><i class="icon-Seat seat taken previewseat"></i></span>'+
            '<span class="svgtext">Taken Seats</span></div>';
        htmlstring+='<div class="prevsdi"> <span class="svgicon"><i class="icon-Seat seat sel previewseat"></i></span>'+
            '<span class="svgtext">Selected Seats</span></div><div id="ticketpreviews2"></div><div id="ProceedToCheckOut"></div></div>';
        roomdiv.innerHTML = htmlstring;
        loadSeatsType();
    }
    function loadSeatsType() //Loads seats correct color (taken, disabled and cancelled)
    {
        for(let i=0; i<cancelledseats.length; i++)
        {
            let tempseat = document.getElementById(cancelledseats[i]['seat']);
            tempseat.classList.add('cancelled');
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
            tempseat.classList.add('pointercursor');
        }
    }
    function changeSeatLimit() //changes ticket limit
    {
        seatlimit = document.getElementById('tickets').value;
        if(seatlimit<addedseats.length)
        {
            const num = addedseats.length - seatlimit;
            console.log(num );
            for(let i=0; i<num; i++)
            {
                document.getElementById(addedseats[addedseats.length-1]['seat']).classList.remove("sel");
                addedseats.splice(addedseats.length-1,1);
            }
            document.getElementById("ticketpreviews2").innerHTML="";
            for(let i=0;i<addedseats.length;i++)
            {
                addTicketPreview(addedseats[i]['seat'],i+1);
            }
            console.log(addedseats);
        }
    }
    function changeColor(s)//When seat clicked it selects or unselects it
    {
        const seat = document.getElementById(s);
        let success = true;
        if(seat!=null)
        {
            for(let i=0; i<cancelledseats.length; i++)
            {
                if(cancelledseats[i]['seat']===s)
                {
                    success=false;
                }
            }
            for(let i=0; i<disabledseats.length; i++)
            {
                if(disabledseats[i]['seat']===s)
                {
                    success=false;
                }
            }
            if (success)
            {
                let add = true;
                for(let i=0; i<addedseats.length;i++)
                {
                    if(addedseats[i]['seat']===s)
                    {
                        add = false;
                        addedseats.splice(i,1);
                        seat.classList.remove("sel");
                        removeTicketPreview(s);
                    }
                }
                if(add)
                {
                    addedseats[addedseats.length]={'seat':s};
                    seat.classList.add("sel");
                    addTicketPreview(s,addedseats.length);
                }
            }
        }
        console.log(addedseats);
    }
    function addTicketPreview(s,leng)//Adds selected seat preview
    {
        const ticketspreviewsdiv = document.getElementById("ticketpreviews2");
        const data = {"type":"selectedseat","seat":s,"projection":currprojectionid}
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
                    const str = s.replace('seat', '');
                    const row = str.substring(0,1);
                    const column = str.substring(1,str.length);
                    if(ticketspreviewsdiv!=null)
                    {
                        ticketspreviewsdiv.innerHTML += '<div class="prevticket" id="prevticket'+s+'">'
                            +'<div class="titleticket">Selected Seat #'+leng+'</div>'
                            +'<p style="font-size: 15px;">'+result["data"]["name"]+'</p>'
                            +'<label for="row'+s+'">Ticket Row: </label><input name="row'+s+'" class="smallinput" value="'+row+'" readonly><br> '
                            +'<label for="column'+s+'">Ticket Column: </label><input name="column'+s+'" class="smallinput" value="'+column+'" readonly><br>'
                            +'<label for="cost'+s+'">Cost: </label><input name="cost'+s+'" class="smallinput" value="7,50€" readonly></div>';
                    }
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
    function removeTicketPreview(s)//Removes ticket preview
    {
        document.getElementById('prevticket'+s).remove();
    }
</script>
</body>
</html>
