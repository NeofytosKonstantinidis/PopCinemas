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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <title>Profile</title>


</head>
<body>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); }
$checkoutpage=true;
require 'curtains.php';
?>

<div class="sections">
    <?php
    if($loggedin && isset($_SESSION['user']))
    {
        $sql= "SELECT ticket.seatName,ticket.timeclosed,projtime.dayP,projtime.timestamp,projection.projDate,movies.Title,movies.preview,halls.hall_Name FROM user JOIN ticket ON user.user_ID=ticket.user_ID LEFT JOIN projection ON ticket.projection_ID= projection.projection_ID LEFT JOIN projtime ON projection.time_ID=projtime.time_ID LEFT JOIN movies ON projection.movie_ID=movies.Movie_ID LEFT JOIN halls ON projection.hall_ID=halls.hall_ID WHERE user.username='".$_SESSION['user']."' ORDER BY ticket.timeclosed,ticket.ticket_ID";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
    ?>
<div class="section">
    <div class="sectitle">
        <h2>Tickets History</h2>
    </div>
    <div class="ticketsresults">
        <table style="width:100%;">
            <tr>
                <th>Date</th>
                <th>Seat</th>
                <th>Hall Name</th>
                <th>Projection Date</th>
                <th colspan="2">Movie</th>
            </tr>
    <?php
    while($row = $result->fetch_assoc()) {
        $date = new DateTime($row['timeclosed']);
        $date2 = new DateTime($row['projDate'].' '.$row['timestamp']);
        echo '<tr>';
        echo '<td>'.$date->format('d/m/Y H:i').'</td>';
        echo '<td>'.str_replace('seat','',$row['seatName']).'</td>';
        echo '<td>'.$row['hall_Name'].'</td>';
        echo '<td>'.$date2->format('d/m/Y H:i').'</td>';
        echo '<td>'.$row['Title'].'</td>';
        echo '<td style="text-align: right;"><img src="./Images/'.$row['preview'].'" alt="'.$row['Title'].' image" class="prevticketsimg"></td>';
        echo '</tr>';
    }
    ?>
        </table>
    </div>
</div>
    <?php
        }
    }
    if($loggedin && isset($_SESSION['user']))
    {
    ?>
    <div class="section">
        <div class="sectitle">
            <h2><?php echo $_SESSION['user']; ?></h2>
        </div>
        <?php
        $sql="SELECT IFNULL(customer.name,employee.name) as name,IFNULL(customer.surname,employee.surname) as surname,IFNULL(customer.email,employee.email) as email,IFNULL(customer.telephone,employee.telephone) as telephone FROM user LEFT JOIN employee ON user.user_ID=employee.user_ID LEFT JOIN customer ON user.user_ID=customer.user_ID WHERE username='".$_SESSION['user']."' GROUP BY user.user_ID";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<table id="userdatatable" style="width:100%;">';
                echo '<tr><th>Firstname: </th><td>'.$row['name'].'</td></tr>';
                echo '<tr><th>Lastname: </th><td>'.$row['surname'].'</td></tr>';
                echo '<tr><th>E-mail: </th><td><input id="useremail" type="email" class="inputs" value="'.$row['email'].'"></td></tr>';
                echo '<tr><th>Telephone: </th><td><input id="usertelephone" type="text" class="inputs" value="'.$row['telephone'].'"></td></tr>';
                echo '<tr><td colspan="2"><button onclick="saveUserData();" class="seemore" >Save Data</button></td></tr>';
                echo '<tr><th>Old Password: </th><td><input id="useroldpass" type="password" class="inputs" value=""></td></tr>';
                echo '<tr><th>New Password: </th><td><input id="usernewpass" type="password" class="inputs" value=""></td></tr>';
                echo '<tr><td colspan="2"><button onclick="changePassword();" class="seemore">Change Password</button></td></tr>';
                echo '</table>';
            }
        }
        ?>
    </div>
    <?php
    }
    ?>

    <script>
        function saveUserData(){
            const emailinput = document.getElementById('useremail');
            const telephoneinput = document.getElementById('usertelephone');
            if(emailinput.value && telephoneinput.value){
                const data = {'type':'savedata','email':emailinput.value,'telephone':telephoneinput.value};
                const jsondata = JSON.stringify(data);
                jQuery.ajax({
                    method: "POST",
                    url: "profilecontroller.php",
                    data:jsondata,
                    dataType:"json",
                    success:function(result)
                    {
                        if(result['status']==='success')
                        {
                            alert('Data changed successfully!');
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
            }else{
                alert('Inputs are empty.');
            }
        }
        function changePassword(){
            const oldpassinput = document.getElementById('useroldpass');
            const newpassinput = document.getElementById('usernewpass');
            if(oldpassinput.value && newpassinput.value){
                const data = {'type':'changepassword','oldpass':oldpassinput.value,'newpass':newpassinput.value};
                const jsondata = JSON.stringify(data);
                jQuery.ajax({
                    method: "POST",
                    url: "profilecontroller.php",
                    data:jsondata,
                    dataType:"json",
                    success:function(result)
                    {
                        if(result['status']==='success')
                        {
                            alert('Password changed successfully!');
                        }
                        else if(result['status']==='error')
                        {
                            alert('Wrong old password!');
                        }
                    },
                    error: function(){
                        alert("Error! Something Went Wrong with the data.");
                    }
                });
            }else{
                alert('Inputs are empty.');
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