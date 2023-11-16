<?php
    use PHPMailer\PHPMailer\PHPMailer;
	$table=array();
	$rawdata = json_decode(file_get_contents('php://input'),true);
	if(isset($rawdata['type']))
	{
		if($rawdata['type']==='login')
		{
			login($rawdata);
		}
		else if ($rawdata['type']==='signup')
		{
            try {
                signup($rawdata);
            } catch (Exception $e) {
            }
        }
		else if ($rawdata['type']==='signout')
        {
            session_start();
            session_destroy();
            $table['status']='success';
            echo json_encode($table);
        }
		else if($rawdata['type']==='vercode' && isset($rawdata['user']))
        {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "cinemadtbs";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error); }

            $sql ='SELECT user.isActivated, customer.email as cusemail, employee.email as empemail FROM user LEFT JOIN customer ON user.user_ID=customer.user_ID LEFT JOIN employee ON user.user_ID = employee.user_ID WHERE user.username=\''.$rawdata['user'].'\'; ';
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    if($row['isActivated']==='0' || $row['isActivated']===0)
                    {
                        $mailres='';
                        if($row['empemail'] !== null)
                        {
                            try {
                                $mailres=sendVerificationCode($row['empemail'], $rawdata['user']);
                            } catch (Exception $e) {
                            }
                        }else if ($row['cusemail']!==null)
                        {
                            try {
                                $mailres=sendVerificationCode($row['cusemail'], $rawdata['user']);
                            } catch (Exception $e) {
                            }
                        }
                        $data=null;
                        if($mailres==='success')
                        {
                            $data['status']='success';
                        }
                        else
                        {
                            $data['status']='error';
                        }
                        echo json_encode($data);
                    }
                }
            }
        }
	}
/**
 * @throws Exception
 */
function sessionStart($lifetime, $path, $domain, $secure, $httpOnly)//Creates a safe session that is allowed to be manipulated only from http requests
{
    session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
    session_start();
}

function login($rawdata)//handles login
	{
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "cinemadtbs";
        $table=array();
		if(isset($rawdata['username'],$rawdata['password']) && sqlInjectionChecker($rawdata['username']) && sqlInjectionChecker($rawdata['password']) )
		{
			$conn = new mysqli($servername, $username, $password, $dbname);
			if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error); }
			$username = $rawdata['username'];
			$password = $rawdata['password'];
			$encpassword = sha1($password);
			$sql = "SELECT user.username,permissions.name, user.isActivated FROM user LEFT JOIN permissions ON user.role = permissions.permission_ID WHERE user.username='".$username."' AND user.password='".$encpassword."' GROUP BY user.user_ID ";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
				    if($row['isActivated']==='0' || $row['isActivated']===0)
                    {
                        $table['status']='error';
                        $table['fail'] ='Account is not activated. A link has been sent to your e-mail. If you havent recieved one click <span onclick="sentVerCode()" style="color:#0034ff; text-decoration: underline; cursor: pointer;">here</span> to receive a new one';
                    }
				    else {
                        $user = $row['username'];
                        $permission = $row['name'];
                        if ($permission !== null) {
                            $table['data']['permission'] = $permission;
                        } else {
                            $table['data']['permission'] = 'User';
                        }
                        $table['data']['user'] = $user;
                        $table['data']['isactive'] = $row['isActivated'];
                        $table['status'] = 'success';
                        sessionStart(0, '/', 'localhost', false, true);
                        $_SESSION['user']=$user;
                        $_SESSION['role']=$permission;
                    }
				}
			}else{
				$table['status']='error';
				$table['fail']='Wrong Username or Password';
			}
            $conn->close();
		}else{$table['result']='error';
		$table['fail'] ='Wrong Data';
		}
		echo json_encode($table);
	}

/**
 * @throws Exception
 */
