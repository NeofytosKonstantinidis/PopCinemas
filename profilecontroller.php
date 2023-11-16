<?php
$rawdata = json_decode(file_get_contents('php://input'),true);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cinemadtbs";
$table = array();
$success=true;
if($rawdata['type']==='savedata' && isset($rawdata['email'],$rawdata['telephone']))
{
    session_start();
    if(isset($_SESSION['user'])){
        $conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error); }
        $sql="UPDATE user JOIN employee ON user.user_ID= employee.user_ID SET telephone='".$rawdata['telephone']."',email='".$rawdata['email']."' WHERE user.username='".$_SESSION['user']."'";
        if ($conn->query($sql) === FALSE) {
            $success=false;
        }
        $sql="UPDATE user JOIN customer ON user.user_ID= customer.user_ID SET telephone='".$rawdata['telephone']."',email='".$rawdata['email']."' WHERE user.username='".$_SESSION['user']."'";
        if ($conn->query($sql) === FALSE) {
            $success=false;
        }
        if($success)
        {
            $table['status']='success';
        }
    }else{
        $table['status']='error';
    }
    echo json_encode($table);
}
else if ($rawdata['type']==='changepassword' && isset($rawdata['oldpass'],$rawdata['newpass']))
{
    session_start();
    if(isset($_SESSION['user']))
    {
        $encoldpass = sha1($rawdata['oldpass']);
        $encnewpass = sha1($rawdata['newpass']);
        $conn = new mysqli($servername, $username, $password, $dbname);//Creates connection to database
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error); }
        $sql="UPDATE user set user.password='".$encnewpass."' WHERE user.username='".$_SESSION['user']."' AND user.password='".$encoldpass."'";
        if ($conn->query($sql)){
            if($conn->affected_rows>0){
                $table['status']='success';
            }else{
                $table['status']='error';
            }
        }
    }else{
        $table['status']='error';
    }
echo json_encode($table);
}
