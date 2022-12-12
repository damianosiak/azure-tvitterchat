<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
//=====DB parameters=====
$dbServer = "tvitterchatmssqlsrv.database.windows.net";
$dbUser="tvitterchatmssqlsrv";
$dbPassword="Administrator2022!";
$connectionInfo = array("Database"=>"TvitterChatMSSQL", "UID"=>$dbUser, "PWD"=>$dbPassword);
//=======================
$cSID=null;
$uid=null;

$conn=sqlsrv_connect($dbServer, $connectionInfo);
if(!$conn){
	die("DB connection failed: ");
	echo "<script>window.location.replace('error.php');</script>";
}


if(isset($_COOKIE["cSID"])){
	$cSID=$_COOKIE["cSID"];
	$sql="select userid from sessions where sid='".$cSID."'";
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET );
	$result=sqlsrv_query($conn, $sql, $params, $options);
	if(sqlsrv_num_rows($result)>0){
		while($row=sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
			$uid=$row["userid"];
			break;
		}
	}else{
		setcookie('cSID', '', time()-3600);
		echo "<script>window.location.replace('login.php');</script>";
	}
}else{
	echo "<script>window.location.replace('login.php');</script>";
}


if(array_key_exists("addPostButton",$_POST)){
	$sql="select userid from sessions where sid='".$cSID."'";
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET );
	$result=sqlsrv_query($conn, $sql, $params, $options);
	if(sqlsrv_num_rows($result)>0){
		$iPostContent=$_POST["iPostContent"];
		$date=date("Y-m-d H:i:s");
		date_default_timezone_set('Europe/Warsaw');
		$datetime_variable = new DateTime();
		$datetime_formatted = date_format($datetime_variable, 'Y-m-d H:i:s');
		$sql="insert into posts (userid, date, content) values(?,?,?)";
		$params = array($uid, $datetime_formatted, $iPostContent);
		if(!sqlsrv_query($conn, $sql, $params)){
			echo "<script>alert('Error. Post has not been added!');</script>";
		}
	}
	
}


if(array_key_exists("deletePostButton",$_POST)){
	$sql="delete from posts where pid='".$_POST["iPID"]."'";
	if(sqlsrv_query($conn, $sql)){
		echo "<script>alert('Deleted');</script>";
	}else{
		echo "<script>alert('Error. Post has not been deleted!');</script>";
	}
}


if(array_key_exists("logoutButton",$_POST)){
	unset($_COOKIE['cSID']);
	setcookie('cSID', '', time()-3600);
	$sql="delete from sessions where sid='".$cSID."'";
	if(!sqlsrv_query($conn, $sql)){
		echo "<script>alert('ErrorI1');</script>";
	}
	echo "<script>window.location.replace('login.php');</script>";
}


if(array_key_exists("buttonAddLike",$_POST)){
	echo "<script>alert('NIE ZAIMPLEMENTOWANO W TEJ WERSJI APLIKACJI - problematyczna migracja do MSSQL');</script>";
}


if(array_key_exists("buttonAddDislike",$_POST)){
	echo "<script>alert('NIE ZAIMPLEMENTOWANO W TEJ WERSJI APLIKACJI - problematyczna migracja do MSSQL');</script>";
}


if(array_key_exists("buttonRemoveLike",$_POST)){
	echo "<script>alert('NIE ZAIMPLEMENTOWANO W TEJ WERSJI APLIKACJI - problematyczna migracja do MSSQL');</script>";
}


if(array_key_exists("buttonRemoveDislike",$_POST)){
	echo "<script>alert('NIE ZAIMPLEMENTOWANO W TEJ WERSJI APLIKACJI - problematyczna migracja do MSSQL');</script>";
}

if(array_key_exists("transferButton",$_POST)){
	date_default_timezone_set('Europe/Warsaw');
	$datetime_variable = new DateTime();
	$datetime_formatted = date_format($datetime_variable, 'Y-m-d H:i:s');
	$sql="insert into transfers (date, userid) values(?,?)";
	$params = array($datetime_formatted, $uid);
	if(!sqlsrv_query($conn, $sql, $params)){
		echo "<script>alert('Error. Transfer has not been submited!');</script>";
	}else{
		echo "<script>alert('Transfer submited');</script>";
	}
}
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Tvitter-Chat</title>
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.1/font/bootstrap-icons.css">
	<link rel="stylesheet" href="styles.css">
	