function signup($rawdata)//handles signup
	{
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "cinemadtbs";
        $table=array();
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); }
        if(validateEmail($rawdata['email']) && sqlInjectionChecker($rawdata['username']) && sqlInjectionChecker($rawdata['password']) && validateInput($rawdata['fname']) && validateInput($rawdata['lname']) && validateInput($rawdata['telephone']))
        {
            $email = $rawdata['email'];
            $username = $rawdata['username'];
            $password = $rawdata['password'];
            $fname = $rawdata['fname'];
            $lname = $rawdata['lname'];
            $telephone = $rawdata['telephone'];
            $encpassword = sha1($password);
            $sql = "SELECT user.username, employee.email as eemail, customer.email as cemail FROM user LEFT JOIN customer ON user.user_ID=customer.user_ID LEFT JOIN employee ON user.user_ID=employee.user_ID WHERE user.username='".$username."' OR employee.email='".$email."' OR customer.email='".$email."' ";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    if($row['eemail']===$email || $row['cemail']===$email)
                    {
                        $table['status']='error';
                        $table['fail'][] ='Email is already in use';
                    }
                    if($row['username']===$username)
                    {
                        $table['status']='error';
                        $table['fail'][] ='Username is already in use';
                    }
                }
            }else{
                $sql ="INSERT INTO user (username, password, role, isActivated) VALUES ('".$username."','".$encpassword."',0,0)";
                if ($conn->query($sql) === TRUE) {
                    $last_id = $conn->insert_id;
                    $sql = "INSERT INTO customer (user_ID, name, surname, email, telephone) VALUES(".$last_id.",'".$fname."','".$lname."','".$email."','".$telephone."') ";
                    if ($conn->query($sql) === TRUE) {
                        if(sendVerificationCode($email,$username)==='success')
                        {
                            $table['status']='success';
                        }else{
                            $table['status']='error';
                            $table['fail'][] ='Failed to send verification Link';
                        }

                    } else {
                        $table['status']='error';
                        $table['fail'][] ='Failed to create Profile. Please contact us to fix the issue.';
                    }
                } else {
                    $table['status']='error';
                    $table['fail'][] ='Failed to create account. Please try again later.';
                }
            }
            $conn->close();
            }else{$table['status']='error';
            $table['fail'][]='Inputs contain not allowed characters';
        }
        echo json_encode($table);
	}

/**
 * @throws Exception
 */
function validateInput($name)//checks if name contains only letters
{
    if (!ctype_alnum($name)) {
        return false;
    }
    return true;
}
function validateEmail($email)//validates that email has correct format
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}
function sendVerificationCode($to,$user)//sends verification code to user email
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cinemadtbs";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); }

    $from = 'popcinemasgr@gmail.com';
    $fromName = 'Pop Cinemas';
    $subject = 'Account Verification';
    $decryptcode= keygenerator(10);
    $sql='INSERT INTO userconfirmation (username,confirmationcode) VALUES (\''.$user.'\',\''.$decryptcode.'\') ON DUPLICATE KEY UPDATE confirmationcode=\''.$decryptcode.'\';';
    if ($conn->query($sql) === FALSE) {
        $success=false;
    }
    $htmlContent='<html lang="en"> 
    <head> 
        <title>Welcome to Pop Cinemas World!</title> 
    </head> 
    <body> 
        <h1>Thanks you for joining us!</h1> 
        <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
            <tr> 
                <th>Name:</th><td>'.$user.'</td> 
            </tr> 
            <tr style="background-color: #e0e0e0;"> 
                <th>Email:</th><td>'.$to.'</td> 
            </tr> 
            <tr> 
                <th>Website:</th><td><a href="http://localhost/popcinemas/">www.localhost/popcinemas</a></td> 
            </tr>
            <tr> 
                <th>Click The verification link: </th><td><a href="http://localhost/popcinemas/verify?code='.$decryptcode.'&user='.$user.'">Verify</a></td> 
            </tr>  
        </table> 
    </body> 
    </html>';


    require_once "PHPMailer/PHPMailer.php";
    require_once "PHPMailer/SMTP.php";
    require_once "PHPMailer/Exception.php";
    $mail = new PHPMailer();
    $mail -> isSMTP();
    $mail -> Host = "smtp.gmail.com";
    $mail -> SMTPAuth = true;
    $mail -> Username = "popcinemasgr@gmail.com";
    $mail -> Password = "BkkLqL4Wq86zUmi";
    $mail -> Port = 587;
    $mail -> SMTPSecure="tls";
    $mail -> isHTML(true);
    $mail -> setFrom($from,$fromName);
    $mail -> addAddress($to);
    $mail -> Subject = $subject;
    $mail -> Body = $htmlContent;
    $conn -> close();
    if($mail -> send())
    {
        return 'success';
    }

    return 'fail';
}

/**
 * @throws Exception
 */
function keygenerator($length): string//generates a random code
{
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

function sqlInjectionChecker($mystring):bool//checks if string contains sql injection
{
    if(strpos($mystring,"'") !== false || strpos($mystring," ") !== false || strpos($mystring,'"') !== false){
        return false;
    }
    return true;
}
