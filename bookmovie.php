<?php
use PHPMailer\PHPMailer\PHPMailer;
$rawdata = json_decode(file_get_contents('php://input'),true);
$table = array();
$success=true;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
//This file manages responses from pages: movie.php, checkout.php, check-reservations
if(isset($rawdata['type']))
{
    if($rawdata['type']==='date' && isset($rawdata['date'],$rawdata['movieid'],$rawdata['cinemaid']))
    {
        $var=$rawdata['date'];
        $date = str_replace('/', '-', $var);
        $date = date("Y-m-d", strtotime($date));
        $movieid=$rawdata['movieid'];
        $sql="SELECT projection.projection_ID, projtime.timestamp, halls.hall_Name, halls.availableSeats,
            (halls.availableSeats- COUNT(ticket.ticket_ID)) AS seatsleft FROM projection LEFT JOIN projtime ON projection.time_ID=projtime.time_ID 
                LEFT JOIN halls ON projection.hall_ID=halls.hall_ID LEFT JOIN cinemas ON halls.cinema_ID=cinemas.cinema_ID LEFT JOIN ticket ON projection.projection_ID= ticket.projection_ID 
                    WHERE movie_ID=".$movieid." AND projDate='".$date."' AND cinemas.cinema_ID=".$rawdata['cinemaid']." GROUP BY projection.projection_ID ORDER BY projtime.timestamp";

        $conn = new mysqli($servername, $username, $password, $dbname);
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $count=0;
            while($row = $result->fetch_assoc()) {
                $table['time'][$count]['proj_ID']=$row['projection_ID'];
                $table['time'][$count]['hallName']=$row['hall_Name'];
                $table['time'][$count]['timestamp']=$row['timestamp'];
                $table['time'][$count]['avSeats']=$row['availableSeats'];
                $table['time'][$count]['seatsLeft']=$row['seatsleft'];
                $count++;
            }
        }
        $conn->close();
        $table['status']='success';
        echo json_encode($table);
    }
    else if($rawdata['type']==='cinema' && isset($rawdata['movieid'],$rawdata['cinemaid']))
    {
        $sql = 'SELECT projDate FROM projection LEFT JOIN halls ON projection.hall_ID= halls.hall_ID WHERE movie_ID='.$rawdata['movieid'].' AND projDate>= curdate() AND halls.cinema_ID='.$rawdata['cinemaid'].' GROUP BY projDate ORDER BY projDate';
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error); }
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $count=0;
            while($row = $result->fetch_assoc()) {
                $table['date'][$count]=$row['projDate'];
                $count++;
            }
        }
        $conn->close();
        $table['status']='success';
        echo json_encode($table);
    }
    else if($rawdata['type']==='cinema2' && isset($rawdata['movieid'],$rawdata['cinemaid']))
    {
        $sql = 'SELECT projDate FROM projection LEFT JOIN halls ON projection.hall_ID= halls.hall_ID WHERE movie_ID='.$rawdata['movieid'].' AND  halls.cinema_ID='.$rawdata['cinemaid'].' GROUP BY projDate ORDER BY projDate';
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error); }
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $count=0;
            while($row = $result->fetch_assoc()) {
                $table['date'][$count]=$row['projDate'];
                $count++;
            }
        }
        $conn->close();
        $table['status']='success';
        echo json_encode($table);
    }
    else if($rawdata['type']==='room' && isset($rawdata['projection_id']))
    {
        $id = $rawdata['projection_id'];
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error); }
        $success=true;
        $hallarray = array();
        $sql = "SELECT halls.hall_ID,hall_Name,halls.rows,halls.columns,halls.availableSeats FROM halls LEFT JOIN projection ON halls.hall_ID=projection.hall_ID WHERE projection.projection_ID=".$id;
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $hallarray['id']= $row['hall_ID'];
                $hallarray['name']= $row['hall_Name'];
                $hallarray['rows']= $row['rows'];
                $hallarray['cols']= $row['columns'];
                $hallarray['avseats']= $row['availableSeats'];
            }
        }else{$success=false;}
        if($success) {
            $disabledseats = array();
            $cancelledseats = array();
            $takenseats = array();
            $sql = 'SELECT cs_ID,seat FROM cancelledseats WHERE hall_ID=' . $hallarray['id'];
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $count = 0;
                while ($row = $result->fetch_assoc()) {
                    $disabledseats[$count]['id'] = $row['cs_ID'];
                    $disabledseats[$count]['seat'] = $row['seat'];
                    $count++;
                }
            }
            $sql = 'SELECT ds_ID,seat FROM disabledseats WHERE hall_ID=' . $hallarray['id'];
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $count = 0;
                while ($row = $result->fetch_assoc()) {
                    $cancelledseats[$count]['id'] = $row['ds_ID'];
                    $cancelledseats[$count]['seat'] = $row['seat'];
                    $count++;
                }
            }
            $sql = 'SELECT seatName FROM ticket WHERE projection_ID=' . $id;
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $count = 0;
                while ($row = $result->fetch_assoc()) {
                    $takenseats[$count]['seat'] = $row['seatName'];
                    $count++;
                }
            }
            $table['status']='success';
            $table['hallinfo']=$hallarray;
            $table['disabledseats'] = $disabledseats;
            $table['cancelledseats'] = $cancelledseats;
            $table['takenseats'] = $takenseats;
            echo json_encode($table);
        }else{
            $table['status']='error';
            echo json_encode($table);
        }
        $conn->close();
    }
    else if($rawdata['type']==='addtickets' && isset($rawdata['projid'],$rawdata['tickets'],$rawdata['hallid']))
    {
        $tickets = $rawdata['tickets'];
        $projid = $rawdata['projid'];
        $hallid = $rawdata['hallid'];
        $success=true;
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error); }
        for ($i=0, $iMax = count($tickets); $i< $iMax; $i++)
        {
            $sql="SELECT COUNT(*) as res FROM ticket,cancelledseats,disabledseats 
            WHERE (ticket.seatName='".$tickets[$i]['seat']."' AND ticket.projection_ID=".$projid.") 
            OR (cancelledseats.seat='".$tickets[$i]['seat']."' AND cancelledseats.hall_ID=".$hallid.") 
            OR (disabledseats.seat='".$tickets[$i]['seat']."' AND disabledseats.hall_ID=".$hallid.") ";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if($row['res']>0)
                    {
                        $success=false;
                    }
                }
            }
        }
        session_start();
        $userid=-1;
        if(isset($_SESSION['user']))
        {
            $sql="SELECT user_ID FROM user WHERE user.username='".$_SESSION['user']."'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $userid=$row['user_ID'];
                }
            }else{
                $success=false;
            }
        }else{
            $success=false;
        }

        if($success)
        {
            for($i=0, $iMax = count($tickets); $i< $iMax; $i++)
            {
                $sql="INSERT INTO  ticket (seatName, projection_ID,user_ID) VALUES ('".$tickets[$i]['seat']."',".$projid.",".$userid.")";
                if ($conn->query($sql) === FALSE) {
                    $success=false;
                }
            }
            if($success)
            {
                emailTickets($tickets,$userid,$projid,$conn);
                $table['status']='success';
                echo json_encode($table);
            }
        }else{
            $table['status']='error';
            echo json_encode($table);
        }
        $conn->close();
    }else if($rawdata['type']==='submitrate' && isset($rawdata['rate'],$rawdata['movieid']))
    {
        session_start();
        if(is_numeric($rawdata['rate']) && isset($_SESSION['user']))
        {
            $exist=false;
            $success=true;
            $userid=-1;
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error); }
            $sql="SELECT user.user_ID,(SELECT moviesrating.mr_ID FROM user LEFT JOIN moviesrating ON user.user_ID=moviesrating.user_ID WHERE moviesrating.movie_ID=".$rawdata['movieid']." AND user.username='".$_SESSION['user']."') as rating FROM user WHERE user.username='".$_SESSION['user']."' ";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $userid=$row['user_ID'];
                    if($row['rating']!==null)
                    {
                        $exist=true;

                    }
                }
            }
            if($userid>0)
            {
                if($exist)
                {
                    $sql='UPDATE moviesrating SET moviesrating.rating='.$rawdata['rate'].' WHERE moviesrating.user_ID='.$userid.' AND moviesrating.movie_ID='.$rawdata['movieid'].';';
                }
                else{
                    $sql='INSERT INTO moviesrating (movie_ID, user_ID, rating) VALUES ('.$rawdata['movieid'].','.$userid.','.$rawdata['rate'].') ';
                }
                if ($conn->query($sql) === FALSE) {
                    $success=false;
                }
            }
            $table=array();
            if($success)
            {
                $table['status']='success';
            }else{
                $table['status']='error';
            }
            echo json_encode($table);


        }
    }else if ($rawdata['type']==='selectedseat' && isset($rawdata['seat'],$rawdata['projection'])){
        $success=false;
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error); }
        $sql = "SELECT CONCAT(customer.surname, ' ', customer.name) as customername,CONCAT(employee.surname , ' ', employee.name) as employeename FROM ticket JOIN user ON ticket.user_ID=user.user_ID LEFT JOIN customer ON user.user_ID = customer.user_ID LEFT JOIN employee ON user.user_ID = employee.user_ID WHERE ticket.projection_ID=".$rawdata['projection']." and ticket.seatName='".$rawdata['seat']."'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $success=true;
            while ($row = $result->fetch_assoc()) {
                $custname = $row['customername'];
                $employeename = $row['employeename'];

                if($custname!==null){
                    $table['data']['name']=$custname;
                }else{
                    $table['data']['name']=$employeename;
                }
            }
        }
        $conn->close();
        if($success){
            $table['status']='success';
        }else{
            $table['status']='error';
        }
        echo json_encode($table);
    }
}else
{
    $table['status']='error';
    echo json_encode($table);
}
function emailTickets($tickets,$userid,$projid,$conn): string//Sends e-mail to user using PHPMailer to notify him for tickets bought and give him receipt.
{
    $sql="SELECT user.username,IFNULL(customer.email,employee.email) as email,movies.Title , projection.projDate,projtime.timestamp,halls.hall_Name,cinemas.name FROM user LEFT JOIN ticket ON user.user_ID=ticket.user_ID LEFT JOIN projection ON ticket.projection_ID=projection.projection_ID LEFT JOIN projtime ON projection.time_ID=projtime.time_ID LEFT JOIN halls ON projection.hall_ID=halls.hall_ID LEFT JOIN cinemas ON halls.cinema_ID=cinemas.cinema_ID LEFT JOIN movies ON projection.movie_ID=movies.Movie_ID LEFT JOIN customer ON customer.user_ID=user.user_ID LEFT JOIN employee ON employee.user_ID=user.user_ID WHERE user.user_ID=".$userid." AND projection.projection_ID=".$projid." GROUP BY user.username";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $username=$row['username'];
            $to = $row['email'];
            $projdate = $row['projDate'];
            $projTime = $row['timestamp'];
            $datetime = new DateTime($projdate.' '.$projTime);
            $hall = $row['hall_Name'];
            $cinema = $row['name'];
            $movie = $row['Title'];
            $from = 'popcinemasgr@gmail.com';
            $fromName = 'Pop Cinemas';
            $subject = 'Tickets Receipt';
            $htmlContent='<html lang="en"> 
            <head> 
                <title>Tickets Receipt</title> 
            </head> 
            <body> 
                <center><h1>You just bought '.count($tickets).' tickets from us!</h1> </center>
                <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;text-align: center;"> 
                    <tr><th>Movie Title: </th><td>'.$movie.'</td></tr>
                    <tr><th>Cinema: </th><td>'.$cinema.'</td></tr>
                    <tr><th>Projection Room: </th><td>'.$hall.'</td></tr>
                    <tr><th>Projection Datetime: </th><td>'.$datetime->format('d/m/Y H:i').'</td></tr>
                    <tr>';
                for($i=0, $iMax = count($tickets); $i< $iMax; $i++)
                {
                    $tempticket = $tickets[$i]['seat'];
                    $htmlContent.='<tr style="background-color: #e0e0e0;"><th colspan="2">Ticket #'.($i+1).'</th></tr>';
                    $htmlContent.='<tr><th>Seat: </th><td>'.str_replace('seat','',$tempticket).'</td></tr>';
                    $htmlContent.='<tr><th>Cost: </th><td>7,50€</td></tr>';
                }
                $htmlContent.='<tr style="background-color: #e0e0e0;"><th>Total Cost: </th><td>'.number_format((float)(3*7.5),2,',','.').'€</td></tr>';
                $htmlContent.='</tr>  
                <tr> 
                        <th>Website:</th><td><a href="http://localhost/popcinemas/">www.localhost/popcinemas</a></td> 
                </tr>
                </table>
                <center><h2>Enjoy the movie!</h2> </center>
            </body> 
            </html>';
            require_once "PHPMailer/PHPMailer.php";
            require_once "PHPMailer/SMTP.php";
            require_once "PHPMailer/Exception.php";
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail -> SMTPAuth = true;
            $mail -> Username = "popcinemasgr@gmail.com";
            $mail -> Password = "BkkLqL4Wq86zUmi";
            $mail -> Port = 587;
            $mail -> SMTPSecure="tls";
            $mail->CharSet = 'UTF-8';
            $mail -> isHTML(true);
            $mail -> setFrom($from,$fromName);
            $mail -> addAddress($to);
            $mail -> Subject = $subject;
            $mail -> Body = $htmlContent;
            if($mail -> send())
            {
                return 'success';
            }

            return 'fail';
        }
    }
}
