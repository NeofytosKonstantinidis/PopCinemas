<?php
$rawdata = json_decode(file_get_contents('php://input'),true);
if(isset($rawdata['type']))
{
    if(($rawdata['type'] === 'saveproj') && isset($rawdata['cinemas'], $rawdata['hallname'], $rawdata['rows'], $rawdata['columns'], $rawdata['avseats'], $rawdata['cseats'], $rawdata['dseats'])) {
        addProjectionRoom($rawdata);
    }
    else if($rawdata['type'] === 'updcinema' && isset($rawdata['cinname'],$rawdata['cinid'],$rawdata['cinopen']))
    {
        updateCinema($rawdata);
    }
    else if($rawdata['type'] === 'addcinema' && isset($rawdata['cinname']))
    {
        addCinema($rawdata);
    }
    else if(($rawdata['type'] === 'updproj') && isset($rawdata['hallid'], $rawdata['hallname'], $rawdata['rows'], $rawdata['columns'], $rawdata['avseats'], $rawdata['cseats'], $rawdata['dseats']))
    {
        updateProjectionRoom($rawdata);
    }
}
function addCinema($rawdata)//adds cinema
{
    $table = array();
    $success=true;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinemadtbs";

    $cinname = $rawdata["cinname"];
    $conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); }

    $sql ='INSERT INTO cinemas (cinemas.name) VALUES (\''.$cinname.'\')';//inserts new cinema to database
    if ($conn->query($sql) === FALSE) {
        $success=false;
    }
    if($success)
    {
        $table['status']='success';
    }
    else
    {
        $table['status']='fail';
    }
    $conn->close();
    echo json_encode($table);
}
function addProjectionRoom($rawdata)//checks if data are correct and adds a new projection room to database
{
    $table = array();
    $success=true;
    $last_id =-1;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinemadtbs";

    $cinema = $rawdata['cinemas'];
    $name = $rawdata['hallname'];
    $rows = $rawdata['rows'];
    $columns = $rawdata['columns'];
    $avseats = $rawdata['avseats'];
    $greyseats = $rawdata['cseats'];
    $corridor = $rawdata['dseats'];

    $conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); }
    $sql ='INSERT INTO halls (cinema_ID,hall_Name,rows,columns,availableSeats) VALUES ('.$cinema.',\''.$name.'\','.$rows.','.$columns.','.$avseats.')';
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
    } else {
        $success=false;
    }
    if($success)
    {
        for($i=0, $iMax = count($greyseats); $i< $iMax; $i++)
        {
            $sql = 'INSERT INTO disabledseats (seat,hall_ID) VALUES (\''.$greyseats[$i].'\','.$last_id.')';
            if ($conn->query($sql) === FALSE) {
                $success=false;
            }
        }
        for($i=0, $iMax = count($corridor); $i< $iMax; $i++)
        {
            $sql = 'INSERT INTO cancelledseats (seat,hall_ID) VALUES (\''.$corridor[$i].'\','.$last_id.')';
            if ($conn->query($sql) === FALSE) {
                $success=false;
            }
        }
    }
    $conn->close();
    if($success)
    {
        $table['status']='success';
    }
    else
    {
        $table['status']='fail';
    }
    echo json_encode($table);
}
function updateCinema($rawdata)////checks if data are correct and updates cinema
{
    $table = array();
    $success=true;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinemadtbs";
    $conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); }
    $sql = 'UPDATE cinemas SET cinemas.name =\''.$rawdata['cinname'].'\', cinemas.isOpen='.$rawdata['cinopen'].' WHERE cinema_ID ='.$rawdata['cinid'].';';
    if ($conn->query($sql) === FALSE) {
        $success=false;
    }
    if($success)
    {
        $table['status']='success';
    }
    else
    {
        $table['status']='fail';
    }
    $conn->close();
    echo json_encode($table);
}
function updateProjectionRoom($rawdata)//checks if data are correct and updates projection rooom
{
    $table = array();
    $success=true;
    $last_id =-1;

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinemadtbs";

    $hallid = $rawdata['hallid'];
    $name = $rawdata['hallname'];
    $rows = $rawdata['rows'];
    $columns = $rawdata['columns'];
    $avseats = $rawdata['avseats'];
    $greyseats = $rawdata['cseats'];
    $corridor = $rawdata['dseats'];

    $conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); }
    $sql ='UPDATE halls SET hall_Name=\''.$name.'\',halls.rows ='.$rows.', halls.columns = '.$columns.', availableSeats= '.$avseats.' WHERE hall_ID='.$hallid;
    if ($conn->query($sql) === FALSE) {
        $success=false;
    }
    if($success) {
        $sql = 'DELETE FROM cancelledseats WHERE hall_ID='.$hallid;
        if ($conn->query($sql) === FALSE) {
            $success = false;
        }
        $sql = 'DELETE FROM disabledseats WHERE hall_ID='.$hallid;
        if ($conn->query($sql) === FALSE) {
            $success = false;
        }
    }
    if($success)
    {
        for($i=0, $iMax = count($greyseats); $i< $iMax; $i++)
        {
            $sql = 'INSERT INTO disabledseats (seat,hall_ID) VALUES (\''.$greyseats[$i]["seat"].'\','.$hallid.')';
            if ($conn->query($sql) === FALSE) {
                $success=false;
            }
        }
        for($i=0, $iMax = count($corridor); $i< $iMax; $i++)
        {
            $sql = 'INSERT INTO cancelledseats (seat,hall_ID) VALUES (\''.$corridor[$i]["seat"].'\','.$hallid.')';
            if ($conn->query($sql) === FALSE) {
                $success=false;
            }
        }
    }
    $conn->close();
    if($success)
    {
        $table['status']='success';
    }
    else
    {
        $table['status']='error';
    }
    echo json_encode($table);
}