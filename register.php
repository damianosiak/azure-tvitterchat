<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
//=====DB parameters=====
$dbServer = "tvitterchatmssqlsrv.database.windows.net";
$dbUser="tvitterchatmssqlsrv";
$dbPassword="Administrator2022!";
$connectionInfo = array("Database"=>"TvitterChatMSSQL", "UID"=>$dbUser, "PWD"=>$dbPassword);
//=======================

$conn=sqlsrv_connect($dbServer, $connectionInfo);
if(!$conn){
	die("DB connection failed: ");
	echo "<script>window.location.replace('error.php');</script>";
}


if(array_key_exists("registerButton",$_POST)){
	$iLogin=$_POST["iLogin"];
	$iPassword=$_POST["iPassword"];
	$iEmail=$_POST["iEmail"];
	$password=password_hash($iPassword, PASSWORD_ARGON2I);
	
	$sql="select login from users where login='".$iLogin."' or email='".$iEmail."'";
	$params = array();
	$options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET );
	$result=sqlsrv_query($conn, $sql, $params, $options);
	if(sqlsrv_num_rows($result)>0){
		echo "<script>alert('User with this name or email exists!');</script>";
	}else{
		$sql="insert into users (login, password, email) values (?,?,?)";
		$params = array($iLogin, $password, $iEmail);
		if(!sqlsrv_query($conn, $sql, $params)){
			echo "ErrorR1";
		}else{
			$uid=null;
			
			$sql="select uid from users where login='".$iLogin."'";
			$params = array();
			$options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET );
			$result=sqlsrv_query($conn, $sql, $params, $options);
			while($row=sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
				$uid=$row["uid"];
				break;
			}
			
			$sid=substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPRSTUWXYZ"), 1, 16);
			$date=date("Y-m-d H:i:s");
			
			$sql="insert into sessions (sid, userid, date) values (?,?,?)";
			$params = array($sid, $uid, $date);
			if(!sqlsrv_query($conn, $sql, $params)){
				echo "<script>alert('Login failedR2');</script>";
			}else{
				setcookie("cSID",$sid);
				echo "<script>alert('Register & Login complete');</script>";
				echo "<script>window.location.replace('index.php');</script>";
			}
		}
	}
}
	
	
if(isset($_COOKIE["cSID"])){
	$cSID=$_COOKIE["cSID"];
	
	$sql="select userid from sessions where sid='".$cSID."'";
	$params = array();
	$options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET );
	$result=sqlsrv_query($conn, $sql, $params, $options);
	if(sqlsrv_num_rows($result)>0){
		echo "<script>window.location.replace('index.php');</script>";
	}
}
?>


<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Register | Tvitter-Chat</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<div id="root">
		<div id="content">
			<div class="card nav-login-register-card">
				<div class="card-body">
					<h1 class="card-title" style="color:white">Tvitter-Chat</h1>
					<h3 class="card-title" style="color:white">- Register -</h3>
				</div>
			</div>
			
			<form action="" method="POST">
				<div class="card login-register-card">
					<div class="card-body">
						<label for="iLogin" class="form-label login-register-labels">Login</label>
						<input type="text" class="form-control login-register-inputs" id="iLogin" name="iLogin" placeholder="login" required />
						<label for="iPassword" class="form-label login-register-labels">Password</label>
						<input type="password" class="form-control login-register-inputs" id="iPassword" name="iPassword" placeholder="password" required />
						<label for="iEmail" class="form-label login-register-labels">Email</label>
						<input type="email" class="form-control login-register-inputs" id="iEmail" name="iEmail" placeholder="email" required />
						<a href="login.php"><button type="button" class="btn btn-warning btn-lg login-register-buttons">Login</button></a>
						<input type="submit" value="Register" id="registerButton" name="registerButton" class="btn btn-success btn-lg login-register-buttons"/>
					</div>
				</div>
			</form>
		</div>
		<div class="author">40843 - Damian Osiak</div>
	</div>
</body>
</html>