<?php
$success = true;
if(isset($_GET['code'],$_GET['user']))
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "cinemadtbs";
	$conn = new mysqli($servername, $username, $password, $dbname);
    $code = $_GET['code'];
    $user = $_GET['user'];
    $permission='';
    $sql = 'SELECT permissions.name, user.isActivated FROM `userconfirmation` LEFT JOIN user ON user.username=userconfirmation.username LEFT JOIN permissions ON user.role=permissions.permission_ID WHERE userconfirmation.username=\''.$user.'\' && confirmationcode=\''.$code.'\';';
	$result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $permission=$row['name'];
                    if($row['isActivated']==='0')
                    {
						$sql='UPDATE user SET isActivated=\'1\' WHERE user.username=\''.$user.'\'';
						if ($conn->query($sql) === FALSE) {
							$success=false;
						}
						$sql='DELETE  FROM userconfirmation WHERE userconfirmation.username=\''.$user.'\' && confirmationcode=\''.$code.'\';';
						if ($conn->query($sql) === FALSE) {
							$success=false;
						}
					}
				}
			}else{$success=false;}
			if($success){
                sessionStart(0, '/', 'localhost', false, true);
                $_SESSION['user']=$user;
                $_SESSION['role']=$permission;
			}
    header("Location: ./");
}
function sessionStart($lifetime, $path, $domain, $secure, $httpOnly)//Creates a safe session that is allowed to be manipulated only from http requests
{
    session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
    session_start();
}