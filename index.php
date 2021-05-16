<?php
session_start();
if(isset($_COOKIE['token']))
{
	$token = $_COOKIE['token'];	
	ConnectDB();
	$res = $mysqli->query("SELECT * FROM users WHERE token = '$token'");
	if($res->num_rows==1)
	{
		$user = $res->fetch_object();
		LogIn($user->id);		
	}
}


function ConnectDB()
{
	global $mysqli;
	$mysqli = new mysqli('localhost','root','12345678','Shoe_store');
}

function LogIn($userid)
{
	global $mysqli;
	$_SESSION['userid'] = $userid;
	$token = sprintf('%08x%08x%08x%08x', rand(), rand(), rand(), rand());
	setcookie('token',$token,time()+3600*24*30);
	$res = $mysqli->query("UPDATE users SET token = '$token' WHERE id = '$userid'");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset = "utf-8">
	<title> Театр </title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="header">
		<a class="logo" href="index.php"><img src="logoT.jpg" alt=""></a>
		<div class="top-menu">
		<ul>
			<li><a href="index.php">Главная</a></li>
			<li><a href="pages/actors.php">Актёры</a></li>
			<li><a href="pages/spectacles.php">Спектакли</a></li>
		<div class="auth">
			<?php
			if(isset($_SESSION['userid']))
			{
				$userid = $_SESSION['userid'];
				$mysqli = new mysqli('localhost','root','12345678','theatre');
				
				$res = $mysqli->query("SELECT login From users WHERE id='$userid'");
				$user = $res->fetch_object();
				$login = $user->login;

				echo "<li><a href='basket.php'>Вы вошли как $login </a></li>
				<li><a href='exit.php'>Выход</a></li>";
			}
			else
			{
				echo"<li><a href='authForm.php'>Вход</a></li>
				<li><a href='registrationForm.php'>Регистрация</a></li>";
			}
			?>
		</ul>
		</div>
		</div>
	</div>
	<div class="content">
		<div class='mainPage'>
		</div>
	</div>
	<div class="footer">
			8(960)319-71-39 г. Заречный Пензенская область<br>
			Горячая линия
	</div>
</body>
</html>