<script>
	document.addEventListener("DOMContentLoaded", function(event) { 
		var scrollpos = localStorage.getItem('scrollpos');
		if (scrollpos) window.scrollTo(0, scrollpos);
	});

	window.onbeforeunload = function(e) {
		localStorage.setItem('scrollpos', window.scrollY);
	};
</script>

</head>
<body>
	<div id="root">
		<div id="mssqlToCosmosDB">
			<form action="" method="POST" class="inline-form">
				<input type="submit" id="transferButton" name="transferButton" value="Transfer" class="btn btn-info" />
			</form>
		</div>
	
		<div id="content">
			<div class="card nav-card">
				<div class="card-body">
					<div class="nav-buttons">
						<a href="user.php"><button type="button" class="btn btn-warning">User</button></a>
						<form action="" method="POST" class="inline-form"><input type="submit" id="logoutButton" name="logoutButton" value="Logout" class="btn btn-danger" /></form>
					</div>
					<h1 class="card-title" style="color:white">Tvitter-Chat</h1>
				</div>
			</div>
		
			<form action="" method="POST">
				<div class="card addpost-card">
					<div class="card-body">
						<label for="iPostContent" class="form-label" style="color:white;"><h5>Add new post</h5></label>
						<textarea class="form-control textarea-newpost" id="iPostContent" name="iPostContent" minlength="1" maxlength="512" required></textarea>
						<button type="reset" class="btn btn-danger addpost-buttons">Clear</button>
						<input type="submit" id="addPostButton" name="addPostButton" value="Add post" class="btn btn-success addpost-buttons"/>
					</div>
				</div>
			</form>
			<div class="card maintvitter-card">
				<div class="card-body">
					<?php
						$conn=sqlsrv_connect($dbServer, $connectionInfo);
						if(!$conn){
							die("DB connection failed: ");
						}else{
							$sql="select p.userid, p.pid, p.date, p.content, p.likes, p.dislikes, u.login from posts p inner join users u on p.userid=u.uid order by p.pid desc";
							$params = array();
							$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET );
							$result=sqlsrv_query($conn, $sql, $params, $options);
							if(sqlsrv_num_rows($result)<=0){
								echo "no posts";
							}else{
								while($row=sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
									$liked=null;
									$disliked=null;
									$sqlLikedDisliked="select count(l.post) liked, (select count(dl.post) from dislikes dl where dl.post='".$row["pid"]."' and dl.userid='".$uid."') disliked from likes l where l.post='".$row["pid"]."' and l.userid='".$uid."'";
									$params = array();
									$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET );
									$resultLikedDisliked=sqlsrv_query($conn, $sqlLikedDisliked, $params, $options);
									while($rowLD=sqlsrv_fetch_array($resultLikedDisliked, SQLSRV_FETCH_ASSOC)){
										if($rowLD["liked"]>0){
											$liked=True;
										}
										if($rowLD["disliked"]>0){
											$disliked=True;
										}
									}
									
									
									echo "<div class='card post-card'>";
									echo "<div class='card-header'>";
									//echo "<i>#".$row["pid"]." ~ ".$row["login"]." (".$row["date"].")</i>";
									//echo "<div class='postdate'>#".$row["pid"]." (".$row["date"].")</div>";
									echo "<div class='postdate'>#".$row["pid"]." (tutaj_data)</div>";
									echo "<div class='postuser'>".$row["login"]."</div>";
									if($row["userid"]==$uid){
										echo "<form action='' method='POST' class='inline-form' onsubmit=\"return confirm('Are you sure you want to delete this post?');\"><input type='number' name='iPID' id='iPID' value='".$row["pid"]."' required hidden><input type='submit' id='deletePostButton' name='deletePostButton' class='btn btn-outline-danger deletepost-button' value='Delete' /></form>";
									}
									echo "</div>";
									echo "<div class='card-body'>";
									echo "".$row["content"]."";
									echo "</div>";
									echo "<div class='card-footer'>";
									
									echo "<form action='' method='POST' class='inline-form'>";
									echo "<input type='number' id='lPID' name='lPID' hidden required value='".$row["pid"]."' />";
									if($liked){
										echo "<button type='submit' class='button-like' id='buttonRemoveLike' name='buttonRemoveLike'>";
										echo "<div class='likes-dislikes-count'>".$row["likes"]."</div>";
										echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='#209cf4' class='bi bi-hand-thumbs-up-fill' viewBox='0 0 16 16'><path d='M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z'/></svg>";
										echo "</button>";
									}else{
										echo "<button type='submit' class='button-like' id='buttonAddLike' name='buttonAddLike'>";
										echo "<div class='likes-dislikes-count'>".$row["likes"]."</div>";
										echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='#209cf4' class='bi bi-hand-thumbs-up' viewBox='0 0 16 16'><path d='M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2.144 2.144 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a9.84 9.84 0 0 0-.443.05 9.365 9.365 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111L8.864.046zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a8.908 8.908 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.224 2.224 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.866.866 0 0 1-.121.416c-.165.288-.503.56-1.066.56z'/></svg>";
										echo "</button>";
									}
									echo "</form>";
									
									
									echo "<form action='' method='POST' class='inline-form'>";
									echo "<input type='number' id='dlPID' name='dlPID' hidden required value='".$row["pid"]."' />";
									if($disliked){
										echo "<button type='submit' class='button-dislike' id='buttonRemoveDislike' name='buttonRemoveDislike'>";
										echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='#209cf4' class='bi bi-hand-thumbs-down-fill' viewBox='0 0 16 16'><path d='M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z'/></svg>";
										echo "<div class='likes-dislikes-count'>".$row["dislikes"]."</div>";
										echo "</button>";
									}else{
										echo "<button type='submit' class='button-dislike' id='buttonAddDislike' name='buttonAddDislike'>";
										echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='#209cf4' class='bi bi-hand-thumbs-down' viewBox='0 0 16 16'><path d='M8.864 15.674c-.956.24-1.843-.484-1.908-1.42-.072-1.05-.23-2.015-.428-2.59-.125-.36-.479-1.012-1.04-1.638-.557-.624-1.282-1.179-2.131-1.41C2.685 8.432 2 7.85 2 7V3c0-.845.682-1.464 1.448-1.546 1.07-.113 1.564-.415 2.068-.723l.048-.029c.272-.166.578-.349.97-.484C6.931.08 7.395 0 8 0h3.5c.937 0 1.599.478 1.934 1.064.164.287.254.607.254.913 0 .152-.023.312-.077.464.201.262.38.577.488.9.11.33.172.762.004 1.15.069.13.12.268.159.403.077.27.113.567.113.856 0 .289-.036.586-.113.856-.035.12-.08.244-.138.363.394.571.418 1.2.234 1.733-.206.592-.682 1.1-1.2 1.272-.847.283-1.803.276-2.516.211a9.877 9.877 0 0 1-.443-.05 9.364 9.364 0 0 1-.062 4.51c-.138.508-.55.848-1.012.964l-.261.065zM11.5 1H8c-.51 0-.863.068-1.14.163-.281.097-.506.229-.776.393l-.04.025c-.555.338-1.198.73-2.49.868-.333.035-.554.29-.554.55V7c0 .255.226.543.62.65 1.095.3 1.977.997 2.614 1.709.635.71 1.064 1.475 1.238 1.977.243.7.407 1.768.482 2.85.025.362.36.595.667.518l.262-.065c.16-.04.258-.144.288-.255a8.34 8.34 0 0 0-.145-4.726.5.5 0 0 1 .595-.643h.003l.014.004.058.013a8.912 8.912 0 0 0 1.036.157c.663.06 1.457.054 2.11-.163.175-.059.45-.301.57-.651.107-.308.087-.67-.266-1.021L12.793 7l.353-.354c.043-.042.105-.14.154-.315.048-.167.075-.37.075-.581 0-.211-.027-.414-.075-.581-.05-.174-.111-.273-.154-.315l-.353-.354.353-.354c.047-.047.109-.176.005-.488a2.224 2.224 0 0 0-.505-.804l-.353-.354.353-.354c.006-.005.041-.05.041-.17a.866.866 0 0 0-.121-.415C12.4 1.272 12.063 1 11.5 1z'/></svg>";
										echo "<div class='likes-dislikes-count'>".$row["dislikes"]."</div>";
										echo "</button>";
									}
									echo "</form>";
									
									echo "</div>";
									echo "</div>";
									//echo "<hr>";
								}
							}
						}
					?>
				</div>
			</div>
		</div>
		<div class="author">40843 - Damian Osiak</div>
	</div>
</body>
</html>