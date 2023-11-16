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
$id=null;
if(isset($_GET["id"]))
{
    $id = $_GET["id"];
}else
{
    header("Location: ./");
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
$conn = new mysqli($servername, $username, $password, $dbname);//Connects to database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); }
$adminpage=true;
require 'curtains.php';//Calls curtains.php
$success=true;
$hallarray = array();
$sql = "SELECT hall_Name,halls.rows,halls.columns FROM halls WHERE hall_ID=".$id;
//selects all hall details
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hallarray['id']= $id;
        $hallarray['name']= $row['hall_Name'];
        $hallarray['rows']= $row['rows'];
        $hallarray['cols']= $row['columns'];
    }
}else{$success=false;}
$disabledseats = array();
$cancelledseats = array();
if ($success)
{//Selects all cancelled seats in this projection room
    $sql = 'SELECT cs_ID,seat FROM cancelledseats WHERE hall_ID='.$id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $count=0;
        while($row = $result->fetch_assoc()) {
            $disabledseats[$count]['id']= $row['cs_ID'];
            $disabledseats[$count]['seat']= $row['seat'];
            $count++;
        }
    }
    $sql = 'SELECT ds_ID,seat FROM disabledseats WHERE hall_ID='.$id;
    //selects all disabled seats
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $count=0;
        while($row = $result->fetch_assoc()) {
            $cancelledseats[$count]['id']= $row['ds_ID'];
            $cancelledseats[$count]['seat']= $row['seat'];
            $count++;
        }
    }
}
?>
<div class="sections">
    <div class="section gapless">
        <div class="sectitle">
            <h2>Edit Projection Room</h2>
        </div>
        <div id="roomdiv2" class="roomtableedit">
        </div>
    </div>
    <script>
        let hall = <?php echo json_encode($hallarray)?>;
        let disabledseats = <?php echo json_encode($disabledseats)?>;
        let cancelledseats = <?php echo json_encode($cancelledseats)?>;
        const olddseats = <?php echo json_encode($disabledseats)?>;
        const oldcseats = <?php echo json_encode($disabledseats)?>;

        const roomdiv = document.getElementById('roomdiv2');
        let avseats = 0;
        let rowsnum=hall['rows'];
        let colnum=hall['cols'];
        $(function(){
            loadRoom();
            loadSeatsType();
        });
        function loadRoom()//Loads projection room details
        {
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
            htmlstring+='<div style="width: 100%;background-color: #2e2e3b;color: #cecece;padding: 5px;"><i class="icon-Seat seat previewseat"></i>'+
                '</g>'+
                '</svg> Available Seats: <div id="avseats">'+avseats+'</div>';
            htmlstring+=' <i class="icon-Seat seat cancelled previewseat"></i>'+
                'Disabled Seats</div>';
            htmlstring +='<br><label for="hallname">Name: </label><input id="hallname" class="inputs" name="hallname" value="'+hall['name']+'" type="text" placeholder="Enter Hall Name">';
            htmlstring +='<br><button onclick="saveProjectionRoom()"  class="seemore">Save Projection Room</button>';
            roomdiv.innerHTML = htmlstring;
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
                tempseat.classList.add('corridor');
            }
            const availseats = document.getElementById('avseats');
            availseats.innerHTML = (avseats - cancelledseats.length - disabledseats.length).toString();
        }
        function searchIndex(arr, obj)//Search if specific seat equals the string given
        {
            for(let i=0; i<arr.length;i++)
            {
                if (arr[i]['seat']===obj)
                {
                    return i;
                }
            }
            return -1;
        }
        function changeColor(seatid)//When seat clicked it selects or unselects it
        {
            const currseat = document.getElementById(seatid);
            const availseats = document.getElementById('avseats');
            let x = searchIndex(disabledseats,seatid);
            if(x>-1)
            {
                disabledseats.splice(x,1);
                currseat.classList.remove('corridor');
            }
            if(x<0)
            {
                x = searchIndex(cancelledseats,seatid);
                if(x>-1)
                {
                    cancelledseats.splice(x,1);
                    disabledseats.push({"seat":seatid});
                    currseat.classList.remove('cancelled-edit');
                    currseat.classList.add('corridor');
                }
            }
            if(x<0)
            {
                currseat.classList.add('cancelled-edit');
                cancelledseats.push({"seat":seatid});
            }
            availseats.innerHTML = (avseats - cancelledseats.length - disabledseats.length).toString();
        }
        function saveProjectionRoom()//Saves projection room
        {
            const hallname = document.getElementById('hallname');
            if(hallname.value)
            {
                const name = hallname.value;
                const availseats = avseats - cancelledseats.length - disabledseats.length;
                const data = {
                    "type":"updproj",
                    "hallid": hall['id'],
                    "hallname": name,
                    "rows": rowsnum,
                    "columns": colnum,
                    "avseats": availseats,
                    "cseats": cancelledseats,
                    "dseats": disabledseats
                };
                const jsondata = JSON.stringify(data);
                console.log(data);
                jQuery.ajax({
                    url:"projroom.php",
                    type:"POST",
                    data:jsondata,
                    dataType:"json",
                    success:function(result)
                    {
                        if(result['status']==='success')
                        {
                            alert("Projection Room Updated Successfully");
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